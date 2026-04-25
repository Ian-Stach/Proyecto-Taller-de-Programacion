

<?php $__env->startSection('content'); ?>
<div class="container my-5">
    <div class="row">
        <!-- Detalles del Producto -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body p-0">
                    <?php if($product->image): ?>
                        <div class="products-image-wrapper product-detail-image-wrapper rounded overflow-hidden">
                            <img src="<?php echo e($product->image); ?>"
                                 class="card-img-top product-detail-image"
                                 alt="<?php echo e($product->name); ?>"
                                 onerror="this.classList.add('d-none'); this.nextElementSibling.classList.remove('d-none');"
                            >
                            <div class="bg-light products-image-placeholder product-detail-image-placeholder d-none rounded">
                                <span class="text-muted">Sin imagen</span>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="bg-light products-image-placeholder product-detail-image-placeholder rounded">
                            <span class="text-muted">Sin imagen</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Información del Producto -->
        <div class="col-md-6">
            <h1><?php echo e($product->name); ?></h1>
            
            <div class="mb-3 d-flex flex-wrap gap-2">
                <?php $__empty_1 = true; $__currentLoopData = $product->deepestCategories(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <span class="badge bg-warning text-dark fs-6"><?php echo e($category->name); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <span class="badge bg-secondary fs-6">Sin categoria</span>
                <?php endif; ?>
            </div>

            <p class="lead"><?php echo e($product->description); ?></p>

            <h3 class="text-warning mb-3">$<?php echo e(number_format($product->price, 2)); ?></h3>

            <?php if($product->height_meters !== null): ?>
                <div class="mb-3">
                    <strong>Altura:</strong>
                    <span class="badge bg-info text-dark"><?php echo e(number_format((float) $product->height_meters, 2)); ?> m</span>
                </div>
            <?php endif; ?>

            <div class="mb-4">
                <strong>Stock:</strong>
                <span class="badge <?php if($product->stock > 5): ?> bg-success <?php elseif($product->stock > 0): ?> bg-warning text-dark <?php else: ?> bg-danger <?php endif; ?>">
                    <?php echo e($product->stock); ?> unidades
                </span>
            </div>

            <?php if($product->stock > 0): ?>
                <?php if(auth()->guard()->check()): ?>
                    <form action="<?php echo e(route('cart.add', $product)); ?>" method="POST" class="mb-4">
                        <?php echo csrf_field(); ?>
                    
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Cantidad:</label>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" max="<?php echo e($product->stock); ?>" class="form-control product-quantity-input">
                        </div>

                        <button type="submit" class="btn btn-warning btn-lg me-2">
                            🛒 Añadir al carrito
                        </button>
                    </form>
                    <form action="<?php echo e(route('favorites.add', $product)); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-danger btn-lg">❤️ Añadir a favoritos</button>
                    </form>
                     <?php else: ?>
                        <div class="alert alert-info">
                            Inicia sesión para agregar este producto al carrito.
                        </div>
                        <a href="<?php echo e(route('login')); ?>" class="btn btn-warning btn-lg">Iniciar sesión para comprar</a>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-danger">
                    Sin stock
                </div>
            <?php endif; ?>

            <hr>

            <a href="<?php echo e(route('products.index')); ?>" class="btn btn-secondary">
                ← Volver a productos
            </a>
        </div>
    </div>

    <!-- Productos Relacionados -->
    <?php if($relatedProducts->count() > 0): ?>
        <div class="row mt-5 pt-4 border-top">
            <div class="col-md-12">
                <h3>Productos relacionados</h3>
            </div>
            <?php $__currentLoopData = $relatedProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $related): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo e($related->name); ?></h5>
                            <p class="fs-5 fw-bold text-warning">$<?php echo e(number_format($related->price, 2)); ?></p>
                            <a href="<?php echo e(route('products.show', $related)); ?>" class="btn btn-sm btn-info w-100">Detalles</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.Jurassic_Store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views/products/show.blade.php ENDPATH**/ ?>