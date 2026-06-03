<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Accès interdit.');
        }

        $actual = $user->role ? strtolower(trim((string) $user->role)) : null;

        if ($actual !== 'client') {
            abort(403, 'Accès interdit.');
        }

        return $next($request);
    }
}

