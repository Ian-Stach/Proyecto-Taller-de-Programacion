
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
        
        <header class="navbar navbar-expand-lg navbar-dark bg-black header-tall user-account-header">
            <div class="container-fluid user-account-header-bar">
                <div class="user-account-brand">
                    
                    <button class="navbar-toggler d-md-none account-menu-toggle"
                            type="button"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#accountSidebarMobile"
                            aria-controls="accountSidebarMobile"
                            aria-label="Abrir menú de cuenta"
                    >
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <a href="<?php echo e(route('home')); ?>"
                       class="d-inline-flex align-items-center"
                    >
                        <img src="<?php echo e(asset('images/js_logo_header.png')); ?>"
                             alt="logo"
                             width="60"
                             height="40"
                             class="d-inline-block align-text-top"
                        >
                    </a>
                    
                    <a class="navbar-brand navbar-brand-custom navbar-brand-large mb-0 d-none d-md-inline-block"
                       href="<?php echo e(route('home')); ?>"
                    >Jurassic Store</a>
                </div>

                
                <div class="user-account-summary d-none d-md-flex">
                    <div class="user-account-meta">
                        <div class="user-account-name"><?php echo e(Auth::user()->name); ?></div>
                        <div class="user-account-email"><?php echo e(Auth::user()->email); ?></div>
                    </div>
                    
                    <div class="user-account-avatar">
                        <?php echo e(strtoupper(substr(Auth::user()->name, 0, 1))); ?>

                    </div>
                </div>

                
                <a href="<?php echo e(route('user')); ?>"
                   class="d-flex d-md-none user-account-mobile-avatar-link"
                   aria-label="Mi cuenta"
                >
                    <div class="user-account-avatar">
                        <?php echo e(strtoupper(substr(Auth::user()->name, 0, 1))); ?>

                    </div>
                </a>
            </div>
        </header>

        
        <?php echo $__env->make('layouts.partials.main-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <?php echo $__env->yieldContent('content'); ?>

        
        <?php echo $__env->make('layouts.partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <script src="<?php echo e(asset('vendor/bootstrap/js/bootstrap.bundle.min.js')); ?>"></script>

        
        <?php echo $__env->yieldPushContent('scripts'); ?>
    </body>
</html>
<?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views/layouts/account.blade.php ENDPATH**/ ?>