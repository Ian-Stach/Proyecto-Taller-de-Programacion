<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/*
 * Favorite
 * ---------
 * Representa un producto marcado como favorito por un usuario.
 * Actúa como tabla de unión explícita entre User y Product con timestamps propios
 * (created_at indica cuándo fue añadido a favoritos).
 *
 * Tabla: favorites
 * Columnas: id, user_id, product_id, created_at, updated_at
 *
 * Por qué es un modelo propio en vez de una relación BelongsToMany directa:
 *   • Tener timestamps propios permite ordenar favoritos por fecha de agregado.
 *   • Se puede consultar directamente (Auth::user()->favorites()->paginate())
 *     y acceder al producto vía la relación product() sin joins extra en la vista.
 *   • FavoriteController usa Favorite::where(...)->delete() directamente,
 *     lo que sería más complejo con detach() de una BelongsToMany.
 *
 * Unicidad: la combinación (user_id, product_id) debe ser única.
 * FavoriteController verifica esto manualmente antes de crear para poder
 * devolver un mensaje descriptivo en vez de un error de BD.
 */
class Favorite extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'product_id'];

    // Relacion: Un favorite pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relacion: Un favorite pertenece a un producto
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
