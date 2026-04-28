{{--
    PARTIAL: auth/partials/modals
    ─────────────────────────────────────────────────────────────────────────────
    Orquestador de todos los modales de autenticación del sitio.
    Este archivo es el único punto de entrada: el layout principal lo incluye
    una sola vez con @include('auth.partials.modals') dentro de @guest,
    lo que evita renderizar HTML de auth cuando el usuario ya está logueado.

    Orden de inclusión:
      1. login-modal          → modal con formulario de inicio de sesión.
      2. register-modal       → modal con formulario de registro de cuenta nueva.
      3. forgot-password-modal → modal para solicitar el enlace de recuperación.
      4. modal-open-script    → script que decide cuál modal abrir automáticamente
                                 en función de errores del servidor o parámetros URL.

    Los tres modales coexisten en el DOM al mismo tiempo. Bootstrap se encarga
    de mostrar solo uno a la vez; el script decide cuál activar.
    ─────────────────────────────────────────────────────────────────────────────
--}}
{{-- Modal de inicio de sesión --}}
@include('auth.partials.login-modal')

{{-- Modal de creación de cuenta --}}
@include('auth.partials.register-modal')

{{-- Modal de recuperación de contraseña por email --}}
@include('auth.partials.forgot-password-modal')

{{-- Script unificado de lógica de apertura automática de modales --}}
@include('auth.partials.modal-open-script')