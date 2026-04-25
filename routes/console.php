<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\Category;

/**
 * COMANDOS DE CONSOLA - Tienda Dinosaurios
 * 
 * Uso:
 *   php artisan inspire              - Quote inspirador
 *   php artisan store:stats          - Estadísticas de la tienda
 *   php artisan store:products       - Listar productos
 *   php artisan store:users          - Listar usuarios verificados
 *   php artisan store:orders         - Listar órdenes
 */

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('store:stats', function () {
    $this->info('📊 ESTADÍSTICAS - Tienda Dinosaurios');
    $this->line('─────────────────────────────────');
    $this->line('👥 Usuarios totales:     ' . User::count());
    $this->line('✅ Usuarios verificados: ' . User::whereNotNull('email_verified_at')->count());
    $this->line('🦕 Productos disponibles: ' . Product::sum('stock'));
    $this->line('📦 Categorías:           ' . Category::count());
    $this->line('🛒 Órdenes realizadas:   ' . Order::count());
    $this->line('💰 Total en ventas:      $' . number_format(Order::sum('total_price'), 2));
    $this->line('─────────────────────────────────');
})->purpose('Show store statistics');

Artisan::command('store:products', function () {
    $products = Product::with('category')->get();
    
    if ($products->isEmpty()) {
        $this->warn('No hay productos en la base de datos');
        return;
    }
    
    $this->info('🦖 PRODUCTOS DISPONIBLES');
    $this->line('');
    
    foreach ($products as $product) {
        $this->line("📛 {$product->id} | {$product->name}");
        $this->line("   Categoría: {$product->category->name}");
        $this->line("   Stock: {$product->stock} | Precio: \${$product->price}");
        $this->line('');
    }
})->purpose('List all products');

Artisan::command('store:users', function () {
    $users = User::whereNotNull('email_verified_at')->get();
    
    if ($users->isEmpty()) {
        $this->warn('No hay usuarios verificados');
        return;
    }
    
    $this->info('👥 USUARIOS VERIFICADOS');
    $this->line('');
    
    foreach ($users as $user) {
        $ordersCount = $user->orders->count();
        $this->line("ID: {$user->id} | Nombre: {$user->name}");
        $this->line("   Email: {$user->email}");
        $this->line("   Órdenes: {$ordersCount}");
        $this->line('');
    }
})->purpose('List verified users');

Artisan::command('store:orders', function () {
    $orders = Order::with(['user', 'orderItems'])->get();
    
    if ($orders->isEmpty()) {
        $this->warn('No hay órdenes registradas');
        return;
    }
    
    $this->info('📦 ÓRDENES REGISTRADAS');
    $this->line('');
    
    foreach ($orders as $order) {
        $itemsCount = $order->orderItems->count();
        $this->line("Orden #{$order->id} | Usuario: {$order->user->name}");
        $this->line("   Items: {$itemsCount} | Total: \$" . number_format($order->total_price, 2));
        $this->line("   Fecha: {$order->created_at}");
        $this->line('');
    }
})->purpose('List all orders');