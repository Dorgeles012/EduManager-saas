@extends('personnel.layouts.app')
@section('title', 'EduManager - Matieres')
@section('content')

@if(session('error'))
    <div class="mb-4 px-4 py-3 rounded-lg bg-error-container/20 text-error">{{ session('error') }}</div>
@endif

<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-primary">Gestion des Matières</h2>
        <p class="font-body-md text-body-md text-text-muted mt-1">Gérez l'ensemble des matières enseignées dans l'établissement</p>
    </div>
    <button class="flex items-center gap-2 px-6 py-2.5 bg-primary text-on-primary rounded-lg font-label-md text-label-md hover:opacity-90 active:scale-95 transition-all card-shadow" onclick="openModal('addModal')">
        <span class="material-symbols-outlined">add</span>
        Ajouter une matière
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-gutter-desktop mb-10">
    <div class="bg-surface-container-lowest p-6 rounded-xl shadow-[4px_4px_12px_rgba(55,48,163,0.04)] border border-outline-variant/30 flex items-center gap-5">
        <div class="w-14 h-14 rounded-full bg-primary-container/10 flex items-center justify-center text-primary">
            <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">book</span>
        </div>
        <div>
            <p class="text-text-muted text-sm font-medium">Matières disponibles</p>
            <p class="text-3xl font-headline-md text-primary" id="totalSubjectsCount">{{ $totalSubjects ?? 0 }}</p>
        </div>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-xl shadow-[4px_4px_12px_rgba(55,48,163,0.04)] border border-outline-variant/30 flex items-center gap-5">
        <div class="w-14 h-14 rounded-full bg-warning-amber/10 flex items-center justify-center text-warning-amber">
            <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">person_search</span>
        </div>
        <div>
            <p class="text-text-muted text-sm font-medium">Enseignants assignés</p>
            <p class="text-3xl font-headline-md text-warning-amber">{{ $assignedTeachersCount ?? 0 }}</p>
        </div>
    </div>
</div>

<div class="bg-surface-container-lowest rounded-xl shadow-[0_4px_24px_rgba(55,48,163,0.04)] border border-outline-variant overflow-hidden">
    <div class="px-6 py-5 border-b border-surface-subtle flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full sm:w-auto">
            <h3 class="font-headline-md text-headline-md text-on-surface whitespace-nowrap">Liste des Matières</h3>
            <div class="relative w-full sm:w-56">
                <select id="serieFilterSelect" class="w-full appearance-none px-4 py-2 pr-10 rounded-lg border border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all bg-surface-container-lowest cursor-pointer" onchange="filterBySerie(this.value)">
                    <option value="all">Toutes les séries</option>
                    @foreach($series ?? [] as $serie)
                        <option value="{{ $serie->id }}">{{ $serie->nom_serie }}</option>
                    @endforeach
                </select>
                <span class="absolute inset-y-0 right-3 flex items-center text-text-muted pointer-events-none">
                    <span class="material-symbols-outlined text-xl">expand_more</span>
                </span>
            </div>
        </div>
    </div>

    <div id="activeFilterIndicator" class="px-6 py-2 bg-primary/5 border-b border-outline-variant/20 text-sm text-text-muted hidden">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <span class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-base">filter_list</span>
                Filtré par : <span id="activeFilterName" class="font-medium text-primary">Toutes</span>
                <button onclick="resetAllFilters()" class="ml-2 text-xs text-primary hover:underline">(Effacer le filtre)</button>
            </span>
            <span class="flex items-center gap-2 text-sm">
                <span class="text-text-muted">Coefficient total :</span>
                <span class="font-bold text-secondary" id="filteredCoefficientDisplay">0</span>
            </span>
        </div>
    </div>

    <template id="initialSubjectsTemplate">
        @foreach($subjects ?? [] as $subject)
        <tr class="hover:bg-primary/5 transition-colors group subject-row" 
            data-subject-name="{{ strtolower($subject['name']) }}" 
            data-subject-serie="{{ $subject['serie_id'] ?? '' }}"
            data-subject-coefficient="{{ $subject['coefficient'] ?? 0 }}">
            <td class="px-6 py-4 text-on-surface-variant">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
            <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded bg-primary/10 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-lg">menu_book</span>
                    </div>
                    <span class="font-medium text-on-surface subject-name">{{ $subject['name'] }}</span>
                </div>
            </td>
            <td class="px-6 py-4 text-center">
                <span class="px-3 py-1 bg-surface-container-high text-primary rounded-full font-bold text-xs subject-coefficient">{{ $subject['coefficient'] }}</span>
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-success-green"></div>
                    <span class="text-sm font-medium text-success-green">Active</span>
                </div>
            </td>
            <td class="px-6 py-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-surface-container-high/40 text-on-surface subject-serie">{{ $subject['serie'] ?? 'N/A' }}</span>
            </td>
            <td class="px-6 py-4 text-right">
                <div class="flex justify-end gap-2">
                    <button class="p-2 text-primary hover:bg-primary/10 rounded-lg transition-all" onclick="editMatiere({{ $subject['id'] }}, @js($subject['name']), {{ $subject['coefficient'] }}, @js($subject['serie_id'] ?? 0))" title="Modifier">
                        <span class="material-symbols-outlined">edit</span>
                    </button>
                    <form action="{{ route('personnel.matieres.destroy', $subject['id']) }}" method="POST" class="inline delete-subject-form">
                        @csrf
                        @method('DELETE')
                        <button class="p-2 text-alert-red hover:bg-alert-red/10 rounded-lg transition-all delete-subject-btn" data-name="{{ $subject['name'] }}" type="button" title="Supprimer">
                            <span class="material-symbols-outlined">delete</span>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @endforeach
    </template>

    <div class="overflow-x-auto">
        <table class="w-full text-left zebra-table" id="subjectsTable">
            <thead>
                <tr class="bg-surface-subtle/50 text-slate-600">
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">N°</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Nom de la matière</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px] text-center">Coefficient</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Statut</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Série</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px] text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/20" id="subjectsTableBody"></tbody>
        </table>
    </div>

    <div class="px-6 py-4 bg-surface-container-low/30 border-t border-outline-variant flex justify-between items-center text-sm text-text-muted">
        <span>Affichage de <span id="visibleCount">0</span> sur <span id="totalCount">{{ count($subjects ?? []) }}</span> matières</span>
    </div>
</div>

<div class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4" id="addModal">
    <div class="absolute inset-0 modal-backdrop backdrop-blur-md bg-black/30" onclick="closeModal('addModal')"></div>
    <div class="bg-white w-full max-w-md rounded-xl shadow-2xl relative z-10 overflow-hidden transform transition-all scale-95 opacity-0 duration-300" id="addModalContent">
        <div class="p-6 border-b border-outline-variant/30 flex justify-between items-center bg-primary text-on-primary">
            <h3 class="font-headline-md text-headline-md">Ajouter une matière</h3>
            <button class="hover:bg-white/20 p-1 rounded-full transition-colors" onclick="closeModal('addModal')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form class="p-6 space-y-5" action="{{ route('personnel.matieres.store') }}" method="POST">
            @csrf
            <div class="space-y-2">
                <label class="font-label-md text-on-surface">Nom de la matière</label>
                <input class="w-full px-4 py-2.5 rounded-lg border border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all" name="nom" placeholder="Ex: Informatique" required type="text">
            </div>
            <div class="space-y-2">
                <label class="font-label-md text-on-surface">Coefficient</label>
                <input class="w-full px-4 py-2.5 rounded-lg border border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all" name="coefficient" max="10" min="1" placeholder="1-10" required type="number">
            </div>
            <div class="space-y-2">
                <label class="font-label-md text-on-surface">Série <span class="text-alert-red">*</span></label>
                <select class="w-full px-4 py-2.5 rounded-lg border border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all" name="serie" required>
                    <option value="">Sélectionner une série</option>
                    @foreach($series ?? [] as $s)
                        <option value="{{ $s->id }}">{{ $s->nom_serie }}</option>
                    @endforeach
                </select>
            </div>
            <div class="pt-4 flex gap-3">
                <button class="flex-1 px-4 py-2.5 border border-outline-variant rounded-lg font-label-md text-on-surface-variant hover:bg-surface-container-low transition-colors" onclick="closeModal('addModal')" type="button">Annuler</button>
                <button class="flex-1 px-4 py-2.5 bg-primary text-on-primary rounded-lg font-label-md hover:bg-primary-container transition-colors" type="submit">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<div class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4" id="editModal">
    <div class="absolute inset-0 modal-backdrop backdrop-blur-md bg-black/30" onclick="closeModal('editModal')"></div>
    <div class="bg-white w-full max-w-md rounded-xl shadow-2xl relative z-10 overflow-hidden transform transition-all scale-95 opacity-0 duration-300" id="editModalContent">
        <div class="p-6 border-b border-outline-variant/30 flex justify-between items-center bg-primary text-on-primary">
            <h3 class="font-headline-md text-headline-md">Modifier la matière</h3>
            <button class="hover:bg-white/20 p-1 rounded-full transition-colors" onclick="closeModal('editModal')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form class="p-6 space-y-5" id="editSubjectForm" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-2">
                <label class="font-label-md text-on-surface">Nom de la matière</label>
                <input class="w-full px-4 py-2.5 rounded-lg border border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all" id="editName" name="nom" required type="text">
            </div>
            <div class="space-y-2">
                <label class="font-label-md text-on-surface">Coefficient</label>
                <input class="w-full px-4 py-2.5 rounded-lg border border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all" id="editCoeff" name="coefficient" max="10" min="1" required type="number">
            </div>
            <div class="space-y-2">
                <label class="font-label-md text-on-surface">Série <span class="text-alert-red">*</span></label>
                <select class="w-full px-4 py-2.5 rounded-lg border border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all" id="editSerie" name="serie" required>
                    <option value="">Sélectionner une série</option>
                    @foreach($series ?? [] as $s)
                        <option value="{{ $s->id }}">{{ $s->nom_serie }}</option>
                    @endforeach
                </select>
            </div>
            <div class="pt-4 flex gap-3">
                <button class="flex-1 px-4 py-2.5 border border-outline-variant rounded-lg font-label-md text-on-surface-variant hover:bg-surface-container-low transition-colors" onclick="closeModal('editModal')" type="button">Annuler</button>
                <button class="flex-1 px-4 py-2.5 bg-primary text-on-primary rounded-lg font-label-md hover:bg-primary-container transition-colors" type="submit">Appliquer les changements</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let currentSerieFilter = 'all';
    let totalAllCoefficient = {{ $totalCoefficient ?? 0 }};

    function attachDeleteEvents() {
        document.querySelectorAll('.delete-subject-btn').forEach((button) => {
            if (button.dataset.listenerAttached === 'true') return;
            button.dataset.listenerAttached = 'true';
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const form = this.closest('form');
                if (!form) return;
                Swal.fire({ title: `Supprimer la matière « ${this.dataset.name} » ?`, icon: 'warning', showCancelButton: true, cancelButtonText: 'Annuler', confirmButtonText: 'Oui, supprimer', confirmButtonColor: '#d33' }).then((result) => { if (result.isConfirmed) form.submit(); });
            });
        });
    }

    function openModal(id) {
        const modal = document.getElementById(id);
        const content = document.getElementById(id + 'Content');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(() => { content.classList.remove('scale-95', 'opacity-0'); content.classList.add('scale-100', 'opacity-100'); }, 10);
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        const content = document.getElementById(id + 'Content');
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = 'auto'; }, 300);
    }

    async function filterBySerie(serieId) {
        currentSerieFilter = serieId;
        document.getElementById('serieFilterSelect').value = serieId;
        const tbody = document.getElementById('subjectsTableBody');
        if (tbody) tbody.innerHTML = '';
        const indicator = document.getElementById('activeFilterIndicator');
        const activeFilterName = document.getElementById('activeFilterName');
        const filteredCoeffDisplay = document.getElementById('filteredCoefficientDisplay');

        if (String(serieId) === 'all') {
            if (indicator) indicator.classList.add('hidden');
            renderInitialRows();
            return;
        }

        if (indicator) indicator.classList.remove('hidden');
        const select = document.getElementById('serieFilterSelect');
        const selectedOption = select ? select.querySelector(`option[value="${serieId}"]`) : null;
        if (activeFilterName) activeFilterName.textContent = selectedOption ? selectedOption.textContent : 'Série';

        const response = await fetch(`{{ url('/personnel/matieres/by-serie') }}/${serieId}`, {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            credentials: 'same-origin'
        });
        const json = await response.json().catch(() => ({ data: [] }));
        const data = Array.isArray(json.data) ? json.data : [];

        if (!data.length) {
            if (tbody) tbody.innerHTML = `<tr><td colspan="100%" class="text-center text-muted">Aucune matière pour cette série.</td></tr>`;
            if (filteredCoeffDisplay) filteredCoeffDisplay.textContent = '0';
            return;
        }

        let coeffTotal = 0;
        const frag = document.createDocumentFragment();
        data.forEach((subject, index) => {
            coeffTotal += parseFloat(subject.coefficient) || 0;
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-primary/5 transition-colors group subject-row';
            tr.innerHTML = `
                <td class="px-6 py-4 text-on-surface-variant">${String(index + 1).padStart(2,'0')}</td>
                <td class="px-6 py-4"><div class="flex items-center gap-3"><div class="w-8 h-8 rounded bg-primary/10 flex items-center justify-center text-primary"><span class="material-symbols-outlined text-lg">menu_book</span></div><span class="font-medium text-on-surface subject-name">${subject.name ?? ''}</span></div></td>
                <td class="px-6 py-4 text-center"><span class="px-3 py-1 bg-surface-container-high text-primary rounded-full font-bold text-xs subject-coefficient">${subject.coefficient ?? 0}</span></td>
                <td class="px-6 py-4"><div class="flex items-center gap-2"><div class="w-2.5 h-2.5 rounded-full bg-success-green"></div><span class="text-sm font-medium text-success-green">Active</span></div></td>
                <td class="px-6 py-4"><span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-surface-container-high/40 text-on-surface subject-serie">${subject.serie_name ?? 'Série'}</span></td>
                <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-2">
                        <button class="p-2 text-primary hover:bg-primary/10 rounded-lg transition-all" onclick="editMatiere(${subject.id}, '${(subject.name ?? '').replace(/'/g, "\\'")}', ${subject.coefficient ?? 0}, ${subject.serie_id ?? 0})"><span class="material-symbols-outlined">edit</span></button>
                        <form action="{{ url('/personnel/matieres') }}/${subject.id}" method="POST" class="inline delete-subject-form">@csrf @method('DELETE')<button class="p-2 text-alert-red hover:bg-alert-red/10 rounded-lg transition-all delete-subject-btn" data-name="${subject.name ?? ''}" type="button"><span class="material-symbols-outlined">delete</span></button></form>
                    </div>
                </td>`;
            frag.appendChild(tr);
        });
        if (tbody) tbody.appendChild(frag);
        attachDeleteEvents();
        if (filteredCoeffDisplay) filteredCoeffDisplay.textContent = String(coeffTotal);
    }

    function renderInitialRows() {
        const template = document.getElementById('initialSubjectsTemplate');
        const tbody = document.getElementById('subjectsTableBody');
        if (!tbody || !template) return;
        tbody.innerHTML = template.innerHTML;
        attachDeleteEvents();
        const visibleCount = document.querySelectorAll('.subject-row').length;
        document.getElementById('visibleCount').textContent = visibleCount;
    }

    function resetAllFilters() {
        currentSerieFilter = 'all';
        document.getElementById('serieFilterSelect').value = 'all';
        document.getElementById('activeFilterIndicator').classList.add('hidden');
        renderInitialRows();
    }

    function editMatiere(id, name, coeff, serieId) {
        document.getElementById('editSubjectForm').action = `{{ url('/personnel/matieres') }}/${id}`;
        document.getElementById('editName').value = name;
        document.getElementById('editCoeff').value = coeff;
        document.getElementById('editSerie').value = String(serieId);
        openModal('editModal');
    }

    document.addEventListener('DOMContentLoaded', function() {
        renderInitialRows();
        @if(session('success'))
            Swal.fire({ icon: 'success', title: 'Succès', text: @json(session('success')), timer: 2500, showConfirmButton: false });
        @endif
        @if(session('error'))
            Swal.fire({ icon: 'error', title: 'Erreur', text: @json(session('error')), timer: 3000, showConfirmButton: false });
        @endif
    });
</script>
@endsection
