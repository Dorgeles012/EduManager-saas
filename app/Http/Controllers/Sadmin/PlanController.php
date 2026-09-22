<?php

namespace App\Http\Controllers\Sadmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionType;
use App\Http\Requests\Sadmin\PlanStoreRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class PlanController extends Controller
{
    public function index(): View
    {
        $plans = Plan::query()->orderByDesc('created_at')->get();
        $subscriptionTypes = SubscriptionType::query()->orderBy('created_at', 'desc')->get();

        $subscriptions = Subscription::query()
            ->with([
                'user.etablissement',
                'user.etablissements',
                'client.etablissement',
                'client.etablissements',
                'tenant.etablissements',
                'plan',
                'payments',
            ])
            ->orderByDesc('created_at')
            ->get();

        $pendingSubscriptions = $subscriptions->filter(function ($s) {
            return $s->abonnement_status === Subscription::ABONNEMENT_PAYE
                || $s->payments->contains(fn ($p) => in_array($p->statut ?? $p->status, ['pending', 'en_attente'], true));
        });

        $activeSubscriptions = $subscriptions->filter(function ($s) {
            return $s->abonnement_status === Subscription::ABONNEMENT_ACTIF && ! $s->isExpired();
        });

        $expiredSubscriptions = $subscriptions->filter(function ($s) {
            return $s->isExpired();
        });

        return view('sadmin.abonnement', [
            'plans' => $plans,
            'subscriptions' => $subscriptions,
            'pendingSubscriptions' => $pendingSubscriptions,
            'activeSubscriptions' => $activeSubscriptions,
            'expiredSubscriptions' => $expiredSubscriptions,
            'pendingCount' => $pendingSubscriptions->count(),
            'activeCount' => $activeSubscriptions->count(),
            'plansCount' => $plans->count(),
            'lastUpdatedAt' => $subscriptions->max('updated_at'),
            'filterType' => null,
            'subscriptionTypes' => $subscriptionTypes,
        ]);
    }

    public function store(PlanStoreRequest $request)
    {
        $validated = $request->validated();

        $durationType = $validated['duration_type'] ?? 'monthly';
        $typeDefault = $durationType === 'annual' ? 'Annuel' : 'Mensuel';
        $subscriptionTypeId = $this->resolveSubscriptionTypeId($validated['type'] ?? $typeDefault);

        $features = $this->checkedFeaturesFromRequest($request);
        $description = ! empty($features) ? implode(PHP_EOL, $features) : ($validated['description'] ?? '');

        try {
            Plan::create([
                'nom' => $validated['nom'],
                'description' => $description,
                'prix' => $validated['prix'],
                ...$this->durationAndSchoolPayload($validated),
                'subscription_type_id' => $subscriptionTypeId,
                'statut' => $validated['statut'],
            ]);
        } catch (\Throwable $e) {
            Log::error('PlanController@store failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $validated,
            ]);

            $errorMessage = config('app.debug')
                ? "Impossible de créer le plan : " . $e->getMessage()
                : "Impossible de créer le plan. Veuillez réessayer.";

            return back()
                ->with('error', $errorMessage)
                ->withInput();
        }

        return redirect()->route('sadmin.abonnement')->with('success', 'Plan créé avec succès.');
    }

    public function update(PlanStoreRequest $request, Plan $plan): RedirectResponse
    {
        $validated = $request->validated();

        $durationType = $validated['duration_type'] ?? 'monthly';
        $typeDefault = $durationType === 'annual' ? 'Annuel' : 'Mensuel';
        $subscriptionTypeId = $this->resolveSubscriptionTypeId($validated['type'] ?? $typeDefault);

        $features = $this->checkedFeaturesFromRequest($request);
        $description = ! empty($features) ? implode(PHP_EOL, $features) : ($validated['description'] ?? '');

        try {
            $plan->update([
                'nom' => $validated['nom'],
                'description' => $description,
                'prix' => $validated['prix'],
                ...$this->durationAndSchoolPayload($validated),
                'subscription_type_id' => $subscriptionTypeId,
                'statut' => $validated['statut'],
            ]);
        } catch (\Throwable $e) {
            Log::error('PlanController@update failed', [
                'error' => $e->getMessage(),
                'plan_id' => $plan->id,
                'payload' => $validated,
            ]);

            $errorMessage = config('app.debug')
                ? "Impossible de mettre à jour le plan : " . $e->getMessage()
                : "Impossible de mettre à jour le plan. Veuillez réessayer.";

            return back()
                ->with('error', $errorMessage)
                ->withInput();
        }

        return redirect()->route('sadmin.abonnement')->with('success', 'Plan mis à jour avec succès.');
    }

    /**
     * Retourne l'ID du type d'abonnement correspondant, en le créant si besoin.
     */
    private function resolveSubscriptionTypeId(?string $type): ?int
    {
        if (empty($type)) {
            return null;
        }

        return SubscriptionType::query()
            ->firstOrCreate(
                ['type' => $type],
                ['status' => 'active']
            )
            ->id;
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();

        return redirect()->route('sadmin.abonnement')->with('success', 'Plan supprimé avec succès.');
    }

    private function durationAndSchoolPayload(array $validated): array
    {
        $durationType = $validated['duration_type'] ?? 'monthly';
        $durationMonths = $durationType === 'annual' ? 12 : 1;

        $schoolLimit = $validated['school_limit'] ?? null;
        if ($schoolLimit === 'unlimited' || ! empty($validated['is_unlimited'])) {
            $isUnlimited = true;
            $maxSchools = null;
            $maxEcoles = 999999;
        } elseif (is_numeric($schoolLimit)) {
            $isUnlimited = false;
            $maxSchools = (int) $schoolLimit;
            $maxEcoles = $maxSchools;
        } else {
            $isUnlimited = (bool) ($validated['is_unlimited'] ?? false);
            $maxSchools = $isUnlimited ? null : max(1, (int) ($validated['max_schools'] ?? 1));
            $maxEcoles = $isUnlimited ? 999999 : $maxSchools;
        }

        return [
            'duration_type' => $durationType,
            'duration_value' => 1,
            'duree' => $durationMonths,
            'max_schools' => $maxSchools,
            'max_ecoles' => $maxEcoles,
            'is_unlimited' => $isUnlimited,
        ];
    }

    private function checkedFeaturesFromRequest(PlanStoreRequest $request): array
    {
        $features = $request->input('features', []);

        if (empty($features) && $request->filled('features_json')) {
            $decoded = json_decode($request->input('features_json'), true);
            $features = is_array($decoded) ? $decoded : [];
        }

        return collect($features)
            ->map(fn ($feature) => is_string($feature) ? trim($feature) : '')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
