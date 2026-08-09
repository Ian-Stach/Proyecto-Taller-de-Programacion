


<?php $__env->startSection('content'); ?>
<div class="container my-5">
    <h1 class="mb-4">❤️ Mis favoritos</h1>

    <?php if($favorites->count() > 0): ?>
        <div class="row">
            <?php $__currentLoopData = $favorites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $favorite): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                
                <?php $product = $favorite->product; ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-warning">
                        
                        <div class="card-header bg-warning text-dark">
                            <strong><?php echo e($product->deepestCategories()->pluck('name')->implode(', ') ?: 'Sin categorias'); ?></strong>
                        </div>
                        <div class="card-body">
                            
                            <div class="product-card-image-wrap mb-3">
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
                            </div>
                            <h5 class="card-title"><?php echo e($product->name); ?></h5>
                            
                            <p class="card-text text-muted"><?php echo e(Str::limit($product->description, 100)); ?></p>
                            <div class="d-flex justify-content-between align-items-center mb-0">
                                <span class="badge bg-info">Stock: <?php echo e($product->stock); ?></span>
                                <span class="h5 text-warning mb-0">$<?php echo e(number_format($product->price, 2)); ?></span>
                            </div>
                        </div>
                        <div class="card-footer bg-light border-0 pt-0">
                            
                            <a href="<?php echo e(route('products.show', $product)); ?>" class="btn btn-sm btn-info w-100 mb-2">
                                Ver producto
                            </a>
                            
                            <form action="<?php echo e(route('favorites.remove', $product)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-danger w-100">
                                    Quitar de favoritos
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <div class="mt-4">
            <?php echo e($favorites->links()); ?>

        </div>
    <?php else: ?>
        
        <div class="alert alert-info text-center py-5">
            <h4>Aún no tienes favoritos</h4>
            <p class="mb-3">¡Agrega algunos productos a tus favoritos!</p>
            <a href="<?php echo e(route('products.index')); ?>" class="btn btn-warning btn-lg">
                🦕 Explorar productos
            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.Jurassic_Store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views\favorites\index.blade.php ENDPATH**/ ?>