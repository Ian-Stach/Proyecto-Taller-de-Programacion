<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/*
 * Order
 * ------
 * Representa una orden de compra creada por un usuario desde el carrito. Una orden es inmutable una vez creada: el precio total y los ítems quedan
 * fijados en el momento de la compra, independientemente de cambios futuros en los precios de los productos.
 *
 * Tabla: orders
 * Columnas relevantes:
 *   user_id     → FK al usuario que realizó la compra
 *   total_price → precio total ya con impuesto (subtotal + 10% tax), calculado en OrderController
 *   status      → estado actual de la orden ('pending' por defecto al crear)
 *   date        → fecha/hora efectiva de la orden usada para listados y métricas
 *
 * Cast 'decimal:2' en total_price:
 *   Laravel devuelve el valor de la BD como string con exactamente 2 decimales.
 *   Esto evita problemas de punto flotante al hacer cálculos o formatear el precio.
 *
 * Relaciones:
 *   user()       → BelongsTo(User)       — el usuario que realizó la orden
 *   orderItems() → HasMany(OrderItem)    — los productos incluidos en la orden
 */
#[Fillable(['user_id', 'total_price', 'status', 'date'])]

class Order extends Model {
    use HasFactory;

    protected static ?bool $hasDateColumn = null;

    protected function casts(): array {
        return [
            // 'decimal:2' serializa el precio siempre con 2 decimales (ej: "149.90")
            'total_price' => 'decimal:2',
            'status' => 'string',
            'date' => 'datetime',
        ];
    }

    public static function dateColumn(): string {
        if (static::$hasDateColumn === null) {
            static::$hasDateColumn = Schema::hasColumn((new static())->getTable(), 'date');
        }

        return static::$hasDateColumn ? 'date' : 'created_at';
    }

    public function getDateAttribute($value) {
        if ($value !== null) {
            return $this->asDateTime($value);
        }

        $createdAt = $this->getAttributeFromArray($this->getCreatedAtColumn());

        return $createdAt !== null ? $this->asDateTime($createdAt) : null;
    }

    // Relacion: Una orden pertenece a un usuario
    public function user() {
        return $this->belongsTo(User::class);
    }

    // Relacion: Una orden tiene muchos items
    public function orderItems() {
        return $this->hasMany(OrderItem::class);
    }
}
