<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\RoleDashboardService;
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

        // Vérification immédiate de l'abonnement du tenant après authentification.
        $subscriptionStatus = app(SubscriptionStatusService::class);
        $subscription = $subscriptionStatus->subscriptionForUser($user);

        if (! $subscriptionStatus->isExempt($user)) {
            if (! $subscription && strtolower(trim((string) $user->role)) === 'client') {
                return redirect()->route('client.abonnement.index');
            }

            if (! $subscriptionStatus->isActiveForUser($user)) {
                return redirect()->route('subscription.expired');
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
