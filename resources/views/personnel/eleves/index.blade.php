@extends('personnel.layouts.app')
@section('title', 'EduManager - Eleves')
@section('content')
<div class="flex justify-between items-start mb-10">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-primary mb-1">Gestion des Élèves</h2>
        <p class="text-body-md text-text-muted">Gérez l'ensemble des élèves inscrits dans votre établissement</p>
    </div>
    <div class="flex gap-4">
        <button class="flex items-center gap-2 px-6 py-2.5 bg-primary text-white rounded-lg font-label-md text-label-md hover:opacity-90 active:scale-95 transition-all card-shadow" onclick="openModal('modal-standard')" type="button">
            <span class="material-symbols-outlined text-[18px]">person_add</span>
            Nouvel élève
        </button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
    <div class="glass-card p-6 rounded-xl flex items-center gap-5 shadow-[4px_4px_12px_rgba(55,48,163,0.04)]">
        <div class="w-14 h-14 rounded-full bg-primary-fixed flex items-center justify-center text-primary">
            <span class="material-symbols-outlined text-3xl">school</span>
        </div>
        <div>
            <h3 class="text-label-md text-text-muted">Élèves inscrits</h3>
            <p class="text-headline-xl font-headline-xl text-on-surface">{{ $totalStudents ?? 0 }}</p>
        </div>
    </div>
    <div class="glass-card p-6 rounded-xl flex items-center gap-5 shadow-[4px_4px_12px_rgba(55,48,163,0.04)]">
        <div class="w-14 h-14 rounded-full bg-secondary-container flex items-center justify-center text-secondary">
            <span class="material-symbols-outlined text-3xl">meeting_room</span>
        </div>
        <div>
            <h3 class="text-label-md text-text-muted">Classes actives</h3>
            <p class="text-headline-xl font-headline-xl text-on-surface">{{ $activeClasses ?? 0 }}</p>
        </div>
    </div>
</div>

<div class="glass-card rounded-xl overflow-hidden shadow-[0_4px_12px_rgba(55,48,163,0.04)]">
    <div class="px-6 py-4 border-b border-surface-subtle bg-surface-container-low flex justify-between items-center">
        <h4 class="font-headline-md text-headline-md text-primary">Liste des élèves</h4>
    </div>

    <form method="GET" action="{{ route('personnel.eleves.index') }}" class="px-6 py-4 border-b border-surface-subtle grid grid-cols-1 md:grid-cols-5 gap-3 bg-white">
        <input type="text" name="search" value="{{ request('search') }}" class="rounded-lg border-outline-variant text-sm" placeholder="Rechercher nom ou matricule">
        <select name="niveau_id" class="rounded-lg border-outline-variant text-sm">
            <option value="">Tous les niveaux</option>
            @foreach($levels ?? [] as $level)
                <option value="{{ $level['id'] }}" @selected((string) request('niveau_id') === (string) $level['id'])>{{ $level['name'] }}</option>
            @endforeach
        </select>
        <select name="id_serie" class="rounded-lg border-outline-variant text-sm">
            <option value="">Toutes les séries</option>
            @foreach($series ?? [] as $serie)
                <option value="{{ $serie->id }}" @selected((string) request('id_serie') === (string) $serie->id)>{{ $serie->nom_serie }}</option>
            @endforeach
        </select>
        <select name="classe_id" class="rounded-lg border-outline-variant text-sm">
            <option value="">Toutes les classes</option>
            @foreach($classes ?? [] as $classe)
                <option value="{{ $classe['id'] }}" @selected((string) request('classe_id') === (string) $classe['id'])>{{ $classe['name'] }}</option>
            @endforeach
        </select>
        <button class="bg-primary text-white rounded-lg px-4 py-2 font-label-md text-sm" type="submit">Filtrer</button>
    </form>

    @if(($students ?? collect())->isEmpty())
    <div class="min-h-[200px] flex flex-col items-center justify-center text-center p-9">
        <div class="w-24 h-24 bg-surface-container rounded-full flex items-center justify-center mb-5">
            <span class="material-symbols-outlined text-primary text-5xl">school</span>
        </div>
        <h3 class="font-headline-md text-headline-md text-on-surface mb-2">Aucun élève enregistré pour le moment</h3>
    </div>
    @else
    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left text-[14px] border-separate border-spacing-y-2">
            <thead class="bg-surface-container-low text-[13px] uppercase tracking-wider text-text-muted">
                <tr>
                    <th class="px-3 py-4 font-semibold">#</th>
                    <th class="px-4 py-4 font-semibold min-w-[200px]">Nom &amp; Prénoms</th>
                    <th class="px-3 py-4 font-semibold">Matricule</th>
                    <th class="px-3 py-4 font-semibold">Sexe</th>
                    <th class="px-3 py-4 font-semibold">Classe</th>
                    <th class="px-3 py-4 font-semibold">Niveau</th>
                    <th class="px-3 py-4 font-semibold min-w-[100px]">Série</th>
                    <th class="px-3 py-4 font-semibold min-w-[110px]">Date de naissance</th>
                    <th class="px-3 py-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-subtle">
                @foreach($students as $student)
                <tr class="hover:bg-surface-container-low transition-colors rounded-lg shadow-sm bg-white">
                    <td class="px-3 py-4 text-[14px] align-middle">{{ $loop->iteration }}</td>
                    <td class="px-4 py-4 min-w-[200px] align-middle">
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-[14px] whitespace-nowrap">{{ $student['lastname'] }} {{ $student['firstname'] }}</span>
                        </div>
                    </td>
                    <td class="px-3 py-4 text-[14px] text-on-surface-variant align-middle">{{ $student['matricule'] ?? 'N/A' }}</td>
                    <td class="px-3 py-4 align-middle">
                        @php
                            $sexe = strtolower(trim($student['sexe'] ?? ''));
                            $badgeClass = '';
                            $displaySexe = 'N/A';
                            if (in_array($sexe, ['m', 'masculin', 'male', 'homme', 'h'])) {
                                $badgeClass = 'bg-blue-100 text-blue-700';
                                $displaySexe = 'Masculin';
                            } elseif (in_array($sexe, ['f', 'féminin', 'feminin', 'female', 'femme', 'f'])) {
                                $badgeClass = 'bg-pink-100 text-pink-700';
                                $displaySexe = 'Féminin';
                            } else {
                                $badgeClass = 'bg-gray-100 text-gray-700';
                                $displaySexe = 'N/A';
                            }
                        @endphp
                        <span class="px-3 py-1.5 rounded-full text-[12px] font-medium {{ $badgeClass }}">{{ $displaySexe }}</span>
                    </td>
                    <td class="px-3 py-4 text-[14px] text-on-surface-variant align-middle">{{ $student['classe'] ?? $student['class'] ?? 'N/A' }}</td>
                    <td class="px-3 py-4 align-middle">
                        <span class="px-3 py-1.5 rounded-full text-[12px] font-medium bg-secondary-container/20 text-on-secondary-container">{{ $student['level'] }}</span>
                    </td>
                    <td class="px-3 py-4 text-on-surface-variant align-middle min-w-[100px]">{{ $student['serie'] ?? '—' }}</td>
                    <td class="px-3 py-4 text-[14px] text-on-surface-variant align-middle min-w-[110px]">{{ $student['birthdate'] }}</td>
                    <td class="px-3 py-4 text-right align-middle">
                        <div class="flex justify-end items-center gap-1">
                            <button class="inline-flex items-center justify-center p-1.5 text-primary hover:bg-primary-fixed rounded-lg transition-colors leading-none" onclick="viewStudent({{ json_encode($student) }})" title="Voir" type="button">
                                <span class="material-symbols-outlined text-[20px]">visibility</span>
                            </button>
                            <button class="inline-flex items-center justify-center p-1.5 text-warning-amber hover:bg-warning-amber/10 rounded-lg transition-colors leading-none" onclick="editStudent({{ json_encode($student) }})" title="Modifier" type="button">
                                <span class="material-symbols-outlined text-[20px]">edit</span>
                            </button>
                            <form action="{{ route('personnel.eleves.destroy', $student['id']) }}" method="POST" class="inline-flex items-center leading-none m-0 p-0 delete-student-form">
                                @csrf
                                @method('DELETE')
                                <button class="inline-flex items-center justify-center p-1.5 text-alert-red hover:bg-error-container/20 rounded-lg transition-colors delete-student-btn leading-none" data-name="{{ $student['firstname'] }} {{ $student['lastname'] }}" title="Supprimer" type="button">
                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-surface-subtle bg-surface-container-low/30 flex items-center justify-between">
        <span class="text-[13px] text-text-muted">Affichage de {{ $students->firstItem() ?? 0 }} à {{ $students->lastItem() ?? 0 }} sur {{ $students->total() ?? 0 }} élèves</span>
        <div class="flex gap-2 text-sm">{{ $students->links() ?? '' }}</div>
    </div>
    @endif
</div>

@include('personnel.partials.student-modals')
@endsection

@push('styles')
<style>
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    .glass-card { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); border: 1px solid rgba(226, 232, 240, 1); }
    .modal-overlay { transition: backdrop-filter 0.3s ease; }
    #modal-standard, #modal-view, #modal-edit { transition: opacity 0.3s ease; }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@php
    $availableClassesForJs = collect($classes ?? [])->values();
    $availableSeriesForJs = ($series ?? collect())->map(function ($serie) {
        return [
            'id' => $serie->id,
            'class_ids' => $serie->classes->pluck('id')->values(),
            'nom_serie' => $serie->nom_serie,
        ];
    })->values();
@endphp
<script>
    const availableClasses = @json($availableClassesForJs);
    const availableSeries = @json($availableSeriesForJs);

    function populateClasses(levelSelectId, classSelectId, seriesSelectId, seriesWrapperId, selectedClassId = '', selectedSeriesId = '') {
        const levelId = document.getElementById(levelSelectId)?.value;
        const select = document.getElementById(classSelectId);
        if (!select) return;
        const matching = availableClasses.filter(classe => String(classe.level_id) === String(levelId));
        select.innerHTML = '<option value="">Sélectionner une classe</option>' + matching.map(classe =>
            `<option value="${classe.id}">${classe.name}</option>`
        ).join('');
        select.disabled = !levelId || matching.length === 0;
        select.value = matching.some(classe => String(classe.id) === String(selectedClassId)) ? String(selectedClassId) : '';
        populateSeries(classSelectId, seriesSelectId, seriesWrapperId, selectedSeriesId);
    }

    function populateSeries(classSelectId, seriesSelectId, wrapperId, selectedId = '') {
        const classId = document.getElementById(classSelectId)?.value;
        const select = document.getElementById(seriesSelectId);
        const wrapper = document.getElementById(wrapperId);
        if (!select || !wrapper) return;
        const matching = availableSeries.filter(serie => (serie.class_ids ?? []).map(String).includes(String(classId)));
        select.innerHTML = `<option value="">${matching.length ? 'Sélectionner une série' : 'Aucune série pour cette classe'}</option>` + matching.map(serie =>
            `<option value="${serie.id}">${serie.nom_serie}</option>`
        ).join('');
        wrapper.classList.remove('hidden');
        select.disabled = !classId || matching.length === 0;
        select.value = matching.some(serie => String(serie.id) === String(selectedId)) ? String(selectedId) : '';
    }

    function previewPhoto(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) { preview.innerHTML = `<img src="${e.target.result}" alt="Photo" class="w-full h-full object-cover object-center">`; };
            reader.readAsDataURL(input.files[0]);
        } else { preview.innerHTML = `<span class="material-symbols-outlined text-3xl text-text-muted">photo_camera</span>`; }
    }

    function openModal(id) {
        const modal = document.getElementById(id);
        const contentId = id + '-content';
        const content = document.getElementById(contentId);
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        const contentId = id + '-content';
        const content = document.getElementById(contentId);
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }, 300);
    }

    function viewStudent(student) { openViewModal(student); }
    function editStudent(student) { openEditModal(student); }

    function openViewModal(student) {
        const fullName = `${student.firstname ?? ''} ${student.lastname ?? ''}`.trim();
        document.getElementById('viewStudentFullName').textContent = fullName || '-';
        document.getElementById('viewStudentMatricule').textContent = student.matricule ?? 'N/A';
        document.getElementById('viewStudentBirthdate').textContent = student.birthdate ?? 'N/A';
        document.getElementById('viewStudentBirthplace').textContent = student.birthplace ?? 'N/A';
        document.getElementById('viewStudentClasse').textContent = student.classe ?? student.class ?? 'N/A';
        document.getElementById('viewStudentNiveau').textContent = student.level ?? 'N/A';
        document.getElementById('viewStudentSerie').textContent = student.serie ?? '—';
        document.getElementById('viewStudentNationalite').textContent = student.nationalite ?? '-';
        document.getElementById('viewStudentInterne').textContent = student.interne ? 'Oui' : 'Non';
        document.getElementById('viewStudentAffecte').textContent = student.affecte ? 'Oui' : 'Non';
        let sexeDisplay = 'Non renseigné';
        const sexe = (student.sexe ?? '').toString().trim().toLowerCase();
        if (['m','masculin','male','homme','h'].includes(sexe)) sexeDisplay = 'Masculin';
        else if (['f','féminin','feminin','female','femme','f'].includes(sexe)) sexeDisplay = 'Féminin';
        document.getElementById('viewStudentSexeDetail').textContent = sexeDisplay;
        document.getElementById('viewStudentParentLastname').textContent = student.parent_lastname ?? '-';
        document.getElementById('viewStudentParentFirstname').textContent = student.parent_firstname ?? '-';
        document.getElementById('viewStudentParentPhone').textContent = student.parent_phone ?? '-';
        document.getElementById('viewStudentParentEmail').textContent = student.parent_email ?? '-';
        document.getElementById('viewStudentCreatedAt').textContent = student.created_at ?? 'N/A';
        document.getElementById('viewStudentUpdatedAt').textContent = student.updated_at ?? 'N/A';
        openModal('modal-view');
    }

    function openEditModal(student) {
        document.getElementById('editEleveId').value = student.id;
        document.getElementById('editLastname').value = student.lastname ?? '';
        document.getElementById('editFirstname').value = student.firstname ?? '';
        document.getElementById('editMatricule').value = student.matricule ?? '';
        document.getElementById('editBirthdate').value = student.birthdate_raw ?? '';
        document.getElementById('editBirthPlace').value = student.birthplace ?? '';
        document.getElementById('editLevel').value = student.level_id ?? '';
        populateClasses('editLevel', 'editClasse', 'editSerie', 'editSerieWrapper', student.class_id ?? '', student.serie_id ?? '');
        document.getElementById('editParentLastname').value = student.parent_lastname ?? '';
        document.getElementById('editParentFirstname').value = student.parent_firstname ?? '';
        document.getElementById('editParentPhone').value = student.parent_phone ?? '';
        document.getElementById('editParentEmail').value = student.parent_email ?? '';
        document.getElementById('editInterne').value = student.interne ? '1' : '0';
        document.getElementById('editAffecte').value = student.affecte ? '1' : '0';
        document.getElementById('form-edit').action = `/personnel/eleves/${student.id}`;
        openModal('modal-edit');
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('stdLevel')?.addEventListener('change', () => populateClasses('stdLevel', 'stdClasse', 'stdSerie', 'stdSerieWrapper'));
        document.getElementById('editLevel')?.addEventListener('change', () => populateClasses('editLevel', 'editClasse', 'editSerie', 'editSerieWrapper'));
        document.getElementById('stdClasse')?.addEventListener('change', () => populateSeries('stdClasse', 'stdSerie', 'stdSerieWrapper'));
        document.getElementById('editClasse')?.addEventListener('change', () => populateSeries('editClasse', 'editSerie', 'editSerieWrapper'));

        document.querySelectorAll('.delete-student-btn').forEach((button) => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Êtes-vous sûr ?',
                    text: `L'élève "${this.dataset.name}" sera définitivement supprimé.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ba1a1a',
                    cancelButtonColor: '#64748B',
                    confirmButtonText: 'Oui, supprimer',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) this.closest('form').submit();
                });
            });
        });

        @if(session('success'))
            Swal.fire({ icon: 'success', title: 'Succès', text: @json(session('success')), timer: 2500, showConfirmButton: false });
        @endif
        @if(session('error'))
            Swal.fire({ icon: 'error', title: 'Erreur', text: @json(session('error')), timer: 3000, showConfirmButton: false });
        @endif
    });
</script>
@endpush
