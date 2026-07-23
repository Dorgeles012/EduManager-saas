@extends('enseignant.layouts.app')

@section('content')
<div class="dashboard-wrapper">
    <div class="dashboard-content">
        <div class="p-6 space-y-8">
            <!-- En-tête -->
            <div class="flex justify-between items-end flex-wrap gap-3">
                <div>
                    <h2 class="font-headline-lg text-headline-lg text-on-surface">Tableau de bord</h2>
                    <p class="text-text-muted mt-1 font-body-sm">Bienvenue, voici un aperçu de vos enseignements.</p>
                </div>
            </div>

            <!-- Cartes de statistiques -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Mes Classes -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant hover:shadow-md transition-all duration-200">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2 bg-primary/10 rounded-lg">
                            <span class="material-symbols-outlined text-primary">meeting_room</span>
                        </div>
                    </div>
                    <p class="text-text-muted font-label-sm text-label-md">Mes Classes</p>
                    <h3 class="font-headline-md text-headline-md text-on-surface mt-1">{{ $counts['classes'] ?? 0 }}</h3>
                </div>

                <!-- Mes Matières -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant hover:shadow-md transition-all duration-200">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2 bg-secondary/10 rounded-lg">
                            <span class="material-symbols-outlined text-secondary">menu_book</span>
                        </div>
                    </div>
                    <p class="text-text-muted font-label-sm text-label-md">Mes Matières</p>
                    <h3 class="font-headline-md text-headline-md text-on-surface mt-1">{{ $counts['matieres'] ?? 0 }}</h3>
                </div>

                <!-- Mes Élèves -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant hover:shadow-md transition-all duration-200">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <span class="material-symbols-outlined text-yellow-600">group</span>
                        </div>
                    </div>
                    <p class="text-text-muted font-label-sm text-label-md">Mes Élèves</p>
                    <h3 class="font-headline-md text-headline-md text-on-surface mt-1">{{ $counts['eleves'] ?? 0 }}</h3>
                </div>

                <!-- Mes Séries -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant hover:shadow-md transition-all duration-200">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2 bg-red-100 rounded-lg">
                            <span class="material-symbols-outlined text-red-600">description</span>
                        </div>
                    </div>
                    <p class="text-text-muted font-label-sm text-label-md">Mes Séries</p>
                    <h3 class="font-headline-md text-headline-md text-on-surface mt-1">{{ $counts['series'] ?? 0 }}</h3>
                </div>

                <!-- Notes Saisies -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant hover:shadow-md transition-all duration-200">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2 bg-primary/10 rounded-lg">
                            <span class="material-symbols-outlined text-primary">grade</span>
                        </div>
                    </div>
                    <p class="text-text-muted font-label-sm text-label-md">Notes Saisies</p>
                    <h3 class="font-headline-md text-headline-md text-on-surface mt-1">{{ $counts['notes'] ?? 0 }}</h3>
                </div>

                <!-- Séances/Semaine -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant hover:shadow-md transition-all duration-200">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2 bg-secondary/10 rounded-lg">
                            <span class="material-symbols-outlined text-secondary">calendar_month</span>
                        </div>
                    </div>
                    <p class="text-text-muted font-label-sm text-label-md">Séances/Semaine</p>
                    <h3 class="font-headline-md text-headline-md text-on-surface mt-1">{{ $counts['seances'] ?? 0 }}</h3>
                </div>

                <!-- Année Académique Active -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant hover:shadow-md transition-all duration-200 col-span-1 sm:col-span-2 lg:col-span-2">
                    <div class="flex items-start gap-4 mb-4">
                        <div class="p-2 bg-yellow-100 rounded-lg shrink-0">
                            <span class="material-symbols-outlined text-yellow-600">calendar_today</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-text-muted font-label-sm text-label-md mb-1">Année Académique Active</p>
                            @if($activeYear)
                                <p class="font-headline-md text-headline-md text-on-surface mt-1">
                                    Année académique : <strong>{{ $activeYear->libelle }}</strong>
                                </p>
                                <p class="font-body-sm text-body-sm text-text-muted mt-1">
                                    Date de début : {{ \Carbon\Carbon::parse($activeYear->date_debut)->format('d/m/Y') }}
                                </p>
                                <p class="font-body-sm text-body-sm text-text-muted mt-0.5">
                                    Date de fin : {{ \Carbon\Carbon::parse($activeYear->date_fin)->format('d/m/Y') }}
                                </p>
                                <div class="mt-2">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                                        Statut : Actif
                                    </span>
                                </div>
                            @else
                                <p class="font-body-md text-body-md text-text-muted mt-1">
                                    Aucune année académique active.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styles spécifiques au dashboard */
    .dashboard-wrapper {
        height: 100vh;
        overflow: hidden;
    }
    
    .dashboard-content {
        height: calc(100vh - 80px);
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #c8c4d5 #f0f3ff;
    }
    
    .dashboard-content::-webkit-scrollbar {
        width: 6px;
    }
    
    .dashboard-content::-webkit-scrollbar-track {
        background: #f0f3ff;
        border-radius: 10px;
    }
    
    .dashboard-content::-webkit-scrollbar-thumb {
        background: #c8c4d5;
        border-radius: 10px;
    }
    
    .dashboard-content::-webkit-scrollbar-thumb:hover {
        background: #a09eb0;
    }
    
    /* Animation pour le statut */
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .dashboard-content {
            height: calc(100vh - 60px);
        }
    }
</style>
@endsection