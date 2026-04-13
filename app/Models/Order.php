<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'total_price', 'status'];

    protected function casts(): array
    {
        return [
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