<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

/**
 * Service centralisé permettant de déterminer si le tenant d'un utilisateur
 * dispose d'un abonnement actif.
 *
 * Architectuure multi-tenant :
 *
 *    Utilisateur connecté
 *        ↓
 *      tenant_id
 *        ↓
 *      Subscription (tenant_id + date_fin + abonnement_status)
 *        ↓
 *      actif ? → accès normal
 *      non   → blocage global du tenant
 *
 * Le SADMIN n'est jamais soumis au blocage par abonnement client.
 */
class SubscriptionStatusService
{
    /**
     * Détermine si l'utilisateur doit être exempté du contrôle d'abonnement.
     * Le SADMIN est toujours autorisé (il gère les abonnements).
     */
    public function isExempt(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return strtolower(trim((string) $user->role)) === 'sadmin';
    }

    /**
     * Vérifie si le tenant de l'utilisateur dispose d'un abonnement actif.
     *
     * Retourne true si l'accès est autorisé, false si le tenant est bloqué.
     */
    public function isActiveForUser(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        // Le SADMIN n'est jamais bloqué par l'abonnement d'un client.
        if ($this->isExempt($user)) {
            return true;
        }

        // La table subscriptions n'a pas la colonne nécessaire (migration non exécutée).
        if (! Schema::hasColumn('subscriptions', 'abonnement_status')) {
            return true;
        }

        $subscription = $this->resolveSubscriptionForUser($user);

        if (! $subscription) {
            return false;
        }

        // Mise à jour automatique du statut si la date d'expiration est dépassée.
        $this->syncExpiredStatus($subscription);

        if ($subscription->isAbonnementActif() && ! $this->isExpired($subscription)) {
            return true;
        }

        if ($subscription->isWithinGracePeriod()) {
            return true;
        }

        return false;
    }

    public function hasSubscription(?User $user): bool
    {
        return (bool) $this->resolveSubscriptionForUser($user);
    }

    /**
     * Résout l'abonnement à considérer pour un utilisateur.
     *
     * La base de toutes les vérifications est le tenant_id, qui est porté par
     * tous les utilisateurs (client, personnel, enseignant, parent, élève)
     * ainsi que par les subscriptions.
     *
     * @return Subscription|null
     */
    public function resolveSubscriptionForUser(?User $user): ?Subscription
    {
        if (! $user || empty($user->tenant_id)) {
            return null;
        }

        return Subscription::query()
            ->where('tenant_id', $user->tenant_id)
            ->whereNotNull('plan_id')
            ->latest('id')
            ->first();
    }

    /**
     * Retourne l'abonnement actif (ou le plus récent) du tenant pour affichage
     * sur la page de blocage (statut, date d'expiration).
     */
    public function subscriptionForUser(?User $user): ?Subscription
    {
        return $this->resolveSubscriptionForUser($user);
    }

    /**
     * Vérifie si l'abonnement est expiré selon sa date de fin.
     */
    public function isExpired(Subscription $subscription): bool
    {
        if (! $subscription->date_fin) {
            return false;
        }

        return $subscription->date_fin->lt(Carbon::today()->startOfDay());
    }

    /**
     * Si la date de fin est dépassée mais que le statut n'est pas encore
     * "expire", on le met à jour automatiquement (réversible, aucune donnée
     * n'est supprimée).
     */
    public function syncExpiredStatus(Subscription $subscription): void
    {
        if ($this->isExpired($subscription) && $subscription->abonnement_status !== Subscription::ABONNEMENT_EXPIRE) {
            $subscription->update([
                'abonnement_status' => Subscription::ABONNEMENT_EXPIRE,
                'statut' => 'expired',
                'status' => 'expired',
            ]);
        }
    }
}
