<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ContactController;

// ============================================================================
// 🟢 RUTAS PÚBLICAS (Acceso para todos)
// ============================================================================

Route::get('/', function () {
    return view('Principal');
})->name('home');

Route::get('/Principal', function () {
    return view('Principal');
})->name('principal');

// Páginas estáticas
Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/shipping', function () {
    return view('shipping');
})->name('shipping');

Route::get('/terms', function () {
    return view('terms');
})->name('terms');

// Contacto (dinámica)
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

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
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Rutas de perfil (requeridas por Breeze)
    Route::get('/profile', function () {
        return view('profile.edit', ['user' => Auth::user()]);
    })->name('profile.edit');

    Route::post('/profile', function () {
        return redirect()->route('profile.edit');
    })->name('profile.update');

    Route::delete('/profile', function () {
        return redirect()->route('profile.edit');
    })->name('profile.destroy');
});

// ============================================================================
// 🔴 RUTAS PROTEGIDAS + VERIFICADAS (Middleware: auth + verified)
// - Solo usuarios CON login + email verificado
// - Usuarios sin verificación → Redirige a /email/verify
// - ESTAS SE AGREGARÁN EN PASO 4:
//   - GET  /products             → Lista de productos
//   - GET  /products/{id}        → Detalle producto
//   - GET  /cart                 → Ver carrito
//   - POST /cart                 → Agregar al carrito
//   - DELETE /cart/{item}        → Eliminar del carrito
//   - GET  /orders               → Mis órdenes
//   - GET  /orders/{id}          → Detalle orden
//   - POST /checkout             → Procesar pago
//   - POST /favorites/{product}  → Agregar favorito
//   - DELETE /favorites/{product} → Eliminar favorito
// ============================================================================

Route::middleware(['auth', 'verified'])->group(function () {
    // RUTAS DE PRODUCTOS
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

    // RUTAS DE CARRITO
    Route::get('/cart', [CartController::class, 'index'])->name('cart.show');
    Route::post('/cart', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/{product_id}', [CartController::class, 'remove'])->name('cart.remove');

    // RUTAS DE ÓRDENES
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');

    // RUTAS DE FAVORITOS
    Route::post('/favorites/{product}', [FavoriteController::class, 'store'])->name('favorites.add');
    Route::delete('/favorites/{product}', [FavoriteController::class, 'destroy'])->name('favorites.remove');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
});
