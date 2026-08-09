<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/*
 * Category
 * ---------
 * Representa una categoría del catálogo de productos.
 * Soporta jerarquía de categorías en árbol mediante auto-referencia:
 * cada categoría puede tener una categoría padre (parent_id) y muchas hijas.
 *
 * Tabla: categories
 * Columnas relevantes:
 *   parent_id   → FK a la misma tabla (null = categoría raíz)
 *   name        → nombre visible de la categoría
 *
 * Relaciones:
 *   parent()   → BelongsTo(Category) — la categoría padre inmediata
 *   children() → HasMany(Category)   — las subcategorías directas
 *   products() → BelongsToMany(Product) — productos asignados a esta categoría
 *
 * La relación many-to-many con Product usa la tabla pivot 'category_product'
 * (generada automáticamente por Laravel con los nombres en orden alfabético).
 * Esto permite que un producto pertenezca a varias categorías simultáneamente.
 */
class Category extends Model
{
    use HasFactory;

    protected $fillable = ['parent_id', 'name'];

    protected function casts(): array
    {
        return [
            // Castea parent_id a int para que las comparaciones estrictas (===) funcionen
            // correctamente, ya que SQLite puede devolverlo como string.
            'parent_id' => 'integer',
        ];
    }

    /*
     * La categoría padre de esta categoría.
     * self::class como primer argumento indica auto-referencia al mismo modelo.
     * 'parent_id' es la FK en esta tabla que apunta al id del padre.
     * Devuelve null para categorías raíz (parent_id = null).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /*
     * Las subcategorías directas de esta categoría.
     * 'parent_id' es la FK en la tabla hija que apunta al id de esta categoría.
     * Solo devuelve hijos directos (un nivel), no todos los descendientes.
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Una categoria puede pertenecer a muchos productos.
     *
     * La tabla pivot es 'category_product' (Eloquent la infiere en orden alfabético).
     * Se puede usar para navegar de una categoría a sus productos, por ejemplo
     * para mostrar todos los productos de una categoría en una página de listado.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}