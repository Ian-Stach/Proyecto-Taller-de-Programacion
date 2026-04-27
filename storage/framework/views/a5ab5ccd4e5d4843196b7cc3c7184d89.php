

<?php $__env->startSection('content'); ?>
<?php
    $isFavorite = Auth::check()
        ? Auth::user()->favorites()->where('product_id', $product->id)->exists()
        : false;

    $habitatLabel = $product->habitat !== null
        ? (\App\Models\Product::HABITAT_OPTIONS[$product->habitat] ?? ucfirst($product->habitat))
        : null;

    $dietLabel = $product->diet !== null
        ? (\App\Models\Product::DIET_OPTIONS[$product->diet] ?? ucfirst($product->diet))
        : null;

    $eraLabel = $product->era !== null
        ? (\App\Models\Product::ERA_OPTIONS[$product->era] ?? ucfirst($product->era))
        : null;
?>

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
            <div class="product-detail-header mb-2">
                <h1 class="mb-0"><?php echo e($product->name); ?></h1>

                <?php if(auth()->guard()->check()): ?>
                    <form action="<?php echo e($isFavorite ? route('favorites.remove', $product) : route('favorites.add', $product)); ?>"
                          method="POST"
                          class="product-detail-fav-form"
                    >
                        <?php echo csrf_field(); ?>

                        <?php if($isFavorite): ?>
                            <?php echo method_field('DELETE'); ?>
                        <?php endif; ?>

                        <button type="submit"
                                class="product-detail-fav-btn <?php echo e($isFavorite ? 'is-active' : ''); ?>"
                                aria-label="<?php echo e($isFavorite ? 'Quitar de favoritos' : 'Agregar a favoritos'); ?>"
                                title="<?php echo e($isFavorite ? 'Quitar de favoritos' : 'Agregar a favoritos'); ?>"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" height="26px" viewBox="0 -960 960 960" width="26px" fill="currentColor">
                                <path d="m480-120-58-52q-101-91-167-157T150-447.5Q111-500 95.5-544T80-634q0-94 63-157t157-63q52 0 99 22t81 62q34-40 81-62t99-22q94 0 157 63t63 157q0 46-15.5 90T810-447.5Q771-395 705-329T538-172l-58 52Z"/>
                            </svg>
                        </button>
                    </form>
                <?php endif; ?>
            </div>
            
            <div class="mb-3 d-flex flex-wrap gap-2">
                <?php $__empty_1 = true; $__currentLoopData = $product->deepestCategories(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <span class="badge bg-warning text-dark fs-6"><?php echo e($category->name); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <span class="badge bg-secondary fs-6">Sin categoria</span>
                <?php endif; ?>
            </div>

            <p class="lead"><?php echo e($product->description); ?></p>

            <h3 class="text-warning mb-3">$<?php echo e(number_format($product->price, 2)); ?></h3>

            <div class="mb-3 product-detail-attributes">
                <?php if($product->height_meters !== null): ?>
                    <div class="product-detail-attribute-item">
                        <strong>Altura:</strong>
                        <span class="badge bg-info text-dark"><?php echo e(number_format((float) $product->height_meters, 2)); ?> m</span>
                    </div>
                <?php endif; ?>

                <?php if($habitatLabel): ?>
                    <div class="product-detail-attribute-item">
                        <strong>Hábitat:</strong>
                        <span class="badge bg-primary"><?php echo e($habitatLabel); ?></span>
                    </div>
                <?php endif; ?>

                <?php if($dietLabel): ?>
                    <div class="product-detail-attribute-item">
                        <strong>Dieta:</strong>
                        <span class="badge bg-success"><?php echo e($dietLabel); ?></span>
                    </div>
                <?php endif; ?>

                <?php if($eraLabel): ?>
                    <div class="product-detail-attribute-item">
                        <strong>Era:</strong>
                        <span class="badge bg-secondary"><?php echo e($eraLabel); ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <strong>Stock:</strong>
                <span class="badge <?php if($product->stock > 5): ?> bg-success <?php elseif($product->stock > 0): ?> bg-warning text-dark <?php else: ?> bg-danger <?php endif; ?>">
                    <?php echo e($product->stock); ?> unidades
                </span>
            </div>

            <?php if($product->stock > 0): ?>
                <?php if(auth()->guard()->check()): ?>
                    <form action="<?php echo e(route('cart.add', $product)); ?>"
                          method="POST" class="mb-4"
                    >
                        <?php echo csrf_field(); ?>
                    
                        <div class="mb-3">
                            <label for="quantity"
                                   class="form-label"
                            >Cantidad:
                            </label>
                            <input type="number"
                                   name="quantity"
                                   id="quantity"
                                   value="1"
                                   min="1"
                                   max="<?php echo e($product->stock); ?>"
                                   class="form-control product-quantity-input"
                            >
                        </div>

                        <button type="submit"
                                class="btn btn-warning btn-lg me-2"
                        >🛒 Añadir al carrito
                        </button>
                    </form>
                     <?php else: ?>
                        <button type="button"
                                class="btn btn-warning btn-lg"
                                data-bs-toggle="modal"
                                data-bs-target="#loginModal"
                        >Iniciar sesión para comprar</button>
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