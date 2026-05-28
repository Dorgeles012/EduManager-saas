@extends('sadmin.layouts.app')

@section('content')

    <!-- En-tête de bienvenue -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h2 class="font-headline-xl text-headline-xl text-on-background mb-1">Tableau de bord</h2>
            <p class="text-text-muted font-body-md">État du système au 19 juillet 2026</p>
        </div>
        <div class="flex gap-3"></div>
    </div>

    <!-- Grille -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Revenu total -->
        <div class="bg-surface-container-lowest p-6 rounded-xl card-shadow border border-slate-100 flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-secondary-container rounded-lg">
                    <span class="material-symbols-outlined text-on-secondary-container">payments</span>
                </div>
                <span class="flex items-center text-success-green font-label-sm text-label-sm bg-success-green/10 px-2 py-1 rounded-full">
                    <span class="material-symbols-outlined text-[14px] mr-1">trending_up</span>
                    12,5%
                </span>
            </div>
            <div>
                <p class="text-on-surface-variant font-label-md text-label-md mb-1">Revenu total</p>
                <h3 class="text-headline-lg font-headline-lg text-on-background">124 000 $</h3>
            </div>
        </div>

        <!-- Clients actifs -->
        <div class="bg-surface-container-lowest p-6 rounded-xl card-shadow border border-slate-100 flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-primary-fixed rounded-lg">
                    <span class="material-symbols-outlined text-primary">corporate_fare</span>
                </div>
                <span class="flex items-center text-success-green font-label-sm text-label-sm bg-success-green/10 px-2 py-1 rounded-full">
                    <span class="material-symbols-outlined text-[14px] mr-1">add</span>
                    4
                </span>
            </div>
            <div>
                <p class="text-on-surface-variant font-label-md text-label-md mb-1">Clients actifs</p>
                <h3 class="text-headline-lg font-headline-lg text-on-background">42</h3>
            </div>
        </div>

        <!-- Total écoles -->
        <div class="bg-surface-container-lowest p-6 rounded-xl card-shadow border border-slate-100 flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-surface-container-highest rounded-lg">
                    <span class="material-symbols-outlined text-on-surface">school</span>
                </div>
                <span class="flex items-center text-on-surface-variant font-label-sm text-label-sm bg-surface-subtle px-2 py-1 rounded-full">Stable</span>
            </div>
            <div>
                <p class="text-on-surface-variant font-label-md text-label-md mb-1">Total écoles</p>
                <h3 class="text-headline-lg font-headline-lg text-on-background">156</h3>
            </div>
        </div>

        <!-- Factures en attente -->
        <div class="bg-surface-container-lowest p-6 rounded-xl card-shadow border border-slate-100 flex flex-col justify-between relative overflow-hidden">
            <div class="absolute top-0 right-0 p-1 bg-alert-red rounded-bl-lg"></div>
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-error-container rounded-lg">
                    <span class="material-symbols-outlined text-error">priority_high</span>
                </div>
                <span class="flex items-center text-alert-red font-label-sm text-label-sm bg-alert-red/10 px-2 py-1 rounded-full">Priorité</span>
            </div>
            <div>
                <p class="text-on-surface-variant font-label-md text-label-md mb-1">Factures en attente</p>
                <h3 class="text-headline-lg font-headline-lg text-on-background">8</h3>
            </div>
        </div>
    </div>

    <!-- Disposition Bento - Sections principales -->
    <div class="grid grid-cols-12 gap-8 mb-8">
        <!-- Espace réservé pour d'autres sections si nécessaire -->
    </div>

    <!-- Tableau des clients récents -->
    <div class="bg-surface-container-lowest rounded-xl card-shadow border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <h4 class="font-headline-md text-headline-md text-on-background">Clients récents</h4>
            <div class="flex gap-2">
                <div class="relative"></div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                <tr class="bg-surface-container-low">
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Nom du client</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Forfait</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Écoles</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Date d'inscription</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider text-right">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                <!-- Ligne 1 -->
                <tr class="hover:bg-surface-container-low/50">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded bg-primary-fixed-dim text-primary flex items-center justify-center font-bold text-xs">GA</div>
                            <div>
                                <p class="font-label-md text-label-md text-on-surface">Global Academy</p>
                                <p class="text-body-sm text-label-sm text-on-surface-variant">contact@global.edu</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 bg-primary-fixed text-primary rounded-full text-label-sm font-label-sm">Premium</span>
                    </td>
                    <td class="px-6 py-4 font-body-sm text-body-sm">12</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-green/10 text-success-green">Actif</span>
                    </td>
                    <td class="px-6 py-4 text-on-surface-variant font-body-sm">12 oct. 2023</td>
                    <td class="px-6 py-4 text-right">
                        <button class="text-primary hover:underline font-label-md text-label-md">Gérer</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
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