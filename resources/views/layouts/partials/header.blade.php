{{--
    PARTIAL: header
    ─────────────────────────────────────────────────────────────────────────────
    Barra negra superior presente en todas las páginas del sitio.
    Contiene tres zonas horizontales:
      1. Izquierda  → logo e imagen de marca, ambos con link a la home.
      2. Centro     → formulario de búsqueda con sugerencias en tiempo real.
      3. Derecha    → acciones de usuario (varía según si está logueado o no).

    Clases Bootstrap relevantes:
      navbar-dark   → adapta íconos y texto al fondo oscuro (texto blanco).
      bg-black      → fondo negro.
      header-custom   → altura personalizada definida en estilos.css.
      container-fluid → ocupa todo el ancho disponible sin márgenes laterales.
      ms-auto       → empuja la lista de acciones hacia la derecha (margin-start: auto).
      flex-shrink-0 → evita que el logo o las acciones se compriman al escalar.
    ─────────────────────────────────────────────────────────────────────────────
--}}

<header class="navbar navbar-dark bg-black align-items-center header-custom">
    <div class="container-fluid d-flex align-items-center gap-3">

        <!-- ── ZONA IZQUIERDA: logo e imagen de marca -->
        <div class="d-flex gap-3 flex-shrink-0">
            @if(Auth::check() && Auth::user()->is_admin)
                <a class="d-inline-flex align-items-center gap-1 text-decoration-none header-brand" href="{{ route('admin.dashboard') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#e3e3e3">
                        <path d="M380.5-480.5Q340-521 340-580t40.5-99.5Q421-720 480-720t99.5 40.5Q620-639 620-580t-40.5 99.5Q539-440 480-440t-99.5-40.5ZM523-537q17-17 17-43t-17-43q-17-17-43-17t-43 17q-17 17-17 43t17 43q17 17 43 17t43-17ZM480-80q-139-35-229.5-159.5T160-516v-244l320-120 320 120v244q0 152-90.5 276.5T480-80Zm0-400Zm0-315-240 90v189q0 54 15 105t41 96q42-21 88-33t96-12q50 0 96 12t88 33q26-45 41-96t15-105v-189l-240-90Zm-70 523q-34 8-65 22 29 30 63 52t72 34q38-12 72-34t63-52q-31-14-65-22t-70-8q-36 0-70 8Z"/>
                    </svg>
                    Jurassic Control
                </a>
            @else
                <!-- Logo de Jurassic Store con imagen estática. asset() genera la URL absoluta a public/images/js_logo_header.png. -->
                <img src="{{ asset('images/js_logo_header.png') }}" alt="logo" width="75" height="52" class="d-inline-block align-middle">

                <!-- Nombre de la tienda como enlace a /principal -->
                <a class="text-decoration-none header-brand" href="{{ route('principal') }}">Jurassic Store</a>
            @endif
        </div>

        <!-- ZONA CENTRAL: buscador con sugerencias en tiempo real

            El formulario usa GET hacia products.index para que el término de búsqueda quede visible en la URL (?search=...), lo cual permite compartir o recargar la URL manteniendo el filtro activo.

            Atributos data-* usados por header-search-suggest.js:
              data-header-search-form  → marca este form como el buscador del header; el script lo localiza por este selector.
              data-suggestions-url     → URL del endpoint JSON que devuelve sugerencias (route 'products.suggestions' → ProductController@suggestions).

            autocomplete="off" → desactiva el historial nativo del navegador para no interferir con el dropdown de sugerencias propio.
            value="{{ request('search') }}" → si el usuario ya buscó algo, el campo queda pre-relleno con el término actual. -->
        <form method="GET" action="{{ route('products.index') }}" class="header-search-form" data-header-search-form data-suggestions-url="{{ route('products.suggestions') }}" role="search"      >
            <div class="input-group header-search-group">
                <input class="form-control header-search-input" type="search" name="search" placeholder="Buscar productos..." value="{{ request('search') }}" aria-label="Buscar productos" autocomplete="off">
                <!-- Botón de envío con ícono SVG de lupa (Material Symbols) -->
                <button class="btn header-search-button" type="submit" aria-label="Buscar">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#000000">
                        <path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/>
                    </svg>
                </button>
            </div>
        </form>

        <!-- ── ZONA DERECHA: acciones de usuario ── -->
        <ul class="nav ms-auto align-items-center flex-shrink-0">

            <!-- ÍCONOS DE ACCIÓN (solo usuarios autenticados)
                Se ocultan para guests porque requieren sesión activa:
                  - Órdenes:  historial de compras del usuario.
                  - Favoritos: productos guardados en la tabla favorites.
                  - Carrito:  abre el offcanvas #carritoSidebar que solo se renderiza para usuarios autenticados (ver cart-sidebar.blade.php en el layout). -->
            @auth
                <!-- Botón de carrito → abre el offcanvas lateral.
                    data-bs-toggle="offcanvas" + data-bs-target="#carritoSidebar" son atributos de Bootstrap que activan el panel sin JavaScript propio. -->
                <li class="nav-item">
                    <button class="nav-link scale-effect-icon btn btn-link header-utility-icon" type="button" data-bs-toggle="offcanvas" data-bs-target="#carritoSidebar" aria-controls="carritoSidebar">
                        <!-- SVG de carrito de compras (Material Symbols) -->
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#ffffff">
                            <path d="M223.5-103.5Q200-127 200-160t23.5-56.5Q247-240 280-240t56.5 23.5Q360-193 360-160t-23.5 56.5Q313-80 280-80t-56.5-23.5Zm400 0Q600-127 600-160t23.5-56.5Q647-240 680-240t56.5 23.5Q760-193 760-160t-23.5 56.5Q713-80 680-80t-56.5-23.5ZM246-720l96 200h280l110-200H246Zm-38-80h590q23 0 35 20.5t1 41.5L692-482q-11 20-29.5 31T622-440H324l-44 80h480v80H280q-45 0-68-39.5t-2-78.5l54-98-144-304H40v-80h130l38 80Zm134 280h280-280Z"/>
                        </svg>
                    </button>
                </li>

                <!--
                    USUARIO AUTENTICADO: muestra nombre, email y avatar con inicial.
                    Auth::user() devuelve el modelo User de la sesión activa.
                    strtoupper(substr(..., 0, 1)) toma la primera letra del nombre y la convierte a mayúscula para usar como avatar de texto.
                    El enlace lleva a la página de perfil/cuenta del usuario. -->
                <li class="nav-item dropdown">
                    <a class="nav-link text-decoration-none dropdown-toggle user-summary-link" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="p-0 user-summary">
                            @if(Auth::user()->photo)
                                <img src="{{ asset('storage/' . Auth::user()->photo) }}" class="user-summary-photo" alt="Foto de perfil">
                            @else
                                <div class="user-summary-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                            @endif
                        </span>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end summary-dropdown-menu" aria-labelledby="userDropdown">
                        <li class="summary-dropdown-header px-3 py-2">
                            <a class="dropdown-item" href="{{ route('user') }}">
                                <div class="d-flex align-items-center">
                                    @if(Auth::user()->photo)
                                        <img src="{{ asset('storage/' . Auth::user()->photo) }}" class="summary-dropdown-photo" alt="Foto de perfil">
                                    @else
                                        <div class="summary-dropdown-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                                    @endif
                                    <div class="ms-2">
                                        <div class="fw-semibold">{{ Auth::user()->name }}</div>
                                        <div class="text-muted small">{{ Auth::user()->email }}</div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="d-flex align-items-center gap-1 dropdown-item" href="{{ route('user') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                    <path d="M367-527q-47-47-47-113t47-113q47-47 113-47t113 47q47 47 47 113t-47 113q-47 47-113 47t-113-47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Zm80-80h480v-32q0-11-5.5-20T700-306q-54-27-109-40.5T480-360q-56 0-111 13.5T260-306q-9 5-14.5 14t-5.5 20v32Zm296.5-343.5Q560-607 560-640t-23.5-56.5Q513-720 480-720t-56.5 23.5Q400-673 400-640t23.5 56.5Q447-560 480-560t56.5-23.5ZM480-640Zm0 400Z"/>
                                </svg>
                                Mi cuenta
                            </a>
                        </li>
                        <li>
                            <a class="d-flex align-items-center gap-1 dropdown-item" href="{{ route('orders.index') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                    <path d="M160-160v-516L82-846l72-34 94 202h464l94-202 72 34-78 170v516H160Zm240-280h160q17 0 28.5-11.5T600-480q0-17-11.5-28.5T560-520H400q-17 0-28.5 11.5T360-480q0 17 11.5 28.5T400-440ZM240-240h480v-358H240v358Zm0 0v-358 358Z"/>
                                </svg>
                                Mis órdenes
                            </a>
                        </li>
                        <li>
                            <a class="d-flex align-items-center gap-1 dropdown-item" href="{{ route('favorites.index') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" height="22px" viewBox="0 -960 960 960" width="22px" fill="#f1f1f1">
                                    <path d="m480-120-58-52q-101-91-167-157T150-447.5Q111-500 95.5-544T80-634q0-94 63-157t157-63q52 0 99 22t81 62q34-40 81-62t99-22q94 0 157 63t63 157q0 46-15.5 90T810-447.5Q771-395 705-329T538-172l-58 52Zm0-108q96-86 158-147.5t98-107q36-45.5 50-81t14-70.5q0-60-40-100t-100-40q-47 0-87 26.5T518-680h-76q-15-41-55-67.5T300-774q-60 0-100 40t-40 100q0 35 14 70.5t50 81q36 45.5 98 107T480-228Zm0-273Z"/>
                                </svg>
                                Mis favoritos
                            </a>
                        </li>
                        @if(Auth::user()->is_admin)
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="d-flex align-items-center gap-1 dropdown-item" href="{{ route('admin.dashboard') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="22px" viewBox="0 -960 960 960" width="22px" fill="#e3e3e3">
                                        <path d="M380.5-480.5Q340-521 340-580t40.5-99.5Q421-720 480-720t99.5 40.5Q620-639 620-580t-40.5 99.5Q539-440 480-440t-99.5-40.5ZM523-537q17-17 17-43t-17-43q-17-17-43-17t-43 17q-17 17-17 43t17 43q17 17 43 17t43-17ZM480-80q-139-35-229.5-159.5T160-516v-244l320-120 320 120v244q0 152-90.5 276.5T480-80Zm0-400Zm0-315-240 90v189q0 54 15 105t41 96q42-21 88-33t96-12q50 0 96 12t88 33q26-45 41-96t15-105v-189l-240-90Zm-70 523q-34 8-65 22 29 30 63 52t72 34q38-12 72-34t63-52q-31-14-65-22t-70-8q-36 0-70 8Z"/>
                                    </svg>
                                    Panel de administración
                                </a>
                            </li>
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item text-danger" type="submit">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="22px" viewBox="0 -960 960 960" width="22px" fill="#dc3545">
                                        <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z"/>
                                    </svg>
                                    Cerrar sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            @else
                <!-- USUARIO NO AUTENTICADO: botones que abren los modales de auth.
                    data-bs-toggle="modal" + data-bs-target="#loginModal" le dicen a Bootstrap qué modal abrir sin necesitar JavaScript propio.
                    Los modales (#loginModal, #registerModal) están definidos en resources/views/auth/partials/ e incluidos al final del layout. -->
                <li class="nav-item">
                    <button class="nav-link scale-effect-icon border-0 bg-transparent" type="button" data-bs-toggle="modal" data-bs-target="#loginModal">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#ffffff">     <!-- SVG de ícono de persona/usuario (Material Symbols) -->
                            <path d="M367-527q-47-47-47-113t47-113q47-47 113-47t113 47q47 47 47 113t-47 113q-47 47-113 47t-113-47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Zm80-80h480v-32q0-11-5.5-20T700-306q-54-27-109-40.5T480-360q-56 0-111 13.5T260-306q-9 5-14.5 14t-5.5 20v32Zm296.5-343.5Q560-607 560-640t-23.5-56.5Q513-720 480-720t-56.5 23.5Q400-673 400-640t23.5 56.5Q447-560 480-560t56.5-23.5ZM480-640Zm0 400Z"/>
                        </svg>
                        Iniciar sesión
                    </button>
                </li>

                <!-- Separador visual entre "Iniciar sesión" y "Registrarse" -->
                <span class="nav-link">|</span>

                <li class="nav-item">
                    <button class="nav-link scale-effect-icon border-0 bg-transparent" type="button" data-bs-toggle="modal" data-bs-target="#registerModal">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#ffffff">
                            <path d="M720-400v-120H600v-80h120v-120h80v120h120v80H800v120h-80ZM247-527q-47-47-47-113t47-113q47-47 113-47t113 47q47 47 47 113t-47 113q-47 47-113 47t-113-47ZM40-160v-112q0-34 17.5-62.5T104-378q62-31 126-46.5T360-440q66 0 130 15.5T616-378q29 15 46.5 43.5T680-272v112H40Zm80-80h480v-32q0-11-5.5-20T580-306q-54-27-109-40.5T360-360q-56 0-111 13.5T140-306q-9 5-14.5 14t-5.5 20v32Zm296.5-343.5Q440-607 440-640t-23.5-56.5Q393-720 360-720t-56.5 23.5Q280-673 280-640t23.5 56.5Q327-560 360-560t56.5-23.5ZM360-640Zm0 400Z"/>
                        </svg>
                        Registrarse
                    </button>
                </li>
            @endauth
        </ul>
    </div>
</header>