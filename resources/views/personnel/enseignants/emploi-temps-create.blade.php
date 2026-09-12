@extends('personnel.layouts.app')
@section('title', $isEdit ? 'Modifier l\'emploi du temps' : 'Créer l\'emploi du temps')

@section('content')
<div class="mb-6 flex items-center justify-between gap-4 flex-wrap">
    <div class="flex items-center gap-3">
        <a href="{{ route('personnel.enseignants.index') }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white border border-outline-variant hover:bg-surface-container transition-all text-on-surface-variant flex-shrink-0">
            <span class="material-symbols-outlined text-xl">arrow_back</span>
        </a>
        <div>
            <h2 class="font-headline-md text-headline-md text-primary">
                {{ $isEdit ? 'Modifier' : 'Créer' }} l'emploi du temps
            </h2>
            <p class="text-xs text-text-muted">Enseignant : {{ $enseignant->nom }} {{ $enseignant->prenoms }} ({{ $enseignant->matricule ?? 'Sans matricule' }})</p>
        </div>
    </div>
</div>

<form id="scheduleForm" method="POST">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="glass-card rounded-xl border border-outline-variant/50 p-5 mb-6 shadow-sm">
        <div class="border-b border-surface-subtle pb-3 mb-4 flex justify-between items-center flex-wrap gap-2">
            <h3 class="font-bold text-sm uppercase text-primary flex items-center gap-2">
                <span class="material-symbols-outlined">school</span>
                Informations Enseignant &amp; Paramètres
            </h3>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary/10 text-primary">
                {{ $enseignant->matieres->pluck('nom')->join(', ') ?: 'Matière non assignée' }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-xs">
            <div><span class="text-text-muted block">Nom &amp; Prénoms :</span> <strong class="text-sm text-on-surface">{{ $enseignant->nom }} {{ $enseignant->prenoms }}</strong></div>
            <div><span class="text-text-muted block">Matricule :</span> <strong class="text-sm text-on-surface font-mono">{{ $enseignant->matricule ?? '—' }}</strong></div>
            <div><span class="text-text-muted block">Classes assignées :</span> <strong class="text-sm text-on-surface">{{ $enseignant->classes->pluck('nom')->join(', ') ?: 'Aucune' }}</strong></div>
            <div><span class="text-text-muted block">Contact :</span> <strong class="text-sm text-on-surface">{{ $enseignant->telephone ?? $enseignant->email }}</strong></div>
        </div>

        <div class="mt-4 pt-3 border-t border-surface-subtle max-w-sm">
            <label class="block text-xs font-semibold text-text-muted uppercase mb-1">Année académique</label>
            <select name="annee_academique_id" id="anneeAcademiqueId" class="w-full border border-outline-variant rounded-lg px-3 py-1.5 text-sm bg-white focus:ring-primary focus:border-primary">
                <option value="">Sélectionner une année (optionnel)</option>
                @foreach($academicYears as $ay)
                    <option value="{{ $ay->id }}">{{ $ay->libelle ?? ($ay->date_debut ? $ay->date_debut->format('Y') . '-' . ($ay->date_debut->format('Y')+1) : 'Année '.$ay->id) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Grille interactive des créneaux et cours -->
    <div class="glass-card rounded-xl border border-outline-variant/50 p-4 mb-6 shadow-sm overflow-hidden">
        <div class="flex justify-between items-center mb-3">
            <h4 class="font-headline-sm text-sm text-on-surface flex items-center gap-1.5">
                <span class="material-symbols-outlined text-primary">calendar_month</span>
                Grille des cours hebdomadaires
            </h4>
            <span class="text-xs text-text-muted">Cliquez sur une case pour ajouter/modifier un cours</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse border border-outline-variant min-w-[800px]" id="scheduleTable">
                <thead>
                    <tr class="bg-primary text-white text-center">
                        <th class="p-2.5 border border-primary/30 w-36">Créneaux</th>
                        @foreach($days as $day)
                            <th class="p-2.5 border border-primary/30">{{ ucfirst($day) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody id="scheduleBody">
                    @foreach($slots as $idx => $slot)
                        @if(isset($slot['break']))
                            <tr class="bg-surface-container-high font-semibold text-center text-text-muted">
                                <td colspan="{{ count($days) + 1 }}" class="py-1.5 border border-outline-variant">
                                    ☕ {{ $slot['break'] }}
                                </td>
                            </tr>
                        @else
                            @php($slotKey = $slot['key'] ?? 'slot-'.($idx+1))
                            <tr>
                                <td class="p-2 border border-outline-variant bg-surface-container-low text-center font-mono font-semibold text-xs whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1">
                                        <input type="time" class="slot-time-input border rounded px-1 py-0.5 text-xs text-center w-16" data-slot-key="{{ $slotKey }}" data-type="start" value="{{ $slot['start'] }}">
                                        <span>-</span>
                                        <input type="time" class="slot-time-input border rounded px-1 py-0.5 text-xs text-center w-16" data-slot-key="{{ $slotKey }}" data-type="end" value="{{ $slot['end'] }}">
                                    </div>
                                </td>
                                @foreach($days as $day)
                                    <td class="p-1.5 border border-outline-variant h-20 align-top transition-colors cursor-pointer hover:bg-primary-fixed/20 cell-slot"
                                        data-day="{{ $day }}"
                                        data-slot-key="{{ $slotKey }}"
                                        id="cell-{{ $day }}-{{ $slotKey }}">
                                        <div class="cell-content flex flex-col justify-center items-center h-full w-full rounded p-1 text-center">
                                            <span class="text-text-muted text-lg opacity-40 hover:opacity-100">+</span>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex justify-end gap-3 pb-8">
        <a href="{{ route('personnel.enseignants.index') }}" class="px-5 py-2 border border-outline-variant rounded-lg text-sm hover:bg-surface-container bg-white">
            Annuler
        </a>
        <button type="button" id="saveScheduleBtn" class="px-6 py-2 bg-primary text-white font-medium rounded-lg text-sm hover:opacity-90 active:scale-95 shadow-md flex items-center gap-1.5">
            <span class="material-symbols-outlined text-base">save</span>
            Enregistrer l'emploi du temps
        </button>
    </div>
</form>

<!-- Modal pour affecter un cours à une case -->
<div class="fixed inset-0 z-[200] hidden items-center justify-center p-4" id="modalCell">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeCellModal()"></div>
    <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md p-6 transform transition-all">
        <div class="flex justify-between items-center border-b border-surface-subtle pb-3 mb-4">
            <h3 class="font-headline-sm text-base text-primary flex items-center gap-1.5" id="modalCellTitle">
                <span class="material-symbols-outlined">edit_calendar</span>
                Affecter un cours
            </h3>
            <button type="button" onclick="closeCellModal()" class="text-text-muted hover:text-on-surface">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>

        <div class="space-y-4 text-sm">
            <div>
                <label class="block text-xs font-semibold text-text-muted uppercase mb-1">Classe <span class="text-alert-red">*</span></label>
                <select id="modalClasseSelect" class="w-full border border-outline-variant rounded-lg px-3 py-2 text-sm bg-white focus:ring-primary focus:border-primary">
                    <option value="">Sélectionner une classe</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}">{{ $c->nom }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-text-muted uppercase mb-1">Matière <span class="text-alert-red">*</span></label>
                <select id="modalMatiereSelect" class="w-full border border-outline-variant rounded-lg px-3 py-2 text-sm bg-white focus:ring-primary focus:border-primary">
                    <option value="">Sélectionner une matière</option>
                    @foreach($subjects as $s)
                        <option value="{{ $s->id }}">{{ $s->nom }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-text-muted uppercase mb-1">Salle (optionnel)</label>
                <input type="text" id="modalSalleInput" placeholder="Ex: Salle 102" class="w-full border border-outline-variant rounded-lg px-3 py-2 text-sm bg-white focus:ring-primary focus:border-primary">
            </div>
        </div>

        <div class="mt-6 pt-4 border-t border-surface-subtle flex justify-between gap-3">
            <button type="button" id="deleteCellBtn" class="px-3 py-1.5 text-alert-red hover:bg-error-container/20 rounded-lg text-xs font-medium flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">delete</span>
                Vider la case
            </button>
            <div class="flex gap-2">
                <button type="button" onclick="closeCellModal()" class="px-4 py-1.5 border border-outline-variant rounded-lg text-xs text-on-surface hover:bg-surface-container">Annuler</button>
                <button type="button" id="applyCellBtn" class="px-4 py-1.5 bg-primary text-white rounded-lg text-xs font-medium hover:opacity-90">Valider</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let scheduleCells = {};
let currentEditingCell = null;

const initialSlots = @json($slots);
const existingEntries = @json($existingEntries ?? []);

// Classes et Matières dictionnaires
const classesMap = {};
@foreach($classes as $c) classesMap[{{ $c->id }}] = @json($c->nom); @endforeach

const subjectsMap = {};
@foreach($subjects as $s) subjectsMap[{{ $s->id }}] = @json($s->nom); @endforeach

document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les entrées existantes
    existingEntries.forEach(entry => {
        const key = `${entry.day}-${entry.slot_key}`;
        scheduleCells[key] = {
            day: entry.day,
            slot_key: entry.slot_key,
            classe_id: entry.classe_id,
            matiere_id: entry.matiere_id,
            salle: entry.salle || ''
        };
        renderCell(entry.day, entry.slot_key);
    });

    // Clic sur une case
    document.querySelectorAll('.cell-slot').forEach(cell => {
        cell.addEventListener('click', function() {
            const day = this.dataset.day;
            const slotKey = this.dataset.slotKey;
            openCellModal(day, slotKey);
        });
    });

    // Valider modal case
    document.getElementById('applyCellBtn').addEventListener('click', function() {
        if (!currentEditingCell) return;
        const classeId = document.getElementById('modalClasseSelect').value;
        const matiereId = document.getElementById('modalMatiereSelect').value;
        const salle = document.getElementById('modalSalleInput').value.trim();

        if (!classeId || !matiereId) {
            Swal.fire({ icon: 'warning', title: 'Attention', text: 'Veuillez sélectionner au moins une classe et une matière.' });
            return;
        }

        const key = `${currentEditingCell.day}-${currentEditingCell.slotKey}`;
        scheduleCells[key] = {
            day: currentEditingCell.day,
            slot_key: currentEditingCell.slotKey,
            classe_id: parseInt(classeId),
            matiere_id: parseInt(matiereId),
            salle: salle
        };

        renderCell(currentEditingCell.day, currentEditingCell.slotKey);
        closeCellModal();
    });

    // Vider la case
    document.getElementById('deleteCellBtn').addEventListener('click', function() {
        if (!currentEditingCell) return;
        const key = `${currentEditingCell.day}-${currentEditingCell.slotKey}`;
        delete scheduleCells[key];
        renderCell(currentEditingCell.day, currentEditingCell.slotKey);
        closeCellModal();
    });

    // Sauvegarder tout l'emploi du temps
    document.getElementById('saveScheduleBtn').addEventListener('click', function() {
        const slotsData = [];
        document.querySelectorAll('.slot-time-input[data-type="start"]').forEach(startInput => {
            const slotKey = startInput.dataset.slotKey;
            const endInput = document.querySelector(`.slot-time-input[data-slot-key="${slotKey}"][data-type="end"]`);
            slotsData.push({
                key: slotKey,
                start: startInput.value,
                end: endInput.value
            });
        });

        const cellsData = Object.values(scheduleCells);

        const payload = {
            annee_academique_id: document.getElementById('anneeAcademiqueId').value || null,
            slots: slotsData,
            cells: cellsData
        };

        const enseignantId = {{ $enseignant->id }};
        const url = @json($isEdit ? route('personnel.enseignants.emploi-temps.teacher.update', $enseignant) : route('personnel.enseignants.emploi-temps.teacher.store', $enseignant));
        const method = @json($isEdit ? 'PUT' : 'POST');

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(r => r.json().then(data => ({ status: r.status, body: data })))
        .then(({ status, body }) => {
            if (status === 200 || status === 201) {
                Swal.fire({
                    icon: 'success',
                    title: 'Succès',
                    text: body.message || 'Emploi du temps enregistré !',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = body.redirect || @json(route('personnel.enseignants.emploi-temps.show', $enseignant));
                });
            } else {
                const err = body.message || (body.errors ? Object.values(body.errors).flat().join('\n') : 'Une erreur est survenue.');
                Swal.fire({ icon: 'error', title: 'Erreur', text: err });
            }
        })
        .catch(() => {
            Swal.fire({ icon: 'error', title: 'Erreur', text: 'Impossible de contacter le serveur.' });
        });
    });
});

function openCellModal(day, slotKey) {
    currentEditingCell = { day, slotKey };
    const key = `${day}-${slotKey}`;
    const data = scheduleCells[key];

    document.getElementById('modalCellTitle').innerHTML = `<span class="material-symbols-outlined">edit_calendar</span> ${day.charAt(0).toUpperCase() + day.slice(1)} - ${slotKey}`;
    document.getElementById('modalClasseSelect').value = data ? data.classe_id : '';
    document.getElementById('modalMatiereSelect').value = data ? data.matiere_id : '';
    document.getElementById('modalSalleInput').value = data ? (data.salle || '') : '';

    const modal = document.getElementById('modalCell');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeCellModal() {
    const modal = document.getElementById('modalCell');
    modal.classList.remove('flex');
    modal.classList.add('hidden');
    currentEditingCell = null;
}

function renderCell(day, slotKey) {
    const el = document.getElementById(`cell-${day}-${slotKey}`);
    if (!el) return;
    const key = `${day}-${slotKey}`;
    const data = scheduleCells[key];

    if (data) {
        const className = classesMap[data.classe_id] || `Classe #${data.classe_id}`;
        const subjectName = subjectsMap[data.matiere_id] || `Matière #${data.matiere_id}`;
        const salleText = data.salle ? `<span class="text-[10px] text-text-muted">(${data.salle})</span>` : '';

        el.innerHTML = `
            <div class="h-full w-full bg-primary/10 border border-primary/30 rounded p-1 flex flex-col justify-between text-left shadow-sm">
                <span class="font-bold text-primary text-xs truncate">${className}</span>
                <span class="font-medium text-on-surface text-[11px] truncate">${subjectName}</span>
                ${salleText}
            </div>
        `;
    } else {
        el.innerHTML = `
            <div class="cell-content flex flex-col justify-center items-center h-full w-full rounded p-1 text-center">
                <span class="text-text-muted text-lg opacity-40 hover:opacity-100">+</span>
            </div>
        `;
    }
}
</script>
@endpush
