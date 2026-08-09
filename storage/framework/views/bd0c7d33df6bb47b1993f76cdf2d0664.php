

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">     <!-- Codificación de caracteres: garantiza que tildes y ñ se muestren bien -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">     <!-- Viewport responsivo: escala la página correctamente en móviles -->

        <title>JURASSIC STORE</title>

        <link rel="stylesheet" href="<?php echo e(asset('vendor/bootstrap/css/bootstrap.min.css')); ?>">     <!-- Bootstrap CSS, se carga antes de estilos.css para sobreescribirlo -->
        <link rel="stylesheet" href="<?php echo e(asset('css/estilos.css')); ?>?v=<?php echo e(filemtime(public_path('css/estilos.css'))); ?>">     <!-- Estilos propios del sitio -->
    </head>

    <body>
        
        <?php echo $__env->make('layouts.partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>       <!-- HEADER SUPERIOR -->
        <?php echo $__env->make('layouts.partials.main-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>     <!-- NAVBAR PRINCIPAL -->
        <?php echo $__env->yieldContent('content'); ?>                         <!-- CONTENIDO PROPIO DE CADA VISTA -->

        
        <?php if(auth()->guard()->check()): ?>
            
            <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
                <div id="cart-toast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body" id="cart-toast-body"></div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
                    </div>
                </div>
            </div>

            <?php echo $__env->make('layouts.partials.cart-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?>

        
        <?php echo $__env->make('layouts.partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <?php if(auth()->guard()->guest()): ?>
            <?php echo $__env->make('auth.partials.modals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?>

        
        <script src="<?php echo e(asset('js/header-search-suggest.js')); ?>" defer></script>
        <script src="<?php echo e(asset('vendor/bootstrap/js/bootstrap.bundle.min.js')); ?>" defer></script>
        <script src="<?php echo e(asset('js/cart.js')); ?>" defer></script>

        <?php echo $__env->yieldPushContent('scripts'); ?>
    </body>
</html><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views\layouts\Jurassic_Store.blade.php ENDPATH**/ ?>