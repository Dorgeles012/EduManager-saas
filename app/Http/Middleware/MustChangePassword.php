<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Oblige un utilisateur (ex. élève) à modifier son mot de passe
 * avant d'accéder au reste de l'application.
 */
class MustChangePassword
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Accès interdit.');
        }

        // Si l'utilisateur doit changer son mot de passe, on le redirige
        // vers la page de changement (sauf s'il y est déjà).
        if ((bool) $user->must_change_password) {
            $current = $request->route()?->getName();

            $allowed = [
                'eleve.password.change',
                'eleve.password.update',
                'eleve.logout',
                'logout',
            ];

            if (! in_array($current, $allowed, true)) {
                return redirect()->route('eleve.password.change');
            }
        }

        return $next($request);
    }
}
