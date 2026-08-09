

<?php $__env->startSection('title', 'Productos'); ?>

<?php $__env->startSection('content'); ?>
    <h2 class="mb-4 admin-panel-title">Productos</h2>
    
    <div class="row">
        <div class="col-8">
            <div class="row g-3">
                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                        <div class="admin-product-card <?php echo e($product->active ? 'bg-success' : 'bg-danger'); ?>">
                            <div class="position-relative overflow-hidden flex-shrink-0 rounded-2" style="height: 180px; background: #fff">
                                <?php if($product->image): ?>
                                    <img src="<?php echo e($product->image); ?>" class="object-fit-contain w-100 h-100" alt="<?php echo e($product->name); ?>">
                                <?php else: ?>
                                    <div class="d-flex align-items-center justify-content-center" style="height: 100%;">
                                        <span class="text-muted">Sin imagen</span>
                                    </div>
                                <?php endif; ?>
                    
                                <div class="admin-product-card-overlay">
                                    <div>
                                        <p class="mb-1 px-1"><strong>Nombre:</strong> <?php echo e($product->name); ?></p>
                                        <p class="mb-1"><strong>ID:</strong> <?php echo e($product->id); ?></p>
                                        <p class="mb-1"><strong>Precio:</strong> $<?php echo e(number_format($product->price, 2)); ?></p>
                                        <p class="mb-1"><strong>Stock:</strong> <?php echo e($product->stock); ?></p>
                                    </div>

                                    <div class="row g-1">
                                        <div class="col-4">

                                            <form method="POST" action="<?php echo e(route('admin.products.toggle', $product)); ?>">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>
                                                <input type="hidden" name="active" value="<?php echo e($product->active ? 'btn-outline-warning' : 'btn-outline-success'); ?>">
                                                <button class="admin-product-card-btn bg-warning">
                                                    <?php if($product->active): ?>
                                                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000">
                                                            <path d="m644-428-58-58q9-47-27-88t-93-32l-58-58q17-8 34.5-12t37.5-4q75 0 127.5 52.5T660-500q0 20-4 37.5T644-428Zm128 126-58-56q38-29 67.5-63.5T832-500q-50-101-143.5-160.5T480-720q-29 0-57 4t-55 12l-62-62q41-17 84-25.5t90-8.5q151 0 269 83.5T920-500q-23 59-60.5 109.5T772-302Zm20 246L624-222q-35 11-70.5 16.5T480-200q-151 0-269-83.5T40-500q21-53 53-98.5t73-81.5L56-792l56-56 736 736-56 56ZM222-624q-29 26-53 57t-41 67q50 101 143.5 160.5T480-280q20 0 39-2.5t39-5.5l-36-38q-11 3-21 4.5t-21 1.5q-75 0-127.5-52.5T300-500q0-11 1.5-21t4.5-21l-84-82Zm319 93Zm-151 75Z"/>
                                                        </svg>
                                                    <?php else: ?>
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000">
                                                        <path d="M607.5-372.5Q660-425 660-500t-52.5-127.5Q555-680 480-680t-127.5 52.5Q300-575 300-500t52.5 127.5Q405-320 480-320t127.5-52.5Zm-204-51Q372-455 372-500t31.5-76.5Q435-608 480-608t76.5 31.5Q588-545 588-500t-31.5 76.5Q525-392 480-392t-76.5-31.5ZM214-281.5Q94-363 40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200q-146 0-266-81.5ZM480-500Zm207.5 160.5Q782-399 832-500q-50-101-144.5-160.5T480-720q-113 0-207.5 59.5T128-500q50 101 144.5 160.5T480-280q113 0 207.5-59.5Z"/>
                                                    </svg>
                                                    <?php endif; ?>
                                                </button>
                                            </form>
                                        </div>

                                        <div class="col-4">
                                            <a href="<?php echo e(route('admin.products.edit', $product)); ?>">
                                                <button  class="admin-product-card-btn bg-info">
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000">
                                                        <path d="M560-80v-123l221-220q9-9 20-13t22-4q12 0 23 4.5t20 13.5l37 37q8 9 12.5 20t4.5 22q0 11-4 22.5T903-300L683-80H560Zm300-263-37-37 37 37ZM620-140h38l121-122-18-19-19-18-122 121v38ZM240-80q-33 0-56.5-23.5T160-160v-640q0-33 23.5-56.5T240-880h320l240 240v120h-80v-80H520v-200H240v640h240v80H240Zm280-400Zm241 199-19-18 37 37-18-19Z"/>
                                                    </svg>
                                                </button>
                                            </a>
                                        </div>

                                        <div class="col-4">
                                            <form method="POST" action="<?php echo e(route('admin.products.destroy', $product)); ?>" onsubmit="return confirm('¿Eliminar «<?php echo e(addslashes($product->name)); ?>»? Esta acción no se puede deshacer.')">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button class="admin-product-card-btn bg-danger">
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000">
                                                        <path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/>
                                                </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-12">
                        <div class="alert alert-secondary text-center">
                            No hay productos que coincidan con los filtros.
                        </div>
                    </div>
                <?php endif; ?>
                
            </div>
            <div class="mt-3">
                <?php echo e($products->links()); ?>

            </div>
        </div>

        <div class="col-4">
            <div class="stat-card p-2">
                
                <form class="row g-3 text-start" method="GET" action="<?php echo e(route('admin.products')); ?>">

                    <div class="col-sm-6 col-md-12 text-start text-white">
                        <label class="form-label" for="search">Nombre</label>
                        <input class="form-control" type="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="Buscar por nombre...">
                    </div>

                    <div class="col-6 text-start text-white">
                        <label class="form-label" for="active">Estado</label>
                        <select class="form-select" name="active">
                            <option value="">Todos</option>
                            <option value="1" <?php echo e(request('active') === '1' ? 'selected' : ''); ?>>Activos</option>
                            <option value="0" <?php echo e(request('active') === '0' ? 'selected' : ''); ?>>Inactivos</option>
                        </select>
                    </div>

                    <div class="col-6 text-start text-white">
                        <label class="form-label" for="stock">Stock</label>
                        <input class="form-control" type="number" name="stock" id="stock" value="<?php echo e(request('stock')); ?>" placeholder="Ej: 50">
                    </div>

                    <div class="col-12 text-start text-white">
                        <label class="form-label" for="price">Precio</label>
                        <input class="form-control" type="number" step="0.01" name="price" id="price" value="<?php echo e(request('price')); ?>" placeholder="Ej: 1500.00">
                    </div>

                    <div class="col-6 text-start text-white">
                        <label class="form-label" for="habitat">Hábitat</label>
                        <select class="form-select" name="habitat" id="habitat">
                            <option value="">-- Todos --</option>
                            <?php $__currentLoopData = \App\Models\Product::HABITAT_OPTIONS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>" <?php echo e(request('habitat') === $key ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="col-6 text-start text-white">
                        <label class="form-label" for="diet">Dieta</label>
                        <select class="form-select" name="diet" id="diet">
                            <option value="">-- Todos --</option>
                            <?php $__currentLoopData = \App\Models\Product::DIET_OPTIONS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>" <?php echo e(request('diet') === $key ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="col-6 text-start text-white">
                        <label class="form-label" for="height_meters">Altura (m)</label>
                        <input class="form-control" type="number" step="0.01" name="height_meters" id="height_meters" value="<?php echo e(request('height_meters')); ?>" placeholder="Ej: 2.5">
                    </div>

                    <div class="col-6 text-start text-white">
                        <label class="form-label" for="era">Era</label>
                        <select class="form-select" name="era" id="era">
                            <option value="">-- Todos --</option>
                            <?php $__currentLoopData = \App\Models\Product::ERA_OPTIONS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>" <?php echo e(request('era') === $key ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>


                    <div class="col-12 d-flex justify-content-between">
                        <button class="btn btn-secondary" type="submit">Buscar</button>
                        <?php if(request()->hasAny(['search', 'active', 'stock', 'price', 'habitat', 'diet', 'altura', 'era'])): ?>
                            <a class="btn btn-outline-secondary" href="<?php echo e(route('admin.products')); ?>">Limpiar</a>
                        <?php endif; ?>
                        <a class="btn btn-warning fw-semibold" href="<?php echo e(route('admin.products.create')); ?>">+ Nuevo</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views\admin\products\index.blade.php ENDPATH**/ ?>