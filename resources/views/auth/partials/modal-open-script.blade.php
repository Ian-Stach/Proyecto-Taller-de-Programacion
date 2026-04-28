{{--
    PARTIAL: auth/partials/modal-open-script
    ─────────────────────────────────────────────────────────────────────────────
    Script unificado que decide cuál modal de autenticación abrir automáticamente
    al cargar la página. Reemplaza tres scripts separados (uno por modal) que
    existían antes de la refactorización.

    FASE 1 — Decisión en el servidor (PHP/Blade):
      Se evalúan los error bags antes de renderizar la página.
      $serverModalId queda con el ID del modal a abrir, o null si no hay errores.
      Prioridad de evaluación:
        1. Errores en bag 'login'          → 'loginModal'
        2. Errores en bag 'register'       → 'registerModal'
        3. Errores en bag 'forgotPassword'
           o sesión 'forgotPasswordStatus' → 'forgotPasswordModal'

    FASE 2 — Apertura en el cliente (JavaScript):
      DOMContentLoaded garantiza que los elementos del modal ya están en el DOM.

      Fuente A — Servidor: @js($serverModalId) pasa el valor PHP a JS de forma segura.
        @js() escapa el valor correctamente para evitar XSS (no usar {!! !!} para esto).

      Fuente B — URL: el parámetro ?authModal= permite abrir un modal desde un enlace
        externo (ej: un email con un link directo al registro).
        modalQueryMap mapea las claves legibles de la URL a los IDs reales del modal.

      Prioridad: el servidor tiene precedencia sobre la URL.
        const modalId = @js($serverModalId) || queryModalId;

      Limpieza de URL: si el modal se abrió por ?authModal=, se borra ese parámetro
        de la barra de dirección con history.replaceState sin recargar la página,
        para que el usuario no vea el parámetro ni lo comparta accidentalmente.
    ─────────────────────────────────────────────────────────────────────────────
--}}

{{--
    BLOQUE PHP: determina en el servidor qué modal debe abrirse.
    Corre antes del render; el resultado ($serverModalId) se pasa al JS con @js().
--}}
@php
    $serverModalId = null;

    if ($errors->login->isNotEmpty()) {
        // Hay errores de validación del formulario de login
        $serverModalId = 'loginModal';
    } elseif ($errors->register->isNotEmpty()) {
        // Hay errores de validación del formulario de registro
        $serverModalId = 'registerModal';
    } elseif ($errors->forgotPassword->isNotEmpty() || session('forgotPasswordStatus')) {
        // Hay errores en el formulario de recuperación, o bien el enlace ya
        // se envió (sesión 'forgotPasswordStatus') y hay que mostrar el éxito.
        $serverModalId = 'forgotPasswordModal';
    }
@endphp

<script>
    document.addEventListener('DOMContentLoaded', function() {

        {{--
            Mapa de claves URL → IDs de modal.
            Permite usar URLs como: /home?authModal=login
            en lugar de exponer los IDs internos de Bootstrap en los enlaces.
        --}}
        const modalQueryMap = {
            login: 'loginModal',
            register: 'registerModal',
            'forgot-password': 'forgotPasswordModal',
        };

        {{-- Lee el parámetro ?authModal= de la URL actual --}}
        const searchParams = new URLSearchParams(window.location.search);
        const queryModalKey = searchParams.get('authModal');
        const queryModalId = queryModalKey ? modalQueryMap[queryModalKey] : null;

        {{--
            @js($serverModalId) convierte el valor PHP a literal JavaScript seguro.
            Si el servidor determinó un modal, ese tiene prioridad sobre la URL.
        --}}
        const modalId = @js($serverModalId) || queryModalId;

        {{-- Si no hay ninguna razón para abrir un modal, salir --}}
        if (!modalId) {
            return;
        }

        const modalElement = document.getElementById(modalId);

        {{-- Salvaguarda: si el elemento no existe en el DOM, salir sin error --}}
        if (!modalElement) {
            return;
        }

        {{-- Instancia el modal de Bootstrap y lo muestra --}}
        const modal = new bootstrap.Modal(modalElement);
        modal.show();

        {{--
            Limpieza de URL: solo si el modal se abrió por parámetro URL (no por servidor).
            history.replaceState actualiza la URL en el navegador sin recargar la página,
            eliminando ?authModal= para que la URL quede limpia.
        --}}
        if (!queryModalId) {
            return;
        }

        searchParams.delete('authModal');

        const nextQuery = searchParams.toString();
        const nextUrl = `${window.location.pathname}${nextQuery ? `?${nextQuery}` : ''}${window.location.hash}`;

        window.history.replaceState({}, '', nextUrl);
    });
</script>