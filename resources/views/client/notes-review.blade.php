@extends('client.layouts.app')
@section('title', 'EduManager - Examen & Publication des Notes')
@section('content')

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
    <div>
        <a href="{{ route('client.notes.index') }}" class="inline-flex items-center gap-1 text-xs text-gray-500 hover:text-indigo-600 transition-colors mb-2">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            Retour aux validations
        </a>
        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Validation & Publication</h2>
        <p class="text-sm text-gray-500 mt-1">
            <strong class="text-gray-900">{{ $classe->nom }}</strong> ·
            {{ $matiere->nom }} ·
            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 text-[10px] font-bold uppercase tracking-wider">{{ strtoupper($periode) }}</span>
        </p>
    </div>

</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mb-6">
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                <span class="material-symbols-outlined text-lg">groups</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Effectif</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ count($elevesSummaries) }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Élèves</p>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                <span class="material-symbols-outlined text-lg">assignment</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Évaluations</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $notes->count() }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Notes saisies</p>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow col-span-2 lg:col-span-1">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                <span class="material-symbols-outlined text-lg">calculate</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Moyenne</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold {{ ($moyenneGeneraleClasse ?? 0) >= 10 ? 'text-emerald-600' : 'text-rose-600' }}">
            {{ $moyenneGeneraleClasse !== null ? number_format($moyenneGeneraleClasse, 2) : '—' }}
        </h3>
        <p class="text-xs text-gray-500 mt-0.5">Moyenne de classe /20</p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
            <span class="material-symbols-outlined text-base">analytics</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-900">Détail des notes par élève</h3>
            <p class="text-[11px] text-gray-500">Calcul des moyennes et statuts</p>
        </div>
    </div>

    <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Élève</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Matricule</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Notes</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Moyenne</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Appréciation</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Statuts</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($elevesSummaries as $item)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 text-[11px] font-bold flex-shrink-0">
                                {{ strtoupper(substr($item['eleve']->nom, 0, 1)) }}
                            </div>
                            <span class="text-xs font-semibold text-gray-900">{{ $item['eleve']->nom }} {{ $item['eleve']->prenom }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500 font-mono">{{ $item['eleve']->matricule }}</td>
                    <td class="px-4 py-3">
                        @if($item['notes']->isNotEmpty())
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($item['notes'] as $n)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold {{ $n->note >= 10 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}"
                                          title="{{ $n->titre_evaluation ?? 'Évaluation' }} ({{ $n->type_evaluation }})">
                                        {{ number_format($n->note, 2) }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-xs text-gray-400 italic">Aucune note</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($item['moyenne'] !== null)
                            <span class="text-sm font-bold {{ $item['moyenne'] >= 10 ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ number_format($item['moyenne'], 2) }}
                            </span>
                            <span class="text-[10px] text-gray-400">/20</span>
                        @else
                            <span class="text-xs text-gray-400 italic">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-600 font-medium">{{ $item['appreciation'] }}</td>
                    <td class="px-4 py-3">
                        @php $uniqueStatuts = $item['notes']->pluck('statut')->unique(); @endphp
                        <div class="flex flex-wrap gap-1">
                            @foreach($uniqueStatuts as $st)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-100 text-[10px] font-bold text-gray-700 uppercase tracking-wider">
                                    {{ ucfirst($st) }}
                                </span>
                            @endforeach
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-16 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center mb-3">
                                <span class="material-symbols-outlined text-2xl text-gray-300">groups</span>
                            </div>
                            <p class="text-sm font-semibold text-gray-700">Aucun élève trouvé</p>
                            <p class="text-xs text-gray-400 mt-1">Aucun élève dans cette classe.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="md:hidden divide-y divide-gray-100">
        @forelse($elevesSummaries as $item)
        <div class="p-4 space-y-3">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-sm font-bold flex-shrink-0">
                        {{ strtoupper(substr($item['eleve']->nom, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $item['eleve']->nom }} {{ $item['eleve']->prenom }}</p>
                        <p class="text-[11px] text-gray-500 font-mono truncate">{{ $item['eleve']->matricule }}</p>
                    </div>
                </div>
                @if($item['moyenne'] !== null)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold flex-shrink-0 {{ $item['moyenne'] >= 10 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                        {{ number_format($item['moyenne'], 2) }}/20
                    </span>
                @endif
            </div>

            @if($item['notes']->isNotEmpty())
                <div class="flex flex-wrap gap-1.5">
                    @foreach($item['notes'] as $n)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold {{ $n->note >= 10 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                            {{ number_format($n->note, 2) }}
                        </span>
                    @endforeach
                </div>
            @endif

            <p class="text-[11px] text-gray-600">{{ $item['appreciation'] }}</p>
        </div>
        @empty
        <div class="py-16 text-center">
            <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-2xl text-gray-300">groups</span>
            </div>
            <p class="text-sm font-semibold text-gray-700">Aucun élève trouvé</p>
            <p class="text-xs text-gray-400 mt-1">Aucun élève dans cette classe.</p>
        </div>
        @endforelse
    </div>
</div>

<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="rejectModal">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeRejectModal()"></div>
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0 relative z-10" id="rejectModalContent">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600">
                    <span class="material-symbols-outlined text-base">error</span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Rejeter les notes</h3>
                    <p class="text-[11px] text-gray-500">Direction</p>
                </div>
            </div>
            <button type="button" onclick="closeRejectModal()" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center transition">
                <span class="material-symbols-outlined text-gray-500">close</span>
            </button>
        </div>

        <form method="POST" action="{{ route('client.notes.rejeter') }}" class="p-5 space-y-4">
            @csrf
            <input type="hidden" name="classe_id" value="{{ $classe->id }}">
            <input type="hidden" name="matiere_id" value="{{ $matiere->id }}">
            <input type="hidden" name="periode" value="{{ $periode }}">

            <p class="text-xs text-gray-500 leading-relaxed">
                Indiquez le motif du rejet. L'enseignant sera notifié et devra corriger les notes avant une nouvelle soumission.
            </p>

            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">
                    Motif de rejet <span class="text-rose-500">*</span>
                </label>
                <textarea name="rejet_motif" rows="3" required placeholder="Ex: Incohérence constatée sur les notes de composition..."
                          class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-rose-500 focus:ring-2 focus:ring-rose-100 outline-none transition resize-none"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                <button type="button" onclick="closeRejectModal()"
                        class="px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    Annuler
                </button>
                <button type="submit"
                        class="bg-rose-600 hover:bg-rose-700 text-white px-5 py-2 rounded-lg text-xs font-semibold transition shadow-sm">
                    Confirmer le rejet
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openRejectModal() {
    const m = document.getElementById('rejectModal');
    const c = document.getElementById('rejectModalContent');
    m.classList.remove('hidden'); m.classList.add('flex');
    setTimeout(() => { c.classList.remove('scale-95','opacity-0'); c.classList.add('scale-100','opacity-100'); }, 10);
}
function closeRejectModal() {
    const m = document.getElementById('rejectModal');
    const c = document.getElementById('rejectModalContent');
    c.classList.remove('scale-100','opacity-100'); c.classList.add('scale-95','opacity-0');
    setTimeout(() => { m.classList.remove('flex'); m.classList.add('hidden'); }, 300);
}
</script>
@endpush