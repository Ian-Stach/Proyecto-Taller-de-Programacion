

<?php $__env->startSection('content'); ?>
<div class="container-fluid my-5" data-products-page>
    <?php echo $__env->make('products.partials.catalog-content', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<script src="<?php echo e(asset('js/products-index.js')); ?>" defer></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.Jurassic_Store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views/products/index.blade.php ENDPATH**/ ?>