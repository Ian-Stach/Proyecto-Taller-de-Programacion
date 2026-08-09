

<?php $__env->startSection('content'); ?>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-7 col-xl-5">
                <div class="card shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <h1 class="h3 mb-3">Verifica tu correo</h1>
                        <p class="text-muted mb-4">
                            Antes de continuar, revisa tu bandeja de entrada y haz clic en el enlace de verificación que te enviamos al registrarte.
                            Si no lo encuentras, también revisa la carpeta de spam.
                        </p>

                        <?php if(session('status') === 'verification-link-sent'): ?>
                            <div class="alert alert-success mb-4" role="alert">
                                Te enviamos un nuevo enlace de verificación a tu correo.
                            </div>
                        <?php endif; ?>

                        <div class="d-flex flex-column flex-sm-row gap-3">
                            <form method="POST" action="<?php echo e(route('verification.send')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-warning fw-bold">
                                    Reenviar correo de verificación
                                </button>
                            </form>

                            <form method="POST" action="<?php echo e(route('logout')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-outline-secondary">
                                    Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.Jurassic_Store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views\auth\verify-email.blade.php ENDPATH**/ ?>