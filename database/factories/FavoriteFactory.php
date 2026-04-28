<?php

namespace Database\Factories;

use App\Models\Favorite;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/*
 * FavoriteFactory
 * ----------------
 * Genera registros de favoritos para seeders y tests.
 *
 * Por defecto crea un User nuevo y un Product nuevo por cada Favorite.
 * En seeders reales se pasa el user_id y product_id ya existentes para
 * evitar crear usuarios y productos extra:
 *   Favorite::factory()->create(['user_id' => $user->id, 'product_id' => $product->id])
 *
 * ATENCIÓN: la tabla favorites tiene unicidad en (user_id, product_id).
 * Si se genera masivamente sin controlar los IDs, puede haber colisiones.
 */

/**
 * @extends Factory<Favorite>
 */
class FavoriteFactory extends Factory
{
    protected $model = Favorite::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Si no se pasa user_id, crea un User nuevo (no recomendado en seeders con muchos registros)
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
        ];
    }
}
