<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatut
{
    /**
     * Bloque l’accès (sans empêcher le login) si l’utilisateur est "bloqué".
     */
    public function handle(Request $request, Closure $next, string $blockedValue = 'bloqué'): Response
    {
        $user = $request->user();

        if ($user && in_array(strtolower((string) ($user->statut ?? '')), ['bloqué', 'bloque', 'blocked'], true)) {
            abort(403, 'Accès bloqué par l’administrateur.');
        }

        return $next($request);
    }
}



