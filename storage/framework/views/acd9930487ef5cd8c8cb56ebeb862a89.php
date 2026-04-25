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
        <!-- HEADER COMUN PARA TODAS LAS VISTAS -->
        <header class="navbar navbar-expand-lg navbar-dark bg-black header-tall align-items-center">
            <div class="container-fluid">
                <div class="d-flex gap-3">
                    <img src="<?php echo e(asset('images/jp_logo.jpg')); ?>"
                         alt="logo"
                         width="60"
                         height="40"
                         class="d-inline-block align-text-top"
                    >
                    <a class="navbar-brand navbar-brand-custom navbar-brand-large"
                       href="<?php echo e(route('home')); ?>"
                    >Jurassic Store</a>
                </div>

                <ul class="nav justify-content-end">
                    <?php if(auth()->guard()->check()): ?>
                        <!-- favorites -->
                        <li class="nav-item">
                            <a class="nav-link scale-effect-icon"
                               href="<?php echo e(route('favorites.index')); ?>"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     height="24px"
                                     viewBox="0 -960 960 960"
                                     width="24px"
                                >
                                    <path d="m480-120-58-52q-101-91-167-157T150-447.5Q111-500 95.5-544T80-634q0-94 63-157t157-63q52 0 99 22t81 62q34-40 81-62t99-22q94 0 157 63t63 157q0 46-15.5 90T810-447.5Q771-395 705-329T538-172l-58 52Z"
                                          fill="white" class="heart-path"
                                    />
                                </svg>
                            </a>
                        </li>
                    
                        <!-- cart -->
                        <li class="nav-item">
                            <button class="nav-link scale-effect-icon btn btn-link"
                                    type="button"
                                    data-bs-toggle="offcanvas"
                                    data-bs-target="#carritoSidebar"
                                    aria-controls="carritoSidebar"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     height="24px"
                                     viewBox="0 -960 960 960"
                                     width="24px"
                                     fill="#ffffff"
                                >
                                    <path d="M223.5-103.5Q200-127 200-160t23.5-56.5Q247-240 280-240t56.5 23.5Q360-193 360-160t-23.5 56.5Q313-80 280-80t-56.5-23.5Zm400 0Q600-127 600-160t23.5-56.5Q647-240 680-240t56.5 23.5Q760-193 760-160t-23.5 56.5Q713-80 680-80t-56.5-23.5ZM246-720l96 200h280l110-200H246Zm-38-80h590q23 0 35 20.5t1 41.5L692-482q-11 20-29.5 31T622-440H324l-44 80h480v80H280q-45 0-68-39.5t-2-78.5l54-98-144-304H40v-80h130l38 80Zm134 280h280-280Z"/>
                                </svg>
                            </button>
                        </li>
                    <?php endif; ?>

                    <!-- profile -->
                    <?php if(auth()->guard()->check()): ?>
                        <!-- USUARIO AUTENTICADO -->
                        <li class="nav-item user-badge">
                            👤 <?php echo e(Auth::user()->name); ?>

                        </li>
                        <li class="nav-item">
                            <form method="POST"
                                  action="<?php echo e(route('logout')); ?>"
                                  class="d-inline"
                            >
                                <?php echo csrf_field(); ?>
                                <button type="submit"
                                        class="nav-link btn btn-link logout-button"
                                >
                                    🚪 Cerrar sesión
                                </button>
                            </form>
                        </li>
                    <?php else: ?>
                        <!-- USUARIO NO AUTENTICADO -->
                        <li class="nav-item">
                            <button class="nav-link scale-effect-icon border-0 bg-transparent"
                                    type="button"
                                    data-bs-toggle="modal"
                                    data-bs-target="#loginModal"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     height="24px"
                                     viewBox="0 -960 960 960"
                                     width="24px"
                                     fill="#ffffff"
                                >
                                    <path d="M367-527q-47-47-47-113t47-113q47-47 113-47t113 47q47 47 47 113t-47 113q-47 47-113 47t-113-47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Zm80-80h480v-32q0-11-5.5-20T700-306q-54-27-109-40.5T480-360q-56 0-111 13.5T260-306q-9 5-14.5 14t-5.5 20v32Zm296.5-343.5Q560-607 560-640t-23.5-56.5Q513-720 480-720t-56.5 23.5Q400-673 400-640t23.5 56.5Q447-560 480-560t56.5-23.5ZM480-640Zm0 400Z"/>
                                </svg>
                                Iniciar sesión
                            </button>
                        </li>
                        <span class="nav-link">|</span>
                        <li class="nav-item">
                            <a class="nav-link scale-effect-icon" href="<?php echo e(route('register')); ?>">Registrarse</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </header>

        <!-- NAVBAR COMUN PARA TODAS LAS VISTAS -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-warning navbar-short">
            <div class="container-fluid">
                <div class="d-flex gap-3 align-items-center justify-content-center justify-content-evenly w-100">
                    <a class="nav-link" href="<?php echo e(route('home')); ?>">Inicio</a>
                    
                    <a class="nav-link" href="<?php echo e(route('products.index')); ?>">Productos</a>
                    <a class="nav-link" href="<?php echo e(route('about')); ?>">Sobre nosotros</a>
                    <a class="nav-link" href="<?php echo e(route('shipping')); ?>">Envío</a>
                    <a class="nav-link" href="<?php echo e(route('contact')); ?>">Contacto</a>
                    <a class="nav-link" href="<?php echo e(route('terms')); ?>">Términos</a>
                </div>
            </div>
        </nav>

        <!-- CONTENIDO ESPECÍFICO DE CADA VISTA -->
        <?php echo $__env->yieldContent('content'); ?>

        <?php if(auth()->guard()->check()): ?>
            <!-- SIDEBAR DEL CARRITO: COMPARTIDO -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="carritoSidebar" aria-labelledby="carritoSidebarLabel">
                <div class="offcanvas-header bg-warning">
                    <h5 class="offcanvas-title" id="carritoSidebarLabel">🛒 Carrito de compras</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar carrito"></button>
                </div>
                <div class="offcanvas-body d-flex flex-column">
                    <div class="cart-items flex-grow-1">
                        <?php if(count($sidebarCartItems) > 0): ?>
                            <div class="list-group list-group-flush">
                                <?php $__currentLoopData = $sidebarCartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1"><?php echo e($item['product']->name); ?></h6>
                                                <small class="text-muted">Cant.: <?php echo e($item['quantity']); ?></small>
                                            </div>
                                            <span class="text-warning fw-bold">$<?php echo e(number_format($item['subtotal'], 2)); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>

                            <hr class="my-3">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <strong>Subtotal:</strong>
                                    <span>$<?php echo e(number_format($sidebarCartSubtotal, 2)); ?></span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <strong>Impuesto (10%):</strong>
                                    <span>$<?php echo e(number_format($sidebarCartSubtotal * 0.1, 2)); ?></span>
                                </div>
                                <div class="d-flex justify-content-between border-top pt-2 mt-2 fs-6 fw-bold">
                                    <strong>Total:</strong>
                                    <span class="text-warning">$<?php echo e(number_format($sidebarCartSubtotal * 1.1, 2)); ?></span>
                                </div>
                            </div>
                            <?php else: ?>
                                <p class="text-muted text-center py-5">Tu carrito está vacío</p>
                        <?php endif; ?>
                    </div>

                    <div class="mt-auto d-flex flex-column gap-2">
                        <?php if(count($sidebarCartItems) > 0): ?>
                            <a href="<?php echo e(route('cart.show')); ?>" class="btn btn-warning w-100">
                                Ver carrito
                            </a>
                        <?php endif; ?>
                        <a href="<?php echo e(route('products.index')); ?>" class="btn btn-secondary w-100" data-bs-dismiss="offcanvas">
                            Seguir comprando
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- FOOTER COMUN PARA TODAS LAS VISTAS -->
        <footer class="bg-black text-white py-4">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-4">
                        <h5>Sobre nosotros</h5>
                        <p>Jurassic Store trae la emoción prehistórica a la vida con nuestra exclusiva colección de dinosaurios.</p>
                    </div>
                    <div class="col-md-4">
                        <h5>Enlaces</h5>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo e(route('about')); ?>" class="text-white-50">Sobre nosotros</a></li>
                            <li><a href="<?php echo e(route('contact')); ?>" class="text-white-50">Contacto</a></li>
                            <li><a class="text-white-50">Política de privacidad</a></li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h5>Síguenos</h5>
                        <p class="text-white-50">¡Mantente actualizado con nuestros últimos descubrimientos de dinosaurios!</p>
                    </div>
                </div>
                <hr>
                <div class="text-center text-white-50">
                    <p>&copy; 2026 Jurassic Store. Todos los derechos reservados.</p>
                </div>
            </div>
        </footer>

        <?php if(auth()->guard()->guest()): ?>
            <div class="modal fade"
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
                            >Iniciar sesión
                            </h5>
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
                                <?php if(session('status')): ?>
                                    <div class="alert alert-success"><?php echo e(session('status')); ?></div>
                                <?php endif; ?>

                                <div class="mb-3">
                                    <label for="email"
                                           class="form-label"
                                    >Email
                                    </label>
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
                                    >Contraseña
                                    </label>
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
                                    >Recordarme
                                    </label>
                                </div>

                                <?php if(Route::has('password.request')): ?>
                                    <a class="link-dark"
                                       href="<?php echo e(route('password.request')); ?>"
                                    >¿Olvidaste tu contraseña?
                                    </a>
                                <?php endif; ?>
                            </div>

                            <div class="modal-footer">
                                <button type="button"
                                        class="btn btn-secondary"
                                        data-bs-dismiss="modal"
                                >Cerrar
                                </button>
                                <button type="submit"
                                        class="btn btn-warning"
                                >Entrar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if(auth()->guard()->guest()): ?>
            <?php if($errors->login->isNotEmpty()): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const loginModalElement = document.getElementById('loginModal');

                        if (loginModalElement) {
                            const loginModal = new bootstrap.Modal(loginModalElement);
                            loginModal.show();
                        }
                    });
                </script>
            <?php endif; ?>
        <?php endif; ?>

        <script src="<?php echo e(asset('vendor/bootstrap/js/bootstrap.bundle.min.js')); ?>"></script>
    </body>
</html><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views/layouts/Jurassic_Store.blade.php ENDPATH**/ ?>