@extends('personnel.layouts.app')
@section('title', 'EduManager - Examen du lot de notes')
@section('content')

<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-3 mb-5">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <a href="{{ route('personnel.notes.index') }}" class="text-xs text-primary hover:underline flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
                Retour aux lots
            </a>
        </div>
        <h2 class="font-headline-md text-2xl text-primary mb-0.5">Examen des Notes — {{ $classe->nom }}</h2>
        <p class="text-text-muted text-xs">Matière : <strong class="text-primary">{{ $matiere->nom }}</strong> | Période : <strong class="text-primary">{{ strtoupper($periode) }}</strong></p>
    </div>
    
    <div class="flex items-center gap-2">
        <button type="button" class="px-3 py-1.5 bg-alert-red/10 text-alert-red hover:bg-alert-red hover:text-white rounded-lg text-xs font-bold transition-all flex items-center gap-1" onclick="openRejectModal({{ $classe->id }}, {{ $matiere->id }}, '{{ $periode }}')">
            <span class="material-symbols-outlined text-sm">close</span>
            Rejeter le lot
        </button>

        <form method="POST" action="{{ route('personnel.notes.approuver') }}" class="inline">
            @csrf
            <input type="hidden" name="classe_id" value="{{ $classe->id }}">
            <input type="hidden" name="matiere_id" value="{{ $matiere->id }}">
            <input type="hidden" name="periode" value="{{ $periode }}">
            <button type="submit" class="bg-success-green text-white text-xs px-4 py-1.5 rounded-lg font-bold hover:opacity-90 flex items-center gap-1.5 shadow-sm" onclick="return confirm('Confirmer l\'approbation de toutes les notes de ce lot ?');">
                <span class="material-symbols-outlined text-sm">check_circle</span>
                Approuver tout le lot
            </button>
        </form>
    </div>
</div>

<!-- Synthèse -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
    <div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant shadow-ambient">
        <span class="text-[10px] text-text-muted uppercase font-bold tracking-wider">Effectif de la classe</span>
        <p class="text-2xl font-bold text-primary mt-1">{{ count($elevesSummaries) }} élèves</p>
    </div>
    <div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant shadow-ambient">
        <span class="text-[10px] text-text-muted uppercase font-bold tracking-wider">Total des évaluations saisies</span>
        <p class="text-2xl font-bold text-primary mt-1">{{ $notes->count() }} notes</p>
    </div>
    <div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant shadow-ambient">
        <span class="text-[10px] text-text-muted uppercase font-bold tracking-wider">Moyenne générale de la classe</span>
        <p class="text-2xl font-bold {{ ($moyenneGeneraleClasse ?? 0) >= 10 ? 'text-success-green' : 'text-alert-red' }} mt-1">
            {{ $moyenneGeneraleClasse !== null ? number_format($moyenneGeneraleClasse, 2).'/20' : '—' }}
        </p>
    </div>
</div>

<!-- Tableau détaillé des élèves & moyennes -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-ambient overflow-hidden mb-5">
    <div class="p-4 border-b border-outline-variant bg-surface-container-low/30">
        <h3 class="font-headline-sm text-sm text-primary">Détail des notes et calcul des moyennes par élève</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
            <thead class="bg-surface-container-low border-b border-outline-variant">
                <tr>
                    <th class="px-4 py-2.5 text-[10px] uppercase text-text-muted tracking-wider">Élève</th>
                    <th class="px-4 py-2.5 text-[10px] uppercase text-text-muted tracking-wider">Matricule</th>
                    <th class="px-4 py-2.5 text-[10px] uppercase text-text-muted tracking-wider">Notes saisies</th>
                    <th class="px-4 py-2.5 text-[10px] uppercase text-text-muted tracking-wider">Moyenne calculée</th>
                    <th class="px-4 py-2.5 text-[10px] uppercase text-text-muted tracking-wider">Appréciation</th>
                    <th class="px-4 py-2.5 text-[10px] uppercase text-text-muted tracking-wider">Statuts</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/50">
                @forelse($elevesSummaries as $item)
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-4 py-3 font-semibold">{{ $item['eleve']->nom }} {{ $item['eleve']->prenom }}</td>
                    <td class="px-4 py-3 text-text-muted font-mono">{{ $item['eleve']->matricule }}</td>
                    <td class="px-4 py-3">
                        @if($item['notes']->isNotEmpty())
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($item['notes'] as $n)
                                <span class="px-2 py-0.5 rounded bg-surface-container border text-[11px] font-bold {{ $n->note >= 10 ? 'text-success-green border-success-green/30' : 'text-alert-red border-alert-red/30' }}" title="{{ $n->titre_evaluation ?? 'Évaluation' }} ({{ $n->type_evaluation }})">
                                    {{ number_format($n->note, 2) }}
                                </span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-text-muted italic text-[11px]">Aucune note</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 font-bold text-sm">
                        @if($item['moyenne'] !== null)
                            <span class="{{ $item['moyenne'] >= 10 ? 'text-success-green' : 'text-alert-red' }}">
                                {{ number_format($item['moyenne'], 2) }}/20
                            </span>
                        @else
                            <span class="text-text-muted italic text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-text-muted font-medium">{{ $item['appreciation'] }}</td>
                    <td class="px-4 py-3">
                        @php
                            $uniqueStatuts = $item['notes']->pluck('statut')->unique();
                        @endphp
                        @foreach($uniqueStatuts as $st)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-surface-container">
                                {{ ucfirst($st) }}
                            </span>
                        @endforeach
                    </td>
                </tr>
                @empty
                <tr>
                    <td class="py-6 text-center text-text-muted" colspan="6">Aucun élève trouvé dans cette classe.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal de Rejet -->
<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="rejectModal">
    <div class="absolute inset-0 modal-overlay backdrop-blur-sm bg-black/40" onclick="closeRejectModal()"></div>
    <div class="bg-surface-container-lowest w-full max-w-md rounded-xl shadow-2xl border border-outline-variant overflow-hidden transform transition-all duration-300 relative z-10 p-6">
        <div class="flex items-center gap-2 text-alert-red mb-3">
            <span class="material-symbols-outlined text-2xl">error</span>
            <h3 class="font-headline-md text-base font-bold">Rejeter les notes</h3>
        </div>
        <p class="text-xs text-text-muted mb-4">
            Veuillez indiquer le motif du rejet pour l'enseignant.
        </p>

        <form method="POST" action="{{ route('personnel.notes.rejeter') }}">
            @csrf
            <input type="hidden" name="classe_id" value="{{ $classe->id }}">
            <input type="hidden" name="matiere_id" value="{{ $matiere->id }}">
            <input type="hidden" name="periode" value="{{ $periode }}">

            <div class="mb-4">
                <label class="block text-xs font-semibold mb-1">Motif de rejet (obligatoire)</label>
                <textarea name="rejet_motif" rows="3" class="w-full bg-surface rounded-lg border-outline-variant text-xs p-2.5" placeholder="Ex: Incohérence constatée sur les notes de devoir..." required></textarea>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" class="px-4 py-2 text-xs font-medium text-on-surface-variant hover:bg-surface-container rounded-lg" onclick="closeRejectModal()">Annuler</button>
                <button type="submit" class="bg-alert-red text-white px-4 py-2 rounded-lg text-xs font-bold hover:opacity-90">Confirmer le rejet</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('rejectModal').classList.add('flex');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.remove('flex');
    document.getElementById('rejectModal').classList.add('hidden');
}
</script>
@endpush
