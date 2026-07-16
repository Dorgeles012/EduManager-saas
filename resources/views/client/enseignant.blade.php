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
                        <div class="flex items-center justify-end space-x-1.5 relative">
                            <button type="button" class="btn-plus" title="Gérer l'emploi du temps" onclick="handleEmploiTempsPlus({{ $teacher['id'] }}, this)">
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
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
        border: none;
        cursor: pointer;
        padding: 0;
    }
    .btn-plus:hover {
        background-color: #1d4ed8 !important;
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
    }
    .btn-plus:active {
        transform: scale(0.95);
    }
    .btn-plus .material-symbols-outlined {
        font-size: 18px !important;
        font-weight: bold;
    }
    .btn-plus:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none !important;
    }
    
    .action-popup {
        position: fixed;
        z-index: 9999;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        padding: 8px 0;
        min-width: 220px;
        display: none;
        border: 1px solid #e5e7eb;
        animation: popupFadeIn 0.15s ease-out;
    }
    
    @keyframes popupFadeIn {
        from { opacity: 0; transform: translateY(8px) scale(0.96); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    
    .action-popup .popup-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 10px 18px;
        background: white;
        transition: background 0.15s;
        width: 100%;
        text-align: left;
        border: none;
        font-size: 14px;
        font-weight: 500;
        color: #1f2937;
        cursor: pointer;
    }
    
    .action-popup .popup-item:hover {
        background: #f3f4f6;
    }
    
    .action-popup .popup-item .material-symbols-outlined {
        font-size: 20px !important;
    }
    
    .action-popup-arrow {
        position: absolute;
        bottom: -8px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 0;
        border-left: 8px solid transparent;
        border-right: 8px solid transparent;
        border-top: 8px solid white;
        filter: drop-shadow(0 2px 2px rgba(0,0,0,0.05));
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    window.previewImage = function(input, previewId) {
        const preview = document.getElementById(previewId);
        if (!preview) return;
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" alt="Photo" class="w-full h-full object-cover">`;
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.innerHTML = `<span class="material-symbols-outlined text-gray-400 text-4xl">person</span>`;
        }
    };

    window.handleEmploiTempsPlus = function(enseignantId, btnEl) {
        if (btnEl.disabled) return;
        btnEl.disabled = true;

        let existingPopup = document.getElementById('action-popup-' + enseignantId);
        if (existingPopup) {
            existingPopup.remove();
            btnEl.disabled = false;
            return;
        }

        document.querySelectorAll('.action-popup').forEach(el => el.remove());

        fetch(`{{ url('/client/emploi-temps/exists') }}/${enseignantId}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            const exists = !!data.exists;
            let itemsHtml = '';
            
            if (exists) {
                itemsHtml = `
                    <p class="px-4 py-3 text-sm text-on-surface-variant border-b border-gray-100">✅ Emploi du temps existant</p>
                    <button disabled class="popup-item opacity-50 cursor-not-allowed bg-gray-50">
                        <span class="material-symbols-outlined text-green-500">add_circle</span>
                        Créer un emploi du temps
                    </button>
                    <button onclick="window.location.href='{{ url('/client/emploi-temps/edit') }}/${enseignantId}'" class="popup-item">
                        <span class="material-symbols-outlined text-pink-500">edit</span>
                        Modifier l'emploi du temps
                    </button>
                    <button onclick="window.location.href='{{ url('/client/emploi-temps/show') }}/${enseignantId}'" class="popup-item">
                        <span class="material-symbols-outlined text-blue-500">visibility</span>
                        Voir l'emploi du temps
                    </button>
                    <button onclick="deleteTeacherSchedule(${enseignantId})" class="popup-item text-alert-red">
                        <span class="material-symbols-outlined text-alert-red">delete</span>
                        Supprimer l'emploi du temps
                    </button>
                `;
            } else {
                itemsHtml = `
                    <button onclick="window.location.href='{{ url('/client/emploi-temps/create') }}/${enseignantId}'" class="popup-item">
                        <span class="material-symbols-outlined text-green-500">add_circle</span>
                        Créer un emploi du temps
                    </button>
                    <button disabled class="popup-item opacity-50 cursor-not-allowed bg-gray-50">
                        <span class="material-symbols-outlined text-pink-500">edit</span>
                        Modifier l'emploi du temps
                    </button>
                    <button disabled class="popup-item opacity-50 cursor-not-allowed bg-gray-50">
                        <span class="material-symbols-outlined text-alert-red">delete</span>
                        Supprimer l'emploi du temps
                    </button>
                `;
            }

            const popup = document.createElement('div');
            popup.id = 'action-popup-' + enseignantId;
            popup.className = 'action-popup';
            popup.innerHTML = `
                <div class="flex flex-col">
                    ${itemsHtml}
                </div>
                <div class="action-popup-arrow"></div>
            `;

            document.body.appendChild(popup);
            popup.style.display = 'block';

            const rect = btnEl.getBoundingClientRect();
            const popupWidth = 240;
            const popupHeight = popup.offsetHeight || 160;
            const left = rect.left + rect.width / 2 - popupWidth / 2;
            const top = Math.max(12, rect.top - popupHeight - 12);

            popup.style.left = Math.max(12, left) + 'px';
            popup.style.top = top + 'px';

            setTimeout(() => {
                document.addEventListener('click', function closePopup(e) {
                    if (!popup.contains(e.target) && e.target !== btnEl && !btnEl.contains(e.target)) {
                        popup.remove();
                        document.removeEventListener('click', closePopup);
                        btnEl.disabled = false;
                    }
                });
            }, 100);
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
            setTimeout(() => {
                if (btnEl.disabled && !document.getElementById('action-popup-' + enseignantId)) {
                    btnEl.disabled = false;
                }
            }, 500);
        });
    };

    window.deleteTeacherSchedule = function(enseignantId) {
        document.querySelectorAll('.action-popup').forEach(el => el.remove());
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

            Swal.fire({
                title: 'Suppression en cours',
                text: 'Veuillez patienter...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });
            
            fetch(`{{ url('/client/emploi-temps/teacher') }}/${enseignantId}`, {
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

    document.addEventListener('click', function (event) {
        const button = event.target.closest('.delete-teacher-btn');
        if (!button) return;

        event.preventDefault();
        event.stopPropagation();
        handleDeleteClick(button);
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
            if (!result.isConfirmed) return;

            button.disabled = true;
            Swal.fire({
                title: 'Suppression en cours',
                text: 'Veuillez patienter...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });

            fetch(`{{ url("/client/enseignant") }}/${teacherId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(async (response) => {
                const data = await response.json().catch(() => ({}));
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Une erreur est survenue lors de la suppression');
                }
                return data;
            })
            .then((data) => {
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
            })
            .catch((error) => {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: error.message || 'Une erreur est survenue lors de la suppression',
                    showConfirmButton: false,
                    timer: 2000,
                    borderRadius: '12px',
                    position: 'center'
                });
            })
            .finally(() => {
                button.disabled = false;
            });
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
});
</script>
@endpush