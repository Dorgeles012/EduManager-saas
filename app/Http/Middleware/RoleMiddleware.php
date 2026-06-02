<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        $expected = strtolower($role);
        $actual = $user?->role !== null ? strtolower((string) $user->role) : null;

        if (! $user || $actual !== $expected) {
            abort(403, 'Accès interdit.');
        }

        return $next($request);
    }
}
