<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/*
 * Order
 * ------
 * Representa una orden de compra creada por un usuario desde el carrito.
 * Una orden es inmutable una vez creada: el precio total y los ítems quedan
 * fijados en el momento de la compra, independientemente de cambios futuros
 * en los precios de los productos.
 *
 * Tabla: orders
 * Columnas relevantes:
 *   user_id     → FK al usuario que realizó la compra
 *   total_price → precio total ya con impuesto (subtotal + 10% tax), calculado en OrderController
 *   status      → estado actual de la orden ('pending' por defecto al crear)
 *
 * Cast 'decimal:2' en total_price:
 *   Laravel devuelve el valor de la BD como string con exactamente 2 decimales.
 *   Esto evita problemas de punto flotante al hacer cálculos o formatear el precio.
 *
 * Relaciones:
 *   user()       → BelongsTo(User)       — el usuario que realizó la orden
 *   orderItems() → HasMany(OrderItem)    — los productos incluidos en la orden
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'total_price', 'status'];

    protected function casts(): array
    {
        return [
            // 'decimal:2' serializa el precio siempre con 2 decimales (ej: "149.90")
            'total_price' => 'decimal:2',
            'status' => 'string',
        ];
    }

    // Relacion: Una orden pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relacion: Una orden tiene muchos items
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
