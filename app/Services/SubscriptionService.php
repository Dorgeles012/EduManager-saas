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
        protected ConnectionInterface $connection,
        protected SchoolSubscriptionLimitService $schoolLimits,
        protected NotificationService $notificationService
    ) {
    }

    /**
     * Enregistre la demande de paiement du client :
     *  - Crée l'abonnement en statut "paye" (en attente de validation).
     *  - Crée l'enregistrement de paiement avec le statut "pending".
     *  - Envoie une notification immédiate à tous les administrateurs SADMIN.
     */
    public function subscribe(
        User $user,
        int $planId,
        string $methodePaiement,
        string $referenceTransaction
    ): array {
        $plan = Plan::query()->where('id', $planId)->where('statut', 'active')->first();
        if (! $plan) {
            throw new ModelNotFoundException(sprintf('Plan %d non trouvé ou inactif.', $planId));
        }

        $this->schoolLimits->ensurePlanCanBeSelected($user, $plan);

        $referenceTransaction = trim($referenceTransaction);
        $methodePaiement = trim($methodePaiement);

        // Double-sécurité : référence unique côté logique
        $referenceColumn = $this->getPaymentReferenceColumn();
        $exists = Payment::query()->where($referenceColumn, $referenceTransaction)->exists();
        if ($exists) {
            throw new \InvalidArgumentException('La référence de transaction doit être unique.');
        }

        $dateDebut = Carbon::today();
        $dureeMois = $plan->durationInMonths();
        $dateFin = (clone $dateDebut)->addMonthsNoOverflow($dureeMois)->subDay();

        return DB::transaction(function () use ($user, $plan, $methodePaiement, $referenceTransaction, $dateDebut, $dateFin, $referenceColumn) {
            // 1) Créer l'enregistrement Subscription
            $subscriptionPayload = [
                'user_id' => $user->id,
                'client_id' => $user->id,
                'plan_id' => $plan->id,
                'tenant_id' => ($user->tenant_id ?? 1),
                'date_debut' => $dateDebut,
                'date_fin' => $dateFin,
                'statut' => 'active',
                'status' => 'active',
                'abonnement_status' => Subscription::ABONNEMENT_PAYE,
                'name' => $plan->nom ?? 'Abonnement',
                'type' => $plan->durationLabel() ?? 'Mensuel',
                'amount' => (int) $plan->prix,
                'price' => (int) $plan->prix,
                'duration' => $plan->durationInMonths(),
            ];

            $subscription = Subscription::create($subscriptionPayload);

            // 2) Créer l'enregistrement Payment avec statut 'pending'
            $paymentPayload = [
                'tenant_id' => ($user->tenant_id ?? 1),
                'subscription_id' => $subscription->id,
                'montant' => (int) $plan->prix,
                'amount' => (int) $plan->prix,
                'methode_paiement' => $methodePaiement,
                'payment_method' => $methodePaiement,
                'date_paiement' => Carbon::today()->toDateString(),
                'status' => 'pending',
                'statut' => 'pending',
                $referenceColumn => $referenceTransaction,
            ];

            $payment = Payment::query()->create($paymentPayload);

            // 3) Créer une notification pour les Super Administrateurs
            $sadmins = User::query()->whereRaw('LOWER(role) = ?', ['sadmin'])->get();
            if ($sadmins->isNotEmpty()) {
                $clientName = trim(($user->nom ?? '') . ' ' . ($user->prenom ?? ''));
                $montantFmt = number_format((int) $plan->prix, 0, ',', ' ');
                $this->notificationService->sendToUsers(
                    $user,
                    $sadmins,
                    'Nouveau paiement en attente',
                    "Le client {$clientName} a effectué un paiement de {$montantFmt} FCFA pour la formule « {$plan->nom} » (Réf: {$referenceTransaction}). Veuillez valider la demande.",
                    'subscription',
                    'high'
                );
            }

            return [
                'message' => 'Paiement enregistré. Votre abonnement sera activé après validation par l\'administrateur.',
                'subscription' => $subscription,
                'payment' => $payment,
            ];
        });
    }

    /**
     * Valide et active un abonnement (SADMIN) :
     *  - Transaction DB atomique.
     *  - Passe le paiement à 'paid'.
     *  - Passe l'abonnement à 'actif'.
     *  - Calcule la date de début et d'expiration selon le plan.
     *  - Réinitialise la période de grâce de 7 jours.
     *  - Débloque / réactive tous les utilisateurs du tenant (statut = 'actif').
     *  - Notifie le client que ses fonctionnalités sont débloquées.
     */
    public function activate(Subscription $subscription, ?User $admin = null): Subscription
    {
        return DB::transaction(function () use ($subscription, $admin) {
            $subscription->loadMissing(['plan', 'user']);
            $plan = $subscription->plan ?? Plan::find($subscription->plan_id);
            $client = $subscription->user;
            $tenantId = $subscription->tenant_id ?? $client?->tenant_id;

            $today = Carbon::today();

            // Si expiré ou sans date, repart d'aujourd'hui. Si encore actif, prolonge depuis date_fin.
            if (! $subscription->date_fin || $subscription->date_fin->startOfDay()->lte($today->startOfDay())) {
                $newDateDebut = $today->copy();
            } else {
                $newDateDebut = $subscription->date_fin->copy()->addDay();
            }

            $dureeMois = $plan ? $this->getPlanDurationInMonthsForPlan($plan) : 12;
            $newDateFin = $newDateDebut->copy()->addMonthsNoOverflow($dureeMois)->subDay();

            // 1. Mettre à jour l'abonnement
            $subscription->update([
                'abonnement_status' => Subscription::ABONNEMENT_ACTIF,
                'statut' => 'active',
                'status' => 'active',
                'date_debut' => $newDateDebut->toDateString(),
                'date_fin' => $newDateFin->toDateString(),
            ]);

            // 2. Mettre à jour les paiements associés (pending -> paid)
            Payment::query()
                ->where('subscription_id', $subscription->id)
                ->where(function ($q) {
                    $q->where('statut', 'pending')
                      ->orWhere('status', 'pending');
                })
                ->update([
                    'statut' => 'paid',
                    'status' => 'paid',
                ]);

            // 3. Réactiver tous les utilisateurs créés par le client pour ce tenant
            if ($tenantId) {
                User::query()
                    ->where('tenant_id', $tenantId)
                    ->where('statut', 'bloqué')
                    ->update(['statut' => 'actif']);
            }

            // 4. Notifier le client que son abonnement a été validé et activé
            if ($client instanceof User) {
                $dateFinFmt = $newDateFin->format('d/m/Y');
                $planNom = $plan?->nom ?? 'votre formule';
                $this->notificationService->sendToUsers(
                    $admin,
                    collect([$client]),
                    'Abonnement validé et activé',
                    "Votre paiement pour le plan « {$planNom} » a été validé avec succès. Votre abonnement est actif jusqu'au {$dateFinFmt}. Toutes les fonctionnalités de votre établissement sont maintenant débloquées.",
                    'subscription',
                    'high'
                );
            }

            $subscription->refresh();

            return $subscription;
        });
    }

    /**
     * Refuse un paiement d'abonnement (SADMIN) :
     *  - Transaction DB atomique.
     *  - Passe le paiement à 'failed'.
     *  - Remet l'abonnement en statut 'en_attente'.
     *  - Notifie le client du refus avec le motif éventuel.
     */
    public function reject(Subscription $subscription, ?string $motif = null, ?User $admin = null): Subscription
    {
        return DB::transaction(function () use ($subscription, $motif, $admin) {
            $subscription->loadMissing(['plan', 'user']);
            $client = $subscription->user;
            $plan = $subscription->plan;

            // 1. Remettre l'abonnement en attente
            $subscription->update([
                'abonnement_status' => Subscription::ABONNEMENT_EN_ATTENTE,
            ]);

            // 2. Marquer les paiements pending comme 'failed'
            Payment::query()
                ->where('subscription_id', $subscription->id)
                ->where(function ($q) {
                    $q->where('statut', 'pending')
                      ->orWhere('status', 'pending');
                })
                ->update([
                    'statut' => 'failed',
                    'status' => 'failed',
                ]);

            // 3. Notifier le client
            if ($client instanceof User) {
                $planNom = $plan?->nom ?? 'Abonnement';
                $reason = $motif ? " Motif du refus : {$motif}." : '';
                $this->notificationService->sendToUsers(
                    $admin,
                    collect([$client]),
                    'Demande de paiement refusée',
                    "Votre demande de paiement pour le plan « {$planNom} » a été refusée par l'administrateur.{$reason} Veuillez vérifier vos informations de paiement ou contacter l'administration.",
                    'subscription',
                    'high'
                );
            }

            $subscription->refresh();

            return $subscription;
        });
    }

    private function getPaymentReferenceColumn(): string
    {
        if (Schema::hasColumn('payments', 'reference_paiement')) {
            return 'reference_paiement';
        }

        return 'reference_transaction';
    }

    private function getPlanDurationInMonths(Plan $plan): int
    {
        if (isset($plan->duree) && is_numeric($plan->duree)) {
            return (int) $plan->duree;
        }

        if (isset($plan->duration) && is_numeric($plan->duration)) {
            return (int) $plan->duration;
        }

        return 12;
    }

    private function getPlanDurationInMonthsForPlan(Plan $plan): int
    {
        if (method_exists($plan, 'durationInMonths')) {
            return $plan->durationInMonths();
        }

        return $this->getPlanDurationInMonths($plan);
    }
}
