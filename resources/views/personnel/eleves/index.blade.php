@extends('personnel.layouts.app')
@section('title','EduManager - Eleves')
@section('content')
<div class="flex justify-between items-start mb-6">
    <div>
        <h2 class="font-headline-md text-headline-md text-primary mb-0.5">Gestion des Élèves</h2>
        <p class="text-body-sm text-text-muted text-sm">Gérez l'ensemble des élèves inscrits dans votre établissement</p>
    </div>
    <button class="flex items-center gap-1.5 px-4 py-1.5 bg-primary text-white rounded-lg text-sm hover:opacity-90 active:scale-95 transition-all card-shadow" onclick="openModal('modal-standard')" type="button">
        <span class="material-symbols-outlined text-base">person_add</span>Nouvel élève
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div class="glass-card p-4 rounded-xl flex items-center gap-4 shadow-[4px_4px_12px_rgba(55,48,163,0.04)]">
        <div class="w-11 h-11 rounded-full bg-primary-fixed flex items-center justify-center text-primary"><span class="material-symbols-outlined text-2xl">school</span></div>
        <div><h3 class="text-xs text-text-muted">Élèves inscrits</h3><p class="text-headline-lg font-headline-lg text-on-surface">{{ $totalStudents ?? 0 }}</p></div>
    </div>
    <div class="glass-card p-4 rounded-xl flex items-center gap-4 shadow-[4px_4px_12px_rgba(55,48,163,0.04)]">
        <div class="w-11 h-11 rounded-full bg-secondary-container flex items-center justify-center text-secondary"><span class="material-symbols-outlined text-2xl">meeting_room</span></div>
        <div><h3 class="text-xs text-text-muted">Classes actives</h3><p class="text-headline-lg font-headline-lg text-on-surface">{{ $activeClasses ?? 0 }}</p></div>
    </div>
</div>

<div class="glass-card rounded-xl overflow-hidden shadow-[0_4px_12px_rgba(55,48,163,0.04)]">
    <div class="px-4 py-2.5 border-b border-surface-subtle bg-surface-container-low flex justify-between items-center">
        <h4 class="font-headline-sm text-headline-sm text-primary text-base">Liste des élèves</h4>
    </div>
    <form method="GET" action="{{ route('personnel.eleves.index') }}" id="search-form" class="px-4 py-3 border-b border-surface-subtle grid grid-cols-1 md:grid-cols-5 gap-2 bg-white">
        <input type="text" name="search" id="search-input" value="{{ request('search') }}" class="rounded-lg border-outline-variant text-sm px-3 py-1.5" placeholder="Rechercher nom ou matricule" autocomplete="off">
        <select name="niveau_id" id="filter-niveau" class="rounded-lg border-outline-variant text-sm px-3 py-1.5">
            <option value="">Tous les niveaux</option>
            @foreach($levels ?? [] as $level)<option value="{{ $level['id'] }}" @selected((string)request('niveau_id')===(string)$level['id'])>{{ $level['name'] }}</option>@endforeach
        </select>
        <select name="id_serie" id="filter-serie" class="rounded-lg border-outline-variant text-sm px-3 py-1.5">
            <option value="">Toutes les séries</option>
            @foreach($series ?? [] as $serie)<option value="{{ $serie->id }}" @selected((string)request('id_serie')===(string)$serie->id)>{{ $serie->nom_serie }}</option>@endforeach
        </select>
        <select name="classe_id" id="filter-classe" class="rounded-lg border-outline-variant text-sm px-3 py-1.5">
            <option value="">Toutes les classes</option>
            @foreach($classes ?? [] as $classe)<option value="{{ $classe['id'] }}" @selected((string)request('classe_id')===(string)$classe['id'])>{{ $classe['name'] }}</option>@endforeach
        </select>
        <div class="flex gap-2">
            <button class="flex-1 bg-primary text-white rounded-lg px-4 py-1.5 text-sm hover:opacity-90 transition" type="submit">Filtrer</button>
            <a href="{{ route('personnel.eleves.index') }}" id="btn-reset-filters" class="flex items-center justify-center px-3 py-1.5 border border-outline-variant text-text-muted rounded-lg text-sm hover:bg-surface-container transition" title="Réinitialiser">
                <span class="material-symbols-outlined text-base">restart_alt</span>
            </a>
        </div>
    </form>

    <div id="students-table-container">
        @if(($students ?? collect())->isEmpty())
        <div class="min-h-[160px] flex flex-col items-center justify-center text-center p-6">
            <div class="w-20 h-20 bg-surface-container rounded-full flex items-center justify-center mb-3"><span class="material-symbols-outlined text-primary text-4xl">school</span></div>
            <h3 class="font-headline-sm text-headline-sm text-on-surface mb-1 text-base">Aucun élève enregistré</h3>
            @if(request('search'))
            <p class="text-sm text-text-muted">Aucun résultat pour « {{ request('search') }} »</p>
            @endif
        </div>
        @else
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left text-sm border-separate border-spacing-y-1">
                <thead class="bg-surface-container-low text-xs uppercase tracking-wider text-text-muted">
                    <tr><th class="px-2 py-2.5 font-semibold">N°</th><th class="px-3 py-2.5 font-semibold min-w-[160px]">Nom &amp; Prénoms</th><th class="px-2 py-2.5 font-semibold">Matricule</th><th class="px-2 py-2.5 font-semibold">Sexe</th><th class="px-2 py-2.5 font-semibold">Classe</th><th class="px-2 py-2.5 font-semibold">Niveau</th><th class="px-2 py-2.5 font-semibold min-w-[80px]">Série</th><th class="px-2 py-2.5 font-semibold min-w-[90px]">Date naiss.</th><th class="px-2 py-2.5 font-semibold text-right">Actions</th></tr>
                </thead>
                <tbody class="divide-y divide-surface-subtle">
                    @foreach($students as $student)
                    @php
                        $sexe = strtolower(trim($student['sexe'] ?? ''));
                        $isMale = in_array($sexe, ['m','masculin','male','homme','h']);
                        $isFemale = in_array($sexe, ['f','féminin','feminin','female','femme']);
                        $badge = $isMale ? 'bg-blue-100 text-blue-700' : ($isFemale ? 'bg-pink-100 text-pink-700' : 'bg-gray-100 text-gray-700');
                        $display = $isMale ? 'Masculin' : ($isFemale ? 'Féminin' : 'N/A');
                    @endphp
                    <tr class="hover:bg-surface-container-low transition-colors rounded-lg shadow-sm bg-white student-row" 
                        data-student-id="{{ $student['id'] }}"
                        data-name="{{ strtolower(($student['lastname'] ?? '') . ' ' . ($student['firstname'] ?? '')) }}"
                        data-matricule="{{ strtolower($student['matricule'] ?? '') }}">
                        <td class="px-2 py-2.5 align-middle">{{ $loop->iteration }}</td>
                        <td class="px-3 py-2.5 min-w-[160px] align-middle"><span class="font-medium whitespace-nowrap text-sm">{{ $student['lastname'] }} {{ $student['firstname'] }}</span></td>
                        <td class="px-2 py-2.5 text-text-muted align-middle text-sm">{{ $student['matricule'] ?? 'N/A' }}</td>
                        <td class="px-2 py-2.5 align-middle"><span class="px-2 py-1 rounded-full text-xs font-medium {{ $badge }}">{{ $display }}</span></td>
                        <td class="px-2 py-2.5 text-text-muted align-middle text-sm">{{ $student['classe'] ?? $student['class'] ?? 'N/A' }}</td>
                        <td class="px-2 py-2.5 align-middle"><span class="px-2 py-1 rounded-full text-xs font-medium bg-secondary-container/20 text-on-secondary-container">{{ $student['level'] }}</span></td>
                        <td class="px-2 py-2.5 text-text-muted align-middle min-w-[80px] text-sm">{{ $student['serie'] ?? '—' }}</td>
                        <td class="px-2 py-2.5 text-text-muted align-middle min-w-[90px] text-sm">{{ $student['birthdate'] }}</td>
                        <td class="px-2 py-2.5 text-right align-middle">
                            <div class="flex justify-end items-center gap-0.5">
                                <button type="button" class="btn-view inline-flex items-center justify-center p-1 text-primary hover:bg-primary-fixed rounded transition-colors leading-none" data-student='@json($student)' title="Voir">
                                    <span class="material-symbols-outlined text-base">visibility</span>
                                </button>
                                <button type="button" class="btn-edit inline-flex items-center justify-center p-1 text-warning-amber hover:bg-warning-amber/10 rounded transition-colors leading-none" data-student='@json($student)' title="Modifier">
                                    <span class="material-symbols-outlined text-base">edit</span>
                                </button>
                                <form action="{{ route('personnel.eleves.destroy', $student['id']) }}" method="POST" class="inline-flex items-center leading-none m-0 p-0 delete-student-form">
                                    @csrf @method('DELETE')
                                    <button type="button" class="delete-student-btn inline-flex items-center justify-center p-1 text-alert-red hover:bg-error-container/20 rounded transition-colors leading-none" data-name="{{ $student['firstname'] }} {{ $student['lastname'] }}" title="Supprimer">
                                        <span class="material-symbols-outlined text-base">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-2.5 border-t border-surface-subtle bg-surface-container-low/30 flex items-center justify-between text-sm">
            <span class="text-text-muted text-xs">Affichage de {{ $students->firstItem() ?? 0 }} à {{ $students->lastItem() ?? 0 }} sur {{ $students->total() ?? 0 }} élèves</span>
            <div class="flex gap-1 text-sm">{{ $students->appends(request()->query())->links() ?? '' }}</div>
        </div>
        @endif
    </div>
</div>
@include('personnel.partials.student-modals')
@endsection

@push('styles')
<style>
.material-symbols-outlined{font-variation-settings:'FILL'0,'wght'400,'GRAD'0,'opsz'24;font-size:20px}
.glass-card{background:rgba(255,255,255,.8);backdrop-filter:blur(12px);border:1px solid rgba(226,232,240,1)}
.modal-overlay{transition:backdrop-filter .3s ease}
#modal-standard,#modal-view,#modal-edit{transition:opacity .3s ease}
table th,table td{padding-top:0.5rem!important;padding-bottom:0.5rem!important}
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@php
    $availableClassesForJs = collect($classes ?? [])->values();
    $availableSeriesForJs = ($series ?? collect())->map(fn($s) => [
        'id' => $s->id,
        'class_ids' => $s->classes->pluck('id')->values(),
        'nom_serie' => $s->nom_serie
    ])->values();
@endphp
<script>
const availableClasses = @json($availableClassesForJs);
const availableSeries  = @json($availableSeriesForJs);

function populateClasses(l, c, s, w, sc = '', ss = '') {
    const id  = document.getElementById(l)?.value;
    const sel = document.getElementById(c);
    if (!sel) return;
    const m = availableClasses.filter(cls => String(cls.level_id) === String(id));
    sel.innerHTML = '<option value="">Sélectionner une classe</option>' +
        m.map(cls => `<option value="${cls.id}">${cls.name}</option>`).join('');
    sel.disabled = !id || m.length === 0;
    sel.value = m.some(cls => String(cls.id) === String(sc)) ? String(sc) : '';
    populateSeries(c, s, w, ss);
}

function populateSeries(c, s, w, id = '') {
    const classId = document.getElementById(c)?.value;
    const sel = document.getElementById(s);
    const wr  = document.getElementById(w);
    if (!sel || !wr) return;
    const m = availableSeries.filter(serie => (serie.class_ids ?? []).map(String).includes(String(classId)));
    sel.innerHTML = `<option value="">${m.length ? 'Sélectionner une série' : 'Aucune série pour cette classe'}</option>` +
        m.map(serie => `<option value="${serie.id}">${serie.nom_serie}</option>`).join('');
    wr.classList.remove('hidden');
    sel.disabled = !classId || m.length === 0;
    sel.value = m.some(serie => String(serie.id) === String(id)) ? String(id) : '';
}

function previewPhoto(i, p) {
    const pr = document.getElementById(p);
    if (!pr) return;
    if (i.files && i.files[0]) {
        const r = new FileReader();
        r.onload = e => {
            pr.innerHTML = `<img src="${e.target.result}" alt="Photo" class="w-full h-full object-cover object-center">`;
        };
        r.readAsDataURL(i.files[0]);
    } else {
        pr.innerHTML = '<span class="material-symbols-outlined text-2xl text-text-muted">photo_camera</span>';
    }
}

function openModal(id) {
    const m = document.getElementById(id);
    const c = document.getElementById(id + '-content');
    if (!m || !c) return;
    m.classList.remove('hidden');
    m.classList.add('flex');
    document.body.style.overflow = 'hidden';
    setTimeout(() => {
        c.classList.remove('scale-95', 'opacity-0');
        c.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closeModal(id) {
    const m = document.getElementById(id);
    const c = document.getElementById(id + '-content');
    if (!m || !c) return;
    c.classList.remove('scale-100', 'opacity-100');
    c.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        m.classList.remove('flex');
        m.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }, 300);
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        ['modal-standard', 'modal-view', 'modal-edit'].forEach(id => {
            const m = document.getElementById(id);
            if (m && !m.classList.contains('hidden')) closeModal(id);
        });
    }
});

function openViewModal(s) {
    const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val ?? '-'; };
    const setHTML = (id, val) => { const el = document.getElementById(id); if (el) el.innerHTML = val; };

    set('viewStudentFullName', ((s.firstname ?? '') + ' ' + (s.lastname ?? '')).trim() || '-');
    set('viewStudentMatricule',    s.matricule);
    set('viewStudentBirthdate',    s.birthdate);
    set('viewStudentBirthplace',   s.birthplace);
    set('viewStudentClasse',       s.classe ?? s.class);
    set('viewStudentNiveau',       s.level);
    set('viewStudentSerie',        s.serie ?? '—');
    set('viewStudentNationalite',  s.nationalite);
    set('viewStudentInterne',      s.interne ? 'Oui' : 'Non');
    set('viewStudentAffecte',      s.affecte ? 'Oui' : 'Non');

    const sexe = (s.sexe ?? '').toString().trim().toLowerCase();
    const sexeLabel = ['m','masculin','male','homme','h'].includes(sexe) ? 'Masculin'
                    : ['f','féminin','feminin','female','femme'].includes(sexe) ? 'Féminin'
                    : 'Non renseigné';
    set('viewStudentSexeDetail', sexeLabel);

    set('viewStudentParentLastname',  s.parent_lastname);
    set('viewStudentParentFirstname', s.parent_firstname);
    set('viewStudentParentPhone',     s.parent_phone);
    set('viewStudentParentEmail',     s.parent_email);

    if (s.has_account) {
        set('viewStudentAccountEmail', s.eleve_email);
        setHTML('viewStudentAccountPassword', s.eleve_must_change_pwd
            ? '<span class="font-mono bg-primary-fixed/30 px-2 py-0.5 rounded text-primary">12345678</span> <span class="text-text-muted text-[12px]">(par défaut)</span>'
            : '<span class="text-text-muted">•••••••• <span class="text-[12px]">(modifié par l\'élève)</span></span>');
    } else {
        set('viewStudentAccountEmail', 'Aucun compte');
        set('viewStudentAccountPassword', '-');
    }

    openModal('modal-view');
}

function openEditModal(s) {
    const set = (id, val) => { const el = document.getElementById(id); if (el) el.value = val ?? ''; };

    set('editEleveId',         s.id);
    set('editLastname',        s.lastname);
    set('editFirstname',       s.firstname);
    set('editMatricule',       s.matricule);
    set('editBirthdate',       s.birthdate_raw);
    set('editBirthPlace',      s.birthplace);
    set('editParentLastname',  s.parent_lastname);
    set('editParentFirstname', s.parent_firstname);
    set('editParentPhone',     s.parent_phone);
    set('editParentEmail',     s.parent_email);

    const sexeVal = (s.sexe ?? '').toString().trim().toLowerCase();
    const sexeToSet = ['m','masculin','male','homme','h'].includes(sexeVal) ? 'Masculin'
                    : ['f','féminin','feminin','female','femme'].includes(sexeVal) ? 'Féminin'
                    : '';
    document.querySelectorAll('#form-edit input[name="sexe"]').forEach(r => {
        r.checked = (r.value === sexeToSet);
    });

    document.getElementById('editLevel').value = s.level_id ?? '';
    populateClasses('editLevel', 'editClasse', 'editSerie', 'editSerieWrapper', s.class_id ?? '', s.serie_id ?? '');

    set('editInterne', s.interne ? '1' : '0');
    set('editAffecte', s.affecte ? '1' : '0');

    const form = document.getElementById('form-edit');
    if (form) form.action = `/personnel/eleves/${s.id}`;

    openModal('modal-edit');
}

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search-input');
    const searchForm  = document.getElementById('search-form');
    const btnReset    = document.getElementById('btn-reset-filters');

    let searchAbortController = null;
    let searchDebounceTimer   = null;

    // Filtrage instantané côté client (latence 0ms)
    function filterCurrentRows(query) {
        const tableBody = document.querySelector('#students-table-container tbody');
        if (!tableBody) return;
        const rows = tableBody.querySelectorAll('.student-row');
        if (!rows.length) return;

        const q = (query || '').toLowerCase().trim();
        const terms = q.split(/\s+/).filter(Boolean);

        if (terms.length === 0) {
            rows.forEach(row => { row.style.display = ''; });
            const emptyRow = document.getElementById('client-empty-search');
            if (emptyRow) emptyRow.remove();
            return;
        }

        let visibleCount = 0;
        rows.forEach(row => {
            const name = (row.dataset.name || '').toLowerCase();
            const mat  = (row.dataset.matricule || '').toLowerCase();
            const fullText = name + ' ' + mat;
            const matches = terms.every(term => fullText.includes(term));
            if (matches) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        let emptyRow = document.getElementById('client-empty-search');
        if (visibleCount === 0) {
            if (!emptyRow) {
                emptyRow = document.createElement('tr');
                emptyRow.id = 'client-empty-search';
                emptyRow.innerHTML = `<td colspan="9" class="px-4 py-8 text-center text-text-muted text-sm">Recherche en cours...</td>`;
                tableBody.appendChild(emptyRow);
            }
        } else if (emptyRow) {
            emptyRow.remove();
        }
    }

    // Recherche serveur asynchrone (AJAX) sans rechargement de page
    function fetchResults(customUrl = null) {
        if (searchAbortController) {
            searchAbortController.abort();
        }
        searchAbortController = new AbortController();

        let targetUrl = customUrl;
        if (!targetUrl && searchForm) {
            const formData = new FormData(searchForm);
            const params = new URLSearchParams(formData);
            targetUrl = `${searchForm.action}?${params.toString()}`;
        }

        if (!targetUrl) return;

        window.history.replaceState({}, '', targetUrl);

        fetch(targetUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            signal: searchAbortController.signal
        })
        .then(response => {
            if (!response.ok) throw new Error('Erreur réseau');
            return response.text();
        })
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContainer = doc.getElementById('students-table-container');
            const currentContainer = document.getElementById('students-table-container');

            if (newContainer && currentContainer) {
                currentContainer.innerHTML = newContainer.innerHTML;
                if (searchInput && searchInput.value) {
                    filterCurrentRows(searchInput.value);
                }
            }
        })
        .catch(err => {
            if (err.name !== 'AbortError') {
                console.error('Erreur chargement élèves:', err);
            }
        });
    }

    if (searchInput && searchForm) {
        // Recherche instantanée dès la saisie (0ms) + fetch en arrière-plan
        searchInput.addEventListener('input', function () {
            filterCurrentRows(this.value);

            clearTimeout(searchDebounceTimer);
            searchDebounceTimer = setTimeout(() => {
                fetchResults();
            }, 200);
        });

        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchDebounceTimer);
                fetchResults();
            }
        });

        searchForm.addEventListener('submit', function (e) {
            e.preventDefault();
            clearTimeout(searchDebounceTimer);
            fetchResults();
        });
    }

    // Filtres déroulants : mise à jour instantanée sans rechargement
    document.querySelectorAll('#filter-niveau, #filter-serie, #filter-classe').forEach(select => {
        select.addEventListener('change', () => {
            clearTimeout(searchDebounceTimer);
            fetchResults();
        });
    });

    // Bouton réinitialiser
    if (btnReset) {
        btnReset.addEventListener('click', function (e) {
            e.preventDefault();
            if (searchForm) {
                searchForm.reset();
                if (searchInput) searchInput.value = '';
                document.querySelectorAll('#filter-niveau, #filter-serie, #filter-classe').forEach(s => s.value = '');
            }
            clearTimeout(searchDebounceTimer);
            fetchResults(this.href || searchForm.action);
        });
    }

    // Interception de la pagination pour charger via AJAX
    document.addEventListener('click', function (e) {
        const paginationLink = e.target.closest('#students-table-container .pagination a, #students-table-container nav a');
        if (paginationLink && paginationLink.href) {
            e.preventDefault();
            fetchResults(paginationLink.href);
        }
    });

    // Délégation d'événements pour les boutons d'actions (Voir, Modifier, Supprimer)
    document.addEventListener('click', function (e) {
        const viewBtn = e.target.closest('.btn-view');
        if (viewBtn) {
            e.preventDefault();
            try {
                const student = JSON.parse(viewBtn.dataset.student);
                openViewModal(student);
            } catch (err) {
                console.error('Erreur parsing étudiant (view):', err);
                Swal.fire({ icon: 'error', title: 'Erreur', text: 'Impossible d\'ouvrir la fiche de l\'élève.' });
            }
            return;
        }

        const editBtn = e.target.closest('.btn-edit');
        if (editBtn) {
            e.preventDefault();
            try {
                const student = JSON.parse(editBtn.dataset.student);
                openEditModal(student);
            } catch (err) {
                console.error('Erreur parsing étudiant (edit):', err);
                Swal.fire({ icon: 'error', title: 'Erreur', text: 'Impossible d\'ouvrir le formulaire de modification.' });
            }
            return;
        }

        const deleteBtn = e.target.closest('.delete-student-btn');
        if (deleteBtn) {
            e.preventDefault();
            const form = deleteBtn.closest('form');
            const name = deleteBtn.dataset.name || 'cet élève';
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                html: `L'élève <strong>${name}</strong> sera définitivement supprimé.<br><span class="text-sm text-gray-500">Cette action est irréversible.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ba1a1a',
                cancelButtonColor: '#64748B',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then(r => {
                if (r.isConfirmed && form) form.submit();
            });
            return;
        }
    });

    document.getElementById('stdLevel')?.addEventListener('change', () =>
        populateClasses('stdLevel', 'stdClasse', 'stdSerie', 'stdSerieWrapper'));
    document.getElementById('stdClasse')?.addEventListener('change', () =>
        populateSeries('stdClasse', 'stdSerie', 'stdSerieWrapper'));
    document.getElementById('editLevel')?.addEventListener('change', () =>
        populateClasses('editLevel', 'editClasse', 'editSerie', 'editSerieWrapper'));
    document.getElementById('editClasse')?.addEventListener('change', () =>
        populateSeries('editClasse', 'editSerie', 'editSerieWrapper'));

    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Succès', text: @json(session('success')), timer: 2500, showConfirmButton: false });
    @endif
    @if(session('error'))
        Swal.fire({ icon: 'error', title: 'Erreur', text: @json(session('error')), timer: 3000, showConfirmButton: false });
    @endif
});

window.openModal     = openModal;
window.closeModal    = closeModal;
window.viewStudent   = openViewModal;
window.editStudent   = openEditModal;
window.previewPhoto  = previewPhoto;
window.populateClasses = populateClasses;
window.populateSeries  = populateSeries;
</script>
@endpush