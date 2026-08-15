<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\SchoolSubscriptionLimitService;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AbonnementController extends Controller
{
    public function index(Request $request, SchoolSubscriptionLimitService $schoolLimits): View
    {
        $type = $request->query('type');

        $planQuery = Plan::query()->where('statut', 'active');

        if ($type) {
            $planQuery->where('subscription_type_id', $type);
        }

        $plans = $planQuery->orderBy('id')->get();

        $subscriptions = Subscription::query()
            ->with(['plan', 'payment', 'payments'])
            ->when(Schema::hasColumn('subscriptions', 'user_id'), function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->when(! Schema::hasColumn('subscriptions', 'user_id') && Schema::hasColumn('subscriptions', 'tenant_id'), function ($query) {
                $query->where('tenant_id', auth()->user()?->tenant_id);
            })
            ->latest()
            ->get();

        $currentSubscription = $schoolLimits->currentSubscriptionForClient($request->user());
        $currentSubscription?->loadMissing(['plan', 'payments']);

        return view('client.abonnements', [
            'plans' => $plans,
            'featuredPlan' => $plans->first(),
            'subscriptions' => $subscriptions,
            'currentSubscription' => $currentSubscription,
            'usedSchools' => $schoolLimits->usedSchoolsCount($request->user()),
        ]);
    }

    public function create(Request $request, SchoolSubscriptionLimitService $schoolLimits): View
    {
        return $this->index($request, $schoolLimits);
    }

    public function store(Request $request, SubscriptionService $subscriptionService): RedirectResponse
    {
        $validated = $request->validate([
            'plan_id' => ['required', 'integer', 'exists:plans,id'],
            'payment_method' => ['required', 'string', 'max:100'],
        ]);

        try {
            $subscriptionService->subscribe(
                user: $request->user(),
                planId: (int) $validated['plan_id'],
                methodePaiement: trim($validated['payment_method']),
                referenceTransaction: 'PAY-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6))
            );
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()
                ->route('client.abonnements.index')
                ->with('error', $exception->getMessage() ?: 'Impossible de confirmer le paiement. Aucune donnee n a ete enregistree.')
                ->withInput();
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
}
