

<?php $__env->startSection('content'); ?>
<div class="container my-5">
    <h1 class="mb-4">📦 Mis pedidos</h1>

    <?php if($orders->count() > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-warning">
                    <tr>
                        <th>Pedido</th>
                        <th>Fecha</th>
                        <th>Artículos</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $orderSubtotal = $order->orderItems->sum(function ($item) {
                                return $item->unit_price * $item->quantity;
                            });
                            $orderTotal = round($orderSubtotal * 1.1, 2);
                        ?>
                        <tr>
                            <td>
                                <strong>#<?php echo e($order->id); ?></strong>
                            </td>
                            <td>
                                <?php echo e($order->date->translatedFormat('d M Y H:i')); ?>

                            </td>
                            <td>
                                <?php echo e($order->orderItems->count()); ?> artículo(s)
                            </td>
                            <td class="fw-bold text-warning">
                                $<?php echo e(number_format($orderTotal, 2)); ?>

                            </td>
                            <td>
                                <?php if($order->status === 'completed'): ?>
                                    <span class="badge bg-success">✅ Completado</span>
                                <?php elseif($order->status === 'pending'): ?>
                                    <span class="badge bg-warning text-dark">⏳ Pendiente</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">❌ Cancelado</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo e(route('orders.show', $order)); ?>" class="btn btn-sm btn-info">
                                    Ver detalles
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="mt-4">
            <?php echo e($orders->links()); ?>

        </div>
    <?php else: ?>
        <div class="alert alert-info text-center py-5">
            <h4>Aún no tienes pedidos</h4>
            <p class="mb-3">Aún no has realizado ningún pedido</p>
            <a href="<?php echo e(route('products.index')); ?>" class="btn btn-warning btn-lg">
                🦕 Empezar a comprar
            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.Jurassic_Store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views\orders\index.blade.php ENDPATH**/ ?>