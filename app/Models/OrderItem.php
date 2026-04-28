<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/*
 * OrderItem
 * ----------
 * Representa un ítem individual dentro de una orden de compra.
 * Cada fila es la combinación de un producto, una cantidad y el precio
 * unitario en el momento de la compra (snapshot de precio).
 *
 * Tabla: order_items
 * Columnas relevantes:
 *   order_id   → FK a la orden que contiene este ítem
 *   product_id → FK al producto comprado
 *   quantity   → unidades compradas de ese producto
 *   unit_price → precio unitario al momento de la compra (puede diferir del precio actual del producto)
 *
 * Por qué unit_price es un snapshot y no se lee de Product:
 *   Si el precio del producto cambia después de la compra, el historial de órdenes
 *   debe reflejar lo que el usuario pagó realmente, no el precio actual.
 *   Por eso OrderController copia product->price a unit_price al crear el ítem.
 *
 * Cast 'decimal:2' en unit_price: igual que en Order, garantiza 2 decimales exactos.
 * Cast 'integer' en quantity: asegura que el valor es numérico entero, no string.
 *
 * Relaciones:
 *   order()   → BelongsTo(Order)   — la orden a la que pertenece este ítem
 *   product() → BelongsTo(Product) — el producto comprado (puede haberse eliminado)
 */
class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'product_id', 'quantity', 'unit_price'];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'quantity' => 'integer',
        ];
    }

    // Relacion: Un item pertenece a una orden
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relacion: Un item pertenece a un producto
    // NOTA: el producto podría haber sido eliminado de la BD; usar $item->product con cuidado.
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
