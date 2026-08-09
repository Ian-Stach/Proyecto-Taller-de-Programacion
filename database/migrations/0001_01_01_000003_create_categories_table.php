<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Migración: create_categories_table
 * ----------------------------------------
 * Crea la tabla inicial de categorías de productos.
 *
 * ESTADO INICIAL: En esta migración, el nombre es único de forma global
 * (no puede haber dos categorías con el mismo nombre en toda la tabla).
 * Esto cambia en la migración 000012, donde la unicidad pasa a ser
 * compuesta (parent_id, name), permitiendo el mismo nombre en distintas ramas.
 *
 * La jerarquía padre/hijo (parent_id) se agrega en la migración 000011.
 * La relación con productos (tabla pivot category_product) se crea en 000010.
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            // unique() global en esta migración; se reemplaza por (parent_id, name) en 000012
            $table->string('name')->unique();
            $table->timestamps();
            $table->foreignId('parent_id')
                ->nullable()              // null = categoría raíz (nivel superior)
                ->after('id')            // Se posiciona justo después del id
                ->constrained('categories') // FK a la misma tabla (auto-referencial); nombre explícito requerido
                ->nullOnDelete();         // Si el padre se elimina, los hijos pasan a ser raíz
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};