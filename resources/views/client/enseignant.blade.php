@extends('client.layouts.app')
@section('title', 'EduManager - Enseignants')
@section('content')
<!-- Header Section avec bouton -->
<div class="flex justify-between items-end mb-10">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-primary">Gestion des Enseignants</h2>
        <p class="text-body-md text-on-surface-variant">Gérez les enseignants de votre établissement avec précision et clarté.</p>
    </div>
    <button class="inline-flex items-center px-5 py-2.5 bg-primary text-white font-label-md text-label-md rounded-lg hover:bg-primary/90 active:scale-95 transition-all shadow-md" onclick="openModal('add-teacher-modal')">
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
            <p class="font-headline-md text-headline-md text-on-surface" id="totalTeachers">{{ $totalTeachers ?? 1 }}</p>
        </div>
    </div>
    <!-- Card 2 -->
    <div class="bg-surface-container-lowest rounded-xl p-6 custom-shadow border border-outline-variant/30 flex items-center space-x-5">
        <div class="w-14 h-14 rounded-lg bg-orange-500 flex items-center justify-center shadow-lg">
            <span class="material-symbols-outlined text-white text-3xl">book</span>
        </div>
        <div>
            <p class="text-label-sm text-on-surface-variant uppercase tracking-wider">Matières</p>
            <p class="font-headline-md text-headline-md text-on-surface">{{ $totalSubjects ?? 3 }}</p>
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
        <table class="w-full text-left border-collapse text-sm" id="teachersTable">
            <thead class="bg-surface-container-low">
                <tr>
                    <th class="px-4 py-3 font-label-sm text-label-sm text-on-surface-variant uppercase text-xs">N°</th>
                    <th class="px-4 py-3 font-label-sm text-label-sm text-on-surface-variant uppercase text-xs">Nom &amp; Prénoms</th>
                    <th class="px-4 py-3 font-label-sm text-label-sm text-on-surface-variant uppercase text-xs">Email</th>
                    <th class="px-4 py-3 font-label-sm text-label-sm text-on-surface-variant uppercase text-xs">Téléphone</th>
                    <th class="px-4 py-3 font-label-sm text-label-sm text-on-surface-variant uppercase text-xs">Matière</th>
                    <th class="px-4 py-3 font-label-sm text-label-sm text-on-surface-variant uppercase text-xs">Statut</th>
                    <th class="px-4 py-3 font-label-sm text-label-sm text-on-surface-variant uppercase text-xs text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-subtle" id="teachersTableBody">
                @forelse($teachers ?? [] as $teacher)
                <tr class="hover:bg-surface-subtle transition-colors teacher-row text-sm" id="teacher-row-{{ $teacher['id'] }}" data-name="{{ strtolower($teacher['firstname'] . ' ' . $teacher['lastname']) }}" data-email="{{ strtolower($teacher['email']) }}" data-subject="{{ strtolower($teacher['subject']) }}" data-position="{{ strtolower($teacher['subject']) }}">
                    <td class="px-4 py-3 text-on-surface-variant text-sm">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3 font-semibold text-on-surface text-sm">{{ ucfirst($teacher['firstname']) }} {{ ucfirst($teacher['lastname']) }}</td>
                    <td class="px-4 py-3 text-on-surface-variant text-sm">{{ $teacher['email'] }}</td>
                    <td class="px-4 py-3 text-on-surface-variant text-sm">{{ $teacher['phone'] }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2.5 py-1 bg-primary/10 text-primary text-xs font-bold rounded-full">{{ $teacher['subject'] }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2.5 py-1 bg-success-green/10 text-success-green text-xs font-bold rounded-full">
                            {{ ucfirst($teacher['status']) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end space-x-1.5">
                            <button class="w-8 h-8 flex items-center justify-center text-warning-amber hover:bg-warning-amber/10 rounded-full transition-all" onclick="openEditModal({{ json_encode($teacher) }})" title="Modifier">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </button>
                            <button class="w-8 h-8 flex items-center justify-center text-alert-red hover:bg-alert-red/10 rounded-full transition-all delete-teacher-btn" data-id="{{ $teacher['id'] }}" data-name="{{ $teacher['firstname'] }} {{ $teacher['lastname'] }}" type="button" title="Supprimer">
                                <span class="material-symbols-outlined text-base">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="emptyRow">
                    <td colspan="7" class="px-4 py-12 text-center text-on-surface-variant text-sm">
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

<!-- Modal: Ajouter Enseignant -->
<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="add-teacher-modal">
    <div class="absolute inset-0 modal-overlay backdrop-blur-md bg-black/30" onclick="closeModal('add-teacher-modal')"></div>
    <div class="bg-white w-full max-w-2xl max-h-[90vh] rounded-xl custom-shadow overflow-hidden transform transition-all duration-300 scale-95 opacity-0 flex flex-col" id="add-modal-content">
        <div class="px-8 py-6 border-b border-surface-subtle flex items-center justify-between bg-primary text-white flex-shrink-0">
            <h3 class="font-headline-md text-headline-md">Ajouter un enseignant</h3>
            <button class="w-10 h-10 flex items-center justify-center hover:bg-white/20 rounded-full transition-all" onclick="closeModal('add-teacher-modal')">
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
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block font-label-md text-label-md text-on-surface">Nom <span class="text-alert-red">*</span></label>
                        <input class="w-full px-4 py-2.5 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" id="addLastName" placeholder="Entrez le nom" required type="text">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-label-md text-label-md text-on-surface">Prénoms <span class="text-alert-red">*</span></label>
                        <input class="w-full px-4 py-2.5 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" id="addFirstName" placeholder="Entrez les prénoms" required type="text">
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="block font-label-md text-label-md text-on-surface">Email <span class="text-alert-red">*</span></label>
                    <input class="w-full px-4 py-2.5 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" id="addEmail" placeholder="exemple@mail.com" required type="email">
                </div>
                <div class="space-y-1.5">
                    <label class="block font-label-md text-label-md text-on-surface">Téléphone <span class="text-alert-red">*</span></label>
                    <input class="w-full px-4 py-2.5 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" id="addPhone" placeholder="01 02 03 04 05" required type="tel">
                </div>
                <div class="space-y-1.5">
                    <label class="block font-label-md text-label-md text-on-surface">Matière enseignée <span class="text-alert-red">*</span></label>
                    <select class="w-full px-4 py-2.5 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" id="addSubject" required>
                        <option value="">Sélectionnez une matière</option>
                        @foreach($subjects ?? ['Anglais', 'Mathématiques', 'Physique chimie'] as $subject)
                        <option value="{{ is_array($subject) ? $subject['id'] : $subject }}">{{ is_array($subject) ? $subject['name'] : $subject }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1.5"><label class="block font-label-md text-label-md text-on-surface">Classes affectées <span class="text-alert-red">*</span></label><select id="addClasses" multiple required class="w-full px-4 py-2.5 border border-outline-variant rounded-lg"><option disabled>Maintenez Ctrl/Cmd pour sélectionner</option>@foreach($classes as $classe)<option value="{{ $classe->id }}">{{ $classe->nom }}</option>@endforeach</select></div>
            </form>
        </div>
        <div class="px-8 py-6 border-t border-surface-subtle bg-surface-container-low/50 flex space-x-3 justify-end flex-shrink-0">
            <button class="px-6 py-2.5 border border-outline-variant text-on-surface font-label-md text-label-md rounded-lg hover:bg-surface-subtle transition-all" onclick="closeModal('add-teacher-modal')" type="button">Annuler</button>
            <button class="px-8 py-2.5 bg-primary text-white font-label-md text-label-md rounded-lg hover:bg-primary/90 shadow-md active:scale-95 transition-all" id="addTeacherBtn" type="button">Enregistrer</button>
        </div>
    </div>
</div>

<!-- Modal: Modifier Enseignant -->
<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="edit-teacher-modal">
    <div class="absolute inset-0 modal-overlay backdrop-blur-md bg-black/30" onclick="closeModal('edit-teacher-modal')"></div>
    <div class="bg-white w-full max-w-2xl max-h-[90vh] rounded-xl custom-shadow overflow-hidden transform transition-all duration-300 scale-95 opacity-0 flex flex-col" id="edit-modal-content">
        <div class="px-8 py-6 border-b border-surface-subtle flex items-center justify-between bg-primary text-white flex-shrink-0">
            <h3 class="font-headline-md text-headline-md">Modifier l'enseignant</h3>
            <button class="w-10 h-10 flex items-center justify-center hover:bg-white/20 rounded-full transition-all" onclick="closeModal('edit-teacher-modal')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-8">
            <form class="space-y-5" id="editTeacherForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editTeacherId">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block font-label-md text-label-md text-on-surface">Nom <span class="text-alert-red">*</span></label>
                        <input class="w-full px-4 py-2.5 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" id="editLastName" required type="text">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-label-md text-label-md text-on-surface">Prénoms <span class="text-alert-red">*</span></label>
                        <input class="w-full px-4 py-2.5 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" id="editFirstName" required type="text">
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="block font-label-md text-label-md text-on-surface">Email <span class="text-alert-red">*</span></label>
                    <input class="w-full px-4 py-2.5 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" id="editEmail" required type="email">
                </div>
                <div class="space-y-1.5">
                    <label class="block font-label-md text-label-md text-on-surface">Téléphone <span class="text-alert-red">*</span></label>
                    <input class="w-full px-4 py-2.5 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" id="editPhone" required type="tel">
                </div>
                <div class="space-y-1.5">
                    <label class="block font-label-md text-label-md text-on-surface">Matière enseignée <span class="text-alert-red">*</span></label>
                    <select class="w-full px-4 py-2.5 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" id="editSubject" required>
                        <option value="">Sélectionnez une matière</option>
                        @foreach($subjects ?? ['Anglais', 'Mathématiques', 'Physique chimie'] as $subject)
                        <option value="{{ is_array($subject) ? $subject['id'] : $subject }}">{{ is_array($subject) ? $subject['name'] : $subject }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1.5"><label class="block font-label-md text-label-md text-on-surface">Classes affectées <span class="text-alert-red">*</span></label><select id="editClasses" multiple required class="w-full px-4 py-2.5 border border-outline-variant"><option disabled>Maintenez Ctrl/Cmd pour sélectionner</option>@foreach($classes as $classe)<option value="{{ $classe->id }}">{{ $classe->nom }}</option>@endforeach</select></div>
            </form>
        </div>
        <div class="px-8 py-6 border-t border-surface-subtle bg-surface-container-low/50 flex space-x-3 justify-end flex-shrink-0">
            <button class="px-6 py-2.5 border border-outline-variant text-on-surface font-label-md text-label-md rounded-lg hover:bg-surface-subtle transition-all" onclick="closeModal('edit-teacher-modal')" type="button">Annuler</button>
            <button class="px-8 py-2.5 bg-primary text-white font-label-md text-label-md rounded-lg hover:bg-primary/90 shadow-md active:scale-95 transition-all" id="editTeacherBtn" type="button">Enregistrer</button>
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
    
    .modal-overlay {
        transition: backdrop-filter 0.3s ease;
    }
    
    #add-teacher-modal, #edit-teacher-modal {
        transition: opacity 0.3s ease;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Variables
    let rowCounter = document.querySelectorAll('.teacher-row').length;

    // ============ AJOUT ============
    document.getElementById('addTeacherBtn').addEventListener('click', function() {
        const lastName = document.getElementById('addLastName').value.trim();
        const firstName = document.getElementById('addFirstName').value.trim();
        const email = document.getElementById('addEmail').value.trim();
        const phone = document.getElementById('addPhone').value.trim();
        const subject = document.getElementById('addSubject').value;

        // Validation simple
        if (!lastName || !firstName || !email || !phone || !subject) {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Veuillez remplir tous les champs',
                showConfirmButton: false,
                timer: 2000,
                position: 'center'
            });
            return;
        }

        const formData = new FormData();
        formData.append('nom', lastName);
        formData.append('prenoms', firstName);
        formData.append('email', email);
        formData.append('telephone', phone);
        formData.append('matiere_id', subject);
        Array.from(document.getElementById('addClasses').selectedOptions).forEach(option => formData.append('classe_ids[]', option.value));
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}');

        // Désactiver le bouton
        const btn = this;
        btn.disabled = true;
        btn.textContent = 'Enregistrement...';

        fetch('{{ route("client.enseignant.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Ajouter la ligne dans le tableau
                addTeacherRow(data.teacher);
                
                // Mettre à jour le compteur
                updateTotalTeachers(1);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Succès !',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 2000,
                    borderRadius: '12px',
                    position: 'center'
                });
                
                // Fermer le modal et réinitialiser le formulaire
                closeModal('add-teacher-modal');
                document.getElementById('addTeacherForm').reset();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: data.message || 'Une erreur est survenue',
                    showConfirmButton: false,
                    timer: 2000,
                    borderRadius: '12px',
                    position: 'center'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Une erreur est survenue lors de l\'ajout',
                showConfirmButton: false,
                timer: 2000,
                borderRadius: '12px',
                position: 'center'
            });
        })
        .finally(() => {
            btn.disabled = false;
            btn.textContent = 'Enregistrer';
        });
    });

    // ============ MODIFICATION ============
    document.getElementById('editTeacherBtn').addEventListener('click', function() {
        const id = document.getElementById('editTeacherId').value;
        const lastName = document.getElementById('editLastName').value.trim();
        const firstName = document.getElementById('editFirstName').value.trim();
        const email = document.getElementById('editEmail').value.trim();
        const phone = document.getElementById('editPhone').value.trim();
        const subject = document.getElementById('editSubject').value;

        if (!lastName || !firstName || !email || !phone || !subject) {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Veuillez remplir tous les champs',
                showConfirmButton: false,
                timer: 2000,
                borderRadius: '12px',
                position: 'center'
            });
            return;
        }

        const formData = new FormData();
        formData.append('nom', lastName);
        formData.append('prenoms', firstName);
        formData.append('email', email);
        formData.append('telephone', phone);
        formData.append('matiere_id', subject);
        Array.from(document.getElementById('editClasses').selectedOptions).forEach(option => formData.append('classe_ids[]', option.value));
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}');
        formData.append('_method', 'PUT');

        const btn = this;
        btn.disabled = true;
        btn.textContent = 'Modification...';

        fetch(`{{ url("/client/enseignant") }}/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mettre à jour la ligne dans le tableau
                updateTeacherRow(data.teacher);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Succès !',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 2000,
                    borderRadius: '12px',
                    position: 'center'
                });
                
                closeModal('edit-teacher-modal');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: data.message || 'Une erreur est survenue',
                    showConfirmButton: false,
                    timer: 2000,
                    borderRadius: '12px',
                    position: 'center'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Une erreur est survenue lors de la modification',
                showConfirmButton: false,
                timer: 2000,
                borderRadius: '12px',
                position: 'center'
            });
        })
        .finally(() => {
            btn.disabled = false;
            btn.textContent = 'Enregistrer';
        });
    });

    // ============ SUPPRESSION ============
    document.querySelectorAll('.delete-teacher-btn').forEach((button) => {
        button.addEventListener('click', function() {
            const teacherId = this.dataset.id;
            const teacherName = this.dataset.name;

            Swal.fire({
                title: 'Confirmer la suppression',
                text: `Êtes-vous sûr de vouloir supprimer l'enseignant "${teacherName}" ?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
                reverseButtons: true,
                borderRadius: '12px'
            }).then((result) => {
                if (result.isConfirmed) {
                    const btn = this;
                    btn.disabled = true;

                    fetch(`{{ url("/client/enseignant") }}/${teacherId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Supprimer la ligne du tableau
                            const row = document.getElementById(`teacher-row-${teacherId}`);
                            if (row) {
                                row.remove();
                            }
                            
                            // Mettre à jour le compteur
                            updateTotalTeachers(-1);
                            
                            // Vérifier si le tableau est vide
                            checkEmptyTable();
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Supprimé !',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 2000,
                                borderRadius: '12px',
                                position: 'center'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: data.message || 'Une erreur est survenue',
                                showConfirmButton: false,
                                timer: 2000,
                                borderRadius: '12px',
                                position: 'center'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'Une erreur est survenue lors de la suppression',
                            showConfirmButton: false,
                            timer: 2000,
                            borderRadius: '12px',
                            position: 'center'
                        });
                    })
                    .finally(() => {
                        btn.disabled = false;
                    });
                }
            });
        });
    });

    // ============ FONCTIONS UTILITAIRES ============
    
    // Ajouter une ligne dans le tableau
    function addTeacherRow(teacher) {
        const tbody = document.getElementById('teachersTableBody');
        const emptyRow = document.getElementById('emptyRow');
        if (emptyRow) emptyRow.remove();

        const rowCount = tbody.querySelectorAll('.teacher-row').length + 1;
        
        const row = document.createElement('tr');
        row.className = 'hover:bg-surface-subtle transition-colors teacher-row text-sm';
        row.id = `teacher-row-${teacher.id}`;
        row.setAttribute('data-name', `${teacher.firstname} ${teacher.lastname}`.toLowerCase());
        row.setAttribute('data-email', teacher.email.toLowerCase());
        row.setAttribute('data-subject', teacher.subject.toLowerCase());
        row.setAttribute('data-position', teacher.subject.toLowerCase());

        row.innerHTML = `
            <td class="px-4 py-3 text-on-surface-variant text-sm">${rowCount}</td>
            <td class="px-4 py-3 font-semibold text-on-surface text-sm">${teacher.firstname} ${teacher.lastname}</td>
            <td class="px-4 py-3 text-on-surface-variant text-sm">${teacher.email}</td>
            <td class="px-4 py-3 text-on-surface-variant text-sm">${teacher.phone}</td>
            <td class="px-4 py-3">
                <span class="px-2.5 py-1 bg-primary/10 text-primary text-xs font-bold rounded-full">${teacher.subject}</span>
            </td>
            <td class="px-4 py-3">
                <span class="px-2.5 py-1 bg-success-green/10 text-success-green text-xs font-bold rounded-full">${teacher.status}</span>
            </td>
            <td class="px-4 py-3 text-right">
                <div class="flex items-center justify-end space-x-1.5">
                    <button class="w-8 h-8 flex items-center justify-center text-warning-amber hover:bg-warning-amber/10 rounded-full transition-all" onclick='openEditModal(${JSON.stringify(teacher).replace(/'/g, "&#39;")})' title="Modifier">
                        <span class="material-symbols-outlined text-base">edit</span>
                    </button>
                    <button class="w-8 h-8 flex items-center justify-center text-alert-red hover:bg-alert-red/10 rounded-full transition-all delete-teacher-btn" data-id="${teacher.id}" data-name="${teacher.firstname} ${teacher.lastname}" type="button" title="Supprimer">
                        <span class="material-symbols-outlined text-base">delete</span>
                    </button>
                </div>
            </td>
        `;

        tbody.appendChild(row);

        // Re-attacher l'événement de suppression
        row.querySelector('.delete-teacher-btn').addEventListener('click', function() {
            handleDeleteClick(this);
        });

        // Réinitialiser les numéros de ligne
        renumberRows();
    }

    // Mettre à jour une ligne du tableau
    function updateTeacherRow(teacher) {
        const row = document.getElementById(`teacher-row-${teacher.id}`);
        if (row) {
            row.setAttribute('data-name', `${teacher.firstname} ${teacher.lastname}`.toLowerCase());
            row.setAttribute('data-email', teacher.email.toLowerCase());
            row.setAttribute('data-subject', teacher.subject.toLowerCase());
            row.setAttribute('data-position', teacher.subject.toLowerCase());

            const cells = row.querySelectorAll('td');
            if (cells.length >= 6) {
                cells[0].textContent = row.rowIndex;
                cells[1].textContent = `${teacher.firstname} ${teacher.lastname}`;
                cells[2].textContent = teacher.email;
                cells[3].textContent = teacher.phone;
                cells[4].innerHTML = `<span class="px-2.5 py-1 bg-primary/10 text-primary text-xs font-bold rounded-full">${teacher.subject}</span>`;
                cells[5].innerHTML = `<span class="px-2.5 py-1 bg-success-green/10 text-success-green text-xs font-bold rounded-full">${teacher.status}</span>`;
            }
        }
    }

    // Mettre à jour le compteur total
    function updateTotalTeachers(change) {
        const totalSpan = document.getElementById('totalTeachers');
        if (totalSpan) {
            let current = parseInt(totalSpan.textContent) || 0;
            totalSpan.textContent = current + change;
        }
    }

    // Renuméroter les lignes
    function renumberRows() {
        const rows = document.querySelectorAll('.teacher-row');
        rows.forEach((row, index) => {
            const cells = row.querySelectorAll('td');
            if (cells.length > 0) {
                cells[0].textContent = index + 1;
            }
        });
    }

    // Vérifier si le tableau est vide
    function checkEmptyTable() {
        const tbody = document.getElementById('teachersTableBody');
        const rows = tbody.querySelectorAll('.teacher-row');
        if (rows.length === 0) {
            // Supprimer l'ancien empty row s'il existe
            const oldEmpty = document.getElementById('emptyRow');
            if (oldEmpty) oldEmpty.remove();
            
            const emptyRow = document.createElement('tr');
            emptyRow.id = 'emptyRow';
            emptyRow.innerHTML = `
                <td colspan="7" class="px-4 py-12 text-center text-on-surface-variant text-sm">
                    <div class="flex flex-col items-center gap-4">
                        <span class="material-symbols-outlined text-5xl text-outline">school</span>
                        <p>Aucun enseignant trouvé</p>
                    </div>
                </td>
            `;
            tbody.appendChild(emptyRow);
        }
    }

    // Gestion du clic sur suppression (pour les nouveaux éléments)
    function handleDeleteClick(button) {
        const teacherId = button.dataset.id;
        const teacherName = button.dataset.name;

        Swal.fire({
            title: 'Confirmer la suppression',
            text: `Êtes-vous sûr de vouloir supprimer l'enseignant "${teacherName}" ?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler',
            reverseButtons: true,
            borderRadius: '12px'
        }).then((result) => {
            if (result.isConfirmed) {
                const btn = button;
                btn.disabled = true;

                fetch(`{{ url("/client/enseignant") }}/${teacherId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const row = document.getElementById(`teacher-row-${teacherId}`);
                        if (row) {
                            row.remove();
                        }
                        updateTotalTeachers(-1);
                        checkEmptyTable();
                        renumberRows();
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Supprimé !',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 2000,
                            borderRadius: '12px',
                            position: 'center'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: data.message || 'Une erreur est survenue',
                            showConfirmButton: false,
                            timer: 2000,
                            borderRadius: '12px',
                            position: 'center'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Une erreur est survenue lors de la suppression',
                        showConfirmButton: false,
                        timer: 2000,
                        borderRadius: '12px',
                        position: 'center'
                    });
                })
                .finally(() => {
                    btn.disabled = false;
                });
            }
        });
    }

    // ============ OPEN / CLOSE MODAL ============
    window.openModal = function(modalId) {
        const modal = document.getElementById(modalId);
        const contentId = modalId === 'add-teacher-modal' ? 'add-modal-content' : 'edit-modal-content';
        const content = document.getElementById(contentId);

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';

        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    };

    window.closeModal = function(modalId) {
        const modal = document.getElementById(modalId);
        const contentId = modalId === 'add-teacher-modal' ? 'add-modal-content' : 'edit-modal-content';
        const content = document.getElementById(contentId);

        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }, 300);
    };

    window.openEditModal = function(teacher) {
        // Remplir les champs du formulaire de modification
        document.getElementById('editTeacherId').value = teacher.id;
        document.getElementById('editLastName').value = teacher.lastname || '';
        document.getElementById('editFirstName').value = teacher.firstname || '';
        document.getElementById('editEmail').value = teacher.email || '';
        document.getElementById('editPhone').value = teacher.phone || '';

        // Sélectionner la matière
        const subjectSelect = document.getElementById('editSubject');
        if (subjectSelect) {
            // Parcourir toutes les options pour trouver celle qui correspond
            let found = false;
            for (let i = 0; i < subjectSelect.options.length; i++) {
                if (subjectSelect.options[i].value == teacher.subject_id) {
                    subjectSelect.selectedIndex = i;
                    found = true;
                    break;
                }
            }
            // Si non trouvé, sélectionner la première option vide ou la première
            if (!found) {
                subjectSelect.selectedIndex = 0;
            }
        }
        const classSelect = document.getElementById('editClasses');
        if (classSelect) Array.from(classSelect.options).forEach(option => option.selected = (teacher.class_ids || []).map(String).includes(option.value));

        // Ouvrir le modal
        openModal('edit-teacher-modal');
    };

    // Recherche
    const searchInput = document.getElementById('searchTeacher');
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('.teacher-row');
            let visibleCount = 0;

            rows.forEach(row => {
                const name = row.getAttribute('data-name') || '';
                const email = row.getAttribute('data-email') || '';
                const subject = row.getAttribute('data-subject') || '';
                const position = row.getAttribute('data-position') || '';

                if (name.includes(searchTerm) || email.includes(searchTerm) || subject.includes(searchTerm) || position.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            const paginationSpan = document.getElementById('paginationInfo');
            if (paginationSpan && !paginationSpan.innerHTML.includes('Précédent')) {
                paginationSpan.textContent = visibleCount === 1
                    ? `Affichage de 1 sur ${visibleCount} enseignant`
                    : `Affichage de ${visibleCount} sur ${visibleCount} enseignants`;
            }
        });
    }

    // Escape pour fermer les modals
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const modals = ['add-teacher-modal', 'edit-teacher-modal'];
            modals.forEach(id => {
                const modal = document.getElementById(id);
                if (modal && modal.classList.contains('flex')) {
                    closeModal(id);
                }
            });
        }
    });
});
</script>
@endpush
