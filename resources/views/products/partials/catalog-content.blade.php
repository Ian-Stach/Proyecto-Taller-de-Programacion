<h1 class="mb-4">Todos los productos</h1>

<div class="py-3 mb-5"
     style="display: flex;
            justify-content: center;"
>
    <form method="GET"
          action="{{ route('products.index') }}"
          //class="products-search-form"
          style="width: 100%;
                 display: flex;
                 justify-centent: center;"
          data-products-async-form
          data-products-search-form
    >
        @foreach($filterFacets as $facet)
            @foreach($facet['selected'] as $selectedValue)
                <input type="hidden"
                       name="{{ $facet['input_name'] }}"
                       value="{{ $selectedValue }}"
                >
            @endforeach
        @endforeach

        @if($currentSort !== 'latest')
            <input type="hidden"
                   name="sort"
                   value="{{ $currentSort }}"
            >
        @endif

        <div //class="input-group products-search-input-group"
             style="width: 90%;
                    max-width: 700px;"        
        >
            <input //class="form-control products-search-input"
                   style="border-radius: 25px 0 0 25px;
                          border: 1px solid #000;"
                   type="search"
                   name="search"
                   placeholder="Buscar productos..."
                   value="{{ request('search') }}"
                   aria-label="Buscar productos"
            >

            <button class="btn products-search-button"
                    type="submit"
                    aria-label="Buscar"
            >
                <svg xmlns="http://www.w3.org/2000/svg"
                     height="24px"
                     viewBox="0 -960 960 960"
                     width="24px"
                     fill="#000000"
                >
                    <path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/>
                </svg>
            </button>
        </div>
    </form>
</div>

<div class="row">
    <aside //class="col-12 col-md-4 col-lg-3 mb-4 ps-3 products-filter-sidebar"
           style="min-width: 0;"
    >
        <div //class="bg-warning p-3 products-filter-panel" style="border-radius: 0.5rem;">
            <div //class="mb-3 pruducts-filter-heading" style="font-weight: 700;">
                <h5 class="mb-0">Filtros</h5>
            </div>

            <form method="GET"
                  action="{{ route('products.index') }}"
                  //class="products-filter-form"
                  style="display: flex; flex-direction: column; gap: 0.75rem;"
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
                    <div //class="products-filter-group"
                         style="display: flex;
                                flex-direction: column;
                                gap: 0.5rem;"
                    >
                        <button class="btn products-filter-toggle w-100"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#{{ $facet['collapse_id'] }}"
                                aria-expanded="{{ $facet['selected_count'] > 0 ? 'true' : 'false' }}"
                                aria-controls="{{ $facet['collapse_id'] }}"
                        >
                            <span>{{ $facet['label'] }}</span>
                            <span //class="products-filter-meta"
                                  style="display: flex;
                                         align-items: center;
                                         gap: 0.6rem;"
                            >
                                <span //class="products-filter-count"
                                      style="min-width: 1.75rem;
                                             height: 1.75rem;
                                             border-radius: 999px;
                                             display: inline-flex;
                                             align-items: center;
                                             justify-content: center;
                                             background: #212529;
                                             color: #fff;
                                             font-size: 0.85rem;"

                                      data-filter-param="{{ $facet['input_name'] }}"
                                >{{ $facet['selected_count'] }}
                                </span>
                                <span //class="products-filter-chevron" 
                                      style="font-size: 0.9rem;">▾</span>
                            </span>
                        </button>

                        <div class="collapse {{ $facet['selected_count'] > 0 ? 'show' : '' }}"
                             id="{{ $facet['collapse_id'] }}"
                        >
                            <div //class="products-filter-options"
                                 style="margin-top: 0.35rem;
                                        max-height: 260px;
                                        overflow-y: auto;
                                        overflow-x; hidden;
                                        padding-right: 0.25rem;"
                            >
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