@extends('enseignant.layouts.app')
@section('title', 'EduManager - Gestion des Notes')
@section('content')

<!-- SweetAlert2 CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Header & Actions -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-3 mb-5">
    <div>
        <h2 class="font-headline-md text-2xl text-primary mb-0.5">Gestion des Notes & Évaluations</h2>
        <p class="text-text-muted text-xs">Saisissez les notes de vos élèves, visualisez les moyennes et soumettez-les au personnel.</p>
    </div>
    <div class="flex items-center gap-2">
        <button class="bg-surface-container-high text-primary px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1.5 hover:bg-primary-fixed transition-all" onclick="openBulkModal()">
            <span class="material-symbols-outlined text-base">table_rows</span>
            Saisie par classe
        </button>
        <button class="bg-primary text-on-primary px-4 py-1.5 rounded-lg font-label-sm text-sm flex items-center gap-1.5 hover:opacity-90 active:scale-95 transition-all card-shadow" onclick="openModal()">
            <span class="material-symbols-outlined text-base">add</span>
            Ajouter une note
        </button>
    </div>
</div>

<!-- Alertes Rejets éventuels -->
@if(($rejectedCount ?? 0) > 0)
<div class="mb-5 p-4 rounded-xl bg-alert-red/10 border border-alert-red/30 flex items-start gap-3 text-on-surface">
    <div class="p-2 bg-alert-red/20 text-alert-red rounded-lg flex-shrink-0">
        <span class="material-symbols-outlined text-xl">warning</span>
    </div>
    <div class="flex-1">
        <h4 class="text-sm font-bold text-alert-red">Attention : vous avez {{ $rejectedCount }} note(s) rejetée(s)</h4>
        <p class="text-xs text-text-muted mt-0.5">
            Le personnel ou la direction a renvoyé des notes à corriger. Consultez le motif dans le tableau ci-dessous, ajustez les notes concernées, puis cliquez sur <strong>"Soumettre pour validation"</strong>.
        </p>
    </div>
</div>
@endif

<!-- Stats Grid -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-5">
    <div class="bg-surface-container-lowest p-3 rounded-lg border border-outline-variant shadow-ambient">
        <div class="flex items-center justify-between mb-2">
            <div class="p-1.5 bg-surface-container rounded-lg text-primary">
                <span class="material-symbols-outlined text-sm">grade</span>
            </div>
            <span class="text-[8px] font-bold text-text-muted uppercase">Total</span>
        </div>
        <h3 class="text-headline-sm text-xl mb-0">{{ $totalGrades ?? 0 }}</h3>
        <p class="text-text-muted text-[11px]">Notes enregistrées</p>
    </div>
    <div class="bg-surface-container-lowest p-3 rounded-lg border border-outline-variant shadow-ambient">
        <div class="flex items-center justify-between mb-2">
            <div class="p-1.5 bg-surface-container-high rounded-lg text-primary">
                <span class="material-symbols-outlined text-sm">edit_note</span>
            </div>
            <span class="text-[8px] font-bold text-primary uppercase">Brouillons</span>
        </div>
        <h3 class="text-headline-sm text-xl mb-0 text-primary">{{ $draftCount ?? 0 }}</h3>
        <p class="text-text-muted text-[11px]">À soumettre</p>
    </div>
    <div class="bg-surface-container-lowest p-3 rounded-lg border border-outline-variant shadow-ambient">
        <div class="flex items-center justify-between mb-2">
            <div class="p-1.5 bg-warning-amber/10 rounded-lg text-warning-amber">
                <span class="material-symbols-outlined text-sm">schedule</span>
            </div>
            <span class="text-[8px] font-bold text-warning-amber uppercase">En cours</span>
        </div>
        <h3 class="text-headline-sm text-xl mb-0 text-warning-amber">{{ $submittedCount ?? 0 }}</h3>
        <p class="text-text-muted text-[11px]">En attente validation</p>
    </div>
    <div class="bg-surface-container-lowest p-3 rounded-lg border border-outline-variant shadow-ambient">
        <div class="flex items-center justify-between mb-2">
            <div class="p-1.5 bg-alert-red/10 rounded-lg text-alert-red">
                <span class="material-symbols-outlined text-sm">cancel</span>
            </div>
            <span class="text-[8px] font-bold text-alert-red uppercase">Rejetées</span>
        </div>
        <h3 class="text-headline-sm text-xl mb-0 text-alert-red">{{ $rejectedCount ?? 0 }}</h3>
        <p class="text-text-muted text-[11px]">À corriger</p>
    </div>
    <div class="bg-surface-container-lowest p-3 rounded-lg border border-outline-variant shadow-ambient">
        <div class="flex items-center justify-between mb-2">
            <div class="p-1.5 bg-success-green/10 rounded-lg text-success-green">
                <span class="material-symbols-outlined text-sm">verified</span>
            </div>
            <span class="text-[8px] font-bold text-success-green uppercase">Publiées</span>
        </div>
        <h3 class="text-headline-sm text-xl mb-0 text-success-green">{{ $publishedCount ?? 0 }}</h3>
        <p class="text-text-muted text-[11px]">Visibles élèves</p>
    </div>
</div>

<!-- Filtres & Soumission -->
<div class="bg-surface-container-lowest rounded-lg border border-outline-variant shadow-ambient p-4 mb-5">
    <form method="GET" action="{{ route('enseignant.notes.index') }}" id="filterForm">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-[10px] text-on-surface-variant mb-1 uppercase font-bold tracking-wider">Classe</label>
                <select class="w-full bg-surface-container-low border-outline-variant rounded-lg text-xs focus:ring-primary focus:border-primary py-1.5 px-2" name="classe_id" id="filterClasse" onchange="this.form.submit()">
                    <option value="">Toutes les classes</option>
                    @foreach($classes ?? [] as $classe)
                    <option value="{{ $classe->id }}" {{ ($selectedClass ?? 0) == $classe->id ? 'selected' : '' }}>{{ $classe->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] text-on-surface-variant mb-1 uppercase font-bold tracking-wider">Matière</label>
                @if(($subjects ?? collect())->count() === 1)
                    <div class="w-full bg-surface-container-low border border-outline-variant/60 rounded-lg text-xs py-1.5 px-3 font-semibold text-primary flex items-center justify-between h-[34px]">
                        <span>{{ $subjects->first()->nom }}</span>
                    </div>
                    <input type="hidden" name="matiere_id" value="{{ $subjects->first()->id }}">
                @else
                    <select class="w-full bg-surface-container-low border-outline-variant rounded-lg text-xs focus:ring-primary focus:border-primary py-1.5 px-2" name="matiere_id" id="filterMatiere" onchange="this.form.submit()">
                        <option value="">Toutes mes matières</option>
                        @foreach($subjects ?? [] as $subject)
                        <option value="{{ $subject->id }}" {{ ($selectedSubject ?? 0) == $subject->id ? 'selected' : '' }}>{{ $subject->nom }} (Coef. {{ $subject->coefficient ?? 1 }})</option>
                        @endforeach
                    </select>
                @endif
            </div>
            <div>
                <label class="block text-[10px] text-on-surface-variant mb-1 uppercase font-bold tracking-wider">Période</label>
                <select class="w-full bg-surface-container-low border-outline-variant rounded-lg text-xs focus:ring-primary focus:border-primary py-1.5 px-2" name="periode" id="filterPeriode" onchange="this.form.submit()">
                    <option value="">Toutes les périodes</option>
                    <option value="t1" {{ ($selectedPeriode ?? '') == 't1' ? 'selected' : '' }}>1er Trimestre</option>
                    <option value="t2" {{ ($selectedPeriode ?? '') == 't2' ? 'selected' : '' }}>2ème Trimestre</option>
                    <option value="t3" {{ ($selectedPeriode ?? '') == 't3' ? 'selected' : '' }}>3ème Trimestre</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] text-on-surface-variant mb-1 uppercase font-bold tracking-wider">Statut</label>
                <select class="w-full bg-surface-container-low border-outline-variant rounded-lg text-xs focus:ring-primary focus:border-primary py-1.5 px-2" name="statut" id="filterStatus" onchange="this.form.submit()">
                    <option value="">Tous les statuts</option>
                    <option value="brouillon" {{ ($selectedStatus ?? '') == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                    <option value="soumis" {{ ($selectedStatus ?? '') == 'soumis' ? 'selected' : '' }}>Soumis (En attente)</option>
                    <option value="approuve_personnel" {{ ($selectedStatus ?? '') == 'approuve_personnel' ? 'selected' : '' }}>Vérifié Personnel</option>
                    <option value="rejete_personnel" {{ ($selectedStatus ?? '') == 'rejete_personnel' ? 'selected' : '' }}>Rejeté Personnel</option>
                    <option value="rejete_client" {{ ($selectedStatus ?? '') == 'rejete_client' ? 'selected' : '' }}>Rejeté Client</option>
                    <option value="publie" {{ ($selectedStatus ?? '') == 'publie' ? 'selected' : '' }}>Publié</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <a href="{{ route('enseignant.notes.index') }}" class="bg-surface-variant text-on-surface px-3 py-1.5 rounded-lg text-xs font-medium flex items-center justify-center gap-1 hover:bg-outline-variant/30 transition-all flex-1 h-[34px]">
                    <span class="material-symbols-outlined text-sm">restart_alt</span>
                    Effacer
                </a>
            </div>
        </div>
    </form>

    <!-- Barre d'action Soumission si classe + matière + période filtrées -->
    @if($selectedClass && $selectedSubject && $selectedPeriode)
    <div class="mt-4 pt-3 border-t border-outline-variant/50 flex flex-wrap items-center justify-between gap-3 bg-primary-fixed/30 p-3 rounded-lg">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-xl">send</span>
            <div>
                <p class="text-xs font-semibold text-primary">Prêt pour la validation ?</p>
                <p class="text-[11px] text-text-muted">Soumettez les notes de cette classe et matière au personnel administratif.</p>
            </div>
        </div>
        <form method="POST" action="{{ route('enseignant.notes.soumettre') }}" class="inline" id="soumissionForm">
            @csrf
            <input type="hidden" name="classe_id" value="{{ $selectedClass }}">
            <input type="hidden" name="matiere_id" value="{{ $selectedSubject }}">
            <input type="hidden" name="periode" value="{{ $selectedPeriode }}">
            <button type="button" onclick="confirmSoumission()" class="bg-primary text-white text-xs px-4 py-2 rounded-lg font-bold hover:opacity-90 active:scale-95 transition-all flex items-center gap-1.5 card-shadow">
                <span class="material-symbols-outlined text-sm">assignment_turned_in</span>
                Soumettre pour validation
            </button>
        </form>
    </div>
    @endif
</div>

<!-- Synthèse des Moyennes calculées par élève (Si filtre actif) -->
@if(($studentAverages ?? collect())->isNotEmpty())
<div class="bg-surface-container-lowest rounded-lg border border-outline-variant shadow-ambient p-4 mb-5">
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-base">calculate</span>
            <h3 class="font-headline-sm text-sm text-primary">Moyennes calculées pour la matière ({{ strtoupper($selectedPeriode) }})</h3>
        </div>
        <span class="text-[11px] text-text-muted">{{ $studentAverages->count() }} élève(s)</span>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2.5">
        @foreach($studentAverages as $item)
        <div class="p-2.5 rounded-lg bg-surface-container-low border border-outline-variant/30 flex items-center justify-between">
            <div class="min-w-0 flex-1 pr-2">
                <p class="text-xs font-semibold truncate">{{ $item['student']->nom }} {{ $item['student']->prenom }}</p>
                <p class="text-[10px] text-text-muted">{{ $item['count'] }} note(s) saisie(s)</p>
            </div>
            <div class="text-right flex-shrink-0">
                @if($item['average'] !== null)
                <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $item['average'] >= 10 ? 'bg-success-green/10 text-success-green' : 'bg-alert-red/10 text-alert-red' }}">
                    {{ number_format($item['average'], 2) }}/20
                </span>
                <p class="text-[9px] text-text-muted mt-0.5">{{ $item['appreciation'] }}</p>
                @else
                <span class="text-[10px] text-text-muted italic">—</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Table des Notes -->
<div class="bg-surface-container-lowest rounded-lg border border-outline-variant shadow-ambient overflow-hidden mb-5">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
            <thead class="bg-surface-container-low border-b border-outline-variant">
                <tr>
                    <th class="px-3 py-2 text-[10px] uppercase text-text-muted tracking-wider">Élève</th>
                    <th class="px-3 py-2 text-[10px] uppercase text-text-muted tracking-wider">Classe</th>
                    <th class="px-3 py-2 text-[10px] uppercase text-text-muted tracking-wider">Matière</th>
                    <th class="px-3 py-2 text-[10px] uppercase text-text-muted tracking-wider">Évaluation</th>
                    <th class="px-3 py-2 text-[10px] uppercase text-text-muted tracking-wider">Période</th>
                    <th class="px-3 py-2 text-[10px] uppercase text-text-muted tracking-wider">Note</th>
                    <th class="px-3 py-2 text-[10px] uppercase text-text-muted tracking-wider">Appréciation</th>
                    <th class="px-3 py-2 text-[10px] uppercase text-text-muted tracking-wider">Statut</th>
                    <th class="px-3 py-2 text-[10px] uppercase text-text-muted tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/50">
                @forelse($grades ?? [] as $grade)
                <tr class="hover:bg-surface-container-low transition-colors {{ $grade->isRejete() ? 'bg-alert-red/5' : '' }}">
                    <td class="px-3 py-2">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-primary-fixed flex items-center justify-center text-primary text-xs font-bold">
                                {{ strtoupper(substr($grade->eleve?->nom ?? 'E', 0, 1)) }}
                            </div>
                            <div>
                                <span class="font-medium text-xs">{{ $grade->eleve?->nom }} {{ $grade->eleve?->prenom }}</span>
                                <span class="text-[10px] text-text-muted block">{{ $grade->eleve?->matricule }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-3 py-2 text-on-surface-variant">{{ $grade->classe?->nom }}</td>
                    <td class="px-3 py-2">
                        <span class="px-2 py-0.5 rounded-full text-[10px] bg-primary/10 text-primary font-medium">
                            {{ $grade->matiere?->nom }}
                        </span>
                    </td>
                    <td class="px-3 py-2">
                        <div class="text-xs font-medium">{{ $grade->titre_evaluation ?? 'Évaluation' }}</div>
                        <span class="text-[10px] text-text-muted uppercase">{{ $grade->type_evaluation ?? 'devoir' }}</span>
                    </td>
                    <td class="px-3 py-2 font-medium">{{ strtoupper($grade->periode ?? '') }}</td>
                    <td class="px-3 py-2">
                        <span class="font-bold text-sm {{ $grade->note >= 10 ? 'text-success-green' : 'text-alert-red' }}">{{ number_format($grade->note, 2) }}</span>
                        <span class="text-on-surface-variant text-[10px]">/20</span>
                    </td>
                    <td class="px-3 py-2 text-on-surface-variant text-xs">{{ $grade->appreciation ?? '—' }}</td>
                    <td class="px-3 py-2">
                        @if($grade->statut === \App\Models\Note::STATUT_BROUILLON)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                                Brouillon
                            </span>
                        @elseif($grade->statut === \App\Models\Note::STATUT_SOUMIS)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                En attente de validation
                            </span>
                        @elseif($grade->statut === \App\Models\Note::STATUT_APPROUVE_PERSONNEL)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                Vérifié (en attente client)
                            </span>
                        @elseif($grade->statut === \App\Models\Note::STATUT_PUBLIE)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-300">
                                <span class="material-symbols-outlined text-[11px]">verified</span>
                                Validé & Publié
                            </span>
                        @elseif($grade->isRejete())
                            <div>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-300">
                                    <span class="material-symbols-outlined text-[11px]">error</span>
                                    Rejeté ({{ $grade->statut === \App\Models\Note::STATUT_REJETE_PERSONNEL ? 'Personnel' : 'Client' }})
                                </span>
                                @if($grade->rejet_motif)
                                <p class="text-[10px] text-alert-red mt-0.5 max-w-xs truncate" title="{{ $grade->rejet_motif }}">
                                    <strong>Motif :</strong> {{ $grade->rejet_motif }}
                                </p>
                                @endif
                            </div>
                        @endif
                    </td>
                    <td class="px-3 py-2 text-right">
                        <div class="flex justify-end gap-1">
                            <button class="p-1 text-primary hover:bg-primary-fixed rounded transition-colors"
                                    onclick="editGrade({{ $grade->id }}, {{ $grade->isValidee() ? 'true' : 'false' }})"
                                    title="Modifier">
                                <span class="material-symbols-outlined text-sm">edit</span>
                            </button>
                            <button class="p-1 text-alert-red hover:bg-alert-red/10 rounded transition-colors"
                                    onclick="confirmDelete({{ $grade->id }}, {{ $grade->isValidee() ? 'true' : 'false' }})"
                                    title="Supprimer">
                                <span class="material-symbols-outlined text-sm">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td class="py-10 text-center" colspan="9">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-12 h-12 bg-surface-container rounded-full flex items-center justify-center mb-2">
                                <span class="material-symbols-outlined text-2xl text-outline">grade</span>
                            </div>
                            <h4 class="font-headline-sm text-sm text-primary mb-1">Aucune note trouvée</h4>
                            <p class="text-text-muted text-xs max-w-sm mx-auto">Aucune note ne correspond aux critères sélectionnés. Utilisez le bouton ci-dessus pour en ajouter.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(($grades ?? collect())->isNotEmpty() && method_exists($grades, 'links'))
    <div class="px-3 py-2 border-t border-outline-variant bg-surface-container-low/30 flex items-center justify-between">
        <span class="text-[10px] text-text-muted">
            {{ $grades->firstItem() ?? 1 }} - {{ $grades->lastItem() ?? count($grades ?? []) }} sur {{ $grades->total() ?? count($grades ?? []) }}
        </span>
        <div class="flex gap-1 text-xs">
            {{ $grades->links() ?? '' }}
        </div>
    </div>
    @endif
</div>

<!-- Modal Saisie Note Individuelle -->
<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="noteModal">
    <div class="absolute inset-0 modal-overlay backdrop-blur-sm bg-black/40" onclick="closeModal()"></div>
    <div class="bg-surface-container-lowest w-full max-w-lg rounded-xl shadow-2xl border border-outline-variant overflow-hidden transform transition-all duration-300 scale-95 opacity-0 relative z-10" id="noteModalContent">
        <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-primary text-white">
            <h3 class="font-headline-md text-base" id="modalTitle">Ajouter une note</h3>
            <button class="text-white/80 hover:text-white transition-colors" onclick="closeModal()">
                <span class="material-symbols-outlined text-2xl">close</span>
            </button>
        </div>
        
        <form class="px-6 py-5 space-y-4" id="gradeForm" method="POST" action="{{ route('enseignant.notes.store') }}">
            @csrf
            <input type="hidden" id="gradeId" name="grade_id">
            <input type="hidden" id="methodField" name="_method" value="POST">

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-on-surface">Classe</label>
                    <select class="w-full bg-surface rounded-lg border-outline-variant focus:border-primary focus:ring-primary py-2 px-3 text-xs" name="classe_id" id="classId" onchange="loadStudentsForClass(this.value)" required>
                        <option value="">Sélectionner une classe</option>
                        @foreach($classes ?? [] as $classe)
                        <option value="{{ $classe->id }}" {{ ($selectedClass ?? 0) == $classe->id ? 'selected' : '' }}>{{ $classe->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-on-surface">Matière</label>
                    @if(($subjects ?? collect())->count() === 1)
                        <input type="hidden" name="matiere_id" id="subjectId" value="{{ $subjects->first()->id }}">
                        <div class="w-full bg-surface-container rounded-lg border border-outline-variant py-2 px-3 text-xs font-bold text-primary flex items-center justify-between">
                            <span>{{ $subjects->first()->nom }}</span>
                            <span class="text-[10px] uppercase font-bold text-primary/70 bg-primary/10 px-2 py-0.5 rounded-full">Votre matière</span>
                        </div>
                    @else
                        <select class="w-full bg-surface rounded-lg border-outline-variant focus:border-primary focus:ring-primary py-2 px-3 text-xs" name="matiere_id" id="subjectId" required>
                            @foreach($subjects ?? [] as $subject)
                            <option value="{{ $subject->id }}" {{ ($selectedSubject ?? 0) == $subject->id ? 'selected' : '' }}>{{ $subject->nom }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </div>
            
            <div class="space-y-1">
                <label class="block text-xs font-semibold text-on-surface">Élève</label>
                <select class="w-full bg-surface rounded-lg border-outline-variant focus:border-primary focus:ring-primary py-2 px-3 text-xs" name="eleve_id" id="studentId" required>
                    <option value="">Sélectionner un élève</option>
                    @foreach($students ?? [] as $student)
                    <option value="{{ $student->id }}">{{ $student->nom }} {{ $student->prenom }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-on-surface">Titre de l'évaluation</label>
                    <input type="text" class="w-full bg-surface rounded-lg border-outline-variant focus:border-primary focus:ring-primary py-2 px-3 text-xs" name="titre_evaluation" id="titreEvaluation" placeholder="Ex: Devoir sur table 1" required>
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-on-surface">Type d'évaluation</label>
                    <select class="w-full bg-surface rounded-lg border-outline-variant focus:border-primary focus:ring-primary py-2 px-3 text-xs" name="type_evaluation" id="typeEvaluation" required>
                        <option value="devoir">Devoir</option>
                        <option value="interrogation">Interrogation</option>
                        <option value="composition">Composition</option>
                        <option value="autre">Autre contrôle</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-on-surface">Période</label>
                    <select class="w-full bg-surface rounded-lg border-outline-variant focus:border-primary focus:ring-primary py-2 px-3 text-xs" name="periode" id="periodeSelect" required>
                        <option value="t1" {{ ($selectedPeriode ?? '') == 't1' ? 'selected' : '' }}>1er Trimestre</option>
                        <option value="t2" {{ ($selectedPeriode ?? '') == 't2' ? 'selected' : '' }}>2ème Trimestre</option>
                        <option value="t3" {{ ($selectedPeriode ?? '') == 't3' ? 'selected' : '' }}>3ème Trimestre</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-on-surface">Note (sur 20)</label>
                    <input class="w-full bg-surface rounded-lg border-outline-variant focus:border-primary focus:ring-primary py-2 px-3 text-xs font-bold" name="note" id="noteInput" max="20" min="0" oninput="updateAppreciation()" placeholder="Ex: 14.5" step="0.25" type="number" required>
                </div>
            </div>
            
            <div class="bg-surface-container-low p-3 rounded-lg border border-outline-variant/30 text-center flex flex-col items-center justify-center">
                <span class="text-[10px] uppercase font-bold text-text-muted tracking-widest">Appréciation suggérée</span>
                <div class="text-sm font-semibold italic text-primary" id="appreciationResult">
                    Saisissez une note...
                </div>
            </div>
            
            <div class="flex justify-end gap-3 pt-2">
                <button class="px-4 py-2 text-xs font-medium text-on-surface-variant hover:bg-surface-container rounded-lg transition-colors" onclick="closeModal()" type="button">
                    Annuler
                </button>
                <button class="bg-primary text-on-primary px-5 py-2 rounded-lg text-xs font-bold hover:opacity-90 active:scale-95 transition-all shadow-md" id="submitBtn" type="submit">
                    Enregistrer (Brouillon)
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Saisie Groupée par Classe -->
<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="bulkModal">
    <div class="absolute inset-0 modal-overlay backdrop-blur-sm bg-black/40" onclick="closeBulkModal()"></div>
    <div class="bg-surface-container-lowest w-full max-w-2xl rounded-xl shadow-2xl border border-outline-variant overflow-hidden transform transition-all duration-300 scale-95 opacity-0 relative z-10 max-h-[90vh] flex flex-col" id="bulkModalContent">
        <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-primary text-white flex-shrink-0">
            <div>
                <h3 class="font-headline-md text-base">Saisie groupée des notes par classe</h3>
                <p class="text-[11px] text-white/80">Saisissez les notes de toute la classe pour une évaluation donnée</p>
            </div>
            <button class="text-white/80 hover:text-white transition-colors" onclick="closeBulkModal()">
                <span class="material-symbols-outlined text-2xl">close</span>
            </button>
        </div>

        <form class="p-6 space-y-4 overflow-y-auto flex-1" method="POST" action="{{ route('enseignant.notes.bulk-store') }}" id="bulkForm">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold mb-1">Classe</label>
                    <select class="w-full bg-surface rounded-lg border-outline-variant text-xs py-2 px-3" name="classe_id" id="bulkClasseId" onchange="loadBulkStudents(this.value)" required>
                        <option value="">Sélectionner</option>
                        @foreach($classes ?? [] as $classe)
                        <option value="{{ $classe->id }}" {{ ($selectedClass ?? 0) == $classe->id ? 'selected' : '' }}>{{ $classe->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Matière</label>
                    @if(($subjects ?? collect())->count() === 1)
                        <input type="hidden" name="matiere_id" value="{{ $subjects->first()->id }}">
                        <div class="w-full bg-surface-container rounded-lg border border-outline-variant py-2 px-3 text-xs font-bold text-primary flex items-center justify-between">
                            <span>{{ $subjects->first()->nom }}</span>
                            <span class="text-[10px] uppercase font-bold text-primary/70 bg-primary/10 px-2 py-0.5 rounded-full">Votre matière</span>
                        </div>
                    @else
                        <select class="w-full bg-surface rounded-lg border-outline-variant text-xs py-2 px-3" name="matiere_id" required>
                            @foreach($subjects ?? [] as $subject)
                            <option value="{{ $subject->id }}" {{ ($selectedSubject ?? 0) == $subject->id ? 'selected' : '' }}>{{ $subject->nom }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Période</label>
                    <select class="w-full bg-surface rounded-lg border-outline-variant text-xs py-2 px-3" name="periode" required>
                        <option value="t1" {{ ($selectedPeriode ?? '') == 't1' ? 'selected' : '' }}>1er Trimestre</option>
                        <option value="t2" {{ ($selectedPeriode ?? '') == 't2' ? 'selected' : '' }}>2ème Trimestre</option>
                        <option value="t3" {{ ($selectedPeriode ?? '') == 't3' ? 'selected' : '' }}>3ème Trimestre</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold mb-1">Titre de l'évaluation</label>
                    <input type="text" class="w-full bg-surface rounded-lg border-outline-variant text-xs py-2 px-3" name="titre_evaluation" placeholder="Ex: Devoir sur table 1" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Type d'évaluation</label>
                    <select class="w-full bg-surface rounded-lg border-outline-variant text-xs py-2 px-3" name="type_evaluation" required>
                        <option value="devoir">Devoir</option>
                        <option value="interrogation">Interrogation</option>
                        <option value="composition">Composition</option>
                        <option value="autre">Autre</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 border rounded-lg overflow-hidden">
                <div class="bg-surface-container-low px-4 py-2 border-b font-semibold text-xs text-primary flex justify-between">
                    <span>Élève</span>
                    <span>Note (/20)</span>
                </div>
                <div class="divide-y max-h-60 overflow-y-auto" id="bulkStudentsList">
                    @forelse($students ?? [] as $student)
                    <div class="px-4 py-2 flex items-center justify-between hover:bg-surface-container-low/50">
                        <span class="text-xs font-medium">{{ $student->nom }} {{ $student->prenom }}</span>
                        <input type="number" step="0.25" min="0" max="20" name="notes[{{ $student->id }}]" class="w-24 bg-surface rounded-lg border-outline-variant text-xs py-1 px-2 text-right font-bold" placeholder="—/20">
                    </div>
                    @empty
                    <div class="p-4 text-center text-xs text-text-muted">Sélectionnez une classe pour charger la liste des élèves.</div>
                    @endforelse
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-3">
                <button type="button" class="px-4 py-2 text-xs font-medium text-on-surface-variant hover:bg-surface-container rounded-lg" onclick="closeBulkModal()">Annuler</button>
                <button type="submit" class="bg-primary text-on-primary px-5 py-2 rounded-lg text-xs font-bold hover:opacity-90">Enregistrer les notes de la classe</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
// CONFIGURATION GLOBALE SWEETALERT2
const SwalCustom = Swal.mixin({
    customClass: {
        popup: 'rounded-xl',
        confirmButton: 'bg-primary text-white px-4 py-2 rounded-lg text-xs font-bold hover:opacity-90 mx-1 cursor-pointer',
        cancelButton: 'bg-surface-variant text-on-surface px-4 py-2 rounded-lg text-xs font-medium hover:bg-outline-variant/30 mx-1 cursor-pointer',
        title: 'text-base font-semibold',
        htmlContainer: 'text-xs text-text-muted'
    },
    buttonsStyling: false,
    reverseButtons: true
});

// MESSAGES FLASH (succès / erreur / warning / erreurs validation)
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Succès',
        text: @json(session('success')),
        confirmButtonText: 'OK',
        timer: 2000,
        timerProgressBar: false,
        position: 'center',         
        showConfirmButton: false
    });
@endif

@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Erreur',
        text: @json(session('error')),
        confirmButtonText: 'OK',
        position: 'center'          
    });
@endif

@if(session('warning'))
    Swal.fire({
        icon: 'warning',
        title: 'Attention',
        text: @json(session('warning')),
        confirmButtonText: 'Compris',
        position: 'center'          
    });
@endif

@if($errors->any())
    Swal.fire({
        icon: 'error',
        title: 'Erreur de validation',
        html: `<ul class="text-left text-xs list-disc pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>`,
        confirmButtonText: 'Corriger',
        position: 'center'          
    });
@endif

// MODALES
function openModal() {
    const modal = document.getElementById('noteModal');
    const content = document.getElementById('noteModalContent');
    document.getElementById('gradeForm').reset();
    document.getElementById('gradeId').value = '';
    document.getElementById('methodField').value = 'POST';
    document.getElementById('gradeForm').action = "{{ route('enseignant.notes.store') }}";
    document.getElementById('modalTitle').textContent = 'Ajouter une note';
    document.getElementById('submitBtn').textContent = 'Enregistrer (Brouillon)';
    document.getElementById('appreciationResult').textContent = 'Saisissez une note...';
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closeModal() {
    const modal = document.getElementById('noteModal');
    const content = document.getElementById('noteModalContent');
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }, 300);
}

function openBulkModal() {
    const modal = document.getElementById('bulkModal');
    const content = document.getElementById('bulkModalContent');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closeBulkModal() {
    const modal = document.getElementById('bulkModal');
    const content = document.getElementById('bulkModalContent');
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }, 300);
}

// SOUMISSION POUR VALIDATION
function confirmSoumission() {
    SwalCustom.fire({
        icon: 'question',
        title: 'Soumettre pour validation ?',
        html: 'Les notes de cette classe et matière seront envoyées au <strong>personnel administratif</strong> pour vérification.<br><br>Vous ne pourrez plus les modifier tant qu\'elles n\'auront pas été traitées.',
        showCancelButton: true,
        confirmButtonText: '<span class="material-symbols-outlined text-sm align-middle">send</span> Soumettre',
        cancelButtonText: 'Annuler',
        iconColor: '#3b82f6'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('soumissionForm').submit();
        }
    });
}

// ÉDITION D'UNE NOTE
function editGrade(id, isValidated = false) {
    if (isValidated) {
        SwalCustom.fire({
            icon: 'warning',
            title: 'Note validée',
            html: 'Cette note est <strong>validée</strong>. Sa modification invalidera les validations précédentes et nécessitera une nouvelle validation.<br><br>Continuer ?',
            showCancelButton: true,
            confirmButtonText: '<span class="material-symbols-outlined text-sm align-middle">edit</span> Continuer',
            cancelButtonText: 'Annuler',
            iconColor: '#f59e0b'
        }).then((result) => {
            if (result.isConfirmed) {
                loadGradeForEdit(id);
            }
        });
    } else {
        loadGradeForEdit(id);
    }
}

function loadGradeForEdit(id) {
    fetch(`{{ url('/enseignant/notes') }}/${id}/edit`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        const note = data.note;
        document.getElementById('gradeId').value = note.id;
        document.getElementById('methodField').value = 'PUT';
        document.getElementById('gradeForm').action = `{{ url('/enseignant/notes') }}/${note.id}`;
        document.getElementById('classId').value = note.classe_id;
        document.getElementById('subjectId').value = note.matiere_id;
        document.getElementById('studentId').value = note.eleve_id;
        document.getElementById('titreEvaluation').value = note.titre_evaluation || '';
        document.getElementById('typeEvaluation').value = note.type_evaluation || 'devoir';
        document.getElementById('periodeSelect').value = note.periode || 't1';
        document.getElementById('noteInput').value = note.note;
        document.getElementById('modalTitle').textContent = 'Modifier la note';
        document.getElementById('submitBtn').textContent = 'Mettre à jour';
        updateAppreciation();

        const modal = document.getElementById('noteModal');
        const content = document.getElementById('noteModalContent');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    })
    .catch(err => {
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: 'Impossible de charger la note. Veuillez réessayer.',
            confirmButtonText: 'OK'
        });
    });
}

// SUPPRESSION D'UNE NOTE
function confirmDelete(id, isValidated = false) {
    const config = isValidated ? {
        icon: 'warning',
        title: 'Supprimer cette note ?',
        html: 'Cette note est <strong class="text-rose-600">publiée ou validée</strong>.<br>Sa suppression la retirera du bulletin et nécessitera une nouvelle validation.',
        confirmButtonText: '<span class="material-symbols-outlined text-sm align-middle">delete</span> Supprimer',
        cancelButtonText: 'Annuler',
    } : {
        icon: 'question',
        title: 'Confirmer la suppression',
        text: 'Êtes-vous sûr de vouloir supprimer cette note ? Cette action est irréversible.',
        confirmButtonText: '<span class="material-symbols-outlined text-sm align-middle">delete</span> Supprimer',
        cancelButtonText: 'Annuler',
    };

    SwalCustom.fire({
        ...config,
        showCancelButton: true,
        iconColor: '#e11d48'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteForm');
            form.action = `{{ url('/enseignant/notes') }}/${id}`;
            form.submit();
        }
    });
}

// APPRÉCIATION
function updateAppreciation() {
    const val = parseFloat(document.getElementById('noteInput').value);
    const res = document.getElementById('appreciationResult');
    if (isNaN(val)) {
        res.textContent = 'Saisissez une note...';
        return;
    }
    if (val >= 16) res.textContent = 'Très Bien';
    else if (val >= 14) res.textContent = 'Bien';
    else if (val >= 12) res.textContent = 'Assez Bien';
    else if (val >= 10) res.textContent = 'Passable';
    else res.textContent = 'Insuffisant';
}

// CHARGEMENT DYNAMIQUE DES ÉLÈVES
function loadStudentsForClass(classId) {
    if (!classId) return;
    fetch(`{{ route('enseignant.notes.data') }}?classe_id=${classId}`)
        .then(res => res.json())
        .then(data => {
            const sel = document.getElementById('studentId');
            sel.innerHTML = '<option value="">Sélectionner un élève</option>';
            data.students.forEach(s => {
                sel.innerHTML += `<option value="${s.id}">${s.nom} ${s.prenom}</option>`;
            });
        })
        .catch(() => {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Impossible de charger la liste des élèves.',
                confirmButtonText: 'OK'
            });
        });
}

function loadBulkStudents(classId) {
    if (!classId) return;
    fetch(`{{ route('enseignant.notes.data') }}?classe_id=${classId}`)
        .then(res => res.json())
        .then(data => {
            const list = document.getElementById('bulkStudentsList');
            list.innerHTML = '';
            if (data.students.length === 0) {
                list.innerHTML = '<div class="p-4 text-center text-xs text-text-muted">Aucun élève trouvé dans cette classe.</div>';
                return;
            }
            data.students.forEach(s => {
                list.innerHTML += `
                    <div class="px-4 py-2 flex items-center justify-between hover:bg-surface-container-low/50">
                        <span class="text-xs font-medium">${s.nom} ${s.prenom}</span>
                        <input type="number" step="0.25" min="0" max="20" name="notes[${s.id}]" class="w-24 bg-surface rounded-lg border-outline-variant text-xs py-1 px-2 text-right font-bold" placeholder="—/20">
                    </div>
                `;
            });
        })
        .catch(() => {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Impossible de charger la liste des élèves.',
                confirmButtonText: 'OK'
            });
        });
}

// VALIDATIONS FORMULAIRES
document.addEventListener('DOMContentLoaded', function() {
    // Formulaire note individuelle
    const gradeForm = document.getElementById('gradeForm');
    if (gradeForm) {
        gradeForm.addEventListener('submit', function(e) {
            const note = parseFloat(document.getElementById('noteInput').value);
            if (isNaN(note) || note < 0 || note > 20) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Note invalide',
                    text: 'La note doit être comprise entre 0 et 20.',
                    confirmButtonText: 'Corriger'
                });
                return false;
            }
        });
    }

    // Formulaire saisie groupée
    const bulkForm = document.getElementById('bulkForm');
    if (bulkForm) {
        bulkForm.addEventListener('submit', function(e) {
            const inputs = bulkForm.querySelectorAll('input[name^="notes["]');
            let hasValue = false;
            inputs.forEach(inp => {
                if (inp.value !== '' && inp.value !== null) hasValue = true;
            });
            if (!hasValue) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Aucune note saisie',
                    text: 'Veuillez saisir au moins une note avant d\'enregistrer.',
                    confirmButtonText: 'Compris'
                });
                return false;
            }
        });
    }
});
</script>
@endpush