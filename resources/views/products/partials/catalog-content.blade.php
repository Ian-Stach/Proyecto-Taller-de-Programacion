{{--
    catalog-content.blade.php
    --------------------------
    Partial que define la estructura de dos columnas del catálogo de productos:
      • Columna izquierda (<aside>): panel de filtros facetados con checkboxes y colapso.
      • Columna derecha (<main>): resultados, chips de filtros activos, selector de orden
        y grilla de productos. Delega ese contenido a results-content.blade.php.

    Variables recibidas del controlador (ProductController@index):
      - $filterFacets  → array de facetas. Cada faceta tiene:
                           'label', 'collapse_id', 'input_name', 'request_key',
                           'selected_count', 'selected' (valores activos),
                           'options' (array de {value, label}).
      - $currentSort   → clave del orden actual (p. ej. 'latest', 'price_asc').
      - $products      → LengthAwarePaginator con los productos filtrados.
        (El <main> pasa estas variables a results-content.blade.php por herencia de scope.)
--}}
<div class="row">

    {{-- ================================================================
         SIDEBAR DE FILTROS
         Formulario GET que envía los checkboxes seleccionados a products.index.
         data-products-async-form  → el JS intercepta el submit para hacer la petición
                                     sin recargar la página completa.
         data-products-filter-form → el JS identifica este form como el panel de filtros
                                     (distinto del sort-form que está en results-content).
         ================================================================ --}}
    <aside class="col-12 col-md-4 col-lg-3 mb-4 ps-3 products-filter-sidebar">
        <div class="bg-warning p-3 products-filter-panel">
            <div class="mb-3 products-filter-heading">
                <h5 class="mb-0">Filtros</h5>
            </div>

            <form method="GET"
                  action="{{ route('products.index') }}"
                  class="products-filter-form"
                  data-products-async-form
                  data-products-filter-form
            >
                {{-- Si hay búsqueda activa se preserva como hidden input para que
                     al marcar un filtro la búsqueda no se pierda en el submit. --}}
                @if(request()->filled('search'))
                    <input type="hidden"
                           name="search"
                           value="{{ request('search') }}"
                    >
                @endif

                {{-- Igual con el orden: si no es el default ('latest') se preserva.
                     'latest' se omite porque es el valor por defecto del controlador. --}}
                @if($currentSort !== 'latest')
                    <input type="hidden"
                           name="sort"
                           value="{{ $currentSort }}"
                    >
                @endif

                {{-- Cada faceta genera un grupo colapsable con un botón toggle y checkboxes.
                     • El collapse comienza abierto (class 'show') si ya hay opciones
                       seleccionadas en esa faceta ($facet['selected_count'] > 0).
                     • aria-expanded refleja ese mismo estado para accesibilidad.
                     • data-filter-param es leído por el JS para actualizar el contador
                       numérico en tiempo real al marcar/desmarcar sin hacer submit.
                     • in_array(..., true) hace comparación estricta de tipo (no coercitiva). --}}
                @foreach($filterFacets as $facet)
                    <div class="products-filter-group">
                        <button class="btn products-filter-toggle w-100"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#{{ $facet['collapse_id'] }}"
                                aria-expanded="{{ $facet['selected_count'] > 0 ? 'true' : 'false' }}"
                                aria-controls="{{ $facet['collapse_id'] }}"
                        >
                            <span>{{ $facet['label'] }}</span>
                            <span class="products-filter-meta">
                                {{-- Muestra cuántas opciones de esta faceta están seleccionadas --}}
                                <span class="products-filter-count"
                                      data-filter-param="{{ $facet['input_name'] }}"
                                >{{ $facet['selected_count'] }}
                                </span>
                                <span class="products-filter-chevron">▾</span>
                            </span>
                        </button>

                        <div class="collapse {{ $facet['selected_count'] > 0 ? 'show' : '' }}"
                             id="{{ $facet['collapse_id'] }}"
                        >
                            <div class="products-filter-options">
                                @foreach($facet['options'] as $option)
                                    <label class="products-filter-option">
                                        <input type="checkbox"
                                               name="{{ $facet['input_name'] }}"
                                               value="{{ $option['value'] }}"
                                               {{ in_array($option['value'], $facet['selected'], true) ? 'checked' : '' }}
                                        >
                                        <span>{{ $option['label'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </form>
        </div>
    </aside>

    {{-- La columna principal delega todo su contenido a results-content.blade.php,
         que tiene acceso a las mismas variables ($products, $filterFacets, $currentSort)
         por herencia de scope de Blade. --}}
    <main class="col-12 col-md-8 col-lg-9 products-results-main">
        @include('products.partials.results-content')
    </main>
</div>