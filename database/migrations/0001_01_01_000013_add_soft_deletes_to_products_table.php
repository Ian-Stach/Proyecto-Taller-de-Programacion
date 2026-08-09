<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Migración: add_soft_deletes_to_products_table
 * -----------------------------------------------
 * Agrega la columna deleted_at a la tabla products para implementar
 * el borrado lógico (soft delete) mediante el trait SoftDeletes de Eloquent.
 *
 * ANTES: $product->delete() ejecutaba DELETE FROM products WHERE id = ?
 *        eliminando el registro permanentemente, incluyendo referencias en order_items.
 *
 * DESPUÉS: $product->delete() rellena deleted_at con el timestamp actual.
 *   - El registro queda en la BD, preservando el historial de órdenes.
 *   - Eloquent agrega WHERE deleted_at IS NULL automáticamente a todos los queries,
 *     por lo que el producto desaparece del catálogo y del panel admin sin código extra.
 *   - Para restaurar: $product->restore()  → deleted_at = null
 *   - Para borrar permanentemente: $product->forceDelete()
 *
 * NOTA: El modelo Product debe usar el trait SoftDeletes para que este campo
 * sea reconocido por Eloquent (ver app/Models/Product.php).
 */

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // softDeletes() agrega deleted_at timestamp nullable con índice
        $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
