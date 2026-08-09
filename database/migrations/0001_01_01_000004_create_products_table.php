<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Migración: create_products_table
 * ----------------------------------------
 * Crea la tabla base de productos del catálogo.
 *
 * ESTADO INICIAL: Solo incluye campos fundamentales. Los atributos facetables
 * (habitat, diet, era) se agregan en la migración 000009. La columna
 * height_meters y la tabla pivot category_product se agregan en 000010.
 *
 * La columna category_id es una FK legacy (BelongsTo directo). La relación
 * many-to-many real usa la tabla pivot category_product (creada en 000010).
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            // onDelete('cascade') → si se elimina la categoría, sus productos también se eliminan
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name')->unique(); // Nombre único en todo el catálogo
            $table->text('description');
            // decimal(8, 2) → hasta 999,999.99; suficiente para cualquier precio de producto
            $table->decimal('price', 8, 2);
            $table->integer('stock'); // Unidades disponibles; el checkout lo decrementa atómicamente
            $table->string('habitat')->nullable()->index();
            $table->string('diet')->nullable()->index();
            $table->string('era')->nullable()->index();
            $table->decimal('height_meters', 5, 2)->nullable(); // Altura en metros; nullable para productos existentes
            $table->string('image')->nullable(); // Ruta o URL de imagen; nullable si no tiene foto
            // active=false oculta el producto del catálogo sin eliminarlo
            $table->boolean('active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        // Valores válidos para cada faceta (deben coincidir con las constantes del modelo Product)
        $habitats = ['terrestre', 'acuatico', 'volador'];
        $diets = ['carnivoro', 'herbivoro', 'omnivoro'];
        $eras = ['triasico', 'jurasico', 'cretacico'];

        // Rellena los productos existentes en orden de ID con valores en round-robin
        // values() resetea los índices del array para que $index empiece en 0
        DB::table('products')
            ->orderBy('id')
            ->get(['id'])
            ->values()
            ->each(function ($product, $index) use ($habitats, $diets, $eras) {
                DB::table('products')
                    ->where('id', $product->id)
                    ->update([
                        'habitat' => $habitats[$index % count($habitats)],
                        'diet' => $diets[$index % count($diets)],
                        'era' => $eras[$index % count($eras)],
                    ]);
            });

        Schema::create('category_product', function (Blueprint $table) {
            // cascadeOnDelete() → sintáxis fluida equivalente a onDelete('cascade')
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            // PK compuesta → no se necesita columna 'id' propia en una tabla pivot pura
            $table->primary(['category_id', 'product_id']);
        });

        // A) Migra las relaciones existentes del FK legacy category_id al pivot many-to-many
        // insertOrIgnore() → usa INSERT OR IGNORE (SQLite) / INSERT IGNORE (MySQL); sin error si ya existe
        DB::table('products')
            ->orderBy('id')
            ->get(['id', 'category_id'])
            ->each(function ($product) {
                if (! $product->category_id) {
                    return; // Productos sin categoría asignada se saltan
                }

                DB::table('category_product')->insertOrIgnore([
                    'category_id' => $product->category_id,
                    'product_id' => $product->id,
                ]);
            });

        // B) Alturas de demostración en round-robin (pequeño, mediano, grande)
        $demoHeights = [1.80, 6.50, 12.00];

        DB::table('products')
            ->orderBy('id')
            ->get(['id'])
            ->values() // reset de índices para que $index empiece en 0
            ->each(function ($product, $index) use ($demoHeights) {
                DB::table('products')
                    ->where('id', $product->id)
                    ->update([
                        'height_meters' => $demoHeights[$index % count($demoHeights)],
                    ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_product');
        Schema::dropIfExists('products');
    }
};