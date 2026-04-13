

<?php $__env->startSection('content'); ?>
<div class="container my-5">
    <h1 class="mb-4">📦 My Orders</h1>

    <?php if($orders->count() > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-warning">
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <strong>#<?php echo e($order->id); ?></strong>
                            </td>
                            <td>
                                <?php echo e($order->created_at->format('M d, Y H:i')); ?>

                            </td>
                            <td>
                                <?php echo e($order->orderItems->count()); ?> item(s)
                            </td>
                            <td class="fw-bold text-warning">
                                $<?php echo e(number_format($order->total_price, 2)); ?>

                            </td>
                            <td>
                                <?php if($order->status === 'completed'): ?>
                                    <span class="badge bg-success">✅ Completed</span>
                                <?php elseif($order->status === 'pending'): ?>
                                    <span class="badge bg-warning text-dark">⏳ Pending</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">❌ Cancelled</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo e(route('orders.show', $order)); ?>" class="btn btn-sm btn-info">
                                    View Details
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
            <h4>No orders yet</h4>
            <p class="mb-3">You haven't placed any orders</p>
            <a href="<?php echo e(route('products.index')); ?>" class="btn btn-warning btn-lg">
                🦕 Start Shopping
            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.Jurassic_Store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views/orders/index.blade.php ENDPATH**/ ?>