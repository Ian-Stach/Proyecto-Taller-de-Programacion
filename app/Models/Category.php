<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    #[Fillable(['name', 'description', 'image'])]

    // Relacion: Un acategoria tiene muchos productos
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}