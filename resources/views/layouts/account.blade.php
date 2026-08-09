{{--
    LAYOUT: layouts/account
    ─────────────────────────────────────────────────────────────────────────────
    Esqueleto compartido por todas las vistas del área de cuenta de usuario.
    Las vistas que lo extienden solo definen su contenido dentro de
    @section('content') y, opcionalmente, scripts adicionales en @push('scripts').

    Diferencias con layouts/Jurassic_Store:
      - Header propio (más compacto, sin buscador ni iconos de carrito/favoritos)
      - No incluye el sidebar del carrito ni los modales de autenticación
      - Añade @stack('scripts') para que las vistas puedan inyectar JS puntual

    Estructura en orden de renderizado:
      1. <head>            → metadatos, Bootstrap CSS y estilos propios
      2. header            → barra negra con logo, nombre de marca y avatar de cuenta
      3. main-nav          → barra de navegación amarilla (partial compartido)
      4. @yield('content') → contenido propio de la vista actual
      5. footer            → pie de página común (partial compartido)
      6. Bootstrap JS      → cargado al final del body para no bloquear el render
      7. @stack('scripts') → scripts opcionales inyectados por la vista activa
    ─────────────────────────────────────────────────────────────────────────────
--}}
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">     <!-- Codificación de caracteres: garantiza que tildes y ñ -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">     <!-- Viewport responsivo: escala la página correctamente en móviles -->
        <title>JURASSIC STORE</title>
        <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">     <!-- Bootstrap CSS (copiado a public/vendor para no depender de CDN) -->
        <link rel="stylesheet" href="{{ asset('css/estilos.css') }}?v={{ filemtime(public_path('css/estilos.css')) }}" >     <!-- Estilos propios -->
    </head>

    <body>
        {{--
            HEADER DE CUENTA
            Barra negra específica del área de usuario. Más compacta que el header
            principal del sitio: no tiene buscador ni iconos de carrito/favoritos.

            Contiene tres zonas:
              · Izquierda (.user-account-brand):
                  - Botón hamburguesa (solo mobile) que abre el offcanvas del sidebar
                    de navegación definido en la vista activa (#accountSidebarMobile).
                  - Logo imagen enlazado a la home.
                  - Nombre de marca "Jurassic Store" (oculto en mobile).
              · Centro/derecha desktop (.user-account-summary, d-none d-md-flex):
                  - Nombre y email del usuario autenticado.
                  - Avatar con la inicial del nombre.
              · Derecha mobile (.user-account-mobile-avatar-link, d-flex d-md-none):
                  - Solo el avatar, enlazado a la página de cuenta (/user).
        --}}
        <header class="navbar navbar-expand-lg navbar-dark bg-black header-custom user-account-header">
            <div class="container-fluid user-account-header-bar">
                <div class="user-account-brand">
                    
                {{--
                        Botón hamburguesa (solo mobile):
                        Abre el offcanvas #accountSidebarMobile definido en cada vista
                        que extiende este layout (actualmente solo profile/user).
                    --}}
                    <button class="navbar-toggler d-md-none account-menu-toggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#accountSidebarMobile" aria-controls="accountSidebarMobile" aria-label="Abrir menú de cuenta">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    
                    <a href="{{ route('home') }}" class="d-inline-flex align-items-center">
                        <img src="{{ asset('images/js_logo_header.png') }}" alt="logo" width="60" height="40" class="d-inline-block align-text-top">
                    </a>
                    <!-- Nombre de marca: visible solo en pantallas medianas y grandes -->
                    <a class="header-brand header-brand-font header-brand-large mb-0 d-none d-md-inline-block" href="{{ route('home') }}">Jurassic Store</a>
                </div>

                <!-- Resumen del usuario autenticado: visible solo en desktop -->
                <ul class="nav ms-auto align-items-center flex-shrink-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link text-decoration-none dropdown-toggle user-summary-link" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="d-none d-md-flex user-summary">
                                <span class="user-summary-meta">
                                    <span class="user-summary-name">{{ Auth::user()->name }}</span>
                                    <span class="user-summary-email">{{ Auth::user()->email }}</span>
                                </span>
                                @if(Auth::user()->photo)
                                    <img src="{{ asset('storage/' . Auth::user()->photo) }}" class="summary-dropdown-photo" alt="Foto de perfil">
                                @else
                                    <div class="summary-dropdown-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                                @endif
                            </span>
                        </a>
                
                        <ul class="dropdown-menu dropdown-menu-end summary-dropdown-menu" aria-labelledby="userDropdown">
                            <li class="summary-dropdown-header mx-2 px-3 py-2">
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
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('favorites.index') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="22px" viewBox="0 -960 960 960" width="22px" fill="#f1f1f1">
                                        <path d="m480-120-58-52q-101-91-167-157T150-447.5Q111-500 95.5-544T80-634q0-94 63-157t157-63q52 0 99 22t81 62q34-40 81-62t99-22q94 0 157 63t63 157q0 46-15.5 90T810-447.5Q771-395 705-329T538-172l-58 52Zm0-108q96-86 158-147.5t98-107q36-45.5 50-81t14-70.5q0-60-40-100t-100-40q-47 0-87 26.5T518-680h-76q-15-41-55-67.5T300-774q-60 0-100 40t-40 100q0 35 14 70.5t50 81q36 45.5 98 107T480-228Zm0-273Z"/>
                                    </svg>
                                    Mis favoritos
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('principal') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="22px" viewBox="0 -960 960 960" width="22px" fill="#f1f1f1">
                                        <path d="M841-518v318q0 33-23.5 56.5T761-120H201q-33 0-56.5-23.5T121-200v-318q-23-21-35.5-54t-.5-72l42-136q8-26 28.5-43t47.5-17h556q27 0 47 16.5t29 43.5l42 136q12 39-.5 71T841-518Zm-272-42q27 0 41-18.5t11-41.5l-22-140h-78v148q0 21 14 36.5t34 15.5Zm-180 0q23 0 37.5-15.5T441-612v-148h-78l-22 140q-4 24 10.5 42t37.5 18Zm-178 0q18 0 31.5-13t16.5-33l22-154h-78l-40 134q-6 20 6.5 43t41.5 23Zm540 0q29 0 42-23t6-43l-42-134h-76l22 154q3 20 16.5 33t31.5 13ZM201-200h560v-282q-5 2-6.5 2H751q-27 0-47.5-9T663-518q-18 18-41 28t-49 10q-27 0-50.5-10T481-518q-17 18-39.5 28T393-480q-29 0-52.5-10T299-518q-21 21-41.5 29.5T211-480h-4.5q-2.5 0-5.5-2v282Zm560 0H201h560Z"/>
                                    </svg>
                                    Volver a la tienda
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
                                    <button class="dropdown-item text-danger" type="submit">Cerrar sesión</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>

                <!-- Avatar mobile: enlaza a /user y reemplaza al resumen de desktop -->
                <a href="{{ route('user') }}" class="d-flex d-md-none user-summary-mobile-avatar-link" aria-label="Mi cuenta">
                    <div class="user-summary-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                </a>
            </div>
        </header>

        {{--
            BARRA DE NAVEGACIÓN PRINCIPAL
            Franja amarilla con los enlaces del sitio, compartida con el layout
            principal. Se incluye como partial para no duplicar el marcado.
            → resources/views/layouts/partials/main-nav.blade.php
        --}}
        @include('layouts.partials.main-nav')

        {{--
            CONTENIDO DE LA VISTA ACTUAL
            Reemplazado en tiempo de render por el @section('content') de cada vista.
        --}}
        @yield('content')

        {{--
            FOOTER
            Pie de página común a todas las vistas del sitio.
            → resources/views/layouts/partials/footer.blade.php
        --}}
        @include('layouts.partials.footer')

        {{--
            Bootstrap JS (con Popper incluido).
            Se carga al final del body para no bloquear el render de la página.
            Necesario para el offcanvas del sidebar mobile y el modal de eliminación.
        --}}
        <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

        {{--
            Scripts opcionales de la vista activa.
            Se ejecutan después de Bootstrap JS para que los componentes estén
            disponibles cuando el script los necesite.
            Ejemplo: profile/user lo usa para reabrir el modal de eliminación
            de cuenta si la validación falló.
        --}}
        @stack('scripts')
    </body>
</html>