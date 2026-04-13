<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    #[Fillable(['category_id', 'name', 'description', 'price', 'stock', 'image', 'active'])]

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'active' => 'boolean',
        ];
    }

    // Relacion: Un producto pertenece a una categoria
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relacion: Un producto puede estar en muchos items de orden
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    
    // Relacio: Un producto puede estar en muchos favoritos
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
}