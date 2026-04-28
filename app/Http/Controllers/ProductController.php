<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/*
 * ProductController
 * ------------------
 * Controlador principal del cat??logo de productos.
 * Maneja el listado con filtros facetados, el detalle de un producto,
 * el endpoint de sugerencias para el buscador del header, y tres m??todos
 * protegidos que encapsulan la l??gica de filtrado reutilizable.
 *
 * Rutas:
 *   GET /products              ??? index()       ??? cat??logo con filtros y paginaci??n
 *   GET /products/suggestions  ??? suggestions() ??? JSON para autocompletado del header
 *   GET /products/{product}    ??? show()        ??? detalle de producto
 *
 * IMPORTANTE: /products/suggestions debe definirse ANTES de /products/{product}
 * en routes/web.php, o Laravel interpretar??a "suggestions" como el ID del producto.
 *
 * M??todos protegidos (l??gica interna):
 *   buildFilterFacets()         ??? construye el array de facetas disponibles
 *   extractSelectedFacetValues() ??? sanitiza y valida los valores seleccionados del request
 *   applyFacetFilter()          ??? aplica cada tipo de filtro al query Builder
 */
class ProductController extends Controller
{
    /**
     * Listar todos los productos
     * GET /products
     *
     * Construye un query Builder progresivo al que se le van encadenando condiciones:
     *
     *   1. buildFilterFacets() genera el array de facetas desde las categor??as y los
     *      atributos del modelo Product. Cada faceta describe su etiqueta, sus opciones
     *      v??lidas y c??mo se aplica al query.
     *
     *   2. Por cada faceta, extractSelectedFacetValues() lee el request y devuelve
     *      solo los valores v??lidos (whitelist). Los valores inv??lidos son silenciosamente
     *      descartados. Si hay valores seleccionados, applyFacetFilter() los aplica.
     *
     *   3. El bloque de b??squeda agrega un WHERE agrupado (name LIKE o description LIKE)
     *      para que ambas condiciones se eval??en como una unidad OR dentro del AND global.
     *
     *   4. El sort tiene una whitelist expl??cita; si el valor recibido no est?? en
     *      $allowedSorts, se normaliza a 'latest' para evitar inyecciones en el ORDER BY.
     *
     *   5. Se pagina a 12 resultados. paginate() ejecuta dos queries: una de COUNT para
     *      el total y otra con LIMIT/OFFSET para los resultados de la p??gina.
     *
     *   6. Si la petici??n es AJAX ($request->ajax()), devuelve solo el partial
     *      results-content.blade.php (la columna de resultados sin el sidebar).
     *      Esto permite actualizar el cat??logo sin recargar la p??gina completa.
     *      Si no es AJAX, devuelve la vista completa products.index.
     */
    public function index(Request $request)
    {
        $query = Product::where('active', true)->with('categories');
        $categories = Category::orderBy('name')->get();
        $filterFacets = $this->buildFilterFacets($categories);

        foreach ($filterFacets as $facetKey => $facet) {
            $selectedValues = $this->extractSelectedFacetValues($request, $facet);

            $filterFacets[$facetKey]['selected'] = $selectedValues;
            $filterFacets[$facetKey]['selected_count'] = count($selectedValues);
            // collapse_id se usa como id del elemento Bootstrap Collapse en la vista
            $filterFacets[$facetKey]['collapse_id'] = $facetKey . 'Dropdown';
            // input_name agrega [] al final para que PHP reciba los checkboxes como array
            $filterFacets[$facetKey]['input_name'] = $facet['request_key'] . '[]';
            // option_map permite lookup O(1) de value ??? label para los chips de filtros activos
            $filterFacets[$facetKey]['option_map'] = collect($facet['options'])
                ->mapWithKeys(fn (array $option) => [$option['value'] => $option['label']])
                ->all();

            if (!empty($selectedValues)) {
                $this->applyFacetFilter($query, $facet, $selectedValues);
            }
        }

        if ($request->filled('search')) {
            // Envuelve en funci??n an??nima para agrupar el OR dentro del AND global del query
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('description', 'like', $searchTerm);
            });
        }

        $sort = $request->input('sort', 'latest');
        $allowedSorts = ['latest', 'price_asc', 'price_desc', 'name_asc', 'stock_desc'];

        // Whitelist: cualquier valor no reconocido cae al default 'latest'
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'latest';
        }

        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'stock_desc':
                $query->orderBy('stock', 'desc');
                break;
            default:
                $query->latest(); // Equivale a orderBy('created_at', 'desc')
                 break;
        }

        $products = $query->paginate(12);

        $viewData = [
            'products' => $products,
            'categories' => $categories,
            // array_values() reindexia para que la vista pueda iterar con @foreach sin problemas
            'filterFacets' => array_values($filterFacets),
            'currentSort' => $sort,
        ];

        // Respuesta parcial para peticiones AJAX (actualizaci??n as??ncrona del cat??logo)
        if ($request->ajax()) {
            return view('products.partials.results-content', $viewData);
        }

        return view('products.index', $viewData);
    }

    /**
     * Mostrar detalle de un producto
     * GET /products/{product}
     *
     * Si el producto existe pero tiene active=false se devuelve 404, comport??ndose
     * igual que si no existiera (evita revelar que el producto existe pero est?? inactivo).
     *
     * Productos relacionados: busca hasta 4 productos activos que compartan al menos
     * una categor??a con el producto actual, excluyendo el producto mismo.
     * Si el producto no tiene categor??as ($relatedCategoryIds === []), el whereHas se
     * omite para no devolver un resultado vac??o forzado; en ese caso, devuelve otros
     * productos activos sin filtro de categor??a.
     */
    public function show(Product $product)
    {
        // Verificar si es activo
        if (!$product->active) {
            abort(404);
        }

        $product->load('categories');

        $relatedCategoryIds = $product->categories
            ->pluck('id')
            ->all();

        $relatedProductsQuery = Product::where('id', '!=', $product->id)
            ->where('active', true)
            ->with('categories');

        if ($relatedCategoryIds !== []) {
            $relatedProductsQuery->whereHas('categories', function (Builder $categoryQuery) use ($relatedCategoryIds) {
                $categoryQuery->whereIn('categories.id', $relatedCategoryIds);
            });
        }

        $relatedProducts = $relatedProductsQuery
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    /**
     * Sugerencias para autocompletado del buscador del header
     * GET /products/suggestions?q=...
     *
     * Endpoint JSON llamado por header-search-suggest.js mientras el usuario escribe.
     * Devuelve hasta 8 productos cuyo nombre empieza por el t??rmino buscado.
     *
     * Decisiones de dise??o:
     *   ??? 'name LIKE t??rmino%' (prefijo) en vez de '%t??rmino%' (contiene):
     *     es m??s r??pido con ??ndice en la columna 'name' y produce sugerencias
     *     m??s relevantes (el usuario est?? completando lo que est?? escribiendo).
     *   ??? get(['id', 'name', 'price', 'image']): solo las columnas necesarias,
     *     evita traer description, stock, etc. que no se muestran en el dropdown.
     *   ??? mb_strlen() en vez de strlen() para contar correctamente caracteres UTF-8
     *     (tildes, ??, etc. son 2 bytes en strlen pero 1 car??cter en mb_strlen).
     *   ??? number_format((float) $product->price, 2): castea a float antes de formatear
     *     para evitar errores si price llega como string desde la BD.
     *   ??? ->values() al final reindexia el array antes de serializarlo a JSON,
     *     evitando que se serialice como objeto ({0:..., 1:...}) en vez de array ([...]).
     */
    public function suggestions(Request $request)
    {
        $query = trim((string) $request->input('q', ''));

        if (mb_strlen($query) < 1) {
            return response()->json(['items' => []]);
        }

        $searchTerm = $query . '%';

        $products = Product::query()
            ->where('active', true)
            ->where('name', 'like', $searchTerm)
            ->orderBy('name')
            ->limit(8)
            ->get(['id', 'name', 'price', 'image']);

        $items = $products->map(function (Product $product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => number_format((float) $product->price, 2),
                'image' => $product->image,
                'url' => route('products.show', $product),
            ];
        })->values();

        return response()->json(['items' => $items]);
    }

    /**
     * Construye el array de facetas disponibles para el cat??logo.
     *
     * Fuentes de facetas:
     *   1. Categor??as: se construye a partir de $categories (ya consultadas en index())
     *      para evitar una segunda query. Cada categor??a se convierte en una option
     *      con value=(string)id y label=name.
     *
     *   2. Product::catalogAttributeFacets(): m??todo est??tico del modelo que devuelve
     *      las facetas de atributos (per??odo geol??gico, dieta, tipo de locomoci??n, etc.)
     *      definidas como constantes en el modelo. Cada faceta tiene filter_type y options.
     *
     * El array resultante tiene la forma:
     *   [
     *     'categories' => [ label, chip_label, request_key, filter_type, value_type, options[] ],
     *     'period'     => [ label, chip_label, request_key, filter_type, column, value_type, options[] ],
     *     ...
     *   ]
     *
     * Este array es el mismo que se pasa a index() para armar los filtros del sidebar.
     */
    protected function buildFilterFacets(Collection $categories): array
    {
        $facets = [
            'categories' => [
                'label' => 'Categorias',
                'chip_label' => 'Categoria',
                'request_key' => 'categories',
                'filter_type' => 'categories',
                'value_type' => 'int',
                'legacy_request_key' => 'category_id', // soporta URLs antiguas con ?category_id=X
                'options' => $categories
                    ->map(fn (Category $category) => [
                        'value' => (string) $category->id,
                        'label' => $category->name,
                    ])
                    ->values()
                    ->all(),
            ],
        ];

        foreach (Product::catalogAttributeFacets() as $facetKey => $facetDefinition) {
            $facets[$facetKey] = [
                'label' => $facetDefinition['label'],
                'chip_label' => $facetDefinition['chip_label'] ?? $facetDefinition['label'],
                'request_key' => $facetKey,
                'filter_type' => $facetDefinition['filter_type'] ?? 'column',
                'column' => $facetDefinition['column'] ?? null,
                'value_type' => 'string',
                'options' => collect($facetDefinition['options'])
                    ->map(fn (string $label, string $value) => [
                        'value' => $value,
                        'label' => $label,
                    ])
                    ->values()
                    ->all(),
            ];
        }

        return $facets;
    }

    /**
     * Extrae y sanitiza los valores seleccionados de una faceta desde el request.
     *
     * Proceso de sanitizaci??n:
     *   1. Lee el input como array (puede estar vac??o si no hay checkboxes marcados).
     *   2. Filtra valores vac??os con filled() para descartar cadenas vac??as y null.
     *   3. Castea todos los valores a string para comparaci??n uniforme.
     *   4. Filtra contra la whitelist de valores permitidos (in_array estricto).
     *   5. Elimina duplicados con unique() y reindexia con values().
     *
     * Soporte legacy: si la faceta tiene 'legacy_request_key' y el request trae ese
     * par??metro (p. ej. ?category_id=3 de un enlace antiguo), se intenta recuperar
     * ese valor como si hubiera sido enviado por el filtro nuevo.
     *
     * La whitelist es cr??tica para seguridad: evita que un usuario pueda inyectar
     * valores arbitrarios en el WHERE de la query a trav??s de la URL.
     */
    protected function extractSelectedFacetValues(Request $request, array $facet): array
    {
        $allowedValues = collect($facet['options'])
            ->pluck('value')
            ->all();

        $selectedValues = collect($request->input($facet['request_key'], []))
            ->filter(fn ($value) => filled($value))
            ->map(fn ($value) => (string) $value)
            ->filter(fn ($value) => in_array($value, $allowedValues, true))
            ->unique()
            ->values()
            ->all();

        if (empty($selectedValues) && !empty($facet['legacy_request_key']) && $request->filled($facet['legacy_request_key'])) {
            $legacyValue = (string) $request->input($facet['legacy_request_key']);

            if (in_array($legacyValue, $allowedValues, true)) {
                $selectedValues = [$legacyValue];
            }
        }

        return $selectedValues;
    }

    /**
     * Aplica el filtro de una faceta al query Builder seg??n su filter_type.
     *
     * Tipos de filtro soportados:
     *
     *   'categories':
     *     Filtra por relaci??n many-to-many. Usa whereHas() para hacer un EXISTS subquery
     *     en la tabla pivot. Castea a int con array_map('intval', ...) para evitar
     *     comparaciones de string contra id entero.
     *
     *   'height_range':
     *     Faceta especial para altura de dinosaurios. Cada valor seleccionado es una clave
     *     de rango (p. ej. 'small', 'medium', 'large'). Product::heightRangeDefinition()
     *     devuelve los l??mites {min, max} para cada clave.
     *     Se agrupan con orWhere() porque el usuario quiere ver productos en CUALQUIERA
     *     de los rangos seleccionados (uni??n, no intersecci??n).
     *     Si heightRangeDefinition() devuelve null para una clave inv??lida, se omite.
     *
     *   'column' (default):
     *     Filtro simple whereIn sobre una columna de la tabla products.
     *     $facet['column'] especifica qu?? columna usar (p. ej. 'diet', 'period').
     *     Si column est?? vac??o, no aplica ning??n filtro (evita errores silenciosos).
     */
    protected function applyFacetFilter(Builder $query, array $facet, array $selectedValues): void
    {
        $filterType = $facet['filter_type'] ?? 'column';

        switch ($filterType) {
            case 'categories':
                $categoryIds = array_map('intval', $selectedValues);

                $query->whereHas('categories', function (Builder $categoryQuery) use ($categoryIds) {
                    $categoryQuery->whereIn('categories.id', $categoryIds);
                });
                return;

            case 'height_range':
                // Agrupa todos los rangos seleccionados con OR: el producto cumple si
                // su altura cae dentro de CUALQUIERA de los rangos elegidos.
                $query->where(function (Builder $heightQuery) use ($selectedValues) {
                    foreach ($selectedValues as $rangeKey) {
                        $range = Product::heightRangeDefinition($rangeKey);

                        if ($range === null) {
                            continue;
                        }

                        $heightQuery->orWhere(function (Builder $rangeQuery) use ($range) {
                            $rangeQuery->whereNotNull('height_meters');

                            if ($range['min'] !== null) {
                                $rangeQuery->where('height_meters', '>=', $range['min']);
                            }

                            if ($range['max'] !== null) {
                                $rangeQuery->where('height_meters', '<', $range['max']);
                            }
                        });
                    }
                });
                return;

            default:
                if (! empty($facet['column'])) {
                    $query->whereIn($facet['column'], $selectedValues);
                }
        }
    }
}
