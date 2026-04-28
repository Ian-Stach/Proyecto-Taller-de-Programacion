<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Migración: create_orders_table
 * ----------------------------------------
 * Crea la tabla de pedidos (órdenes de compra).
 *
 * Un pedido es inmutable tras su creación: total_price es un snapshot calculado
 * en el checkout (productos + impuesto 10%). No se recalcula si los precios cambian.
 *
 * Los ítems individuales del pedido están en la tabla order_items (migración 000006).
 * Un pedido pertenece a un usuario; si el usuario se elimina, el pedido también.
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            // onDelete('cascade') → si el usuario se da de baja, sus pedidos se eliminan
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // decimal(10, 2) → hasta 99,999,999.99; más amplio que el precio unitario (8,2)
            // porque es la suma de todos los ítems + impuesto
            $table->decimal('total_price', 10, 2);
            // string en lugar de enum: más flexible para agregar estados en el futuro sin migrar
            $table->string('status')->default('pending'); // pending, completed, cancelled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};