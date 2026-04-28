<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
 * Migración: add_height_and_category_product_table
 * ---------------------------------------------------
 * Agrega dos cosas relacionadas con el sistema de categorías y atributos:
 *
 * 1. Columna height_meters en products:
 *    Permite el filtro de rango de altura en el catálogo (faceta 'height_range').
 *    decimal(5, 2) soporta hasta 999.99 metros; más que suficiente para dinosaurios.
 *
 * 2. Tabla pivot category_product:
 *    Implementa la relación many-to-many entre categorías y productos.
 *    Un producto puede pertenecer a múltiples categorías (ej: 'Carnívoros' + 'Jurásico').
 *    Usa clave primaria compuesta (category_id, product_id); no necesita id propio.
 *
 * Migración de datos (dos operaciones):
 *   A) Popula el pivot desde category_id legacy: cada producto existente se conecta
 *      a su categoría actual usando insertOrIgnore() (seguro ante duplicados).
 *   B) Asigna alturas demo en round-robin a los productos existentes.
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // nullable() → productos existentes no tienen este valor aún
            $table->decimal('height_meters', 5, 2)->nullable()->after('era');
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
        // El pivot se elimina primero para no dejar FKs huérfanas
        Schema::dropIfExists('category_product');

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('height_meters');
        });
    }
};