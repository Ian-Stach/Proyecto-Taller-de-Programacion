<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/*
 * OrderFactory
 * -------------
 * Genera órdenes de compra para seeders y tests.
 *
 * total_price se genera aleatoriamente entre 50 y 2000 con 2 decimales.
 * En producción real, total_price se calcula en OrderController (subtotal + 10% tax);
 * aquí es un valor simulado independiente de cualquier OrderItem.
 *
 * status se escoge aleatoriamente entre los 3 estados posibles del sistema.
 * Para tests que requieran un estado específico, usar el método state():
 *   Order::factory()->state(['status' => 'completed'])->create()
 */

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            // randomFloat(decimales, min, max)
            'total_price' => fake()->randomFloat(2, 50, 2000),
            // Los 3 estados válidos del sistema de órdenes
            'status' => fake()->randomElement(['pending', 'completed', 'cancelled']),
        ];
    }
}