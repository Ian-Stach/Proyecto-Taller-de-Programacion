
<?php $__env->startSection('title', "Orden #<?php echo e($order->id); ?>"); ?>

<?php $__env->startSection('content'); ?>
    <h2 class="mb-4 admin-panel-title">Orden #<?php echo e($order->id); ?></h2>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        
        <div class="col-8">
            <div class="stat-card">
                <h5 class="stat-table-header m-0 py-2 fs-4 justify-content-center">Productos</h5>
                <div class="stat-table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="stat-table">
                            <tr>
                                <th></th>
                                <th>Producto</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Precio unit.</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $order->orderItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="text-start">
                                        <?php if($item->product): ?>
                                            <a href="<?php echo e(route('admin.products.edit', $item->product)); ?>">
                                                <button class="admin-product-card-btn bg-info">
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000">
                                                        <path d="M440-183v-274L200-596v274l240 139Zm80 0 240-139v-274L520-457v274Zm-80 92L160-252q-19-11-29.5-29T120-321v-318q0-22 10.5-40t29.5-29l280-161q19-11 40-11t40 11l280 161q19 11 29.5 29t10.5 40v318q0 22-10.5 40T800-252L520-91q-19 11-40 11t-40-11Zm200-528 77-44-237-137-78 45 238 136Zm-160 93 78-45-237-137-78 45 237 137Z"/>
                                                    </svg>
                                                </button>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($item->product): ?>
                                            <a class="text-decoration-none text-dark" href="<?php echo e(route('products.show', $item->product)); ?>"><?php echo e($item->product->name); ?></a>
                                        <?php else: ?>
                                            <span class="text-muted fst-italic">Producto eliminado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center"><?php echo e($item->quantity); ?></td>
                                    <td class="text-end"><strong>USD</strong> <?php echo e(number_format($item->unit_price, 2)); ?></td>
                                    <td class="text-end"><strong>USD</strong> <?php echo e(number_format($item->unit_price * $item->quantity, 2)); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end fw-bold">Total</td>
                                <td class="text-end fw-bold"><strong>USD</strong> <?php echo e(number_format($order->total_price, 2)); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        
        <div class="col-4 d-flex flex-column gap-3">

            
            <div class="stat-card p-3">
                <h6 class="text-white mb-2">Cliente</h6>
                <p class="mb-1 fw-bold text-white"><?php echo e($order->user->name); ?></p>
                <p class="mb-0 text-secondary small"><?php echo e($order->user->email); ?></p>
                <a class="btn btn-sm btn-outline-secondary mt-2" href="<?php echo e(route('admin.users.edit', $order->user)); ?>">Ver usuario</a>
            </div>

            
            <div class="stat-card p-3">
                <h6 class="text-white mb-2">Detalles</h6>
                <dl class="mb-0 text-white small">
                    <dt>Fecha</dt>
                    <dd><?php echo e($order->date->format('d/m/Y H:i')); ?></dd>
                    <dt>Estado actual</dt>
                    <dd>
                        <?php
                            $badge = match($order->status) {
                                'completado' => 'bg-success',
                                'cancelado'  => 'bg-danger',
                                default      => 'bg-warning text-dark',
                            };
                        ?>
                        <span class="badge <?php echo e($badge); ?>"><?php echo e(ucfirst($order->status)); ?></span>
                    </dd>
                </dl>
            </div>

            
            <div class="stat-card p-3">
                <h6 class="text-white mb-2">Cambiar estado</h6>
                <form method="POST" action="<?php echo e(route('admin.orders.status', $order)); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <select class="form-select mb-2" name="status">
                        <option value="pendiente"  <?php echo e($order->status === 'pendiente'  ? 'selected' : ''); ?>>Pendiente</option>
                        <option value="completado" <?php echo e($order->status === 'completado' ? 'selected' : ''); ?>>Completado</option>
                        <option value="cancelado"  <?php echo e($order->status === 'cancelado'  ? 'selected' : ''); ?>>Cancelado</option>
                    </select>
                    <button class="btn btn-secondary w-100" type="submit">Guardar</button>
                </form>
            </div>

            <a class="btn btn-outline-secondary" href="<?php echo e(route('admin.orders')); ?>">← Volver a órdenes</a>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views\admin\orders\show.blade.php ENDPATH**/ ?>