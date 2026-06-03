<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Redirection automatique selon le rôle.
        // Vérifie d'abord explicitement que l'utilisateur existe bien dans la table `users`
        // (à partir du user déjà authentifié), puis redirige selon son rôle.
        $authUser = $request->user();

        if (! $authUser) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Utilisateur introuvable.']);
        }

        $existsInUsers = \App\Models\User::query()
            ->where('id', $authUser->id)
            ->exists();

        if (! $existsInUsers) {
            // Cas anormal: on force la déconnexion, car l'utilisateur n'existe plus.
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Utilisateur introuvable.']);
        }

        $role = $authUser->role ? strtolower((string) $authUser->role) : null;

        // Redirection selon le rôle, avec fallback non-destructif.
        return match ($role) {
            'sadmin' => redirect()->route('sadmin.dashboard'),
            'client' => redirect()->route('client.dashboard'),
            'personnel' => redirect()->route('personnel.dashboard'),
            'enseignant' => redirect()->route('enseignant.dashboard'),
            'parent' => redirect()->route('parent.dashboard'),
            default => redirect()->intended(route('sadmin.dashboard')),
        };



    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
