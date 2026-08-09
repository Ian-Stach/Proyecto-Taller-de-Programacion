{{--
    LAYOUT PRINCIPAL: Jurassic_Store
    ----------------------------------------------------------------------------------------------------------------------------------------------
    Este archivo es el esqueleto compartido por todas las vistas del sitio.
    Ninguna vista genera su propio HTML completo; en cambio, cada una extiende este layout con @extends('layouts.Jurassic_Store') y define solo su
    contenido particular dentro de @section('content').

    Estructura en orden de renderizado:
      1. <head>        → metadatos y estilos globales
      2. header        → barra negra superior (logo, buscador, acciones de usuario)
      3. main-nav      → barra de navegación amarilla con los enlaces del sitio
      4. @yield        → contenido propio de la vista actual (varía por página)
      5. cart-sidebar  → panel lateral del carrito (solo para usuarios autenticados)
      6. footer        → pie de página común
      7. auth modals   → modals de login, registro y recuperación (solo para invitados)
      8. scripts       → JavaScript global cargado al final del body
    ----------------------------------------------------------------------------------------------------------------------------------------------
--}}

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">     <!-- Codificación de caracteres: garantiza que tildes y ñ se muestren bien -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">     <!-- Viewport responsivo: escala la página correctamente en móviles -->

        <title>JURASSIC STORE</title>

        <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">     <!-- Bootstrap CSS, se carga antes de estilos.css para sobreescribirlo -->
        <link rel="stylesheet" href="{{ asset('css/estilos.css') }}?v={{ filemtime(public_path('css/estilos.css')) }}">     <!-- Estilos propios del sitio -->
    </head>

    <body>
        
        @include('layouts.partials.header')       <!-- HEADER SUPERIOR -->
        @include('layouts.partials.main-nav')     <!-- NAVBAR PRINCIPAL -->
        @yield('content')                         <!-- CONTENIDO PROPIO DE CADA VISTA -->

        {{--
            SIDEBAR DEL CARRITO (solo usuarios autenticados)
            Panel offcanvas de Bootstrap que se desliza desde la derecha.
            Se omite completamente para guests porque:
              - no tienen sesión de carrito activa,
              - requiere las variables $sidebarCartItems y $sidebarCartSubtotal que el ViewComposer solo inyecta cuando hay usuario logueado.
            → resources/views/layouts/partials/cart-sidebar.blade.php
        --}}
        @auth
            {{--
                TOAST DEL CARRITO
                Notificación flotante (Bootstrap Toast) que aparece en la esquina inferior derecha cuando el usuario agrega un producto al carrito vía AJAX (cart.js).
                Solo existe para usuarios autenticados porque solo ellos tienen acceso al carrito.
            --}}
            <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
                <div id="cart-toast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body" id="cart-toast-body"></div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
                    </div>
                </div>
            </div>

            @include('layouts.partials.cart-sidebar')
        @endauth

        {{--
            FOOTER
            Pie de página común con descripción del sitio, enlaces rápidos y sección de redes sociales.
            → resources/views/layouts/partials/footer.blade.php
        --}}
        @include('layouts.partials.footer')

        {{--
            MODALES DE AUTENTICACIÓN (solo usuarios no autenticados)
            Se omiten para usuarios ya logueados porque no los necesitan y evitamos renderizar HTML innecesario en cada request.
            Incluye tres modales y un único script de lógica de apertura:
              - login-modal          → formulario de inicio de sesión
              - register-modal       → formulario de registro de cuenta nueva
              - forgot-password-modal → formulario de recuperación por email
              - modal-open-script    → decide qué modal abrir automáticamente:
                                        a) si hay errores de validación del servidor
                                        b) si la URL trae ?authModal=login|register|forgot-password
            → resources/views/auth/partials/modals.blade.php
        --}}
        @guest
            @include('auth.partials.modals')
        @endguest

        {{--
            SCRIPTS GLOBALES
            Se colocan al final del <body> para que el HTML ya esté completamente parseado cuando el JavaScript se ejecute, evitando errores de "elemento no encontrado".

            header-search-suggest.js → autocomplete del buscador del header.
              defer: el script se descarga en paralelo pero ejecuta después de que el DOM esté listo, sin bloquear el render de la página.

            bootstrap.bundle.min.js → Bootstrap JS con Popper incluido.
              Necesario para modales, offcanvas del carrito y tooltips.
              Va después de los modales en el DOM para que Bootstrap pueda encontrar los elementos cuando inicializa los listeners.

            cart.js → maneja el envío AJAX de formularios .cart-add-form.
              Llama a CartController@add vía fetch() y muestra el resultado en el Bootstrap Toast #cart-toast sin recargar la página.
              Va después de bootstrap.bundle porque usa bootstrap.Toast.
        --}}
        <script src="{{ asset('js/header-search-suggest.js') }}" defer></script>
        <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}" defer></script>
        <script src="{{ asset('js/cart.js') }}" defer></script>

        @stack('scripts')
    </body>
</html>