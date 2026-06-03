@extends('client.layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="fr" class="overflow-x-hidden overflow-y-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduManager - Dashboard</title>
    <style>
        /* Suppression complète des scrollbars */
        html, body {
            overflow: hidden !important;
            height: 100vh !important;
        }
        
        /* Container principal sans scroll */
        .dashboard-wrapper {
            height: 100vh;
            overflow: hidden;
        }
        
        /* Zone de contenu avec scroll interne invisible */
        .dashboard-content {
            height: calc(100vh - 80px);
            overflow-y: auto;
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE/Edge */
        }
        
        /* Cache la scrollbar sur Chrome/Safari/Opera */
        .dashboard-content::-webkit-scrollbar {
            display: none;
            width: 0;
            background: transparent;
        }
        
        /* Ajustements responsifs */
        @media (max-width: 768px) {
            .dashboard-content {
                height: calc(100vh - 60px);
            }
        }
    </style>
</head>
<body class="font-body-sm text-body-sm overflow-hidden h-screen">

<!-- Dashboard Canvas -->
<div class="dashboard-wrapper">
    <div class="dashboard-content">
        <div class="p-6 space-y-8">
            <!-- Page Header -->
            <div class="flex justify-between items-end flex-wrap gap-3">
                <div>
                    <h2 class="font-headline-lg text-headline-lg text-on-surface">Tableau de bord</h2>
                    <p class="text-text-muted mt-1 font-body-sm">Bienvenue, voici la performance de votre réseau aujourd'hui.</p>
                </div>
            </div>

            <!-- Metric Cards Bento -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Revenue Card -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant hover:shadow-md transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2 bg-primary/10 rounded-lg">
                            <span class="material-symbols-outlined text-primary">payments</span>
                        </div>
                        <span class="text-green-600 font-label-xs text-label-xs flex items-center gap-1 bg-green-100 px-2 py-1 rounded-full">
                            <span class="material-symbols-outlined text-[12px]">trending_up</span> +12.5%
                        </span>
                    </div>
                    <p class="text-text-muted font-label-sm text-label-md">Revenu Total</p>
                    <h3 class="font-headline-md text-headline-md text-on-surface mt-1">$124,000</h3>
                </div>

                <!-- Students Card -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant hover:shadow-md transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2 bg-secondary/10 rounded-lg">
                            <span class="material-symbols-outlined text-secondary">group</span>
                        </div>
                        <span class="text-text-muted font-label-xs text-label-xs">Total Actif</span>
                    </div>
                    <p class="text-text-muted font-label-sm text-label-md">Étudiants Totaux</p>
                    <h3 class="font-headline-md text-headline-md text-on-surface mt-1">2,450</h3>
                </div>

                <!-- Schools Card -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant hover:shadow-md transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <span class="material-symbols-outlined text-yellow-600">domain</span>
                        </div>
                        <span class="text-text-muted font-label-xs text-label-xs">Sur 5 sites</span>
                    </div>
                    <p class="text-text-muted font-label-sm text-label-md">Établissements</p>
                    <h3 class="font-headline-md text-headline-md text-on-surface mt-1">1</h3>
                </div>

                <!-- Invoices Card -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant hover:shadow-md transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2 bg-red-100 rounded-lg">
                            <span class="material-symbols-outlined text-red-600">receipt_long</span>
                        </div>
                        <span class="text-red-600 font-label-xs text-label-xs flex items-center gap-1 bg-red-100 px-2 py-1 rounded-full">
                            Attention
                        </span>
                    </div>
                    <p class="text-text-muted font-label-sm text-label-md">Factures en attente</p>
                    <h3 class="font-headline-md text-headline-md text-on-surface mt-1">12</h3>
                </div>

                <!-- Teachers Card -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant hover:shadow-md transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2 bg-primary/10 rounded-lg">
                            <span class="material-symbols-outlined text-primary">person_4</span>
                        </div>
                    </div>
                    <p class="text-text-muted font-label-sm text-label-md">Enseignants</p>
                    <h3 class="font-headline-md text-headline-md text-on-surface mt-1">1</h3>
                </div>

                <!-- Subjects Card -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant hover:shadow-md transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2 bg-secondary/10 rounded-lg">
                            <span class="material-symbols-outlined text-secondary">menu_book</span>
                        </div>
                    </div>
                    <p class="text-text-muted font-label-sm text-label-md">Matières</p>
                    <h3 class="font-headline-md text-headline-md text-on-surface mt-1">3</h3>
                </div>

                <!-- Levels Card -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant hover:shadow-md transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <span class="material-symbols-outlined text-yellow-600">layers</span>
                        </div>
                    </div>
                    <p class="text-text-muted font-label-sm text-label-md">Niveaux</p>
                    <h3 class="font-headline-md text-headline-md text-on-surface mt-1">2</h3>
                </div>

                <!-- Classes Card -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant hover:shadow-md transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2 bg-red-100 rounded-lg">
                            <span class="material-symbols-outlined text-red-600">class</span>
                        </div>
                    </div>
                    <p class="text-text-muted font-label-sm text-label-md">Classes</p>
                    <h3 class="font-headline-md text-headline-md text-on-surface mt-1">0</h3>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
@endsection