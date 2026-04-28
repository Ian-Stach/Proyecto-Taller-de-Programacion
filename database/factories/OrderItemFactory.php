<?php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/*
 * OrderItemFactory
 * -----------------
 * Genera ítems individuales de una orden para seeders y tests.
 *
 * unit_price se genera antes del array de definición para poder reutilizarlo
 * como valor único coherente (el mismo precio en todos los campos que lo necesiten).
 * En producción, unit_price es un snapshot del precio del producto al momento
 * de la compra (copiado en OrderController); aquí es un valor aleatorio simulado.
 *
 * Por defecto crea una Order nueva y un Product nuevo por cada ítem.
 * En seeders se suele pasar order_id existente:
 *   OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $product->id])
 */

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Se genera antes del return para poder referenciarlo en otros campos si es necesario
        $price = fake()->randomFloat(2, 10, 500);
        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'quantity' => fake()->numberBetween(1, 10),
            'unit_price' => $price,
        ];
    }
}