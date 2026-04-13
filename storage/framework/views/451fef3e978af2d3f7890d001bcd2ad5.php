

<?php $__env->startSection('content'); ?>
<div class="container my-5">
    <h1 class="mb-4">🛒 Shopping Cart</h1>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <?php echo e($errors->first()); ?>

        </div>
    <?php endif; ?>

    <?php if(count($cartItems) > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-warning">
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $cartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <strong><?php echo e($item['product']->name); ?></strong>
                            </td>
                            <td>$<?php echo e(number_format($item['product']->price, 2)); ?></td>
                            <td><?php echo e($item['quantity']); ?></td>
                            <td class="fw-bold text-warning">$<?php echo e(number_format($item['subtotal'], 2)); ?></td>
                            <td>
                                <form action="<?php echo e(route('cart.remove', $item['product']->id)); ?>" method="POST" style="display:inline;">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <!-- Resumen del Carrito -->
        <div class="row mt-4">
            <div class="col-md-8"></div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body">
                        <h5 class="card-title">Order Summary</h5>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Subtotal:</strong>
                            <span>$<?php echo e(number_format($total, 2)); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Tax (10%):</strong>
                            <span>$<?php echo e(number_format($total * 0.10, 2)); ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fs-5 fw-bold">
                            <strong>Total:</strong>
                            <span class="text-warning">$<?php echo e(number_format($total * 1.10, 2)); ?></span>
                        </div>
                    </div>
                </div>

                <form action="<?php echo e(route('checkout.store')); ?>" method="POST" class="mt-3">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-warning btn-lg w-100">
                        💳 Proceed to Checkout
                    </button>
                </form>

                <a href="<?php echo e(route('products.index')); ?>" class="btn btn-secondary w-100 mt-2">
                    Continue Shopping
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center py-5">
            <h4>Your cart is empty</h4>
            <p class="mb-3">Start shopping to add items to your cart!</p>
            <a href="<?php echo e(route('products.index')); ?>" class="btn btn-warning btn-lg">
                🦕 Shop Now
            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.Jurassic_Store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views/cart/show.blade.php ENDPATH**/ ?>