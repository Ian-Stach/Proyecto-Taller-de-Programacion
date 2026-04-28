<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/*
 * CategoryFactory
 * ----------------
 * Genera categorías de prueba para seeders y tests.
 * No asigna parent_id por defecto: las categorías generadas son raíz.
 * Si se necesita una jerarquía, se puede encadenar manualmente:
 *   Category::factory()->create(['parent_id' => $parent->id])
 *
 * fake()->unique()->word() garantiza que no se repitan nombres de categoría
 * dentro de la misma ejecución del seeder, evitando violaciones de unicidad en BD.
 */

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // unique() previene duplicados de nombre en la misma ejecución del factory
            'name' => fake()->unique()->word(),
            'description' => fake()->sentence(10),
            'image' => fake()->imageUrl(640, 480, 'dinosaurs'),
            // parent_id no se incluye → las categorías son raíz por defecto
        ];
    }
}