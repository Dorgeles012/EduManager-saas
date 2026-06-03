@extends('client.layouts.app')

@section('title', 'EduAdmin Pro - Gestion des Enseignants')

@section('content')
<!-- Header Section avec bouton -->
<div class="flex justify-between items-end mb-10">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-primary">Gestion des Enseignants</h2>
        <p class="text-body-md text-on-surface-variant">Gérez les enseignants de votre établissement avec précision et clarté.</p>
    </div>
    <button class="inline-flex items-center px-5 py-2.5 bg-primary text-white font-label-md text-label-md rounded-lg hover:bg-primary/90 active:scale-95 transition-all shadow-md" onclick="toggleModal('add-teacher-modal')">
        <span class="material-symbols-outlined mr-2">add</span>
        Ajouter un enseignant
    </button>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <!-- Card 1 -->
    <div class="bg-surface-container-lowest rounded-xl p-6 custom-shadow border border-outline-variant/30 flex items-center space-x-5">
        <div class="w-14 h-14 rounded-lg bg-gradient-to-br from-primary to-primary-container flex items-center justify-center shadow-lg">
            <span class="material-symbols-outlined text-white text-3xl">school</span>
        </div>
        <div>
            <p class="text-label-sm text-on-surface-variant uppercase tracking-wider">Enseignants</p>
            <p class="font-headline-md text-headline-md text-on-surface">{{ $totalTeachers ?? 1 }}</p>
        </div>
    </div>
    <!-- Card 2 -->
    <div class="bg-surface-container-lowest rounded-xl p-6 custom-shadow border border-outline-variant/30 flex items-center space-x-5">
        <div class="w-14 h-14 rounded-lg bg-gradient-to-br from-secondary to-secondary-container flex items-center justify-center shadow-lg">
            <span class="material-symbols-outlined text-white text-3xl">book</span>
        </div>
        <div>
            <p class="text-label-sm text-on-surface-variant uppercase tracking-wider">Matières</p>
            <p class="font-headline-md text-headline-md text-on-surface">{{ $totalSubjects ?? 3 }}</p>
        </div>
    </div>
    <!-- Card 3 -->
    <div class="bg-surface-container-lowest rounded-xl p-6 custom-shadow border border-outline-variant/30 flex items-center space-x-5">
        <div class="w-14 h-14 rounded-lg bg-gradient-to-br from-primary-container to-secondary-container flex items-center justify-center shadow-lg">
            <span class="material-symbols-outlined text-white text-3xl">user_attributes</span>
        </div>
        <div>
            <p class="text-label-sm text-on-surface-variant uppercase tracking-wider">Moy. par matière</p>
            <p class="font-headline-md text-headline-md text-on-surface">{{ $avgPerSubject ?? 0.3 }}</p>
        </div>
    </div>
</div>

<!-- Main Content Card -->
<div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant/30 overflow-hidden">
    <!-- Header avec titre et barre de recherche -->
    <div class="px-6 py-6 border-b border-surface-subtle flex flex-col md:flex-row md:items-center justify-between gap-4">
        <h4 class="font-headline-md text-headline-md flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">people</span>
            Liste des enseignants
        </h4>
        <div class="relative w-full md:w-80">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-xl">search</span>
            <input type="text" id="searchTeacher" placeholder="Rechercher un enseignant par nom, email ou matière..." class="w-full pl-10 pr-4 py-2.5 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-surface-container-lowest">
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse" id="teachersTable">
            <thead class="bg-surface-container-low">
                <tr>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase">N°</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase">Nom &amp; Prénoms</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase">Email</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase">Téléphone</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase">Matière</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase">Statut</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-subtle">
                @forelse($teachers ?? [
                    ['id' => 1, 'firstname' => 'Die osé', 'lastname' => 'emmanuel', 'email' => 'ose@mail.com', 'phone' => '0709090909', 'subject' => 'Anglais', 'status' => 'active'],
                    ['id' => 2, 'firstname' => 'Jean', 'lastname' => 'Kouadio', 'email' => 'jean@mail.com', 'phone' => '0709090910', 'subject' => 'Mathématiques', 'status' => 'active'],
                    ['id' => 3, 'firstname' => 'Marie', 'lastname' => 'Koné', 'email' => 'marie@mail.com', 'phone' => '0709090911', 'subject' => 'Physique chimie', 'status' => 'active']
                ] as $teacher)
                <tr class="hover:bg-surface-subtle transition-colors teacher-row" data-name="{{ strtolower($teacher['firstname'] . ' ' . $teacher['lastname']) }}" data-email="{{ strtolower($teacher['email']) }}" data-subject="{{ strtolower($teacher['subject']) }}" data-position="{{ strtolower($teacher['subject']) }}">
                    <td class="px-6 py-4 text-on-surface-variant">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4 font-semibold text-on-surface">{{ ucfirst($teacher['firstname']) }} {{ ucfirst($teacher['lastname']) }}</td>
                    <td class="px-6 py-4 text-on-surface-variant">{{ $teacher['email'] }}</td>
                    <td class="px-6 py-4 text-on-surface-variant">{{ $teacher['phone'] }}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 bg-primary/10 text-primary text-xs font-bold rounded-full">{{ $teacher['subject'] }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 bg-success-green/10 text-success-green text-xs font-bold rounded-full">
                            {{ ucfirst($teacher['status']) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <button class="w-8 h-8 flex items-center justify-center text-warning-amber hover:bg-warning-amber/10 rounded-full transition-all" onclick="openEditModal({{ json_encode($teacher) }})" title="Modifier">
                                <span class="material-symbols-outlined">edit</span>
                            </button>
                            <button class="w-8 h-8 flex items-center justify-center text-alert-red hover:bg-alert-red/10 rounded-full transition-all" onclick="confirmDelete({{ $teacher['id'] }}, '{{ $teacher['firstname'] }} {{ $teacher['lastname'] }}')" title="Supprimer">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-on-surface-variant">
                        <div class="flex flex-col items-center gap-4">
                            <span class="material-symbols-outlined text-5xl text-outline">school</span>
                            <p>Aucun enseignant trouvé</p>
                            
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-surface-subtle flex items-center justify-between">
        <span class="text-sm text-on-surface-variant" id="paginationInfo">
            @php
                $isPaginator = isset($teachers) && method_exists($teachers, 'links');
            @endphp
            @if($isPaginator)
                Affichage de {{ $teachers->firstItem() }} à {{ $teachers->lastItem() }} sur {{ $teachers->total() }} enseignants
            @else
                Affichage de {{ count($teachers ?? []) }} enseignant(s)
            @endif
        </span>
        <div class="flex space-x-2">
            @if($isPaginator)
                {{ $teachers->links() }}
            @endif
        </div>
    </div>
</div>

<!-- Modal: Ajouter Enseignant (avec scrollbar interne) -->
<div class="fixed inset-0 z-[60] bg-black/50 backdrop-blur-sm hidden items-center justify-center p-4" id="add-teacher-modal">
    <div class="bg-white w-full max-w-2xl max-h-[90vh] rounded-xl custom-shadow overflow-hidden transform transition-all scale-95 opacity-0 duration-300 flex flex-col" id="add-modal-content">
        <div class="px-8 py-6 border-b border-surface-subtle flex items-center justify-between bg-surface-container-low flex-shrink-0">
            <h3 class="font-headline-md text-headline-md text-primary">Ajouter un enseignant</h3>
            <button class="w-10 h-10 flex items-center justify-center text-on-surface-variant hover:bg-white rounded-full transition-all" onclick="toggleModal('add-teacher-modal')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-8">
            <div class="mb-6 p-4 bg-primary/5 border border-primary/20 rounded-lg flex items-start">
                <span class="material-symbols-outlined text-primary mr-3 mt-0.5">info</span>
                <div>
                    <p class="text-sm text-primary font-bold">Mot de passe par défaut : 12345678</p>
                    <p class="text-xs text-primary/70">L'enseignant pourra modifier ce mot de passe lors de sa première connexion.</p>
                </div>
            </div>
            <form class="space-y-5" id="addTeacherForm">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block font-label-md text-label-md text-on-surface">Nom <span class="text-alert-red">*</span></label>
                        <input class="w-full px-4 py-2.5 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" id="teacherLastname" placeholder="Entrez le nom" required type="text">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-label-md text-label-md text-on-surface">Prénoms <span class="text-alert-red">*</span></label>
                        <input class="w-full px-4 py-2.5 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" id="teacherFirstname" placeholder="Entrez les prénoms" required type="text">
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="block font-label-md text-label-md text-on-surface">Email <span class="text-alert-red">*</span></label>
                    <input class="w-full px-4 py-2.5 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" id="teacherEmail" placeholder="exemple@mail.com" required type="email">
                </div>
                <div class="space-y-1.5">
                    <label class="block font-label-md text-label-md text-on-surface">Téléphone <span class="text-alert-red">*</span></label>
                    <input class="w-full px-4 py-2.5 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" id="teacherPhone" placeholder="01 02 03 04 05" required type="tel">
                </div>
                <div class="space-y-1.5">
                    <label class="block font-label-md text-label-md text-on-surface">Matière enseignée <span class="text-alert-red">*</span></label>
                    <select class="w-full px-4 py-2.5 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" id="teacherSubject" required>
                        <option value="">Sélectionnez une matière</option>
                        @foreach($subjects ?? ['Anglais', 'Mathématiques', 'Physique chimie'] as $subject)
                        <option value="{{ $subject }}">{{ $subject }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
        <div class="px-8 py-6 border-t border-surface-subtle bg-surface-container-low/50 flex space-x-3 justify-end flex-shrink-0">
            <button class="px-6 py-2.5 border border-outline-variant text-on-surface font-label-md text-label-md rounded-lg hover:bg-surface-subtle transition-all" onclick="toggleModal('add-teacher-modal')" type="button">Annuler</button>
            <button class="px-8 py-2.5 bg-primary text-white font-label-md text-label-md rounded-lg hover:bg-primary/90 shadow-md active:scale-95 transition-all" type="submit" form="addTeacherForm">Enregistrer</button>
        </div>
    </div>
</div>

<!-- Modal: Modifier Enseignant (avec scrollbar interne) -->
<div class="fixed inset-0 z-[60] bg-black/50 backdrop-blur-sm hidden items-center justify-center p-4" id="edit-teacher-modal">
    <div class="bg-white w-full max-w-2xl max-h-[90vh] rounded-xl custom-shadow overflow-hidden transform transition-all scale-95 opacity-0 duration-300 flex flex-col" id="edit-modal-content">
        <div class="px-8 py-6 border-b border-surface-subtle flex items-center justify-between bg-surface-container-low flex-shrink-0">
            <h3 class="font-headline-md text-headline-md text-primary">Modifier l'enseignant</h3>
            <button class="w-10 h-10 flex items-center justify-center text-on-surface-variant hover:bg-white rounded-full transition-all" onclick="toggleModal('edit-teacher-modal')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-8">
            <form class="space-y-5" id="editTeacherForm">
                <input type="hidden" id="editTeacherId">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block font-label-md text-label-md text-on-surface">Nom <span class="text-alert-red">*</span></label>
                        <input class="w-full px-4 py-2.5 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" id="editTeacherLastname" required type="text">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-label-md text-label-md text-on-surface">Prénoms <span class="text-alert-red">*</span></label>
                        <input class="w-full px-4 py-2.5 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" id="editTeacherFirstname" required type="text">
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="block font-label-md text-label-md text-on-surface">Email <span class="text-alert-red">*</span></label>
                    <input class="w-full px-4 py-2.5 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" id="editTeacherEmail" required type="email">
                </div>
                <div class="space-y-1.5">
                    <label class="block font-label-md text-label-md text-on-surface">Téléphone <span class="text-alert-red">*</span></label>
                    <input class="w-full px-4 py-2.5 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" id="editTeacherPhone" required type="tel">
                </div>
                <div class="space-y-1.5">
                    <label class="block font-label-md text-label-md text-on-surface">Matière enseignée <span class="text-alert-red">*</span></label>
                    <select class="w-full px-4 py-2.5 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" id="editTeacherSubject" required>
                        @foreach($subjects ?? ['Anglais', 'Mathématiques', 'Physique chimie'] as $subject)
                        <option value="{{ $subject }}">{{ $subject }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
        <div class="px-8 py-6 border-t border-surface-subtle bg-surface-container-low/50 flex space-x-3 justify-end flex-shrink-0">
            <button class="px-6 py-2.5 border border-outline-variant text-on-surface font-label-md text-label-md rounded-lg hover:bg-surface-subtle transition-all" onclick="toggleModal('edit-teacher-modal')" type="button">Annuler</button>
            <button class="px-8 py-2.5 bg-primary text-white font-label-md text-label-md rounded-lg hover:bg-primary/90 shadow-md active:scale-95 transition-all" type="submit" form="editTeacherForm">Enregistrer</button>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    .custom-shadow {
        box-shadow: 0 4px 12px rgba(55, 48, 163, 0.04);
    }
    
    /* Personnalisation de la scrollbar pour les modals */
    .overflow-y-auto::-webkit-scrollbar {
        width: 6px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #c7c7c7;
        border-radius: 10px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: #a0a0a0;
    }
</style>
@endpush

@push('scripts')
<script>
    // Fonction de recherche
    const searchInput = document.getElementById('searchTeacher');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('.teacher-row');
            let visibleCount = 0;
            
            rows.forEach(row => {
                const name = row.getAttribute('data-name');
                const email = row.getAttribute('data-email');
                const subject = row.getAttribute('data-subject');
                const position = row.getAttribute('data-position');
                
                if (name.includes(searchTerm) || email.includes(searchTerm) || subject.includes(searchTerm) || position.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Mettre à jour le texte de pagination
            const paginationSpan = document.getElementById('paginationInfo');
            if (paginationSpan && !paginationSpan.innerHTML.includes('Précédent')) {
                if (visibleCount === 1) {
                    paginationSpan.textContent = `Affichage de 1 sur ${visibleCount} enseignant`;
                } else {
                    paginationSpan.textContent = `Affichage de ${visibleCount} sur ${visibleCount} enseignants`;
                }
            }
        });
    }

    function toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        const contentId = modalId === 'add-teacher-modal' ? 'add-modal-content' : 'edit-modal-content';
        const content = document.getElementById(contentId);
        
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        } else {
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }, 300);
        }
    }

    function openEditModal(teacher) {
        document.getElementById('editTeacherId').value = teacher.id;
        document.getElementById('editTeacherLastname').value = teacher.lastname;
        document.getElementById('editTeacherFirstname').value = teacher.firstname;
        document.getElementById('editTeacherEmail').value = teacher.email;
        document.getElementById('editTeacherPhone').value = teacher.phone;
        
        const subjectSelect = document.getElementById('editTeacherSubject');
        for(let i = 0; i < subjectSelect.options.length; i++) {
            if(subjectSelect.options[i].value === teacher.subject) {
                subjectSelect.selectedIndex = i;
                break;
            }
        }
        
        toggleModal('edit-teacher-modal');
    }

    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Êtes-vous sûr ?',
            html: `L'enseignant <strong>${name}</strong> sera définitivement supprimé.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1f108e',
            cancelButtonColor: '#E11D48',
            confirmButtonText: 'Oui, supprimer !',
            cancelButtonText: 'Annuler',
            borderRadius: '12px'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Supprimé !',
                    text: 'L\'enseignant a été supprimé avec succès.',
                    icon: 'success',
                    confirmButtonColor: '#1f108e',
                    borderRadius: '12px'
                });
            }
        });
    }

    // Add Teacher Form
    const addForm = document.getElementById('addTeacherForm');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const name = document.getElementById('teacherFirstname').value + ' ' + document.getElementById('teacherLastname').value;
            
            Swal.fire({
                title: 'Confirmation',
                text: `Souhaitez-vous ajouter l'enseignant "${name}" ?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1f108e',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Oui, ajouter',
                cancelButtonText: 'Annuler',
                borderRadius: '12px'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Succès !',
                        text: 'L\'enseignant a été ajouté avec succès.',
                        icon: 'success',
                        confirmButtonColor: '#1f108e',
                        timer: 2000,
                        timerProgressBar: true,
                        borderRadius: '12px'
                    });
                    toggleModal('add-teacher-modal');
                    addForm.reset();
                }
            });
        });
    }

    // Edit Teacher Form
    const editForm = document.getElementById('editTeacherForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const name = document.getElementById('editTeacherFirstname').value + ' ' + document.getElementById('editTeacherLastname').value;
            
            Swal.fire({
                title: 'Confirmation',
                text: `Souhaitez-vous modifier l'enseignant "${name}" ?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#D97706',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Oui, modifier',
                cancelButtonText: 'Annuler',
                borderRadius: '12px'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Modifié !',
                        text: 'Les modifications ont été enregistrées avec succès.',
                        icon: 'success',
                        confirmButtonColor: '#D97706',
                        timer: 2000,
                        timerProgressBar: true,
                        borderRadius: '12px'
                    });
                    toggleModal('edit-teacher-modal');
                }
            });
        });
    }

    // Close modal on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const modals = ['add-teacher-modal', 'edit-teacher-modal'];
            modals.forEach(id => {
                const modal = document.getElementById(id);
                if (modal && !modal.classList.contains('hidden')) {
                    toggleModal(id);
                }
            });
        }
    });

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modals = ['add-teacher-modal', 'edit-teacher-modal'];
        modals.forEach(id => {
            const modal = document.getElementById(id);
            if (event.target == modal && modal && !modal.classList.contains('hidden')) {
                toggleModal(id);
            }
        });
    }
</script>
@endpush