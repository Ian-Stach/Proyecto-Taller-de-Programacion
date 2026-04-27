@php
    $sortLabels = [
        'latest' => 'Más recientes',
        'price_asc' => 'Precio: menor a mayor',
        'price_desc' => 'Precio: mayor a menor',
        'name_asc' => 'Nombre: A-Z',
        'stock_desc' => 'Mayor stock',
    ];

    $favoriteProductIds = Auth::check()
        ? Auth::user()->favorites()->pluck('product_id')->all()
        : [];

    $baseParams = [];

    if (request()->filled('search')) {
        $baseParams['search'] = request('search');
    }

    foreach($filterFacets as $facet) {
        if ($facet['selected_count'] > 0) {
            $baseParams[$facet['request_key']] = $facet['selected'];
        }
    }

    if ($currentSort !== 'latest') {
        $baseParams['sort'] = $currentSort;
    }

    $activeFilterChips = collect();

    if (request()->filled('search')) {
        $paramsWithoutSearch = $baseParams;
        unset($paramsWithoutSearch['search']);

        $activeFilterChips->push([
            'label' => 'Búsqueda: ' . request('search'),
            'remove_url' => route('products.index', $paramsWithoutSearch),
        ]);
    }

    foreach ($filterFacets as $facet) {
        foreach ($facet['selected'] as $selectedValue) {
            $remainingValues = array_values(array_filter(
                $facet['selected'],
                fn ($value) => $value !== $selectedValue
            ));

            $paramsWithoutFacetValue = $baseParams;

            if (empty($remainingValues)) {
                unset($paramsWithoutFacetValue[$facet['request_key']]);
            } else {
                $paramsWithoutFacetValue[$facet['request_key']] = $remainingValues;
            }

            $activeFilterChips->push([
                'label' => $facet['chip_label'] . ': ' . ($facet['option_map'][$selectedValue] ?? $selectedValue),
                'remove_url' => route('products.index', $paramsWithoutFacetValue),
            ]);
        }
    }

    if ($currentSort !== 'latest') {
        $paramsWithoutSort = $baseParams;
        unset($paramsWithoutSort['sort']);

        $activeFilterChips->push([
            'label' => 'Orden: ' . ($sortLabels[$currentSort] ?? 'Más recientes'),
            'remove_url' => route('products.index', $paramsWithoutSort),
        ]);
    }
@endphp

<div data-products-results class="products-results-panel">
    @if($activeFilterChips->isNotEmpty())
        <div class="mb-3 products-active-filters">
            <span class="products-active-filters-label">Filtros aplicados:</span>
            <div class="products-active-filters-list">
                @foreach($activeFilterChips as $activeFilterChip)
                    <a href="{{ $activeFilterChip['remove_url'] }}"
                       class="products-filter-chip"
                       data-products-async-link
                       aria-label="Quitar filtro {{ $activeFilterChip['label'] }}"
                    >
                        <span>{{ $activeFilterChip['label'] }}</span>
                        <span class="products-filter-chip-remove" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                <path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/>
                            </svg>
                        </span>
                    </a>
                @endforeach

                <a href="{{ route('products.index') }}"
                   class="products-clear-filters"
                   data-products-async-link
                >Limpiar filtros
                </a>
            </div>
        </div>
    @endif

    <div class="mb-4 products-results-bar">
        <p class="mb-0 products-results-count">
            Resultados encontrados: <strong>{{ $products->total() }}</strong>
        </p>

        <form method="GET"
              action="{{ route('products.index') }}"
              class="products-sort-form"
              data-products-async-form
        >
            @if(request()->filled('search'))
                <input type="hidden"
                       name="search"
                       value="{{ request('search') }}"
                >
            @endif

            @foreach($filterFacets as $facet)
                @foreach($facet['selected'] as $selectedValue)
                    <input type="hidden"
                           name="{{ $facet['input_name'] }}"
                           value="{{ $selectedValue }}"
                    >
                @endforeach
            @endforeach

            <div class="products-sort-control">
                <svg class="products-sort-icon"
                     xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 -960 960 960"
                     aria-hidden="true"
                >
                    <path d="M120-240v-80h240v80H120Zm0-200v-80h480v80H120Zm0-200v-80h720v80H120Z"/>
                </svg>
                <select id="sort"
                        name="sort"
                        class="form-select form-select-sm products-sort-select"
                        aria-label="Ordenar productos"
                >
                    <option value="latest" {{ $currentSort === 'latest' ? 'selected' : '' }}>Más recientes</option>
                    <option value="price_asc" {{ $currentSort === 'price_asc' ? 'selected' : '' }}>Precio: menor a mayor</option>
                    <option value="price_desc" {{ $currentSort === 'price_desc' ? 'selected' : '' }}>Precio: mayor a menor</option>
                    <option value="name_asc" {{ $currentSort === 'name_asc' ? 'selected' : '' }}>Nombre: A-Z</option>
                    <option value="stock_desc" {{ $currentSort === 'stock_desc' ? 'selected' : '' }}>Mayor stock</option>
                </select>
            </div>
        </form>
    </div>

    <div class="row">
        @forelse($products as $product)
            <div class="col-6 col-md-4 col-lg-3 mb-4">
                @php
                    $isFavorite = in_array($product->id, $favoriteProductIds, true);
                @endphp

                <div class="product-card">
                    <!-- Imagen -->
                    <a href="{{ route('products.show', $product) }}"
                       class="product-card-link"
                       aria-label="Ver detalle de {{ $product->name }}"
                    ></a>

                    <div class="product-card-image-wrap">
                        @if($product->image ?? false)
                            <img src="{{ $product->image }}"
                                 class="product-card-img"
                                 alt="{{ $product->name }}"
                                 onerror="this.classList.add('d-none'); this.nextElementSibling.classList.remove('d-none');"
                            >
                            <div class="product-card-img-placeholder d-none">
                                <span class="text-muted">Sin imagen</span>
                            </div>
                        @else
                            <div class="product-card-img-placeholder">
                                <span class="text-muted">Sin imagen</span>
                            </div>
                        @endif

                        <!-- Corazon favoritos (top-right, visible en hover) -->
                        @if(Auth::check())
                            <form action="{{ $isFavorite ? route('favorites.remove', $product) : route('favorites.add', $product) }}"
                                  method="POST"
                                  class="product-card-fav-form"
                                  data-products-favorite-form
                                  data-product-id="{{ $product->id }}"
                            >
                                @csrf

                                @if($isFavorite)
                                    @method('DELETE')
                                @endif

                                <button type="submit"
                                        class="product-card-fav-btn {{ $isFavorite ? 'is-active' : '' }}"
                                        aria-label="{{ $isFavorite ? 'Quitar de favoritos' : 'Agregar a favoritos' }}"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" height="22px" viewBox="0 -960 960 960" width="22px" fill="currentColor">
                                        <path d="m480-120-58-52q-101-91-167-157T150-447.5Q111-500 95.5-544T80-634q0-94 63-157t157-63q52 0 99 22t81 62q34-40 81-62t99-22q94 0 157 63t63 157q0 46-15.5 90T810-447.5Q771-395 705-329T538-172l-58 52Z"/>
                                    </svg>
                                </button>
                            </form>
                        @endif

                        <!-- Boton carrito (overlay en hover) -->
                        @if(Auth::check() && $product->stock > 0)
                            <div class="product-card-overlay">
                                <form action="{{ route('cart.add', $product) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="product-card-cart-btn">
                                        Añadir al carrito
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                    <!-- Info -->
                    <div class="product-card-body">
                        <div class="product-card-categories">
                            @forelse($product->deepestCategories() as $category)
                                <span class="product-card-badge">{{ $category->name }}</span>
                            @empty
                                <span class="product-card-badge product-card-badge--muted">Sin categoria</span>
                            @endforelse
                        </div>

                        <h5 class="product-card-name">{{ $product->name }}</h5>

                        <p class="product-card-desc">{{ Str::limit($product->description, 60) }}</p>

                        <p class="product-card-price">${{ number_format($product->price, 2) }}</p>

                        <span class="product-card-stock
                            @if($product->stock > 5) product-card-stock--ok
                            @elseif($product->stock > 0) product-card-stock--low
                            @else product-card-stock--out @endif"
                        >Stock: {{ $product->stock }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    No se encontraron productos.
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-1 products-pagination">
        {{ $products->withQueryString()->links() }}
    </div>
</div>