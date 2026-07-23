@extends('personnel.layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="fr" class="overflow-x-hidden overflow-y-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduManager - Dashboard Personnel</title>
    <style>
        html, body {
            overflow: hidden !important;
            height: 100vh !important;
        }
        .dashboard-wrapper {
            height: 100vh;
            overflow: hidden;
        }
        .dashboard-content {
            height: calc(100vh - 80px);
            overflow-y: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .dashboard-content::-webkit-scrollbar {
            display: none;
            width: 0;
            background: transparent;
        }
        @media (max-width: 768px) {
            .dashboard-content {
                height: calc(100vh - 60px);
            }
        }
    </style>
</head>
<body class="font-body-sm text-body-sm overflow-hidden h-screen">
<div class="dashboard-wrapper">
    <div class="dashboard-content">
        <div class="p-6 space-y-8">
            <div class="flex justify-between items-end flex-wrap gap-3">
                <div>
                    <h2 class="font-headline-lg text-headline-lg text-on-surface">Tableau de bord</h2>
                    <p class="text-text-muted mt-1 font-body-sm">Bienvenue, voici la performance de votre établissement.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant hover:shadow-md transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2 bg-primary/10 rounded-lg">
                            <span class="material-symbols-outlined text-primary">payments</span>
                        </div>
                    </div>
                    <p class="text-text-muted font-label-sm text-label-md">Revenu Total</p>
                    <h3 class="font-headline-md text-headline-md text-on-surface mt-1">{{ number_format($counts['revenu_total'] ?? 0, 0, ',', ' ') }} FCFA</h3>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant hover:shadow-md transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2 bg-secondary/10 rounded-lg">
                            <span class="material-symbols-outlined text-secondary">group</span>
                        </div>
                        <span class="text-text-muted font-label-xs text-label-xs">Total Actif</span>
                    </div>
                    <p class="text-text-muted font-label-sm text-label-md">Étudiants Totaux</p>
                    <h3 class="font-headline-md text-headline-md text-on-surface mt-1">{{ $counts['eleves'] ?? 0 }}</h3>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant hover:shadow-md transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <span class="material-symbols-outlined text-yellow-600">domain</span>
                        </div>
                    </div>
                    <p class="text-text-muted font-label-sm text-label-md">Établissements</p>
                    <h3 class="font-headline-md text-headline-md text-on-surface mt-1">{{ $counts['etablissements'] ?? 0 }}</h3>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant hover:shadow-md transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2 bg-red-100 rounded-lg">
                            <span class="material-symbols-outlined text-red-600">receipt_long</span>
                        </div>
                    </div>
                    <p class="text-text-muted font-label-sm text-label-md">Factures en attente</p>
                    <h3 class="font-headline-md text-headline-md text-on-surface mt-1">{{ $counts['factures_impayees'] ?? 0 }}</h3>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant hover:shadow-md transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2 bg-primary/10 rounded-lg">
                            <span class="material-symbols-outlined text-primary">person_4</span>
                        </div>
                    </div>
                    <p class="text-text-muted font-label-sm text-label-md">Enseignants</p>
                    <h3 class="font-headline-md text-headline-md text-on-surface mt-1">{{ $counts['enseignants'] ?? 0 }}</h3>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant hover:shadow-md transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2 bg-secondary/10 rounded-lg">
                            <span class="material-symbols-outlined text-secondary">menu_book</span>
                        </div>
                    </div>
                    <p class="text-text-muted font-label-sm text-label-md">Matières</p>
                    <h3 class="font-headline-md text-headline-md text-on-surface mt-1">{{ $counts['matieres'] ?? 0 }}</h3>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant hover:shadow-md transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <span class="material-symbols-outlined text-yellow-600">layers</span>
                        </div>
                    </div>
                    <p class="text-text-muted font-label-sm text-label-md">Niveaux</p>
                    <h3 class="font-headline-md text-headline-md text-on-surface mt-1">{{ $counts['niveaux'] ?? 0 }}</h3>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant hover:shadow-md transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2 bg-red-100 rounded-lg">
                            <span class="material-symbols-outlined text-red-600">class</span>
                        </div>
                    </div>
                    <p class="text-text-muted font-label-sm text-label-md">Classes</p>
                    <h3 class="font-headline-md text-headline-md text-on-surface mt-1">{{ $counts['classes'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
@endsection
