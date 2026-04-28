
<div class="row">

    
    <aside class="col-12 col-md-4 col-lg-3 mb-4 ps-3 products-filter-sidebar">
        <div class="bg-warning p-3 products-filter-panel">
            <div class="mb-3 products-filter-heading">
                <h5 class="mb-0">Filtros</h5>
            </div>

            <form method="GET"
                  action="<?php echo e(route('products.index')); ?>"
                  class="products-filter-form"
                  data-products-async-form
                  data-products-filter-form
            >
                
                <?php if(request()->filled('search')): ?>
                    <input type="hidden"
                           name="search"
                           value="<?php echo e(request('search')); ?>"
                    >
                <?php endif; ?>

                
                <?php if($currentSort !== 'latest'): ?>
                    <input type="hidden"
                           name="sort"
                           value="<?php echo e($currentSort); ?>"
                    >
                <?php endif; ?>

                
                <?php $__currentLoopData = $filterFacets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $facet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="products-filter-group">
                        <button class="btn products-filter-toggle w-100"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#<?php echo e($facet['collapse_id']); ?>"
                                aria-expanded="<?php echo e($facet['selected_count'] > 0 ? 'true' : 'false'); ?>"
                                aria-controls="<?php echo e($facet['collapse_id']); ?>"
                        >
                            <span><?php echo e($facet['label']); ?></span>
                            <span class="products-filter-meta">
                                
                                <span class="products-filter-count"
                                      data-filter-param="<?php echo e($facet['input_name']); ?>"
                                ><?php echo e($facet['selected_count']); ?>

                                </span>
                                <span class="products-filter-chevron">▾</span>
                            </span>
                        </button>

                        <div class="collapse <?php echo e($facet['selected_count'] > 0 ? 'show' : ''); ?>"
                             id="<?php echo e($facet['collapse_id']); ?>"
                        >
                            <div class="products-filter-options">
                                <?php $__currentLoopData = $facet['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="products-filter-option">
                                        <input type="checkbox"
                                               name="<?php echo e($facet['input_name']); ?>"
                                               value="<?php echo e($option['value']); ?>"
                                               <?php echo e(in_array($option['value'], $facet['selected'], true) ? 'checked' : ''); ?>

                                        >
                                        <span><?php echo e($option['label']); ?></span>
                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </form>
        </div>
    </aside>

    
    <main class="col-12 col-md-8 col-lg-9 products-results-main">
        <?php echo $__env->make('products.partials.results-content', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </main>
</div><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views\products\partials\catalog-content.blade.php ENDPATH**/ ?>