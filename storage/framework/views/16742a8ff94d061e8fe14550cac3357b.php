<?php
    $sortLabels = [
        'latest' => 'Más recientes',
        'price_asc' => 'Precio: menor a mayor',
        'price_desc' => 'Precio: mayor a menor',
        'name_asc' => 'Nombre: A-Z',
        'stock_desc' => 'Mayor stock',
    ];

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
?>

<div data-products-results class="products-results-panel">
    <?php if($activeFilterChips->isNotEmpty()): ?>
        <div class="mb-3 products-active-filters">
            <span class="products-active-filters-label">Filtros aplicados:</span>
            <div class="products-active-filters-list">
                <?php $__currentLoopData = $activeFilterChips; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activeFilterChip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e($activeFilterChip['remove_url']); ?>"
                       class="products-filter-chip"
                       data-products-async-link
                       aria-label="Quitar filtro <?php echo e($activeFilterChip['label']); ?>"
                    >
                        <span><?php echo e($activeFilterChip['label']); ?></span>
                        <span class="products-filter-chip-remove" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                <path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/>
                            </svg>
                        </span>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <a href="<?php echo e(route('products.index')); ?>"
                   class="products-clear-filters"
                   data-products-async-link
                >Limpiar filtros
                </a>
            </div>
        </div>
    <?php endif; ?>

    <div class="mb-4 products-results-bar">
        <p class="mb-0 products-results-count">
            Resultados encontrados: <strong><?php echo e($products->total()); ?></strong>
        </p>

        <form method="GET"
              action="<?php echo e(route('products.index')); ?>"
              class="products-sort-form"
              data-products-async-form
        >
            <?php if(request()->filled('search')): ?>
                <input type="hidden"
                       name="search"
                       value="<?php echo e(request('search')); ?>"
                >
            <?php endif; ?>

            <?php $__currentLoopData = $filterFacets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $facet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $__currentLoopData = $facet['selected']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $selectedValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <input type="hidden"
                           name="<?php echo e($facet['input_name']); ?>"
                           value="<?php echo e($selectedValue); ?>"
                    >
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

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
                    <option value="latest" <?php echo e($currentSort === 'latest' ? 'selected' : ''); ?>>Más recientes</option>
                    <option value="price_asc" <?php echo e($currentSort === 'price_asc' ? 'selected' : ''); ?>>Precio: menor a mayor</option>
                    <option value="price_desc" <?php echo e($currentSort === 'price_desc' ? 'selected' : ''); ?>>Precio: mayor a menor</option>
                    <option value="name_asc" <?php echo e($currentSort === 'name_asc' ? 'selected' : ''); ?>>Nombre: A-Z</option>
                    <option value="stock_desc" <?php echo e($currentSort === 'stock_desc' ? 'selected' : ''); ?>>Mayor stock</option>
                </select>
            </div>
        </form>
    </div>

    <div class="row">
        <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="col-6 col-md-4 col-lg-3 mb-4">
                <div class="card h-100 shadow-sm">
                    <?php if($product->image ?? false): ?>
                        <div class="products-image-wrapper products-grid-image-wrapper">
                            <img src="<?php echo e($product->image); ?>"
                                 class="card-img-top products-grid-image"
                                 alt="<?php echo e($product->name); ?>"
                                 onerror="this.classList.add('d-none'); this.nextElementSibling.classList.remove('d-none');"
                            >
                            <div class="bg-light products-image-placeholder d-none">
                                <span class="text-muted">Sin imagen</span>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="bg-light products-image-placeholder">
                            <span class="text-muted">Sin imagen</span>
                        </div>
                    <?php endif; ?>

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo e($product->name); ?></h5>
                        <p class="card-text text-muted small mb-2"><?php echo e(Str::limit($product->description, 60)); ?></p>

                        <div class="mb-2 d-flex flex-wrap gap-1">
                            <?php $__empty_2 = true; $__currentLoopData = $product->deepestCategories(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                <span class="badge bg-dark text-light"><?php echo e($category->name); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                <span class="badge bg-secondary">Sin categoria</span>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <span class="badge 
                                <?php if($product->stock > 5): ?> bg-success 
                                <?php elseif($product->stock > 0): ?> bg-warning text-dark
                                <?php else: ?> bg-danger <?php endif; ?>"
                            >
                                Stock: <?php echo e($product->stock); ?>

                            </span>
                        </div>
                        <div class="mt-auto">
                            <p class="fs-5 fw-bold text-warning mb-3">$<?php echo e(number_format($product->price, 2)); ?></p>

                            <div class="d-flex gap-2">
                                <a href="<?php echo e(route('products.show', $product)); ?>"
                                   class="btn btn-sm btn-info flex-grow-1"
                                >👁️ Detalles
                                </a>
                                <?php if(Auth::check()): ?>
                                    <form action="<?php echo e(route('favorites.add', $product)); ?>"
                                        method="POST"
                                        class="d-inline"
                                    >
                                    <?php echo csrf_field(); ?>
                                        <button type="submit"
                                                class="btn btn-sm btn-danger"
                                                aria-label="Agregar a favoritos"
                                        >❤️
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-12">
                <div class="alert alert-info">
                    No se encontraron productos.
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="mt-1 products-pagination">
        <?php echo e($products->withQueryString()->links()); ?>

    </div>
</div><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views/products/partials/results-content.blade.php ENDPATH**/ ?>