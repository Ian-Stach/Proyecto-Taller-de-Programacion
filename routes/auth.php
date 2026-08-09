<?php

/*
|--------------------------------------------------------------------------
| ARCHIVO: routes/auth.php
|--------------------------------------------------------------------------
| Define las rutas del sistema de autenticación de Laravel.
| Este archivo es importado por web.php mediante require __DIR__.'/auth.php'.
|
| Las rutas están organizadas en dos grupos de middleware:
|
|   'guest'  → solo usuarios SIN sesión activa pueden acceder.
|              Si el usuario ya está logueado, Laravel lo redirige a /home.
|              Evita que un usuario logueado vea el formulario de login, etc.
|
|   'auth'   → solo usuarios CON sesión activa pueden acceder.
|              Si no está logueado, Laravel redirige a /login.
|
| Controllers involucrados (todos en app/Http/Controllers/Auth/):
|   AuthenticatedSessionController → login y logout
|   RegisteredUserController       → registro de cuenta nueva
|   PasswordResetLinkController    → solicitud de enlace de reset
|   NewPasswordController          → procesamiento del reset con token
|   ConfirmablePasswordController  → confirmación de contraseña (acción sensible)
|   PasswordController             → cambio de contraseña desde el perfil
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

// ============================================================================
// 🟡 RUTAS GUEST ONLY (Middleware: guest)
// - Solo usuarios SIN login pueden acceder
// - Usuarios logueados → Redirige a /home
// ============================================================================

Route::middleware('guest')->group(function () {

    /*
     * REGISTRO DE CUENTA
     *   GET  /register → muestra el formulario (RegisteredUserController@create)
     *                    En este proyecto no se usa esta vista standalone porque el registro se hace en el modal #registerModal.
     *   POST /register → procesa el registro   (RegisteredUserController@store)
     *                    Valida nombre, email, contraseña y crea el User. Usa el error bag 'register' para devolver errores al modal.
     */
    Route::get('register', [RegisteredUserController::class, 'create'])
                ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    /*
     * INICIO DE SESIÓN
     *   GET  /login → muestra el formulario (AuthenticatedSessionController@create)
     *                 No se usa standalone; el login está en el modal #loginModal.
     *   POST /login → procesa el login       (AuthenticatedSessionController@store)
     *                 Valida email y contraseña via LoginRequest. Usa el error bag 'login' para devolver errores al modal.
     */
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    /*
     * SOLICITUD DE ENLACE DE RESET
     *   GET  /forgot-password → muestra el formulario (PasswordResetLinkController@create)
     *                           No se usa standalone; está en el modal #forgotPasswordModal.
     *   POST /forgot-password → envía el email con el enlace (PasswordResetLinkController@store)
     *                           Flashea 'forgotPasswordStatus' en sesión al tener éxito.
     *                           Usa el error bag 'forgotPassword'.
     */
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
                ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
                ->name('password.email');

    /*
     * RESET DE CONTRASEÑA CON TOKEN
     *   GET  /reset-password/{token} → muestra el formulario con el token de la URL (NewPasswordController@create) El usuario llega aquí desde el email de reset.
     *   POST /reset-password         → valida el token y actualiza la contraseña (NewPasswordController@store)
     *                                   Al tener éxito, redirige a /login y flashea 'loginStatus' para mostrarlo en el modal de login.
     */
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
                ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
                ->name('password.store');
});

// ============================================================================
// 🔵 RUTAS AUTHENTICATED (Middleware: auth)
// - Solo usuarios CON login pueden acceder
// - Usuarios sin login → Redirige a /login
// ============================================================================

Route::middleware('auth')->group(function () {

    /*
     * CONFIRMACIÓN DE CONTRASEÑA
     *   GET  /confirm-password → solicita la contraseña antes de una acción sensible (ConfirmablePasswordController@show)
     *   POST /confirm-password → valida la contraseña introducida (ConfirmablePasswordController@store)
     *
     *   Laravel usa esta ruta cuando el middleware 'password.confirm' protege una acción (ej: eliminar cuenta). Si la contraseña no fue confirmada recientemente, redirige aquí antes de continuar.
     */
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
                ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    /*
     * CAMBIO DE CONTRASEÑA DESDE EL PERFIL
     *   PUT /password → actualiza la contraseña del usuario autenticado (PasswordController@update)
     *                   Requiere la contraseña actual para confirmar la identidad. Se usa PUT (no PATCH) porque reemplaza el campo completo.
     */
    Route::put('password', [PasswordController::class, 'update'])
                ->name('password.update');

    /*
     * CIERRE DE SESIÓN
     *   POST /logout → invalida la sesión y redirige a la home (AuthenticatedSessionController@destroy)
     *                  Se usa POST (no GET) para prevenir que un enlace externo o una imagen pueda cerrar la sesión del usuario sin su intención.
     */
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');

    /*
     * VERIFICACIÓN DE EMAIL
     *   GET  /verify-email             → avisa al usuario que debe verificar su correo (EmailVerificationPromptController) Se muestra cuando accede a una ruta 'verified' sin haber confirmado.
     *   GET  /verify-email/{id}/{hash} → procesa el enlace del email y marca el correo como verificado (VerifyEmailController) Redirige a /user?verified=1 al tener éxito.
     *   POST /email/verification-notification → reenvía el email de verificación (EmailVerificationNotificationController) Limitado a 6 envíos por minuto (throttle).
     */
    Route::get('verify-email', EmailVerificationPromptController::class)
                ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
                ->middleware(['signed', 'throttle:6,1'])
                ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->middleware('throttle:6,1')
                ->name('verification.send');
});
