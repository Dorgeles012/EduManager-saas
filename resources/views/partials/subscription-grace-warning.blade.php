@php
    $user = auth()->user();
    $subscription = app(\App\Services\SubscriptionStatusService::class)->subscriptionForUser($user);
@endphp

@if($subscription?->isWithinGracePeriod())
    <div class="mb-6 rounded-3xl border border-amber-200 bg-amber-50 p-6 text-amber-900 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="font-semibold text-lg">⚠️ Votre abonnement a expiré.</p>
                <p class="mt-1 text-sm text-amber-900/80">Vous bénéficiez actuellement d'une période de grâce de {{ \App\Models\Subscription::GRACE_DAYS }} jours.</p>
                <p class="mt-1 text-sm text-amber-900/80">Il vous reste <strong>{{ $subscription->remainingGraceDays() }} jour{{ $subscription->remainingGraceDays() > 1 ? 's' : '' }}</strong> avant le blocage de votre compte.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('client.abonnement.index') }}" class="inline-flex items-center justify-center rounded-full bg-amber-900 px-5 py-3 text-sm font-semibold text-white hover:bg-amber-800 transition">Renouveler maintenant</a>
            </div>
        </div>
    </div>
@endif
