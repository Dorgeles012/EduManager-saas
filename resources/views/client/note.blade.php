@extends('client.layouts.app')
@section('title', 'EduManager - Validation & Publication des Notes')
@section('content')

<!-- Header -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Validation & Publication des Notes</h2>
    <p class="text-sm text-gray-500 mt-1">Validez définitivement les notes vérifiées par le personnel pour publier les bulletins scolaires.</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                <span class="material-symbols-outlined text-lg">assignment_turned_in</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">À publier</span>
        </div>
        <h3 class="text-2xl font-bold text-gray-900">{{ $pendingClientCount ?? 0 }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Prêtes à publier</p>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                <span class="material-symbols-outlined text-lg">schedule</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Personnel</span>
        </div>
        <h3 class="text-2xl font-bold text-gray-900">{{ $pendingPersonnelCount ?? 0 }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">En vérification</p>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                <span class="material-symbols-outlined text-lg">verified</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Publiées</span>
        </div>
        <h3 class="text-2xl font-bold text-gray-900">{{ $publishedCount ?? 0 }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Visibles aux élèves</p>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600">
                <span class="material-symbols-outlined text-lg">cancel</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Rejetées</span>
        </div>
        <h3 class="text-2xl font-bold text-gray-900">{{ $rejectedCount ?? 0 }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Renvoyées à corriger</p>
    </div>
</div>

<!-- Filtres -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('client.notes.index') }}">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Classe</label>
                <select class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition" name="classe_id" onchange="this.form.submit()">
                    <option value="">Toutes les classes</option>
                    @foreach($classes ?? [] as $c)
                    <option value="{{ $c->id }}" {{ ($selectedClass ?? 0) == $c->id ? 'selected' : '' }}>{{ $c->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Matière</label>
                <select class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition" name="matiere_id" onchange="this.form.submit()">
                    <option value="">Toutes les matières</option>
                    @foreach($subjects ?? [] as $s)
                    <option value="{{ $s->id }}" {{ ($selectedSubject ?? 0) == $s->id ? 'selected' : '' }}>{{ $s->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Période</label>
                <select class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition" name="periode" onchange="this.form.submit()">
                    <option value="">Toutes les périodes</option>
                    <option value="t1" {{ ($selectedPeriode ?? '') == 't1' ? 'selected' : '' }}>1er Trimestre</option>
                    <option value="t2" {{ ($selectedPeriode ?? '') == 't2' ? 'selected' : '' }}>2ème Trimestre</option>
                    <option value="t3" {{ ($selectedPeriode ?? '') == 't3' ? 'selected' : '' }}>3ème Trimestre</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Statut</label>
                <select class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2 px-3 font-medium focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition" name="statut" onchange="this.form.submit()">
                    <option value="" {{ empty($selectedStatus) ? 'selected' : '' }}>Tous les statuts</option>
                    <option value="approuve_personnel" {{ ($selectedStatus ?? '') == 'approuve_personnel' ? 'selected' : '' }}>⭐ Prêt à publier</option>
                    <option value="publie" {{ ($selectedStatus ?? '') == 'publie' ? 'selected' : '' }}>🌟 Publié</option>
                    <option value="soumis" {{ ($selectedStatus ?? '') == 'soumis' ? 'selected' : '' }}>⏳ En attente personnel</option>
                    <option value="rejete_client" {{ ($selectedStatus ?? '') == 'rejete_client' ? 'selected' : '' }}>❌ Rejeté</option>
                </select>
            </div>
            <div class="flex items-end">
                <a href="{{ route('client.notes.index') }}" class="w-full h-[38px] bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-medium flex items-center justify-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-sm">restart_alt</span>
                    Réinitialiser
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Section Lots -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

    <!-- En-tête de section -->
    <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
            <span class="material-symbols-outlined text-base">publish</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-900">Lots de notes prêts pour publication</h3>
            <p class="text-[11px] text-gray-500">Validez ou rejetez les notes vérifiées par le personnel</p>
        </div>
    </div>

    <!-- Tableau (desktop) -->
    <div class="hidden lg:block overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Classe</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Matière</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Enseignant</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Période</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider text-center">Notes</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider text-center">Moyenne</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($batches ?? [] as $batch)
                @php
                    $classe = $classes->firstWhere('id', $batch->classe_id);
                    $matiere = $subjects->firstWhere('id', $batch->matiere_id);
                    $enseignant = $enseignants->firstWhere('id', $batch->enseignant_id);
                @endphp
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 text-[11px] font-bold">
                                {{ strtoupper(substr($classe?->nom ?? 'C', 0, 1)) }}
                            </div>
                            <span class="text-xs font-semibold text-gray-900">{{ $classe?->nom ?? 'Classe #'.$batch->classe_id }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-700 font-medium">{{ $matiere?->nom ?? 'Matière #'.$batch->matiere_id }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $enseignant ? trim(($enseignant->prenoms ?? '').' '.$enseignant->nom) : '—' }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-100 text-[10px] font-bold text-gray-700 uppercase">
                            {{ $batch->periode }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-xs font-semibold text-gray-900">{{ $batch->total_notes }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-xs font-bold {{ $batch->moyenne_classe >= 10 ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ number_format((float) $batch->moyenne_classe, 2) }}
                        </span>
                        <span class="text-[10px] text-gray-400">/20</span>
                    </td>
                    <td class="px-4 py-3">
                        @if($batch->statut === \App\Models\Note::STATUT_APPROUVE_PERSONNEL)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-indigo-50 text-indigo-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                Prêt à publier
                            </span>
                        @elseif($batch->statut === \App\Models\Note::STATUT_PUBLIE)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700">
                                <span class="material-symbols-outlined text-[12px]">verified</span>
                                Publié
                            </span>
                        @elseif($batch->statut === \App\Models\Note::STATUT_SOUMIS)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                En attente
                            </span>
                        @elseif($batch->statut === \App\Models\Note::STATUT_REJETE_CLIENT)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700">
                                <span class="material-symbols-outlined text-[12px]">error</span>
                                Rejeté
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-600">
                                {{ ucfirst($batch->statut) }}
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="{{ route('client.notes.review', ['classe_id' => $batch->classe_id, 'matiere_id' => $batch->matiere_id, 'periode' => $batch->periode, 'annee_academique_id' => $batch->annee_academique_id]) }}"
                               class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center transition"
                               title="Examiner">
                                <span class="material-symbols-outlined text-base">visibility</span>
                            </a>

                            @if(in_array($batch->statut, [\App\Models\Note::STATUT_APPROUVE_PERSONNEL, \App\Models\Note::STATUT_SOUMIS]))
                            <form method="POST" action="{{ route('client.notes.publier') }}" class="inline">
                                @csrf
                                <input type="hidden" name="classe_id" value="{{ $batch->classe_id }}">
                                <input type="hidden" name="matiere_id" value="{{ $batch->matiere_id }}">
                                <input type="hidden" name="periode" value="{{ $batch->periode }}">
                                <button type="submit"
                                        class="h-8 px-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-[11px] font-semibold flex items-center gap-1.5 transition shadow-sm"
                                        onclick="return confirm('Valider définitivement et publier ces notes ? Les bulletins seront générés automatiquement.');">
                                    <span class="material-symbols-outlined text-sm">publish</span>
                                    Publier
                                </button>
                            </form>

                            <button type="button"
                                    class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition"
                                    onclick="openRejectModal({{ $batch->classe_id }}, {{ $batch->matiere_id }}, '{{ $batch->periode }}')"
                                    title="Rejeter">
                                <span class="material-symbols-outlined text-base">close</span>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-16 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center mb-3">
                                <span class="material-symbols-outlined text-2xl text-gray-300">inbox</span>
                            </div>
                            <p class="text-sm font-semibold text-gray-700">Aucun lot en attente</p>
                            <p class="text-xs text-gray-400 mt-1">Toutes les notes vérifiées ont été traitées.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Cartes (mobile/tablette) -->
    <div class="lg:hidden divide-y divide-gray-100">
        @forelse($batches ?? [] as $batch)
        @php
            $classe = $classes->firstWhere('id', $batch->classe_id);
            $matiere = $subjects->firstWhere('id', $batch->matiere_id);
            $enseignant = $enseignants->firstWhere('id', $batch->enseignant_id);
        @endphp
        <div class="p-4 space-y-3">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-sm font-bold flex-shrink-0">
                        {{ strtoupper(substr($classe?->nom ?? 'C', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $classe?->nom ?? 'Classe #'.$batch->classe_id }}</p>
                        <p class="text-[11px] text-gray-500 truncate">{{ $matiere?->nom ?? 'Matière' }}</p>
                    </div>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-100 text-[10px] font-bold text-gray-700 uppercase flex-shrink-0">
                    {{ $batch->periode }}
                </span>
            </div>

            <div class="grid grid-cols-3 gap-2 py-2">
                <div class="text-center">
                    <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Notes</p>
                    <p class="text-sm font-bold text-gray-900 mt-0.5">{{ $batch->total_notes }}</p>
                </div>
                <div class="text-center border-x border-gray-100">
                    <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Moyenne</p>
                    <p class="text-sm font-bold mt-0.5 {{ $batch->moyenne_classe >= 10 ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ number_format((float) $batch->moyenne_classe, 2) }}
                    </p>
                </div>
                <div class="text-center">
                    <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Statut</p>
                    <p class="text-[11px] font-semibold mt-0.5
                        @if($batch->statut === \App\Models\Note::STATUT_APPROUVE_PERSONNEL) text-indigo-600
                        @elseif($batch->statut === \App\Models\Note::STATUT_PUBLIE) text-emerald-600
                        @elseif($batch->statut === \App\Models\Note::STATUT_SOUMIS) text-amber-600
                        @elseif($batch->statut === \App\Models\Note::STATUT_REJETE_CLIENT) text-rose-600
                        @else text-gray-600 @endif">
                        @if($batch->statut === \App\Models\Note::STATUT_APPROUVE_PERSONNEL) À publier
                        @elseif($batch->statut === \App\Models\Note::STATUT_PUBLIE) Publié
                        @elseif($batch->statut === \App\Models\Note::STATUT_SOUMIS) En attente
                        @elseif($batch->statut === \App\Models\Note::STATUT_REJETE_CLIENT) Rejeté
                        @else {{ ucfirst($batch->statut) }} @endif
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <a href="{{ route('client.notes.review', ['classe_id' => $batch->classe_id, 'matiere_id' => $batch->matiere_id, 'periode' => $batch->periode, 'annee_academique_id' => $batch->annee_academique_id]) }}"
                   class="flex-1 h-9 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold flex items-center justify-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-sm">visibility</span>
                    Examiner
                </a>

                @if(in_array($batch->statut, [\App\Models\Note::STATUT_APPROUVE_PERSONNEL, \App\Models\Note::STATUT_SOUMIS]))
                <form method="POST" action="{{ route('client.notes.publier') }}" class="flex-1">
                    @csrf
                    <input type="hidden" name="classe_id" value="{{ $batch->classe_id }}">
                    <input type="hidden" name="matiere_id" value="{{ $batch->matiere_id }}">
                    <input type="hidden" name="periode" value="{{ $batch->periode }}">
                    <button type="submit"
                            class="w-full h-9 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold flex items-center justify-center gap-1.5 transition shadow-sm"
                            onclick="return confirm('Valider définitivement et publier ces notes ?');">
                        <span class="material-symbols-outlined text-sm">publish</span>
                        Publier
                    </button>
                </form>

                <button type="button"
                        class="w-9 h-9 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition"
                        onclick="openRejectModal({{ $batch->classe_id }}, {{ $batch->matiere_id }}, '{{ $batch->periode }}')">
                    <span class="material-symbols-outlined text-base">close</span>
                </button>
                @endif
            </div>
        </div>
        @empty
        <div class="py-16 text-center">
            <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-2xl text-gray-300">inbox</span>
            </div>
            <p class="text-sm font-semibold text-gray-700">Aucun lot en attente</p>
            <p class="text-xs text-gray-400 mt-1">Toutes les notes vérifiées ont été traitées.</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(($batches ?? collect())->isNotEmpty() && method_exists($batches, 'links'))
    <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between">
        <span class="text-[11px] text-gray-500">
            {{ $batches->count() }} lot(s) affiché(s)
        </span>
        <div class="text-xs">
            {{ $batches->links() ?? '' }}
        </div>
    </div>
    @endif
</div>

<!-- Modal de Rejet -->
<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="rejectModal">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeRejectModal()"></div>
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden relative z-10 p-6">

        <div class="flex items-start gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600 flex-shrink-0">
                <span class="material-symbols-outlined text-xl">error</span>
            </div>
            <div>
                <h3 class="text-base font-bold text-gray-900">Rejeter les notes</h3>
                <p class="text-xs text-gray-500 mt-0.5">L'enseignant sera notifié et devra corriger les notes.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('client.notes.rejeter') }}">
            @csrf
            <input type="hidden" name="classe_id" id="rejectClasseId">
            <input type="hidden" name="matiere_id" id="rejectMatiereId">
            <input type="hidden" name="periode" id="rejectPeriode">

            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Motif de rejet <span class="text-rose-500">*</span></label>
                <textarea name="rejet_motif" rows="3"
                          class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs p-3 focus:border-rose-500 focus:ring-2 focus:ring-rose-100 outline-none transition resize-none"
                          placeholder="Ex: Veuillez réévaluer les notes de la composition..."
                          required></textarea>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button"
                        class="px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition"
                        onclick="closeRejectModal()">
                    Annuler
                </button>
                <button type="submit"
                        class="bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-lg text-xs font-semibold transition shadow-sm">
                    Confirmer le rejet
                </button>
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
    const modal = document.getElementById('rejectModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    modal.classList.remove('flex');
    modal.classList.add('hidden');
}
</script>
@endpush