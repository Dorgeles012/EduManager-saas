<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\RoleDashboardService;
use App\Services\SingleSessionService;
use App\Services\SubscriptionStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = $request->user();
        app(SingleSessionService::class)->claim($request, $user);

        // Si l'utilisateur doit changer obligatoirement son mot de passe à la première connexion
        if ((bool) $user->must_change_password) {
            $role = strtolower(trim((string) $user->role));
            if ($role === 'parent') {
                return redirect()->route('parent.password.change');
            }
            if ($role === 'eleve') {
                return redirect()->route('eleve.password.change');
            }
        }

        // Vérification de l'abonnement du tenant
        $subscriptionStatus = app(SubscriptionStatusService::class);
        $subscription = $subscriptionStatus->subscriptionForUser($user);

        if (! $subscriptionStatus->isExempt($user)) {
            $role = strtolower(trim((string) $user->role));
            if (! $subscription && $role === 'client') {
                return redirect()->route('client.abonnement.index');
            }
        }

        $routeName = app(RoleDashboardService::class)->routeNameFor($user);
        if (! $routeName) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['email' => 'Rôle utilisateur non autorisé.']);
        }

        return redirect()->intended(route($routeName));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
