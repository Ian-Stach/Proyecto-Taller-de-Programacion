<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ProductController extends Controller
{
    /**
     * Listar todos los productos
     * GET /products
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
            $filterFacets[$facetKey]['collapse_id'] = $facetKey . 'Dropdown';
            $filterFacets[$facetKey]['input_name'] = $facet['request_key'] . '[]';
            $filterFacets[$facetKey]['option_map'] = collect($facet['options'])
                ->mapWithKeys(fn (array $option) => [$option['value'] => $option['label']])
                ->all();

            if (!empty($selectedValues)) {
                $this->applyFacetFilter($query, $facet, $selectedValues);
            }
        }

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('description', 'like', $searchTerm);
            });
        }

        $sort = $request->input('sort', 'latest');
        $allowedSorts = ['latest', 'price_asc', 'price_desc', 'name_asc', 'stock_desc'];

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
                $query->latest();
                 break;
        }

        $products = $query->paginate(12);

        $viewData = [
            'products' => $products,
            'categories' => $categories,
            'filterFacets' => array_values($filterFacets),
            'currentSort' => $sort,
        ];

        if ($request->ajax()) {
            return view('products.partials.results-content', $viewData);
        }

        return view('products.index', $viewData);
    }

    /**
     * Mostrar detalle de un producto
     * GET /products/{product}
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

    protected function buildFilterFacets(Collection $categories): array
    {
        $facets = [
            'categories' => [
                'label' => 'Categorias',
                'chip_label' => 'Categoria',
                'request_key' => 'categories',
                'filter_type' => 'categories',
                'value_type' => 'int',
                'legacy_request_key' => 'category_id',
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
