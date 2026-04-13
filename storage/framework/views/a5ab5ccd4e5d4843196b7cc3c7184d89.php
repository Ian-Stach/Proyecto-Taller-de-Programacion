

<?php $__env->startSection('content'); ?>
<div class="container my-5">
    <div class="row">
        <!-- Detalles del Producto -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body bg-light p-5">
                    <div class="bg-warning p-5 text-center rounded">
                        <h2>🦖 <?php echo e($product->name); ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Producto -->
        <div class="col-md-6">
            <h1><?php echo e($product->name); ?></h1>
            
            <div class="mb-3">
                <span class="badge bg-warning text-dark fs-6"><?php echo e($product->category->name); ?></span>
            </div>

            <p class="lead"><?php echo e($product->description); ?></p>

            <h3 class="text-warning mb-3">$<?php echo e(number_format($product->price, 2)); ?></h3>

            <div class="mb-4">
                <strong>Stock Disponible:</strong>
                <span class="badge <?php if($product->stock > 5): ?> bg-success <?php elseif($product->stock > 0): ?> bg-warning text-dark <?php else: ?> bg-danger <?php endif; ?>">
                    <?php echo e($product->stock); ?> unidades
                </span>
            </div>

            <?php if($product->stock > 0): ?>
                <form action="<?php echo e(route('cart.add')); ?>" method="POST" class="mb-4">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="product_id" value="<?php echo e($product->id); ?>">
                    
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Cantidad:</label>
                        <input type="number" name="quantity" id="quantity" value="1" min="1" max="<?php echo e($product->stock); ?>" class="form-control" style="width: 100px;">
                    </div>

                    <button type="submit" class="btn btn-warning btn-lg me-2">
                        🛒 Add to Cart
                    </button>

                    <?php if(Auth::check()): ?>
                        <form action="<?php echo e(route('favorites.add', $product)); ?>" method="POST" style="display:inline;">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-danger btn-lg">❤️ Add to Favorites</button>
                        </form>
                    <?php endif; ?>
                </form>
            <?php else: ?>
                <div class="alert alert-danger">
                    Out of Stock
                </div>
            <?php endif; ?>

            <hr>

            <a href="<?php echo e(route('products.index')); ?>" class="btn btn-secondary">
                ← Back to Products
            </a>
        </div>
    </div>

    <!-- Productos Relacionados -->
    <?php if($relatedProducts->count() > 0): ?>
        <div class="row mt-5 pt-4 border-top">
            <div class="col-md-12">
                <h3>Related Products</h3>
            </div>
            <?php $__currentLoopData = $relatedProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $related): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo e($related->name); ?></h5>
                            <p class="fs-5 fw-bold text-warning">$<?php echo e(number_format($related->price, 2)); ?></p>
                            <a href="<?php echo e(route('products.show', $related)); ?>" class="btn btn-sm btn-info w-100">View</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.Jurassic_Store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views/products/show.blade.php ENDPATH**/ ?>