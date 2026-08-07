<?php

namespace App\Http\Controllers\Sadmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Http\Requests\Sadmin\PlanStoreRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PlanController extends Controller
{
public function index(): View
    {
        $plans = Plan::query()->orderByDesc('created_at')->get();
        $subscriptionTypes = \App\Models\SubscriptionType::query()->orderBy('created_at', 'desc')->get();

        $subscriptions = \App\Models\Subscription::query()
            ->with(['user', 'plan', 'payments'])
            ->orderByDesc('created_at')
            ->get();

        return view('sadmin.abonnement', [
            'plans' => $plans,
            'subscriptions' => $subscriptions,
            'activeCount' => $subscriptions->count(),
            'lastUpdatedAt' => $subscriptions->max('updated_at'),
            'filterType' => null,
            'subscriptionTypes' => $subscriptionTypes,
        ]);
    }

    public function store(PlanStoreRequest $request)
    {
        $validated = $request->validated();

        // Le champ 'type' fait référence à subscription_types.type (ex: mensuel, annuel...).
        // En base, la table plans référence désormais subscription_types via subscription_type_id.
        // On crée automatiquement le type s'il n'existe pas encore pour ne jamais bloquer la création.
        $subscriptionTypeId = $this->resolveSubscriptionTypeId($validated['type'] ?? null);

        $features = $this->checkedFeaturesFromRequest($request);
        // Une ligne par fonctionnalité cochée dans plans.description.
        $description = implode(PHP_EOL, $features);

        try {
            Plan::create([
                'nom' => $validated['nom'],
                'description' => $description,
                'prix' => $validated['prix'],
                'duree' => $validated['duree'] ?? 12,
                'subscription_type_id' => $subscriptionTypeId,
                'statut' => $validated['statut'],
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('PlanController@store failed', [
                'error' => $e->getMessage(),
                'payload' => $validated,
            ]);

            return back()
                ->with('error', 'Impossible de créer le plan. Veuillez réessayer.')
                ->withInput();
        }

        return back()->with('success', 'Plan créé avec succès.');
    }

    public function update(PlanStoreRequest $request, Plan $plan): RedirectResponse
    {
        $validated = $request->validated();

        $subscriptionTypeId = $this->resolveSubscriptionTypeId($validated['type'] ?? null);

        $features = $this->checkedFeaturesFromRequest($request);
        $description = implode(PHP_EOL, $features);

        try {
            $plan->update([
                'nom' => $validated['nom'],
                'description' => $description,
                'prix' => $validated['prix'],
                'duree' => $validated['duree'] ?? 12,
                'subscription_type_id' => $subscriptionTypeId,
                'statut' => $validated['statut'],
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('PlanController@update failed', [
                'error' => $e->getMessage(),
                'plan_id' => $plan->id,
                'payload' => $validated,
            ]);

            return back()
                ->with('error', 'Impossible de mettre à jour le plan. Veuillez réessayer.')
                ->withInput();
        }

        return back()->with('success', 'Plan mis à jour avec succès.');
    }

    /**
     * Retourne l'ID du type d'abonnement correspondant, en le créant si besoin.
     */
    private function resolveSubscriptionTypeId(?string $type): ?int
    {
        if (empty($type)) {
            return null;
        }

        return \App\Models\SubscriptionType::query()
            ->firstOrCreate(
                ['type' => $type],
                ['status' => 'active']
            )
            ->id;
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();

        return back()->with('success', 'Plan supprimé avec succès.');
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




