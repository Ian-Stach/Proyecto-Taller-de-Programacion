<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Migración: create_order_items_table
 * ----------------------------------------
 * Crea la tabla de ítems individuales dentro de un pedido.
 *
 * Cada fila representa un producto comprado en una orden, con la cantidad
 * y el precio unitario en el momento de la compra (snapshot).
 *
 * unit_price no cambia si el precio del producto sube o baja después de la compra.
 * Esto permite mostrar el historial de pedidos con los precios originales.
 *
 * Si el producto se elimina de la BD, product_id queda como null (onDelete cascade
 * elimina el ítem junto con el pedido, no de forma independiente).
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            // onDelete('cascade') → al eliminar el pedido, se eliminan todos sus ítems
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            // onDelete('cascade') → si el producto es eliminado, el ítem también desaparece
            // NOTA: en producción podría preferirse nullOnDelete() para conservar el historial
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity'); // Unidades compradas de este producto en esta orden
            // Snapshot del precio unitario al momento de la compra (inmutable)
            $table->decimal('unit_price', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};