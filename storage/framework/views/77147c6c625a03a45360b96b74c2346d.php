

<?php $__env->startSection('title', 'Editar producto'); ?>

<?php $__env->startSection('content'); ?>
    <div class="d-flex align-items-center gap-3 mb-4">
        <a class="text-muted text-decoration-none"
           href="<?php echo e(route('admin.products')); ?>"
        >← Productos</a>
        <h2 class="mb-0">Editar: <?php echo e($product->name); ?></h2>
    </div>

    <div class="card shadow-sm p-4">
        <?php echo $__env->make('admin.products.partials.form', [
            'formAction' => route('admin.products.update', $product),
            'formMethod' => 'PATCH',
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views\admin\products\edit.blade.php ENDPATH**/ ?>