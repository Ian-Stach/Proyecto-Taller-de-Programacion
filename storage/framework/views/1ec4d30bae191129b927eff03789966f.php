

<?php $__env->startSection('content'); ?>
<div class="container my-5">
    <h1 class="mb-4">❤️ My Favorites</h1>

    <?php if($favorites->count() > 0): ?>
        <div class="row">
            <?php $__currentLoopData = $favorites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $favorite): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-warning">
                        <div class="card-header bg-warning text-dark">
                            <strong><?php echo e($favorite->product->category->name ?? 'Uncategorized'); ?></strong>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <span style="font-size: 3rem;"><?php echo e($favorite->product->dinosaur_emoji ?? '🦕'); ?></span>
                            </div>
                            <h5 class="card-title"><?php echo e($favorite->product->name); ?></h5>
                            <p class="card-text text-muted"><?php echo e(Str::limit($favorite->product->description, 100)); ?></p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-info">Stock: <?php echo e($favorite->product->stock); ?></span>
                                <span class="h5 text-warning mb-0">$<?php echo e(number_format($favorite->product->price, 2)); ?></span>
                            </div>
                        </div>
                        <div class="card-footer bg-light">
                            <a href="<?php echo e(route('products.show', $favorite->product)); ?>" class="btn btn-sm btn-info w-100 mb-2">
                                View Product
                            </a>
                            <form action="<?php echo e(route('favorites.remove', $favorite->product)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-danger w-100">
                                    Remove from Favorites
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <!-- Paginación -->
        <div class="mt-4">
            <?php echo e($favorites->links()); ?>

        </div>
    <?php else: ?>
        <div class="alert alert-info text-center py-5">
            <h4>No favorites yet</h4>
            <p class="mb-3">Add some products to your favorites!</p>
            <a href="<?php echo e(route('products.index')); ?>" class="btn btn-warning btn-lg">
                🦕 Browse Products
            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.Jurassic_Store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views/favorites/index.blade.php ENDPATH**/ ?>