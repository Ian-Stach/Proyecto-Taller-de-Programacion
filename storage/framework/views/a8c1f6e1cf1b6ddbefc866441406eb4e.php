<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin – <?php echo $__env->yieldContent('title', 'Panel de administración'); ?></title>

        <link rel="stylesheet" href="<?php echo e(asset('vendor/bootstrap/css/bootstrap.min.css')); ?>">
        <link rel="stylesheet" href="<?php echo e(asset('css/estilos.css')); ?>?v=<?php echo e(filemtime(public_path('css/estilos.css'))); ?>">

    </head>
    <body>
        <div class="d-flex min-vh-100">

            
            <nav class="admin-sidebar d-flex flex-column">
                <div class="admin-brand">Admin Panel</div>

                <ul class="nav flex-column mt-3">
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>"
                           href="<?php echo e(route('admin.dashboard')); ?>"
                        >Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('admin.products*') ? 'active' : ''); ?>"
                           href="<?php echo e(route('admin.products')); ?>"
                        >Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('admin.users*') ? 'active' : ''); ?>"
                           href="<?php echo e(route('admin.users')); ?>"
                        >Usuarios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('admin.orders*') ? 'active' : ''); ?>"
                           href="<?php echo e(route('admin.orders')); ?>"
                        >Órdenes</a>
                    </li>
                    <LI class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('admin.metrics*') ? 'active' : ''); ?>"
                            href="<?php echo e(route('admin.metrics')); ?>"
                        >Métricas</a>
                    </LI>
                </ul>

                <div class="mt-auto p-3 border-top border-secondary">
                    <small class="d-block text-muted mb-2"><?php echo e(auth()->user()->name); ?></small>
                    <a class="nav-link ps-0" href="<?php echo e(route('home')); ?>">← Volver al sitio</a>
                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button class="btn btn-sm btn-outline-danger mt-1 w-100" type="submit">Cerrar sesión</button>
                    </form>
                </div>
            </nav>

            
            <main class="admin-main">
                <?php if(session('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show"
                         role="alert"
                    >
                        <?php echo e(session('success')); ?>

                        <button class="btn-close"
                                type="button"
                                data-bs-dismiss="alert"
                        ></button>
                    </div>
                <?php endif; ?>

                <?php echo $__env->yieldContent('content'); ?>
            </main>

        </div>

        <script src="<?php echo e(asset('vendor/bootstrap/js/bootstrap.bundle.min.js')); ?>"></script>
        <?php echo $__env->yieldPushContent('scripts'); ?>
    </body>
</html>
<?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views\admin\layout.blade.php ENDPATH**/ ?>