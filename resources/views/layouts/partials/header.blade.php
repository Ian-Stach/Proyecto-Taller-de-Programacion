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
      header-tall   → altura personalizada definida en estilos.css.
      container-fluid → ocupa todo el ancho disponible sin márgenes laterales.
      ms-auto       → empuja la lista de acciones hacia la derecha (margin-start: auto).
      flex-shrink-0 → evita que el logo o las acciones se compriman al escalar.
    ─────────────────────────────────────────────────────────────────────────────
--}}
<header class="navbar navbar-dark bg-black header-tall align-items-center">
    <div class="container-fluid d-flex align-items-center gap-3">

        {{-- ── ZONA IZQUIERDA: logo e imagen de marca ── --}}
        <div class="d-flex gap-3 flex-shrink-0">
            {{--
                Logo de Jurassic Store como imagen estática.
                asset() genera la URL absoluta a public/images/js_logo_header.png.
                width/height explícitos evitan el layout shift (CLS) al cargar la página.
            --}}
            <img src="{{ asset('images/js_logo_header.png') }}"
                 alt="logo"
                 width="75"
                 height="52"
                 class="d-inline-block align-middle"
            >

            {{--
                Nombre de la tienda como enlace a la home.
                route('home') genera la URL de la ruta nombrada 'home' en routes/web.php.
                navbar-brand-large y navbar-brand-custom son clases de estilos.css
                que amplían el tamaño de fuente respecto al brand de Bootstrap.
            --}}
            <a class="navbar-brand navbar-brand-custom navbar-brand-large"
               href="{{ route('home') }}"
            >Jurassic Store</a>
        </div>

        {{--
            ── ZONA CENTRAL: buscador con sugerencias en tiempo real ──

            El formulario usa GET hacia products.index para que el término de
            búsqueda quede visible en la URL (?search=...), lo cual permite
            compartir o recargar la URL manteniendo el filtro activo.

            Atributos data-* usados por header-search-suggest.js:
              data-header-search-form  → marca este form como el buscador del header;
                                         el script lo localiza por este selector.
              data-suggestions-url     → URL del endpoint JSON que devuelve sugerencias
                                         (route 'products.suggestions' → ProductController@suggestions).

            autocomplete="off" → desactiva el historial nativo del navegador para
                                  no interferir con el dropdown de sugerencias propio.
            value="{{ request('search') }}" → si el usuario ya buscó algo, el campo
                                              queda pre-relleno con el término actual.
        --}}
        <form method="GET"
              action="{{ route('products.index') }}"
              class="header-search-form"
              data-header-search-form
              data-suggestions-url="{{ route('products.suggestions') }}"
              role="search"
        >
            <div class="input-group header-search-group">
                <input class="form-control header-search-input"
                       type="search"
                       name="search"
                       placeholder="Buscar productos..."
                       value="{{ request('search') }}"
                       aria-label="Buscar productos"
                       autocomplete="off"
                >
                {{-- Botón de envío con ícono SVG de lupa (Material Symbols) --}}
                <button class="btn header-search-button"
                        type="submit"
                        aria-label="Buscar"
                >
                    <svg xmlns="http://www.w3.org/2000/svg"
                         height="20px"
                         viewBox="0 -960 960 960"
                         width="20px"
                         fill="#000000"
                    >
                        <path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/>
                    </svg>
                </button>
            </div>
        </form>

        {{-- ── ZONA DERECHA: acciones de usuario ── --}}
        <ul class="nav ms-auto align-items-center flex-shrink-0">

            {{--
                ÍCONOS DE ACCIÓN (solo usuarios autenticados)
                Se ocultan para guests porque requieren sesión activa:
                  - Órdenes:  historial de compras del usuario.
                  - Favoritos: productos guardados en la tabla favorites.
                  - Carrito:  abre el offcanvas #carritoSidebar que solo se renderiza
                    para usuarios autenticados (ver cart-sidebar.blade.php en el layout).
            --}}
            @auth
                {{-- Ícono de bolsa → historial de órdenes del usuario --}}
                <li class="nav-item">
                    <a class="nav-link scale-effect-icon"
                       href="{{ route('orders.index') }}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg"
                             height="24px" viewBox="0 -960 960 960"
                             width="24px" fill="#e3e3e3"
                        >
                            <path d="M160-160v-516L82-846l72-34 94 202h464l94-202 72 34-78 170v516H160Zm240-280h160q17 0 28.5-11.5T600-480q0-17-11.5-28.5T560-520H400q-17 0-28.5 11.5T360-480q0 17 11.5 28.5T400-440ZM240-240h480v-358H240v358Zm0 0v-358 358Z"/>
                        </svg>
                    </a>
                </li>


                {{-- Ícono de corazón → página de favoritos del usuario --}}
                <li class="nav-item">
                    <a class="nav-link scale-effect-icon header-utility-icon"
                       href="{{ route('favorites.index') }}"
                    >
                        {{-- SVG de corazón relleno (Material Symbols) --}}
                        <svg xmlns="http://www.w3.org/2000/svg"
                             height="24px"
                             viewBox="0 -960 960 960"
                             width="24px"
                        >
                            <path d="m480-120-58-52q-101-91-167-157T150-447.5Q111-500 95.5-544T80-634q0-94 63-157t157-63q52 0 99 22t81 62q34-40 81-62t99-22q94 0 157 63t63 157q0 46-15.5 90T810-447.5Q771-395 705-329T538-172l-58 52Z"
                                  fill="white"
                                  class="heart-path"
                            />
                        </svg>
                    </a>
                </li>

                {{--
                    Botón de carrito → abre el offcanvas lateral.
                    data-bs-toggle="offcanvas" + data-bs-target="#carritoSidebar"
                    son atributos de Bootstrap que activan el panel sin JavaScript propio.
                --}}
                <li class="nav-item">
                    <button class="nav-link scale-effect-icon btn btn-link header-utility-icon"
                            type="button"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#carritoSidebar"
                            aria-controls="carritoSidebar"
                    >
                        {{-- SVG de carrito de compras (Material Symbols) --}}
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
            @endauth

            {{--
                ÁREA DE PERFIL / ACCESO
                Mismo bloque @auth/@else para mostrar info del usuario o botones de acceso.
                Se separa de los íconos anteriores para mayor claridad visual en el código.
            --}}
            @auth
                {{--
                    USUARIO AUTENTICADO: muestra nombre, email y avatar con inicial.
                    Auth::user() devuelve el modelo User de la sesión activa.
                    strtoupper(substr(..., 0, 1)) toma la primera letra del nombre
                    y la convierte a mayúscula para usar como avatar de texto.
                    El enlace lleva a la página de perfil/cuenta del usuario.
                --}}
                <li class="nav-item">
                    <a class="nav-link user-account-link text-decoration-none"
                       href="{{ route('user') }}"
                       aria-label="Abrir mi cuenta"
                    >
                        <span class="user-account-summary">
                            <span class="user-account-meta">
                                <span class="user-account-name">{{ Auth::user()->name }}</span>
                                <span class="user-account-email">{{ Auth::user()->email }}</span>
                            </span>
                            {{-- Avatar circular con la inicial del nombre --}}
                            <span class="user-account-avatar">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                        </span>
                    </a>
                </li>
            @else
                {{--
                    USUARIO NO AUTENTICADO: botones que abren los modales de auth.
                    data-bs-toggle="modal" + data-bs-target="#loginModal" le dicen
                    a Bootstrap qué modal abrir sin necesitar JavaScript propio.
                    Los modales (#loginModal, #registerModal) están definidos en
                    resources/views/auth/partials/ e incluidos al final del layout.
                --}}
                <li class="nav-item">
                    <button class="nav-link scale-effect-icon border-0 bg-transparent"
                            type="button"
                            data-bs-toggle="modal"
                            data-bs-target="#loginModal"
                    >
                        {{-- SVG de ícono de persona/usuario (Material Symbols) --}}
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

                {{-- Separador visual entre "Iniciar sesión" y "Registrarse" --}}
                <span class="nav-link">|</span>

                <li class="nav-item">
                    <button class="nav-link scale-effect-icon border-0 bg-transparent"
                            type="button"
                            data-bs-toggle="modal"
                            data-bs-target="#registerModal"
                    >Registrarse</button>
                </li>
            @endauth
        </ul>
    </div>
</header>