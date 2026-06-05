@extends('client.layouts.app')
@section('title', 'EduManager - Abonnements')
@section('content')
<!-- Header Section -->
@php
    $monthlyPrice = $featuredPlan?->prix ?? 0;
    $annualPrice = $monthlyPrice * 12;
@endphp

<div class="mb-10">
    <h2 class="font-headline-lg text-headline-lg text-primary mb-2"> Abonnements</h2>

    <p class="font-body-lg text-body-lg text-on-surface-variant">Choisissez l'abonnement qui correspond à vos besoins</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
    <div class="bg-white p-6 rounded-xl ambient-shadow border border-outline-variant flex items-center justify-between">
        <div>
            <p class="font-label-sm text-label-sm text-on-surface-variant mb-1 uppercase tracking-widest">Formules disponibles</p>
            <h3 class="font-headline-lg text-headline-lg text-on-surface">1</h3>
        </div>
        <div class="w-12 h-12 bg-primary-container/10 rounded-lg flex items-center justify-center">
            <span class="material-symbols-outlined text-primary text-3xl">sell</span>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl ambient-shadow border border-outline-variant flex items-center justify-between">
        <div>
            <p class="font-label-sm text-label-sm text-on-surface-variant mb-1 uppercase tracking-widest">Options</p>
            <h3 class="font-headline-lg text-headline-lg text-on-surface">Plusieurs</h3>
        </div>
        <div class="w-12 h-12 bg-success-green/10 rounded-lg flex items-center justify-center">
            <span class="material-symbols-outlined text-success-green text-3xl">group</span>
        </div>
    </div>
</div>

<!-- Subscription Formula Section -->
<div class="mb-8">
    <h4 class="font-headline-md text-headline-md text-on-surface mb-6 flex items-center gap-2">
        <span class="material-symbols-outlined text-primary">verified_user</span>
        Nos formules d'abonnement
    </h4>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
  
        <div class="bg-white rounded-xl premium-shadow border border-outline-variant overflow-hidden flex flex-col hover:border-primary transition-colors duration-300">
            <div class="bg-primary-container p-6 text-on-primary-container">
                <span class="font-label-sm text-label-sm uppercase tracking-widest opacity-80">PLAN</span>
                <h5 class="font-headline-lg text-headline-lg mt-1">{{ $featuredPlan?->nom ?? '—' }}</h5>
            </div>
            <div class="p-8 flex-grow">
                <div class="mb-6">
                    <span class="font-headline-lg text-headline-lg text-primary">{{ number_format((int) $monthlyPrice, 0, ',', ' ') }} FCFA</span>
                    <span class="font-body-md text-body-md text-on-surface-variant">/ mois</span>
                </div>
                
                <div class="flex items-center gap-2 px-4 py-2 bg-surface-container-low rounded-lg mb-8 border border-outline-variant/50">
                    <span class="material-symbols-outlined text-primary text-xl">calendar_month</span>
                    <p class="font-label-md text-label-md text-on-surface">Prix annuel: <span class="font-bold">{{ number_format((int) $annualPrice, 0, ',', ' ') }} FCFA</span></p>
                </div>

                @php
                    $features = [];

                    if (!empty($featuredPlan?->description)) {
                        $decoded = json_decode($featuredPlan->description, true);

                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $decodedFeatures = $decoded['features'] ?? $decoded['data'] ?? $decoded;
                            $features = is_array($decodedFeatures) ? $decodedFeatures : [];
                        } else {
                            $features = preg_split("/\r?\n/", $featuredPlan->description) ?: [];
                        }

                        $features = array_values(array_filter(array_map(
                            fn($feature) => is_string($feature) ? trim($feature) : '',
                            $features
                        )));
                    }
                @endphp

                <ul class="space-y-4 mb-10">

                    @forelse($features as $feature)
                        <li class="flex items-center gap-3 text-on-surface-variant">
                            <span class="material-symbols-outlined text-success-green">check_circle</span>
                            <span class="font-body-md text-body-md">{{ $feature }}</span>
                        </li>
                    @empty
                        {{-- Si aucune feature n'est enregistrée, on n'affiche rien --}}
                    @endforelse
                </ul>

                <button class="w-full py-4 bg-primary text-on-primary rounded-lg font-headline-md text-headline-md flex items-center justify-center gap-2 hover:bg-primary-container transition-all transform active:scale-95 shadow-lg" onclick="openModal()">
                    <span class="material-symbols-outlined">rocket_launch</span>
                    Souscrire
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Overlay -->
<div class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black/40 backdrop-blur-sm transition-opacity duration-300" id="subscriptionModal">
    <div class="bg-white w-full max-w-md mx-4 rounded-xl premium-shadow border border-outline-variant overflow-hidden transform transition-all scale-95 opacity-0 duration-300" id="modalContainer">
        <div class="p-6 border-b border-outline-variant flex justify-between items-center">
            <h3 class="font-headline-md text-headline-md text-on-surface">Confirmation d'abonnement</h3>
            <button class="text-on-surface-variant hover:text-primary transition-colors" onclick="closeModal()">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-8">
            <div class="bg-surface-container-low p-6 rounded-lg border border-outline-variant mb-6 text-center">
                <p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-widest mb-2">Formule choisie</p>
                <h4 class="font-headline-xl text-headline-xl text-primary">{{ $featuredPlan?->nom ?? 'PRIMAIRE' }}</h4>
            </div>
            <div class="space-y-4 mb-8">
                <div class="flex justify-between items-center py-2 border-b border-outline-variant/30">
                    <span class="font-body-md text-body-md text-on-surface-variant">Prix mensuel</span>
                    <span class="font-label-md text-label-md text-on-surface">{{ number_format((int) $monthlyPrice, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-outline-variant/30">
                    <span class="font-body-md text-body-md text-on-surface-variant">Engagement</span>
                    <span class="font-label-md text-label-md text-on-surface">Annuel</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="font-headline-md text-headline-md text-on-surface">Total à payer</span>
                    <span class="font-headline-md text-headline-md text-primary">{{ number_format((int) $annualPrice, 0, ',', ' ') }} FCFA</span>
                </div>
            </div>
            <div class="flex flex-col gap-3">
                <button class="w-full py-4 bg-primary text-on-primary rounded-lg font-headline-md text-headline-md hover:bg-primary-container transition-all" id="confirmBtn">
                    Confirmer la souscription
                </button>
                <button class="w-full py-4 border border-outline text-on-surface-variant rounded-lg font-headline-md text-headline-md hover:bg-surface-container-low transition-all" onclick="closeModal()">
                    Annuler
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const modal = document.getElementById('subscriptionModal');
    const modalContainer = document.getElementById('modalContainer');
    const confirmBtn = document.getElementById('confirmBtn');

    function openModal() {
        modal.classList.remove('hidden');
        setTimeout(() => {
            modalContainer.classList.remove('scale-95', 'opacity-0');
            modalContainer.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeModal() {
        modalContainer.classList.remove('scale-100', 'opacity-100');
        modalContainer.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            Swal.fire({
                title: 'Confirmer la souscription',
                text: "Voulez-vous vraiment souscrire à cette formule ?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1f108e',
                cancelButtonColor: '#ba1a1a',
                confirmButtonText: 'Oui, souscrire !',
                cancelButtonText: 'Annuler',
                customClass: {
                    title: 'font-headline-md',
                    content: 'font-body-md',
                    confirmButton: 'rounded-lg',
                    cancelButton: 'rounded-lg'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    closeModal();
                    
                    Swal.fire({
                        title: 'Traitement en cours...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });

                    setTimeout(() => {
                        Swal.fire({
                            title: 'Succès !',
                            text: 'Votre abonnement a été activé avec succès.',
                            icon: 'success',
                            confirmButtonColor: '#1f108e'
                        });
                    }, 2000);
                }
            });
        });
    }

    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });
</script>
@endpush
