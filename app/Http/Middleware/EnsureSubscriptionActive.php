<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vérifie que le client dispose d'un abonnement dont le statut est "actif".
 *
 * Si l'abonnement est en_attente ou payé (mais non validé par le SADMIN),
 * l'utilisateur est redirigé vers la page Abonnements (seule rubrique accessible).
 */
class EnsureSubscriptionActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Accès interdit.');
        }

        // Seuls les clients sont concernés par la validation d'abonnement.
        if (strtolower(trim((string) $user->role)) !== 'client') {
            return $next($request);
        }

        // Si la colonne n'existe pas encore (migration non exécutée),
        // on laisse passer pour ne pas casser l'existant.
        if (! Schema::hasColumn('subscriptions', 'abonnement_status')) {
            return $next($request);
        }

        $subscription = \App\Models\Subscription::query()
            ->where('user_id', $user->id)
            ->orWhere('client_id', $user->id)
            ->whereNotNull('plan_id')
            ->latest()
            ->first();

        // Aucun abonnement : seule la rubrique Abonnements est accessible.
        if (! $subscription || ! $subscription->isAbonnementActif()) {
            // Routes déjà autorisées (abonnements, dashboard, logout...)
            $allowed = [
                'client.abonnement.index',
                'client.abonnements.index',
                'client.abonnements.create',
                'client.abonnements.store',
                'client.dashboard',
                'logout',
                'client.parametres.index',
            ];

            if ($request->route() && in_array($request->route()->getName(), $allowed, true)) {
                return $next($request);
            }

            return redirect()->route('client.abonnements.index')
                ->with('error', 'Votre abonnement doit être validé pour accéder à cette fonctionnalité.');
        }

        return $next($request);
    }
}
