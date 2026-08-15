<?php

namespace App\Services;

use App\Models\Etablissement;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class SchoolSubscriptionLimitService
{
    public function __construct(
        protected SubscriptionStatusService $subscriptionStatus,
    ) {
    }

    public function schoolsForClient(User $client): Collection
    {
        return $this->schoolsQueryForClient($client)
            ->orderBy('nom')
            ->get();
    }

    public function usedSchoolsCount(User $client): int
    {
        return $this->schoolsQueryForClient($client)->count();
    }

    public function remainingSchools(User $client, ?Plan $plan = null): ?int
    {
        $plan ??= $this->currentPlanForClient($client);

        if (! $plan || $plan->is_unlimited) {
            return null;
        }

        return max(0, (int) $plan->max_schools - $this->usedSchoolsCount($client));
    }

    public function currentPlanForClient(User $client): ?Plan
    {
        return $this->subscriptionStatus
            ->resolveSubscriptionForUser($client)
            ?->loadMissing('plan')
            ?->plan;
    }

    public function currentSubscriptionForClient(User $client): ?Subscription
    {
        return $this->subscriptionStatus->resolveSubscriptionForUser($client);
    }

    public function canCreateSchool(User $client): bool
    {
        $subscription = $this->subscriptionStatus->resolveSubscriptionForUser($client);

        if (! $subscription || ! $subscription->plan) {
            return false;
        }

        $this->subscriptionStatus->syncExpiredStatus($subscription);

        if (! $this->subscriptionStatus->isActiveForUser($client)) {
            return false;
        }

        return $subscription->plan->allowsSchoolCount($this->usedSchoolsCount($client) + 1);
    }

    public function ensureCanCreateSchool(User $client): void
    {
        $plan = $this->currentPlanForClient($client);
        $used = $this->usedSchoolsCount($client);

        if ($this->canCreateSchool($client)) {
            return;
        }

        $allowed = $plan?->schoolsLimitLabel() ?? '0';

        throw ValidationException::withMessages([
            'abonnement' => "Vous avez atteint la limite de votre abonnement. Votre abonnement autorise {$allowed} etablissement(s). Vous utilisez actuellement {$used} etablissement(s). Veuillez changer d'abonnement pour ajouter une autre ecole.",
        ]);
    }

    public function ensurePlanCanBeSelected(User $client, Plan $plan): void
    {
        $used = $this->usedSchoolsCount($client);

        if ($plan->allowsSchoolCount($used)) {
            return;
        }

        throw ValidationException::withMessages([
            'plan_id' => "Impossible de passer a cet abonnement. Vous gerez actuellement {$used} etablissement(s). Ce plan autorise seulement {$plan->schoolsLimitLabel()} etablissement(s). Veuillez d'abord reduire le nombre d'etablissements geres.",
        ]);
    }

    private function schoolsQueryForClient(User $client): Builder
    {
        $tenantId = $client->tenant_id ?? 1;

        return Etablissement::query()
            ->where(function (Builder $query) use ($client, $tenantId) {
                $query->where('tenant_id', $tenantId);

                if (! empty($client->etablissement_id)) {
                    $query->orWhere('id', $client->etablissement_id);
                }
            });
    }
}
