@extends('sadmin.layouts.app')

@section('content')

<!-- Page Header with Back Button -->
<div class="mb-8">
    <div class="flex items-center gap-4 mb-2">
        <a href="{{ route('sadmin.notifications') }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-surface-container-lowest hover:bg-surface-container-high transition-all duration-200 group shadow-sm">
            <span class="material-symbols-outlined text-[18px] text-on-surface-variant group-hover:text-primary transition-colors">arrow_back</span>
        </a>
        <div>
            <h2 class="font-headline-lg text-[22px] text-primary mb-1">Historique des Notifications</h2>
            <p class="font-body-md text-[13px] text-text-muted">Consultez et analysez l'ensemble des communications envoyées sur la plateforme.</p>
        </div>
    </div>
</div>

<!-- Toolbar & Filters -->
<div class="bg-surface-container-lowest rounded-xl p-5 card-shadow mb-6">
    <div class="flex flex-wrap items-center gap-4">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-label-sm text-[11px] text-text-muted mb-1 ml-1">Mots-clés</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[18px]">filter_list</span>
                <input class="w-full border-outline-variant rounded-lg py-1.5 pl-9 pr-3 focus:border-primary focus:ring-primary/20 text-[13px]" placeholder="Sujet du message..." type="text">
            </div>
        </div>
        <div class="w-48">
            <label class="block text-label-sm text-[11px] text-text-muted mb-1 ml-1">Catégorie</label>
            <select class="w-full border-outline-variant rounded-lg py-1.5 px-3 focus:border-primary focus:ring-primary/20 text-[13px]">
                <option>Toutes</option>
                <option>Système</option>
                <option>Facturation</option>
                <option>Sécurité</option>
                <option>Académique</option>
            </select>
        </div>
        <div class="w-48">
            <label class="block text-label-sm text-[11px] text-text-muted mb-1 ml-1">Période</label>
            <select class="w-full border-outline-variant rounded-lg py-1.5 px-3 focus:border-primary focus:ring-primary/20 text-[13px]">
                <option>Derniers 30 jours</option>
                <option>7 derniers jours</option>
                <option>Aujourd'hui</option>
                <option>Personnalisé...</option>
            </select>
        </div>
        <div class="w-48">
            <label class="block text-label-sm text-[11px] text-text-muted mb-1 ml-1">Audience</label>
            <select class="w-full border-outline-variant rounded-lg py-1.5 px-3 focus:border-primary focus:ring-primary/20 text-[13px]">
                <option>Tous</option>
                <option>Parents</option>
                <option>Enseignants</option>
                <option>Élèves</option>
            </select>
        </div>
        <div class="pt-5">
            <button class="bg-surface-subtle text-primary font-label-md text-[12px] px-3 py-1.5 rounded-lg border border-outline-variant/30 hover:bg-surface-container-low transition-colors">
                Réinitialiser
            </button>
        </div>
    </div>
</div>

<!-- Main Data Table -->
<div class="bg-surface-container-lowest rounded-xl card-shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-bright border-b border-surface-container">
                    <th class="px-4 py-3 font-label-sm text-[11px] text-text-muted uppercase tracking-wider">Date d'envoi</th>
                    <th class="px-4 py-3 font-label-sm text-[11px] text-text-muted uppercase tracking-wider">Sujet</th>
                    <th class="px-4 py-3 font-label-sm text-[11px] text-text-muted uppercase tracking-wider">Catégorie</th>
                    <th class="px-4 py-3 font-label-sm text-[11px] text-text-muted uppercase tracking-wider">Audience</th>
                    <th class="px-4 py-3 font-label-sm text-[11px] text-text-muted uppercase tracking-wider">Statut</th>
                    <th class="px-4 py-3 font-label-sm text-[11px] text-text-muted uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-container">
                <!-- Row 1 -->
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-4 py-3 font-body-sm text-[12px] whitespace-nowrap">24 Oct 2023, 14:30</td>
                    <td class="px-4 py-3">
                        <div class="font-label-md text-[13px] text-primary truncate max-w-xs">Mise à jour majeure du portail académique</div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-primary/10 text-primary">Système</span>
                    </td>
                    <td class="px-4 py-3 font-body-sm text-[12px]">Tous</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-success-green/10 text-success-green">
                            <span class="w-1 h-1 rounded-full bg-success-green"></span>
                            Envoyé
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex justify-end gap-1">
                            <button class="p-1.5 hover:bg-surface-container-high rounded-full transition-colors" title="Détails">
                                <span class="material-symbols-outlined text-[18px] text-on-surface-variant">visibility</span>
                            </button>
                            <button class="p-1.5 hover:bg-surface-container-high rounded-full transition-colors" title="Renvoyer">
                                <span class="material-symbols-outlined text-[18px] text-on-surface-variant">replay</span>
                            </button>
                        </div>
                    </td>
                </tr>
                <!-- Row 2 -->
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-4 py-3 font-body-sm text-[12px] whitespace-nowrap">23 Oct 2023, 09:15</td>
                    <td class="px-4 py-3">
                        <div class="font-label-md text-[13px] text-primary truncate max-w-xs">Factures du trimestre T3 disponibles</div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-warning-amber/10 text-warning-amber">Facturation</span>
                    </td>
                    <td class="px-4 py-3 font-body-sm text-[12px]">Parents</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-success-green/10 text-success-green">
                            <span class="w-1 h-1 rounded-full bg-success-green"></span>
                            Envoyé
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex justify-end gap-1">
                            <button class="p-1.5 hover:bg-surface-container-high rounded-full transition-colors">
                                <span class="material-symbols-outlined text-[18px] text-on-surface-variant">visibility</span>
                            </button>
                            <button class="p-1.5 hover:bg-surface-container-high rounded-full transition-colors">
                                <span class="material-symbols-outlined text-[18px] text-on-surface-variant">replay</span>
                            </button>
                        </div>
                    </td>
                </tr>
                <!-- Row 3 -->
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-4 py-3 font-body-sm text-[12px] whitespace-nowrap">25 Oct 2023, 08:00</td>
                    <td class="px-4 py-3">
                        <div class="font-label-md text-[13px] text-primary truncate max-w-xs">Alerte : Maintenance de sécurité hebdomadaire</div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-alert-red/10 text-alert-red">Sécurité</span>
                    </td>
                    <td class="px-4 py-3 font-body-sm text-[12px]">Administrateurs</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-primary-container/10 text-primary-container">
                            <span class="w-1 h-1 rounded-full bg-primary-container"></span>
                            Programmé
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex justify-end gap-1">
                            <button class="p-1.5 hover:bg-surface-container-high rounded-full transition-colors">
                                <span class="material-symbols-outlined text-[18px] text-on-surface-variant">edit</span>
                            </button>
                            <button class="p-1.5 hover:bg-surface-container-high rounded-full transition-colors">
                                <span class="material-symbols-outlined text-[18px] text-alert-red">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                <!-- Row 4 -->
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-4 py-3 font-body-sm text-[12px] whitespace-nowrap">22 Oct 2023, 11:45</td>
                    <td class="px-4 py-3">
                        <div class="font-label-md text-[13px] text-primary truncate max-w-xs">Nouveau calendrier des examens</div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-success-green/10 text-success-green">Succès</span>
                    </td>
                    <td class="px-4 py-3 font-body-sm text-[12px]">Enseignants</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-alert-red/10 text-alert-red">
                            <span class="w-1 h-1 rounded-full bg-alert-red"></span>
                            Échec
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex justify-end gap-1">
                            <button class="p-1.5 hover:bg-surface-container-high rounded-full transition-colors">
                                <span class="material-symbols-outlined text-[18px] text-alert-red">delete</span>
                            </button>
                            <button class="p-1.5 hover:bg-surface-container-high rounded-full transition-colors">
                                <span class="material-symbols-outlined text-[18px] text-on-surface-variant">replay</span>
                            </button>
                        </div>
                    </td>
                 </tr>
            </tbody>
         </table>
    </div>
    <!-- Pagination -->
    <div class="px-4 py-3 bg-surface-bright border-t border-surface-container flex items-center justify-between">
        <p class="text-body-sm text-[11px] text-text-muted">Affichage de 1 à 10 sur 124 notifications</p>
        <div class="flex items-center gap-1">
            <button class="p-1.5 rounded-lg border border-outline-variant hover:bg-white transition-colors disabled:opacity-50" disabled>
                <span class="material-symbols-outlined text-[18px]">chevron_left</span>
            </button>
            <div class="flex items-center gap-1">
                <button class="w-7 h-7 rounded-lg bg-primary text-white font-label-md text-[12px] flex items-center justify-center">1</button>
                <button class="w-7 h-7 rounded-lg hover:bg-white font-label-md text-[12px] flex items-center justify-center transition-colors">2</button>
                <button class="w-7 h-7 rounded-lg hover:bg-white font-label-md text-[12px] flex items-center justify-center transition-colors">3</button>
                <span class="mx-1 text-text-muted text-[11px]">...</span>
                <button class="w-7 h-7 rounded-lg hover:bg-white font-label-md text-[12px] flex items-center justify-center transition-colors">13</button>
            </div>
            <button class="p-1.5 rounded-lg border border-outline-variant hover:bg-white transition-colors">
                <span class="material-symbols-outlined text-[18px]">chevron_right</span>
            </button>
        </div>
    </div>
</div>

@endsection