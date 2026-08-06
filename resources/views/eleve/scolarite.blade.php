@extends('eleve.layouts.app')

@section('title', 'Ma scolarité')

@section('content')
<div class="mb-6">
    <h2 class="font-headline-lg text-headline-lg text-primary">Ma scolarité</h2>
    <p class="text-sm text-on-surface-variant">Consultation de mes informations scolaires (lecture seule)</p>
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
            <div class="flex justify-between"><span class="text-text-muted">Établissement</span><span class="font-medium">{{ $eleve->etablissement?->nom ?? '—' }}</span></div>
            <div class="flex justify-between"><span class="text-text-muted">Année académique</span><span class="font-medium">{{ $anneeActive?->libelle ?? now()->year.'-'.now()->addYear()->year }}</span></div>
            <div class="flex justify-between"><span class="text-text-muted">Classe</span><span class="font-medium">{{ $eleve->classe?->nom ?? '—' }}</span></div>
            <div class="flex justify-between"><span class="text-text-muted">Niveau</span><span class="font-medium">{{ $eleve->niveau?->nom ?? $eleve->classe?->niveau?->nom ?? '—' }}</span></div>
            <div class="flex justify-between"><span class="text-text-muted">Série / Filière</span><span class="font-medium">{{ $eleve->serie?->nom_serie ?? '—' }}</span></div>
            <div class="flex justify-between"><span class="text-text-muted">Statut</span><span class="font-medium">{{ ucfirst($eleve->statut) }}</span></div>
        </div>
    </div>
</div>

<!-- Scolarité / paiement -->
<div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant overflow-hidden">
    <div class="p-6 border-b border-outline-variant">
        <h4 class="font-headline-md text-headline-md flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">payments</span> Situation de scolarité
        </h4>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-surface-container-low border-b border-outline-variant">
                <tr>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Année scolaire</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Montant total</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Montant payé</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Reste</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Statut</th>
                </tr>
            </thead>
            <tbody>
                @forelse($scolarites as $scolarite)
                <tr class="border-b border-outline-variant/50 hover:bg-surface-container-low transition-colors">
                    <td class="px-6 py-4 text-body-sm">{{ $scolarite->annee_scolaire ?? '—' }}</td>
                    <td class="px-6 py-4 text-body-sm">{{ number_format($scolarite->montant_total, 0, ',', ' ') }} FCFA</td>
                    <td class="px-6 py-4 text-body-sm text-success-green">{{ number_format($scolarite->montant_paye, 0, ',', ' ') }} FCFA</td>
                    <td class="px-6 py-4 text-body-sm text-alert-red">{{ number_format($scolarite->reste, 0, ',', ' ') }} FCFA</td>
                    <td class="px-6 py-4">
                        @php
                            $badge = ['paye' => 'bg-success-green/10 text-success-green', 'partiel' => 'bg-warning-amber/10 text-warning-amber', 'impaye' => 'bg-alert-red/10 text-alert-red'];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-label-sm {{ $badge[$scolarite->statut] ?? 'bg-surface-container text-on-surface-variant' }}">{{ ucfirst($scolarite->statut) }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-10 px-6 text-center text-on-surface-variant">Aucune information de scolarité disponible.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
