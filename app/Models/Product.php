<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/*
 * Product
 * --------
 * Modelo central del catálogo de dinosaurios. Contiene todos los atributos
 * del producto y la lógica de datos necesaria para el catálogo con filtros facetados.
 *
 * Tabla: products
 * Columnas relevantes:
 *   name, description → información básica del producto
 *   price             → precio con 2 decimales (cast 'decimal:2')
 *   stock             → unidades disponibles; se decrementa en OrderController al comprar
 *   image             → URL o path de la imagen principal
 *   active            → boolean; solo los productos activos (true) son visibles en el catálogo
 *   category_id       → FK legacy a la tabla categories (relación directa, ver category())
 *   habitat           → uno de HABITAT_OPTIONS (terrestre|acuatico|volador)
 *   diet              → uno de DIET_OPTIONS (carnivoro|herbivoro|omnivoro)
 *   era               → uno de ERA_OPTIONS (triasico|jurasico|cretacico)
 *   height_meters     → altura del dinosaurio en metros (decimal:2); base para filtro de tamaño
 *
 * Constantes de opciones (HABITAT_OPTIONS, DIET_OPTIONS, ERA_OPTIONS):
 *   Definen los valores válidos para cada atributo facetable.
 *   Formato: ['clave_bd' => 'Etiqueta visible'].
 *   Se usan tanto para construir los filtros del sidebar como para validar en los seeders/importadores.
 *
 * HEIGHT_RANGE_OPTIONS:
 *   Define los rangos de altura para el filtro "Tamaño" (pequeño/mediano/grande).
 *   Formato: ['clave' => ['label' => '...', 'min' => float|null, 'max' => float|null]].
 *   null en min/max indica sin límite por ese extremo (ej: grande: min=9, max=null → ≥ 9m).
 *
 * Relaciones:
 *   category()   → BelongsTo(Category)      — relación legacy por FK directa
 *   categories() → BelongsToMany(Category)  — relación principal many-to-many
 *   orderItems() → HasMany(OrderItem)        — ítems en órdenes que incluyen este producto
 *   favorites()  → HasMany(Favorite)         — registros de favoritos de usuarios
 */
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

    /*
     * Caché estático en memoria para el mapa de parent_id de categorías.
     * Se inicializa la primera vez que deepestCategories() lo necesita y se reutiliza
     * en todas las instancias del modelo durante la misma petición HTTP.
     * Evita queries repetidas cuando se renderizan muchas tarjetas de productos.
     */
    protected static ?Collection $catalogCategoryParentMap = null;

    /*
     * Define los rangos de altura para el filtro "Tamaño" en el catálogo.
     * El filtro de tipo 'height_range' en ProductController usa estas definiciones
     * para construir las condiciones WHERE del query (>= min, < max).
     */
    public const HEIGHT_RANGE_OPTIONS = [
        'pequeno' => [
            'label' => 'Pequeno',
            'min' => null,   // sin límite inferior (cualquier altura < 4m)
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
            'max' => null,   // sin límite superior (cualquier altura >= 9m)
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

    /*
     * Define las facetas de atributos disponibles en el catálogo.
     * Llamado por ProductController@buildFilterFacets() para construir el sidebar de filtros.
     *
     * Cada faceta describe:
     *   'label'       → etiqueta del grupo en el sidebar (ej: "Habitat")
     *   'chip_label'  → prefijo del chip cuando el filtro está activo (ej: "Habitat: Terrestre")
     *   'column'      → columna de la tabla products sobre la que se hace el whereIn
     *   'filter_type' → tipo de filtro ('column' es el default; 'height_range' es especial)
     *   'options'     → mapa de clave → etiqueta de las opciones disponibles
     *
     * NOTA: 'heights' no tiene 'column' porque su filter_type 'height_range' usa
     * condiciones de rango (>= / <) en lugar de un whereIn simple.
     */
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
                'label' => 'Tamaño',
                'chip_label' => 'Tamaño',
                'filter_type' => 'height_range', // filtro especial: no es un whereIn sino rangos
                'options' => self::heightRangeLabels(),
            ],
        ];
    }

    /*
     * Devuelve solo las etiquetas de los rangos de altura (sin min/max).
     * Formato: ['pequeno' => 'Pequeno', 'mediano' => 'Mediano', 'grande' => 'Grande'].
     * Usado por catalogAttributeFacets() para construir las options del filtro 'heights'
     * en el mismo formato que las demás facetas (clave → label).
     */
    public static function heightRangeLabels(): array
    {
        return collect(self::HEIGHT_RANGE_OPTIONS)
            ->mapWithKeys(fn (array $range, string $key) => [$key => $range['label']])
            ->all();
    }

    /*
     * Devuelve la definición completa (min, max, label) de un rango de altura por su clave.
     * Devuelve null si la clave no existe — esto es usado en applyFacetFilter() para
     * saltar silenciosamente valores de rango inválidos sin generar errores.
     */
    public static function heightRangeDefinition(string $rangeKey): ?array
    {
        return self::HEIGHT_RANGE_OPTIONS[$rangeKey] ?? null;
    }

    protected function casts(): array
    {
        return [
            // 'decimal:2' devuelve el precio siempre como string con 2 decimales exactos
            'price' => 'decimal:2',
            'height_meters' => 'decimal:2',
            // 'boolean' convierte el 0/1 de la BD a false/true en PHP
            'active' => 'boolean',
        ];
    }

    // Relacion legacy: categoria principal temporal mientras exista category_id.
    // Esta relación directa (FK en products) es la forma antigua de asignar categorías.
    // La relación principal ahora es categories() (many-to-many).
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relacion principal del catalogo: un producto puede tener muchas categorias.
    // Usa la tabla pivot 'category_product' (inferida alfabéticamente por Eloquent).
    // El sidebar de filtros y deepestCategories() usan esta relación.
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    /*
     * Devuelve solo las categorías "hoja" (las más específicas) del producto.
     * Filtra las categorías que son ancestros de otras categorías también asignadas,
     * evitando mostrar tanto "Carnívoros" como su subcategoría "Terópodos" cuando
     * el producto tiene ambas — en ese caso, solo se muestra "Terópodos".
     *
     * Algoritmo:
     *   1. Obtiene todas las categorías del producto (de la relación cargada o de una query).
     *   2. Para cada categoría, recorre el árbol hacia arriba (vía parent_id) buscando
     *      si algún ancestro también está entre las categorías asignadas.
     *   3. Si lo encuentra, marca ese ancestro como "no hoja" (ancestorIds[]).
     *   4. Al final, filtra el array devolviendo solo las categorías no marcadas.
     *
     * Optimizaciones:
     *   • Si la relación 'categories' ya está cargada (eager loading), la usa directamente
     *     en vez de hacer una nueva query (evita N+1 en la grilla del catálogo).
     *   • Para categorías que no están en $loadedParentMap (categorías padre del árbol
     *     que no son del producto), consulta catalogCategoryParentMap() — un mapa estático
     *     en caché que contiene TODOS los parent_id de la tabla categories en una sola query.
     *   • El operador ??= (null-coalescing assignment) inicializa $catalogParentMap solo
     *     si se necesita y no estaba cargado, evitando queries innecesarias.
     */
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
        $loadedParentMap = $categories->pluck('parent_id', 'id'); // mapa id → parent_id de las categorías del producto
        $catalogParentMap = null; // se carga bajo demanda si se necesita navegar más allá del conjunto cargado

        foreach ($assignedCategoryIds as $categoryId) {
            $parentId = $loadedParentMap->get($categoryId);

            while ($parentId !== null) {
                if ($assignedCategoryIds->contains($parentId)) {
                    // Este parent también está asignado al producto → es un ancestro, no una hoja
                    $ancestorIds[$parentId] = true;
                }

                if ($loadedParentMap->has($parentId)) {
                    // El parent está entre las categorías del producto → continuar el recorrido
                    $parentId = $loadedParentMap->get($parentId);

                    continue;
                }

                // El parent NO está entre las categorías del producto → consultar el mapa global
                $catalogParentMap ??= self::catalogCategoryParentMap();
                $parentId = $catalogParentMap->get($parentId);
            }
        }

        return $categories
            ->reject(fn (Category $category) => isset($ancestorIds[$category->id]))
            ->values();
    }

    /*
     * Caché estático del mapa completo de parent_id de todas las categorías.
     * Formato: Collection(id → parent_id).
     * Se construye en la primera llamada y se reutiliza en las siguientes dentro
     * de la misma petición HTTP (el static persiste durante el ciclo de vida de PHP).
     * Evita múltiples queries a la tabla categories cuando deepestCategories() se llama
     * para muchos productos en la misma página.
     */
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

    // Relacion: Un producto puede estar en muchos favoritos
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
}