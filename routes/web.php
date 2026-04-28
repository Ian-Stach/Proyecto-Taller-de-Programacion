<?php

/*
|--------------------------------------------------------------------------
| ARCHIVO: routes/web.php
|--------------------------------------------------------------------------
| Define todas las rutas HTTP del sitio que devuelven respuestas web
| (páginas HTML, redirecciones). Las rutas están organizadas en tres bloques
| según el nivel de acceso requerido:
|
|   1. PÚBLICAS      → cualquier visitante puede acceder, sin login.
|   2. AUTH (guest)  → importadas desde auth.php, solo sin sesión activa.
|   3. AUTENTICADAS  → requieren middleware 'auth', solo usuarios logueados.
|
| Middleware 'auth': si el usuario no está logueado, redirige a /login.
| Middleware 'guest': si el usuario ya está logueado, redirige a /home.
|--------------------------------------------------------------------------
*/

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProfileController;
use App\Models\Category;

// ============================================================================
// 🟢 RUTAS PÚBLICAS (Acceso para todos)
// ============================================================================

/*
 * Closure reutilizable para la página principal.
 * Se asigna a una variable porque la misma lógica sirve para dos rutas:
 *   GET /          → URL raíz (acceso directo al sitio)
 *   GET /principal → alias usado en algunos enlaces internos
 *
 * Lógica:
 *   Busca la categoría con más productos activos para usarla como
 *   "categoría destacada" en el hero de la home. Si existe y tiene
 *   productos activos, carga hasta 16 para el carrusel.
 *
 *   withCount(['products as active_products_count' => fn ...])
 *     → agrega una columna virtual active_products_count al query,
 *       contando solo productos donde products.active = true.
 *
 *   orderByDesc('active_products_count') → la categoría más "viva" primero.
 *   orderBy('name') → desempate alfabético si dos categorías tienen el mismo conteo.
 */
$principalPage = function () {
    $featuredCategory = Category::query()
        ->withCount([
            'products as active_products_count' => fn ($query) => $query->where('products.active', true),
        ])
        ->orderByDesc('active_products_count')
        ->orderBy('name')
        ->first();

    $featuredProducts = collect();

    if ($featuredCategory && $featuredCategory->active_products_count > 0) {
        $featuredProducts = $featuredCategory->products()
            ->where('products.active', true)
            ->orderBy('products.name')
            ->limit(16)
            ->get(['products.id', 'products.name', 'products.price', 'products.image', 'products.stock']);
    }

    return view('principal', [
        'featuredCategory' => $featuredCategory,
        'featuredProducts' => $featuredProducts,
    ]);
};

// Página de inicio: dos URLs distintas apuntan a la misma lógica
Route::get('/', $principalPage)->name('home');
Route::get('/principal', $principalPage)->name('principal');

// Páginas estáticas: Route::view() devuelve la vista directamente sin pasar por un Controller
Route::view('/about', 'about')->name('about');
Route::view('/shipping', 'shipping')->name('shipping');
Route::view('/terms', 'terms')->name('terms');

/*
 * Contacto (rutas dinámicas con Controller)
 *   GET  /contact → muestra el formulario (ContactController@show)
 *   POST /contact → procesa el envío    (ContactController@store)
 *
 *   throttle:20,1 → limita a 20 peticiones POST por minuto por IP.
 *   Protege el endpoint de spam y abuso del formulario de contacto.
 */
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:20,1')->name('contact.store');

/*
 * Rutas del catálogo de productos
 *   GET /products             → listado con filtros (search, categoría, precio)
 *   GET /products/suggestions → endpoint JSON para el autocomplete del buscador
 *                               (usado por header-search-suggest.js)
 *   GET /products/{product}   → detalle de un producto. {product} usa Route Model
 *                               Binding: Laravel busca automáticamente el Product
 *                               por su ID y lo inyecta en el método del Controller.
 *
 * IMPORTANTE: /products/suggestions debe estar ANTES de /products/{product},
 * porque si estuviera después, Laravel interpretaría 'suggestions' como un {product}.
 */
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/suggestions', [ProductController::class, 'suggestions'])->name('products.suggestions');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// ============================================================================
// 🟡 RUTAS DE AUTENTICACIÓN (Importadas desde auth.php)
// - Contienen middleware 'guest' (solo sin login)
// - Contienen middleware 'auth' (solo con login)
// ============================================================================

/*
 * require __DIR__.'/auth.php' incluye el archivo auth.php en el mismo contexto.
 * A diferencia de @include en Blade, aquí las rutas se registran directamente
 * en el Router de Laravel, como si estuvieran escritas en este mismo archivo.
 */
require __DIR__.'/auth.php';

// ============================================================================
// 🔵 RUTAS AUTENTICADAS (Middleware: auth)
// - Solo usuarios CON login pueden acceder
// - Usuarios sin login → Redirige a /login
// ============================================================================

Route::middleware('auth')->group(function () {

    /*
     * PERFIL DE USUARIO
     *   GET    /user    → vista de cuenta del usuario (ProfileController@show)
     *   GET    /profile → formulario de edición de perfil (ProfileController@edit)
     *   PATCH  /profile → guarda los cambios del perfil (ProfileController@update)
     *                     PATCH en lugar de PUT porque solo actualiza campos parciales.
     *   DELETE /profile → elimina la cuenta del usuario (ProfileController@destroy)
     */
    Route::get('/user', [ProfileController::class, 'show'])->name('user');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
     * CARRITO DE COMPRAS
     *   GET    /cart/{product} → vista del carrito con todos los items (CartController@index)
     *   POST   /cart/{product} → agrega un producto al carrito  (CartController@add)
     *   DELETE /cart/{product} → quita un producto del carrito  (CartController@remove)
     *
     *   {product} usa Route Model Binding: inyecta el modelo Product automáticamente.
     *   El carrito se guarda en sesión (no en base de datos), por lo que requiere
     *   que el usuario esté logueado para mantener coherencia con el ViewComposer.
     */
    Route::get('/cart', [CartController::class, 'index'])->name('cart.show');
    Route::get('/cart/sidebar', [CartController::class, 'sidebar'])->name('cart.sidebar');
    Route::post('/cart/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/{product}', [CartController::class, 'remove'])->name('cart.remove');

    /*
     * ÓRDENES
     *   GET  /orders          → historial de todas las órdenes del usuario (OrderController@index)
     *   GET  /orders/{order}  → detalle de una orden específica             (OrderController@show)
     *   POST /checkout        → crea una nueva orden desde el carrito       (OrderController@store)
     *
     *   {order} usa Route Model Binding. Laravel verifica además que la orden
     *   pertenece al usuario autenticado (scoping implícito o verificación en Controller).
     */
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');

    /*
     * FAVORITOS
     *   GET    /favorites          → lista de productos guardados (FavoriteController@index)
     *   POST   /favorites/{product} → agrega un producto a favoritos (FavoriteController@store)
     *   DELETE /favorites/{product} → quita un producto de favoritos (FavoriteController@destroy)
     *
     *   Los favoritos se persisten en la tabla 'favorites' de la base de datos,
     *   vinculando user_id con product_id.
     */
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{product}', [FavoriteController::class, 'store'])->name('favorites.add');
    Route::delete('/favorites/{product}', [FavoriteController::class, 'destroy'])->name('favorites.remove');
});

