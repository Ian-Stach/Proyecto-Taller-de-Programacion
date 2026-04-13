<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Favorite;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear usuario de test
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    
        // Crear 10 usuarios adicionales
        User::factory(10)->create();

        // Crear 5 categorías
        $categories = Category::factory(5)->create();

        // Crear 30 productos (distribuidos entre las categorías)
        Product::factory(30)->create();

        // Crear 15 órdenes
        $orders = Order::factory(15)->create();

        // Crear 40 ítems de órdenes
        OrderItem::factory(40)->create();

        // Crear favoritos: Cada usuario favorea 2-5 productos random
        $users = User::all();
        $products = Product::all();

        foreach ($users as $user) {
            $randomProducts = $products->random(rand(2, 5));
            foreach ($randomProducts as $product) {
                Favorite::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                ]);
            }
        }
    }
}