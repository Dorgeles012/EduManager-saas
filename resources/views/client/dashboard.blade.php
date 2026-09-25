@extends('client.layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduManager - Dashboard</title>
    <style>
        .dashboard-content {
            height: calc(100vh - 80px);
            overflow-y: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .dashboard-content::-webkit-scrollbar {
            display: none;
        }
        @media (max-width: 768px) {
            .dashboard-content {
                height: calc(100vh - 60px);
            }
        }
    </style>
</head>
<body class="font-body-sm text-body-sm h-screen overflow-hidden">

<div class="dashboard-content">
    <div class="p-4 sm:p-6 space-y-6">

        @php
            $subscription = app(\App\Services\SubscriptionStatusService::class)
                ->subscriptionForUser(auth()->user());
        @endphp

        @if (! $subscription)
            <div class="rounded-2xl border border-indigo-100 bg-gradient-to-r from-indigo-50 to-white p-5 shadow-sm">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600 flex-shrink-0">
                            <span class="material-symbols-outlined text-xl">info</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Aucun abonnement actif</p>
                            <p class="text-xs text-gray-500 mt-0.5">Choisissez un abonnement pour commencer à utiliser EduManager.</p>
                        </div>
                    </div>
                    <a href="{{ route('client.abonnement.index') }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 px-4 py-2 text-xs font-semibold text-white transition shadow-sm">
                        <span class="material-symbols-outlined text-sm">rocket_launch</span>
                        Voir les abonnements
                    </a>
                </div>
            </div>
        @endif

        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Tableau de bord</h2>
            <p class="text-sm text-gray-500 mt-1">Bienvenue, voici la performance de votre réseau aujourd'hui.</p>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                        <span class="material-symbols-outlined text-lg">payments</span>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Revenu</span>
                </div>
                <h3 class="text-xl sm:text-2xl font-bold text-gray-900 truncate">{{ number_format($counts['revenu_total'] ?? 0, 0, ',', ' ') }}</h3>
                <p class="text-xs text-gray-500 mt-0.5">FCFA total</p>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                        <span class="material-symbols-outlined text-lg">group</span>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Actif</span>
                </div>
                <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $counts['eleves'] ?? 0 }}</h3>
                <p class="text-xs text-gray-500 mt-0.5">Étudiants totaux</p>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                        <span class="material-symbols-outlined text-lg">domain</span>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Réseau</span>
                </div>
                <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $counts['etablissements'] ?? 0 }}</h3>
                <p class="text-xs text-gray-500 mt-0.5">Établissements</p>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-9 h-9 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600">
                        <span class="material-symbols-outlined text-lg">receipt_long</span>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Finance</span>
                </div>
                <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $counts['factures_impayees'] ?? 0 }}</h3>
                <p class="text-xs text-gray-500 mt-0.5">Factures en attente</p>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-9 h-9 rounded-xl bg-violet-50 flex items-center justify-center text-violet-600">
                        <span class="material-symbols-outlined text-lg">person_4</span>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Équipe</span>
                </div>
                <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $counts['enseignants'] ?? 0 }}</h3>
                <p class="text-xs text-gray-500 mt-0.5">Enseignants</p>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-9 h-9 rounded-xl bg-teal-50 flex items-center justify-center text-teal-600">
                        <span class="material-symbols-outlined text-lg">menu_book</span>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Cursus</span>
                </div>
                <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $counts['matieres'] ?? 0 }}</h3>
                <p class="text-xs text-gray-500 mt-0.5">Matières</p>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-9 h-9 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600">
                        <span class="material-symbols-outlined text-lg">layers</span>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Structure</span>
                </div>
                <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $counts['niveaux'] ?? 0 }}</h3>
                <p class="text-xs text-gray-500 mt-0.5">Niveaux</p>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-9 h-9 rounded-xl bg-pink-50 flex items-center justify-center text-pink-600">
                        <span class="material-symbols-outlined text-lg">class</span>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Salles</span>
                </div>
                <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $counts['classes'] ?? 0 }}</h3>
                <p class="text-xs text-gray-500 mt-0.5">Classes</p>
            </div>
        </div>

    </div>
</div>

</body>
</html>
@endsection