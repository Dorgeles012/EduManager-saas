<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AbonnementController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->query('type');

        $planQuery = Plan::query()->where('statut', 'active');

        if ($type) {
            $planQuery->where('subscription_type_id', $type);
        }

        $plans = $planQuery->orderBy('id')->get();

        $subscriptions = Subscription::query()
            ->with(['plan', 'payment'])
            ->when(Schema::hasColumn('subscriptions', 'user_id'), function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->when(! Schema::hasColumn('subscriptions', 'user_id') && Schema::hasColumn('subscriptions', 'tenant_id'), function ($query) {
                $query->where('tenant_id', auth()->user()?->tenant_id);
            })
            ->latest()
            ->get();

        return view('client.abonnements', [
            'plans' => $plans,
            'featuredPlan' => $plans->first(),
            'subscriptions' => $subscriptions,
        ]);
    }

    public function create(Request $request): View
    {
        return $this->index($request);
    }

    public function store(Request $request, NotificationService $notifications): RedirectResponse
    {
        $validated = $request->validate([
            'plan_id' => ['required', 'integer', 'exists:plans,id'],
            'payment_method' => ['required', 'string', 'max:100'],
        ]);

        $plan = Plan::query()
            ->where('statut', 'active')
            ->findOrFail($validated['plan_id']);

        /** @var User $user */
        $user = $request->user();

        try {
            DB::transaction(function () use ($user, $plan, $validated, $notifications) {
                $dateDebut = Carbon::today();
                $duration = $this->planDuration($plan);
                $dateFin = (clone $dateDebut)->addMonthsNoOverflow($duration);

                $subscription = Subscription::query()->create($this->subscriptionPayload(
                    user: $user,
                    plan: $plan,
                    amount: (int) $plan->prix,
                    dateDebut: $dateDebut,
                    dateFin: $dateFin,
                    duration: $duration
                ));

                Payment::query()->create($this->paymentPayload(
                    user: $user,
                    subscription: $subscription,
                    amount: (int) $plan->prix,
                    paymentMethod: trim($validated['payment_method'])
                ));

                $notifications->sendToUsers($user, collect([$user]), 'Abonnement payé', 'Votre paiement pour le plan '.$plan->nom.' a été confirmé. Votre abonnement est maintenant actif.', 'subscription');
            });
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()
                ->route('client.abonnements.index')
                ->with('error', 'Impossible de confirmer le paiement. Aucune donnee n a ete enregistree.');
        }

        return redirect()
            ->route('client.abonnements.index')
            ->with('success', 'Paiement confirme. L abonnement est maintenant actif.');
    }

    public function show($abonnement): RedirectResponse
    {
        return redirect()->route('client.abonnement.index');
    }

    public function edit($abonnement): RedirectResponse
    {
        return redirect()->route('client.abonnement.index');
    }

    public function update(Request $request, $abonnement): RedirectResponse
    {
        return redirect()->route('client.abonnement.index');
    }

    public function destroy($abonnement): RedirectResponse
    {
        return redirect()->route('client.abonnement.index');
    }

    private function subscriptionPayload(User $user, Plan $plan, int $amount, Carbon $dateDebut, Carbon $dateFin, int $duration): array
    {
        $payload = [];

        if (Schema::hasColumn('subscriptions', 'tenant_id')) {
            $payload['tenant_id'] = $user->tenant_id ?? 1;
        }

        if (Schema::hasColumn('subscriptions', 'client_id')) {
            $payload['client_id'] = $user->id;
        }

        if (Schema::hasColumn('subscriptions', 'user_id')) {
            $payload['user_id'] = $user->id;
        }

        if (Schema::hasColumn('subscriptions', 'plan_id')) {
            $payload['plan_id'] = $plan->id;
        }

        if (Schema::hasColumn('subscriptions', 'amount')) {
            $payload['amount'] = $amount;
        }

        if (Schema::hasColumn('subscriptions', 'status')) {
            $payload['status'] = 'active';
        }

        if (Schema::hasColumn('subscriptions', 'statut')) {
            $payload['statut'] = 'active';
        }

        if (Schema::hasColumn('subscriptions', 'date_debut')) {
            $payload['date_debut'] = $dateDebut->toDateString();
        }

        if (Schema::hasColumn('subscriptions', 'date_fin')) {
            $payload['date_fin'] = $dateFin->toDateString();
        }

        if (Schema::hasColumn('subscriptions', 'name')) {
            $payload['name'] = $plan->nom;
        }

        if (Schema::hasColumn('subscriptions', 'type')) {
            $payload['type'] = 'client-' . $user->id . '-' . now()->format('YmdHis');
        }

        if (Schema::hasColumn('subscriptions', 'price')) {
            $payload['price'] = $amount;
        }

        if (Schema::hasColumn('subscriptions', 'duration')) {
            $payload['duration'] = $duration;
        }

        return $payload;
    }

    private function paymentPayload(User $user, Subscription $subscription, int $amount, string $paymentMethod): array
    {
        $payload = [
            'subscription_id' => $subscription->id,
        ];

        if (Schema::hasColumn('payments', 'tenant_id')) {
            $payload['tenant_id'] = $user->tenant_id ?? 1;
        }

        if (Schema::hasColumn('payments', 'amount')) {
            $payload['amount'] = $amount;
        }

        if (Schema::hasColumn('payments', 'montant')) {
            $payload['montant'] = $amount;
        }

        if (Schema::hasColumn('payments', 'payment_method')) {
            $payload['payment_method'] = $paymentMethod;
        }

        if (Schema::hasColumn('payments', 'methode_paiement')) {
            $payload['methode_paiement'] = $paymentMethod;
        }

        if (Schema::hasColumn('payments', 'reference_paiement')) {
            $payload['reference_paiement'] = 'PAY-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6));
        }

        if (Schema::hasColumn('payments', 'date_paiement')) {
            $payload['date_paiement'] = Carbon::today()->toDateString();
        }

        if (Schema::hasColumn('payments', 'status')) {
            $payload['status'] = 'paid';
        }

        if (Schema::hasColumn('payments', 'statut')) {
            $payload['statut'] = 'paid';
        }

        return $payload;
    }

    private function planDuration(Plan $plan): int
    {
        if (isset($plan->duree) && is_numeric($plan->duree)) {
            return max(1, (int) $plan->duree);
        }

        if (isset($plan->duration) && is_numeric($plan->duration)) {
            return max(1, (int) $plan->duration);
        }

        return 12;
    }
}
