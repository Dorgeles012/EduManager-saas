<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PersonnelMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Accès interdit.');
        }

        $actual = $user->role ? strtolower(trim((string) $user->role)) : null;

        if ($actual !== 'personnel') {
            abort(403, 'Accès interdit.');
        }

        return $next($request);
    }
}

