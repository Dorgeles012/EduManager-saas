@extends('client.layouts.app')
@section('title', 'EduManager - Enseignants')
@section('content')
<div class="flex justify-between items-end mb-10">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-primary">Gestion des Enseignants</h2>
        <p class="text-body-md text-on-surface-variant">Gérez les enseignants de votre établissement avec précision et clarté.</p>
    </div>
    <a href="{{ route('client.enseignants.create') }}" class="inline-flex items-center px-5 py-2.5 bg-primary text-white font-label-md text-label-md rounded-lg hover:bg-primary/90 active:scale-95 transition-all shadow-md">
        <span class="material-symbols-outlined mr-2">add</span>
        Ajouter un enseignant
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <div class="bg-surface-container-lowest rounded-xl p-6 custom-shadow border border-outline-variant/30 flex items-center space-x-5">
        <div class="w-14 h-14 rounded-lg bg-gradient-to-br from-primary to-primary-container flex items-center justify-center shadow-lg">
            <span class="material-symbols-outlined text-white text-3xl">school</span>
        </div>
        <div>
            <p class="text-label-sm text-on-surface-variant uppercase tracking-wider">Enseignants</p>
            <p class="font-headline-md text-headline-md text-on-surface" id="totalTeachers">{{ $totalTeachers ?? 1 }}</p>
        </div>
    </div>
    <div class="bg-surface-container-lowest rounded-xl p-6 custom-shadow border border-outline-variant/30 flex items-center space-x-5">
        <div class="w-14 h-14 rounded-lg bg-orange-500 flex items-center justify-center shadow-lg">
            <span class="material-symbols-outlined text-white text-3xl">book</span>
        </div>
        <div>
            <p class="text-label-sm text-on-surface-variant uppercase tracking-wider">Matières</p>
            <p class="font-headline-md text-headline-md text-on-surface">{{ $totalSubjects ?? 3 }}</p>
        </div>
    </div>
    <div class="bg-surface-container-lowest rounded-xl p-6 custom-shadow border border-outline-variant/30 flex items-center space-x-5">
        <div class="w-14 h-14 rounded-lg bg-green-500 flex items-center justify-center shadow-lg">
            <span class="material-symbols-outlined text-white text-3xl">event_note</span>
        </div>
        <div>
            <p class="text-label-sm text-on-surface-variant uppercase tracking-wider">Emplois du temps</p>
            <p class="font-headline-md text-headline-md text-on-surface">{{ $totalSchedules ?? 0 }}</p>
        </div>
    </div>
</div>

<div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant/30 overflow-hidden">
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
                            <button type="button" class="btn-plus" title="Gérer l'emploi du temps" data-teacher-id="{{ $teacher['id'] }}" onclick="handleEmploiTempsPlus({{ $teacher['id'] }}, this, event)">
                                <span class="material-symbols-outlined">add</span>
                            </button>
                            
                            <a href="{{ route('client.enseignants.edit', $teacher['id']) }}" class="w-8 h-8 flex items-center justify-center text-warning-amber hover:bg-warning-amber/10 rounded-full transition-all" title="Modifier">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </a>
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
@endsection

@push('styles')
<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    .custom-shadow {
        box-shadow: 0 4px 12px rgba(55, 48, 163, 0.04);
    }
    .btn-plus {
        background-color: #2563EB !important;
        color: white !important;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        cursor: pointer;
        padding: 0;
        position: relative;
    }
    .btn-plus .material-symbols-outlined {
        font-size: 18px !important;
        font-weight: bold;
    }
    .btn-plus:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    /* Style WhatsApp-like popup */
    .whatsapp-popup {
        position: absolute;
        z-index: 9999;
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2), 0 6px 20px rgba(0,0,0,0.15);
        padding: 8px 0;
        min-width: 220px;
        max-width: 280px;
        opacity: 0;
        transform: translateY(5px);
        transition: opacity 0.18s ease-out, transform 0.18s ease-out;
        pointer-events: none;
    }
    
    .whatsapp-popup.visible {
        opacity: 1;
        transform: translateY(0);
        pointer-events: all;
    }
    
    .whatsapp-popup .popup-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 10px 20px;
        background: white;
        transition: background-color 0.1s;
        width: 100%;
        text-align: left;
        border: none;
        font-size: 14px;
        font-weight: 400;
        color: #111b21;
        cursor: pointer;
        white-space: nowrap;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    }
    
    .whatsapp-popup .popup-item:hover {
        background-color: #f0f2f5;
    }
    
    .whatsapp-popup .popup-item:active {
        background-color: #e9edef;
    }
    
    .whatsapp-popup .popup-item .material-symbols-outlined {
        font-size: 20px !important;
        flex-shrink: 0;
        color: #54656f;
    }
    
    .whatsapp-popup .popup-item.text-danger {
        color: #dc2626;
    }
    
    .whatsapp-popup .popup-item.text-danger .material-symbols-outlined {
        color: #dc2626;
    }
    
    .whatsapp-popup .popup-item.disabled {
        opacity: 0.4;
        cursor: not-allowed;
        pointer-events: none;
    }
    
    .whatsapp-popup .popup-divider {
        height: 1px;
        background-color: #e9edef;
        margin: 4px 0;
    }
    
    .whatsapp-popup .popup-header {
        padding: 6px 20px 8px;
        font-size: 12.5px;
        color: #667781;
        font-weight: 400;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function() {
    'use strict';
    
    // État global du système de popup
    let activePopup = null;
    let activeButton = null;
    let scrollRefreshId = null;
    
    /**
     * Calcule la position optimale de la popup par rapport au bouton
     * La popup est centrée horizontalement sur le bouton
     */
    function calculatePopupPosition(btn, popup) {
        const btnRect = btn.getBoundingClientRect();
        const popupRect = popup.getBoundingClientRect();
        
        const windowWidth = window.innerWidth;
        const windowHeight = window.innerHeight;
        const scrollX = window.scrollX || window.pageXOffset;
        const scrollY = window.scrollY || window.pageYOffset;
        
        // Valeur négative pour que la popup remonte sur le bouton
        const GAP = -6;
        const MARGIN = 6;
        
        // Position par défaut : sous le bouton, centrée horizontalement
        let top = btnRect.bottom + scrollY + GAP;
        // Centrer la popup sur le bouton
        let left = btnRect.left + scrollX + (btnRect.width / 2) - (popupRect.width / 2);
        
        // Vérifier si la popup dépasse à droite
        if (left + popupRect.width > scrollX + windowWidth - MARGIN) {
            left = scrollX + windowWidth - popupRect.width - MARGIN;
        }
        
        // Vérifier si la popup dépasse à gauche
        if (left < scrollX + MARGIN) {
            left = scrollX + MARGIN;
        }
        
        // Vérifier si la popup dépasse en bas
        if (top + popupRect.height > scrollY + windowHeight - MARGIN) {
            // Afficher au-dessus du bouton avec le même gap
            top = btnRect.top + scrollY - popupRect.height - Math.abs(GAP);
        }
        
        // Si toujours pas assez d'espace en haut, positionner au mieux
        if (top < scrollY + MARGIN) {
            top = scrollY + MARGIN;
        }
        
        return { top, left };
    }
    
    /**
     * Met à jour la position de la popup active
     */
    function updatePopupPosition() {
        if (!activePopup || !activeButton) return;
        
        const pos = calculatePopupPosition(activeButton, activePopup);
        activePopup.style.top = pos.top + 'px';
        activePopup.style.left = pos.left + 'px';
    }
    
    /**
     * Ferme la popup active
     */
    function closePopup() {
        if (activePopup) {
            activePopup.classList.remove('visible');
            
            // Supprimer après la transition
            setTimeout(() => {
                if (activePopup && !activePopup.classList.contains('visible')) {
                    activePopup.remove();
                }
            }, 200);
            
            activePopup = null;
        }
        
        if (scrollRefreshId) {
            cancelAnimationFrame(scrollRefreshId);
            scrollRefreshId = null;
        }
        
        activeButton = null;
    }
    
    /**
     * Gère le clic sur le bouton + pour afficher la popup
     */
    window.handleEmploiTempsPlus = function(enseignantId, btnEl, event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        // Si on clique sur le même bouton, toggle
        if (activeButton === btnEl && activePopup) {
            closePopup();
            return;
        }
        
        // Fermer toute popup existante
        closePopup();
        
        // Désactiver le bouton pendant le chargement
        btnEl.disabled = true;
        
        // Récupérer l'URL de base pour les routes
        const baseUrl = '{{ url('/') }}';
        
        fetch(baseUrl + '/client/emploi-temps/exists/' + enseignantId, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            const exists = !!data.exists;
            
            // Construire le contenu de la popup
            const popup = document.createElement('div');
            popup.className = 'whatsapp-popup';
            
            if (exists) {
                popup.innerHTML = `
                    <div class="popup-header">Emploi du temps</div>
                    <button class="popup-item disabled">
                        <span class="material-symbols-outlined">add_circle</span>
                        <span>Créer</span>
                    </button>
                    <button class="popup-item" data-action="navigate" data-url="${baseUrl}/client/emploi-temps/edit/${enseignantId}">
                        <span class="material-symbols-outlined">edit</span>
                        <span>Modifier</span>
                    </button>
                    <button class="popup-item" data-action="navigate" data-url="${baseUrl}/client/emploi-temps/show/${enseignantId}">
                        <span class="material-symbols-outlined">visibility</span>
                        <span>Consulter</span>
                    </button>
                    <div class="popup-divider"></div>
                    <button class="popup-item text-danger" data-action="delete-schedule" data-teacher-id="${enseignantId}">
                        <span class="material-symbols-outlined">delete</span>
                        <span>Supprimer</span>
                    </button>
                `;
            } else {
                popup.innerHTML = `
                    <div class="popup-header">Emploi du temps</div>
                    <button class="popup-item" data-action="navigate" data-url="${baseUrl}/client/emploi-temps/create/${enseignantId}">
                        <span class="material-symbols-outlined">add_circle</span>
                        <span>Créer</span>
                    </button>
                    <button class="popup-item disabled">
                        <span class="material-symbols-outlined">edit</span>
                        <span>Modifier</span>
                    </button>
                    <button class="popup-item disabled">
                        <span class="material-symbols-outlined">delete</span>
                        <span>Supprimer</span>
                    </button>
                `;
            }
            
            // Ajouter les écouteurs d'événements
            popup.querySelectorAll('.popup-item:not(.disabled)').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const action = this.dataset.action;
                    
                    if (action === 'navigate') {
                        const url = this.dataset.url;
                        closePopup();
                        window.location.href = url;
                    } else if (action === 'delete-schedule') {
                        const tid = this.dataset.teacherId;
                        closePopup();
                        window.deleteTeacherSchedule(tid);
                    }
                });
            });
            
            // Empêcher la propagation des clics dans la popup
            popup.addEventListener('click', function(e) {
                e.stopPropagation();
            });
            
            // Ajouter au body
            document.body.appendChild(popup);
            
            // Calculer et appliquer la position
            const pos = calculatePopupPosition(btnEl, popup);
            popup.style.top = pos.top + 'px';
            popup.style.left = pos.left + 'px';
            
            // Déclencher l'animation d'apparition
            requestAnimationFrame(() => {
                popup.classList.add('visible');
            });
            
            // Stocker les références
            activePopup = popup;
            activeButton = btnEl;
            
            // Mettre en place le rafraîchissement de position au scroll
            function refreshOnScroll() {
                updatePopupPosition();
                scrollRefreshId = requestAnimationFrame(refreshOnScroll);
            }
            scrollRefreshId = requestAnimationFrame(refreshOnScroll);
        })
        .catch(() => {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Impossible de vérifier l\'emploi du temps.',
                confirmButtonText: 'OK',
                timer: 3000
            });
        })
        .finally(() => {
            btnEl.disabled = false;
        });
    };
    
    /**
     * Fonction de suppression d'emploi du temps (appelée depuis la popup)
     */
    window.deleteTeacherSchedule = function(enseignantId) {
        Swal.fire({
            title: 'Supprimer cet emploi du temps ?',
            text: 'Cette action est irréversible.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler',
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            borderRadius: '12px'
        }).then(result => {
            if (!result.isConfirmed) return;
            
            const baseUrl = '{{ url('/') }}';
            
            fetch(baseUrl + '/client/emploi-temps/teacher/' + enseignantId, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                if (!response.ok) {
                    const error = await response.json().catch(() => ({}));
                    throw new Error(error.message || 'Impossible de supprimer cet emploi du temps.');
                }
                return response.json();
            })
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Succès',
                    text: data.message || 'Emploi du temps supprimé avec succès.',
                    timer: 2200,
                    showConfirmButton: false,
                    borderRadius: '12px',
                    position: 'center'
                });
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: error.message || 'Impossible de supprimer cet emploi du temps.',
                    confirmButtonText: 'OK',
                    borderRadius: '12px'
                });
            });
        });
    };
    
    // Fermer la popup au clic ailleurs
    document.addEventListener('click', function(e) {
        if (activePopup && activeButton) {
            // Vérifier si le clic est en dehors de la popup et du bouton
            if (!activePopup.contains(e.target) && !activeButton.contains(e.target)) {
                closePopup();
            }
        }
    }, true);
    
    // Fermer la popup avec Échap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && activePopup) {
            closePopup();
        }
    });
    
    // Gestion du redimensionnement
    window.addEventListener('resize', function() {
        updatePopupPosition();
    });
    
    // GESTION DES SUPPRESSIONS D'ENSEIGNANTS
    
    document.querySelectorAll('.delete-teacher-btn').forEach((button) => {
        button.addEventListener('click', function() {
            handleDeleteClick(this);
        });
    });

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

                const baseUrl = '{{ url('/') }}';
                
                fetch(baseUrl + '/client/enseignant/' + teacherId, {
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

    function updateTotalTeachers(change) {
        const totalSpan = document.getElementById('totalTeachers');
        if (totalSpan) {
            let current = parseInt(totalSpan.textContent) || 0;
            totalSpan.textContent = current + change;
        }
    }

    function renumberRows() {
        const rows = document.querySelectorAll('.teacher-row');
        rows.forEach((row, index) => {
            const cells = row.querySelectorAll('td');
            if (cells.length > 0) {
                cells[0].textContent = index + 1;
            }
        });
    }

    function checkEmptyTable() {
        const tbody = document.getElementById('teachersTableBody');
        const rows = tbody.querySelectorAll('.teacher-row');
        if (rows.length === 0) {
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

    // RECHERCHE
    
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
    
    // Exposer closePopup globalement si nécessaire
    window.closeTeacherPopup = closePopup;
})();
</script>
@endpush