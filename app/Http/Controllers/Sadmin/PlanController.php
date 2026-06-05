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

        return view('sadmin.abonnement', [
            'plans' => $plans,
            'subscriptionTypes' => $subscriptionTypes,
        ]);
    }

    public function store(PlanStoreRequest $request)
    {
        $validated = $request->validated();

        // Le champ 'type' fait référence à subscription_types.type (ex: mensuel, annuel...).
        // En base, la table plans référence désormais subscription_types via subscription_type_id.
        $subscriptionTypeId = \App\Models\SubscriptionType::query()
            ->where('type', $validated['type'])
            ->value('id');

        $features = $this->checkedFeaturesFromRequest($request);
        // Une ligne par fonctionnalité cochée dans plans.description.
        $description = implode(PHP_EOL, $features);

        Plan::create([
            'nom' => $validated['nom'],
            'description' => $description,
            'prix' => $validated['prix'],
            'subscription_type_id' => $subscriptionTypeId,
            'statut' => $validated['statut'],
        ]);


        return back()->with('success', 'Plan créé avec succès.');
    }

    public function update(PlanStoreRequest $request, Plan $plan): RedirectResponse
    {
        $validated = $request->validated();

        $subscriptionTypeId = \App\Models\SubscriptionType::query()
            ->where('type', $validated['type'])
            ->value('id');

        $features = $this->checkedFeaturesFromRequest($request);
        $description = implode(PHP_EOL, $features);

        $plan->update([
            'nom' => $validated['nom'],
            'description' => $description,
            'prix' => $validated['prix'],
            'subscription_type_id' => $subscriptionTypeId,
            'statut' => $validated['statut'],
        ]);


        return back()->with('success', 'Plan mis à jour avec succès.');
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




