<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function configure(): static
    {
        return $this->afterCreating(function (Product $product) {
            if ($product->category_id) {
                $product->categories()->syncWithoutDetaching([$product->category_id]);
            }
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $attributes = [
            'category_id' => Category::query()->inRandomOrder()->value('id') ?? Category::factory(),
            'name' => fake()->unique()->words(3, asText: true),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 10, 500),
            'stock' => fake()->numberBetween(0, 100),
            'image' => fake()->imageUrl(640, 480, 'dinosaurs'),
            'height_meters' => fake()->randomFloat(2, 1, 18),
            'active' => fake()->boolean(90), // 90% de probabilidad de ser activo
        ];

        foreach (Product::catalogAttributeFacets() as $facetDefinition) {
            $attributes[$facetDefinition['column']] = fake()->randomElement(array_keys($facetDefinition['options']));
        }

        return $attributes;
    }
}