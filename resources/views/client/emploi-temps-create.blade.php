@extends('client.layouts.app')

@section('title', $editing ? 'Modifier un emploi du temps' : 'Créer un emploi du temps')

@section('content')

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
    <div class="flex items-center gap-3">
        <a href="{{ route('client.enseignant') }}" class="w-9 h-9 rounded-xl bg-white border border-gray-100 hover:bg-gray-100 flex items-center justify-center transition flex-shrink-0">
            <span class="material-symbols-outlined text-gray-600 text-base">arrow_back</span>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $editing ? 'Modifier' : 'Créer' }} l'emploi du temps</h2>
            <p class="text-sm text-gray-500 mt-0.5">Cliquez sur une cellule pour ajouter ou modifier un cours</p>
        </div>
    </div>
</div>

<form id="scheduleForm"
      action="{{ $editing ? route('client.emploi-temps.teacher.update', $enseignant) : route('client.emploi-temps.teacher.store', $enseignant) }}"
      method="POST">

    @csrf
    @if($editing) @method('PUT') @endif

    <input type="hidden" name="etablissement_id" value="{{ $establishmentId }}">

    {{-- Fiche enseignant --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-5">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                <span class="material-symbols-outlined text-base">person</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Informations de l'enseignant</h3>
                <p class="text-[11px] text-gray-500">Récapitulatif</p>
            </div>
        </div>

        <div class="p-5 space-y-4">
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
                <div class="bg-gray-50/50 rounded-xl p-3">
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Nom</p>
                    <p class="text-xs font-semibold text-gray-900">{{ $enseignant->nom }}</p>
                </div>
                <div class="bg-gray-50/50 rounded-xl p-3">
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Prénoms</p>
                    <p class="text-xs font-semibold text-gray-900">{{ $enseignant->prenoms }}</p>
                </div>
                <div class="bg-gray-50/50 rounded-xl p-3">
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Matricule</p>
                    <p class="text-xs font-semibold text-gray-900 font-mono">{{ $enseignant->matricule ?? '—' }}</p>
                </div>
                <div class="bg-gray-50/50 rounded-xl p-3 col-span-2">
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Discipline(s)</p>
                    <p class="text-xs font-semibold text-gray-900">{{ $enseignant->matieres->pluck('nom')->join(', ') ?: '—' }}</p>
                </div>
                <div class="bg-gray-50/50 rounded-xl p-3">
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Contact</p>
                    <p class="text-xs font-semibold text-gray-900">{{ $enseignant->telephone ?? $enseignant->email ?? '—' }}</p>
                </div>
                <div class="bg-gray-50/50 rounded-xl p-3 col-span-2 lg:col-span-1">
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Années d'enseignement</p>
                    <p class="text-xs font-semibold text-gray-900">{{ $enseignant->nombre_annees_enseignement ?? '—' }}</p>
                </div>
                <div class="bg-gray-50/50 rounded-xl p-3 col-span-2">
                    <label class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Année académique</label>
                    <select name="annee_academique_id"
                            class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                        <option value="">Non précisée</option>
                        @php($selectedYearId = old('annee_academique_id', $year?->id))
                        @foreach($years as $academicYear)
                            <option value="{{ $academicYear->id }}" @selected($selectedYearId == $academicYear->id)>{{ $academicYear->libelle }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Grille --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-5">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                <span class="material-symbols-outlined text-base">calendar_view_week</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Grille hebdomadaire</h3>
                <p class="text-[11px] text-gray-500">Cliquez sur une cellule pour ajouter, modifier ou supprimer</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1200px] border-collapse text-xs">
                <thead>
                    <tr class="bg-gradient-to-r from-indigo-600 to-indigo-500">
                        <th class="sticky left-0 z-20 bg-gradient-to-r from-indigo-700 to-indigo-600 text-white text-center px-4 py-3 text-[10px] font-bold uppercase tracking-wider w-[160px] min-w-[160px] max-w-[160px]">
                            Horaires
                        </th>
                        @foreach($days as $day)
                            <th class="text-white text-center px-4 py-3 text-[10px] font-bold uppercase tracking-wider min-w-[200px] w-[200px]">
                                {{ ucfirst($day) }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($slots as $slot)
                        @if(isset($slot['break']))
                            <tr>
                                <th class="sticky left-0 z-10 bg-gradient-to-r from-indigo-100 to-indigo-50 text-indigo-700 text-[10px] font-bold uppercase tracking-widest text-center py-2.5 px-3 border-y border-indigo-200">
                                    {{ $slot['break'] }}
                                </th>
                                <td colspan="{{ count($days) }}" class="bg-gradient-to-r from-indigo-50 to-indigo-100/50 text-indigo-700 text-center py-2.5 text-[10px] font-bold uppercase tracking-widest border-y border-indigo-200">
                                    {{ $slot['break'] }}
                                </td>
                            </tr>
                        @else
                            @php($slotKey = $slot['key'] ?? $slot[0] . '-' . $slot[1])
                            @php($startTime = $slot['start'] ?? $slot[0])
                            @php($endTime = $slot['end'] ?? $slot[1])
                            <tr class="border-t border-gray-100 hover:bg-gray-50/30 transition-colors">
                                <th class="sticky left-0 z-10 bg-gray-50/95 backdrop-blur-sm align-middle py-3 px-3 border-r border-gray-100">
                                    <div class="flex flex-col gap-1.5 items-center">
                                        <label class="sr-only" for="start-{{ $slotKey }}">Début</label>
                                        <input id="start-{{ $slotKey }}" type="time" name="slots[{{ $slotKey }}][heure_debut]" value="{{ $startTime }}" required
                                               class="w-[74px] text-center bg-white border border-gray-200 rounded-lg text-[11px] font-semibold text-gray-900 py-1 px-1.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                                        <span class="text-[10px] font-bold text-gray-400 uppercase">à</span>
                                        <label class="sr-only" for="end-{{ $slotKey }}">Fin</label>
                                        <input id="end-{{ $slotKey }}" type="time" name="slots[{{ $slotKey }}][heure_fin]" value="{{ $endTime }}" required
                                               class="w-[74px] text-center bg-white border border-gray-200 rounded-lg text-[11px] font-semibold text-gray-900 py-1 px-1.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                                    </div>
                                </th>

                                @foreach($days as $day)
                                    @php($entry = $grid[$day][$slotKey] ?? null)
                                    @php($key = $day . '|' . $slotKey)
                                    @php($hasData = $entry && ($entry->classe_id || $entry->matiere_id))
                                    <td class="align-top p-2 {{ $hasData ? 'has-data' : 'empty-cell' }}" data-day="{{ $day }}" data-slot="{{ $slotKey }}">
                                        @if($hasData)
                                            <div class="bg-indigo-50/50 border border-indigo-100 rounded-lg p-3 h-full space-y-1.5 border-l-[3px] border-l-indigo-500">
                                                <span class="block text-xs font-bold text-gray-900 leading-tight">{{ $entry->classe->nom ?? '—' }}</span>
                                                <span class="block text-[11px] text-gray-600 font-medium">{{ $entry->matiere->nom ?? '—' }}</span>
                                                @if($entry->serie)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-100 text-[10px] font-bold text-gray-700 uppercase tracking-wider">
                                                        {{ $entry->serie->nom_serie }}
                                                    </span>
                                                @endif
                                                <button type="button" class="edit-cell-btn mt-2 inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-[10px] font-semibold text-indigo-700 bg-white border border-indigo-200 hover:bg-indigo-100 transition"
                                                        data-key="{{ $key }}" data-day="{{ $day }}" data-slot="{{ $slotKey }}">
                                                    <span class="material-symbols-outlined text-[14px]">edit</span>
                                                    Modifier
                                                </button>
                                            </div>

                                            <input type="hidden" name="cells[{{ $key }}][classe_id]" value="{{ $entry->classe_id }}">
                                            <input type="hidden" name="cells[{{ $key }}][matiere_id]" value="{{ $entry->matiere_id }}">
                                            <input type="hidden" name="cells[{{ $key }}][serie_id]" value="{{ $entry->serie_id }}">
                                        @else
                                            <button type="button" class="add-cell-btn w-full h-full min-h-[100px] flex items-center justify-center transition"
                                                    data-key="{{ $key }}" data-day="{{ $day }}" data-slot="{{ $slotKey }}">
                                                <span class="add-dot w-3 h-3 rounded-full bg-gray-300 transition"></span>
                                            </button>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal --}}
    <div id="cellModal" class="modal-overlay hidden">
        <div class="modal-content">
            <div class="modal-header">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                        <span class="material-symbols-outlined text-base">event</span>
                    </div>
                    <div>
                        <h3 id="modalTitle" class="text-base font-bold text-gray-900">Ajouter un cours</h3>
                        <p class="text-[11px] text-gray-500">Sélectionnez classe et matière</p>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center transition" onclick="closeModal()">
                    <span class="material-symbols-outlined text-gray-500">close</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modalCellKey">
                <input type="hidden" id="modalDay">
                <input type="hidden" id="modalSlot">

                <div class="form-group">
                    <label for="modalClasse" class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Classe</label>
                    <select id="modalClasse" class="modal-select">
                        <option value="">— Libre —</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="modalMatiere" class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Matière</label>
                    <select id="modalMatiere" class="modal-select">
                        <option value="">Matière</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="modalSerie" class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Série (facultatif)</label>
                    <select id="modalSerie" class="modal-select">
                        <option value="">Aucune série</option>
                        @foreach($series as $serie)
                            <option value="{{ $serie->id }}">{{ $serie->nom_serie }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                @if($editing)
                    <button type="button" class="btn-danger" id="deleteCellBtn">Supprimer</button>
                @endif
                <button type="button" class="btn-secondary" onclick="closeModal()">Annuler</button>
                <button type="button" class="btn-primary" id="saveCellBtn">Enregistrer</button>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
        <a href="{{ route('client.enseignant') }}"
           class="w-full sm:w-auto text-center px-5 py-2.5 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition">
            Annuler
        </a>
        <button type="submit"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-xs font-semibold transition shadow-sm">
            <span class="material-symbols-outlined text-sm">save</span>
            Enregistrer l'emploi du temps
        </button>
    </div>
</form>

@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.css" rel="stylesheet">
<style>
    .sr-only {
        position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px;
        overflow: hidden; clip: rect(0, 0, 0, 0); border: 0;
    }

    /* Empty cell — hover effect */
    .empty-cell .add-cell-btn .add-dot {
        position: relative;
    }
    .empty-cell .add-cell-btn .add-dot::before {
        content: '+';
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-size: 10px;
        font-weight: bold;
        opacity: 0;
        transition: opacity 0.2s ease;
    }
    .empty-cell:hover {
        background: #f8fafc;
    }
    .empty-cell:hover .add-dot {
        background: #4f46e5 !important;
        transform: scale(1.3);
        box-shadow: 0 0 20px rgba(79, 70, 229, 0.2);
    }
    .empty-cell:hover .add-dot::before {
        opacity: 1;
    }

    /* Modal */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.4);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        padding: 1rem;
    }
    .modal-overlay.hidden { display: none; }

    .modal-content {
        background: white;
        border-radius: 16px;
        width: 100%;
        max-width: 480px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
        animation: modalSlideUp 0.25s ease-out;
    }

    @keyframes modalSlideUp {
        from { transform: translateY(20px); opacity: 0; }
        to   { transform: translateY(0);    opacity: 1; }
    }

    .modal-header {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .modal-body {
        padding: 20px;
    }

    .form-group { margin-bottom: 14px; }
    .form-group:last-child { margin-bottom: 0; }

    .modal-select {
        width: 100%;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        font-size: 12px;
        padding: 10px 12px;
        color: #111827;
        outline: none;
        transition: all 0.15s ease;
    }
    .modal-select:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
        background: #ffffff;
    }

    .modal-footer {
        padding: 14px 20px;
        border-top: 1px solid #f1f5f9;
        background: #f9fafb;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        border-radius: 0 0 16px 16px;
    }

    .btn-secondary {
        padding: 8px 16px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: white;
        color: #374151;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s ease;
    }
    .btn-secondary:hover { background: #f3f4f6; }

    .btn-primary {
        padding: 8px 20px;
        border: none;
        border-radius: 8px;
        background: #4f46e5;
        color: white;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s ease;
    }
    .btn-primary:hover { background: #4338ca; }

    .btn-danger {
        padding: 8px 14px;
        border: 1px solid #fecaca;
        border-radius: 8px;
        background: #fef2f2;
        color: #e11d48;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s ease;
        margin-right: auto;
    }
    .btn-danger:hover { background: #fee2e2; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentCellKey = null;
    let currentDay = null;
    let currentSlot = null;
    let isEditing = false;

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

    // ---- Bind cell buttons (with event delegation to survive re-render) ----
    document.querySelector('table').addEventListener('click', function (e) {
        const addBtn = e.target.closest('.add-cell-btn');
        if (addBtn) {
            e.stopPropagation();
            openModal(addBtn.dataset.key, addBtn.dataset.day, addBtn.dataset.slot, false);
            return;
        }
        const editBtn = e.target.closest('.edit-cell-btn');
        if (editBtn) {
            e.stopPropagation();
            const cell = editBtn.closest('td');
            const classId = cell.querySelector('input[name*="[classe_id]"]')?.value || '';
            const matiereId = cell.querySelector('input[name*="[matiere_id]"]')?.value || '';
            const serieId = cell.querySelector('input[name*="[serie_id]"]')?.value || '';
            openModal(editBtn.dataset.key, editBtn.dataset.day, editBtn.dataset.slot, true, classId, matiereId, serieId);
        }
    });

    function openModal(key, day, slot, editMode = false, classId = '', matiereId = '', serieId = '') {
        currentCellKey = key;
        currentDay = day;
        currentSlot = slot;
        isEditing = editMode;

        const modal = document.getElementById('cellModal');
        const title = document.getElementById('modalTitle');
        const deleteBtn = document.getElementById('deleteCellBtn');

        title.textContent = editMode ? 'Modifier le cours' : 'Ajouter un cours';
        if (deleteBtn) deleteBtn.style.display = editMode ? 'inline-block' : 'none';

        document.getElementById('modalClasse').value = classId;
        document.getElementById('modalMatiere').value = matiereId;
        document.getElementById('modalSerie').value = serieId;

        modal.classList.remove('hidden');
    }

    window.closeModal = function () {
        document.getElementById('cellModal').classList.add('hidden');
        currentCellKey = null;
        currentDay = null;
        currentSlot = null;
        isEditing = false;
    };

    // ---- Save cell ----
    document.getElementById('saveCellBtn').addEventListener('click', function () {
        const classId = document.getElementById('modalClasse').value;
        const matiereId = document.getElementById('modalMatiere').value;
        const serieId = document.getElementById('modalSerie').value;

        if (!classId || !matiereId) {
            Swal.fire({ ...swalConfig, icon: 'warning', title: 'Champs requis', text: 'Veuillez sélectionner une classe et une matière.', confirmButtonText: 'OK' });
            return;
        }

        const cell = document.querySelector(`td[data-day="${currentDay}"][data-slot="${currentSlot}"]`);

        if (cell) {
            const classText = document.querySelector(`#modalClasse option[value="${classId}"]`)?.textContent || '—';
            const matiereText = document.querySelector(`#modalMatiere option[value="${matiereId}"]`)?.textContent || '—';
            const serieText = serieId ? (document.querySelector(`#modalSerie option[value="${serieId}"]`)?.textContent || '') : '';

            let html = `
                <div class="bg-indigo-50/50 border border-indigo-100 rounded-lg p-3 h-full space-y-1.5 border-l-[3px] border-l-indigo-500">
                    <span class="block text-xs font-bold text-gray-900 leading-tight">${classText}</span>
                    <span class="block text-[11px] text-gray-600 font-medium">${matiereText}</span>
                    ${serieText ? `<span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-100 text-[10px] font-bold text-gray-700 uppercase tracking-wider">${serieText}</span>` : ''}
                    <button type="button" class="edit-cell-btn mt-2 inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-[10px] font-semibold text-indigo-700 bg-white border border-indigo-200 hover:bg-indigo-100 transition"
                            data-key="${currentCellKey}" data-day="${currentDay}" data-slot="${currentSlot}">
                        <span class="material-symbols-outlined text-[14px]">edit</span>
                        Modifier
                    </button>
                </div>
                <input type="hidden" name="cells[${currentCellKey}][classe_id]" value="${classId}">
                <input type="hidden" name="cells[${currentCellKey}][matiere_id]" value="${matiereId}">
                <input type="hidden" name="cells[${currentCellKey}][serie_id]" value="${serieId}">
            `;

            cell.innerHTML = html;
            cell.className = 'align-top p-2 has-data';
            cell.setAttribute('data-day', currentDay);
            cell.setAttribute('data-slot', currentSlot);
        }

        closeModal();
        Swal.fire({ ...swalConfig, icon: 'success', title: 'Succès', text: 'Le cours a été enregistré.', timer: 1500, showConfirmButton: false });
    });

    // ---- Delete cell ----
    document.getElementById('deleteCellBtn')?.addEventListener('click', function () {
        Swal.fire({
            ...swalConfig,
            title: 'Supprimer ce cours ?',
            text: 'Cette action est irréversible.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler',
            iconColor: '#e11d48'
        }).then((result) => {
            if (result.isConfirmed) {
                const cell = document.querySelector(`td[data-day="${currentDay}"][data-slot="${currentSlot}"]`);
                if (cell) {
                    cell.innerHTML = `
                        <button type="button" class="add-cell-btn w-full h-full min-h-[100px] flex items-center justify-center transition"
                                data-key="${currentCellKey}" data-day="${currentDay}" data-slot="${currentSlot}">
                            <span class="add-dot w-3 h-3 rounded-full bg-gray-300 transition"></span>
                        </button>
                    `;
                    cell.className = 'align-top p-2 empty-cell';
                    cell.setAttribute('data-day', currentDay);
                    cell.setAttribute('data-slot', currentSlot);
                }
                closeModal();
                Swal.fire({ ...swalConfig, icon: 'success', title: 'Supprimé', text: 'Le cours a été supprimé.', timer: 1500, showConfirmButton: false });
            }
        });
    });

    // ---- Modal close on backdrop click ----
    document.getElementById('cellModal').addEventListener('click', function (e) {
        if (e.target === this) closeModal();
    });

    // ---- Modal close on Escape ----
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !document.getElementById('cellModal').classList.contains('hidden')) {
            closeModal();
        }
    });

    // ---- Form submit via AJAX ----
    document.getElementById('scheduleForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        const form = e.currentTarget;

        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: new FormData(form)
        });

        const data = await response.json().catch(() => ({}));

        if (response.ok) {
            await Swal.fire({ ...swalConfig, icon: 'success', title: 'Succès', text: data.message || 'Emploi du temps enregistré avec succès.', confirmButtonText: 'OK' });
            if (data.redirect) window.location = data.redirect;
        } else {
            let msg = 'Veuillez vérifier la grille.';
            if (data.errors) msg = Object.values(data.errors).flat().join('\n');
            else if (data.message) msg = data.message;
            Swal.fire({ ...swalConfig, icon: 'error', title: 'Erreur', text: msg, confirmButtonText: 'OK' });
        }
    });
});
</script>
@endpush