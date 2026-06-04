@extends('client.layouts.app')
@section('title', 'EduManager - Notes')
@section('content')
<!-- Header & Action -->
<div class="flex justify-between items-end mb-8">
    <div>
        <h2 class="font-headline-md text-[32px] text-primary mb-1">Gestion des Notes</h2>
        <p class="text-text-muted">Consultez et administrez les performances académiques des élèves.</p>
    </div>
    <button class="bg-primary text-on-primary px-6 py-3 rounded-lg flex items-center gap-2 font-label-md hover:opacity-90 transition-all active:scale-95 shadow-md shadow-primary/20" onclick="openModal()">
        <span class="material-symbols-outlined">add</span>
        Ajouter une note
    </button>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-ambient">
        <div class="flex items-center justify-between mb-4">
            <div class="p-2 bg-surface-container rounded-lg text-primary">
                <span class="material-symbols-outlined">person</span>
            </div>
            <span class="text-[10px] font-bold text-success-green uppercase">Total</span>
        </div>
        <h3 class="text-headline-md text-3xl mb-1">{{ $totalStudents ?? 0 }}</h3>
        <p class="text-text-muted text-sm">Élèves</p>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-ambient">
        <div class="flex items-center justify-between mb-4">
            <div class="p-2 bg-surface-container rounded-lg text-primary">
                <span class="material-symbols-outlined">book</span>
            </div>
            <span class="text-[10px] font-bold text-primary uppercase">Actives</span>
        </div>
        <h3 class="text-headline-md text-3xl mb-1">{{ $totalSubjects ?? 3 }}</h3>
        <p class="text-text-muted text-sm">Matières</p>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-ambient">
        <div class="flex items-center justify-between mb-4">
            <div class="p-2 bg-surface-container rounded-lg text-primary">
                <span class="material-symbols-outlined">meeting_room</span>
            </div>
            <span class="text-[10px] font-bold text-warning-amber uppercase">Configurées</span>
        </div>
        <h3 class="text-headline-md text-3xl mb-1">{{ $totalClasses ?? 0 }}</h3>
        <p class="text-text-muted text-sm">Classes</p>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-ambient">
        <div class="flex items-center justify-between mb-4">
            <div class="p-2 bg-surface-container rounded-lg text-primary">
                <span class="material-symbols-outlined">grade</span>
            </div>
            <span class="text-[10px] font-bold text-text-muted uppercase">Saisie</span>
        </div>
        <h3 class="text-headline-md text-3xl mb-1">{{ $totalGrades ?? 0 }}</h3>
        <p class="text-text-muted text-sm">Notes saisies</p>
    </div>
</div>

<!-- Content Area: Table -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-ambient overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-surface-container-low border-b border-outline-variant">
                <tr>
                    <th class="px-6 py-4 text-label-sm uppercase text-text-muted tracking-wider">Élève</th>
                    <th class="px-6 py-4 text-label-sm uppercase text-text-muted tracking-wider">Classe</th>
                    <th class="px-6 py-4 text-label-sm uppercase text-text-muted tracking-wider">Matière</th>
                    <th class="px-6 py-4 text-label-sm uppercase text-text-muted tracking-wider">Note</th>
                    <th class="px-6 py-4 text-label-sm uppercase text-text-muted tracking-wider">Appréciation</th>
                    <th class="px-6 py-4 text-label-sm uppercase text-text-muted tracking-wider">Date</th>
                    <th class="px-6 py-4 text-label-sm uppercase text-text-muted tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/50">
                @forelse($grades ?? [] as $grade)
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary-fixed flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-[18px]">person</span>
                            </div>
                            <span class="font-body-md">{{ $grade['student_name'] }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-on-surface-variant">{{ $grade['class_name'] }}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-label-sm bg-primary/10 text-primary">
                            {{ $grade['subject'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-bold {{ $grade['grade_color'] }}">{{ $grade['grade'] }}</span>
                        <span class="text-on-surface-variant">/20</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-label-sm {{ $grade['appreciation_class'] }}">
                            {{ $grade['appreciation'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-on-surface-variant">{{ $grade['date'] }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <button class="p-2 text-warning-amber hover:bg-warning-amber/10 rounded-lg transition-colors" onclick="editGrade({{ json_encode($grade) }})" title="Modifier">
                                <span class="material-symbols-outlined">edit</span>
                            </button>
                            <button class="p-2 text-alert-red hover:bg-alert-red/10 rounded-lg transition-colors" onclick="confirmDelete({{ $grade['id'] }}, '{{ $grade['student_name'] }}', '{{ $grade['subject'] }}')" title="Supprimer">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td class="py-20 text-center" colspan="7">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-20 h-20 bg-surface-container rounded-full flex items-center justify-center mb-6">
                                <span class="material-symbols-outlined text-4xl text-outline">database</span>
                            </div>
                            <h4 class="font-headline-md text-primary mb-2">Aucune donnée trouvée</h4>
                            <p class="text-text-muted max-w-sm mx-auto">La table des notes n'est pas encore configurée dans la base de données ou ne contient aucun enregistrement.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(($grades ?? collect())->isNotEmpty())
    <div class="px-6 py-4 border-t border-outline-variant bg-surface-container-low/30 flex items-center justify-between">
        <span class="text-label-sm text-text-muted">
            Affichage de {{ $grades->firstItem() ?? 1 }} à {{ $grades->lastItem() ?? count($grades ?? []) }} sur {{ $grades->total() ?? count($grades ?? []) }} notes
        </span>
        <div class="flex gap-2">
            {{ $grades->links() ?? '' }}
        </div>
    </div>
    @endif
</div>

<!-- Modal: Ajouter/Modifier une note -->
<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="noteModal">
    <div class="absolute inset-0 modal-overlay backdrop-blur-md bg-black/30" onclick="closeModal()"></div>
    <div class="bg-surface-container-lowest w-full max-w-lg rounded-xl shadow-2xl border border-outline-variant overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="noteModalContent">
        <div class="px-8 py-6 border-b border-outline-variant flex justify-between items-center bg-primary text-white">
            <h3 class="font-headline-md" id="modalTitle">Ajouter une nouvelle note</h3>
            <button class="text-white/80 hover:text-white transition-colors" onclick="closeModal()">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form class="p-8 space-y-6" id="gradeForm">
            <input type="hidden" id="gradeId">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-label-md text-on-surface">Élève</label>
                    <select class="w-full bg-surface rounded-lg border-outline-variant focus:border-primary focus:ring-primary py-2 text-body-md" id="studentId" required>
                        <option value="">Sélectionner un élève</option>
                        @foreach($students ?? [] as $student)
                        <option value="{{ $student['id'] }}">{{ $student['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="block text-label-md text-on-surface">Classe</label>
                    <select class="w-full bg-surface rounded-lg border-outline-variant focus:border-primary focus:ring-primary py-2 text-body-md" id="classId" required>
                        <option value="">Sélectionner une classe</option>
                        @foreach($classes ?? [] as $class)
                        <option value="{{ $class['id'] }}">{{ $class['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="space-y-2">
                <label class="block text-label-md text-on-surface">Matière</label>
                <select class="w-full bg-surface rounded-lg border-outline-variant focus:border-primary focus:ring-primary py-2 text-body-md" id="subjectId" required>
                    <option value="">Sélectionner une matière</option>
                    @foreach($subjects ?? ['Anglais', 'Mathématiques', 'Physique chimie'] as $subject)
                    <option value="{{ $subject }}">{{ $subject }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-2">
                <label class="block text-label-md text-on-surface">Note (0 - 20)</label>
                <input class="w-full bg-surface rounded-lg border-outline-variant focus:border-primary focus:ring-primary py-2 text-body-md" id="noteInput" max="20" min="0" oninput="updateAppreciation()" placeholder="Ex: 14.5" step="0.25" type="number" required>
            </div>
            <!-- Dynamic Appreciation Preview -->
            <div class="bg-surface-container-low p-6 rounded-lg border border-outline-variant/30 text-center min-h-[100px] flex flex-col items-center justify-center">
                <span class="text-[10px] uppercase font-bold text-text-muted mb-2 tracking-widest">Aperçu de l'appréciation</span>
                <div class="text-headline-md text-primary italic" id="appreciationResult">
                    Veuillez saisir une note...
                </div>
            </div>
            <div class="flex gap-4 pt-4">
                <button class="flex-1 px-4 py-3 rounded-lg border border-outline-variant text-on-surface-variant font-label-md hover:bg-surface-container transition-colors" onclick="closeModal()" type="button">Annuler</button>
                <button class="flex-1 px-4 py-3 rounded-lg bg-primary text-white font-label-md hover:opacity-90 shadow-md transition-all" type="submit">Enregistrer la note</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    :root {
        --primary: #1f108e;
        --primary-container: #3730a3;
    }
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f9f9ff;
        color: #111c2d;
    }
    .font-headline { font-family: 'Lexend', sans-serif; }
    .shadow-ambient {
        box-shadow: 0 4px 12px rgba(55, 48, 163, 0.04);
    }
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        vertical-align: middle;
    }
    
    /* Appreciation classes */
    .appreciation-excellent { background-color: #05966910; color: #059669; }
    .appreciation-good { background-color: #1f108e10; color: #1f108e; }
    .appreciation-average { background-color: #D9770610; color: #D97706; }
    .appreciation-poor { background-color: #E11D4810; color: #E11D48; }
    
    .grade-excellent { color: #059669; }
    .grade-good { color: #1f108e; }
    .grade-average { color: #D97706; }
    .grade-poor { color: #E11D48; }

    /* Custom scrollbar for professional look */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: #f1f5f9; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    
    /* Modal overlay animation */
    .modal-overlay {
        transition: backdrop-filter 0.3s ease;
    }
    
    /* Modal animation */
    #noteModal {
        transition: opacity 0.3s ease;
    }
</style>
@endpush

@push('scripts')
<script>
    let isEditMode = false;

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
        document.getElementById('modalTitle').textContent = 'Ajouter une nouvelle note';
        document.getElementById('gradeForm').reset();
        document.getElementById('gradeId').value = '';
        document.getElementById('appreciationResult').innerHTML = 'Veuillez saisir une note...';
        document.getElementById('appreciationResult').className = 'text-headline-md text-primary italic';
    }

    function editGrade(grade) {
        isEditMode = true;
        document.getElementById('modalTitle').textContent = 'Modifier la note';
        document.getElementById('gradeId').value = grade.id;
        document.getElementById('studentId').value = grade.student_id;
        document.getElementById('classId').value = grade.class_id;
        document.getElementById('subjectId').value = grade.subject;
        document.getElementById('noteInput').value = grade.grade;
        updateAppreciation();
        openModal();
    }

    function updateAppreciation() {
        const val = parseFloat(document.getElementById('noteInput').value);
        const display = document.getElementById('appreciationResult');
        
        if (isNaN(val)) {
            display.innerHTML = "Veuillez saisir une note...";
            display.className = "text-headline-md text-primary italic";
            return;
        }

        if (val > 20 || val < 0) {
            display.innerHTML = "⚠️ Note invalide";
            display.className = "text-headline-md text-alert-red font-bold";
            return;
        }

        if (val >= 16) {
            display.innerHTML = "⭐ Excellent";
            display.className = "text-headline-md text-success-green font-bold";
        } else if (val >= 14) {
            display.innerHTML = "👍 Très Bien";
            display.className = "text-headline-md text-success-green font-semibold";
        } else if (val >= 12) {
            display.innerHTML = "👌 Bien";
            display.className = "text-headline-md text-primary font-medium";
        } else if (val >= 10) {
            display.innerHTML = "📖 Passable";
            display.className = "text-headline-md text-warning-amber font-medium";
        } else {
            display.innerHTML = "⚠️ Insuffisant";
            display.className = "text-headline-md text-alert-red font-bold";
        }
    }

    function confirmDelete(id, studentName, subject) {
        Swal.fire({
            title: 'Êtes-vous sûr ?',
            html: `Supprimer la note de <strong>${studentName}</strong> en <strong>${subject}</strong> ?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#E11D48',
            cancelButtonColor: '#64748B',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler',
            borderRadius: '12px'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Supprimé !',
                    text: 'La note a été supprimée avec succès.',
                    icon: 'success',
                    confirmButtonColor: '#1f108e',
                    borderRadius: '12px'
                });
            }
        });
    }

    // Form Submission
    const gradeForm = document.getElementById('gradeForm');
    if (gradeForm) {
        gradeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const noteValue = parseFloat(document.getElementById('noteInput').value);
            
            if (noteValue > 20 || noteValue < 0) {
                Swal.fire({
                    title: 'Note invalide',
                    text: 'La note doit être comprise entre 0 et 20.',
                    icon: 'error',
                    confirmButtonColor: '#1f108e',
                    borderRadius: '12px'
                });
                return;
            }

            const action = isEditMode ? 'modifiée' : 'ajoutée';
            
            Swal.fire({
                title: 'Confirmation',
                text: `Souhaitez-vous ${action === 'ajoutée' ? 'ajouter' : 'enregistrer les modifications de'} cette note ?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1f108e',
                cancelButtonColor: '#64748B',
                confirmButtonText: 'Oui',
                cancelButtonText: 'Annuler',
                borderRadius: '12px'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Succès !',
                        text: `La note a été ${action} avec succès.`,
                        icon: 'success',
                        confirmButtonColor: '#1f108e',
                        timer: 2000,
                        timerProgressBar: true,
                        borderRadius: '12px'
                    });
                    closeModal();
                }
            });
        });
    }

    // Close modal on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const modal = document.getElementById('noteModal');
            if (modal && modal.classList.contains('flex')) {
                closeModal();
            }
        }
    });
</script>
@endpush