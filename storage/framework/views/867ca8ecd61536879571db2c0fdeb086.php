

<?php $__env->startSection('content'); ?>
<div class="container my-5">
    <h1 class="mb-4">🦕 All Products</h1>

    <!-- Filtros de Categoría -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="btn-group" role="group">
                <a href="<?php echo e(route('products.index')); ?>" class="btn btn-outline-warning">All</a>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('products.index', ['category' => $category->name])); ?>" 
                       class="btn btn-outline-warning">
                        <?php echo e($category->name); ?>

                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    <!-- Tarjetas de Productos -->
    <div class="row">
        <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo e($product->name); ?></h5>
                        <p class="card-text text-muted"><?php echo e(Str::limit($product->description, 50)); ?></p>
                        
                        <div class="mb-3">
                            <span class="badge bg-warning text-dark"><?php echo e($product->category->name); ?></span>
                            <span class="badge 
                                <?php if($product->stock > 5): ?> bg-success 
                                <?php elseif($product->stock > 0): ?> bg-warning text-dark
                                <?php else: ?> bg-danger <?php endif; ?>">
                                Stock: <?php echo e($product->stock); ?>

                            </span>
                        </div>

                        <p class="fs-5 fw-bold text-warning">$<?php echo e(number_format($product->price, 2)); ?></p>

                        <div class="d-flex gap-2">
                            <a href="<?php echo e(route('products.show', $product)); ?>" class="btn btn-sm btn-info flex-grow-1">
                                👁️ View
                            </a>
                            <?php if(Auth::check()): ?>
                                <form action="<?php echo e(route('favorites.add', $product)); ?>" method="POST" style="display:inline;">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-sm btn-danger">❤️</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-12">
                <div class="alert alert-info">
                    No products found in this category.
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Paginación -->
    <div class="row mt-5">
        <div class="col-md-12">
            <?php echo e($products->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.Jurassic_Store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views/products/index.blade.php ENDPATH**/ ?>