@extends('enseignant.layouts.app')
@section('title', 'EduManager - Gestion des Notes')
@section('content')

<!-- Header & Action -->
<div class="flex justify-between items-end mb-5">
    <div>
        <h2 class="font-headline-md text-2xl text-primary mb-0.5">Gestion des Notes</h2>
        <p class="text-text-muted text-xs">Saisissez et consultez les notes de vos classes et matières.</p>
    </div>
    <button class="bg-primary text-on-primary px-4 py-1.5 rounded-lg font-label-sm text-sm flex items-center gap-1.5 hover:opacity-90 active:scale-95 transition-all card-shadow" onclick="openModal()">
        <span class="material-symbols-outlined text-base">add</span>
        Ajouter une note
    </button>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
    <div class="bg-surface-container-lowest p-3 rounded-lg border border-outline-variant shadow-ambient">
        <div class="flex items-center justify-between mb-2">
            <div class="p-1.5 bg-surface-container rounded-lg text-primary">
                <span class="material-symbols-outlined text-sm">group</span>
            </div>
            <span class="text-[8px] font-bold text-success-green uppercase">Total</span>
        </div>
        <h3 class="text-headline-sm text-xl mb-0">{{ $totalStudents ?? 0 }}</h3>
        <p class="text-text-muted text-[11px]">Élèves</p>
    </div>
    <div class="bg-surface-container-lowest p-3 rounded-lg border border-outline-variant shadow-ambient">
        <div class="flex items-center justify-between mb-2">
            <div class="p-1.5 bg-surface-container rounded-lg text-primary">
                <span class="material-symbols-outlined text-sm">book</span>
            </div>
            <span class="text-[8px] font-bold text-primary uppercase">Matières</span>
        </div>
        <h3 class="text-headline-sm text-xl mb-0">{{ $totalSubjects ?? 0 }}</h3>
        <p class="text-text-muted text-[11px]">Matières</p>
    </div>
    <div class="bg-surface-container-lowest p-3 rounded-lg border border-outline-variant shadow-ambient">
        <div class="flex items-center justify-between mb-2">
            <div class="p-1.5 bg-surface-container rounded-lg text-primary">
                <span class="material-symbols-outlined text-sm">meeting_room</span>
            </div>
            <span class="text-[8px] font-bold text-warning-amber uppercase">Classes</span>
        </div>
        <h3 class="text-headline-sm text-xl mb-0">{{ $totalClasses ?? 0 }}</h3>
        <p class="text-text-muted text-[11px]">Classes</p>
    </div>
    <div class="bg-surface-container-lowest p-3 rounded-lg border border-outline-variant shadow-ambient">
        <div class="flex items-center justify-between mb-2">
            <div class="p-1.5 bg-surface-container rounded-lg text-primary">
                <span class="material-symbols-outlined text-sm">grade</span>
            </div>
            <span class="text-[8px] font-bold text-text-muted uppercase">Notes</span>
        </div>
        <h3 class="text-headline-sm text-xl mb-0">{{ $totalGrades ?? 0 }}</h3>
        <p class="text-text-muted text-[11px]">Notes saisies</p>
    </div>
</div>

<!-- Mes classes & Mes matières -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-5">
    <div class="bg-surface-container-lowest rounded-lg border border-outline-variant shadow-ambient p-4">
        <div class="flex items-center gap-1.5 mb-2">
            <span class="material-symbols-outlined text-sm text-primary">meeting_room</span>
            <h3 class="font-headline-sm text-primary text-sm">Mes classes</h3>
        </div>
        <p class="text-text-muted text-[11px] mb-3">Vous êtes affilié aux classes suivantes :</p>
        @forelse($classes ?? [] as $classe)
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-surface-container-low mb-1.5">
            <span class="material-symbols-outlined text-sm text-primary">check_circle</span>
            <span class="font-body-sm text-sm">{{ $classe->nom }}</span>
        </div>
        @empty
        <p class="text-text-muted text-xs italic">Aucune classe ne vous est affiliée.</p>
        @endforelse
    </div>
    <div class="bg-surface-container-lowest rounded-lg border border-outline-variant shadow-ambient p-4">
        <div class="flex items-center gap-1.5 mb-2">
            <span class="material-symbols-outlined text-sm text-primary">book</span>
            <h3 class="font-headline-sm text-primary text-sm">Matières affiliées</h3>
        </div>
        <p class="text-text-muted text-[11px] mb-3">Seules vos matières sont disponibles.</p>
        @forelse($subjects ?? [] as $subject)
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-surface-container-low mb-1.5">
            <span class="material-symbols-outlined text-sm text-success-green">check_box</span>
            <span class="font-body-sm text-sm">{{ $subject->nom }}</span>
            @if($subject->coefficient)
            <span class="ml-auto text-[11px] text-text-muted">Coef. {{ $subject->coefficient }}</span>
            @endif
        </div>
        @empty
        <p class="text-text-muted text-xs italic">Aucune matière ne vous est affiliée.</p>
        @endforelse
    </div>
</div>

<!-- Filtres -->
<div class="bg-surface-container-lowest rounded-lg border border-outline-variant shadow-ambient p-3 mb-5">
    <div class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[150px]">
            <label class="block text-[10px] text-on-surface-variant mb-1 uppercase font-bold tracking-wider">Classe</label>
            <select class="w-full bg-surface-container-low border-outline-variant rounded-lg text-xs focus:ring-primary focus:border-primary py-1 px-2" id="filterClasse">
                <option value="">Toutes</option>
                @foreach($classes ?? [] as $classe)
                <option value="{{ $classe->id }}" {{ ($selectedClass ?? 0) == $classe->id ? 'selected' : '' }}>{{ $classe->nom }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[150px]">
            <label class="block text-[10px] text-on-surface-variant mb-1 uppercase font-bold tracking-wider">Matière</label>
            <select class="w-full bg-surface-container-low border-outline-variant rounded-lg text-xs focus:ring-primary focus:border-primary py-1 px-2" id="filterMatiere">
                <option value="">Toutes</option>
                @foreach($subjects ?? [] as $subject)
                <option value="{{ $subject->id }}" {{ ($selectedSubject ?? 0) == $subject->id ? 'selected' : '' }}>{{ $subject->nom }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[150px]">
            <label class="block text-[10px] text-on-surface-variant mb-1 uppercase font-bold tracking-wider">Élève</label>
            <select class="w-full bg-surface-container-low border-outline-variant rounded-lg text-xs focus:ring-primary focus:border-primary py-1 px-2" id="filterEleve">
                <option value="">Tous</option>
                @foreach($students ?? [] as $student)
                <option value="{{ $student->id }}" {{ ($selectedStudent ?? 0) == $student->id ? 'selected' : '' }}>{{ $student->nom }} {{ $student->prenom }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[150px]">
            <label class="block text-[10px] text-on-surface-variant mb-1 uppercase font-bold tracking-wider">Période</label>
            <select class="w-full bg-surface-container-low border-outline-variant rounded-lg text-xs focus:ring-primary focus:border-primary py-1 px-2" id="filterPeriode">
                <option value="">Toutes</option>
                <option value="t1" {{ ($selectedPeriode ?? '') == 't1' ? 'selected' : '' }}>1er Trim.</option>
                <option value="t2" {{ ($selectedPeriode ?? '') == 't2' ? 'selected' : '' }}>2ème Trim.</option>
                <option value="t3" {{ ($selectedPeriode ?? '') == 't3' ? 'selected' : '' }}>3ème Trim.</option>
            </select>
        </div>
        <div class="flex items-end h-full pt-4">
            <button class="bg-surface-variant text-on-surface px-3 py-1.5 rounded-lg text-xs font-medium flex items-center gap-1 hover:bg-outline-variant/30 transition-all" onclick="resetFilters()">
                <span class="material-symbols-outlined text-sm">restart_alt</span>
                Réinitialiser
            </button>
        </div>
    </div>
</div>

<!-- Content Area: Table -->
<div class="bg-surface-container-lowest rounded-lg border border-outline-variant shadow-ambient overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead class="bg-surface-container-low border-b border-outline-variant">
                <tr>
                    <th class="px-3 py-2 text-[10px] uppercase text-text-muted tracking-wider">Élève</th>
                    <th class="px-3 py-2 text-[10px] uppercase text-text-muted tracking-wider">Classe</th>
                    <th class="px-3 py-2 text-[10px] uppercase text-text-muted tracking-wider">Matière</th>
                    <th class="px-3 py-2 text-[10px] uppercase text-text-muted tracking-wider">Note</th>
                    <th class="px-3 py-2 text-[10px] uppercase text-text-muted tracking-wider">Coef.</th>
                    <th class="px-3 py-2 text-[10px] uppercase text-text-muted tracking-wider">Appréciation</th>
                    <th class="px-3 py-2 text-[10px] uppercase text-text-muted tracking-wider">Période</th>
                    <th class="px-3 py-2 text-[10px] uppercase text-text-muted tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/50">
                @forelse($grades ?? [] as $grade)
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-3 py-2">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-primary-fixed flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-[14px]">person</span>
                            </div>
                            <span class="text-sm">{{ $grade->student_nom }} {{ $grade->student_prenom }}</span>
                        </div>
                    </td>
                    <td class="px-3 py-2 text-xs text-on-surface-variant">{{ $grade->class_name }}</td>
                    <td class="px-3 py-2">
                        <span class="px-2 py-0.5 rounded-full text-[10px] bg-primary/10 text-primary">
                            {{ $grade->subject_name }}
                        </span>
                    </td>
                    <td class="px-3 py-2">
                        <span class="font-bold text-sm">{{ number_format($grade->note, 2) }}</span>
                        <span class="text-on-surface-variant text-xs">/20</span>
                    </td>
                    <td class="px-3 py-2 text-xs text-on-surface-variant">{{ $grade->coefficient ?? '—' }}</td>
                    <td class="px-3 py-2 text-xs text-on-surface-variant">{{ $grade->appreciation ?? '—' }}</td>
                    <td class="px-3 py-2 text-xs text-on-surface-variant">{{ strtoupper($grade->periode ?? '') }}</td>
                    <td class="px-3 py-2 text-right">
                        <div class="flex justify-end gap-1">
                            <button class="p-1 text-primary hover:bg-primary-fixed rounded transition-colors" onclick="editGrade({{ $grade->id }})" title="Modifier">
                                <span class="material-symbols-outlined text-sm">edit</span>
                            </button>
                            <button class="p-1 text-alert-red hover:bg-alert-red/10 rounded transition-colors" onclick="confirmDelete({{ $grade->id }})" title="Supprimer">
                                <span class="material-symbols-outlined text-sm">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td class="py-10 text-center" colspan="8">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-12 h-12 bg-surface-container rounded-full flex items-center justify-center mb-3">
                                <span class="material-symbols-outlined text-2xl text-outline">database</span>
                            </div>
                            <h4 class="font-headline-sm text-sm text-primary mb-1">Aucune donnée trouvée</h4>
                            <p class="text-text-muted text-xs max-w-sm mx-auto">Aucune note n'a été saisie pour vos classes et matières.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
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

<!-- Modal AGRANDI -->
<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="noteModal">
    <div class="absolute inset-0 modal-overlay backdrop-blur-sm bg-black/30" onclick="closeModal()"></div>
    <div class="bg-surface-container-lowest w-full max-w-lg rounded-xl shadow-2xl border border-outline-variant overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="noteModalContent">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-primary text-white">
            <h3 class="font-headline-md text-lg" id="modalTitle">Ajouter une nouvelle note</h3>
            <button class="text-white/80 hover:text-white transition-colors" onclick="closeModal()">
                <span class="material-symbols-outlined text-2xl">close</span>
            </button>
        </div>
        
        <!-- Formulaire -->
        <form class="px-6 py-5 space-y-4" id="gradeForm" method="POST" action="{{ route('enseignant.notes.store') }}">
            @csrf
            <input type="hidden" id="gradeId" name="grade_id">
            <input type="hidden" id="methodField" name="_method" value="POST">
            
            <!-- Classe + Matière -->
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-sm text-on-surface font-medium">Classe</label>
                    <select class="w-full bg-surface rounded-lg border-outline-variant focus:border-primary focus:ring-primary py-2 px-3 text-sm" name="classe_id" id="classId" required>
                        <option value="">Sélectionner une classe</option>
                        @foreach($classes ?? [] as $classe)
                        <option value="{{ $classe->id }}">{{ $classe->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="block text-sm text-on-surface font-medium">Matière</label>
                    <select class="w-full bg-surface rounded-lg border-outline-variant focus:border-primary focus:ring-primary py-2 px-3 text-sm" name="matiere_id" id="subjectId" required>
                        <option value="">Sélectionner une matière</option>
                        @foreach($subjects ?? [] as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->nom }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <!-- Élève -->
            <div class="space-y-1.5">
                <label class="block text-sm text-on-surface font-medium">Élève</label>
                <select class="w-full bg-surface rounded-lg border-outline-variant focus:border-primary focus:ring-primary py-2 px-3 text-sm" name="eleve_id" id="studentId" required>
                    <option value="">Sélectionner un élève</option>
                    @foreach($students ?? [] as $student)
                    <option value="{{ $student->id }}">{{ $student->nom }} {{ $student->prenom }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Période + Note -->
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-sm text-on-surface font-medium">Période</label>
                    <select class="w-full bg-surface rounded-lg border-outline-variant focus:border-primary focus:ring-primary py-2 px-3 text-sm" name="periode" id="periodeSelect" required>
                        <option value="t1">1er Trimestre</option>
                        <option value="t2">2ème Trimestre</option>
                        <option value="t3">3ème Trimestre</option>
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="block text-sm text-on-surface font-medium">Note (0 - 20)</label>
                    <input class="w-full bg-surface rounded-lg border-outline-variant focus:border-primary focus:ring-primary py-2 px-3 text-sm" name="note" id="noteInput" max="20" min="0" oninput="updateAppreciation()" placeholder="Ex: 14.5" step="0.25" type="number" required>
                </div>
            </div>
            
            <!-- Appreciation Preview (lecture seule, calculée automatiquement) -->
            <div class="bg-surface-container-low p-4 rounded-lg border border-outline-variant/30 text-center min-h-[70px] flex flex-col items-center justify-center">
                <span class="text-[10px] uppercase font-bold text-text-muted tracking-widest">Appréciation suggérée</span>
                <div class="text-base font-semibold italic" id="appreciationResult">
                    Veuillez saisir une note...
                </div>
            </div>

            <!-- Boutons -->
            <div class="flex gap-3 pt-2">
                <button class="flex-1 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant text-sm font-medium hover:bg-surface-container transition-colors" onclick="closeModal()" type="button">Annuler</button>
                <button class="flex-1 px-4 py-2.5 rounded-lg bg-primary text-white text-sm font-medium hover:opacity-90 shadow-md transition-all" type="submit">Enregistrer la note</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .shadow-ambient {
        box-shadow: 0 2px 8px rgba(55, 48, 163, 0.04);
    }
    .card-shadow {
        box-shadow: 0 2px 8px rgba(55, 48, 163, 0.1);
    }
    .modal-overlay {
        transition: backdrop-filter 0.3s ease;
    }
    #noteModal {
        transition: opacity 0.3s ease;
    }
</style>
@endpush

@push('scripts')
<script>
    let isEditMode = false;
    let editGradeId = null;

    function openModal() {
        const modal = document.getElementById('noteModal');
        const content = document.getElementById('noteModalContent');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
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
            document.body.style.overflow = 'auto';
            resetForm();
        }, 300);
    }

function resetForm() {
        isEditMode = false;
        editGradeId = null;
        document.getElementById('modalTitle').textContent = 'Ajouter une nouvelle note';
        document.getElementById('gradeForm').reset();
        document.getElementById('gradeId').value = '';
        document.getElementById('methodField').value = 'POST';
        document.getElementById('appreciationResult').innerHTML = 'Veuillez saisir une note...';
        document.getElementById('appreciationResult').className = 'text-base font-semibold italic';
        const form = document.getElementById('gradeForm');
        form.action = '{{ route("enseignant.notes.store") }}';
        form.querySelector('[name="_method"]').value = 'POST';
    }

    function editGrade(id) {
        isEditMode = true;
        editGradeId = id;
        document.getElementById('modalTitle').textContent = 'Modifier la note';
        document.getElementById('gradeId').value = id;
        document.getElementById('methodField').value = 'PUT';
        const form = document.getElementById('gradeForm');
        form.action = '{{ route("enseignant.notes.update", ["note" => "__NOTE__"]) }}'.replace('__NOTE__', id);
        form.querySelector('[name="_method"]').value = 'PUT';

        // Récupérer les données de la note pour préremplir le formulaire
        fetch('{{ route("enseignant.notes.edit", ["note" => "__NOTE__"]) }}'.replace('__NOTE__', id), {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json().then(data => ({ ok: response.ok, data })))
        .then(({ ok, data }) => {
            if (!ok) {
                Swal.fire({ title: 'Erreur', text: data.message || 'Impossible de charger la note.', icon: 'error', confirmButtonColor: '#1f108e', borderRadius: '8px', customClass: { title: 'text-sm', htmlContainer: 'text-xs' } });
                return;
            }
            const note = data.note;
            document.getElementById('classId').value = note.classe_id;
            document.getElementById('subjectId').value = note.matiere_id;
            document.getElementById('noteInput').value = note.note;
            document.getElementById('periodeSelect').value = note.periode || 't1';


            // Charger les élèves de la classe puis sélectionner l'élève de la note
            const studentSelect = document.getElementById('studentId');
            studentSelect.innerHTML = '<option value="">Sélectionner un élève</option>';
            fetch('{{ route("enseignant.notes.data") }}?classe_id=' + note.classe_id, {
                headers: { 'Accept': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                (data.students || []).forEach(student => {
                    const option = document.createElement('option');
                    option.value = student.id;
                    option.textContent = student.nom + ' ' + (student.prenom || '');
                    if (student.id == note.eleve_id) option.selected = true;
                    studentSelect.appendChild(option);
                });
            })
            .catch(() => {});

            updateAppreciation();
        })
        .catch(() => {
            Swal.fire({ title: 'Erreur', text: 'Impossible de charger la note.', icon: 'error', confirmButtonColor: '#1f108e', borderRadius: '8px', customClass: { title: 'text-sm', htmlContainer: 'text-xs' } });
        });

        updateAppreciation();
        openModal();
    }

    function updateAppreciation() {
        const val = parseFloat(document.getElementById('noteInput').value);
        const display = document.getElementById('appreciationResult');
        if (isNaN(val)) {
            display.innerHTML = "Veuillez saisir une note...";
            display.className = "text-base font-semibold italic";
            return;
        }
        if (val > 20 || val < 0) {
            display.innerHTML = "⚠️ Note invalide";
            display.className = "text-base font-bold text-alert-red";
            return;
        }
        if (val >= 16) {
            display.innerHTML = "⭐ Excellent";
            display.className = "text-base font-bold text-success-green";
        } else if (val >= 14) {
            display.innerHTML = "👍 Très Bien";
            display.className = "text-base font-semibold text-success-green";
        } else if (val >= 12) {
            display.innerHTML = "👌 Bien";
            display.className = "text-base font-medium text-primary";
        } else if (val >= 10) {
            display.innerHTML = "📖 Passable";
            display.className = "text-base font-medium text-warning-amber";
        } else {
            display.innerHTML = "⚠️ Insuffisant";
            display.className = "text-base font-bold text-alert-red";
        }
    }

    function confirmDelete(id) {
        Swal.fire({
            title: 'Êtes-vous sûr ?',
            text: "Supprimer cette note ?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#E11D48',
            cancelButtonColor: '#64748B',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler',
            borderRadius: '8px',
            customClass: {
                title: 'text-sm',
                htmlContainer: 'text-xs'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("enseignant.notes.destroy", ["note" => "__DELETE_ID__"]) }}'.replace('__DELETE_ID__', id);
                form.innerHTML = '@csrf @method("DELETE")';
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Charger les élèves par classe
    document.getElementById('classId')?.addEventListener('change', function() {
        const classId = this.value;
        const studentSelect = document.getElementById('studentId');
        if (!classId) {
            studentSelect.innerHTML = '<option value="">Sélectionner un élève</option>';
            return;
        }
        fetch('{{ route("enseignant.notes.data") }}?classe_id=' + classId, {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            studentSelect.innerHTML = '<option value="">Sélectionner un élève</option>';
            (data.students || []).forEach(student => {
                const option = document.createElement('option');
                option.value = student.id;
                option.textContent = student.nom + ' ' + (student.prenom || '');
                studentSelect.appendChild(option);
            });
        })
        .catch(() => {
            studentSelect.innerHTML = '<option value="">Sélectionner un élève</option>';
        });
    });

    // Validation
    const gradeForm = document.getElementById('gradeForm');
    if (gradeForm) {
        gradeForm.addEventListener('submit', function(e) {
            const noteValue = parseFloat(document.getElementById('noteInput').value);
            if (noteValue > 20 || noteValue < 0) {
                e.preventDefault();
                Swal.fire({
                    title: 'Note invalide',
                    text: 'La note doit être comprise entre 0 et 20.',
                    icon: 'error',
                    confirmButtonColor: '#1f108e',
                    borderRadius: '8px',
                    customClass: {
                        title: 'text-sm',
                        htmlContainer: 'text-xs'
                    }
                });
            }
        });
    }

    // Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const modal = document.getElementById('noteModal');
            if (modal && modal.classList.contains('flex')) {
                closeModal();
            }
        }
    });

    // Filters
    document.getElementById('filterClasse')?.addEventListener('change', applyFilters);
    document.getElementById('filterMatiere')?.addEventListener('change', applyFilters);
    document.getElementById('filterEleve')?.addEventListener('change', applyFilters);
    document.getElementById('filterPeriode')?.addEventListener('change', applyFilters);

    function applyFilters() {
        const classe = document.getElementById('filterClasse').value;
        const matiere = document.getElementById('filterMatiere').value;
        const eleve = document.getElementById('filterEleve').value;
        const periode = document.getElementById('filterPeriode').value;
        const params = new URLSearchParams(window.location.search);
        if (classe) params.set('classe_id', classe); else params.delete('classe_id');
        if (matiere) params.set('matiere_id', matiere); else params.delete('matiere_id');
        if (eleve) params.set('eleve_id', eleve); else params.delete('eleve_id');
        if (periode) params.set('periode', periode); else params.delete('periode');
        window.location.search = params.toString();
    }

    function resetFilters() {
        window.location.search = '';
        Swal.fire({ title: 'Filtres réinitialisés', icon: 'success', timer: 1000, showConfirmButton: false, customClass: { title: 'text-sm' } });
    }
</script>
@endpush