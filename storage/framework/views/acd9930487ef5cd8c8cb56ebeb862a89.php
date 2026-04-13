<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>JURASSIC STORE</title>
        <link rel="stylesheet" href="<?php echo e(asset('vendor/bootstrap/css/bootstrap.min.css')); ?>">
        <link rel="stylesheet" href="<?php echo e(asset('css/estilos.css')); ?>">
    </head>

    <body>
        <!-- HEADER COMUN PARA TODAS LAS VISTAS -->
        <header class="navbar navbar-expand-lg navbar-dark bg-black navbar-tall align-items-center">
            <div class="container-fluid">
                <div class="d-flex gap-3">
                    <img src="<?php echo e(asset('images/jp_logo.jpg')); ?>" alt="logo" width="60" height="40" class="d-inline-block align-text-top">
                    <a class="navbar-brand navbar-brand-custom" href="/Principal" style="font-size: 2rem;">Jurassic Store</a>
                </div>

                <ul class="nav justify-content-end">
                    <!-- favorites -->
                    <li class="nav-item">
                        <a class="nav-link scale-effect-icon" href="<?php echo e(route('favorites.index')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px">
                                <path d="m480-120-58-52q-101-91-167-157T150-447.5Q111-500 95.5-544T80-634q0-94 63-157t157-63q52 0 99 22t81 62q34-40 81-62t99-22q94 0 157 63t63 157q0 46-15.5 90T810-447.5Q771-395 705-329T538-172l-58 52Z" fill="white" class="heart-path"/>
                            </svg>
                        </a>
                    </li>
                    <!-- cart -->
                    <li class="nav-item">
                        <button class="nav-link scale-effect-icon btn btn-link" type="button" data-bs-toggle="offcanvas" data-bs-target="#carritoSidebar" aria-controls="carritoSidebar">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#ffffff">
                                <path d="M223.5-103.5Q200-127 200-160t23.5-56.5Q247-240 280-240t56.5 23.5Q360-193 360-160t-23.5 56.5Q313-80 280-80t-56.5-23.5Zm400 0Q600-127 600-160t23.5-56.5Q647-240 680-240t56.5 23.5Q760-193 760-160t-23.5 56.5Q713-80 680-80t-56.5-23.5ZM246-720l96 200h280l110-200H246Zm-38-80h590q23 0 35 20.5t1 41.5L692-482q-11 20-29.5 31T622-440H324l-44 80h480v80H280q-45 0-68-39.5t-2-78.5l54-98-144-304H40v-80h130l38 80Zm134 280h280-280Z"/>
                            </svg>
                        </button>
                    </li>

                    <!-- profile -->
                    <?php if(auth()->guard()->check()): ?>
                        <!-- USUARIO AUTENTICADO -->
                        <li class="nav-item" style="margin-left: 20px; padding: 8px 12px; background-color: #FFD700; border-radius: 6px; color: black; font-weight: bold;">
                            👤 <?php echo e(Auth::user()->name); ?>

                        </li>
                        <li class="nav-item">
                            <form method="POST" action="<?php echo e(route('logout')); ?>" style="display: inline;">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="nav-link btn btn-link" style="color: #FF6B6B; text-decoration: none; font-weight: bold;">
                                    🚪 Logout
                                </button>
                            </form>
                        </li>
                    <?php else: ?>
                        <!-- USUARIO NO AUTENTICADO -->
                        <li class="nav-item">
                            <a class="nav-link scale-effect-icon" href="/login">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#ffffff">
                                    <path d="M367-527q-47-47-47-113t47-113q47-47 113-47t113 47q47 47 47 113t-47 113q-47 47-113 47t-113-47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Zm80-80h480v-32q0-11-5.5-20T700-306q-54-27-109-40.5T480-360q-56 0-111 13.5T260-306q-9 5-14.5 14t-5.5 20v32Zm296.5-343.5Q560-607 560-640t-23.5-56.5Q513-720 480-720t-56.5 23.5Q400-673 400-640t23.5 56.5Q447-560 480-560t56.5-23.5ZM480-640Zm0 400Z"/>
                                </svg>
                                Login
                            </a>
                        </li>
                        <span class="nav-link">|</span>
                        <li class="nav-item">
                            <a class="nav-link scale-effect-icon" href="/register">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </header>

        <!-- NAVBAR COMUN PARA TODAS LAS VISTAS -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-warning navbar-short">
            <div class="container-fluid">
                <div class="d-flex gap-3 align-items-center">
                    <a class="nav-link" href="<?php echo e(route('principal')); ?>">Home</a>
                    
                    <div class="dropdown">
                        <button class="btn btn-warning dropdown-toggle dropdown-toggle-custom" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Categories
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo e(route('products.index')); ?>">All Products</a></li>
                            <li><a class="dropdown-item" href="<?php echo e(route('products.index')); ?>?category=action-figures">Action Figures</a></li>
                            <li><a class="dropdown-item" href="<?php echo e(route('products.index')); ?>?category=plush-toys">Plush Toys</a></li>
                            <li><a class="dropdown-item" href="<?php echo e(route('products.index')); ?>?category=apparel">Apparel</a></li>
                        </ul>
                    </div>

                    <a class="nav-link" href="<?php echo e(route('about')); ?>">About Us</a>
                    <a class="nav-link" href="<?php echo e(route('shipping')); ?>">Shipping</a>
                    <a class="nav-link" href="<?php echo e(route('contact')); ?>">Contact</a>
                    <a class="nav-link" href="<?php echo e(route('terms')); ?>">Terms</a>
                </div>
            </div>
        </nav>

        <!-- CONTENIDO ESPECÍFICO DE CADA VISTA -->
        <?php echo $__env->yieldContent('content'); ?>

        <!-- SIDEBAR DEL CARRITO: COMPARTIDO -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="carritoSidebar" aria-labelledby="carritoSidebarLabel">
            <div class="offcanvas-header bg-warning">
                <h5 class="offcanvas-title" id="carritoSidebarLabel">🛒 Shopping Cart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body d-flex flex-column">
                <div class="cart-items flex-grow-1">
                    <?php
                        $cart = session()->get('cart') ?? [];
                        $cartTotal = 0;
                    ?>

                    <?php if(is_array($cart) && count($cart) > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php $__currentLoopData = $cart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $productId => $quantity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $product = \App\Models\Product::find($productId);
                                    if ($product) {
                                        $subtotal = $product->price * $quantity;
                                        $cartTotal += $subtotal;
                                    }
                                ?>

                                <?php if($product): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1"><?php echo e($product->name); ?></h6>
                                                <small class="text-muted">Qty: <?php echo e($quantity); ?></small>
                                            </div>
                                            <span class="text-warning fw-bold">$<?php echo e(number_format($subtotal, 2)); ?></span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>

                        <hr class="my-3">

                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <strong>Subtotal:</strong>
                                <span>$<?php echo e(number_format($cartTotal, 2)); ?></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <strong>Tax (10%):</strong>
                                <span>$<?php echo e(number_format($cartTotal * 0.10, 2)); ?></span>
                            </div>
                            <div class="d-flex justify-content-between border-top pt-2 mt-2 fs-6 fw-bold">
                                <strong>Total:</strong>
                                <span class="text-warning">$<?php echo e(number_format($cartTotal * 1.10, 2)); ?></span>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center py-5">Your cart is empty</p>
                    <?php endif; ?>
                </div>

                <div class="mt-auto d-flex flex-column gap-2">
                    <?php if(is_array($cart) && count($cart) > 0): ?>
                        <a href="<?php echo e(route('cart.show')); ?>" class="btn btn-warning w-100">
                            💳 View Full Cart
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo e(route('products.index')); ?>" class="btn btn-secondary w-100" data-bs-dismiss="offcanvas">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>

        <!-- FOOTER COMUN PARA TODAS LAS VISTAS -->
        <footer class="bg-black text-white py-4">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-4">
                        <h5>About Us</h5>
                        <p>Jurassic Store brings prehistoric excitement to life with our exclusive dinosaur collection.</p>
                    </div>
                    <div class="col-md-4">
                        <h5>Links</h5>
                        <ul class="list-unstyled">
                            <li><a href="/about" class="text-white-50">About</a></li>
                            <li><a href="/contact" class="text-white-50">Contact</a></li>
                            <li><a href="/privacy" class="text-white-50">Privacy Policy</a></li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h5>Follow Us</h5>
                        <p class="text-white-50">Stay updated with our latest dinosaur discoveries!</p>
                    </div>
                </div>
                <hr>
                <div class="text-center text-white-50">
                    <p>&copy; 2026 Jurassic Store. All rights reserved.</p>
                </div>
            </div>
        </footer>

        <script src="<?php echo e(asset('vendor/bootstrap/js/bootstrap.bundle.min.js')); ?>"></script>
    </body>
</html><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views/layouts/Jurassic_Store.blade.php ENDPATH**/ ?>