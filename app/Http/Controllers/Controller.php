<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

/*
 * Controller (clase base abstracta)
 * -----------------------------------
 * Todos los controladores del proyecto extienden esta clase.
 * No tiene métodos de acción HTTP propios; solo provee helpers
 * de uso común para los controladores hijos.
 *
 * Métodos utilitarios:
 *   resolvePreviousAppUrl() → URL segura a la que redirigir después de una acción de auth.
 *   redirectToAuthModal()   → redirige a la home pidiendo que se abra un modal específico.
 */
abstract class Controller
{
    /**
     * Resolve a safe in-app URL to return to after auth actions.
     *
     * Propósito: después de login/logout/register, redirigir al usuario a donde estaba,
     * pero SOLO si la URL anterior pertenece al mismo dominio (previene open redirect).
     *
     * Lógica:
     *   1. Si el host de la URL anterior es externo → usa la ruta fallback (home por defecto).
     *   2. Si el path anterior es una ruta de auth (login, register, forgot-password,
     *      reset-password) → también usa el fallback, para no crear bucles.
     *   3. En cualquier otro caso, reconstruye la URL interna con su path y query string.
     *
     * absolute: false en route() devuelve solo el path (/home) en vez de la URL completa,
     * para que el fallback también sea relativo y no dependa del host configurado.
     */
    protected function resolvePreviousAppUrl(string $fallbackRouteName = 'home'): string
    {
        $fallbackUrl = route($fallbackRouteName, absolute: false);
        $previousUrl = url()->previous();
        $previousHost = parse_url($previousUrl, PHP_URL_HOST);
        $currentHost = parse_url(url('/'), PHP_URL_HOST);
        $previousPath = trim((string) parse_url($previousUrl, PHP_URL_PATH), '/');

        if ($previousHost === null || $currentHost === null || $previousHost === $currentHost) {
            if (! in_array($previousPath, ['login', 'register', 'forgot-password'], true)
                && ! str_starts_with($previousPath, 'reset-password')) {
                $query = parse_url($previousUrl, PHP_URL_QUERY);
                $fallbackUrl = $previousPath === '' ? '/' : '/'.$previousPath;
                $fallbackUrl = $query ? $fallbackUrl.'?'.$query : $fallbackUrl;
            }
        }

        return $fallbackUrl;
    }

    /**
     * Redirect to the home page and request an auth modal to open.
     *
     * Propósito: cuando un guest intenta acceder a una ruta protegida (p. ej. /favorites),
     * en vez de redirigir a /login (que en este proyecto no existe como página), se
     * redirige a la home con ?authModal=login en la URL. modal-open-script.blade.php
     * lee ese parámetro para abrir automáticamente el modal de login correspondiente.
     *
     * $modal puede ser: 'login', 'register' o 'forgotPassword'.
     */
    protected function redirectToAuthModal(string $modal): RedirectResponse
    {
        return redirect()->route('home', ['authModal' => $modal]);
    }
}
