<?php

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

// Páginas estáticas
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

Route::get('/', $principalPage)->name('home');
Route::get('/principal', $principalPage)->name('principal');
Route::view('/about', 'about')->name('about');
Route::view('/shipping', 'shipping')->name('shipping');
Route::view('/terms', 'terms')->name('terms');

// Contacto (dinámica)
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:20,1')->name('contact.store');

// Rutas de productos (dinámicas)
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/suggestions', [ProductController::class, 'suggestions'])->name('products.suggestions');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// ============================================================================
// 🟡 RUTAS DE AUTENTICACIÓN (Importadas desde auth.php)
// - Contienen middleware 'guest' (solo sin login)
// - Contienen middleware 'auth' (solo con login)
// ============================================================================

require __DIR__.'/auth.php';

// ============================================================================
// 🔵 RUTAS AUTENTICADAS (Middleware: auth)
// - Solo usuarios CON login pueden acceder
// - Usuarios sin login → Redirige a /login
// ============================================================================

Route::middleware('auth')->group(function () {
    // Rutas de perfil
    Route::get('/user', [ProfileController::class, 'show'])->name('user');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // RUTAS DE CARRITO
    Route::get('/cart', [CartController::class, 'index'])->name('cart.show');
    Route::post('/cart/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/{product}', [CartController::class, 'remove'])->name('cart.remove');

    // RUTAS DE ÓRDENES
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');

    // RUTAS DE FAVORITOS
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{product}', [FavoriteController::class, 'store'])->name('favorites.add');
    Route::delete('/favorites/{product}', [FavoriteController::class, 'destroy'])->name('favorites.remove');
});

