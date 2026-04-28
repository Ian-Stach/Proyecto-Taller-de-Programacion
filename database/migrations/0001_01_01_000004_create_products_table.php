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
            $table->string('image')->nullable(); // Ruta o URL de imagen; nullable si no tiene foto
            // active=false oculta el producto del catálogo sin eliminarlo
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};