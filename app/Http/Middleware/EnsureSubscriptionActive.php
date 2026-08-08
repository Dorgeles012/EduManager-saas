<?php

namespace App\Http\Middleware;

use App\Services\SubscriptionStatusService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blocage global du tenant via l'abonnement EduManager.
 *
 * Tout utilisateur (client, personnel, enseignant, parent, élève) dont le
 * tenant n'a pas un abonnement actif est redirigé vers la page de blocage
 * (ou reçoit un 403 JSON pour les requêtes AJAX).
 *
 * Le SADMIN n'est jamais bloqué par l'abonnement d'un client.
 */
class EnsureSubscriptionActive
{
    public function __construct(
        protected SubscriptionStatusService $subscriptionStatus,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Accès interdit.');
        }

        // Le SADMIN n'est jamais soumis au blocage d'abonnement.
        if ($this->subscriptionStatus->isExempt($user)) {
            return $next($request);
        }

        $routeName = $request->route()?->getName();
        $allowed = [
            'subscription.expired',
            'logout',
        ];

        if ($routeName && in_array($routeName, $allowed, true)) {
            return $next($request);
        }

        $subscription = $this->subscriptionStatus->subscriptionForUser($user);

        if (! $subscription && $this->allowsClientWithoutSubscription($user, $routeName)) {
            return $next($request);
        }

        if ($this->subscriptionStatus->isActiveForUser($user)) {
            return $next($request);
        }

        if (! $subscription && strtolower(trim((string) $user->role)) === 'client') {
            return redirect()->route('client.abonnement.index');
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Votre abonnement EduManager n\'est plus actif.',
                'status' => 'subscription_expired',
                'redirect' => route('subscription.expired'),
            ], 403);
        }

        return redirect()->route('subscription.expired');
    }

    protected function allowsClientWithoutSubscription($user, ?string $routeName): bool
    {
        if (strtolower(trim((string) $user->role)) !== 'client') {
            return false;
        }

        if (! $routeName) {
            return false;
        }

        return $routeName === 'client.dashboard'
            || str_starts_with($routeName, 'client.abonnement')
            || str_starts_with($routeName, 'client.abonnements');
    }
}
