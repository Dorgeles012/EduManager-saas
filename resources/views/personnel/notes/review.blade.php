@extends('personnel.layouts.app')
@section('title', 'EduManager - Examen du lot de notes')
@section('content')

{{-- En-tête --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-3 mb-5">
    <div>
        <a href="{{ route('personnel.notes.index') }}" class="inline-flex items-center gap-1 text-xs text-slate-500 ">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Retour aux lots
        </a>
        <h2 class="text-2xl font-semibold text-primary mb-0.5-800 tracking-tight">Examen des Notes — {{ $classe->nom }}</h2>
        <p class="text-slate-500 text-sm mt-1">
            Matière : <strong class="text-primary mb-0.5-600 font-semibold">{{ $matiere->nom }}</strong>
            <span class="mx-1.5 text-slate-300">|</span>
            Période : <strong class="text-primary mb-0.5-600 font-semibold">{{ strtoupper($periode) }}</strong>
        </p>
    </div>
</div>

{{-- Synthèse --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">

    {{-- Effectif --}}
    <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm hover:shadow-md transition-all">
        <div class="flex items-center gap-2.5 mb-3">
            <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-primary mb-0.5-500 text-lg">group</span>
            </div>
            <p class="text-slate-500 text-xs font-medium uppercase tracking-wide">Effectif de la classe</p>
        </div>
        <h3 class="text-xl font-bold text-slate-800 leading-tight">
            {{ count($elevesSummaries) }}
            <span class="text-sm font-semibold text-slate-400">élèves</span>
        </h3>
    </div>

    {{-- Total évaluations --}}
    <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm hover:shadow-md transition-all">
        <div class="flex items-center gap-2.5 mb-3">
            <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-emerald-500 text-lg">rule</span>
            </div>
            <p class="text-slate-500 text-xs font-medium uppercase tracking-wide">Évaluations saisies</p>
        </div>
        <h3 class="text-xl font-bold text-slate-800 leading-tight">
            {{ $notes->count() }}
            <span class="text-sm font-semibold text-slate-400">notes</span>
        </h3>
    </div>

    {{-- Moyenne générale --}}
    <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm hover:shadow-md transition-all">
        <div class="flex items-center gap-2.5 mb-3">
            <div class="w-9 h-9 rounded-lg {{ ($moyenneGeneraleClasse ?? 0) >= 10 ? 'bg-emerald-50' : 'bg-rose-50' }} flex items-center justify-center">
                <span class="material-symbols-outlined {{ ($moyenneGeneraleClasse ?? 0) >= 10 ? 'text-emerald-500' : 'text-rose-500' }} text-lg">trending_up</span>
            </div>
            <p class="text-slate-500 text-xs font-medium uppercase tracking-wide">Moyenne de la classe</p>
        </div>
        <h3 class="text-xl font-bold leading-tight {{ ($moyenneGeneraleClasse ?? 0) >= 10 ? 'text-emerald-600' : 'text-rose-600' }}">
            {{ $moyenneGeneraleClasse !== null ? number_format($moyenneGeneraleClasse, 2).'/20' : '—' }}
        </h3>
    </div>
</div>

{{-- Tableau détaillé --}}
<div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden mb-5">
    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
        <h3 class="text-base font-semibold text-slate-700 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary mb-0.5-500 text-lg">analytics</span>
            Détail des notes et calcul des moyennes par élève
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-4 py-3 text-xs uppercase text-slate-500 font-semibold tracking-wide">Élève</th>
                    <th class="px-4 py-3 text-xs uppercase text-slate-500 font-semibold tracking-wide">Matricule</th>
                    <th class="px-4 py-3 text-xs uppercase text-slate-500 font-semibold tracking-wide">Notes saisies</th>
                    <th class="px-4 py-3 text-xs uppercase text-slate-500 font-semibold tracking-wide">Moyenne</th>
                    <th class="px-4 py-3 text-xs uppercase text-slate-500 font-semibold tracking-wide">Appréciation</th>
                    <th class="px-4 py-3 text-xs uppercase text-slate-500 font-semibold tracking-wide">Statuts</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($elevesSummaries as $item)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 font-semibold text-slate-800">
                        {{ $item['eleve']->nom }} {{ $item['eleve']->prenom }}
                    </td>
                    <td class="px-4 py-3 text-slate-500 font-mono text-xs">{{ $item['eleve']->matricule }}</td>
                    <td class="px-4 py-3">
                        @if($item['notes']->isNotEmpty())
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($item['notes'] as $n)
                                <span class="px-2 py-1 rounded-lg bg-slate-50 border text-xs font-semibold {{ $n->note >= 10 ? 'text-emerald-600 border-emerald-200' : 'text-rose-600 border-rose-200' }}" title="{{ $n->titre_evaluation ?? 'Évaluation' }} ({{ $n->type_evaluation }})">
                                    {{ number_format($n->note, 2) }}
                                </span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-slate-400 italic text-xs">Aucune note</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($item['moyenne'] !== null)
                            <span class="font-bold text-sm {{ $item['moyenne'] >= 10 ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ number_format($item['moyenne'], 2) }}/20
                            </span>
                        @else
                            <span class="text-slate-400 italic text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-slate-600 font-medium text-xs">{{ $item['appreciation'] }}</td>
                    <td class="px-4 py-3">
                        @php
                            $uniqueStatuts = $item['notes']->pluck('statut')->unique();
                        @endphp
                        <div class="flex flex-wrap gap-1">
                            @forelse($uniqueStatuts as $st)
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                    @if($st === 'soumis') bg-amber-50 text-amber-700 border border-amber-200
                                    @elseif($st === 'approuve_personnel') bg-indigo-50 text-primary mb-0.5-700 border border-indigo-200
                                    @elseif($st === 'publie') bg-emerald-50 text-emerald-700 border border-emerald-200
                                    @elseif($st === 'rejete_personnel' || $st === 'rejete') bg-rose-50 text-rose-700 border border-rose-200
                                    @else bg-slate-100 text-slate-600 border border-slate-200
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $st)) }}
                                </span>
                            @empty
                                <span class="text-slate-400 italic text-xs">—</span>
                            @endforelse
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td class="py-12 text-center" colspan="6">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center mb-3">
                                <span class="material-symbols-outlined text-2xl text-slate-400">person_off</span>
                            </div>
                            <p class="text-sm font-semibold text-slate-700">Aucun élève trouvé</p>
                            <p class="text-xs text-slate-400 mt-1">Cette classe ne contient aucun élève inscrit.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal de Rejet --}}
<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="rejectModal">
    <div class="absolute inset-0 backdrop-blur-sm bg-black/40" onclick="closeRejectModal()"></div>
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl border border-slate-100 overflow-hidden relative z-10 p-6">
        <div class="flex items-center gap-2.5 text-rose-600 mb-3">
            <div class="w-10 h-10 rounded-full bg-rose-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-xl">error</span>
            </div>
            <h3 class="text-lg font-bold text-slate-800">Rejeter les notes</h3>
        </div>
        <p class="text-sm text-slate-500 mb-4 leading-relaxed">
            Veuillez indiquer le motif du rejet pour l'enseignant.
        </p>

        <form method="POST" action="{{ route('personnel.notes.rejeter') }}">
            @csrf
            <input type="hidden" name="classe_id" value="{{ $classe->id }}">
            <input type="hidden" name="matiere_id" value="{{ $matiere->id }}">
            <input type="hidden" name="periode" value="{{ $periode }}">

            <div class="mb-4">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Motif de rejet (obligatoire)</label>
                <textarea name="rejet_motif" rows="3" class="w-full bg-slate-50 rounded-lg border border-slate-200 text-sm p-3 focus:border-rose-400 focus:ring-rose-400" placeholder="Ex: Incohérence constatée sur les notes de devoir..." required></textarea>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" class="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-lg transition-colors" onclick="closeRejectModal()">Annuler</button>
                <button type="submit" class="bg-rose-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-rose-700 transition-colors shadow-sm">Confirmer le rejet</button>
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