<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'habitat',
        'diet',
        'era',
        'height_meters',
        'name',
        'description',
        'price',
        'stock',
        'image',
        'active',
    ];

    protected static ?Collection $catalogCategoryParentMap = null;

    public const HEIGHT_RANGE_OPTIONS = [
        'pequeno' => [
            'label' => 'Pequeno',
            'min' => null,
            'max' => 4.00,
        ],
        'mediano' => [
            'label' => 'Mediano',
            'min' => 4.00,
            'max' => 9.00,
        ],
        'grande' => [
            'label' => 'Grande',
            'min' => 9.00,
            'max' => null,
        ],
    ];

    public const HABITAT_OPTIONS = [
        'terrestre' => 'Terrestre',
        'acuatico' => 'Acuatico',
        'volador' => 'Volador',
    ];

    public const DIET_OPTIONS = [
        'carnivoro' => 'Carnivoro',
        'herbivoro' => 'Herbivoro',
        'omnivoro' => 'Omnivoro',
    ];

    public const ERA_OPTIONS = [
        'triasico' => 'Triasico',
        'jurasico' => 'Jurasico',
        'cretacico' => 'Cretacico',
    ];

    public static function catalogAttributeFacets(): array
    {
        return [
            'habitats' => [
                'label' => 'Habitat',
                'chip_label' => 'Habitat',
                'column' => 'habitat',
                'options' => self::HABITAT_OPTIONS,
            ],
            'diets' => [
                'label' => 'Dieta',
                'chip_label' => 'Dieta',
                'column' => 'diet',
                'options' => self::DIET_OPTIONS,
            ],
            'eras' => [
                'label' => 'Era',
                'chip_label' => 'Era',
                'column' => 'era',
                'options' => self::ERA_OPTIONS,
            ],
            'heights' => [
                'label' => 'Tamano',
                'chip_label' => 'Tamano',
                'filter_type' => 'height_range',
                'options' => self::heightRangeLabels(),
            ],
        ];
    }

    public static function heightRangeLabels(): array
    {
        return collect(self::HEIGHT_RANGE_OPTIONS)
            ->mapWithKeys(fn (array $range, string $key) => [$key => $range['label']])
            ->all();
    }

    public static function heightRangeDefinition(string $rangeKey): ?array
    {
        return self::HEIGHT_RANGE_OPTIONS[$rangeKey] ?? null;
    }

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'height_meters' => 'decimal:2',
            'active' => 'boolean',
        ];
    }

    // Relacion legacy: categoria principal temporal mientras exista category_id.
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relacion principal del catalogo: un producto puede tener muchas categorias.
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function deepestCategories(): Collection
    {
        $categories = $this->relationLoaded('categories')
            ? $this->categories
            : $this->categories()->get();

        if ($categories->isEmpty()) {
            return collect();
        }

        $assignedCategoryIds = $categories->pluck('id');
        $ancestorIds = [];
        $loadedParentMap = $categories->pluck('parent_id', 'id');
        $catalogParentMap = null;

        foreach ($assignedCategoryIds as $categoryId) {
            $parentId = $loadedParentMap->get($categoryId);

            while ($parentId !== null) {
                if ($assignedCategoryIds->contains($parentId)) {
                    $ancestorIds[$parentId] = true;
                }

                if ($loadedParentMap->has($parentId)) {
                    $parentId = $loadedParentMap->get($parentId);

                    continue;
                }

                $catalogParentMap ??= self::catalogCategoryParentMap();
                $parentId = $catalogParentMap->get($parentId);
            }
        }

        return $categories
            ->reject(fn (Category $category) => isset($ancestorIds[$category->id]))
            ->values();
    }

    protected static function catalogCategoryParentMap(): Collection
    {
        if (self::$catalogCategoryParentMap === null) {
            self::$catalogCategoryParentMap = Category::query()->pluck('parent_id', 'id');
        }

        return self::$catalogCategoryParentMap;
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