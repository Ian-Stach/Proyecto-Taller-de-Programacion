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
        {{-- Codificación de caracteres: garantiza que tildes y ñ se muestren bien --}}
        <meta charset="UTF-8">

        {{-- Viewport responsivo: escala la página correctamente en móviles --}}
        <meta name="viewport"
              content="width=device-width, initial-scale=1.0"
        >

        <title>JURASSIC STORE</title>

        {{-- Bootstrap CSS (copiado a public/vendor para no depender de CDN) --}}
        <link rel="stylesheet"
              href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}"
        >

        {{-- Estilos propios: colores, tipografía y componentes custom del sitio --}}
        <link rel="stylesheet"
              href="{{ asset('css/estilos.css') }}"
        >
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
        <header class="navbar navbar-expand-lg navbar-dark bg-black header-tall user-account-header">
            <div class="container-fluid user-account-header-bar">
                <div class="user-account-brand">
                    {{--
                        Botón hamburguesa (solo mobile):
                        Abre el offcanvas #accountSidebarMobile definido en cada vista
                        que extiende este layout (actualmente solo profile/user).
                    --}}
                    <button class="navbar-toggler d-md-none account-menu-toggle"
                            type="button"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#accountSidebarMobile"
                            aria-controls="accountSidebarMobile"
                            aria-label="Abrir menú de cuenta"
                    >
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <a href="{{ route('home') }}"
                       class="d-inline-flex align-items-center"
                    >
                        <img src="{{ asset('images/jp_logo.jpg') }}"
                             alt="logo"
                             width="60"
                             height="40"
                             class="d-inline-block align-text-top"
                        >
                    </a>
                    {{-- Nombre de marca: visible solo en pantallas medianas y grandes --}}
                    <a class="navbar-brand navbar-brand-custom navbar-brand-large mb-0 d-none d-md-inline-block"
                       href="{{ route('home') }}"
                    >Jurassic Store</a>
                </div>

                {{-- Resumen del usuario autenticado: visible solo en desktop --}}
                <div class="user-account-summary d-none d-md-flex">
                    <div class="user-account-meta">
                        <div class="user-account-name">{{ Auth::user()->name }}</div>
                        <div class="user-account-email">{{ Auth::user()->email }}</div>
                    </div>
                    {{-- Avatar desktop: inicial del nombre en mayúscula --}}
                    <div class="user-account-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                </div>

                {{-- Avatar mobile: enlaza a /user y reemplaza al resumen de desktop --}}
                <a href="{{ route('user') }}"
                   class="d-flex d-md-none user-account-mobile-avatar-link"
                   aria-label="Mi cuenta"
                >
                    <div class="user-account-avatar">
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
