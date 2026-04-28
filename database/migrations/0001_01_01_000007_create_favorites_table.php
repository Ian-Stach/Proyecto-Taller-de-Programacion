<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Migración: create_favorites_table
 * ----------------------------------------
 * Crea la tabla de favoritos (productos guardados por el usuario).
 *
 * A diferencia de una tabla pivot simple (sin modelo), 'favorites' tiene
 * su propio id y timestamps. Esto permite:
 *   - Ordenar por fecha de agregado (order by created_at)
 *   - Paginación directa con paginate() en el modelo Favorite
 *   - DELETE directo sobre la fila sin cargar el modelo
 *
 * La restricción UNIQUE en (user_id, product_id) garantiza que un usuario
 * no pueda marcar el mismo producto como favorito dos veces. Se lanza
 * una excepción de integridad si se intenta insertar un duplicado.
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            // cascade: si el usuario se da de baja, sus favoritos desaparecen
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // cascade: si el producto se elimina, los favoritos que lo referencian también
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            // Restricción compuesta: evita duplicar un favorito para el mismo par usuario-producto
            $table->unique(['user_id', 'product_id']);
            $table->timestamps(); // created_at se usa para ordenar la lista de favoritos por recencia
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};