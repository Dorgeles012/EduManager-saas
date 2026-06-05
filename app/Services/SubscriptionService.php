<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


class SubscriptionService
{
    public function __construct(
        protected ConnectionInterface $connection
    ) {
    }

    public function subscribe(
        User $user,
        int $planId,
        string $methodePaiement,
        string $referenceTransaction
    ): array {
        $plan = Plan::query()->where('id', $planId)->first();
        if (! $plan) {
            throw new ModelNotFoundException(sprintf('Plan %d not found.', $planId));
        }

        $referenceTransaction = trim($referenceTransaction);
        $methodePaiement = trim($methodePaiement);

        // Double-sécurité: référence unique côté logique.
        // (La contrainte DB unique n'est pas forcément en place sur tous les environnements.)
        $referenceColumn = $this->getPaymentReferenceColumn();
        $exists = Payment::query()->where($referenceColumn, $referenceTransaction)->exists();
        if ($exists) {
            throw new \InvalidArgumentException('reference_transaction must be unique.');
        }

        $dateDebut = Carbon::today();
        $dureeMois = $this->getPlanDurationInMonths($plan);
        $dateFin = (clone $dateDebut)->addMonthsNoOverflow($dureeMois);

        // Architecture prête pour plus tard : on centralise la création paiement
        // et on pourra brancher un "PaymentProvider" (Orange/MTN/Wave/Stripe/PayPal)
        // sans casser la création subscription.
        return DB::transaction(function () use ($user, $plan, $methodePaiement, $referenceTransaction, $dateDebut, $dateFin, $referenceColumn) {
            // 1) Create subscription
            $subscriptionPayload = [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'date_debut' => $dateDebut,
                'date_fin' => $dateFin,
                'statut' => 'active',
            ];

            // Si la table contient une colonne "name" (ancien modèle historique),
            // on la renseigne pour maximiser la compat.
            if (\Schema::hasColumn('subscriptions', 'name')) {
                $subscriptionPayload['name'] = $plan->nom ?? ($plan->name ?? null);
            }

            if (\Schema::hasColumn('subscriptions', 'tenant_id')) {
                $subscriptionPayload['tenant_id'] = ($user->tenant_id ?? 1);
            }


            $subscription = Subscription::create($subscriptionPayload);

            // 2) Create payment
            // note: pour l'instant on crée directement en base avec statut paid/failed.
            $paymentPayload = [
                'subscription_id' => $subscription->id,
                'montant' => (int) $plan->prix,
                'methode_paiement' => $methodePaiement,
                'date_paiement' => Carbon::today()->toDateString(),
                'statut' => 'paid',
                $referenceColumn => $referenceTransaction,
            ];

            if (\Schema::hasColumn('payments', 'tenant_id')) {
                $paymentPayload['tenant_id'] = ($user->tenant_id ?? 1);
            }

            $payment = Payment::query()->create($paymentPayload);


            return [
                'message' => 'Subscription créée avec succès.',
                'subscription' => $subscription,
                'payment' => $payment,
            ];
        });
    }

    private function getPaymentReferenceColumn(): string
    {
        // Migrations actuelles: reference_paiement
        if (\Schema::hasColumn('payments', 'reference_paiement')) {
            return 'reference_paiement';
        }

        // fallback éventuel
        return 'reference_transaction';
    }

    private function getPlanDurationInMonths(Plan $plan): int
    {
        // Plusieurs colonnes peuvent exister selon migrations/normalisation.
        if (isset($plan->duree) && is_numeric($plan->duree)) {
            return (int) $plan->duree;
        }

        if (isset($plan->duration) && is_numeric($plan->duration)) {
            return (int) $plan->duration;
        }

        if (isset($plan->subscription_type_id) && $plan->subscription_type_id) {
            // On ne peut pas déterminer ici sans charger le type.
            // Donc on force un fallback safe.
        }

        return 12;
    }
}

