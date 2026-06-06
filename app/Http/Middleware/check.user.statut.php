<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class check.user.statut
{
    /**
     * Bloque l’accès si l’utilisateur est "bloqué".
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ($user->statut ?? null) === 'bloqué') {
            abort(403, 'Accès bloqué par l’administrateur.');
        }

        return $next($request);
    }
}



