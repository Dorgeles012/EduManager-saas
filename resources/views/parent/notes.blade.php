@extends('parent.layouts.app')

@section('title', 'Notes de '.$eleve->nom.' '.$eleve->prenom)

@section('content')
<div class="mb-6 flex items-center justify-between gap-3 flex-wrap">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-primary">Notes</h2>
        <p class="text-sm text-on-surface-variant">Notes de {{ $eleve->nom }} {{ $eleve->prenom }} ({{ $eleve->classe?->nom ?? '—' }})</p>
    </div>
    <a class="inline-flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-primary rounded-lg" href="{{ route('parent.enfants') }}">
        <span class="material-symbols-outlined text-lg">arrow_back</span> Retour
    </a>
</div>

<!-- Filtres -->
<div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant p-4 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4" action="{{ route('parent.enfant.notes', $eleve) }}">
        <div class="space-y-1">
            <label class="font-label-md text-label-md text-on-surface-variant">Période</label>
            <select name="periode" class="w-full bg-white border border-outline-variant rounded-lg px-4 py-2.5 text-body-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none">
                <option value="">Toutes les périodes</option>
                @foreach($periodes as $periode)
                <option value="{{ $periode }}" {{ $selectedPeriode == $periode ? 'selected' : '' }}>{{ $periode }}</option>
                @endforeach
            </select>
        </div>
        <div class="space-y-1">
            <label class="font-label-md text-label-md text-on-surface-variant">Matière</label>
            <select name="matiere_id" class="w-full bg-white border border-outline-variant rounded-lg px-4 py-2.5 text-body-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none">
                <option value="">Toutes les matières</option>
                @foreach($matieres as $matiere)
                <option value="{{ $matiere->id }}" {{ $selectedMatiere == $matiere->id ? 'selected' : '' }}>{{ $matiere->nom }}</option>
                @endforeach
            </select>
        </div>
        <div class="space-y-1 flex items-end">
            <button type="submit" class="w-full bg-primary text-on-primary px-4 py-2.5 rounded-lg font-label-md hover:bg-primary/90 transition-colors">Filtrer</button>
        </div>
    </form>
</div>

<!-- Tableau des notes -->
<div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-surface-container-low border-b border-outline-variant">
                <tr>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Matière</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Enseignant</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Note /20</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Coefficient</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Appréciation</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Période</th>
                </tr>
            </thead>
            <tbody>
                @forelse($notes as $note)
                <tr class="border-b border-outline-variant/50 hover:bg-surface-container-low transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-primary-fixed flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-[18px]">menu_book</span>
                            </div>
                            <span class="font-body-md font-medium">{{ $note->matiere?->nom ?? '—' }}</span>
                        </div>
                    </td>
<td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $note->matiere && $note->matiere->enseignants->isNotEmpty() ? trim($note->matiere->enseignants->first()->prenoms.' '.$note->matiere->enseignants->first()->nom) : '—' }}</td>
                    <td class="px-6 py-4">
                        @php
                            $noteValue = (float) $note->note;
                            $badge = $noteValue >= 10 ? 'bg-success-green/10 text-success-green' : 'bg-alert-red/10 text-alert-red';
                        @endphp
                        <span class="px-3 py-1 rounded-full text-label-sm font-bold {{ $badge }}">{{ number_format($noteValue, 2, ',', ' ') }}</span>
                    </td>
                    <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $note->matiere?->coefficient ?? '—' }}</td>
                    <td class="px-6 py-4 text-body-sm">{{ $note->appreciation }}</td>
                    <td class="px-6 py-4 text-body-sm">{{ $note->periode ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-10 px-6 text-center">
                        <div class="flex flex-col items-center max-w-xs mx-auto">
                            <div class="w-20 h-20 bg-surface-container rounded-full flex items-center justify-center mb-6">
                                <span class="material-symbols-outlined text-5xl text-outline-variant">fact_check</span>
                            </div>
                            <h5 class="font-headline-md text-headline-md text-on-surface mb-2">Aucune note disponible</h5>
                            <p class="text-sm text-text-muted text-center">Les notes de votre enfant apparaîtront ici dès leur saisie.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
