<!DOCKTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name"viewport" content="width=device-width, initial-scale=1.0">
        <title>JURASSIC STORE</title>
        <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
    </head>

    <body>
        <!-- HEADER COMUN PARA TODAS LAS VISTAS -->
        <header class="navbar navbar-expand-lg navbar-dark bg-black navbar-tall align-items-center">
            <div class="container-fluid">
                <div class="d-flex gap-3">
                    <img src="{{ asset('images/jp_logo.jpg') }}" alt="logo" width="60" height="40" class="d-inline-block align-text-top">
                    <a class="navbar-brand navbar-brand-custom" href="/Principal" style="font-size: 2rem;">Jurassic Store</a>
                </div>

                <ul class="nav justify-content-end">
                    <li class="nav-item">
                        <a class="nav-link scale-effect-icon" href="/Favorites">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px">
                                <path d="m480-120-58-52q-101-91-167-157T150-447.5Q111-500 95.5-544T80-634q0-94 63-157t157-63q52 0 99 22t81 62q34-40 81-62t99-22q94 0 157 63t63 157q0 46-15.5 90T810-447.5Q771-395 705-329T538-172l-58 52Z" fill="white" class="heart-path"/>
                            </svg>
                        </a>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link scale-effect-icon btn btn-link" type="button" data-bs-toggle="offcanvas" data-bs-target="#carritoSidebar" aria-controls="carritoSidebar">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#ffffff">
                                <path d="M223.5-103.5Q200-127 200-160t23.5-56.5Q247-240 280-240t56.5 23.5Q360-193 360-160t-23.5 56.5Q313-80 280-80t-56.5-23.5Zm400 0Q600-127 600-160t23.5-56.5Q647-240 680-240t56.5 23.5Q760-193 760-160t-23.5 56.5Q713-80 680-80t-56.5-23.5ZM246-720l96 200h280l110-200H246Zm-38-80h590q23 0 35 20.5t1 41.5L692-482q-11 20-29.5 31T622-440H324l-44 80h480v80H280q-45 0-68-39.5t-2-78.5l54-98-144-304H40v-80h130l38 80Zm134 280h280-280Z"/>
                            </svg>
                        </button>
                    </li>
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
                </ul>
            </div>
        </header>

        <!-- NAVBAR COMUN PARA TODAS LAS VISTAS -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-warning navbar-short">
            <div class="container-fluid">
                <div class="d-flex gap-3 align-items-center">
                    <a class="nav-link" href="/Principal">Home</a>
                    <div class="dropdown">
                        <button class="btn btn-warning dropdown-toggle dropdown-toggle-custom" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Categories
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/products">All Products</a></li>
                            <li><a class="dropdown-item" href="/products?category=action-figures">Action Figures</a></li>
                            <li><a class="dropdown-item" href="/products?category=plush-toys">Plush Toys</a></li>
                            <li><a class="dropdown-item" href="/products?category=apparel">Apparel</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- CONTENIDO ESPECÍFICO DE CADA VISTA -->
        @yield('content')

        <!-- SIDEBAR DEL CARRITO: COMPARTIDO -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="carritoSidebar" aria-labelledby="carritoSidebarLabel">
            <div class="offcanvas-header bg-warning">
                <h5 class="offcanvas-title" id="carritoSidebarLabel">Shopping Cart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="cart-items">
                    <!-- Los items del carrito irán aquí -->
                    <p class="text-muted">Your cart is empty</p>
                </div>
                <div class="mt-auto">
                    <button class="btn btn-warning w-100 mb-2">Checkout</button>
                    <button class="btn btn-secondary w-100" data-bs-dismiss="offcanvas">Continue Shopping</button>
                </div>
            </div>
        </div>

        <!-- FOOTER COMUN PARA TODAS LAS VISTAS -->
        <footer class="bg-black text-white mt-5 py-4">
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

        <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    </body>
</html>