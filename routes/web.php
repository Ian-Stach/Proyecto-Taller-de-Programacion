<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProfileController;

// ============================================================================
// 🟢 RUTAS PÚBLICAS (Acceso para todos)
// ============================================================================

// Páginas estáticas
Route::view('/', 'principal')->name('home');
Route::view('/principal', 'principal')->name('principal');
Route::view('/about', 'about')->name('about');
Route::view('/shipping', 'shipping')->name('shipping');
Route::view('/terms', 'terms')->name('terms');

// Contacto (dinámica)
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:20,1')->name('contact.store');

// Rutas de productos (dinámicas)
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
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
    // Dashboard
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    // Rutas de perfil
    Route::view('/user', 'profile.user')->name('user');
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

