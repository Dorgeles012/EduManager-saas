@extends('client.layouts.app')
@section('title', 'EduManager - Personnel')
@section('content')

<!-- Page Header -->
<div class="flex justify-between items-end mb-8">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-primary mb-1">Gestion du Personnel</h2>
        <p class="text-text-muted font-body-lg text-body-lg">Gérez les utilisateurs de votre établissement</p>
    </div>
    <button class="flex items-center gap-2 bg-primary text-white px-5 py-2.5 rounded-lg font-label-md text-label-md hover:bg-opacity-90 transition-all active:scale-95 whitespace-nowrap shadow-md" onclick="openModal('add-user-modal')">
        <span class="material-symbols-outlined text-[20px]">person_add</span>
        Ajouter un utilisateur
    </button>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-gutter-desktop mb-8">
    <div class="bg-white p-6 rounded-xl custom-shadow border border-[#E2E8F0] flex items-center gap-4">
        <div class="w-12 h-12 bg-primary-fixed flex items-center justify-center rounded-lg text-primary">
            <span class="material-symbols-outlined text-[32px]">group</span>
        </div>
        <div>
            <p class="text-text-muted font-label-md text-label-md">Total Employés</p>
            <p class="font-headline-lg text-headline-lg text-on-surface">1</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl custom-shadow border border-[#E2E8F0] flex items-center gap-4">
        <div class="w-12 h-12 bg-secondary-container flex items-center justify-center rounded-lg text-secondary">
            <span class="material-symbols-outlined text-[32px]">person_check</span>
        </div>
        <div>
            <p class="text-text-muted font-label-md text-label-md">Actifs</p>
            <p class="font-headline-lg text-headline-lg text-on-surface">1</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl custom-shadow border border-[#E2E8F0] flex items-center gap-4">
        <div class="w-12 h-12 bg-error-container flex items-center justify-center rounded-lg text-error">
            <span class="material-symbols-outlined text-[32px]">block</span>
        </div>
        <div>
            <p class="text-text-muted font-label-md text-label-md">Bloqués</p>
            <p class="font-headline-lg text-headline-lg text-on-surface">0</p>
        </div>
    </div>
</div>

<!-- Content Card with List -->
<div class="bg-white rounded-xl custom-shadow border border-[#E2E8F0] overflow-hidden">
    <div class="p-6 border-b border-[#F1F5F9] flex flex-col md:flex-row md:items-center justify-between gap-4">
        <h3 class="font-headline-md text-headline-md text-on-surface">Liste du personnel</h3>
        <div class="relative w-full md:w-80">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[20px]">search</span>
            <input id="searchEmployee" class="pl-10 pr-4 py-2.5 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-body-sm w-full" placeholder="Rechercher un employé par nom, email ou poste..." type="text">
        </div>
    </div>

    <!-- Modern Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse" id="employeesTable">
            <thead class="bg-surface-container-low">
                <tr>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-text-muted uppercase tracking-wider">Employé</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-text-muted uppercase tracking-wider">Email</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-text-muted uppercase tracking-wider">Téléphone</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-text-muted uppercase tracking-wider">Établissement</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-text-muted uppercase tracking-wider">Poste</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-text-muted uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-text-muted uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#F1F5F9]">
                <tr class="employee-row hover:bg-surface-bright transition-colors" data-name="tra kazan" data-email="baca@mail.com" data-position="comptable">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <span class="font-label-md text-label-md text-on-surface">TRA kazan</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-body-sm text-on-surface-variant">baca@mail.com</td>
                    <td class="px-6 py-4 text-body-sm text-on-surface-variant">09090909</td>
                    <td class="px-6 py-4 text-body-sm text-on-surface-variant">Saint François Xavier</td>
                    <td class="px-6 py-4 text-body-sm text-on-surface-variant">Comptable</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-label-sm font-label-sm bg-[#059669]/10 text-success-green">Actif</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-primary-fixed rounded-lg transition-all" onclick="openModal('view-user-modal')" title="Voir">
                                <span class="material-symbols-outlined text-[20px]">visibility</span>
                            </button>
                            <button class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-primary-fixed rounded-lg transition-all" onclick="openModal('edit-user-modal')" title="Modifier">
                                <span class="material-symbols-outlined text-[20px]">edit</span>
                            </button>
                            <button class="p-1.5 text-on-surface-variant hover:text-warning-amber hover:bg-surface-container rounded-lg transition-all" onclick="confirmBlock()" title="Bloquer">
                                <span class="material-symbols-outlined text-[20px]">block</span>
                            </button>
                            <button class="p-1.5 text-on-surface-variant hover:text-alert-red hover:bg-error-container rounded-lg transition-all" onclick="confirmDelete()" title="Supprimer">
                                <span class="material-symbols-outlined text-[20px]">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-[#F1F5F9] flex items-center justify-between text-label-sm text-text-muted font-label-sm">
        <span id="paginationInfo">Affichage de 1 sur 1 employés</span>
        <div class="flex items-center gap-2">
            <button class="px-3 py-1 border border-outline-variant rounded-lg opacity-50 cursor-not-allowed">Précédent</button>
            <button class="px-3 py-1 bg-primary text-white rounded-lg">1</button>
            <button class="px-3 py-1 border border-outline-variant rounded-lg opacity-50 cursor-not-allowed">Suivant</button>
        </div>
    </div>
</div>

<!-- Modal: Ajouter un utilisateur -->
<div class="hidden fixed inset-0 z-[100] items-center justify-center p-4" id="add-user-modal">
    <div class="absolute inset-0 modal-backdrop backdrop-blur-md bg-black/30" onclick="closeModal('add-user-modal')"></div>
    <div class="bg-white w-full max-w-2xl max-h-[90vh] rounded-xl shadow-2xl overflow-hidden flex flex-col transform transition-all duration-300 scale-95 opacity-0" id="add-user-modal-content">
        <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-primary text-white flex-shrink-0">
            <h3 class="font-headline-md text-headline-md">Ajouter un utilisateur</h3>
            <button class="p-2 hover:bg-white/20 rounded-full transition-colors" onclick="closeModal('add-user-modal')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-6">
            <form id="addUserForm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="font-label-md text-label-md text-on-surface">Nom <span class="text-alert-red">*</span></label>
                        <input class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" placeholder="Entrez le nom" type="text" required>
                    </div>
                    <div class="space-y-2">
                        <label class="font-label-md text-label-md text-on-surface">Prénom <span class="text-alert-red">*</span></label>
                        <input class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" placeholder="Entrez le prénom" type="text" required>
                    </div>
                    <div class="space-y-2">
                        <label class="font-label-md text-label-md text-on-surface">Téléphone <span class="text-alert-red">*</span></label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[20px]">phone</span>
                            <input class="w-full pl-10 pr-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" placeholder="00 00 00 00" type="tel" required>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="font-label-md text-label-md text-on-surface">Email <span class="text-alert-red">*</span></label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[20px]">mail</span>
                            <input class="w-full pl-10 pr-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" placeholder="exemple@mail.com" type="email" required>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="font-label-md text-label-md text-on-surface">Établissement</label>
                        <select class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-white">
                            <option>Sélectionnez un établissement</option>
                            <option>Saint François Xavier</option>
                            <option>Sainte Marie</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="font-label-md text-label-md text-on-surface">Poste</label>
                        <input class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" placeholder="Ex: Comptable" type="text">
                    </div>
                    <div class="space-y-2">
                        <label class="font-label-md text-label-md text-on-surface">Type d'utilisateur</label>
                        <select class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-white">
                            <option>Administrateur</option>
                            <option>Employé</option>
                            <option>Lecteur seul</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="font-label-md text-label-md text-on-surface">Mot de passe <span class="text-alert-red">*</span></label>
                        <input class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" placeholder="••••••••" type="password" required>
                    </div>
                </div>
            </form>
        </div>
        <div class="px-6 py-4 border-t border-outline-variant bg-surface-container-low/30 flex justify-end gap-4 flex-shrink-0">
            <button class="px-6 py-2.5 border border-outline text-on-surface font-label-md text-label-md rounded-lg hover:bg-surface-subtle transition-all" onclick="closeModal('add-user-modal')" type="button">Annuler</button>
            <button class="px-6 py-2.5 bg-primary text-white font-label-md text-label-md rounded-lg hover:opacity-90 transition-all shadow-md" type="submit" form="addUserForm">Créer l'utilisateur</button>
        </div>
    </div>
</div>

<!-- Modal: Détails de TRA kazan -->
<div class="hidden fixed inset-0 z-[100] items-center justify-center p-4" id="view-user-modal">
    <div class="absolute inset-0 modal-backdrop backdrop-blur-md bg-black/30" onclick="closeModal('view-user-modal')"></div>
    <div class="bg-white w-full max-w-lg max-h-[90vh] rounded-xl shadow-2xl overflow-hidden flex flex-col transform transition-all duration-300 scale-95 opacity-0" id="view-user-modal-content">
        <div class="p-8 text-center bg-primary relative flex-shrink-0">
            <button class="absolute top-4 right-4 p-2 bg-white/10 hover:bg-white/20 rounded-full text-white transition-colors" onclick="closeModal('view-user-modal')">
                <span class="material-symbols-outlined">close</span>
            </button>
            <div class="relative inline-block">
                <img alt="Avatar Detail" class="w-32 h-32 rounded-full border-4 border-white shadow-lg mx-auto" src="https://lh3.googleusercontent.com/aida-public/AB6AXuA3p_ZP2_x4M0bKBozaPnQSKyw-BL94k6TYL7E9gZpj16KVTe2LQlApcFAKOoQn243Wta83BtbLndGrASTQ0q-onx7WQgSC3H8Z3PJ4R7b94Z22FebUHzWXsiXxrfeeWklTLsCRLuyk8NEVLAiUqLoIRTKgqh81siu2U_FNQj6Ie092xSJrXNtUWbqqdjoycJi4bHkHTvTILZYxpvzS4S_0z10dxU73_JwGTmeRKGy08gSLPdLDDkc2A1gQ5hZtdtGBw5O80lbKhLy0">
                <span class="absolute bottom-2 right-2 w-6 h-6 bg-success-green border-4 border-white rounded-full"></span>
            </div>
            <h3 class="mt-4 font-headline-lg text-headline-lg text-white">TRA kazan</h3>
            <span class="inline-block px-4 py-1 mt-2 rounded-full text-label-sm font-label-sm bg-white/20 text-white">Actif • Comptable</span>
        </div>
        <div class="flex-1 overflow-y-auto p-8 bg-white">
            <div class="grid grid-cols-1 gap-4">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-primary">mail</span>
                    <div>
                        <p class="text-label-sm font-label-sm text-text-muted uppercase">Email</p>
                        <p class="text-body-md font-body-md text-on-surface">baca@mail.com</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-primary">phone</span>
                    <div>
                        <p class="text-label-sm font-label-sm text-text-muted uppercase">Téléphone</p>
                        <p class="text-body-md font-body-md text-on-surface">09090909</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-primary">domain</span>
                    <div>
                        <p class="text-label-sm font-label-sm text-text-muted uppercase">Établissement</p>
                        <p class="text-body-md font-body-md text-on-surface">Saint François Xavier</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-primary">badge</span>
                    <div>
                        <p class="text-label-sm font-label-sm text-text-muted uppercase">Role</p>
                        <p class="text-body-md font-body-md text-on-surface">Administrateur</p>
                    </div>
                </div>
            </div>
            <div class="pt-6 mt-6 border-t border-outline-variant flex justify-center gap-4">
                <button class="flex items-center gap-2 text-primary font-label-md text-label-md hover:underline" onclick="closeModal('view-user-modal'); openModal('edit-user-modal')">
                    <span class="material-symbols-outlined">edit</span> Modifier le profil
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Modifier TRA kazan -->
<div class="hidden fixed inset-0 z-[100] items-center justify-center p-4" id="edit-user-modal">
    <div class="absolute inset-0 modal-backdrop backdrop-blur-md bg-black/30" onclick="closeModal('edit-user-modal')"></div>
    <div class="bg-white w-full max-w-2xl max-h-[90vh] rounded-xl shadow-2xl overflow-hidden flex flex-col transform transition-all duration-300 scale-95 opacity-0" id="edit-user-modal-content">
        <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-primary text-white flex-shrink-0">
            <h3 class="font-headline-md text-headline-md">Modifier l'utilisateur</h3>
            <button class="p-2 hover:bg-white/20 rounded-full transition-colors" onclick="closeModal('edit-user-modal')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-6">
            <form id="editUserForm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="font-label-md text-label-md text-on-surface">Nom</label>
                        <input class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary" type="text" value="TRA">
                    </div>
                    <div class="space-y-2">
                        <label class="font-label-md text-label-md text-on-surface">Prénom</label>
                        <input class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary" type="text" value="kazan">
                    </div>
                    <div class="space-y-2">
                        <label class="font-label-md text-label-md text-on-surface">Téléphone</label>
                        <input class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary" type="tel" value="09090909">
                    </div>
                    <div class="space-y-2">
                        <label class="font-label-md text-label-md text-on-surface">Email</label>
                        <input class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary" type="email" value="baca@mail.com">
                    </div>
                    <div class="space-y-2">
                        <label class="font-label-md text-label-md text-on-surface">Établissement</label>
                        <select class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary bg-white">
                            <option selected>Saint François Xavier</option>
                            <option>Sainte Marie</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="font-label-md text-label-md text-on-surface">Poste</label>
                        <input class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary" type="text" value="Comptable">
                    </div>
                </div>
            </form>
        </div>
        <div class="px-6 py-4 border-t border-outline-variant bg-surface-container-low/30 flex justify-end gap-4 flex-shrink-0">
            <button class="px-6 py-2.5 border border-outline text-on-surface font-label-md text-label-md rounded-lg hover:bg-surface-subtle transition-all" onclick="closeModal('edit-user-modal')" type="button">Annuler</button>
            <button class="px-6 py-2.5 bg-primary text-white font-label-md text-label-md rounded-lg hover:opacity-90 shadow-md" type="submit" form="editUserForm">Enregistrer les modifications</button>
        </div>
    </div>
</div>

<style>
    /* Animation styles for modals */
    #add-user-modal, #view-user-modal, #edit-user-modal {
        transition: opacity 0.3s ease;
    }
    
    .modal-backdrop {
        transition: backdrop-filter 0.3s ease;
    }
</style>

<script>
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        const contentId = modalId + '-content';
        const content = document.getElementById(contentId);
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        const contentId = modalId + '-content';
        const content = document.getElementById(contentId);
        
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }, 300);
    }

    // Recherche d'employés
    const searchInput = document.getElementById('searchEmployee');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('.employee-row');
            let visibleCount = 0;
            
            rows.forEach(row => {
                const name = row.getAttribute('data-name');
                const email = row.getAttribute('data-email');
                const position = row.getAttribute('data-position');
                
                if (name.includes(searchTerm) || email.includes(searchTerm) || position.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            const paginationSpan = document.getElementById('paginationInfo');
            if (paginationSpan) {
                if (visibleCount === 1) {
                    paginationSpan.textContent = `Affichage de 1 sur ${visibleCount} employé`;
                } else {
                    paginationSpan.textContent = `Affichage de ${visibleCount} sur ${visibleCount} employés`;
                }
            }
        });
    }

    function confirmDelete() {
        Swal.fire({
            title: 'Êtes-vous sûr ?',
            text: "Vous ne pourrez pas annuler cette suppression !",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1f108e',
            cancelButtonColor: '#ba1a1a',
            confirmButtonText: 'Oui, supprimer !',
            cancelButtonText: 'Annuler',
            borderRadius: '12px'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire(
                    'Supprimé !',
                    'L\'utilisateur a été supprimé.',
                    'success'
                )
            }
        })
    }

    function confirmBlock() {
        Swal.fire({
            title: 'Bloquer l\'utilisateur ?',
            text: "L'utilisateur n'aura plus accès à la plateforme.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#D97706',
            cancelButtonColor: '#64748B',
            confirmButtonText: 'Bloquer',
            cancelButtonText: 'Annuler',
            borderRadius: '12px'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire(
                    'Bloqué !',
                    'L\'utilisateur a été suspendu.',
                    'success'
                )
            }
        })
    }

    // Add User Form Submit
    const addForm = document.getElementById('addUserForm');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Succès !',
                text: 'L\'utilisateur a été créé avec succès.',
                icon: 'success',
                confirmButtonColor: '#1f108e',
                borderRadius: '12px'
            }).then(() => {
                closeModal('add-user-modal');
                addForm.reset();
            });
        });
    }

    // Edit User Form Submit
    const editForm = document.getElementById('editUserForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Modifié !',
                text: 'Les modifications ont été enregistrées.',
                icon: 'success',
                confirmButtonColor: '#1f108e',
                borderRadius: '12px'
            }).then(() => {
                closeModal('edit-user-modal');
            });
        });
    }

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === "Escape") {
            const modals = ['add-user-modal', 'view-user-modal', 'edit-user-modal'];
            modals.forEach(id => {
                const modal = document.getElementById(id);
                if (modal && modal.classList.contains('flex')) {
                    closeModal(id);
                }
            });
        }
    });
</script>

@endsection