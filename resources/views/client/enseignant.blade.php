@extends('client.layouts.app')
@section('title', 'EduManager - Enseignants')
@section('content')

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Gestion des Enseignants</h2>
        <p class="text-sm text-gray-500 mt-1">Gérez les enseignants de votre établissement avec précision et clarté.</p>
    </div>
    <a href="{{ route('client.enseignants.create') }}" class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-xs font-semibold transition shadow-sm">
        <span class="material-symbols-outlined text-sm">add</span>
        Ajouter un enseignant
    </a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mb-6">
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                <span class="material-symbols-outlined text-lg">school</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Total</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold text-gray-900" id="totalTeachers">{{ $totalTeachers ?? 1 }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Enseignants</p>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600">
                <span class="material-symbols-outlined text-lg">book</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Matières</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $totalSubjects ?? 3 }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Enseignées</p>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow col-span-2 lg:col-span-1">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                <span class="material-symbols-outlined text-lg">event_note</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Emplois</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $totalSchedules }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Emplois du temps</p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                <span class="material-symbols-outlined text-base">people</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Liste des enseignants</h3>
                <p class="text-[11px] text-gray-500">{{ $totalTeachers ?? count($teachers ?? []) }} enseignant(s)</p>
            </div>
        </div>
        <div class="relative w-full sm:w-72">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base pointer-events-none">search</span>
            <input type="text" id="searchTeacher" placeholder="Rechercher..."
                   class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 pl-10 pr-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
        </div>
    </div>

    <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left" id="teachersTable">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">N°</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Nom & Prénoms</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Téléphone</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Matière</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100" id="teachersTableBody">
                @forelse($teachers ?? [] as $teacher)
                <tr class="hover:bg-gray-50/50 transition-colors teacher-row" id="teacher-row-{{ $teacher['id'] }}"
                    data-name="{{ strtolower($teacher['firstname'] . ' ' . $teacher['lastname']) }}"
                    data-email="{{ strtolower($teacher['email']) }}"
                    data-subject="{{ strtolower($teacher['subject']) }}"
                    data-position="{{ strtolower($teacher['subject']) }}">
                    <td class="px-4 py-3 text-xs text-gray-500 font-semibold">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 text-[11px] font-bold flex-shrink-0">
                                {{ strtoupper(substr($teacher['firstname'], 0, 1)) }}{{ strtoupper(substr($teacher['lastname'], 0, 1)) }}
                            </div>
                            <span class="text-xs font-semibold text-gray-900">{{ ucfirst($teacher['firstname']) }} {{ ucfirst($teacher['lastname']) }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-600">{{ $teacher['email'] }}</td>
                    <td class="px-4 py-3 text-xs text-gray-600">{{ $teacher['phone'] }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 text-[10px] font-bold uppercase tracking-wider">{{ $teacher['subject'] }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            {{ ucfirst($teacher['status']) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-1.5">
                            <button type="button" class="btn-plus" title="Gérer l'emploi du temps"
                                    data-teacher-id="{{ $teacher['id'] }}"
                                    onclick="handleEmploiTempsPlus({{ $teacher['id'] }}, this, event)">
                                <span class="material-symbols-outlined">add</span>
                            </button>

                            <a href="{{ route('client.enseignants.edit', $teacher['id']) }}"
                               class="w-8 h-8 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600 flex items-center justify-center transition"
                               title="Modifier">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </a>

                            <button type="button"
                                    class="delete-teacher-btn w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition"
                                    data-id="{{ $teacher['id'] }}"
                                    data-name="{{ $teacher['firstname'] }} {{ $teacher['lastname'] }}"
                                    title="Supprimer">
                                <span class="material-symbols-outlined text-base">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="emptyRow">
                    <td colspan="7" class="py-16 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center mb-3">
                                <span class="material-symbols-outlined text-2xl text-gray-300">school</span>
                            </div>
                            <p class="text-sm font-semibold text-gray-700">Aucun enseignant trouvé</p>
                            <p class="text-xs text-gray-400 mt-1">Commencez par ajouter un enseignant.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="md:hidden divide-y divide-gray-100" id="teachersMobileBody">
        @forelse($teachers ?? [] as $teacher)
        <div class="teacher-row p-4 space-y-3" id="teacher-mobile-{{ $teacher['id'] }}"
             data-name="{{ strtolower($teacher['firstname'] . ' ' . $teacher['lastname']) }}"
             data-email="{{ strtolower($teacher['email']) }}"
             data-subject="{{ strtolower($teacher['subject']) }}">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-sm font-bold flex-shrink-0">
                        {{ strtoupper(substr($teacher['firstname'], 0, 1)) }}{{ strtoupper(substr($teacher['lastname'], 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ ucfirst($teacher['firstname']) }} {{ ucfirst($teacher['lastname']) }}</p>
                        <p class="text-[11px] text-gray-500 truncate">{{ $teacher['email'] }}</p>
                    </div>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 text-[10px] font-bold uppercase tracking-wider flex-shrink-0">
                    {{ $teacher['subject'] }}
                </span>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <button type="button" class="btn-plus" title="Emploi du temps"
                        data-teacher-id="{{ $teacher['id'] }}"
                        onclick="handleEmploiTempsPlus({{ $teacher['id'] }}, this, event)">
                    <span class="material-symbols-outlined">add</span>
                </button>
                <a href="{{ route('client.enseignants.edit', $teacher['id']) }}"
                   class="flex-1 h-9 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold flex items-center justify-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-sm">edit</span>
                    Modifier
                </a>
                <button type="button"
                        class="delete-teacher-btn w-9 h-9 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition"
                        data-id="{{ $teacher['id'] }}"
                        data-name="{{ $teacher['firstname'] }} {{ $teacher['lastname'] }}">
                    <span class="material-symbols-outlined text-base">delete</span>
                </button>
            </div>
        </div>
        @empty
        <div class="py-16 text-center">
            <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-2xl text-gray-300">school</span>
            </div>
            <p class="text-sm font-semibold text-gray-700">Aucun enseignant trouvé</p>
            <p class="text-xs text-gray-400 mt-1">Commencez par ajouter un enseignant.</p>
        </div>
        @endforelse
    </div>

    <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between">
        <span class="text-[11px] text-gray-500" id="paginationInfo">
            @php $isPaginator = isset($teachers) && method_exists($teachers, 'links'); @endphp
            @if($isPaginator)
                Affichage de {{ $teachers->firstItem() }} à {{ $teachers->lastItem() }} sur {{ $teachers->total() }} enseignants
            @else
                Affichage de {{ count($teachers ?? []) }} enseignant(s)
            @endif
        </span>
        <div class="flex items-center gap-1.5 text-xs">
            @if($isPaginator)
                {{ $teachers->links() }}
            @endif
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .btn-plus {
        background-color: #4f46e5 !important;
        color: white !important;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        cursor: pointer;
        padding: 0;
        position: relative;
        transition: background-color 0.2s ease;
        flex-shrink: 0;
    }
    .btn-plus:hover { background-color: #4338ca !important; }
    .btn-plus .material-symbols-outlined {
        font-size: 18px !important;
        font-weight: bold;
    }
    .btn-plus:disabled { opacity: 0.6; cursor: not-allowed; }

    .whatsapp-popup {
        position: absolute;
        z-index: 9999;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15), 0 2px 6px rgba(0,0,0,0.08);
        padding: 6px 0;
        min-width: 200px;
        max-width: 260px;
        opacity: 0;
        transform: translateY(5px);
        transition: opacity 0.18s ease-out, transform 0.18s ease-out;
        pointer-events: none;
        border: 1px solid #f1f5f9;
    }
    .whatsapp-popup.visible {
        opacity: 1;
        transform: translateY(0);
        pointer-events: all;
    }
    .whatsapp-popup .popup-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 9px 16px;
        background: white;
        transition: background-color 0.1s;
        width: 100%;
        text-align: left;
        border: none;
        font-size: 12px;
        font-weight: 500;
        color: #111827;
        cursor: pointer;
        white-space: nowrap;
        font-family: inherit;
    }
    .whatsapp-popup .popup-item:hover { background-color: #f9fafb; }
    .whatsapp-popup .popup-item:active { background-color: #f3f4f6; }
    .whatsapp-popup .popup-item .material-symbols-outlined {
        font-size: 18px !important;
        flex-shrink: 0;
        color: #6b7280;
    }
    .whatsapp-popup .popup-item.text-danger { color: #e11d48; }
    .whatsapp-popup .popup-item.text-danger .material-symbols-outlined { color: #e11d48; }
    .whatsapp-popup .popup-item.disabled {
        opacity: 0.4;
        cursor: not-allowed;
        pointer-events: none;
    }
    .whatsapp-popup .popup-divider {
        height: 1px;
        background-color: #f1f5f9;
        margin: 4px 0;
    }
    .whatsapp-popup .popup-header {
        padding: 6px 16px 8px;
        font-size: 10px;
        color: #9ca3af;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function() {
    'use strict';

    const swalConfig = {
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold text-white mx-1',
            cancelButton: 'px-4 py-2 rounded-lg text-xs font-semibold text-white mx-1',
            title: 'text-base font-semibold',
            htmlContainer: 'text-xs text-gray-500'
        },
        buttonsStyling: false,
        reverseButtons: true
    };

    let activePopup = null;
    let activeButton = null;
    let scrollRefreshId = null;

    function calculatePopupPosition(btn, popup) {
        const btnRect = btn.getBoundingClientRect();
        const popupRect = popup.getBoundingClientRect();
        const windowWidth = window.innerWidth;
        const windowHeight = window.innerHeight;
        const scrollX = window.scrollX || window.pageXOffset;
        const scrollY = window.scrollY || window.pageYOffset;
        const GAP = -6;
        const MARGIN = 6;

        let top = btnRect.bottom + scrollY + GAP;
        let left = btnRect.left + scrollX + (btnRect.width / 2) - (popupRect.width / 2);

        if (left + popupRect.width > scrollX + windowWidth - MARGIN) left = scrollX + windowWidth - popupRect.width - MARGIN;
        if (left < scrollX + MARGIN) left = scrollX + MARGIN;
        if (top + popupRect.height > scrollY + windowHeight - MARGIN) top = btnRect.top + scrollY - popupRect.height - Math.abs(GAP);
        if (top < scrollY + MARGIN) top = scrollY + MARGIN;
        return { top, left };
    }

    function updatePopupPosition() {
        if (!activePopup || !activeButton) return;
        const pos = calculatePopupPosition(activeButton, activePopup);
        activePopup.style.top = pos.top + 'px';
        activePopup.style.left = pos.left + 'px';
    }

    function closePopup() {
        if (activePopup) {
            activePopup.classList.remove('visible');
            setTimeout(() => { if (activePopup && !activePopup.classList.contains('visible')) activePopup.remove(); }, 200);
            activePopup = null;
        }
        if (scrollRefreshId) { cancelAnimationFrame(scrollRefreshId); scrollRefreshId = null; }
        activeButton = null;
    }

    window.handleEmploiTempsPlus = function(enseignantId, btnEl, event) {
        if (event) { event.preventDefault(); event.stopPropagation(); }
        if (activeButton === btnEl && activePopup) { closePopup(); return; }
        closePopup();
        btnEl.disabled = true;
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
            const popup = document.createElement('div');
            popup.className = 'whatsapp-popup';

            if (exists) {
                popup.innerHTML = `
                    <div class="popup-header">Emploi du temps</div>
                    <button class="popup-item disabled"><span class="material-symbols-outlined">add_circle</span><span>Créer</span></button>
                    <button class="popup-item" data-action="navigate" data-url="${baseUrl}/client/emploi-temps/edit/${enseignantId}"><span class="material-symbols-outlined">edit</span><span>Modifier</span></button>
                    <button class="popup-item" data-action="navigate" data-url="${baseUrl}/client/emploi-temps/show/${enseignantId}"><span class="material-symbols-outlined">visibility</span><span>Consulter</span></button>
                    <div class="popup-divider"></div>
                    <button class="popup-item text-danger" data-action="delete-schedule" data-teacher-id="${enseignantId}"><span class="material-symbols-outlined">delete</span><span>Supprimer</span></button>
                `;
            } else {
                popup.innerHTML = `
                    <div class="popup-header">Emploi du temps</div>
                    <button class="popup-item" data-action="navigate" data-url="${baseUrl}/client/emploi-temps/create/${enseignantId}"><span class="material-symbols-outlined">add_circle</span><span>Créer</span></button>
                    <button class="popup-item disabled"><span class="material-symbols-outlined">edit</span><span>Modifier</span></button>
                    <button class="popup-item disabled"><span class="material-symbols-outlined">delete</span><span>Supprimer</span></button>
                `;
            }

            popup.querySelectorAll('.popup-item:not(.disabled)').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const action = this.dataset.action;
                    if (action === 'navigate') {
                        closePopup();
                        window.location.href = this.dataset.url;
                    } else if (action === 'delete-schedule') {
                        closePopup();
                        window.deleteTeacherSchedule(this.dataset.teacherId);
                    }
                });
            });

            popup.addEventListener('click', e => e.stopPropagation());
            document.body.appendChild(popup);

            const pos = calculatePopupPosition(btnEl, popup);
            popup.style.top = pos.top + 'px';
            popup.style.left = pos.left + 'px';

            requestAnimationFrame(() => popup.classList.add('visible'));
            activePopup = popup;
            activeButton = btnEl;

            function refreshOnScroll() {
                updatePopupPosition();
                scrollRefreshId = requestAnimationFrame(refreshOnScroll);
            }
            scrollRefreshId = requestAnimationFrame(refreshOnScroll);
        })
        .catch(() => {
            Swal.fire({ ...swalConfig, icon: 'error', title: 'Erreur', text: 'Impossible de vérifier l\'emploi du temps.', confirmButtonText: 'OK' });
        })
        .finally(() => { btnEl.disabled = false; });
    };

    window.deleteTeacherSchedule = function(enseignantId) {
        Swal.fire({
            ...swalConfig,
            title: 'Supprimer cet emploi du temps ?',
            text: 'Cette action est irréversible.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler',
            iconColor: '#e11d48'
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
                if (!response.ok) { const error = await response.json().catch(() => ({})); throw new Error(error.message || 'Impossible de supprimer cet emploi du temps.'); }
                return response.json();
            })
            .then(data => {
                Swal.fire({ ...swalConfig, icon: 'success', title: 'Succès', text: data.message || 'Emploi du temps supprimé.', timer: 2200, showConfirmButton: false });
            })
            .catch(error => {
                Swal.fire({ ...swalConfig, icon: 'error', title: 'Erreur', text: error.message || 'Impossible de supprimer.', confirmButtonText: 'OK' });
            });
        });
    };

    document.addEventListener('click', function(e) {
        if (activePopup && activeButton && !activePopup.contains(e.target) && !activeButton.contains(e.target)) closePopup();
    }, true);

    document.addEventListener('keydown', e => { if (e.key === 'Escape' && activePopup) closePopup(); });
    window.addEventListener('resize', updatePopupPosition);
    window.closeTeacherPopup = closePopup;

    document.querySelectorAll('.delete-teacher-btn').forEach((button) => {
        button.addEventListener('click', function() { handleDeleteClick(this); });
    });

    function handleDeleteClick(button) {
        const teacherId = button.dataset.id;
        const teacherName = button.dataset.name;

        Swal.fire({
            ...swalConfig,
            title: 'Supprimer cet enseignant ?',
            html: `L'enseignant <strong class="text-rose-600">"${teacherName}"</strong> sera définitivement supprimé.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler',
            iconColor: '#e11d48'
        }).then((result) => {
            if (result.isConfirmed) {
                button.disabled = true;
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
                        document.getElementById(`teacher-row-${teacherId}`)?.remove();
                        document.getElementById(`teacher-mobile-${teacherId}`)?.remove();
                        updateTotalTeachers(-1);
                        checkEmptyTable();
                        renumberRows();
                        Swal.fire({ ...swalConfig, icon: 'success', title: 'Supprimé', text: data.message, showConfirmButton: false, timer: 2000 });
                    } else {
                        Swal.fire({ ...swalConfig, icon: 'error', title: 'Erreur', text: data.message || 'Une erreur est survenue', confirmButtonText: 'OK' });
                    }
                })
                .catch(() => Swal.fire({ ...swalConfig, icon: 'error', title: 'Erreur', text: 'Une erreur est survenue', confirmButtonText: 'OK' }))
                .finally(() => { button.disabled = false; });
            }
        });
    }

    function updateTotalTeachers(change) {
        const totalSpan = document.getElementById('totalTeachers');
        if (totalSpan) totalSpan.textContent = (parseInt(totalSpan.textContent) || 0) + change;
    }

    function renumberRows() {
        document.querySelectorAll('#teachersTableBody .teacher-row').forEach((row, index) => {
            const cell = row.querySelector('td');
            if (cell) cell.textContent = index + 1;
        });
    }

    function checkEmptyTable() {
        const tbody = document.getElementById('teachersTableBody');
        const rows = tbody.querySelectorAll('.teacher-row');
        if (rows.length === 0) {
            document.getElementById('emptyRow')?.remove();
            const emptyRow = document.createElement('tr');
            emptyRow.id = 'emptyRow';
            emptyRow.innerHTML = `
                <td colspan="7" class="py-16 text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center mb-3">
                            <span class="material-symbols-outlined text-2xl text-gray-300">school</span>
                        </div>
                        <p class="text-sm font-semibold text-gray-700">Aucun enseignant trouvé</p>
                        <p class="text-xs text-gray-400 mt-1">Commencez par ajouter un enseignant.</p>
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
            let visibleCount = 0;
            document.querySelectorAll('.teacher-row').forEach(row => {
                const name = row.getAttribute('data-name') || '';
                const email = row.getAttribute('data-email') || '';
                const subject = row.getAttribute('data-subject') || '';
                const match = name.includes(searchTerm) || email.includes(searchTerm) || subject.includes(searchTerm);
                row.style.display = match ? '' : 'none';
                if (match) visibleCount++;
            });
            const paginationSpan = document.getElementById('paginationInfo');
            if (paginationSpan && !paginationSpan.innerHTML.includes('Précédent')) {
                paginationSpan.textContent = visibleCount === 1
                    ? `Affichage de 1 sur ${visibleCount} enseignant`
                    : `Affichage de ${visibleCount} sur ${visibleCount} enseignants`;
            }
        });
    }
})();
</script>
@endpush