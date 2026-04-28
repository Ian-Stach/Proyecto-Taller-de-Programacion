
<?php
    /*
     * Mapa de claves de orden a etiquetas legibles.
     * Se usa para mostrar el chip "Orden: Precio: menor a mayor"
     * cuando hay un orden activo distinto del default.
     */
    $sortLabels = [
        'latest' => 'Más recientes',
        'price_asc' => 'Precio: menor a mayor',
        'price_desc' => 'Precio: mayor a menor',
        'name_asc' => 'Nombre: A-Z',
        'stock_desc' => 'Mayor stock',
    ];

    /*
     * IDs de productos que el usuario autenticado tiene en favoritos.
     * Se obtienen de una vez aquí para evitar N consultas dentro del @forelse.
     * Para guests se usa un array vacío, así el resto del código no necesita
     * chequear Auth::check() repetidamente.
     */
    $favoriteProductIds = Auth::check()
        ? Auth::user()->favorites()->pluck('product_id')->all()
        : [];

    /*
     * $baseParams representa el estado actual de la URL (búsqueda + filtros + orden)
     * como un array asociativo. Se usa como base para construir las URLs de los chips
     * "quitar filtro": se clona y se elimina el parámetro correspondiente.
     * Solo se incluyen parámetros que realmente están activos (no se agrega 'sort'
     * si es el default, ni facetas sin opciones seleccionadas).
     */
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

    /*
     * $activeFilterChips es una colección de chips para renderizar en la barra
     * "Filtros aplicados". Cada chip tiene:
     *   - 'label'      → texto visible (p. ej. "Período: Jurásico").
     *   - 'remove_url' → URL que elimina solo ese filtro y mantiene el resto.
     *
     * Se construye en tres pasadas:
     *   1. Chip de búsqueda (si hay texto en ?search=).
     *   2. Un chip por cada valor seleccionado en cada faceta.
     *      Se usa array_filter + array_values para reconstruir el array sin ese valor.
     *      Si no quedan valores para esa faceta se elimina su clave de $baseParams.
     *   3. Chip de orden (si no es 'latest').
     */
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
            // Reconstruye el array de valores sin el que se está quitando.
            // array_values() reindexia para evitar claves numéricas no consecutivas en la URL.
            $remainingValues = array_values(array_filter(
                $facet['selected'],
                fn ($value) => $value !== $selectedValue
            ));

            $paramsWithoutFacetValue = $baseParams;

            if (empty($remainingValues)) {
                // Si era el único valor seleccionado, elimina la clave completa de la faceta.
                unset($paramsWithoutFacetValue[$facet['request_key']]);
            } else {
                $paramsWithoutFacetValue[$facet['request_key']] = $remainingValues;
            }

            // $facet['option_map'] mapea value → label para mostrar el nombre legible.
            // El ?? $selectedValue es el fallback por si el valor no está en el mapa.
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
                <?php
                    $isFavorite = in_array($product->id, $favoriteProductIds, true);
                ?>

                <div class="product-card">
                    <!-- Imagen -->
                    <a href="<?php echo e(route('products.show', $product)); ?>"
                       class="product-card-link"
                       aria-label="Ver detalle de <?php echo e($product->name); ?>"
                    ></a>

                    <div class="product-card-image-wrap">
                        <?php if($product->image ?? false): ?>
                            <img src="<?php echo e($product->image); ?>"
                                 class="product-card-img"
                                 alt="<?php echo e($product->name); ?>"
                                 onerror="this.classList.add('d-none'); this.nextElementSibling.classList.remove('d-none');"
                            >
                            <div class="product-card-img-placeholder d-none">
                                <span class="text-muted">Sin imagen</span>
                            </div>
                        <?php else: ?>
                            <div class="product-card-img-placeholder">
                                <span class="text-muted">Sin imagen</span>
                            </div>
                        <?php endif; ?>

                        <!-- Corazon favoritos (top-right, visible en hover) -->
                        <?php if(Auth::check()): ?>
                            <form action="<?php echo e($isFavorite ? route('favorites.remove', $product) : route('favorites.add', $product)); ?>"
                                  method="POST"
                                  class="product-card-fav-form"
                                  data-products-favorite-form
                                  data-product-id="<?php echo e($product->id); ?>"
                            >
                                <?php echo csrf_field(); ?>

                                <?php if($isFavorite): ?>
                                    <?php echo method_field('DELETE'); ?>
                                <?php endif; ?>

                                <button type="submit"
                                        class="product-card-fav-btn <?php echo e($isFavorite ? 'is-active' : ''); ?>"
                                        aria-label="<?php echo e($isFavorite ? 'Quitar de favoritos' : 'Agregar a favoritos'); ?>"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" height="22px" viewBox="0 -960 960 960" width="22px" fill="currentColor">
                                        <path d="m480-120-58-52q-101-91-167-157T150-447.5Q111-500 95.5-544T80-634q0-94 63-157t157-63q52 0 99 22t81 62q34-40 81-62t99-22q94 0 157 63t63 157q0 46-15.5 90T810-447.5Q771-395 705-329T538-172l-58 52Z"/>
                                    </svg>
                                </button>
                            </form>
                        <?php endif; ?>

                        <!-- Boton carrito (overlay en hover) -->
                        <?php if(Auth::check() && $product->stock > 0): ?>
                            <div class="product-card-overlay">
                                <form action="<?php echo e(route('cart.add', $product)); ?>"
                                      method="POST"
                                      class="cart-add-form"
                                >
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="product-card-cart-btn">
                                        Añadir al carrito
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Info -->
                    <div class="product-card-body">
                        
                        <div class="product-card-categories">
                            <?php $__empty_2 = true; $__currentLoopData = $product->deepestCategories(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                <span class="product-card-badge"><?php echo e($category->name); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                <span class="product-card-badge product-card-badge--muted">Sin categoria</span>
                            <?php endif; ?>
                        </div>

                        <h5 class="product-card-name"><?php echo e($product->name); ?></h5>

                        
                        <p class="product-card-desc"><?php echo e(Str::limit($product->description, 60)); ?></p>

                        <p class="product-card-price">$<?php echo e(number_format($product->price, 2)); ?></p>

                        <span class="product-card-stock
                            <?php if($product->stock > 5): ?> product-card-stock--ok
                            <?php elseif($product->stock > 0): ?> product-card-stock--low
                            <?php else: ?> product-card-stock--out <?php endif; ?>"
                        >Stock: <?php echo e($product->stock); ?></span>
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
</div>
<?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views/products/partials/results-content.blade.php ENDPATH**/ ?>