<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
 * Migración: add_product_facets_to_products_table
 * ---------------------------------------------------
 * Agrega las columnas de atributos facetables al catálogo de productos:
 *   habitat → terrestre / acuático / volador
 *   diet    → carnívoro / herbívoro / omnívoro
 *   era     → triásico / jurásico / cretácico
 *
 * Por qué son nullable(): los productos existentes en BD no tienen estos valores
 * todavía al momento de ejecutar el ALTER TABLE.
 *
 * Por qué tienen index(): el catálogo filtra con WHERE habitat=?, WHERE diet=?,
 * WHERE era=?. Sin índices, cada filtro genera un full-table scan.
 *
 * Migración de datos (round-robin):
 * Para los productos existentes, se asigna un valor usando $index % count($array).
 * Ejemplo con 10 productos: habitat alterna terrestre, acuático, volador, terrestre...
 * Es solo datos de demostración, no datos de producción reales.
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // after() posiciona la columna después de category_id (cosmetic en SQLite, real en MySQL)
            $table->string('habitat')->nullable()->after('category_id')->index();
            $table->string('diet')->nullable()->after('habitat')->index();
            $table->string('era')->nullable()->after('diet')->index();
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Los índices deben eliminarse antes que las columnas (requerido por algunos motores)
            $table->dropIndex(['habitat']);
            $table->dropIndex(['diet']);
            $table->dropIndex(['era']);
            $table->dropColumn(['habitat', 'diet', 'era']);
        });
    }
};