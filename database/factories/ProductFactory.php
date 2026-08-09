<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/*
 * ProductFactory
 * ---------------
 * Genera productos de prueba con todos sus atributos de catálogo.
 * Es el factory más complejo porque Product tiene atributos facetables
 * (habitat, diet, era) cuyas opciones están definidas en el modelo.
 *
 * Métodos especiales:
 *   configure() → hook afterCreating: después de persistir el producto,
 *                 sincroniza la categoría legacy (category_id) con la tabla
 *                 pivot 'category_product', para que las relaciones many-to-many
 *                 estén consistentes con la relación legacy BelongsTo.
 *                 syncWithoutDetaching() agrega sin eliminar vínculos existentes.
 */

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory {
    protected $model = Product::class;

    /*
     * Hook que se ejecuta DESPUÉS de que el producto es guardado en BD.
     * Asegura que el category_id (FK legacy) también queda registrado en la
     * tabla pivot 'category_product', manteniendo coherencia entre la relación
     * directa (category()) y la many-to-many (categories()).
     * Si category_id es null (producto sin categoría), no hace nada.
     */
    public function configure(): static {
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
    public function definition(): array {
        $attributes = [
            // Intenta tomar una categoría existente al azar (inRandomOrder → 1 query).
            // Si no existe ninguna categoría en BD, crea una nueva con Category::factory().
            // Esto evita crear categorías extra cuando ya hay datos en la BD.
            'category_id' => Category::query()->inRandomOrder()->value('id') ?? Category::factory(),

            // unique() evita colisiones de nombre entre productos del mismo seeder
            'name' => fake()->unique()->words(3, asText: true),
            'description' => fake()->paragraph(),
            // randomFloat(decimales, min, max)
            'price' => fake()->randomFloat(2, 10, 500),
            'stock' => fake()->numberBetween(100, 1000),
            'image' => fake()->imageUrl(640, 480, 'dinosaurs'),
            'height_meters' => fake()->randomFloat(2, 1, 18),
            // 90% de probabilidad de que el producto sea activo (visible en catálogo)
            'active' => fake()->boolean(90),
        ];

        /*
         * Itera sobre las facetas de atributos del modelo para asignar un valor
         * aleatorio válido a cada columna facetable (habitat, diet, era).
         * Usa array_keys($facetDefinition['options']) para obtener las claves BD
         * (ej: 'carnivoro', 'herbivoro') y no las etiquetas visibles.
         * Esto garantiza que los valores generados siempre sean válidos según las
         * constantes del modelo, sin duplicar la lista de opciones aquí.
         *
         * NOTA: la faceta 'heights' no tiene 'column' (es un filtro de rango),
         * por eso el foreach solo funciona correctamente para las facetas de tipo 'column'.
         * Si se agregaran facetas sin 'column', este código podría fallar. Por ahora
         * las únicas facetas sin 'column' son las de tipo 'height_range'.
         */
        foreach (Product::catalogAttributeFacets() as $facetDefinition) {
            if (!empty($facetDefinition['column'])) {
                $attributes[$facetDefinition['column']] = fake()->randomElement(array_keys($facetDefinition['options']));
            }
        }

        return $attributes;
    }
}