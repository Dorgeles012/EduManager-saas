@extends('client.layouts.app')

@section('title', 'EduManager - Abonnements')

@section('content')
<div class="mb-8">
    <h2 class="font-headline-lg text-headline-lg text-primary mb-2">Abonnements</h2>
    <p class="font-body-lg text-body-lg text-on-surface-variant">Choisissez une formule, confirmez le paiement, puis retrouvez votre abonnement actif.</p>
</div>

@if ($errors->any())
    <div class="mb-6 rounded-lg border border-alert-red/30 bg-alert-red/10 px-4 py-3 text-alert-red">
        <p class="font-label-md mb-2">Veuillez corriger les erreurs suivantes :</p>
        <ul class="list-disc pl-5 text-body-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
    <div class="bg-white p-7 rounded-xl ambient-shadow border border-outline-variant flex items-center justify-between">
        <div>
            <p class="font-label-sm text-label-sm text-on-surface-variant mb-1 uppercase tracking-widest">Formules</p>
            <h3 class="font-headline-lg text-headline-lg text-on-surface">{{ $plans->count() }}</h3>
        </div>
        <span class="material-symbols-outlined text-primary text-3xl">sell</span>
    </div>
    <div class="bg-white p-7 rounded-xl ambient-shadow border border-outline-variant flex items-center justify-between">
        <div>
            <p class="font-label-sm text-label-sm text-on-surface-variant mb-1 uppercase tracking-widest">Actifs</p>
            <h3 class="font-headline-lg text-headline-lg text-on-surface">{{ $subscriptions->count() }}</h3>
        </div>
        <span class="material-symbols-outlined text-success-green text-3xl">verified</span>
    </div>
</div>

<section class="mb-10">
    @php
        $activeSub = $currentSubscription ?? null;
        $subStatus = $activeSub?->abonnement_status;
    @endphp

    {{-- Bannière d'état contextuelle --}}
    @if(! $activeSub)
        <div class="mb-8 rounded-2xl border border-primary/20 bg-primary/5 p-6 shadow-sm">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="font-semibold text-lg text-primary">👋 Bienvenue ! Vous n'avez pas encore d'abonnement.</p>
                    <p class="mt-1 text-sm text-primary/70">Choisissez une formule ci-dessous pour commencer à utiliser EduManager.</p>
                </div>
            </div>
        </div>
    @elseif($subStatus === 'paye')
        <div class="mb-8 rounded-2xl border border-blue-200 bg-blue-50 p-6 shadow-sm">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="font-semibold text-lg text-blue-800">⏳ Paiement reçu — En attente de validation</p>
                    <p class="mt-1 text-sm text-blue-700">Votre paiement a bien été enregistré. L'administrateur doit valider votre abonnement pour activer toutes les fonctionnalités.</p>
                    @if($activeSub->plan)
                        <p class="mt-1 text-xs text-blue-600 font-medium">
                            Plan : {{ $activeSub->plan->nom }} —
                            {{ number_format((int) ($activeSub->price ?? $activeSub->amount ?? 0), 0, ',', ' ') }} FCFA
                        </p>
                    @endif
                </div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-4 py-2 text-sm font-semibold text-blue-700">
                    <span class="h-2 w-2 rounded-full bg-blue-500 animate-pulse"></span>En attente
                </span>
            </div>
        </div>
    @elseif($subStatus === 'en_attente')
        <div class="mb-8 rounded-2xl border border-amber-200 bg-amber-50 p-6 shadow-sm">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="font-semibold text-lg text-amber-800">💳 Abonnement en attente de paiement</p>
                    <p class="mt-1 text-sm text-amber-700">Choisissez une formule ci-dessous et confirmez votre paiement pour activer votre accès.</p>
                </div>
            </div>
        </div>
    @elseif($activeSub?->isWithinGracePeriod())
        <div class="mb-8 rounded-2xl border border-amber-300 bg-amber-50 p-6 shadow-sm">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="font-semibold text-lg text-amber-900">⚠️ Abonnement expiré — Période de grâce</p>
                    <p class="mt-1 text-sm text-amber-800">
                        Votre abonnement a expiré. Il vous reste
                        <strong>{{ $activeSub->remainingGraceDays() }} jour{{ $activeSub->remainingGraceDays() > 1 ? 's' : '' }}</strong>
                        avant le blocage de votre compte.
                    </p>
                </div>
                <a href="#plans" class="inline-flex items-center justify-center rounded-full bg-amber-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-800 transition">
                    Renouveler maintenant
                </a>
            </div>
        </div>
    @endif

    <h4 class="font-headline-md text-headline-md text-on-surface mb-5 flex items-center gap-2" id="plans">
        <span class="material-symbols-outlined text-primary">workspace_premium</span>
        Formules disponibles
    </h4>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-5xl mx-auto">
        @forelse ($plans as $plan)
            @php
                $features = collect(preg_split("/\r?\n/", (string) $plan->description))
                    ->map(fn ($feature) => trim($feature))
                    ->filter()
                    ->values();
            @endphp

            <article class="bg-white rounded-xl premium-shadow border border-outline-variant overflow-hidden flex flex-col min-h-[430px] max-w-md mx-auto w-full">
                <div class="bg-primary-container p-6">
                    <p class="font-label-sm text-label-sm uppercase tracking-widest text-on-primary">Plan</p>
                    <h5 class="font-headline-md text-headline-md mt-2 text-on-primary">{{ $plan->nom }}</h5>
                </div>

                <div class="p-6 flex flex-col flex-1">
                    <div class="mb-6 flex justify-between items-baseline">
                        <div>
                            <span class="font-headline-md text-headline-md text-primary">
                                {{ number_format((int) $plan->prix, 0, ',', ' ') }} FCFA
                            </span>
                            <span class="font-body-md text-body-md text-on-surface-variant">/ {{ strtolower($plan->durationLabel()) }}</span>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-primary/10 text-primary">
                            {{ $plan->schoolsLimitLabel() }}
                        </span>
                    </div>

                    <ul class="space-y-3 mb-8 flex-1">
                        <li class="flex items-start gap-3 text-on-surface-variant">
                            <span class="material-symbols-outlined text-primary text-xl">domain</span>
                            <span class="font-body-sm text-body-sm font-medium">Établissements : {{ $plan->schoolsLimitLabel() }}</span>
                        </li>
                        <li class="flex items-start gap-3 text-on-surface-variant">
                            <span class="material-symbols-outlined text-primary text-xl">schedule</span>
                            <span class="font-body-sm text-body-sm font-medium">Durée : {{ $plan->durationLabel() }} ({{ $plan->durationInMonths() }} mois)</span>
                        </li>
                        @foreach ($features as $feature)
                            <li class="flex items-start gap-3 text-on-surface-variant">
                                <span class="material-symbols-outlined text-success-green text-xl">check_circle</span>
                                <span class="font-body-sm text-body-sm">{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <form method="POST" action="{{ route('client.abonnements.store') }}"
                          class="subscription-form space-y-4"
                          data-plan-name="{{ $plan->nom }}"
                          data-amount="{{ (int) $plan->prix }}">
                        @csrf
                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">

                        <label class="block">
                            <span class="font-label-sm text-label-sm text-on-surface-variant">Méthode de paiement</span>
                            <select name="payment_method"
                                    class="mt-2 w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary text-body-sm"
                                    required>
                                <option value="">Sélectionner</option>
                                <option value="Mobile Money" @selected(old('payment_method') === 'Mobile Money')>Mobile Money</option>
                                <option value="Carte bancaire" @selected(old('payment_method') === 'Carte bancaire')>Carte bancaire</option>
                                <option value="Virement" @selected(old('payment_method') === 'Virement')>Virement</option>
                                <option value="Espèces" @selected(old('payment_method') === 'Espèces')>Espèces</option>
                            </select>
                        </label>

                        <button type="submit"
                                class="w-full py-3 bg-primary text-on-primary rounded-lg font-headline-md text-headline-md flex items-center justify-center gap-2 hover:bg-primary-container transition-all">
                            <span class="material-symbols-outlined">add_card</span>
                            Souscrire
                        </button>
                    </form>
                </div>
            </article>
        @empty
            <div class="md:col-span-2 bg-white rounded-xl border border-outline-variant p-8 text-center text-on-surface-variant">
                Aucune formule active disponible.
            </div>
        @endforelse
    </div>
</section>

<section>
    <h4 class="font-headline-md text-headline-md text-on-surface mb-5 flex items-center gap-2">
        <span class="material-symbols-outlined text-success-green">done_all</span>
        Abonnements actifs
    </h4>

    <div class="bg-white rounded-xl border border-outline-variant overflow-hidden ambient-shadow">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-surface-container-low text-on-surface-variant">
                    <tr>
                        <th class="px-6 py-4 font-label-sm text-label-sm uppercase">N°</th>
                        <th class="px-6 py-4 font-label-sm text-label-sm uppercase">Offre</th>
                        <th class="px-6 py-4 font-label-sm text-label-sm uppercase">Montant</th>
                        <th class="px-6 py-4 font-label-sm text-label-sm uppercase">Souscription</th>
                        <th class="px-6 py-4 font-label-sm text-label-sm uppercase">Expiration</th>
                        <th class="px-6 py-4 font-label-sm text-label-sm uppercase">Paiement</th>
                        <th class="px-6 py-4 font-label-sm text-label-sm uppercase">Statut paiement</th>
                        <th class="px-6 py-4 font-label-sm text-label-sm uppercase">Validation</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse ($subscriptions as $subscription)
                        @php
                            $payment = $subscription->payment ?? $subscription->payments->first();
                            $amount = $payment?->montant ?? $payment?->amount ?? $subscription->amount ?? $subscription->price ?? 0;
                            $methode = $payment?->methode_paiement ?? $payment?->payment_method ?? '-';
                            $paiementStatut = $payment?->statut ?? $payment?->status ?? 'paid';
                            $aboStatus = $subscription->abonnement_status ?? 'en_attente';
                            $aboLabel = [
                                'en_attente' => 'En attente',
                                'paye'       => 'Payé (en attente de validation)',
                                'actif'      => 'Validé (Actif)',
                                'expire'     => 'Expiré',
                            ][$aboStatus] ?? ucfirst($aboStatus);
                            $aboColor = match ($aboStatus) {
                                'actif'   => 'bg-success-green/10 text-success-green',
                                'paye'    => 'bg-primary-fixed text-primary',
                                'expire'  => 'bg-red-500/10 text-red-600',
                                default   => 'bg-warning-amber/10 text-warning-amber',
                            };
                        @endphp
                        <tr>
                            <td class="px-6 py-4 font-label-md text-on-surface-variant">#{{ $subscription->id }}</td>
                            <td class="px-6 py-4 font-label-md text-on-surface">{{ $subscription->plan?->nom ?? $subscription->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-primary font-semibold">{{ number_format((int) $amount, 0, ',', ' ') }} FCFA</td>
                            <td class="px-6 py-4 text-on-surface-variant">
                                {{ optional($subscription->date_debut ?? $subscription->created_at)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-on-surface-variant">
                                {{ $subscription->date_fin ? \Carbon\Carbon::parse($subscription->date_fin)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-on-surface-variant">{{ $methode }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full bg-primary-fixed text-primary font-label-sm">
                                    {{ ucfirst($paiementStatut) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full font-label-sm {{ $aboColor }}">{{ $aboLabel }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-on-surface-variant">
                                Aucun abonnement confirmé pour le moment.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.subscription-form').forEach((form) => {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            const paymentMethod = form.querySelector('[name="payment_method"]')?.value;
            const planName = form.dataset.planName || 'ce plan';
            const amount = Number(form.dataset.amount || 0).toLocaleString('fr-FR');

            if (!paymentMethod) {
                Swal.fire({
                    title: 'Méthode requise',
                    text: 'Veuillez sélectionner une méthode de paiement.',
                    icon: 'warning',
                    confirmButtonColor: '#1f108e',
                });
                return;
            }

            Swal.fire({
                title: 'Confirmer le paiement',
                html: `Voulez-vous confirmer le paiement de <strong>${amount} FCFA</strong> pour <strong>${planName}</strong> ?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#ba1a1a',
                confirmButtonText: 'Oui, confirmer',
                cancelButtonText: 'Annuler',
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    @if (session('success'))
        Swal.fire({
            title: 'Paiement enregistré !',
            text: @json(session('success')),
            icon: 'success',
            confirmButtonColor: '#1f108e',
        });
    @endif

    @if (session('error'))
        Swal.fire({
            title: 'Erreur',
            text: @json(session('error')),
            icon: 'error',
            confirmButtonColor: '#ba1a1a',
        });
    @endif
</script>
@endpush