{{--
    PARTIAL: auth/partials/login-modal
    ─────────────────────────────────────────────────────────────────────────────
    Modal de Bootstrap con el formulario de inicio de sesión.
    Solo contiene HTML; la lógica de apertura automática está en modal-open-script.

    ID del modal: #loginModal
    Error bag:    'login'  → los errores de validación se agrupan bajo este nombre para no mezclarse con los de register ni forgotPassword.
                             Ver app/Http/Requests/Auth/LoginRequest.php.

    Sesión:
      session('loginStatus') → mensaje de éxito que llega tras un reset de contraseña exitoso. Lo flash NewPasswordController con la clave 'loginStatus' (no 'status') para evitar que el modal
                               de recuperación lo absorba.

    Campos:
      email    → type="email", autocomplete="username" (estándar de accesibilidad).
      password → type="password", autocomplete="current-password".
      remember → checkbox que extiende la duración de la sesión.

    Acciones:
      POST {{ route('login') }} → maneja AuthenticatedSessionController@store.
      Enlace "¿Olvidaste tu contraseña?" → abre #forgotPasswordModal con data-bs-toggle.
    ─────────────────────────────────────────────────────────────────────────────
--}}

<!-- MODAL DE LOGIN -->
<div class="modal fade auth-modal" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="loginModalLabel">Iniciar sesión</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- El formulario envuelve tanto el body como el footer del modal para que el botón "Entrar" del footer pueda hacer submit.
                action="route('login')" → POST a /login → AuthenticatedSessionController@store. @csrf → token de Laravel obligatorio para cualquier formulario POST. -->

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="modal-body">

                    <!-- Mensaje de éxito post-reset de contraseña.
                        NewPasswordController flashea 'loginStatus' al redirigir a /login, y aquí lo mostramos como alerta verde antes del formulario.
                        Se usa 'loginStatus' en lugar de 'status' para que el modal de recuperación no lo consuma accidentalmente. -->
                    
                    @if(session('loginStatus'))
                        <div class="alert alert-success">{{ session('loginStatus') }}</div>
                    @endif

                    <!-- CAMPO: email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <!-- old('email') repopula el campo si el formulario falla, para que el usuario no tenga que volver a escribirlo.
                            @@error('email', 'login') agrega la clase is-invalid de Bootstrap que muestra el borde rojo y habilita .invalid-feedback. -->
                        <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email', 'login') is-invalid @enderror" required autofocus autocomplete="username">
                        @error('email', 'login')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- CAMPO: contraseña -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <!-- No usa old() porque las contraseñas nunca se repopulan por seguridad (nunca deben volver al HTML como valor). -->
                        <input id="password" type="password" name="password" class="form-control @error('password', 'login') is-invalid @enderror" required autocomplete="current-password">
                        @error('password', 'login')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- CAMPO: recordarme -->
                    <div class="mb-3 form-check">
                        <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                        <label for="remember_me" class="form-check-label">Recordarme</label>
                    </div>

                    <!-- Enlace a recuperación de contraseña.
                        Solo se muestra si la ruta 'password.request' existe en el sistema (Route::has verifica esto en tiempo de render).
                        Usa data-bs-toggle/target para cambiar de modal sin cerrar el overlay. -->
                    @if (Route::has('password.request'))
                        <button type="button" class="btn btn-link link-dark p-0 text-decoration-none" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">¿Olvidaste tu contraseña?</button>
                    @endif
                </div>

                <!-- Pie del modal: botones de acción -->
                <div class="modal-footer">
                    <!-- Cierra el modal sin enviar el formulario -->
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <!-- Envía el formulario POST a /login -->
                    <button type="submit" class="btn btn-warning">Entrar</button>
                </div>
            </form>
        </div>
    </div>
</div>