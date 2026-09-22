@extends('client.layouts.app')
@section('title', 'EduManager - Validation & Publication des Notes')
@section('content')

<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-3 mb-5">
    <div>
        <h2 class="font-headline-md text-2xl text-primary mb-0.5">Validation Finale & Publication des Notes</h2>
        <p class="text-text-muted text-xs">Validez définitivement les notes vérifiées par le personnel pour publier les notes et générer les bulletins scolaires.</p>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
    <div class="bg-surface-container-lowest p-3 rounded-lg border border-outline-variant shadow-ambient">
        <div class="flex items-center justify-between mb-2">
            <div class="p-1.5 bg-indigo-50 rounded-lg text-indigo-700">
                <span class="material-symbols-outlined text-sm">assignment_turned_in</span>
            </div>
            <span class="text-[8px] font-bold text-indigo-700 uppercase">À publier</span>
        </div>
        <h3 class="text-headline-sm text-xl mb-0 text-indigo-700">{{ $pendingClientCount ?? 0 }}</h3>
        <p class="text-text-muted text-[11px]">Notes vérifiées par le personnel</p>
    </div>
    <div class="bg-surface-container-lowest p-3 rounded-lg border border-outline-variant shadow-ambient">
        <div class="flex items-center justify-between mb-2">
            <div class="p-1.5 bg-warning-amber/10 rounded-lg text-warning-amber">
                <span class="material-symbols-outlined text-sm">schedule</span>
            </div>
            <span class="text-[8px] font-bold text-warning-amber uppercase">Personnel</span>
        </div>
        <h3 class="text-headline-sm text-xl mb-0 text-warning-amber">{{ $pendingPersonnelCount ?? 0 }}</h3>
        <p class="text-text-muted text-[11px]">En cours de vérification personnel</p>
    </div>
    <div class="bg-surface-container-lowest p-3 rounded-lg border border-outline-variant shadow-ambient">
        <div class="flex items-center justify-between mb-2">
            <div class="p-1.5 bg-success-green/10 rounded-lg text-success-green">
                <span class="material-symbols-outlined text-sm">verified</span>
            </div>
            <span class="text-[8px] font-bold text-success-green uppercase">Publiées</span>
        </div>
        <h3 class="text-headline-sm text-xl mb-0 text-success-green">{{ $publishedCount ?? 0 }}</h3>
        <p class="text-text-muted text-[11px]">Visibles élèves & parents</p>
    </div>
    <div class="bg-surface-container-lowest p-3 rounded-lg border border-outline-variant shadow-ambient">
        <div class="flex items-center justify-between mb-2">
            <div class="p-1.5 bg-alert-red/10 rounded-lg text-alert-red">
                <span class="material-symbols-outlined text-sm">cancel</span>
            </div>
            <span class="text-[8px] font-bold text-alert-red uppercase">Rejetées</span>
        </div>
        <h3 class="text-headline-sm text-xl mb-0 text-alert-red">{{ $rejectedCount ?? 0 }}</h3>
        <p class="text-text-muted text-[11px]">Renvoyées pour correction</p>
    </div>
</div>

<!-- Filtres -->
<div class="bg-surface-container-lowest rounded-lg border border-outline-variant shadow-ambient p-4 mb-5">
    <form method="GET" action="{{ route('client.notes.index') }}">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-[10px] text-on-surface-variant mb-1 uppercase font-bold tracking-wider">Classe</label>
                <select class="w-full bg-surface-container-low border-outline-variant rounded-lg text-xs py-1.5 px-2" name="classe_id" onchange="this.form.submit()">
                    <option value="">Toutes les classes</option>
                    @foreach($classes ?? [] as $c)
                    <option value="{{ $c->id }}" {{ ($selectedClass ?? 0) == $c->id ? 'selected' : '' }}>{{ $c->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] text-on-surface-variant mb-1 uppercase font-bold tracking-wider">Matière</label>
                <select class="w-full bg-surface-container-low border-outline-variant rounded-lg text-xs py-1.5 px-2" name="matiere_id" onchange="this.form.submit()">
                    <option value="">Toutes les matières</option>
                    @foreach($subjects ?? [] as $s)
                    <option value="{{ $s->id }}" {{ ($selectedSubject ?? 0) == $s->id ? 'selected' : '' }}>{{ $s->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] text-on-surface-variant mb-1 uppercase font-bold tracking-wider">Période</label>
                <select class="w-full bg-surface-container-low border-outline-variant rounded-lg text-xs py-1.5 px-2" name="periode" onchange="this.form.submit()">
                    <option value="">Toutes les périodes</option>
                    <option value="t1" {{ ($selectedPeriode ?? '') == 't1' ? 'selected' : '' }}>1er Trimestre</option>
                    <option value="t2" {{ ($selectedPeriode ?? '') == 't2' ? 'selected' : '' }}>2ème Trimestre</option>
                    <option value="t3" {{ ($selectedPeriode ?? '') == 't3' ? 'selected' : '' }}>3ème Trimestre</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] text-on-surface-variant mb-1 uppercase font-bold tracking-wider">Statut</label>
                <select class="w-full bg-surface-container-low border-outline-variant rounded-lg text-xs py-1.5 px-2 font-semibold" name="statut" onchange="this.form.submit()">
                    <option value="">Tous les statuts</option>
                    <option value="approuve_personnel" {{ ($selectedStatus ?? '') == 'approuve_personnel' ? 'selected' : '' }}>⭐ Prêt pour validation & publication</option>
                    <option value="publie" {{ ($selectedStatus ?? '') == 'publie' ? 'selected' : '' }}>🌟 Validé & Publié</option>
                    <option value="soumis" {{ ($selectedStatus ?? '') == 'soumis' ? 'selected' : '' }}>⏳ En attente personnel</option>
                    <option value="rejete_client" {{ ($selectedStatus ?? '') == 'rejete_client' ? 'selected' : '' }}>❌ Rejeté Client</option>
                    <option value="rejete_personnel" {{ ($selectedStatus ?? '') == 'rejete_personnel' ? 'selected' : '' }}>❌ Rejeté Personnel</option>
                </select>
            </div>
            <div class="flex items-end">
                <a href="{{ route('client.notes.index') }}" class="bg-surface-variant text-on-surface px-3 py-1.5 rounded-lg text-xs font-medium flex items-center justify-center gap-1 hover:bg-outline-variant/30 transition-all w-full h-[34px]">
                    <span class="material-symbols-outlined text-sm">restart_alt</span>
                    Réinitialiser
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Table des Lots de Soumission -->
<div class="bg-surface-container-lowest rounded-lg border border-outline-variant shadow-ambient overflow-hidden mb-5">
    <div class="p-4 border-b border-outline-variant bg-surface-container-low/30 flex items-center justify-between">
        <h3 class="font-headline-sm text-sm text-primary flex items-center gap-1.5">
            <span class="material-symbols-outlined text-base">publish</span>
            Lots de notes prêts pour publication officielle
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
            <thead class="bg-surface-container-low border-b border-outline-variant">
                <tr>
                    <th class="px-4 py-2.5 text-[10px] uppercase text-text-muted tracking-wider">Classe</th>
                    <th class="px-4 py-2.5 text-[10px] uppercase text-text-muted tracking-wider">Matière</th>
                    <th class="px-4 py-2.5 text-[10px] uppercase text-text-muted tracking-wider">Enseignant</th>
                    <th class="px-4 py-2.5 text-[10px] uppercase text-text-muted tracking-wider">Période</th>
                    <th class="px-4 py-2.5 text-[10px] uppercase text-text-muted tracking-wider">Nb Notes</th>
                    <th class="px-4 py-2.5 text-[10px] uppercase text-text-muted tracking-wider">Moyenne Classe</th>
                    <th class="px-4 py-2.5 text-[10px] uppercase text-text-muted tracking-wider">Statut</th>
                    <th class="px-4 py-2.5 text-[10px] uppercase text-text-muted tracking-wider text-right">Actions de Publication</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/50">
                @forelse($batches ?? [] as $batch)
                @php
                    $classe = $classes->firstWhere('id', $batch->classe_id);
                    $matiere = $subjects->firstWhere('id', $batch->matiere_id);
                    $enseignant = $enseignants->firstWhere('id', $batch->enseignant_id);
                @endphp
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-4 py-3 font-semibold text-primary">{{ $classe?->nom ?? 'Classe #'.$batch->classe_id }}</td>
                    <td class="px-4 py-3 font-medium">{{ $matiere?->nom ?? 'Matière #'.$batch->matiere_id }}</td>
                    <td class="px-4 py-3 text-text-muted">{{ $enseignant ? trim(($enseignant->prenoms ?? '').' '.$enseignant->nom) : '—' }}</td>
                    <td class="px-4 py-3 font-bold uppercase text-primary">{{ $batch->periode }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full bg-surface-container text-xs font-semibold">{{ $batch->total_notes }} notes</span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="font-bold text-xs {{ $batch->moyenne_classe >= 10 ? 'text-success-green' : 'text-alert-red' }}">
                            {{ number_format((float) $batch->moyenne_classe, 2) }}/20
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        @if($batch->statut === \App\Models\Note::STATUT_APPROUVE_PERSONNEL)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-indigo-100 text-indigo-800 border border-indigo-300">
                                <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                                Vérifié Personnel (À valider)
                            </span>
                        @elseif($batch->statut === \App\Models\Note::STATUT_PUBLIE)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                <span class="material-symbols-outlined text-xs">verified</span>
                                Validé & Publié
                            </span>
                        @elseif($batch->statut === \App\Models\Note::STATUT_SOUMIS)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                <span class="material-symbols-outlined text-xs">schedule</span>
                                En attente Personnel
                            </span>
                        @elseif($batch->statut === \App\Models\Note::STATUT_REJETE_CLIENT)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                <span class="material-symbols-outlined text-xs">error</span>
                                Rejeté Direction
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">
                                {{ ucfirst($batch->statut) }}
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="{{ route('client.notes.review', ['classe_id' => $batch->classe_id, 'matiere_id' => $batch->matiere_id, 'periode' => $batch->periode, 'annee_academique_id' => $batch->annee_academique_id]) }}" class="px-2.5 py-1 bg-surface-container hover:bg-surface-container-high rounded text-primary text-[11px] font-semibold flex items-center gap-1">
                                <span class="material-symbols-outlined text-xs">visibility</span>
                                Examiner
                            </a>

                            @if(in_array($batch->statut, [\App\Models\Note::STATUT_APPROUVE_PERSONNEL, \App\Models\Note::STATUT_SOUMIS]))
                            <!-- Valider & Publier -->
                            <form method="POST" action="{{ route('client.notes.publier') }}" class="inline">
                                @csrf
                                <input type="hidden" name="classe_id" value="{{ $batch->classe_id }}">
                                <input type="hidden" name="matiere_id" value="{{ $batch->matiere_id }}">
                                <input type="hidden" name="periode" value="{{ $batch->periode }}">
                                <button type="submit" class="px-2.5 py-1 bg-primary text-white rounded text-[11px] font-bold hover:opacity-90 flex items-center gap-1 shadow-sm" onclick="return confirm('Valider définitivement et publier ces notes ? Les moyennes seront calculées et les bulletins scolaires publiés pour les élèves et parents.');">
                                    <span class="material-symbols-outlined text-xs">publish</span>
                                    Valider & Publier
                                </button>
                            </form>

                            <!-- Rejeter -->
                            <button type="button" class="px-2.5 py-1 bg-alert-red/10 text-alert-red hover:bg-alert-red hover:text-white rounded text-[11px] font-bold transition-all flex items-center gap-1" onclick="openRejectModal({{ $batch->classe_id }}, {{ $batch->matiere_id }}, '{{ $batch->periode }}')">
                                <span class="material-symbols-outlined text-xs">close</span>
                                Rejeter
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td class="py-10 text-center text-text-muted" colspan="8">
                        <div class="flex flex-col items-center justify-center">
                            <span class="material-symbols-outlined text-3xl text-outline mb-1">verified</span>
                            <p class="text-sm font-semibold">Aucun lot de notes en attente de publication</p>
                            <p class="text-xs">Toutes les notes vérifiées ont été publiées ou aucun lot ne correspond aux filtres.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(($batches ?? collect())->isNotEmpty() && method_exists($batches, 'links'))
    <div class="px-4 py-2 border-t border-outline-variant bg-surface-container-low/30 flex items-center justify-between">
        <span class="text-[10px] text-text-muted">
            Affichage de {{ $batches->count() }} lot(s)
        </span>
        <div class="flex gap-1 text-xs">
            {{ $batches->links() ?? '' }}
        </div>
    </div>
    @endif
</div>

<!-- Modal de Rejet -->
<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="rejectModal">
    <div class="absolute inset-0 modal-overlay backdrop-blur-sm bg-black/40" onclick="closeRejectModal()"></div>
    <div class="bg-surface-container-lowest w-full max-w-md rounded-xl shadow-2xl border border-outline-variant overflow-hidden transform transition-all duration-300 relative z-10 p-6">
        <div class="flex items-center gap-2 text-alert-red mb-3">
            <span class="material-symbols-outlined text-2xl">error</span>
            <h3 class="font-headline-md text-base font-bold">Rejeter les notes (Direction)</h3>
        </div>
        <p class="text-xs text-text-muted mb-4">
            Indiquez le motif du rejet. L'enseignant sera notifié et devra corriger les notes avant une nouvelle soumission.
        </p>

        <form method="POST" action="{{ route('client.notes.rejeter') }}">
            @csrf
            <input type="hidden" name="classe_id" id="rejectClasseId">
            <input type="hidden" name="matiere_id" id="rejectMatiereId">
            <input type="hidden" name="periode" id="rejectPeriode">

            <div class="mb-4">
                <label class="block text-xs font-semibold mb-1">Motif de rejet (obligatoire)</label>
                <textarea name="rejet_motif" rows="3" class="w-full bg-surface rounded-lg border-outline-variant text-xs p-2.5 focus:border-alert-red focus:ring-alert-red" placeholder="Ex: Veuillez réévaluer les notes de la composition..." required></textarea>
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
