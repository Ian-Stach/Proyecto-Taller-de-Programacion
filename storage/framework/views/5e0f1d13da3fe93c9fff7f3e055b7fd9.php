

<?php $__env->startSection('content'); ?>
<div class="container my-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>📦 Order #<?php echo e($order->id); ?></h1>
        </div>
        <div class="col-md-4 text-end">
            <?php if($order->status === 'completed'): ?>
                <span class="badge bg-success fs-5">✅ Completed</span>
            <?php elseif($order->status === 'pending'): ?>
                <span class="badge bg-warning text-dark fs-5">⏳ Pending</span>
            <?php else: ?>
                <span class="badge bg-danger fs-5">❌ Cancelled</span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Información de la Orden -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning">
                    <strong>Order Information</strong>
                </div>
                <div class="card-body">
                    <p><strong>Order ID:</strong> #<?php echo e($order->id); ?></p>
                    <p><strong>Date:</strong> <?php echo e($order->created_at->format('M d, Y H:i')); ?></p>
                    <p><strong>Customer:</strong> <?php echo e(Auth::user()->name); ?></p>
                    <p><strong>Email:</strong> <?php echo e(Auth::user()->email); ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning">
                    <strong>Order Total</strong>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <strong>Subtotal:</strong>
                        <span>$<?php echo e(number_format($order->total_price, 2)); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <strong>Tax (10%):</strong>
                        <span>$<?php echo e(number_format($order->total_price * 0.10, 2)); ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fs-5 fw-bold">
                        <strong>Total:</strong>
                        <span class="text-warning">$<?php echo e(number_format($order->total_price * 1.10, 2)); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Items de la Orden -->
    <div class="card">
        <div class="card-header bg-warning">
            <strong>Order Items</strong>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $order->orderItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <a href="<?php echo e(route('products.show', $item->product)); ?>">
                                        <?php echo e($item->product->name); ?>

                                    </a>
                                </td>
                                <td>$<?php echo e(number_format($item->unit_price, 2)); ?></td>
                                <td><?php echo e($item->quantity); ?></td>
                                <td class="fw-bold text-warning">
                                    $<?php echo e(number_format($item->unit_price * $item->quantity, 2)); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Acciones -->
    <div class="mt-4">
        <a href="<?php echo e(route('orders.index')); ?>" class="btn btn-secondary">
            ← Back to Orders
        </a>
        <a href="<?php echo e(route('products.index')); ?>" class="btn btn-warning">
            Continue Shopping
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.Jurassic_Store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views/orders/show.blade.php ENDPATH**/ ?>