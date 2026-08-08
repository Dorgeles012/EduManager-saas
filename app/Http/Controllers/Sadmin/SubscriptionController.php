<?php

namespace App\Http\Controllers\Sadmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->query('type');

        $subscriptionsQuery = Subscription::query()
            ->with(['user', 'plan', 'payments'])
            ->when($type, function ($q) use ($type) {
                $q->where('type', $type);
            })
            ->orderByDesc('created_at');

        $subscriptions = $subscriptionsQuery->get();

        // Total (sur la même base de filtre que la liste)
        $activeCount = $subscriptions->count();

        // Dernière mise à jour (max updated_at)
        $lastUpdatedAt = $subscriptions->max('updated_at');

        $types = \App\Models\SubscriptionType::query()
            ->orderBy('created_at', 'desc')
            ->get();

        $plans = Plan::query()->orderByDesc('created_at')->get();

        return view('sadmin.abonnement', [
            'subscriptions' => $subscriptions,
            'activeCount' => $activeCount,
            'lastUpdatedAt' => $lastUpdatedAt,
            'filterType' => $type,
            'subscriptionTypes' => $types,
            'plans' => $plans,
        ]);
    }

/**
     * Valide l'abonnement d'un client : passe de "payé" à "actif" et
     * déverrouille automatiquement les fonctionnalités du client.
     *
     * Gère aussi le renouvellement : si l'abonnement est expiré (date_fin
     * dépassée), on prolonge la date_fin à partir d'aujourd'hui selon la
     * durée du plan, ce qui redonne immédiatement l'accès à tout le tenant.
     */
    public function validate(Request $request, int $id, NotificationService $notifications)
    {
        $subscription = Subscription::with(['user', 'plan'])->findOrFail($id);

        $subscription->statut = 'active';
        $subscription->status = 'active';
        $subscription->abonnement_status = Subscription::ABONNEMENT_ACTIF;

        // Renouvellement : si la date de fin est passée, on la prolonge.
        $isDateFinPassee = $subscription->date_fin && $subscription->date_fin->lt(\Carbon\Carbon::today()->startOfDay());
        if ($isDateFinPassee) {
            $dureeMois = $this->planDurationInMonths($subscription->plan);
            $subscription->date_debut = \Carbon\Carbon::today();
            $subscription->date_fin = \Carbon\Carbon::today()->addMonthsNoOverflow($dureeMois);
        }

        $subscription->save();

        // Notifier le client que son abonnement a été validé.
        $client = $subscription->user;
        if ($client instanceof User) {
            $notifications->sendToUsers(
                $request->user(),
                collect([$client]),
                'Abonnement validé',
                'Votre abonnement a été validé. Toutes les fonctionnalités sont maintenant déverrouillées.',
                'subscription'
            );
        }

        return back()->with('success', 'Abonnement validé. Les fonctionnalités du client sont maintenant actives.');
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

    /**
     * Calcule la durée (en mois) de l'abonnement à partir du plan associé.
     */
    private function planDurationInMonths($plan): int
    {
        if (! $plan) {
            return 12;
        }

        if (isset($plan->duree) && is_numeric($plan->duree)) {
            return max(1, (int) $plan->duree);
        }

        if (isset($plan->duration) && is_numeric($plan->duration)) {
            return max(1, (int) $plan->duration);
        }

        return 12;
    }
}

