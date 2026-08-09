{{--
    PARTIAL: auth/partials/modals
    ----------------------------------------------------------------------------------------------------------------------------------------------
    Junta todos los modals del sitio.
    Este archivo es el único punto de entrada: el layout principal lo incluye una sola vez con @include('auth.partials.modals') dentro de @guest,
    lo que evita renderizar HTML de auth cuando el usuario ya está logueado.

    Orden de inclusión:
      1. login-modal          → modal con formulario de inicio de sesión.
      2. register-modal       → modal con formulario de registro de cuenta nueva.
      3. forgot-password-modal → modal para solicitar el enlace de recuperación.
      4. modal-open-script    → script que decide cuál modal abrir automáticamente en función de errores del servidor o parámetros URL.

    Los tres modales coexisten en el DOM al mismo tiempo. Bootstrap se encarga de mostrar solo uno a la vez; el script decide cuál activar.
    ----------------------------------------------------------------------------------------------------------------------------------------------
--}}


@include('auth.partials.login-modal')               <!-- Modal de inicio de sesión -->
@include('auth.partials.register-modal')            <!-- Modal de creación de cuenta -->
@include('auth.partials.forgot-password-modal')     <!-- Modal de recuperación de contraseña por email -->
@include('auth.partials.modal-open-script')         <!-- Script unificado de lógica de apertura automática de modales -->