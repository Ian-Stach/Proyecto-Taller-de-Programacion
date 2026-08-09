
<?php $__env->startSection('title', 'Órdenes'); ?>

<?php $__env->startSection('content'); ?>
    <h2 class="mb-4 admin-panel-title">Órdenes</h2>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-9">
            <div class="stat-card h-100">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Usuario</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($order->id); ?></td>
                                    <td>
                                        <span class="d-flex flex-column">
                                            <span class="fw-bold text-dark lh-1"><?php echo e($order->user->name); ?></span>
                                            <span class="text-secondary small"><?php echo e($order->user->email); ?></span>
                                        </span>
                                    </td>
                                    <td><strong>USD</strong> <?php echo e(number_format($order->total_price, 2)); ?></td>
                                    <td>
                                        <?php
                                            $badge = match($order->status) {
                                                'completado' => 'bg-success',
                                                'cancelado'  => 'bg-danger',
                                                default      => 'bg-warning text-dark',
                                            };
                                        ?>
                                        <span class="badge <?php echo e($badge); ?>"><?php echo e(ucfirst($order->status)); ?></span>
                                    </td>
                                    <td><?php echo e($order->date->format('d/m/Y H:i')); ?></td>
                                    <td class="text-end">
                                        <a href="<?php echo e(route('admin.orders.show', $order)); ?>">
                                            <button class="admin-product-card-btn bg-info">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000">
                                                    <path d="M200-800v241-1 400-640 200-200Zm0 720q-33 0-56.5-23.5T120-160v-640q0-33 23.5-56.5T200-880h320l240 240v100q-19-8-39-12.5t-41-6.5v-41H480v-200H200v640h241q16 24 36 44.5T521-80H200Zm531-149q29-29 29-71t-29-71q-29-29-71-29t-71 29q-29 29-29 71t29 71q29 29 71 29t71-29ZM864-40 756-148q-21 14-45.5 21t-50.5 7q-75 0-127.5-52.5T480-300q0-75 52.5-127.5T660-480q75 0 127.5 52.5T840-300q0 26-7 50.5T812-204L920-96l-56 56Z"/>
                                                </svg>
                                            </button>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td class="text-muted text-center" colspan="6">No se encontraron órdenes.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer"><?php echo e($orders->withQueryString()->links()); ?></div>
            </div>
        </div>

        <div class="col-3">
            <div class="stat-card p-2">
                <form class="row g-3 text-start" method="GET" action="<?php echo e(route('admin.orders')); ?>">
                    <div class="col-12 text-white">
                        <label class="form-label" for="search">Usuario</label>
                        <input class="form-control" type="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="Nombre o email...">
                    </div>
                    <div class="col-12 text-white">
                        <label class="form-label" for="status">Estado</label>
                        <select class="form-select" name="status">
                            <option value="">Todos</option>
                            <option value="pendiente"   <?php echo e(request('status') === 'pendiente'   ? 'selected' : ''); ?>>Pendiente</option>
                            <option value="completado"  <?php echo e(request('status') === 'completado'  ? 'selected' : ''); ?>>Completado</option>
                            <option value="cancelado"   <?php echo e(request('status') === 'cancelado'   ? 'selected' : ''); ?>>Cancelado</option>
                        </select>
                    </div>
                    <div class="col-12 d-flex justify-content-between">
                        <button class="btn btn-secondary" type="submit">Buscar</button>
                        <?php if(request()->hasAny(['search', 'status'])): ?>
                            <a class="btn btn-outline-secondary" href="<?php echo e(route('admin.orders')); ?>">Limpiar</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views\admin\orders\index.blade.php ENDPATH**/ ?>