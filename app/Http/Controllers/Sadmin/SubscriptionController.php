<?php

namespace App\Http\Controllers\Sadmin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->query('type');

        $subscriptionsQuery = Subscription::query()
            ->where('status', 'active')
            ->when($type, function ($q) use ($type) {
                $q->where('type', $type);
            })
            ->orderByDesc('created_at');

        $subscriptions = $subscriptionsQuery->get();

        // Total exact (sur la même base de filtre que la liste)
        $activeCount = $subscriptionsQuery->count();

        // Dernière mise à jour (max updated_at) parmi les abonnements actifs
        $lastUpdatedAt = Subscription::query()
            ->where('status', 'active')
            ->when($type, function ($q) use ($type) {
                $q->where('type', $type);
            })
            ->max('updated_at');



        $types = \App\Models\SubscriptionType::query()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('sadmin.abonnement', [
            'subscriptions' => $subscriptions,
            'activeCount' => $activeCount,
            'lastUpdatedAt' => $lastUpdatedAt,
            'filterType' => $type,
            'subscriptionTypes' => $types,
        ]);

    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric'],
            'duration' => ['required', 'numeric'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);





        $status = $validated['status'] === 'active' ? 'active' : 'inactive';

        $tenantId = $request->user()->tenant_id ?? 1;

        // Anti-duplication : même nom + même type (et on ne crée qu'un abonnement actif par combinaison)
        $q = Subscription::query()
            ->where('name', $validated['name'])
            ->where('type', $validated['type'])
            ->where('status', 'active');

        // Si la colonne existe dans la table, on segmente par tenant.
        if (\Schema::hasColumn('subscriptions', 'tenant_id')) {
            $q->where('tenant_id', $tenantId);
        }

        $existing = $q->first();

        if ($existing) {
            return back()->with('error', "Un abonnement actif existe déjà pour ce nom et ce type.");
        }

        Subscription::create([
            // Certains environnements n'ont pas de tenant_id dans la table `subscriptions`.
            // On insère tenant_id uniquement si la colonne existe.
            ...(\Schema::hasColumn('subscriptions', 'tenant_id') ? ['tenant_id' => $tenantId] : []),

            'name' => $validated['name'],
            'type' => $validated['type'],
            'price' => (int) $validated['price'],
            'duration' => (int) $validated['duration'],
            'description' => $validated['description'] ?? null,
            'status' => $status,
        ]);

        return back()->with('success', 'Abonnement créé avec succès.');

    }

    public function edit($id)
    {
        $subscription = Subscription::findOrFail($id);

        return response()->json($subscription);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],


            'type' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric'],
            'duration' => ['required', 'numeric'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $subscription = Subscription::findOrFail($id);

        $subscription->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'price' => (int) $validated['price'],
            'duration' => (int) $validated['duration'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] === 'active' ? 'active' : 'inactive',
        ]);


        return back()->with('success', 'Abonnement mis à jour avec succès.');
    }

    public function destroy($id)
    {
        $subscription = Subscription::findOrFail($id);
        $subscription->delete();

        return back()->with('success', 'Abonnement supprimé avec succès.');
    }
}

