{{--
    PARTIAL: auth/partials/register-modal
    ─────────────────────────────────────────────────────────────────────────────
    Modal de Bootstrap con el formulario de registro de cuenta nueva.
    Solo contiene HTML; la lógica de apertura automática está en modal-open-script.

    ID del modal: #registerModal
    Error bag:    'register' → los errores de validación se agrupan bajo este nombre
                               para no mezclarse con los de login ni forgotPassword.

    Campos:
      name                  → nombre del usuario.
      email                 → correo electrónico, debe ser único en la tabla users.
      password              → contraseña nueva, mínimo 8 caracteres.
      password_confirmation → debe coincidir exactamente con password.
                               Laravel valida esto con la regla 'confirmed'.

    Nota sobre los IDs de los inputs:
      Se usan IDs con prefijo 'register-' (ej. register-email) para evitar
      colisiones con los inputs del modal de login que comparten el mismo DOM.
      Si ambos tuvieran id="email", el label de uno apuntaría al input del otro.

    Acción:
      POST {{ route('register') }} → RegisteredUserController@store.
    ─────────────────────────────────────────────────────────────────────────────
--}}
<!-- MODAL DE REGISTRO -->
<div class="modal fade auth-modal"
     id="registerModal"
     tabindex="-1"
     aria-labelledby="registerModalLabel"
     aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            {{-- Cabecera del modal con fondo amarillo de marca --}}
            <div class="modal-header bg-warning">
                <h5 class="modal-title"
                    id="registerModalLabel"
                >Crear cuenta</h5>
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
                  action="{{ route('register') }}"
            >
                @csrf
                <div class="modal-body">

                    {{-- CAMPO: nombre ────────────────────────────────────── --}}
                    <div class="mb-3">
                        <label for="register-name"
                               class="form-label"
                        >Nombre</label>
                        {{--
                            old('name') repopula el campo si el formulario falla.
                            @error('name', 'register') → agrega is-invalid si hay error
                            en el bag 'register', mostrando el borde rojo de Bootstrap.
                        --}}
                        <input id="register-name"
                               type="text"
                               name="name"
                               value="{{ old('name') }}"
                               class="form-control @error('name', 'register') is-invalid @enderror"
                               required
                               autocomplete="name"
                        >
                        @error('name', 'register')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- CAMPO: email ─────────────────────────────────────── --}}
                    <div class="mb-3">
                        <label for="register-email"
                               class="form-label"
                        >Email</label>
                        <input id="register-email"
                               type="email"
                               name="email"
                               value="{{ old('email') }}"
                               class="form-control @error('email', 'register') is-invalid @enderror"
                               required
                               autocomplete="email"
                        >
                        @error('email', 'register')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- CAMPO: contraseña ────────────────────────────────── --}}
                    <div class="mb-3">
                        <label for="register-password"
                               class="form-label"
                        >Contraseña</label>
                        {{--
                            autocomplete="new-password" le indica al navegador que es
                            una contraseña nueva (no la del login), para evitar que
                            el gestor de contraseñas rellene el campo del login aquí.
                        --}}
                        <input id="register-password"
                               type="password"
                               name="password"
                               class="form-control @error('password', 'register') is-invalid @enderror"
                               required
                               autocomplete="new-password"
                        >
                        @error('password', 'register')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- CAMPO: confirmación de contraseña ───────────────── --}}
                    <div class="mb-3">
                        <label for="register-password-confirmation"
                               class="form-label"
                        >Confirmar contraseña</label>
                        {{--
                            name="password_confirmation" → Laravel espera exactamente
                            este nombre para validar la regla 'confirmed' en el campo
                            'password'. No genera errores propios; si no coincide, el
                            error aparece en el campo 'password'.
                        --}}
                        <input id="register-password-confirmation"
                               type="password"
                               name="password_confirmation"
                               class="form-control @error('password_confirmation', 'register') is-invalid @enderror"
                               required
                               autocomplete="new-password"
                        >
                        @error('password_confirmation', 'register')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Pie del modal: botones de acción --}}
                <div class="modal-footer">
                    {{-- Cierra el modal sin enviar el formulario --}}
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                    >Cerrar</button>
                    {{-- Envía el formulario POST a /register --}}
                    <button type="submit"
                            class="btn btn-warning"
                    >Registrarse</button>
                </div>
            </form>
        </div>
    </div>
</div>