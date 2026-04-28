{{--
    PARTIAL: auth/partials/forgot-password-modal
    ─────────────────────────────────────────────────────────────────────────────
    Modal de Bootstrap para solicitar el enlace de restablecimiento de contraseña.
    Solo contiene HTML; la lógica de apertura automática está en modal-open-script.

    ID del modal: #forgotPasswordModal
    Error bag:    'forgotPassword' → agrupa los errores de este formulario
                                     separados de login y register.

    Sesión:
      session('forgotPasswordStatus') → mensaje de confirmación que flashea
                                        PasswordResetLinkController@store cuando
                                        el email existe y el enlace se envió.
                                        Se usa esta clave (no 'status') para evitar
                                        que el modal de login la absorba accidentalmente.

    Flujo completo:
      1. Usuario abre este modal desde el enlace "¿Olvidaste tu contraseña?" del login.
      2. Ingresa su email y envía el formulario → POST /forgot-password.
      3. PasswordResetLinkController@store flashea 'forgotPasswordStatus'.
      4. La página recarga → modal-open-script detecta la sesión y reabre este modal.
      5. El usuario ve el mensaje de éxito dentro del modal.

    Acción:
      POST {{ route('password.email') }} → PasswordResetLinkController@store.

    Pie del modal:
      "Volver al login" → cambia a #loginModal con data-bs-toggle (sin cerrar overlay).
      "Cerrar"          → cierra el modal con data-bs-dismiss.
      "Enviar enlace"   → submit del formulario.
    ─────────────────────────────────────────────────────────────────────────────
--}}
<!-- MODAL DE RECUPERACION DE CONTRASENA -->
<div class="modal fade auth-modal"
     id="forgotPasswordModal"
     tabindex="-1"
     aria-labelledby="forgotPasswordModalLabel"
     aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            {{-- Cabecera del modal con fondo amarillo de marca --}}
            <div class="modal-header bg-warning">
                <h5 class="modal-title"
                    id="forgotPasswordModalLabel"
                >Recuperar contraseña</h5>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Cerrar"
                ></button>
            </div>

            {{--
                El formulario envuelve body y footer del modal.
                @csrf → token de Laravel obligatorio para cualquier formulario POST.
            --}}
            <form method="POST"
                  action="{{ route('password.email') }}"
            >
                @csrf
                <div class="modal-body">

                    {{-- Texto instructivo: explica qué va a pasar al enviar el form --}}
                    <p class="text-muted mb-3">
                        Ingresa tu email y te enviaremos un enlace para restablecer tu contraseña.
                    </p>

                    {{--
                        Mensaje de éxito post-envío del enlace.
                        PasswordResetLinkController flashea 'forgotPasswordStatus' con
                        el texto traducido de passwords.sent (lang/es/passwords.php).
                        Solo aparece si la sesión tiene esta clave; desaparece en el
                        siguiente request porque es un flash (se consume una sola vez).
                    --}}
                    @if(session('forgotPasswordStatus'))
                        <div class="alert alert-success">{{ session('forgotPasswordStatus') }}</div>
                    @endif

                    {{-- CAMPO: email ─────────────────────────────────────── --}}
                    <div class="mb-3">
                        <label for="forgot-password-email"
                               class="form-label"
                        >Email</label>
                        {{--
                            ID con prefijo 'forgot-password-' para evitar colisiones
                            con el input email del modal de login en el mismo DOM.
                            autocomplete="username" → estándar de accesibilidad para
                            campos de email en contextos de autenticación.
                        --}}
                        <input id="forgot-password-email"
                               type="email"
                               name="email"
                               value="{{ old('email') }}"
                               class="form-control @error('email', 'forgotPassword') is-invalid @enderror"
                               required
                               autocomplete="username"
                        >
                        @error('email', 'forgotPassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{--
                    Pie del modal: justify-content-between separa el botón "Volver"
                    a la izquierda y los botones "Cerrar / Enviar" a la derecha.
                --}}
                <div class="modal-footer justify-content-between">
                    {{--
                        Botón para volver al login sin cerrar el overlay oscuro.
                        data-bs-toggle="modal" + data-bs-target → Bootstrap cambia
                        el modal activo manteniendo el backdrop en pantalla.
                    --}}
                    <button type="button"
                            class="btn btn-link link-dark p-0 text-decoration-none"
                            data-bs-toggle="modal"
                            data-bs-target="#loginModal"
                    >Volver al login</button>
                    <div class="d-flex gap-2">
                        {{-- Cierra el modal sin enviar el formulario --}}
                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal"
                        >Cerrar</button>
                        {{-- Envía el formulario POST a /forgot-password --}}
                        <button type="submit"
                                class="btn btn-warning"
                        >Enviar enlace</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>