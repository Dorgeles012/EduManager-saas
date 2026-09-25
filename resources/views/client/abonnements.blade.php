@extends('client.layouts.app')

@section('title', 'EduManager - Abonnements')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Abonnements</h2>
    <p class="text-sm text-gray-500 mt-1">Choisissez une formule, confirmez le paiement, puis retrouvez votre abonnement actif.</p>
</div>

@if ($errors->any())
    <div class="mb-6 rounded-2xl border border-rose-100 bg-rose-50 px-4 py-3 text-rose-700">
        <p class="text-xs font-semibold mb-2">Veuillez corriger les erreurs suivantes :</p>
        <ul class="list-disc pl-5 text-xs space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-2 lg:grid-cols-2 gap-3 sm:gap-4 mb-6">
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                <span class="material-symbols-outlined text-lg">sell</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Formules</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $plans->count() }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Disponibles</p>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                <span class="material-symbols-outlined text-lg">verified</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Actifs</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $subscriptions->count() }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Souscriptions</p>
    </div>
</div>

<section class="mb-8">
    @php
        $activeSub = $currentSubscription ?? null;
        $subStatus = $activeSub?->abonnement_status;
    @endphp

    @if(! $activeSub)
        <div class="mb-6 rounded-2xl border border-indigo-100 bg-gradient-to-r from-indigo-50 to-white p-5 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600 flex-shrink-0">
                    <span class="material-symbols-outlined text-xl">waving_hand</span>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">Bienvenue ! Vous n'avez pas encore d'abonnement.</p>
                    <p class="text-xs text-gray-500 mt-0.5">Choisissez une formule ci-dessous pour commencer à utiliser EduManager.</p>
                </div>
            </div>
        </div>
    @elseif($subStatus === 'paye')
        <div class="mb-6 rounded-2xl border border-blue-100 bg-blue-50 p-5 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 flex-shrink-0">
                        <span class="material-symbols-outlined text-xl">hourglass_top</span>
                    </div>
                    <div>
                        <p class="font-semibold text-blue-900">Paiement reçu — En attente de validation</p>
                        <p class="text-xs text-blue-700 mt-0.5">L'administrateur doit valider votre abonnement pour activer toutes les fonctionnalités.</p>
                        @if($activeSub->plan)
                            <p class="text-[11px] text-blue-600 font-medium mt-1">
                                {{ $activeSub->plan->nom }} — {{ number_format((int) ($activeSub->price ?? $activeSub->amount ?? 0), 0, ',', ' ') }} FCFA
                            </p>
                        @endif
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-3 py-1.5 text-xs font-semibold text-blue-700 self-start sm:self-auto">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                    En attente
                </span>
            </div>
        </div>
    @elseif($subStatus === 'en_attente')
        <div class="mb-6 rounded-2xl border border-amber-100 bg-amber-50 p-5 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600 flex-shrink-0">
                    <span class="material-symbols-outlined text-xl">credit_card</span>
                </div>
                <div>
                    <p class="font-semibold text-amber-900">Abonnement en attente de paiement</p>
                    <p class="text-xs text-amber-700 mt-0.5">Choisissez une formule ci-dessous et confirmez votre paiement pour activer votre accès.</p>
                </div>
            </div>
        </div>
    @elseif($activeSub?->isWithinGracePeriod())
        <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600 flex-shrink-0">
                        <span class="material-symbols-outlined text-xl">warning</span>
                    </div>
                    <div>
                        <p class="font-semibold text-amber-900">Abonnement expiré — Période de grâce</p>
                        <p class="text-xs text-amber-800 mt-0.5">
                            Il vous reste <strong>{{ $activeSub->remainingGraceDays() }} jour{{ $activeSub->remainingGraceDays() > 1 ? 's' : '' }}</strong> avant le blocage de votre compte.
                        </p>
                    </div>
                </div>
                <a href="#plans" class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 px-4 py-2 text-xs font-semibold text-white transition shadow-sm self-start sm:self-auto">
                    <span class="material-symbols-outlined text-sm">refresh</span>
                    Renouveler
                </a>
            </div>
        </div>
    @endif

    <div class="flex items-center gap-2.5 mb-5" id="plans">
        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
            <span class="material-symbols-outlined text-base">workspace_premium</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-900">Formules disponibles</h3>
            <p class="text-[11px] text-gray-500">Sélectionnez le plan qui correspond à vos besoins</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5 max-w-5xl mx-auto">
        @forelse ($plans as $plan)
            @php
                $features = collect(preg_split("/\r?\n/", (string) $plan->description))
                    ->map(fn ($feature) => trim($feature))
                    ->filter()
                    ->values();
            @endphp

            <article class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow overflow-hidden flex flex-col">
                <div class="bg-gradient-to-br from-indigo-600 to-indigo-700 p-5">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-indigo-100">Plan</p>
                    <h4 class="text-lg font-bold text-white mt-1">{{ $plan->nom }}</h4>
                </div>

                <div class="p-5 flex flex-col flex-1">
                    <div class="mb-5 flex items-baseline justify-between gap-2">
                        <div>
                            <span class="text-2xl font-bold text-gray-900">{{ number_format((int) $plan->prix, 0, ',', ' ') }}</span>
                            <span class="text-xs text-gray-500 ml-1">FCFA / {{ strtolower($plan->durationLabel()) }}</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-50 text-indigo-700 whitespace-nowrap">
                            {{ $plan->schoolsLimitLabel() }}
                        </span>
                    </div>

                    <ul class="space-y-2.5 mb-6 flex-1">
                        <li class="flex items-start gap-2.5 text-gray-700">
                            <span class="material-symbols-outlined text-indigo-600 text-base flex-shrink-0 mt-0.5">domain</span>
                            <span class="text-xs">Établissements : <strong>{{ $plan->schoolsLimitLabel() }}</strong></span>
                        </li>
                        <li class="flex items-start gap-2.5 text-gray-700">
                            <span class="material-symbols-outlined text-indigo-600 text-base flex-shrink-0 mt-0.5">schedule</span>
                            <span class="text-xs">Durée : <strong>{{ $plan->durationLabel() }}</strong> ({{ $plan->durationInMonths() }} mois)</span>
                        </li>
                        @foreach ($features as $feature)
                            <li class="flex items-start gap-2.5 text-gray-600">
                                <span class="material-symbols-outlined text-emerald-600 text-base flex-shrink-0 mt-0.5">check_circle</span>
                                <span class="text-xs">{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <form method="POST" action="{{ route('client.abonnements.store') }}"
                          class="subscription-form space-y-3"
                          data-plan-name="{{ $plan->nom }}"
                          data-amount="{{ (int) $plan->prix }}">
                        @csrf
                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">

                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Méthode de paiement</label>
                            <select name="payment_method"
                                    class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition"
                                    required>
                                <option value="">Sélectionner</option>
                                <option value="Mobile Money" @selected(old('payment_method') === 'Mobile Money')>Mobile Money</option>
                                <option value="Carte bancaire" @selected(old('payment_method') === 'Carte bancaire')>Carte bancaire</option>
                                <option value="Virement" @selected(old('payment_method') === 'Virement')>Virement</option>
                                <option value="Espèces" @selected(old('payment_method') === 'Espèces')>Espèces</option>
                            </select>
                        </div>

                        <button type="submit"
                                class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold flex items-center justify-center gap-1.5 transition shadow-sm">
                            <span class="material-symbols-outlined text-sm">add_card</span>
                            Souscrire maintenant
                        </button>
                    </form>
                </div>
            </article>
        @empty
            <div class="md:col-span-2 bg-white rounded-2xl border border-gray-100 p-8 text-center">
                <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-3">
                    <span class="material-symbols-outlined text-2xl text-gray-300">inbox</span>
                </div>
                <p class="text-sm font-semibold text-gray-700">Aucune formule disponible</p>
                <p class="text-xs text-gray-400 mt-1">Revenez plus tard.</p>
            </div>
        @endforelse
    </div>
</section>

<section>
    <div class="flex items-center gap-2.5 mb-5">
        <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
            <span class="material-symbols-outlined text-base">done_all</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-900">Mes abonnements</h3>
            <p class="text-[11px] text-gray-500">Historique de vos souscriptions</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/50">
                        <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">N°</th>
                        <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Offre</th>
                        <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Montant</th>
                        <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Souscription</th>
                        <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Expiration</th>
                        <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Paiement</th>
                        <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($subscriptions as $subscription)
                        @php
                            $payment = $subscription->payment ?? $subscription->payments->first();
                            $amount = $payment?->montant ?? $payment?->amount ?? $subscription->amount ?? $subscription->price ?? 0;
                            $methode = $payment?->methode_paiement ?? $payment?->payment_method ?? '-';
                            $paiementStatut = $payment?->statut ?? $payment?->status ?? 'paid';
                            $aboStatus = $subscription->abonnement_status ?? 'en_attente';
                            $aboLabel = [
                                'en_attente' => 'En attente',
                                'paye'       => 'Payé',
                                'actif'      => 'Actif',
                                'expire'     => 'Expiré',
                            ][$aboStatus] ?? ucfirst($aboStatus);
                            $aboColor = match ($aboStatus) {
                                'actif'   => 'bg-emerald-50 text-emerald-700',
                                'paye'    => 'bg-blue-50 text-blue-700',
                                'expire'  => 'bg-rose-50 text-rose-700',
                                default   => 'bg-amber-50 text-amber-700',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-4 py-3 text-xs font-semibold text-gray-500">#{{ $subscription->id }}</td>
                            <td class="px-4 py-3 text-xs font-semibold text-gray-900">{{ $subscription->plan?->nom ?? $subscription->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-xs font-bold text-indigo-600">{{ number_format((int) $amount, 0, ',', ' ') }} FCFA</td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                {{ optional($subscription->date_debut ?? $subscription->created_at)->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                {{ $subscription->date_fin ? \Carbon\Carbon::parse($subscription->date_fin)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600">{{ $methode }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold {{ $aboColor }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                    {{ $aboLabel }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center mb-3">
                                        <span class="material-symbols-outlined text-2xl text-gray-300">receipt_long</span>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-700">Aucun abonnement</p>
                                    <p class="text-xs text-gray-400 mt-1">Vos souscriptions apparaîtront ici.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="lg:hidden divide-y divide-gray-100">
            @forelse ($subscriptions as $subscription)
                @php
                    $payment = $subscription->payment ?? $subscription->payments->first();
                    $amount = $payment?->montant ?? $payment?->amount ?? $subscription->amount ?? $subscription->price ?? 0;
                    $methode = $payment?->methode_paiement ?? $payment?->payment_method ?? '-';
                    $aboStatus = $subscription->abonnement_status ?? 'en_attente';
                    $aboLabel = [
                        'en_attente' => 'En attente',
                        'paye'       => 'Payé',
                        'actif'      => 'Actif',
                        'expire'     => 'Expiré',
                    ][$aboStatus] ?? ucfirst($aboStatus);
                    $aboColor = match ($aboStatus) {
                        'actif'   => 'bg-emerald-50 text-emerald-700',
                        'paye'    => 'bg-blue-50 text-blue-700',
                        'expire'  => 'bg-rose-50 text-rose-700',
                        default   => 'bg-amber-50 text-amber-700',
                    };
                @endphp
                <div class="p-4 space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-xs font-bold flex-shrink-0">
                                #{{ $subscription->id }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $subscription->plan?->nom ?? $subscription->name ?? '-' }}</p>
                                <p class="text-[11px] text-gray-500 truncate">{{ $methode }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold {{ $aboColor }} flex-shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                            {{ $aboLabel }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Montant</p>
                            <p class="text-xs font-bold text-indigo-600 mt-0.5">{{ number_format((int) $amount, 0, ',', ' ') }} FCFA</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Souscription</p>
                            <p class="text-xs text-gray-700 mt-0.5">{{ optional($subscription->date_debut ?? $subscription->created_at)->format('d/m/Y') }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Expiration</p>
                            <p class="text-xs text-gray-700 mt-0.5">{{ $subscription->date_fin ? \Carbon\Carbon::parse($subscription->date_fin)->format('d/m/Y') : '-' }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-16 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-3">
                        <span class="material-symbols-outlined text-2xl text-gray-300">receipt_long</span>
                    </div>
                    <p class="text-sm font-semibold text-gray-700">Aucun abonnement</p>
                    <p class="text-xs text-gray-400 mt-1">Vos souscriptions apparaîtront ici.</p>
                </div>
            @endforelse
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
                    confirmButtonColor: '#4f46e5',
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold',
                        title: 'text-base font-semibold',
                        htmlContainer: 'text-xs text-gray-500'
                    },
                    buttonsStyling: false
                });
                return;
            }

            Swal.fire({
                title: 'Confirmer le paiement',
                html: `Voulez-vous confirmer le paiement de <strong>${amount} FCFA</strong> pour <strong>${planName}</strong> ?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#e11d48',
                confirmButtonText: 'Oui, confirmer',
                cancelButtonText: 'Annuler',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold text-white',
                    cancelButton: 'px-4 py-2 rounded-lg text-xs font-semibold text-white',
                    title: 'text-base font-semibold',
                    htmlContainer: 'text-xs text-gray-500'
                },
                buttonsStyling: false
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
            confirmButtonColor: '#4f46e5',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold text-white',
                title: 'text-base font-semibold',
                htmlContainer: 'text-xs text-gray-500'
            },
            buttonsStyling: false
        });
    @endif

    @if (session('error'))
        Swal.fire({
            title: 'Erreur',
            text: @json(session('error')),
            icon: 'error',
            confirmButtonColor: '#e11d48',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold text-white',
                title: 'text-base font-semibold',
                htmlContainer: 'text-xs text-gray-500'
            },
            buttonsStyling: false
        });
    @endif
</script>
@endpush