@extends('sadmin.layouts.app')

@section('content')

    <!-- En-tête de bienvenue -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h2 class="font-headline-xl text-headline-xl text-on-background mb-1">Tableau de bord</h2>
            <p class="text-text-muted font-body-md">État du système</p>
        </div>
        <div class="flex gap-3"></div>
    </div>

    <!-- Grille -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Revenu total -->
        <div class="bg-surface-container-lowest p-6 rounded-xl card-shadow border border-slate-100 flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-success-green/10 rounded-lg">
                    <span class="material-symbols-outlined text-success-green">payments</span>
                </div>
            </div>
            <div>
                <p class="text-on-surface-variant font-label-md text-label-md mb-1">Revenu total </p>
                <h3 class="text-headline-lg font-headline-lg text-on-background">
                    {{ number_format($revenueTotal ?? 0, 0, ',', ' ') }} FCFA
                </h3>
            </div>
        </div>

        <!-- Clients créés -->
        <div class="bg-surface-container-lowest p-6 rounded-xl card-shadow border border-slate-100 flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-primary/10 rounded-lg">
                    <span class="material-symbols-outlined text-primary">corporate_fare</span>
                </div>
            </div>
            <div>
                <p class="text-on-surface-variant font-label-md text-label-md mb-1">Clients créés</p>
                <h3 class="text-headline-lg font-headline-lg text-on-background">{{ $clientsCount ?? 0 }}</h3>
            </div>
        </div>

        <!-- Établissements créés -->
        <div class="bg-surface-container-lowest p-6 rounded-xl card-shadow border border-slate-100 flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-warning-amber/10 rounded-lg">
                    <span class="material-symbols-outlined text-warning-amber">school</span>
                </div>
            </div>
            <div>
                <p class="text-on-surface-variant font-label-md text-label-md mb-1">Établissements créés</p>
                <h3 class="text-headline-lg font-headline-lg text-on-background">{{ $etablissementsCount ?? 0 }}</h3>
            </div>
        </div>

        <!-- Super Administrateurs -->
        <div class="bg-surface-container-lowest p-6 rounded-xl card-shadow border border-slate-100 flex flex-col justify-between relative overflow-hidden">
            <div class="absolute top-0 right-0 p-1 bg-alert-red rounded-bl-lg"></div>
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-alert-red/10 rounded-lg">
                    <span class="material-symbols-outlined text-alert-red">admin_panel_settings</span>
                </div>
            </div>
            <div>
                <p class="text-on-surface-variant font-label-md text-label-md mb-1">Super Administrateurs</p>
                <h3 class="text-headline-lg font-headline-lg text-on-background">{{ $sadminCount ?? 0 }}</h3>
            </div>
        </div>
    </div>

    <!-- Disposition Bento - Sections principales -->
    <div class="grid grid-cols-12 gap-8 mb-8">
        <div class="col-span-12"></div>
    </div>

@endsection

@section('scripts')
    <script>
        // Script simplifié sans animations
        const searchInput = document.querySelector('input[type="text"]');
        if (searchInput) {
            searchInput.addEventListener('focus', () => {
                searchInput.parentElement.classList.add('scale-[1.01]');
            });
            searchInput.addEventListener('blur', () => {
                searchInput.parentElement.classList.remove('scale-[1.01]');
            });
        }
    </script>
@endsection

