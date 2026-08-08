@extends('parent.layouts.app')

@section('title', 'Scolarité de '.$eleve->nom.' '.$eleve->prenom)

@section('content')
@php
    $situation = $situation ?? [];
    $montantTotal = $situation['montant_total'] ?? 0;
    $montantPaye = $situation['montant_paye'] ?? 0;
    $reste = $situation['reste'] ?? 0;
    $statut = $situation['statut'] ?? 'impaye';
    $pourcentage = $situation['pourcentage'] ?? 0;
    $versements = $situation['versements'] ?? collect();
    $anneeScolaire = $situation['annee_scolaire'] ?? now()->year.'-'.now()->addYear()->year;
    $badge = [
        'paye' => 'bg-success-green/10 text-success-green',
        'partiel' => 'bg-warning-amber/10 text-warning-amber',
        'impaye' => 'bg-alert-red/10 text-alert-red',
    ];
    $statutLabel = [
        'paye' => 'SCOLARITÉ SOLDÉE',
        'partiel' => 'Partiellement payé',
        'impaye' => 'Non payé',
    ];
@endphp

<div class="mb-6 flex items-center justify-between gap-3 flex-wrap">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-primary">Scolarité</h2>
        <p class="text-sm text-on-surface-variant">Consultation des informations scolaires de {{ $eleve->nom }} {{ $eleve->prenom }}</p>
    </div>
    <a class="inline-flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-primary rounded-lg" href="{{ route('parent.enfants') }}">
        <span class="material-symbols-outlined text-lg">arrow_back</span> Retour
    </a>
</div>

<!-- Informations générales -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant p-6">
        <h4 class="font-headline-md text-headline-md mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">badge</span> Identité
        </h4>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between"><span class="text-text-muted">Nom</span><span class="font-medium">{{ $eleve->nom }}</span></div>
            <div class="flex justify-between"><span class="text-text-muted">Prénom</span><span class="font-medium">{{ $eleve->prenom }}</span></div>
            <div class="flex justify-between"><span class="text-text-muted">Matricule</span><span class="font-medium">{{ $eleve->matricule }}</span></div>
            <div class="flex justify-between"><span class="text-text-muted">Sexe</span><span class="font-medium">{{ $eleve->sexe }}</span></div>
            <div class="flex justify-between"><span class="text-text-muted">Date de naissance</span><span class="font-medium">{{ $eleve->date_naissance?->format('d/m/Y') ?? '—' }}</span></div>
            <div class="flex justify-between"><span class="text-text-muted">Nationalité</span><span class="font-medium">{{ $eleve->nationalite ?? '—' }}</span></div>
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant p-6">
        <h4 class="font-headline-md text-headline-md mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">school</span> Parcours
        </h4>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between"><span class="text-text-muted">Établissement</span><span class="font-medium">{{ $situation['etablissement']?->nom ?? $eleve->etablissement?->nom ?? '—' }}</span></div>
            <div class="flex justify-between"><span class="text-text-muted">Année académique</span><span class="font-medium">{{ $anneeScolaire }}</span></div>
            <div class="flex justify-between"><span class="text-text-muted">Classe</span><span class="font-medium">{{ $situation['classe']?->nom ?? $eleve->classe?->nom ?? '—' }}</span></div>
            <div class="flex justify-between"><span class="text-text-muted">Niveau</span><span class="font-medium">{{ $situation['niveau']?->nom ?? $eleve->niveau?->nom ?? $eleve->classe?->niveau?->nom ?? '—' }}</span></div>
            <div class="flex justify-between"><span class="text-text-muted">Série / Filière</span><span class="font-medium">{{ $situation['serie']?->nom_serie ?? $eleve->serie?->nom_serie ?? '—' }}</span></div>
            <div class="flex justify-between"><span class="text-text-muted">Statut</span><span class="font-medium">{{ ucfirst($eleve->statut) }}</span></div>
        </div>
    </div>
</div>

<!-- Situation de scolarité calculée -->
<div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant overflow-hidden mb-6">
    <div class="p-6 border-b border-outline-variant flex items-center justify-between gap-3 flex-wrap">
        <h4 class="font-headline-md text-headline-md flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">payments</span> Situation de scolarité
        </h4>
        <span class="px-3 py-1 rounded-full text-label-sm {{ $badge[$statut] ?? $badge['impaye'] }}">{{ $statutLabel[$statut] ?? ucfirst($statut) }}</span>
    </div>
    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-surface-container-low rounded-xl p-5 text-center">
            <p class="text-label-sm text-on-surface-variant uppercase tracking-wider">Frais de scolarité</p>
            <p class="font-headline-md text-headline-md text-primary mt-2">{{ number_format($montantTotal, 0, ',', ' ') }} FCFA</p>
        </div>
        <div class="bg-surface-container-low rounded-xl p-5 text-center">
            <p class="text-label-sm text-on-surface-variant uppercase tracking-wider">Montant payé</p>
            <p class="font-headline-md text-headline-md text-success-green mt-2">{{ number_format($montantPaye, 0, ',', ' ') }} FCFA</p>
        </div>
        <div class="bg-surface-container-low rounded-xl p-5 text-center">
            <p class="text-label-sm text-on-surface-variant uppercase tracking-wider">Reste à payer</p>
            <p class="font-headline-md text-headline-md {{ $reste > 0 ? 'text-alert-red' : 'text-success-green' }} mt-2">{{ number_format($reste, 0, ',', ' ') }} FCFA</p>
        </div>
    </div>
    @if($montantTotal > 0)
    <div class="px-6 pb-6">
        <div class="flex justify-between text-sm mb-1.5">
            <span class="text-on-surface-variant">{{ $montantPaye }} / {{ $montantTotal }} FCFA</span>
            <span class="font-semibold">{{ $pourcentage }}% payé</span>
        </div>
        <div class="w-full bg-outline-variant/40 rounded-full h-3 overflow-hidden">
            <div class="h-3 rounded-full bg-success-green transition-all" style="width: {{ $pourcentage }}%"></div>
        </div>
    </div>
    @endif
</div>

<!-- Historique des paiements -->
<div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant overflow-hidden">
    <div class="p-6 border-b border-outline-variant">
        <h4 class="font-headline-md text-headline-md flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">receipt_long</span> Historique des paiements
        </h4>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-surface-container-low border-b border-outline-variant">
                <tr>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Date</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Montant</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Mode</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Référence</th>
                </tr>
            </thead>
            <tbody>
                @forelse($versements as $versement)
                <tr class="border-b border-outline-variant/50 hover:bg-surface-container-low transition-colors">
                    <td class="px-6 py-4 text-body-sm">{{ $versement->date_versement?->format('d/m/Y') ?? $versement->created_at?->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-body-sm text-success-green font-semibold">{{ number_format($versement->montant, 0, ',', ' ') }} FCFA</td>
                    <td class="px-6 py-4 text-body-sm">{{ $versement->methode ?? '—' }}</td>
                    <td class="px-6 py-4 text-body-sm">{{ $versement->reference ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-10 px-6 text-center text-on-surface-variant">Aucun paiement enregistré pour cet enfant.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
