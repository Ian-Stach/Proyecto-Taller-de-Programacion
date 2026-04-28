


<?php
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
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        
        const modalQueryMap = {
            login: 'loginModal',
            register: 'registerModal',
            'forgot-password': 'forgotPasswordModal',
        };

        
        const searchParams = new URLSearchParams(window.location.search);
        const queryModalKey = searchParams.get('authModal');
        const queryModalId = queryModalKey ? modalQueryMap[queryModalKey] : null;

        
        const modalId = <?php echo \Illuminate\Support\Js::from($serverModalId)->toHtml() ?> || queryModalId;

        
        if (!modalId) {
            return;
        }

        const modalElement = document.getElementById(modalId);

        
        if (!modalElement) {
            return;
        }

        
        const modal = new bootstrap.Modal(modalElement);
        modal.show();

        
        if (!queryModalId) {
            return;
        }

        searchParams.delete('authModal');

        const nextQuery = searchParams.toString();
        const nextUrl = `${window.location.pathname}${nextQuery ? `?${nextQuery}` : ''}${window.location.hash}`;

        window.history.replaceState({}, '', nextUrl);
    });
</script><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views/auth/partials/modal-open-script.blade.php ENDPATH**/ ?>