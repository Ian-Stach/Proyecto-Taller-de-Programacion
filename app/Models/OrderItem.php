<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}