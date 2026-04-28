
<!-- MODAL DE REGISTRO -->
<div class="modal fade auth-modal"
     id="registerModal"
     tabindex="-1"
     aria-labelledby="registerModalLabel"
     aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            
            <div class="modal-header bg-warning">
                <h5 class="modal-title"
                    id="registerModalLabel"
                >Crear cuenta</h5>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Cerrar"
                ></button>
            </div>

            
            <form method="POST"
                  action="<?php echo e(route('register')); ?>"
            >
                <?php echo csrf_field(); ?>
                <div class="modal-body">

                    
                    <div class="mb-3">
                        <label for="register-name"
                               class="form-label"
                        >Nombre</label>
                        
                        <input id="register-name"
                               type="text"
                               name="name"
                               value="<?php echo e(old('name')); ?>"
                               class="form-control <?php $__errorArgs = ['name', 'register'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               required
                               autocomplete="name"
                        >
                        <?php $__errorArgs = ['name', 'register'];
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

                    
                    <div class="mb-3">
                        <label for="register-email"
                               class="form-label"
                        >Email</label>
                        <input id="register-email"
                               type="email"
                               name="email"
                               value="<?php echo e(old('email')); ?>"
                               class="form-control <?php $__errorArgs = ['email', 'register'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               required
                               autocomplete="email"
                        >
                        <?php $__errorArgs = ['email', 'register'];
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

                    
                    <div class="mb-3">
                        <label for="register-password"
                               class="form-label"
                        >Contraseña</label>
                        
                        <input id="register-password"
                               type="password"
                               name="password"
                               class="form-control <?php $__errorArgs = ['password', 'register'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               required
                               autocomplete="new-password"
                        >
                        <?php $__errorArgs = ['password', 'register'];
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

                    
                    <div class="mb-3">
                        <label for="register-password-confirmation"
                               class="form-label"
                        >Confirmar contraseña</label>
                        
                        <input id="register-password-confirmation"
                               type="password"
                               name="password_confirmation"
                               class="form-control <?php $__errorArgs = ['password_confirmation', 'register'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               required
                               autocomplete="new-password"
                        >
                        <?php $__errorArgs = ['password_confirmation', 'register'];
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

                
                <div class="modal-footer">
                    
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                    >Cerrar</button>
                    
                    <button type="submit"
                            class="btn btn-warning"
                    >Registrarse</button>
                </div>
            </form>
        </div>
    </div>
</div><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views\auth\partials\register-modal.blade.php ENDPATH**/ ?>