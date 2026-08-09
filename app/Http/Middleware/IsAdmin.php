<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/*
 * Middleware: IsAdmin
 * --------------------
 * Verifica que el usuario autenticado tenga is_admin = true.
 * Debe usarse DESPUÉS del middleware 'auth', que garantiza que el usuario
 * ya está logueado antes de llegar aquí.
 *
 * Si el usuario no es admin → abort(403) devuelve una respuesta 403 Forbidden.
 */
class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->is_admin) {
            abort(403);
        }

        return $next($request);
    }
}
