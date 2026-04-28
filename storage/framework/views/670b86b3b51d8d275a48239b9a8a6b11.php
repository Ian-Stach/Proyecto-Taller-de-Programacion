
<!-- MODAL DE LOGIN -->
<div class="modal fade auth-modal"
     id="loginModal"
     tabindex="-1"
     aria-labelledby="loginModalLabel"
     aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            
            <div class="modal-header bg-warning">
                <h5 class="modal-title"
                    id="loginModalLabel"
                >Iniciar sesión</h5>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Cerrar"
                ></button>
            </div>

            
            <form method="POST"
                  action="<?php echo e(route('login')); ?>"
            >
                <?php echo csrf_field(); ?>
                <div class="modal-body">

                    
                    <?php if(session('loginStatus')): ?>
                        <div class="alert alert-success"><?php echo e(session('loginStatus')); ?></div>
                    <?php endif; ?>

                    
                    <div class="mb-3">
                        <label for="email"
                               class="form-label"
                        >Email</label>
                        
                        <input id="email"
                               type="email"
                               name="email"
                               value="<?php echo e(old('email')); ?>"
                               class="form-control <?php $__errorArgs = ['email', 'login'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               required
                               autofocus
                               autocomplete="username"
                        >
                        <?php $__errorArgs = ['email', 'login'];
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
                        <label for="password"
                               class="form-label"
                        >Contraseña</label>
                        
                        <input id="password"
                               type="password"
                               name="password"
                               class="form-control <?php $__errorArgs = ['password', 'login'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               required
                               autocomplete="current-password"
                        >
                        <?php $__errorArgs = ['password', 'login'];
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

                    
                    <div class="mb-3 form-check">
                        <input id="remember_me"
                               type="checkbox"
                               class="form-check-input"
                               name="remember"
                        >
                        <label for="remember_me"
                               class="form-check-label"
                        >Recordarme</label>
                    </div>

                    
                    <?php if(Route::has('password.request')): ?>
                        <button type="button"
                                class="btn btn-link link-dark p-0 text-decoration-none"
                                data-bs-toggle="modal"
                                data-bs-target="#forgotPasswordModal"
                        >¿Olvidaste tu contraseña?</button>
                    <?php endif; ?>
                </div>

                
                <div class="modal-footer">
                    
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                    >Cerrar</button>
                    
                    <button type="submit"
                            class="btn btn-warning"
                    >Entrar</button>
                </div>
            </form>
        </div>
    </div>
</div><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views\auth\partials\login-modal.blade.php ENDPATH**/ ?>