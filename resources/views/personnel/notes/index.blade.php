@extends('personnel.layouts.app')
@section('title', 'EduManager - Validation des Notes (Personnel)')
@section('content')

{{-- En-tête --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-3 mb-5">
    <div>
        <h2 class="font-headline-md text-headline-md text-primary mb-0.5">Vérification & Validation des Notes</h2>
        <p class="text-slate-500 text-sm mt-1">Vérifiez les notes soumises par les enseignants avant de les transmettre pour validation finale.</p>
    </div>
</div>

{{-- Messages flash --}}
@if(session('success'))
<div class="mb-4 flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-700 text-sm font-semibold">
    <span class="material-symbols-outlined text-base">check_circle</span>
    {{ session('success') }}
</div>
@endif
@if(session('warning'))
<div class="mb-4 flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-amber-700 text-sm font-semibold">
    <span class="material-symbols-outlined text-base">warning</span>
    {{ session('warning') }}
</div>
@endif

{{-- Statistiques --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">

    {{-- À vérifier --}}
    <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm hover:shadow-md transition-all">
        <div class="flex items-center gap-2.5 mb-3">
            <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-amber-500 text-lg">pending_actions</span>
            </div>
            <p class="text-slate-500 text-xs font-medium uppercase tracking-wide">À vérifier</p>
        </div>
        <h3 class="text-xl font-bold text-amber-600 leading-tight">{{ $pendingPersonnelCount ?? 0 }}</h3>
        <p class="text-slate-400 text-xs mt-1">Notes en attente de vérification</p>
    </div>

    {{-- Approuvées --}}
    <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm hover:shadow-md transition-all">
        <div class="flex items-center gap-2.5 mb-3">
            <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-primary mb-0.5-500 text-lg">thumb_up</span>
            </div>
            <p class="text-slate-500 text-xs font-medium uppercase tracking-wide">Approuvées</p>
        </div>
        <h3 class="text-xl font-bold text-primary mb-0.5-600 leading-tight">{{ $approvedPersonnelCount ?? 0 }}</h3>
        <p class="text-slate-400 text-xs mt-1">Transmises à la direction</p>
    </div>

    {{-- Rejetées --}}
    <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm hover:shadow-md transition-all">
        <div class="flex items-center gap-2.5 mb-3">
            <div class="w-9 h-9 rounded-lg bg-rose-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-rose-500 text-lg">cancel</span>
            </div>
            <p class="text-slate-500 text-xs font-medium uppercase tracking-wide">Rejetées</p>
        </div>
        <h3 class="text-xl font-bold text-rose-600 leading-tight">{{ $rejectedPersonnelCount ?? 0 }}</h3>
        <p class="text-slate-400 text-xs mt-1">Renvoyées aux enseignants</p>
    </div>

    {{-- Publiées --}}
    <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm hover:shadow-md transition-all">
        <div class="flex items-center gap-2.5 mb-3">
            <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-emerald-500 text-lg">verified</span>
            </div>
            <p class="text-slate-500 text-xs font-medium uppercase tracking-wide">Publiées</p>
        </div>
        <h3 class="text-xl font-bold text-emerald-600 leading-tight">{{ $publishedCount ?? 0 }}</h3>
        <p class="text-slate-400 text-xs mt-1">Validées & visibles aux élèves</p>
    </div>
</div>

{{-- Filtres --}}
<div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4 mb-5">
    <form method="GET" action="{{ route('personnel.notes.index') }}">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-xs text-slate-500 mb-1.5 uppercase font-semibold tracking-wide">Classe</label>
                <select class="w-full bg-slate-50 border border-slate-200 rounded-lg text-sm py-2 px-3 focus:border-indigo-400 focus:ring-indigo-400" name="classe_id" onchange="this.form.submit()">
                    <option value="">Toutes les classes</option>
                    @foreach($classes ?? [] as $c)
                    <option value="{{ $c->id }}" {{ ($selectedClass ?? 0) == $c->id ? 'selected' : '' }}>{{ $c->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1.5 uppercase font-semibold tracking-wide">Matière</label>
                <select class="w-full bg-slate-50 border border-slate-200 rounded-lg text-sm py-2 px-3 focus:border-indigo-400 focus:ring-indigo-400" name="matiere_id" onchange="this.form.submit()">
                    <option value="">Toutes les matières</option>
                    @foreach($subjects ?? [] as $s)
                    <option value="{{ $s->id }}" {{ ($selectedSubject ?? 0) == $s->id ? 'selected' : '' }}>{{ $s->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1.5 uppercase font-semibold tracking-wide">Période</label>
                <select class="w-full bg-slate-50 border border-slate-200 rounded-lg text-sm py-2 px-3 focus:border-indigo-400 focus:ring-indigo-400" name="periode" onchange="this.form.submit()">
                    <option value="">Toutes les périodes</option>
                    <option value="t1" {{ ($selectedPeriode ?? '') == 't1' ? 'selected' : '' }}>1er Trimestre</option>
                    <option value="t2" {{ ($selectedPeriode ?? '') == 't2' ? 'selected' : '' }}>2ème Trimestre</option>
                    <option value="t3" {{ ($selectedPeriode ?? '') == 't3' ? 'selected' : '' }}>3ème Trimestre</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1.5 uppercase font-semibold tracking-wide">Statut</label>
                <select class="w-full bg-slate-50 border border-slate-200 rounded-lg text-sm py-2 px-3 font-medium focus:border-indigo-400 focus:ring-indigo-400" name="statut" onchange="this.form.submit()">
                    <option value="">Tous les statuts</option>
                    <option value="soumis" {{ ($selectedStatus ?? '') == 'soumis' ? 'selected' : '' }}>⏳ En attente de vérification</option>
                    <option value="approuve_personnel" {{ ($selectedStatus ?? '') == 'approuve_personnel' ? 'selected' : '' }}>✔️ Vérifié par Personnel</option>
                    <option value="rejete_personnel" {{ ($selectedStatus ?? '') == 'rejete_personnel' ? 'selected' : '' }}>❌ Rejeté par Personnel</option>
                    <option value="publie" {{ ($selectedStatus ?? '') == 'publie' ? 'selected' : '' }}>🌟 Validé & Publié</option>
                </select>
            </div>
            <div class="flex items-end">
                <a href="{{ route('personnel.notes.index') }}" class="bg-slate-100 text-slate-600 px-3 py-2 rounded-lg text-sm font-medium flex items-center justify-center gap-1.5 hover:bg-slate-200 transition-all w-full h-[42px]">
                    <span class="material-symbols-outlined text-base">restart_alt</span>
                    Réinitialiser
                </a>
            </div>
        </div>
    </form>
</div>

{{-- Tableau des lots --}}
<div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden mb-5">
    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
        <h3 class="text-base font-semibold text-slate-700 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary mb-0.5-500 text-lg">checklist</span>
            Lots de notes par classe et matière
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-4 py-3 text-xs uppercase text-slate-500 font-semibold tracking-wide">Classe</th>
                    <th class="px-4 py-3 text-xs uppercase text-slate-500 font-semibold tracking-wide">Matière</th>
                    <th class="px-4 py-3 text-xs uppercase text-slate-500 font-semibold tracking-wide">Enseignant</th>
                    <th class="px-4 py-3 text-xs uppercase text-slate-500 font-semibold tracking-wide">Période</th>
                    <th class="px-4 py-3 text-xs uppercase text-slate-500 font-semibold tracking-wide">Nb Notes</th>
                    <th class="px-4 py-3 text-xs uppercase text-slate-500 font-semibold tracking-wide">Moyenne</th>
                    <th class="px-4 py-3 text-xs uppercase text-slate-500 font-semibold tracking-wide">Statut</th>
                    <th class="px-4 py-3 text-xs uppercase text-slate-500 font-semibold tracking-wide text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($batches ?? [] as $batch)
                @php
                    $classe = $classes->firstWhere('id', $batch->classe_id);
                    $matiere = $subjects->firstWhere('id', $batch->matiere_id);
                    $enseignant = $enseignants->firstWhere('id', $batch->enseignant_id);
                @endphp
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 font-semibold text-slate-800">{{ $classe?->nom ?? 'Classe #'.$batch->classe_id }}</td>
                    <td class="px-4 py-3 font-medium text-slate-700">{{ $matiere?->nom ?? 'Matière #'.$batch->matiere_id }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $enseignant ? trim(($enseignant->prenoms ?? '').' '.$enseignant->nom) : '—' }}</td>
                    <td class="px-4 py-3 font-bold uppercase text-primary mb-0.5-600 text-xs">{{ $batch->periode }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">{{ $batch->total_notes }} notes</span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="font-bold text-sm {{ $batch->moyenne_classe >= 10 ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ number_format((float) $batch->moyenne_classe, 2) }}/20
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        @if($batch->statut === \App\Models\Note::STATUT_SOUMIS)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                En attente
                            </span>
                        @elseif($batch->statut === \App\Models\Note::STATUT_APPROUVE_PERSONNEL)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-primary mb-0.5-700 border border-indigo-200">
                                <span class="material-symbols-outlined text-sm">check_circle</span>
                                Vérifié
                            </span>
                        @elseif($batch->statut === \App\Models\Note::STATUT_PUBLIE)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="material-symbols-outlined text-sm">verified</span>
                                Publié
                            </span>
                        @elseif($batch->statut === \App\Models\Note::STATUT_REJETE_PERSONNEL)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                <span class="material-symbols-outlined text-sm">error</span>
                                Rejeté
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                {{ ucfirst($batch->statut) }}
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="{{ route('personnel.notes.review', ['classe_id' => $batch->classe_id, 'matiere_id' => $batch->matiere_id, 'periode' => $batch->periode, 'annee_academique_id' => $batch->annee_academique_id]) }}" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-700 text-xs font-semibold flex items-center gap-1 transition-colors">
                                <span class="material-symbols-outlined text-sm">visibility</span>
                                Examiner
                            </a>

                            @if($batch->statut === \App\Models\Note::STATUT_SOUMIS)
                            <form method="POST" action="{{ route('personnel.notes.approuver') }}" class="inline">
                                @csrf
                                <input type="hidden" name="classe_id" value="{{ $batch->classe_id }}">
                                <input type="hidden" name="matiere_id" value="{{ $batch->matiere_id }}">
                                <input type="hidden" name="periode" value="{{ $batch->periode }}">
                                <button type="submit" class="px-2.5 py-1.5 bg-emerald-600 text-white rounded-lg text-xs font-semibold hover:bg-emerald-700 flex items-center gap-1 transition-colors shadow-sm" onclick="return confirm('Confirmer l\'approbation de ces notes ? Elles seront transmises au Client pour validation finale.');">
                                    <span class="material-symbols-outlined text-sm">check</span>
                                    Approuver
                                </button>
                            </form>

                            <button type="button" class="px-2.5 py-1.5 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-lg text-xs font-semibold transition-all flex items-center gap-1" onclick="openRejectModal({{ $batch->classe_id }}, {{ $batch->matiere_id }}, '{{ $batch->periode }}')">
                                <span class="material-symbols-outlined text-sm">close</span>
                                Rejeter
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td class="py-12 text-center" colspan="8">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center mb-3">
                                <span class="material-symbols-outlined text-2xl text-slate-400">done_all</span>
                            </div>
                            <p class="text-sm font-semibold text-slate-700">Aucun lot de notes en attente</p>
                            <p class="text-xs text-slate-400 mt-1">Toutes les notes soumises ont été traitées ou aucun lot ne correspond aux filtres.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(($batches ?? collect())->isNotEmpty() && method_exists($batches, 'links'))
    <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between">
        <span class="text-xs text-slate-400">
            Affichage de {{ $batches->count() }} lot(s)
        </span>
        <div class="flex gap-1 text-xs">
            {{ $batches->links() ?? '' }}
        </div>
    </div>
    @endif
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
            Veuillez indiquer le motif du rejet. L'enseignant sera notifié et pourra modifier ses notes avant de les resoumettre.
        </p>

        <form method="POST" action="{{ route('personnel.notes.rejeter') }}">
            @csrf
            <input type="hidden" name="classe_id" id="rejectClasseId">
            <input type="hidden" name="matiere_id" id="rejectMatiereId">
            <input type="hidden" name="periode" id="rejectPeriode">

            <div class="mb-4">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Motif de rejet (obligatoire)</label>
                <textarea name="rejet_motif" rows="3" class="w-full bg-slate-50 rounded-lg border border-slate-200 text-sm p-3 focus:border-rose-400 focus:ring-rose-400" placeholder="Ex: Veuillez vérifier la note de l'élève Kouassi, le coefficient semble erroné..." required></textarea>
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
function openRejectModal(classeId, matiereId, periode) {
    document.getElementById('rejectClasseId').value = classeId;
    document.getElementById('rejectMatiereId').value = matiereId;
    document.getElementById('rejectPeriode').value = periode;
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('rejectModal').classList.add('flex');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.remove('flex');
    document.getElementById('rejectModal').classList.add('hidden');
}
</script>
@endpush