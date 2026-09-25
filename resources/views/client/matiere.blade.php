@extends('client.layouts.app')
@section('title', 'EduManager - Matieres')
@section('content')

@if(session('error'))
    <div class="mb-4 px-4 py-3 rounded-xl bg-rose-50 text-rose-700 text-xs border border-rose-100">
        {{ session('error') }}
    </div>
@endif

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Gestion des Matières</h2>
        <p class="text-sm text-gray-500 mt-1">Gérez l'ensemble des matières enseignées dans l'établissement</p>
    </div>
    <button onclick="openModal('addModal')" class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-xs font-semibold transition shadow-sm">
        <span class="material-symbols-outlined text-sm">add</span>
        Ajouter une matière
    </button>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mb-6">
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                <span class="material-symbols-outlined text-lg">book</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Total</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold text-gray-900" id="totalSubjectsCount">{{ $totalSubjects ?? 0 }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Matières disponibles</p>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                <span class="material-symbols-outlined text-lg">person_search</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Enseignants</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $assignedTeachersCount ?? 0 }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Assignés</p>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow col-span-2 lg:col-span-1">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                <span class="material-symbols-outlined text-lg">calculate</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Coefficients</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold text-gray-900" id="totalCoefficientDisplay">{{ $totalCoefficient ?? 0 }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Somme totale</p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                <span class="material-symbols-outlined text-base">menu_book</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Liste des matières</h3>
                <p class="text-[11px] text-gray-500">{{ count($subjects ?? []) }} matière(s) enregistrée(s)</p>
            </div>
        </div>

        <div class="relative w-full sm:w-64">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base pointer-events-none">filter_list</span>
            <select id="serieFilterSelect" onchange="filterBySerie(this.value)"
                    class="w-full appearance-none bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 pl-10 pr-8 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition cursor-pointer font-medium">
                <option value="all">Toutes les séries</option>
                @foreach($series ?? [] as $serie)
                    @php
                        $countForSerie = $subjects->where('serie_id', $serie->id)->count() ?? 0;
                    @endphp
                    @if($countForSerie > 0)
                        <option value="{{ $serie->id }}">{{ $serie->nom_serie }} ({{ $countForSerie }})</option>
                    @else
                        <option value="{{ $serie->id }}">{{ $serie->nom_serie }}</option>
                    @endif
                @endforeach
            </select>
            <span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 text-base pointer-events-none">expand_more</span>
        </div>
    </div>

    <div id="activeFilterIndicator" class="px-5 py-2.5 bg-indigo-50/50 border-b border-indigo-100 hidden">
        <div class="flex flex-wrap items-center justify-between gap-2 text-xs">
            <span class="flex items-center gap-2 text-gray-700">
                <span class="material-symbols-outlined text-indigo-600 text-base">filter_alt</span>
                Filtré par : <span id="activeFilterName" class="font-semibold text-indigo-700">Toutes</span>
                <button onclick="resetAllFilters()" class="ml-2 text-[11px] text-indigo-600 hover:underline font-medium">(Effacer)</button>
            </span>
            <span class="flex items-center gap-2 text-xs">
                <span class="text-gray-500">Coefficient total :</span>
                <span class="font-bold text-emerald-600" id="filteredCoefficientDisplay">0</span>
                <span id="coefficientSerieLabel" class="hidden text-indigo-600 font-medium text-[11px]"></span>
            </span>
        </div>
    </div>

    <template id="initialSubjectsTemplate">
        @foreach($subjects ?? [] as $subject)
        <tr class="hover:bg-gray-50/50 transition-colors subject-row"
            data-subject-name="{{ strtolower($subject['name']) }}"
            data-subject-serie="{{ $subject['serie_id'] ?? '' }}"
            data-subject-serie-name="{{ strtolower($subject['serie'] ?? '') }}"
            data-subject-coefficient="{{ $subject['coefficient'] ?? 0 }}">
            <td class="px-4 py-3 text-xs text-gray-500 font-semibold">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
            <td class="px-4 py-3">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 text-[11px] font-bold">
                        {{ strtoupper(substr($subject['name'], 0, 1)) }}
                    </div>
                    <span class="text-xs font-semibold text-gray-900 subject-name">{{ $subject['name'] }}</span>
                </div>
            </td>
            <td class="px-4 py-3 text-center">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 font-bold text-xs subject-coefficient">{{ $subject['coefficient'] }}</span>
            </td>
            <td class="px-4 py-3">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Active
                </span>
            </td>
            <td class="px-4 py-3">
                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-100 text-[10px] font-bold text-gray-700 uppercase subject-serie">
                    {{ $subject['serie'] ?? 'N/A' }}
                </span>
            </td>
            <td class="px-4 py-3">
                <div class="flex items-center justify-end gap-1.5">
                    <button onclick="editMatiere({{ $subject['id'] }}, @js($subject['name']), {{ $subject['coefficient'] }}, @js($subject['serie_id'] ?? 0))"
                            class="w-8 h-8 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-600 flex items-center justify-center transition"
                            title="Modifier">
                        <span class="material-symbols-outlined text-base">edit</span>
                    </button>
                    <form action="{{ route('client.matiere.destroy', $subject['id']) }}" method="POST" class="inline delete-subject-form">
                        @csrf @method('DELETE')
                        <button type="button"
                                data-name="{{ $subject['name'] }}"
                                class="delete-subject-btn w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition"
                                title="Supprimer">
                            <span class="material-symbols-outlined text-base">delete</span>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @endforeach
    </template>

    <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left" id="subjectsTable">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">N°</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Nom de la matière</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider text-center">Coeff.</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Série</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100" id="subjectsTableBody">
            </tbody>
        </table>
    </div>

    <div class="md:hidden divide-y divide-gray-100" id="subjectsMobileBody">
    </div>

    <div id="filterResult" class="px-5 py-3 text-xs text-gray-500 border-t border-gray-100 hidden">
        <span id="filterResultText"></span>
    </div>

    <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between">
        <span class="text-[11px] text-gray-500" id="paginationInfo">
            Affichage de <span id="visibleCount">0</span> sur <span id="totalCount">{{ count($subjects ?? []) }}</span> matière(s)
        </span>
        <div class="flex items-center gap-1.5 text-xs">
            <button class="px-3 py-1 bg-indigo-600 text-white rounded-lg text-[11px] font-semibold">1</button>
        </div>
    </div>
</div>

<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="addModal">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('addModal')"></div>
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0 relative z-10 max-h-[90vh] flex flex-col" id="addModalContent">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center flex-shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <span class="material-symbols-outlined text-base">add_circle</span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Ajouter une matière</h3>
                    <p class="text-[11px] text-gray-500">Créez une nouvelle matière</p>
                </div>
            </div>
            <button onclick="closeModal('addModal')" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center transition">
                <span class="material-symbols-outlined text-gray-500">close</span>
            </button>
        </div>

        <form class="p-5 space-y-4 overflow-y-auto flex-1" id="addSubjectForm" action="{{ route('client.matiere.store') }}" method="POST">
            @csrf
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Nom de la matière</label>
                <input type="text" name="nom" required placeholder="Ex: Informatique"
                       class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Coefficient</label>
                <input type="number" name="coefficient" min="1" max="10" required placeholder="1-10"
                       class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Série <span class="text-rose-500">*</span></label>
                <select name="serie" required
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                    <option value="">Sélectionner une série</option>
                    @foreach($series ?? [] as $s)
                        <option value="{{ $s->id }}">{{ $s->nom_serie }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                <button type="button" onclick="closeModal('addModal')"
                        class="px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    Annuler
                </button>
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-xs font-semibold transition shadow-sm">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="editModal">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('editModal')"></div>
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0 relative z-10 max-h-[90vh] flex flex-col" id="editModalContent">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center flex-shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                    <span class="material-symbols-outlined text-base">edit</span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Modifier la matière</h3>
                    <p class="text-[11px] text-gray-500">Mise à jour des informations</p>
                </div>
            </div>
            <button onclick="closeModal('editModal')" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center transition">
                <span class="material-symbols-outlined text-gray-500">close</span>
            </button>
        </div>

        <form class="p-5 space-y-4 overflow-y-auto flex-1" id="editSubjectForm" method="POST">
            @csrf @method('PUT')
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Nom de la matière</label>
                <input type="text" id="editName" name="nom" required
                       class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition">
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Coefficient</label>
                <input type="number" id="editCoeff" name="coefficient" min="1" max="10" required
                       class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition">
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Série <span class="text-rose-500">*</span></label>
                <select id="editSerie" name="serie" required
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition">
                    <option value="">Sélectionner une série</option>
                    @foreach($series ?? [] as $s)
                        <option value="{{ $s->id }}">{{ $s->nom_serie }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                <button type="button" onclick="closeModal('editModal')"
                        class="px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    Annuler
                </button>
                <button type="submit"
                        class="bg-amber-600 hover:bg-amber-700 text-white px-5 py-2 rounded-lg text-xs font-semibold transition shadow-sm">
                    Appliquer
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .subject-row { transition: all 0.3s ease; }
    .subject-row.hidden-row { display: none; }
    #serieFilterSelect { appearance: none; -webkit-appearance: none; -moz-appearance: none; }
    #serieFilterSelect::-ms-expand { display: none; }
    #filteredCoefficientDisplay { transition: all 0.3s ease; }
    .coefficient-update { animation: pulse 0.3s ease; }
    @keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.15); } 100% { transform: scale(1); } }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let currentSerieFilter = 'all';
    let totalAllCoefficient = {{ $totalCoefficient ?? 0 }};

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

    function attachDeleteEvents() {
        document.querySelectorAll('.delete-subject-btn').forEach((button) => {
            if (button.dataset.listenerAttached === 'true') return;
            button.dataset.listenerAttached = 'true';

            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const form = this.closest('form');
                if (!form) return;

                Swal.fire({
                    ...swalConfig,
                    title: 'Supprimer cette matière ?',
                    html: `La matière <strong class="text-rose-600">"${this.dataset.name}"</strong> sera définitivement supprimée.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Oui, supprimer',
                    cancelButtonText: 'Annuler',
                    iconColor: '#e11d48'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    }

    function openModal(id) {
        const modal = document.getElementById(id);
        const content = document.getElementById(id + 'Content');
        modal.classList.remove('hidden'); modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        setTimeout(() => { content.classList.remove('scale-95', 'opacity-0'); content.classList.add('scale-100', 'opacity-100'); }, 10);
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        const content = document.getElementById(id + 'Content');
        content.classList.remove('scale-100', 'opacity-100'); content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => { modal.classList.remove('flex'); modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
    }

    function buildRow(subject, index) {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50/50 transition-colors subject-row';
        tr.dataset.subjectName = (subject.name || '').toLowerCase();
        tr.dataset.subjectSerie = String(subject.serie_id ?? '');
        tr.dataset.subjectCoefficient = subject.coefficient ?? 0;
        tr.innerHTML = `
            <td class="px-4 py-3 text-xs text-gray-500 font-semibold">${String(index + 1).padStart(2,'0')}</td>
            <td class="px-4 py-3">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 text-[11px] font-bold">${(subject.name || '').substring(0, 1).toUpperCase()}</div>
                    <span class="text-xs font-semibold text-gray-900 subject-name">${subject.name ?? ''}</span>
                </div>
            </td>
            <td class="px-4 py-3 text-center">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 font-bold text-xs subject-coefficient">${subject.coefficient ?? 0}</span>
            </td>
            <td class="px-4 py-3">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Active
                </span>
            </td>
            <td class="px-4 py-3">
                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-100 text-[10px] font-bold text-gray-700 uppercase subject-serie">${subject.serie_name ?? 'N/A'}</span>
            </td>
            <td class="px-4 py-3">
                <div class="flex items-center justify-end gap-1.5">
                    <button onclick="editMatiere(${subject.id}, '${(subject.name ?? '').replace(/'/g, "\\'")}', ${subject.coefficient ?? 0}, ${subject.serie_id ?? 0})"
                            class="w-8 h-8 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-600 flex items-center justify-center transition"
                            title="Modifier">
                        <span class="material-symbols-outlined text-base">edit</span>
                    </button>
                    <form action="{{ url('/client/matiere') }}/${subject.id}" method="POST" class="inline delete-subject-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" data-name="${subject.name ?? ''}"
                                class="delete-subject-btn w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition"
                                title="Supprimer">
                            <span class="material-symbols-outlined text-base">delete</span>
                        </button>
                    </form>
                </div>
            </td>
        `;
        return tr;
    }

    async function filterBySerie(serieId) {
        currentSerieFilter = serieId;
        const select = document.getElementById('serieFilterSelect');
        if (select) select.value = serieId;

        const tbody = document.getElementById('subjectsTableBody');
        if (tbody) tbody.innerHTML = '';

        const indicator = document.getElementById('activeFilterIndicator');
        const activeFilterName = document.getElementById('activeFilterName');
        const filteredCoeffDisplay = document.getElementById('filteredCoefficientDisplay');
        const coefficientSerieLabel = document.getElementById('coefficientSerieLabel');

        if (String(serieId) === 'all') {
            if (indicator) indicator.classList.add('hidden');
            if (coefficientSerieLabel) coefficientSerieLabel.classList.add('hidden');

            const response = await fetch(`{{ url('/client/matiere/all') }}`, {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                credentials: 'same-origin'
            });
            const json = await response.json().catch(() => ({ data: [] }));
            const data = Array.isArray(json.data) ? json.data : [];

            if (!data.length) {
                if (tbody) tbody.innerHTML = `<tr><td colspan="100%" class="py-16 text-center"><div class="flex flex-col items-center"><div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center mb-3"><span class="material-symbols-outlined text-2xl text-gray-300">book</span></div><p class="text-sm font-semibold text-gray-700">Aucune matière</p><p class="text-xs text-gray-400 mt-1">Commencez par en créer une.</p></div></td></tr>`;
                if (filteredCoeffDisplay) filteredCoeffDisplay.textContent = '0';
                if (document.getElementById('totalCoefficientDisplay')) document.getElementById('totalCoefficientDisplay').textContent = '0';
                return;
            }

            let coeffTotal = 0;
            const frag = document.createDocumentFragment();
            data.forEach((subject, index) => {
                coeffTotal += parseFloat(subject.coefficient) || 0;
                frag.appendChild(buildRow(subject, index));
            });
            if (tbody) tbody.appendChild(frag);
            attachDeleteEvents();

            if (document.getElementById('totalCoefficientDisplay')) document.getElementById('totalCoefficientDisplay').textContent = String(coeffTotal);
            return;
        }

        if (indicator) indicator.classList.remove('hidden');
        const selectedOption = select ? select.querySelector(`option[value="${serieId}"]`) : null;
        const serieName = selectedOption ? selectedOption.textContent.replace(/\(\d+\)/, '').trim() : 'Série';
        if (activeFilterName) activeFilterName.textContent = serieName;

        const response = await fetch(`{{ url('/client/matiere/by-serie') }}/${serieId}`, {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            credentials: 'same-origin'
        });
        const json = await response.json().catch(() => ({ data: [] }));
        const data = Array.isArray(json.data) ? json.data : [];

        if (!data.length) {
            if (tbody) tbody.innerHTML = `<tr><td colspan="100%" class="py-16 text-center"><div class="flex flex-col items-center"><div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center mb-3"><span class="material-symbols-outlined text-2xl text-gray-300">book</span></div><p class="text-sm font-semibold text-gray-700">Aucune matière</p><p class="text-xs text-gray-400 mt-1">Aucune matière pour cette série.</p></div></td></tr>`;
            if (filteredCoeffDisplay) filteredCoeffDisplay.textContent = '0';
            if (document.getElementById('totalCoefficientDisplay')) document.getElementById('totalCoefficientDisplay').textContent = '0';
            if (coefficientSerieLabel) {
                coefficientSerieLabel.classList.remove('hidden');
                coefficientSerieLabel.textContent = `(Série ${serieName})`;
            }
            return;
        }

        let coeffTotal = 0;
        const frag = document.createDocumentFragment();
        data.forEach((subject, index) => {
            coeffTotal += parseFloat(subject.coefficient) || 0;
            frag.appendChild(buildRow(subject, index));
        });
        if (tbody) tbody.appendChild(frag);
        attachDeleteEvents();

        if (filteredCoeffDisplay) {
            filteredCoeffDisplay.textContent = String(coeffTotal);
            filteredCoeffDisplay.classList.remove('coefficient-update');
            setTimeout(() => filteredCoeffDisplay.classList.add('coefficient-update'), 10);
        }
        if (document.getElementById('totalCoefficientDisplay')) document.getElementById('totalCoefficientDisplay').textContent = String(coeffTotal);
        if (coefficientSerieLabel) {
            coefficientSerieLabel.classList.remove('hidden');
            coefficientSerieLabel.textContent = `(Série ${serieName})`;
        }
    }

    function renderInitialRows() {
        const template = document.getElementById('initialSubjectsTemplate');
        const tbody = document.getElementById('subjectsTableBody');
        if (!tbody || !template) return;
        tbody.innerHTML = template.innerHTML;
        attachDeleteEvents();

        const mobileBody = document.getElementById('subjectsMobileBody');
        if (mobileBody) {
            mobileBody.innerHTML = '';
            document.querySelectorAll('#subjectsTableBody .subject-row').forEach(row => {
                const name = row.querySelector('.subject-name')?.textContent || '';
                const coeff = row.querySelector('.subject-coefficient')?.textContent || '';
                const serie = row.querySelector('.subject-serie')?.textContent || '—';
                const editBtn = row.querySelector('button[onclick*="editMatiere"]');
                const deleteBtn = row.querySelector('.delete-subject-btn');
                const onclickAttr = editBtn?.getAttribute('onclick') || '';
                const nameAttr = deleteBtn?.getAttribute('data-name') || '';
                const formAction = deleteBtn?.closest('form')?.getAttribute('action') || '';

                const card = document.createElement('div');
                card.className = 'p-4 space-y-3 subject-row';
                card.innerHTML = `
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-sm font-bold flex-shrink-0">${name.substring(0,1).toUpperCase()}</div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">${name}</p>
                                <p class="text-[11px] text-gray-500 truncate">Coeff. ${coeff}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-100 text-[10px] font-bold text-gray-700 uppercase flex-shrink-0">${serie}</span>
                    </div>
                    <div class="flex items-center gap-2 pt-1">
                        <button onclick="${onclickAttr.replace(/"/g, '&quot;')}" class="flex-1 h-9 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold flex items-center justify-center gap-1.5 transition">
                            <span class="material-symbols-outlined text-sm">edit</span>Modifier
                        </button>
                        <form action="${formAction}" method="POST" class="delete-subject-form">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="button" data-name="${nameAttr}" class="delete-subject-btn w-9 h-9 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition">
                                <span class="material-symbols-outlined text-base">delete</span>
                            </button>
                        </form>
                    </div>
                `;
                mobileBody.appendChild(card);
            });
            attachDeleteEvents();
        }

        const visibleCountSpan = document.getElementById('visibleCount');
        if (visibleCountSpan) visibleCountSpan.textContent = document.querySelectorAll('#subjectsTableBody .subject-row').length;
    }

    function resetAllFilters() {
        currentSerieFilter = 'all';
        document.getElementById('serieFilterSelect').value = 'all';
        document.getElementById('activeFilterIndicator').classList.add('hidden');
        document.getElementById('totalCoefficientDisplay').textContent = totalAllCoefficient;
        document.getElementById('coefficientSerieLabel').classList.add('hidden');
        renderInitialRows();
    }

    document.addEventListener('DOMContentLoaded', function() {
        ['addSubjectForm', 'editSubjectForm'].forEach((formId) => {
            const form = document.getElementById(formId);
            if (!form) return;
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                HTMLFormElement.prototype.submit.call(form);
            }, true);
        });

        attachDeleteEvents();

        @if(session('success'))
            Swal.fire({ ...swalConfig, icon: 'success', title: 'Succès', text: @json(session('success')), timer: 2500, showConfirmButton: false });
        @endif
        @if(session('error'))
            Swal.fire({ ...swalConfig, icon: 'error', title: 'Erreur', text: @json(session('error')), confirmButtonText: 'OK' });
        @endif

        renderInitialRows();

        const totalCoefficientDisplay = document.getElementById('totalCoefficientDisplay');
        if (totalCoefficientDisplay) totalCoefficientDisplay.textContent = totalAllCoefficient;
    });

    function editMatiere(id, name, coeff, serieId) {
        document.getElementById('editSubjectForm').action = `{{ url('/client/matiere') }}/${id}`;
        document.getElementById('editName').value = name;
        document.getElementById('editCoeff').value = coeff;
        document.getElementById('editSerie').value = String(serieId);
        openModal('editModal');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            ['addModal', 'editModal'].forEach(id => {
                const modal = document.getElementById(id);
                if (modal && modal.classList.contains('flex')) closeModal(id);
            });
        }
    });
</script>
@endsection