<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

abstract class Controller
{
    /**
     * Resolve a safe in-app URL to return to after auth actions.
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
     */
    protected function redirectToAuthModal(string $modal): RedirectResponse
    {
        return redirect()->route('home', ['authModal' => $modal]);
    }
}
