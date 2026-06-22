@extends('client.layouts.app')
@section('title', 'EduManager - Niveaux')
@section('content')
<!-- Page Header -->
<div class="flex justify-between items-end mb-8">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-primary">Gestion des Niveaux</h2>
        <p class="text-body-md text-on-surface-variant">Liste complète des niveaux d'enseignement de votre réseau.</p>
    </div>
    <button class="bg-primary text-on-primary px-6 py-2.5 rounded-lg font-label-md text-label-md flex items-center gap-2 hover:opacity-90 active:scale-95 transition-all card-shadow" id="addLevelBtn">
        <span class="material-symbols-outlined text-[20px]">add</span>
        Ajouter un niveau
    </button>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-gutter-desktop mb-8">
    <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant card-shadow">
        <div class="flex justify-between items-start mb-4">
            <div class="p-2 bg-primary-fixed rounded-lg text-primary">
                <span class="material-symbols-outlined">layers</span>
            </div>
        </div>
        <h3 class="text-on-surface-variant text-label-md mb-1 uppercase tracking-wider">Total des niveaux</h3>
        <p class="font-headline-xl text-headline-xl text-primary" id="totalLevelsCount">{{ $totalLevels ?? 0 }}</p>
    </div>
</div>

<!-- Main Table Container -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant card-shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-white/50">
        <h3 class="font-headline-md text-headline-md text-primary">Liste des Niveaux</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-surface-container-low">
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">N°</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Niveau</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Établissement</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase">Date de création</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant" id="levelsTableBody">
                @forelse($levels ?? [] as $level)
                <tr class="hover:bg-surface-bright transition-colors group" data-level-id="{{ $level['id'] ?? '' }}">
                    <td class="px-6 py-4 text-body-sm font-medium text-on-surface-variant">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <span class="font-body-md font-semibold text-primary level-name">{{ $level['name'] }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-secondary-container/20 text-on-secondary-container px-3 py-1 rounded-full text-label-sm font-medium border border-secondary-container/30 level-school">
                            {{ $level['school'] ?? 'Non assigné' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $level['date'] ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <button class="p-2 text-primary hover:bg-primary-fixed rounded-lg transition-colors edit-btn" data-id="{{ $level['id'] ?? '' }}" data-name="{{ $level['name'] ?? '' }}" data-school-id="{{ $level['school_id'] ?? '' }}" title="Modifier">
                                <span class="material-symbols-outlined">edit</span>
                            </button>
                            <button class="p-2 text-error hover:bg-error-container/20 rounded-lg transition-colors delete-btn" data-id="{{ $level['id'] ?? 0 }}" data-name="{{ $level['name'] ?? '' }}" title="Supprimer" type="button">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="emptyRow">
                    <td colspan="5" class="px-6 py-12 text-center text-on-surface-variant">
                        <div class="flex flex-col items-center gap-4">
                            <span class="material-symbols-outlined text-[48px] text-outline">layers</span>
                            <p>Aucun niveau trouvé</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 bg-surface-container-low border-t border-outline-variant flex justify-between items-center">
        <p class="text-label-sm text-on-surface-variant" id="paginationInfo">Affichage de 1 à {{ count($levels ?? []) }} sur {{ count($levels ?? []) }} niveaux</p>
        <div class="flex gap-1">
            <button class="w-8 h-8 flex items-center justify-center rounded hover:bg-surface-container-high transition-colors disabled:opacity-50" disabled>
                <span class="material-symbols-outlined">chevron_left</span>
            </button>
            <button class="w-8 h-8 flex items-center justify-center rounded bg-primary text-on-primary font-label-sm">1</button>
            <button class="w-8 h-8 flex items-center justify-center rounded hover:bg-surface-container-high transition-colors">
                <span class="material-symbols-outlined">chevron_right</span>
            </button>
        </div>
    </div>
</div>

<!-- Modal: Add Level -->
<div class="hidden fixed inset-0 z-[100] items-center justify-center p-4" id="add-level-modal" style="display: none;">
    <div class="absolute inset-0 modal-backdrop" id="addModalBackdrop"></div>
    <div class="relative bg-surface-container-lowest w-full max-w-md rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="add-level-modal-content">
        <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-primary text-white">
            <h3 class="font-headline-md text-headline-md">Nouveau Niveau</h3>
            <button class="p-1 hover:bg-white/20 rounded-full transition-colors close-modal-btn" data-modal="add-level-modal">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form class="p-6 space-y-5" id="addLevelForm">
            @csrf
            <div class="space-y-2">
                <label class="font-label-md text-label-md text-on-surface-variant block" for="levelName">Nom du niveau</label>
                <input class="w-full border-outline-variant rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all" id="levelName" name="nom" placeholder="Ex: CP, 6ème, CM2 B" type="text" required>
            </div>
            <div class="space-y-2">
                <label class="font-label-md text-label-md text-on-surface-variant block" for="levelSchool">Établissement</label>
                <select class="w-full border-outline-variant rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all bg-white" id="levelSchool" name="etablissement_id" required>
                    <option value="">Sélectionner un établissement</option>
                    @foreach($schools ?? [] as $school)
                    <option value="{{ $school['id'] }}">{{ $school['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="pt-4 flex gap-3">
                <button class="flex-1 py-3 border border-outline text-on-surface-variant font-label-md rounded-lg hover:bg-surface-container-high transition-colors close-modal-btn" data-modal="add-level-modal" type="button">Annuler</button>
                <button class="flex-1 py-3 bg-primary text-white font-label-md rounded-lg hover:opacity-95 transition-opacity shadow-lg" type="submit">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Level -->
<div class="hidden fixed inset-0 z-[100] items-center justify-center p-4" id="edit-level-modal" style="display: none;">
    <div class="absolute inset-0 modal-backdrop" id="editModalBackdrop"></div>
    <div class="relative bg-surface-container-lowest w-full max-w-md rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="edit-level-modal-content">
        <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-primary text-white">
            <h3 class="font-headline-md text-headline-md">Modifier le Niveau</h3>
            <button class="p-1 hover:bg-white/20 rounded-full transition-colors close-modal-btn" data-modal="edit-level-modal">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form class="p-6 space-y-5" id="editLevelForm">
            @csrf
            @method('PUT')
            <input type="hidden" id="editLevelId">
            <div class="space-y-2">
                <label class="font-label-md text-label-md text-on-surface-variant block" for="editLevelName">Nom du niveau</label>
                <input class="w-full border-outline-variant rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all" id="editLevelName" name="nom" type="text" required>
            </div>
            <div class="space-y-2">
                <label class="font-label-md text-label-md text-on-surface-variant block" for="editLevelSchool">Établissement</label>
                <select class="w-full border-outline-variant rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all bg-white" id="editLevelSchool" name="etablissement_id" required>
                    <option value="">Sélectionner un établissement</option>
                    @foreach($schools ?? [] as $school)
                    <option value="{{ $school['id'] }}">{{ $school['name'] }}</option>
                    @endforeach
                </select>
            </div>  
            <div class="pt-4 flex gap-3">
                <button class="flex-1 py-3 border border-outline text-on-surface-variant font-label-md rounded-lg hover:bg-surface-container-high transition-colors close-modal-btn" data-modal="edit-level-modal" type="button">Annuler</button>
                <button class="flex-1 py-3 bg-primary text-white font-label-md rounded-lg hover:opacity-95 transition-opacity shadow-lg" type="submit">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>

<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    
    .card-shadow {
        box-shadow: 0 4px 12px rgba(55, 48, 163, 0.04);
    }
    
    .modal-backdrop {
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        transition: all 0.3s ease;
    }
    
    #add-level-modal, #edit-level-modal {
        transition: opacity 0.3s ease;
    }
    
    #add-level-modal.flex, #edit-level-modal.flex {
        display: flex !important;
    }
</style>

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // --- Fonctions des modals ---

    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        const content = document.getElementById(modalId + '-content');
        
        if (!modal || !content) return;
        
        document.body.classList.add('modal-open');
        modal.style.display = '';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        
        void modal.offsetWidth;
        
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        const content = document.getElementById(modalId + '-content');
        
        if (!modal || !content) return;
        
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
            document.body.classList.remove('modal-open');
        }, 300);
    }

    // --- Gestionnaires d'événements pour l'ouverture ---
    
    document.getElementById('addLevelBtn')?.addEventListener('click', function(e) {
        e.preventDefault();
        openModal('add-level-modal');
    });

    // --- Gestionnaires pour la fermeture ---
    
    document.querySelectorAll('.close-modal-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const modalId = this.getAttribute('data-modal');
            if (modalId) closeModal(modalId);
        });
    });
    
    document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
        backdrop.addEventListener('click', function(e) {
            if (e.target === this) {
                const modal = this.closest('.fixed');
                if (modal) closeModal(modal.id);
            }
        });
    });

    // --- Gestionnaires pour l'édition (ouvrir la modale) ---
    
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const schoolId = this.getAttribute('data-school-id');
            
            document.getElementById('editLevelId').value = id;
            document.getElementById('editLevelName').value = name;
            
            const select = document.getElementById('editLevelSchool');
            if (select && schoolId) select.value = schoolId;
            
            openModal('edit-level-modal');
        });
    });

    // --- SUPPRESSION avec AJAX ---
    
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const id = this.getAttribute('data-id');
            const levelName = this.getAttribute('data-name') || 'ce niveau';
            
            if (!id) return;
            
            const row = this.closest('tr');
            
            Swal.fire({
                title: 'Supprimer ?',
                text: `Voulez-vous supprimer "${levelName}" ?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Requête AJAX
                    fetch(`/client/niveaux/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Supprimer la ligne du tableau
                            if (row) {
                                row.remove();
                            }
                            
                            // Mettre à jour le compteur
                            const totalElement = document.getElementById('totalLevelsCount');
                            if (totalElement) {
                                const currentCount = parseInt(totalElement.textContent) || 0;
                                totalElement.textContent = currentCount - 1;
                            }
                            
                            // Mettre à jour les numéros de ligne
                            updateRowNumbers();
                            
                            // Vérifier si la table est vide
                            const tbody = document.getElementById('levelsTableBody');
                            if (tbody && tbody.children.length === 0) {
                                tbody.innerHTML = `
                                    <tr id="emptyRow">
                                        <td colspan="5" class="px-6 py-12 text-center text-on-surface-variant">
                                            <div class="flex flex-col items-center gap-4">
                                                <span class="material-symbols-outlined text-[48px] text-outline">layers</span>
                                                <p>Aucun niveau trouvé</p>
                                            </div>
                                        </td>
                                    </tr>
                                `;
                            }
                            
                            // Mettre à jour les informations de pagination
                            updatePaginationInfo();
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Supprimé !',
                                text: data.message || 'Le niveau a été supprimé avec succès.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: data.message || 'Une erreur est survenue lors de la suppression.'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'Une erreur est survenue lors de la suppression.'
                        });
                    });
                }
            });
        });
    });

    // --- AJOUT avec AJAX ---
    
    document.getElementById('addLevelForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const levelName = document.getElementById('levelName')?.value?.trim() || '';
        const levelSchool = document.getElementById('levelSchool')?.value || '';

        if (!levelName || !levelSchool) {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Veuillez remplir tous les champs.',
                confirmButtonColor: '#d33'
            });
            return false;
        }
        
        const formData = new FormData(this);
        
        fetch('{{ route("client.niveaux.store") }}', {
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
                closeModal('add-level-modal');
                this.reset();
                
                const tbody = document.getElementById('levelsTableBody');
                const emptyRow = document.getElementById('emptyRow');
                
                if (emptyRow) {
                    emptyRow.remove();
                }
                
                // Ajouter la nouvelle ligne
                const newRow = document.createElement('tr');
                newRow.className = 'hover:bg-surface-bright transition-colors group';
                newRow.setAttribute('data-level-id', data.level.id);
                newRow.innerHTML = `
                    <td class="px-6 py-4 text-body-sm font-medium text-on-surface-variant">${tbody.children.length + 1}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <span class="font-body-md font-semibold text-primary level-name">${data.level.name}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-secondary-container/20 text-on-secondary-container px-3 py-1 rounded-full text-label-sm font-medium border border-secondary-container/30 level-school">
                            ${data.level.school || 'Non assigné'}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-body-sm text-on-surface-variant">${data.level.date || 'N/A'}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <button class="p-2 text-primary hover:bg-primary-fixed rounded-lg transition-colors edit-btn" data-id="${data.level.id}" data-name="${data.level.name}" data-school-id="${data.level.school_id || ''}" title="Modifier">
                                <span class="material-symbols-outlined">edit</span>
                            </button>
                            <button class="p-2 text-error hover:bg-error-container/20 rounded-lg transition-colors delete-btn" data-id="${data.level.id}" data-name="${data.level.name}" title="Supprimer" type="button">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(newRow);
                
                // Mettre à jour le compteur
                const totalElement = document.getElementById('totalLevelsCount');
                if (totalElement) {
                    const currentCount = parseInt(totalElement.textContent) || 0;
                    totalElement.textContent = currentCount + 1;
                }
                
                // Mettre à jour les informations de pagination
                updatePaginationInfo();
                
                // Réattacher les événements aux nouveaux boutons
                attachEventsToNewRow(newRow);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Ajouté !',
                    text: data.message || 'Le niveau a été ajouté avec succès.',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: data.message || 'Une erreur est survenue lors de l\'enregistrement.'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Une erreur est survenue lors de l\'enregistrement.'
            });
        });
    });

    // --- MODIFICATION avec AJAX ---
    
    document.getElementById('editLevelForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const id = document.getElementById('editLevelId')?.value || '';
        const levelName = document.getElementById('editLevelName')?.value?.trim() || '';
        const levelSchool = document.getElementById('editLevelSchool')?.value || '';

        if (!id || !levelName || !levelSchool) {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Veuillez remplir tous les champs.',
                confirmButtonColor: '#d33'
            });
            return false;
        }
        
        const formData = new FormData(this);
        
        fetch(`/client/niveaux/${id}`, {
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
                closeModal('edit-level-modal');
                
                // Mettre à jour la ligne dans le tableau
                const row = document.querySelector(`tr[data-level-id="${id}"]`);
                if (row) {
                    const nameCell = row.querySelector('.level-name');
                    if (nameCell) nameCell.textContent = data.level.name;
                    
                    const schoolCell = row.querySelector('.level-school');
                    if (schoolCell) schoolCell.textContent = data.level.school || 'Non assigné';
                }
                
                Swal.fire({
                    icon: 'success',
                    title: 'Mis à jour !',
                    text: data.message || 'Le niveau a été modifié avec succès.',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: data.message || 'Une erreur est survenue lors de la mise à jour.'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Une erreur est survenue lors de la mise à jour.'
            });
        });
    });

    // --- Fonctions utilitaires ---
    
    function updateRowNumbers() {
        const rows = document.querySelectorAll('#levelsTableBody tr');
        rows.forEach((row, index) => {
            const firstCell = row.querySelector('td:first-child');
            if (firstCell) {
                firstCell.textContent = index + 1;
            }
        });
    }
    
    function updatePaginationInfo() {
        const tbody = document.getElementById('levelsTableBody');
        const paginationInfo = document.getElementById('paginationInfo');
        if (paginationInfo && tbody) {
            const count = tbody.children.length;
            const text = count > 0 ? `Affichage de 1 à ${count} sur ${count} niveaux` : 'Affichage de 0 sur 0 niveaux';
            paginationInfo.textContent = text;
        }
    }
    
    function attachEventsToNewRow(row) {
        // Événement pour le bouton d'édition
        row.querySelector('.edit-btn')?.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const schoolId = this.getAttribute('data-school-id');
            
            document.getElementById('editLevelId').value = id;
            document.getElementById('editLevelName').value = name;
            
            const select = document.getElementById('editLevelSchool');
            if (select && schoolId) select.value = schoolId;
            
            openModal('edit-level-modal');
        });
        
        // Événement pour le bouton de suppression
        row.querySelector('.delete-btn')?.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const id = this.getAttribute('data-id');
            const levelName = this.getAttribute('data-name') || 'ce niveau';
            
            if (!id) return;
            
            const row = this.closest('tr');
            
            Swal.fire({
                title: 'Supprimer ?',
                text: `Voulez-vous supprimer "${levelName}" ?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/client/niveaux/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (row) row.remove();
                            
                            const totalElement = document.getElementById('totalLevelsCount');
                            if (totalElement) {
                                const currentCount = parseInt(totalElement.textContent) || 0;
                                totalElement.textContent = currentCount - 1;
                            }
                            
                            updateRowNumbers();
                            
                            const tbody = document.getElementById('levelsTableBody');
                            if (tbody && tbody.children.length === 0) {
                                tbody.innerHTML = `
                                    <tr id="emptyRow">
                                        <td colspan="5" class="px-6 py-12 text-center text-on-surface-variant">
                                            <div class="flex flex-col items-center gap-4">
                                                <span class="material-symbols-outlined text-[48px] text-outline">layers</span>
                                                <p>Aucun niveau trouvé</p>
                                            </div>
                                        </td>
                                    </tr>
                                `;
                            }
                            
                            updatePaginationInfo();
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Supprimé !',
                                text: data.message || 'Le niveau a été supprimé avec succès.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: data.message || 'Une erreur est survenue lors de la suppression.'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'Une erreur est survenue lors de la suppression.'
                        });
                    });
                }
            });
        });
    }

    // --- Touche Echap ---
    document.addEventListener('keydown', function(e) {
        if (e.key === "Escape") {
            ['add-level-modal', 'edit-level-modal'].forEach(id => {
                const modal = document.getElementById(id);
                if (modal && modal.classList.contains('flex')) closeModal(id);
            });
        }
    });
</script>
@endpush
@endsection