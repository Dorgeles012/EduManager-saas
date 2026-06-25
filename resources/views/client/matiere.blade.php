@extends('client.layouts.app')
@section('title', 'EduManager - Matieres')
@section('content')

@if(session('error'))
    <div class="mb-4 px-4 py-3 rounded-lg bg-error-container/20 text-error">
        {{ session('error') }}
    </div>
@endif

<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-on-surface">Gestion des Matières</h2>
        <p class="font-body-md text-body-md text-text-muted mt-1">Gérez l'ensemble des matières enseignées dans l'établissement</p>
    </div>
    <button class="flex items-center gap-2 px-6 py-2.5 bg-primary text-on-primary px-6 py-2.5 rounded-lg font-label-md text-label-md flex items-center gap-2 hover:opacity-90 active:scale-95 transition-all card-shadow" onclick="openModal('addModal')">
        <span class="material-symbols-outlined">add</span>
        Ajouter une matière
    </button>
</div>

<!-- Bento Stats Cards -->
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

<!-- Data Table Container -->
<div class="bg-surface-container-lowest rounded-xl shadow-[0_4px_24px_rgba(55,48,163,0.04)] border border-outline-variant overflow-hidden">
    <div class="px-6 py-5 border-b border-surface-subtle flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full sm:w-auto">
            <h3 class="font-headline-md text-headline-md text-on-surface whitespace-nowrap">Liste des Matières</h3>
            
            <!-- Filtre par série - Select déroulant -->
            <div class="relative w-full sm:w-56">
                <select 
                    id="serieFilterSelect" 
                    class="w-full appearance-none px-4 py-2 pr-10 rounded-lg border border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all bg-surface-container-lowest cursor-pointer"
                    onchange="filterBySerie(this.value)"
                >
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
                <span class="absolute inset-y-0 right-3 flex items-center text-text-muted pointer-events-none">
                    <span class="material-symbols-outlined text-xl">expand_more</span>
                </span>
            </div>
        </div>
    </div>
    
    <!-- Indicateur de filtre actif avec coefficient -->
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
                <span id="coefficientSerieLabel" class="hidden text-primary font-medium"></span>
            </span>
        </div>
    </div>

    <template id="initialSubjectsTemplate">
        @foreach($subjects ?? [] as $subject)
        <tr class="hover:bg-primary/5 transition-colors group subject-row" 
            data-subject-name="{{ strtolower($subject['name']) }}" 
            data-subject-serie="{{ $subject['serie_id'] ?? '' }}"
            data-subject-serie-name="{{ strtolower($subject['serie'] ?? '') }}"
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
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-surface-container-high/40 text-on-surface subject-serie">
                    {{ $subject['serie'] ?? 'N/A' }}
                </span>
            </td>
            <td class="px-6 py-4 text-right">
                <div class="flex justify-end gap-2">
                    <button class="p-2 text-primary hover:bg-primary/10 rounded-lg transition-all" onclick="editMatiere({{ $subject['id'] }}, @js($subject['name']), {{ $subject['coefficient'] }}, @js($subject['serie_id'] ?? 0))" title="Modifier">
                        <span class="material-symbols-outlined">edit</span>
                    </button>
                    <form action="{{ route('client.matiere.destroy', $subject['id']) }}" method="POST" class="inline delete-subject-form">
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
            <tbody class="divide-y divide-outline-variant/20" id="subjectsTableBody">
                {{-- Lignes injectées par renderInitialRows() (template initialSubjectsTemplate) --}}
            </tbody>
        </table>
    </div>
    
    <!-- Message de résultat de filtrage -->
    <div id="filterResult" class="px-6 py-3 text-sm text-text-muted border-t border-outline-variant/20 hidden">
        <span id="filterResultText"></span>
    </div>
    
    <div class="px-6 py-4 bg-surface-container-low/30 border-t border-outline-variant flex justify-between items-center text-sm text-text-muted">
        <span id="paginationInfo">Affichage de <span id="visibleCount">0</span> sur <span id="totalCount">{{ count($subjects ?? []) }}</span> matières</span>
        <div class="flex gap-1">
            <button class="w-8 h-8 flex items-center justify-center rounded border border-outline-variant bg-white disabled:opacity-50" disabled>
                <span class="material-symbols-outlined text-lg">chevron_left</span>
            </button>
            <button class="w-8 h-8 flex items-center justify-center rounded border border-primary bg-primary text-on-primary">1</button>
            <button class="w-8 h-8 flex items-center justify-center rounded border border-outline-variant bg-white disabled:opacity-50" disabled>
                <span class="material-symbols-outlined text-lg">chevron_right</span>
            </button>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4" id="addModal">
    <div class="absolute inset-0 modal-backdrop backdrop-blur-md bg-black/30" onclick="closeModal('addModal')"></div>
    <div class="bg-white w-full max-w-md rounded-xl shadow-2xl relative z-10 overflow-hidden transform transition-all scale-95 opacity-0 duration-300" id="addModalContent">
        <div class="p-6 border-b border-outline-variant/30 flex justify-between items-center bg-primary text-on-primary">
            <h3 class="font-headline-md text-headline-md">Ajouter une matière</h3>
            <button class="hover:bg-white/20 p-1 rounded-full transition-colors" onclick="closeModal('addModal')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form class="p-6 space-y-5" id="addSubjectForm" action="{{ route('client.matiere.store') }}" method="POST">
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

<!-- Edit Modal -->
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

<style>
    /* Animation styles for modals */
    #addModal, #editModal {
        transition: opacity 0.3s ease;
    }
    
    .modal-backdrop {
        transition: backdrop-filter 0.3s ease;
    }
    
    /* Animation pour le filtre */
    .subject-row {
        transition: all 0.3s ease;
    }
    
    .subject-row.hidden-row {
        display: none;
    }
    
    /* Style du select personnalisé */
    #serieFilterSelect {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        cursor: pointer;
        min-width: 180px;
        background-image: none;
    }
    
    #serieFilterSelect::-ms-expand {
        display: none;
    }
    
    #serieFilterSelect:focus {
        border-color: #1f108e;
        box-shadow: 0 0 0 4px rgba(31, 16, 142, 0.1);
    }
    
    /* Animation du coefficient */
    #filteredCoefficientDisplay {
        transition: all 0.3s ease;
    }
    
    .coefficient-update {
        animation: pulse 0.3s ease;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); color: #1f108e; }
        100% { transform: scale(1); }
    }
    
    /* SweetAlert custom styles */
    .swal2-popup {
        font-size: 0.95rem !important;
        padding: 1.5rem !important;
    }
    
    .swal2-title {
        font-size: 1.3rem !important;
        padding: 0.8rem 0 0.5rem 0 !important;
    }
    
    .swal2-html-container {
        font-size: 0.95rem !important;
        padding: 0.5rem 0 1rem 0 !important;
    }
    
    .swal2-confirm, .swal2-cancel {
        font-size: 0.9rem !important;
        padding: 0.6rem 1.5rem !important;
        margin: 0 0.3rem !important;
    }
    
    .swal2-timer-progress-bar {
        height: 3px !important;
    }
    
    .swal2-icon {
        font-size: 0.7rem !important;
    }
    
    .swal2-close {
        font-size: 1.2rem !important;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let currentSerieFilter = 'all';
    let totalAllCoefficient = {{ $totalCoefficient ?? 0 }};
    
    // Fonction pour attacher les événements aux boutons de suppression
    function attachDeleteEvents() {
        document.querySelectorAll('.delete-subject-btn').forEach((button) => {
            // Éviter les doublons d'événements
            if (button.dataset.listenerAttached === 'true') return;
            button.dataset.listenerAttached = 'true';
            
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const form = this.closest('form');
                if (!form) return;
                
                Swal.fire({
                    title: `Supprimer la matière « ${this.dataset.name} » ?`,
                    icon: 'warning',
                    showCancelButton: true,
                    cancelButtonText: 'Annuler',
                    confirmButtonText: 'Oui, supprimer',
                    confirmButtonColor: '#d33',
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    }
    
    function openModal(id) {
        const modal = document.getElementById(id);
        const content = document.getElementById(id + 'Content');
        
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        const content = document.getElementById(id + 'Content');
        
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }, 300);
    }

    // Filtrer par série (AJAX strict)
    async function filterBySerie(serieId) {
        currentSerieFilter = serieId;

        // Mettre à jour le select
        const select = document.getElementById('serieFilterSelect');
        if (select) {
            select.value = serieId;
        }

        // Vider IMMÉDIATEMENT le tbody pour éviter tout mélange
        const tbody = document.getElementById('subjectsTableBody');
        if (tbody) {
            tbody.innerHTML = '';
        }

        // Mettre à jour l'indicateur de filtre
        const indicator = document.getElementById('activeFilterIndicator');
        const activeFilterName = document.getElementById('activeFilterName');
        const filteredCoeffDisplay = document.getElementById('filteredCoefficientDisplay');
        const coefficientSerieLabel = document.getElementById('coefficientSerieLabel');

        if (String(serieId) === 'all') {
            // Toutes les séries => requête dédiée, pas de filtre.
            if (indicator) indicator.classList.add('hidden');
            if (coefficientSerieLabel) coefficientSerieLabel.classList.add('hidden');

            // Requête AJAX "toutes séries"
            const response = await fetch(`{{ url('/client/matiere/all') }}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });

            const json = await response.json().catch(() => ({ data: [] }));
            const data = Array.isArray(json.data) ? json.data : [];

            if (!data.length) {
                if (tbody) {
                    tbody.innerHTML = `<tr>
                        <td colspan="100%" class="text-center text-muted">
                            Aucune matière créée.
                        </td>
                    </tr>`;
                }
                if (filteredCoeffDisplay) filteredCoeffDisplay.textContent = '0';
                if (document.getElementById('totalCoefficientDisplay')) {
                    document.getElementById('totalCoefficientDisplay').textContent = '0';
                }
                return;
            }

            let coeffTotal = 0;
            const frag = document.createDocumentFragment();

            data.forEach((subject, index) => {
                coeffTotal += parseFloat(subject.coefficient) || 0;

                const tr = document.createElement('tr');
                tr.className = 'hover:bg-primary/5 transition-colors group subject-row';
                tr.dataset.subjectName = (subject.name || '').toLowerCase();
                tr.dataset.subjectSerie = String(subject.serie_id ?? '');
                tr.dataset.subjectCoefficient = subject.coefficient ?? 0;

                tr.innerHTML = `
                    <td class="px-6 py-4 text-on-surface-variant">${String(index + 1).padStart(2,'0')}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded bg-primary/10 flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-lg">menu_book</span>
                            </div>
                            <span class="font-medium text-on-surface subject-name">${subject.name ?? ''}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 bg-surface-container-high text-primary rounded-full font-bold text-xs subject-coefficient">${subject.coefficient ?? 0}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-success-green"></div>
                            <span class="text-sm font-medium text-success-green">Active</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-surface-container-high/40 text-on-surface subject-serie">
                            ${subject.serie_name ?? 'Série'}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <button class="p-2 text-primary hover:bg-primary/10 rounded-lg transition-all" onclick="editMatiere(${subject.id}, '${(subject.name ?? '').replace(/'/g, "\\'")}' , ${subject.coefficient ?? 0}, ${subject.serie_id ?? 0})" title="Modifier">
                                <span class="material-symbols-outlined">edit</span>
                            </button>
                            <form action="{{ url('/client/matiere') }}/${subject.id}" method="POST" class="inline delete-subject-form">
                                @csrf
                                @method('DELETE')
                                <button class="p-2 text-alert-red hover:bg-alert-red/10 rounded-lg transition-all delete-subject-btn" data-name="${subject.name ?? ''}" type="button" title="Supprimer">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                `;

                frag.appendChild(tr);
            });

            if (tbody) tbody.appendChild(frag);

            // Attacher les événements aux nouveaux boutons
            attachDeleteEvents();

            if (document.getElementById('totalCoefficientDisplay')) {
                document.getElementById('totalCoefficientDisplay').textContent = String(coeffTotal);
            }

            return;
        }

        if (indicator) indicator.classList.remove('hidden');

        const selectedOption = select ? select.querySelector(`option[value="${serieId}"]`) : null;
        const serieName = selectedOption ? selectedOption.textContent.replace(/\(\d+\)/, '').trim() : 'Série';
        if (activeFilterName) activeFilterName.textContent = serieName;

        // Requête AJAX strict par série
        const response = await fetch(`{{ url('/client/matiere/by-serie') }}/${serieId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        });

        const json = await response.json().catch(() => ({ data: [] }));
        const data = Array.isArray(json.data) ? json.data : [];

        // Injecter uniquement les résultats de cette série
        if (!data.length) {
            if (tbody) {
                tbody.innerHTML = `<tr>
                    <td colspan="100%" class="text-center text-muted">
                        Aucune matière créée pour cette série.
                    </td>
                </tr>`;
            }

            if (document.getElementById('filterResult')) {
                document.getElementById('filterResult').classList.add('hidden');
            }

            if (filteredCoeffDisplay) filteredCoeffDisplay.textContent = '0';

            if (document.getElementById('totalCoefficientDisplay')) {
                document.getElementById('totalCoefficientDisplay').textContent = '0';
            }
            if (coefficientSerieLabel) {
                coefficientSerieLabel.classList.remove('hidden');
                coefficientSerieLabel.textContent = `(Série ${serieName})`;
            }

            if (document.getElementById('filterResult')) {
                document.getElementById('filterResult').classList.add('hidden');
            }

            return;
        }

        let coeffTotal = 0;
        const frag = document.createDocumentFragment();
        data.forEach((subject, index) => {
            coeffTotal += parseFloat(subject.coefficient) || 0;

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-primary/5 transition-colors group subject-row';
            tr.dataset.subjectName = (subject.name || '').toLowerCase();
            tr.dataset.subjectSerie = String(subject.serie_id ?? '');
            tr.dataset.subjectCoefficient = subject.coefficient ?? 0;

            tr.innerHTML = `
                <td class="px-6 py-4 text-on-surface-variant">${String(index + 1).padStart(2,'0')}</td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded bg-primary/10 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-lg">menu_book</span>
                        </div>
                        <span class="font-medium text-on-surface subject-name">${subject.name ?? ''}</span>
                    </div>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="px-3 py-1 bg-surface-container-high text-primary rounded-full font-bold text-xs subject-coefficient">${subject.coefficient ?? 0}</span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-success-green"></div>
                        <span class="text-sm font-medium text-success-green">Active</span>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-surface-container-high/40 text-on-surface subject-serie">
                        ${subject.serie_name ?? 'Série'}
                    </span>
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-2">
                        <button class="p-2 text-primary hover:bg-primary/10 rounded-lg transition-all" onclick="editMatiere(${subject.id}, '${(subject.name ?? '').replace(/'/g, "\\'")}' , ${subject.coefficient ?? 0}, ${subject.serie_id ?? 0})" title="Modifier">
                            <span class="material-symbols-outlined">edit</span>
                        </button>
                        <form action="{{ url('/client/matiere') }}/${subject.id}" method="POST" class="inline delete-subject-form">
                            @csrf
                            @method('DELETE')
                            <button class="p-2 text-alert-red hover:bg-alert-red/10 rounded-lg transition-all delete-subject-btn" data-name="${subject.name ?? ''}" type="button" title="Supprimer">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </form>
                    </div>
                </td>
            `;

            frag.appendChild(tr);
        });

        if (tbody) tbody.appendChild(frag);

        // Attacher les événements aux nouveaux boutons
        attachDeleteEvents();

        if (filteredCoeffDisplay) {
            filteredCoeffDisplay.textContent = String(coeffTotal);
            filteredCoeffDisplay.classList.remove('coefficient-update');
            setTimeout(() => filteredCoeffDisplay.classList.add('coefficient-update'), 10);
        }
        if (document.getElementById('totalCoefficientDisplay')) {
            document.getElementById('totalCoefficientDisplay').textContent = String(coeffTotal);
        }
        if (coefficientSerieLabel) {
            coefficientSerieLabel.classList.remove('hidden');
            coefficientSerieLabel.textContent = `(Série ${serieName})`;
        }
    }
    
    function renderInitialRows() {
        // Recrée les lignes initiales depuis le HTML serveur (sans recharger DB)
        const template = document.getElementById('initialSubjectsTemplate');
        const tbody = document.getElementById('subjectsTableBody');
        if (!tbody || !template) return;
        tbody.innerHTML = template.innerHTML;
        
        // Attacher les événements aux boutons de suppression initiaux
        attachDeleteEvents();
    }
    
    function resetAllFilters() {
        // Réinitialiser le filtre série
        currentSerieFilter = 'all';
        document.getElementById('serieFilterSelect').value = 'all';
        
        // Cacher l'indicateur
        document.getElementById('activeFilterIndicator').classList.add('hidden');
        
        // Réinitialiser le coefficient
        document.getElementById('totalCoefficientDisplay').textContent = totalAllCoefficient;
        document.getElementById('coefficientSerieLabel').classList.add('hidden');
        
        // Recréer les lignes initiales
        renderInitialRows();
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser les compteurs
        const totalRows = document.querySelectorAll('.subject-row').length;
        const visibleCountSpan = document.getElementById('visibleCount');
        if (visibleCountSpan) {
            visibleCountSpan.textContent = totalRows;
        }
        
        // Gestion des formulaires
        ['addSubjectForm', 'editSubjectForm'].forEach((formId) => {
            const form = document.getElementById(formId);
            if (!form) return;
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                HTMLFormElement.prototype.submit.call(form);
            }, true);
        });

        // Attacher les événements aux boutons de suppression
        attachDeleteEvents();
        
        // Affichage success/error via SweetAlert si dispo
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Succès',
                text: @json(session('success')),
                timer: 2500,
                showConfirmButton: false,
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: @json(session('error')),
                timer: 3000,
                showConfirmButton: false,
            });
        @endif

        // Injecter les lignes initiales
        renderInitialRows();
        
        // Initialiser le coefficient total
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

    // Fermer le modal avec la touche Echap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const addModal = document.getElementById('addModal');
            const editModal = document.getElementById('editModal');
            
            if (addModal && !addModal.classList.contains('hidden')) {
                closeModal('addModal');
            }
            if (editModal && !editModal.classList.contains('hidden')) {
                closeModal('editModal');
            }
        }
    });
</script>
@endsection
