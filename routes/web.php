<?php

/*
 * Archivo: routes/web.php
 * ----------------------------------------------------------------------------------------------
 * Define las rutas web del sitio, organizadas en tres bloques según el nivel de acceso:
 *
 * 1. Rutas PÚBLICAS: accesibles para cualquier visitante, sin necesidad de login.
 * 2. Rutas de AUTENTICACIÓN: importadas desde auth.php, con middleware 'guest' o 'auth'.
 * 3. Rutas AUTENTICADAS: requieren middleware 'auth', solo para usuarios logueados.
 *
 * Además, las rutas de administración bajo /admin requieren ambos middlewares 'auth' + 'admin',
 * donde 'admin' verifica que el usuario tenga is_admin = true en la base de datos.
 *
 * Middleware 'auth': si el usuario no está logueado, redirige a /login.
 * Middleware 'guest': si el usuario ya está logueado, redirige a /home.
 *
 * Controladores:
 *   - AdminController → maneja todas las rutas bajo /admin
 *   - ProductController → maneja catálogo público de productos
 *   - CartController → maneja el carrito de compras
 *   - OrderController → maneja las órdenes y checkout
 *   - FavoriteController → maneja los productos favoritos del usuario
 *   - ContactController → maneja el formulario de contacto
 *   - ProfileController → maneja la cuenta y perfil del usuario
 *
 * Rutas de administración (/admin/*) están protegidas por los middlewares 'auth' + 'admin'.
 * Solo usuarios autenticados con is_admin = true pueden acceder a estas rutas.
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProfileController;

// ======================================
// 🟢 RUTAS PÚBLICAS (Acceso para todos)
// ======================================

// Página de inicio: dos URLs distintas apuntan a la misma lógica
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/principal', [HomeController::class, 'index'])->name('principal');

// Páginas estáticas: Route::view() devuelve la vista directamente sin pasar por un Controller
Route::view('/about', 'about')->name('about');
Route::view('/shipping', 'shipping')->name('shipping');
Route::view('/terms', 'terms')->name('terms');

/*
 * Contacto (rutas dinámicas con Controller)
 *   throttle:20,1 → limita a 20 peticiones POST por minuto por IP.
 *   Protege el endpoint de spam y abuso del formulario de contacto.
 */
Route::get('/contact', [ContactController::class, 'show'])->name('contact');                                          // muestra el formulario (ContactController@show)
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:20,1')->name('contact.store');     // procesa el envío (ContactController@store)

/*
 * Rutas del catálogo de productos
 * - {product} usa Route Model Binding: Laravel busca automáticamente
 * el Product por su ID y lo inyecta en el método del Controller.
 * - IMPORTANTE: /products/suggestions debe estar ANTES de /products/{product},
 * porque si estuviera después, Laravel interpretaría 'suggestions' como un {product}.
 */
Route::get('/products', [ProductController::class, 'index'])->name('products.index');                             // listado con filtros (search, categoría, precio)
Route::get('/products/suggestions', [ProductController::class, 'suggestions'])->name('products.suggestions');     // endpoint JSON para el autocomplete del buscador (usado por header-search-suggest.js)
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');                     // detalle de un producto.

// ======================================================
// 🟡 RUTAS DE AUTENTICACIÓN (Importadas desde auth.php)
// - Contienen middleware 'guest' (solo sin login)
// - Contienen middleware 'auth' (solo con login)
// ======================================================

/*
 * require __DIR__.'/auth.php' incluye el archivo auth.php en el mismo contexto.
 * A diferencia de @include en Blade, aquí las rutas se registran directamente
 * en el Router de Laravel, como si estuvieran escritas en este mismo archivo.
 */
require __DIR__.'/auth.php';

// =========================================
// 🔵 RUTAS AUTENTICADAS (Middleware: auth)
// - Solo usuarios CON login pueden acceder
// - Usuarios sin login → Redirige a /login
// =========================================

Route::middleware(['auth', 'verified'])->group(function () {

    //PERFIL DE USUARIO
    Route::get('/user', [ProfileController::class, 'show'])->name('user');                         // vista de cuenta del usuario (ProfileController@show)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');              // formulario de edición de perfil (ProfileController@edit)
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');        // guarda los cambios del perfil (ProfileController@update). PATCH en lugar de PUT porque solo actualiza campos parciales.
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');     // elimina la cuenta del usuario (ProfileController@destroy)

    /*
     * CARRITO DE COMPRAS
     *   {product} usa Route Model Binding: inyecta el modelo Product automáticamente.
     *   El carrito se guarda en sesión (no en base de datos), por lo que requiere
     *   que el usuario esté logueado para mantener coherencia con el ViewComposer.
     *
     * IMPORTANTE: /cart/sidebar debe estar ANTES de /cart/{product},
     * o Laravel interpretaría 'sidebar' como el ID del producto.
     */
    Route::get('/cart', [CartController::class, 'index'])->name('cart.show');                     // vista del carrito con todos los items (CartController@index)
    Route::get('/cart/sidebar', [CartController::class, 'sidebar'])->name('cart.sidebar');        // partial HTML del carrito lateral (CartController@sidebar)
    Route::post('/cart/{product}', [CartController::class, 'add'])->name('cart.add');             // agrega un producto al carrito (CartController@add)
    Route::delete('/cart/{product}', [CartController::class, 'remove'])->name('cart.remove');     // quita un producto del carrito (CartController@remove)

    /*
     * ÓRDENES
     *   {order} usa Route Model Binding. Laravel verifica además que la orden
     *   pertenece al usuario autenticado (scoping implícito o verificación en Controller).
     */
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');           // historial de todas las órdenes del usuario (OrderController@index)
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');     // detalle de una orden específica (OrderController@show)
    Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');      // crea una nueva orden desde el carrito (OrderController@store)

    /*
     * FAVORITOS
     *   Los favoritos se persisten en la tabla 'favorites' de la base de datos,
     *   vinculando user_id con product_id.
     */
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');                     // lista de productos guardados (FavoriteController@index)
    Route::post('/favorites/{product}', [FavoriteController::class, 'store'])->name('favorites.add');            // agrega un producto a favoritos (FavoriteController@store)
    Route::delete('/favorites/{product}', [FavoriteController::class, 'destroy'])->name('favorites.remove');     // quita un producto de favoritos (FavoriteController@destroy)
});

// =======================================================
// 🔴 RUTAS DE ADMINISTRACIÓN (Middlewares: auth + admin)
// - Solo usuarios con is_admin = true pueden acceder
// - Usuarios sin login → redirige a /login
// - Usuarios sin is_admin → 403 Forbidden
// =======================================================

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // Productos: CRUD completo
    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::get('/products/create', [AdminController::class, 'createProduct'])->name('products.create');
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store');
    Route::get('/products/{product}/edit', [AdminController::class, 'editProduct'])->name('products.edit');
    Route::patch('/products/{product}', [AdminController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{product}', [AdminController::class, 'destroyProduct'])->name('products.destroy');
    Route::patch('/products/{product}/toggle', [AdminController::class, 'toggleProduct'])->name('products.toggle');

    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::patch('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');

    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [AdminController::class, 'showOrder'])->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.status');

    Route::get('/metrics', [AdminController::class, 'metrics'])->name('metrics');
});

