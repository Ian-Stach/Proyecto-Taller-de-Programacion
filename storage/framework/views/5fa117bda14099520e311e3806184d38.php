
<!-- MODAL DE RECUPERACION DE CONTRASENA -->
<div class="modal fade auth-modal"
     id="forgotPasswordModal"
     tabindex="-1"
     aria-labelledby="forgotPasswordModalLabel"
     aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            
            <div class="modal-header bg-warning">
                <h5 class="modal-title"
                    id="forgotPasswordModalLabel"
                >Recuperar contraseña</h5>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Cerrar"
                ></button>
            </div>

            
            <form method="POST"
                  action="<?php echo e(route('password.email')); ?>"
            >
                <?php echo csrf_field(); ?>
                <div class="modal-body">

                    
                    <p class="text-muted mb-3">
                        Ingresa tu email y te enviaremos un enlace para restablecer tu contraseña.
                    </p>

                    
                    <?php if(session('forgotPasswordStatus')): ?>
                        <div class="alert alert-success"><?php echo e(session('forgotPasswordStatus')); ?></div>
                    <?php endif; ?>

                    
                    <div class="mb-3">
                        <label for="forgot-password-email"
                               class="form-label"
                        >Email</label>
                        
                        <input id="forgot-password-email"
                               type="email"
                               name="email"
                               value="<?php echo e(old('email')); ?>"
                               class="form-control <?php $__errorArgs = ['email', 'forgotPassword'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               required
                               autocomplete="username"
                        >
                        <?php $__errorArgs = ['email', 'forgotPassword'];
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
                </div>

                
                <div class="modal-footer justify-content-between">
                    
                    <button type="button"
                            class="btn btn-link link-dark p-0 text-decoration-none"
                            data-bs-toggle="modal"
                            data-bs-target="#loginModal"
                    >Volver al login</button>
                    <div class="d-flex gap-2">
                        
                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal"
                        >Cerrar</button>
                        
                        <button type="submit"
                                class="btn btn-warning"
                        >Enviar enlace</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views/auth/partials/forgot-password-modal.blade.php ENDPATH**/ ?>