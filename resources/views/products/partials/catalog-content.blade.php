<div class="row">
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
                @if(request()->filled('search'))
                    <input type="hidden"
                           name="search"
                           value="{{ request('search') }}"
                    >
                @endif

                @if($currentSort !== 'latest')
                    <input type="hidden"
                           name="sort"
                           value="{{ $currentSort }}"
                    >
                @endif

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

    <main class="col-12 col-md-8 col-lg-9 products-results-main">
        @include('products.partials.results-content')
    </main>
</div>