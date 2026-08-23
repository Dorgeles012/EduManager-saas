<?php

namespace App\Http\Middleware;

use App\Models\Subscription;
use App\Services\SubscriptionStatusService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Contrôle d'abonnement global du tenant EduManager.
 *
 * LOGIQUE D'ACCÈS :
 * ─────────────────────────────────────────────────────────────────
 *  SADMIN                        → toujours autorisé (isExempt)
 *  Client sans abonnement        → dashboard + page abonnement
 *  Client abonnement `en_attente`→ dashboard + page abonnement
 *  Client abonnement `paye`      → dashboard + page abonnement
 *  Client abonnement `actif`     → accès complet (non expiré)
 *  Client en période de grâce    → accès complet + banderole avertissement
 *  Client grâce expirée          → redirection subscription.expired
 *  Autres rôles (personnel, etc.)→ même logique via tenant_id
 * ─────────────────────────────────────────────────────────────────
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

        // Routes toujours accessibles quel que soit le statut d'abonnement.
        if ($routeName && in_array($routeName, $this->alwaysAllowedRoutes(), true)) {
            return $next($request);
        }

        $subscription = $this->subscriptionStatus->subscriptionForUser($user);

        // Cas 1 : Aucun abonnement → dashboard + abonnement autorisés.
        if (! $subscription) {
            if ($this->isAbonnementOrDashboardRoute($user, $routeName)) {
                return $next($request);
            }
            // Toute autre route → redirection vers la page abonnement.
            if (strtolower(trim((string) $user->role)) === 'client') {
                return redirect()->route('client.abonnement.index');
            }
            return redirect()->route('subscription.expired');
        }

        // Cas 2 : Abonnement en attente de paiement ou de validation (paye/en_attente)
        // → dashboard + page abonnement uniquement.
        if ($this->isPendingOrPaid($subscription)) {
            if ($this->isAbonnementOrDashboardRoute($user, $routeName)) {
                return $next($request);
            }
            // Toute autre fonctionnalité est bloquée.
            if (strtolower(trim((string) $user->role)) === 'client') {
                return redirect()->route('client.abonnement.index')
                    ->with('info', 'Votre paiement est en attente de validation par l\'administrateur. L\'accès complet sera disponible après validation.');
            }
            return redirect()->route('subscription.expired');
        }

        // Cas 3 : Abonnement actif et non expiré → accès complet.
        if ($this->subscriptionStatus->isActiveForUser($user)) {
            return $next($request);
        }

        // Cas 4 : Accès refusé (grâce expirée) → JSON pour AJAX, redirect sinon.
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Votre abonnement EduManager n\'est plus actif.',
                'status' => 'subscription_expired',
                'redirect' => route('subscription.expired'),
            ], 403);
        }

        return redirect()->route('subscription.expired');
    }

    /**
     * Routes toujours accessibles (peu importe le statut d'abonnement).
     */
    protected function alwaysAllowedRoutes(): array
    {
        return [
            'subscription.expired',
            'logout',
            'parent.password.change',
            'parent.password.change.update',
            'parent.logout',
            'eleve.password.change',
            'eleve.password.update',
            'eleve.logout',
        ];
    }

    /**
     * Vérifie si l'abonnement est en statut "payé" (attendant validation SADMIN)
     * ou "en attente" (pas encore payé).
     */
    protected function isPendingOrPaid(Subscription $subscription): bool
    {
        return in_array($subscription->abonnement_status, [
            Subscription::ABONNEMENT_EN_ATTENTE,
            Subscription::ABONNEMENT_PAYE,
        ], true);
    }

    /**
     * Vérifie si la route est la page abonnement ou le dashboard client.
     * Ces routes restent accessibles même sans abonnement actif.
     */
    protected function isAbonnementOrDashboardRoute($user, ?string $routeName): bool
    {
        if (! $routeName) {
            return false;
        }

        $role = strtolower(trim((string) $user->role));

        // Client : dashboard et toutes les routes abonnement
        if ($role === 'client') {
            return $routeName === 'client.dashboard'
                || str_starts_with($routeName, 'client.abonnement')
                || str_starts_with($routeName, 'client.abonnements');
        }

        // Autres rôles non-SADMIN : accès dashboard uniquement
        $dashboardRoutes = [
            'personnel.dashboard',
            'enseignant.dashboard',
            'parent.dashboard',
            'eleve.dashboard',
        ];

        return in_array($routeName, $dashboardRoutes, true);
    }
}
