<?php

/*
|--------------------------------------------------------------------------
| ARCHIVO: routes/console.php
|--------------------------------------------------------------------------
| Define comandos de Artisan personalizados para la tienda.
| Estos comandos se ejecutan desde la terminal con: php artisan <nombre>
|
| A diferencia de web.php y auth.php, este archivo no define rutas HTTP.
| Usa Artisan::command() como atajo para comandos simples basados en Closure,
| sin necesidad de crear una clase Command separada en app/Console/Commands/.
|
| Comandos disponibles:
|   php artisan inspire           → Quote inspirador (incluido con Laravel)
|   php artisan store:stats       → Resumen estadístico de la tienda
|   php artisan store:products    → Listado de todos los productos con categoría
|   php artisan store:users       → Listado de usuarios con email verificado
|   php artisan store:orders      → Listado de todas las órdenes realizadas
|
| Métodos de output disponibles en los Closures:
|   $this->info()    → texto en verde  (éxito, títulos)
|   $this->warn()    → texto en amarillo (advertencias)
|   $this->line()    → texto plano
|   $this->comment() → texto en gris/amarillo claro (notas)
|   $this->error()   → texto en rojo  (errores)
|--------------------------------------------------------------------------
*/

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\Category;

/*
 * COMANDO: inspire
 * Muestra una cita inspiradora aleatoria.
 * Viene incluido por defecto en Laravel via Inspiring::quote().
 * Uso: php artisan inspire
 */
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
 * COMANDO: store:stats
 * Muestra un resumen rápido del estado de la tienda en la terminal.
 * Útil para revisión operativa sin abrir la base de datos.
 *
 * Métricas mostradas:
 *   - Usuarios totales y verificados (con email_verified_at no nulo)
 *   - Productos disponibles (suma de stock de todos los productos)
 *   - Número de categorías
 *   - Total de órdenes realizadas
 *   - Suma de ingresos (total_price de todas las órdenes)
 *
 * Uso: php artisan store:stats
 */
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

/*
 * COMANDO: store:products
 * Lista todos los productos con su categoría, stock y precio.
 * Product::with('category') usa eager loading para evitar el problema N+1:
 * carga todas las categorías en una sola query en lugar de una por producto.
 *
 * Uso: php artisan store:products
 */
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

/*
 * COMANDO: store:users
 * Lista solo usuarios con email verificado (email_verified_at no nulo).
 * whereNotNull() filtra en la query SQL, más eficiente que cargar todos y filtrar en PHP.
 * Muestra también cuántas órdenes tiene cada usuario (relación orders en el modelo User).
 *
 * Uso: php artisan store:users
 */
Artisan::command('store:users', function () {
    $users = User::whereNotNull('email_verified_at')->get();

    if ($users->isEmpty()) {
        $this->warn('No hay usuarios verificados');
        return;
    }

    $this->info('👥 USUARIOS VERIFICADOS');
    $this->line('');

    foreach ($users as $user) {
        // $user->orders es la relación hasMany definida en el modelo User
        $ordersCount = $user->orders->count();
        $this->line("ID: {$user->id} | Nombre: {$user->name}");
        $this->line("   Email: {$user->email}");
        $this->line("   Órdenes: {$ordersCount}");
        $this->line('');
    }
})->purpose('List verified users');

/*
 * COMANDO: store:orders
 * Lista todas las órdenes con el usuario que las realizó y la cantidad de items.
 * Order::with(['user', 'orderItems']) hace eager loading de dos relaciones a la vez,
 * evitando queries adicionales dentro del foreach.
 *
 * Uso: php artisan store:orders
 */
Artisan::command('store:orders', function () {
    $orders = Order::with(['user', 'orderItems'])->get();

    if ($orders->isEmpty()) {
        $this->warn('No hay órdenes registradas');
        return;
    }

    $this->info('📦 ÓRDENES REGISTRADAS');
    $this->line('');

    foreach ($orders as $order) {
        // $order->orderItems es la relación hasMany definida en el modelo Order
        $itemsCount = $order->orderItems->count();
        $this->line("Orden #{$order->id} | Usuario: {$order->user->name}");
        $this->line("   Items: {$itemsCount} | Total: \$" . number_format($order->total_price, 2));
        $this->line("   Fecha: {$order->created_at}");
        $this->line('');
    }
})->purpose('List all orders');
