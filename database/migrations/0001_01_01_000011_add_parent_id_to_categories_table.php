<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Migración: add_parent_id_to_categories_table
 * -----------------------------------------------
 * Convierte la tabla 'categories' en una estructura de árbol jerárquico
 * agregando la FK auto-referencial parent_id.
 *
 * parent_id = null → la categoría es raíz (no tiene padre).
 * parent_id = X   → la categoría es hija de la categoría con id X.
 *
 * Por qué nullOnDelete() y no cascadeOnDelete():
 *   Si se elimina una categoría padre, sus hijas se vuelven raíz (parent_id = null)
 *   en lugar de eliminarse en cascada. Esto preserva las subcategorías aunque
 *   la estructura del árbol cambie.
 *
 * La restricción de nombre único se actualiza en la migración 000012 para
 * permitir el mismo nombre en distintas ramas del árbol.
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
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
        Schema::table('categories', function (Blueprint $table) {
            // dropConstrainedForeignId() elimina el índice, la FK y la columna en un solo paso
            $table->dropConstrainedForeignId('parent_id');
        });
    }
};