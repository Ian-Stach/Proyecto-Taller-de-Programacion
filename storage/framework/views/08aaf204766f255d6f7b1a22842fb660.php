<?php $__env->startSection('content'); ?>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-7 col-xl-5">
                <div class="card shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <h1 class="h3 mb-3">Confirmar contraseña</h1>
                        <p class="text-muted mb-4">
                            Esta es una zona protegida. Confirma tu contraseña antes de continuar.
                        </p>

                        <form method="POST" action="<?php echo e(route('password.confirm')); ?>">
                            <?php echo csrf_field(); ?>

                            <div class="mb-4">
                                <label for="confirm-password" class="form-label">Contraseña</label>
                                <input id="confirm-password"
                                       class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       type="password"
                                       name="password"
                                       required
                                       autocomplete="current-password"
                                >
                                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-warning fw-bold">
                                    Confirmar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.Jurassic_Store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views\auth\confirm-password.blade.php ENDPATH**/ ?>