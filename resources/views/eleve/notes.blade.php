@extends('eleve.layouts.app')

@section('title', 'Mes notes')

@section('content')
<div class="mb-6">
    <h2 class="font-headline-lg text-headline-lg text-primary">Mes notes & évaluations</h2>
    <p class="text-sm text-on-surface-variant">Consultation de vos notes officielles validées et publiées.</p>
</div>

<!-- Filtres -->
<div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant p-4 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4" action="{{ route('eleve.notes') }}">
        <div class="space-y-1">
            <label class="font-label-md text-label-md text-on-surface-variant">Année académique</label>
            <select name="annee_academique_id" class="w-full bg-white border border-outline-variant rounded-lg px-4 py-2 text-body-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none" onchange="this.form.submit()">
                <option value="">Toutes les années</option>
                @foreach($years as $year)
                <option value="{{ $year->id }}" {{ $selectedAnnee == $year->id ? 'selected' : '' }}>{{ $year->libelle }}</option>
                @endforeach
            </select>
        </div>
        <div class="space-y-1">
            <label class="font-label-md text-label-md text-on-surface-variant">Période</label>
            <select name="periode" class="w-full bg-white border border-outline-variant rounded-lg px-4 py-2 text-body-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none" onchange="this.form.submit()">
                <option value="">Toutes les périodes</option>
                <option value="t1" {{ $selectedPeriode == 't1' ? 'selected' : '' }}>1er Trimestre</option>
                <option value="t2" {{ $selectedPeriode == 't2' ? 'selected' : '' }}>2ème Trimestre</option>
                <option value="t3" {{ $selectedPeriode == 't3' ? 'selected' : '' }}>3ème Trimestre</option>
            </select>
        </div>
        <div class="space-y-1">
            <label class="font-label-md text-label-md text-on-surface-variant">Matière</label>
            <select name="matiere_id" class="w-full bg-white border border-outline-variant rounded-lg px-4 py-2 text-body-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none" onchange="this.form.submit()">
                <option value="">Toutes les matières</option>
                @foreach($matieres as $matiere)
                <option value="{{ $matiere->id }}" {{ $selectedMatiere == $matiere->id ? 'selected' : '' }}>{{ $matiere->nom }}</option>
                @endforeach
            </select>
        </div>
        <div class="space-y-1 flex items-end">
            <a href="{{ route('eleve.notes') }}" class="w-full bg-surface-variant text-on-surface px-4 py-2 rounded-lg font-label-md hover:bg-outline-variant/30 text-center transition-colors">Réinitialiser</a>
        </div>
    </form>
</div>

<!-- Synthèse des Moyennes de la Période (si période sélectionnée) -->
@if($bilanPeriode && $bilanPeriode['moyenne_generale'] !== null)
<div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant p-5 mb-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-4 border-b border-outline-variant/40">
        <div>
            <h3 class="font-headline-md text-base text-primary font-bold">Moyenne Générale calculée — {{ strtoupper($selectedPeriode) }}</h3>
            <p class="text-xs text-text-muted">Calculée à partir de toutes vos notes publiées et coefficients des matières.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="text-right">
                <span class="text-2xl font-bold {{ $bilanPeriode['moyenne_generale'] >= 10 ? 'text-success-green' : 'text-alert-red' }}">
                    {{ number_format($bilanPeriode['moyenne_generale'], 2, ',', ' ') }}/20
                </span>
                <span class="block text-xs font-semibold text-text-muted">{{ $bilanPeriode['mention'] }}</span>
            </div>
        </div>
    </div>

    <!-- Moyennes par matière -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 mt-4">
        @foreach($bilanPeriode['disciplines'] as $d)
        @if($d['moyenne'] !== null)
        <div class="p-3 rounded-lg bg-surface-container-low border border-outline-variant/30 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-primary">{{ $d['discipline'] }}</p>
                <p class="text-[10px] text-text-muted">Coef. {{ $d['coefficient'] }} • {{ count($d['notes_list'] ?? []) }} note(s)</p>
            </div>
            <div class="text-right">
                <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $d['moyenne'] >= 10 ? 'bg-success-green/10 text-success-green' : 'bg-alert-red/10 text-alert-red' }}">
                    {{ number_format($d['moyenne'], 2, ',', ' ') }}
                </span>
            </div>
        </div>
        @endif
        @endforeach
    </div>
</div>
@endif

<!-- Tableau des notes individuelles -->
<div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant overflow-hidden">
    <div class="p-4 border-b border-outline-variant bg-surface-container-low/30">
        <h3 class="font-headline-sm text-sm text-primary font-semibold">Toutes mes notes publiées</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
            <thead class="bg-surface-container-low border-b border-outline-variant">
                <tr>
                    <th class="px-5 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider text-[11px]">Matière</th>
                    <th class="px-5 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider text-[11px]">Évaluation</th>
                    <th class="px-5 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider text-[11px]">Enseignant</th>
                    <th class="px-5 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider text-[11px]">Note /20</th>
                    <th class="px-5 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider text-[11px]">Appréciation</th>
                    <th class="px-5 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider text-[11px]">Période</th>
                </tr>
            </thead>
            <tbody>
                @forelse($notes as $note)
                <tr class="border-b border-outline-variant/50 hover:bg-surface-container-low transition-colors">
                    <td class="px-5 py-3">
                        <span class="font-semibold text-primary">{{ $note->matiere?->nom ?? '—' }}</span>
                    </td>
                    <td class="px-5 py-3">
                        <span class="font-medium text-on-surface">{{ $note->titre_evaluation ?? 'Évaluation' }}</span>
                        <span class="block text-[10px] text-text-muted uppercase">{{ $note->type_evaluation ?? 'devoir' }}</span>
                    </td>
                    <td class="px-5 py-3 text-on-surface-variant">{{ $note->enseignant_label ?? '—' }}</td>
                    <td class="px-5 py-3">
                        @php
                            $noteValue = (float) $note->note;
                            $badge = $noteValue >= 10 ? 'bg-success-green/10 text-success-green' : 'bg-alert-red/10 text-alert-red';
                        @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $badge }}">{{ number_format($noteValue, 2, ',', ' ') }}</span>
                    </td>
                    <td class="px-5 py-3 text-on-surface-variant">{{ $note->appreciation }}</td>
                    <td class="px-5 py-3 font-semibold uppercase text-primary">{{ $note->periode ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-10 px-6 text-center">
                        <div class="flex flex-col items-center max-w-xs mx-auto">
                            <div class="w-16 h-16 bg-surface-container rounded-full flex items-center justify-center mb-3">
                                <span class="material-symbols-outlined text-3xl text-outline-variant">fact_check</span>
                            </div>
                            <h5 class="font-headline-md text-sm text-on-surface mb-1">Aucune note publiée disponible</h5>
                            <p class="text-xs text-text-muted text-center">Vos notes apparaîtront ici dès leur validation et publication par la Direction.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
