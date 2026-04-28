
<!DOCTYPE html>
<html>
    <head>
        
        <meta charset="UTF-8">

        
        <meta name="viewport"
              content="width=device-width, initial-scale=1.0"
        >

        <title>JURASSIC STORE</title>

        
        <link rel="stylesheet"
              href="<?php echo e(asset('vendor/bootstrap/css/bootstrap.min.css')); ?>"
        >

        
        <link rel="stylesheet"
              href="<?php echo e(asset('css/estilos.css')); ?>"
        >
    </head>

    <body>
        
        <?php echo $__env->make('layouts.partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <?php echo $__env->make('layouts.partials.main-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <?php echo $__env->yieldContent('content'); ?>

        
        <?php if(auth()->guard()->check()): ?>
            <?php echo $__env->make('layouts.partials.cart-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?>

        
        <?php echo $__env->make('layouts.partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <?php if(auth()->guard()->guest()): ?>
            <?php echo $__env->make('auth.partials.modals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?>

        
        <script src="<?php echo e(asset('js/header-search-suggest.js')); ?>"
                defer
        ></script>
        <script src="<?php echo e(asset('vendor/bootstrap/js/bootstrap.bundle.min.js')); ?>"
                defer
        ></script>
    </body>
</html><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views\layouts\Jurassic_Store.blade.php ENDPATH**/ ?>