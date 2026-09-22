<?php

namespace App\Http\Controllers\Sadmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionType;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->query('type');
        $statusFilter = $request->query('status', 'all');

        $subscriptionsQuery = Subscription::query()
            ->with([
                'user.etablissement',
                'user.etablissements',
                'client.etablissement',
                'client.etablissements',
                'tenant.etablissements',
                'plan',
                'payments',
            ])
            ->when($type, function ($q) use ($type) {
                $q->where('type', $type);
            })
            ->orderByDesc('created_at');

        $subscriptions = $subscriptionsQuery->get();

        // Segréger par état
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

        $pendingCount = $pendingSubscriptions->count();
        $activeCount = $activeSubscriptions->count();
        $lastUpdatedAt = $subscriptions->max('updated_at');

        $types = SubscriptionType::query()
            ->orderBy('created_at', 'desc')
            ->get();

        $plans = Plan::query()->orderByDesc('created_at')->get();

        return view('sadmin.abonnement', [
            'subscriptions' => $subscriptions,
            'pendingSubscriptions' => $pendingSubscriptions,
            'activeSubscriptions' => $activeSubscriptions,
            'expiredSubscriptions' => $expiredSubscriptions,
            'pendingCount' => $pendingCount,
            'activeCount' => $activeCount,
            'plansCount' => $plans->count(),
            'lastUpdatedAt' => $lastUpdatedAt,
            'filterType' => $type,
            'statusFilter' => $statusFilter,
            'subscriptionTypes' => $types,
            'plans' => $plans,
        ]);
    }

    /**
     * Valide le paiement d'un abonnement (SADMIN) :
     *  - Active l'abonnement
     *  - Calcule la date d'expiration
     *  - Réactive les utilisateurs du client
     *  - Notifie le client
     */
    public function validate(Request $request, int $id, SubscriptionService $subscriptionService): RedirectResponse
    {
        $subscription = Subscription::with(['user', 'plan'])->findOrFail($id);

        $subscriptionService->activate($subscription, $request->user());

        return back()->with('success', 'Paiement validé avec succès. L\'abonnement est actif et toutes les fonctionnalités du client sont débloquées.');
    }

    /**
     * Refuse le paiement d'un abonnement (SADMIN) :
     *  - Passe le paiement à failed
     *  - Remet l'abonnement en attente
     *  - Notifie le client
     */
    public function reject(Request $request, int $id, SubscriptionService $subscriptionService): RedirectResponse
    {
        $subscription = Subscription::with(['user', 'plan'])->findOrFail($id);

        $motif = $request->input('motif');

        $subscriptionService->reject($subscription, $motif, $request->user());

        return back()->with('success', 'Demande de paiement refusée. Le client a été notifié.');
    }

    public function edit($id)
    {
        $subscription = Subscription::findOrFail($id);

        return response()->json($subscription);
    }

    public function update(Request $request, $id): RedirectResponse
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

    public function destroy($id): RedirectResponse
    {
        $subscription = Subscription::findOrFail($id);
        $subscription->delete();

        return back()->with('success', 'Abonnement supprimé avec succès.');
    }
}
