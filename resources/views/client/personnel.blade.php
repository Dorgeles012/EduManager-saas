@extends('client.layouts.app')
@section('title', 'EduManager - Personnel')
@section('content')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let personnelsData = @json($personnels);
</script>

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

@if (session('success'))
    <script>
        Swal.fire({
            title: 'Succès !',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonColor: '#1f108e',
            borderRadius: '12px',
            timer: 3000
        });
    </script>
@endif

@if ($errors->any())
    <script>
        Swal.fire({
            title: 'Erreur !',
            html: `{!! implode('<br>', $errors->all()) !!}`,
            icon: 'error',
            confirmButtonColor: '#1f108e',
            borderRadius: '12px'
        });
    </script>
@endif

@php
    $totalEmployes = $personnels->count();
    $actifs = $personnels->where('statut', 'actif')->count();
    $bloques = $personnels->where('statut', 'bloqué')->count();
@endphp

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-gutter-desktop mb-8">
    <div class="bg-white p-6 rounded-xl custom-shadow border border-[#E2E8F0] flex items-center gap-4">
        <div class="w-12 h-12 bg-primary-fixed flex items-center justify-center rounded-lg text-primary">
            <span class="material-symbols-outlined text-[32px]">group</span>
        </div>
        <div>
            <p class="text-text-muted font-label-md text-label-md">Total Employés</p>
            <p class="font-headline-lg text-headline-lg text-on-surface">{{ $totalEmployes }}</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl custom-shadow border border-[#E2E8F0] flex items-center gap-4">
        <div class="w-12 h-12 bg-secondary-container flex items-center justify-center rounded-lg text-secondary">
            <span class="material-symbols-outlined text-[32px]">person_check</span>
        </div>
        <div>
            <p class="text-text-muted font-label-md text-label-md">Actifs</p>
            <p class="font-headline-lg text-headline-lg text-on-surface">{{ $actifs }}</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl custom-shadow border border-[#E2E8F0] flex items-center gap-4">
        <div class="w-12 h-12 bg-error-container flex items-center justify-center rounded-lg text-error">
            <span class="material-symbols-outlined text-[32px]">block</span>
        </div>
        <div>
            <p class="text-text-muted font-label-md text-label-md">Bloqués</p>
            <p class="font-headline-lg text-headline-lg text-on-surface">{{ $bloques }}</p>
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
                    <th class="px-6 py-4 font-label-sm text-label-sm text-text-muted uppercase tracking-wider">Rôle</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-text-muted uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-text-muted uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#F1F5F9]">
                @forelse ($personnels as $p)
                    <tr class="employee-row hover:bg-surface-bright transition-colors"
                        data-name="{{ strtolower(($p->nom ?? '').' '.($p->prenom ?? '')) }}"
                        data-email="{{ strtolower($p->email ?? '') }}"
                        data-position="{{ strtolower($p->role ?? '') }}">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <span class="font-label-md text-label-md text-on-surface">
                                    {{ $p->nom }} {{ $p->prenom }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $p->email }}</td>
                        <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $p->telephone }}</td>
                        <td class="px-6 py-4 text-body-sm text-on-surface-variant">
                            {{ optional($p->etablissement)->nom ?? '—' }}
                        </td>
                        <td class="px-6 py-4 text-body-sm text-on-surface-variant">
                            <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-600">Personnel</span>
                        </td>
                        <td class="px-6 py-4">
                            @if (($p->statut ?? '') === 'actif')
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-success-green/10 text-success-green">
                                    <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                    Actif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-error-container/20 text-error">
                                    <span class="material-symbols-outlined text-[14px]">block</span>
                                    Bloqué
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button type="button"
                                        class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-primary-fixed rounded-lg transition-all"
                                        title="Modifier"
                                        onclick="openEditModal({{ $p->id }})">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </button>

                                @if (($p->statut ?? '') === 'actif')
                                    <button type="button"
                                            onclick="confirmBlock({{ $p->id }}, '{{ addslashes($p->nom . ' ' . $p->prenom) }}')"
                                            class="p-1.5 text-on-surface-variant hover:text-alert-red hover:bg-error-container rounded-lg transition-all"
                                            title="Bloquer">
                                        <span class="material-symbols-outlined text-[20px]">block</span>
                                    </button>
                                @else
                                    <button type="button"
                                            onclick="confirmUnblock({{ $p->id }}, '{{ addslashes($p->nom . ' ' . $p->prenom) }}')"
                                            class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-primary-fixed rounded-lg transition-all"
                                            title="Débloquer">
                                        <span class="material-symbols-outlined text-[20px]">lock_open</span>
                                    </button>
                                @endif

                                <button type="button"
                                        onclick="confirmDelete({{ $p->id }}, '{{ addslashes($p->nom . ' ' . $p->prenom) }}')"
                                        class="p-1.5 text-on-surface-variant hover:text-alert-red hover:bg-error-container rounded-lg transition-all"
                                        title="Supprimer">
                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-6 py-10 text-center" colspan="7">Aucun personnel.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-[#F1F5F9] flex items-center justify-between text-label-sm text-text-muted font-label-sm">
        <span id="paginationInfo">Affichage de 1 sur {{ $totalEmployes }} employés</span>
        <div class="flex items-center gap-2">
            <button class="px-3 py-1 border border-outline-variant rounded-lg opacity-50 cursor-not-allowed">Précédent</button>
            <button class="px-3 py-1 bg-primary text-white rounded-lg">1</button>
            <button class="px-3 py-1 border border-outline-variant rounded-lg opacity-50 cursor-not-allowed">Suivant</button>
        </div>
    </div>
</div>

<!-- Modal: Ajouter un utilisateur -->
<div class="hidden fixed inset-0 z-[100] items-center justify-center p-4" id="add-user-modal">
    <div class="absolute inset-0 modal-backdrop bg-black/50" onclick="closeModal('add-user-modal')"></div>
    <div class="bg-white w-full max-w-2xl max-h-[90vh] rounded-xl shadow-2xl overflow-hidden flex flex-col transform transition-all duration-300 scale-95 opacity-0" id="add-user-modal-content">
        <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-primary text-white flex-shrink-0">
            <h3 class="font-headline-md text-headline-md">Ajouter un utilisateur</h3>
            <button class="p-2 hover:bg-white/20 rounded-full transition-colors" onclick="closeModal('add-user-modal')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-6">
            <form id="addUserForm" method="POST" action="{{ route('client.personnel.store') }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="font-label-md text-label-md text-on-surface">Nom <span class="text-alert-red">*</span></label>
                        <input class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" placeholder="Entrez le nom" type="text" name="nom" value="{{ old('nom') }}" required>
                    </div>
                    <div class="space-y-2">
                        <label class="font-label-md text-label-md text-on-surface">Prénom <span class="text-alert-red">*</span></label>
                        <input class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" placeholder="Entrez le prénom" type="text" name="prenom" value="{{ old('prenom') }}" required>
                    </div>
                    <div class="space-y-2">
                        <label class="font-label-md text-label-md text-on-surface">Téléphone <span class="text-alert-red">*</span></label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[20px]">phone</span>
                            <input class="w-full pl-10 pr-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" placeholder="00 00 00 00" type="tel" name="telephone" value="{{ old('telephone') }}" required>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="font-label-md text-label-md text-on-surface">Email <span class="text-alert-red">*</span></label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[20px]">mail</span>
                            <input class="w-full pl-10 pr-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" placeholder="exemple@mail.com" type="email" name="email" value="{{ old('email') }}" required>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="font-label-md text-label-md text-on-surface">Établissement</label>
                        <input type="text" class="w-full px-4 py-2 border border-outline-variant rounded-lg bg-gray-100" value="{{ auth()->user()->etablissement->nom ?? '—' }}" disabled>
                        <input type="hidden" name="etablissement_id" value="{{ auth()->user()->etablissement_id }}">
                        <p class="text-label-sm text-text-muted mt-1">(Défini automatiquement pour votre client)</p>
                    </div>

                    <div class="space-y-2">
                        <label class="font-label-md text-label-md text-on-surface">Mot de passe <span class="text-alert-red">*</span></label>
                        <input class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" placeholder="••••••••" type="password" name="password" required>
                    </div>
                    <div class="space-y-2">
                        <label class="font-label-md text-label-md text-on-surface">Confirmer le mot de passe <span class="text-alert-red">*</span></label>
                        <input class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" placeholder="••••••••" type="password" name="password_confirmation" required>
                    </div>
                </div>
                <input type="hidden" name="role" value="personnel">
                <div class="mt-6 flex justify-end gap-4">
                    <button class="px-6 py-2.5 border border-outline text-on-surface font-label-md text-label-md rounded-lg hover:bg-surface-subtle transition-all" type="button" onclick="closeModal('add-user-modal')">Annuler</button>
                    <button class="px-6 py-2.5 bg-primary text-white font-label-md text-label-md rounded-lg hover:opacity-90 transition-all shadow-md" type="submit">Créer l'utilisateur</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Modifier l'utilisateur -->
<div class="hidden fixed inset-0 z-[100] items-center justify-center p-4" id="edit-user-modal">
    <div class="absolute inset-0 modal-backdrop bg-black/50" onclick="closeModal('edit-user-modal')"></div>
    <div class="bg-white w-full max-w-2xl max-h-[90vh] rounded-xl shadow-2xl overflow-hidden flex flex-col transform transition-all duration-300 scale-95 opacity-0" id="edit-user-modal-content">
        <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-primary text-white flex-shrink-0">
            <h3 class="font-headline-md text-headline-md">Modifier l'utilisateur</h3>
            <button class="p-2 hover:bg-white/20 rounded-full transition-colors" onclick="closeModal('edit-user-modal')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-6">
            <form id="editForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="font-label-md text-label-md text-on-surface">Nom</label>
                        <input id="edit_nom" name="nom" class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary" type="text" value="">
                    </div>
                    <div class="space-y-2">
                        <label class="font-label-md text-label-md text-on-surface">Prénom</label>
                        <input id="edit_prenom" name="prenom" class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary" type="text" value="">
                    </div>
                    <div class="space-y-2">
                        <label class="font-label-md text-label-md text-on-surface">Téléphone</label>
                        <input id="edit_telephone" name="telephone" class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary" type="tel" value="">
                    </div>
                    <div class="space-y-2">
                        <label class="font-label-md text-label-md text-on-surface">Email</label>
                        <input id="edit_email" name="email" class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary" type="email" value="">
                    </div>
                    <div class="space-y-2">
                        <label class="font-label-md text-label-md text-on-surface">Établissement</label>
                        <input type="text" class="w-full px-4 py-2 border border-outline-variant rounded-lg bg-gray-100" id="edit_etablissement_text" value="" disabled>
                        <input type="hidden" id="edit_etablissement_id" name="etablissement_id">
                        <p class="text-label-sm text-text-muted mt-1">(Non modifiable)</p>
                    </div>
                    <div class="space-y-2">
                        <label class="font-label-md text-label-md text-on-surface">Rôle</label>
                        <input type="text" class="w-full px-4 py-2 border border-outline-variant rounded-lg bg-gray-100" id="edit_role" value="Personnel" disabled>
                        <input type="hidden" name="role" value="personnel">
                        <p class="text-label-sm text-text-muted mt-1">(Rôle non modifiable)</p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-4">
                    <button class="px-6 py-2.5 border border-outline text-on-surface font-label-md text-label-md rounded-lg hover:bg-surface-subtle transition-all" type="button" onclick="closeModal('edit-user-modal')">Annuler</button>
                    <button class="px-6 py-2.5 bg-primary text-white font-label-md text-label-md rounded-lg hover:opacity-90 shadow-md" type="submit">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    #add-user-modal, #edit-user-modal {
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
        
        if (content) {
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
        }
        
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

    function openEditModal(id) {
        const personnel = personnelsData.find(p => p.id === id);
        if (!personnel) return;

        // Pre-fill fields
        document.getElementById('edit_nom').value = personnel.nom ?? '';
        document.getElementById('edit_prenom').value = personnel.prenom ?? '';
        document.getElementById('edit_telephone').value = personnel.telephone ?? '';
        document.getElementById('edit_email').value = personnel.email ?? '';

        // Etablissement
        const etabNom = personnel.etablissement?.nom ?? (personnel.etablissement_id ? 'Établissement #' + personnel.etablissement_id : '—');
        document.getElementById('edit_etablissement_text').value = etabNom;
        document.getElementById('edit_etablissement_id').value = personnel.etablissement_id ?? '';

        document.getElementById('editForm').action = "{{ url('client/personnel') }}/" + id;

        openModal('edit-user-modal');
    }

    // SweetAlert confirmations
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Supprimer le personnel',
            text: `Êtes-vous sûr de vouloir supprimer ${name} ?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler',
            borderRadius: '12px'
        }).then((result) => {
            if (result.isConfirmed) {
                // Create and submit form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('client/personnel') }}/${id}`;
                form.innerHTML = `
                    @csrf
                    @method('DELETE')
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function confirmBlock(id, name) {
        Swal.fire({
            title: 'Bloquer le personnel',
            text: `Êtes-vous sûr de vouloir bloquer ${name} ? Il n'aura plus accès à la plateforme.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d97706',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Oui, bloquer',
            cancelButtonText: 'Annuler',
            borderRadius: '12px'
        }).then((result) => {
            if (result.isConfirmed) {
                // Create and submit form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('client/personnel') }}/${id}/block`;
                form.innerHTML = `
                    @csrf
                    @method('PATCH')
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function confirmUnblock(id, name) {
        Swal.fire({
            title: 'Débloquer le personnel',
            text: `Êtes-vous sûr de vouloir débloquer ${name} ? Il pourra à nouveau accéder à la plateforme.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1f108e',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Oui, débloquer',
            cancelButtonText: 'Annuler',
            borderRadius: '12px'
        }).then((result) => {
            if (result.isConfirmed) {
                // Create and submit form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('client/personnel') }}/${id}/unblock`;
                form.innerHTML = `
                    @csrf
                    @method('PATCH')
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // SweetAlert for Add User Form submission
    const addForm = document.getElementById('addUserForm');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Création en cours...',
                text: 'Veuillez patienter',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            this.submit();
        });
    }

    // SweetAlert for Edit Form submission
    const editForm = document.getElementById('editForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Modification en cours...',
                text: 'Veuillez patienter',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            this.submit();
        });
    }

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === "Escape") {
            const modals = ['add-user-modal', 'edit-user-modal'];
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