@extends('client.layouts.app')
@section('title', 'EduManager - Eleves')
@section('content')
<!-- Page Header -->
<div class="flex justify-between items-start mb-10">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-primary mb-1">Gestion des Élèves</h2>
        <p class="text-body-md text-text-muted">Gérez l'ensemble des élèves inscrits dans votre établissement</p>
    </div>
    <div class="flex gap-4">
        <button class="flex items-center gap-2 px-6 py-2.5 bg-success-green text-on-primary px-6 py-2.5 rounded-lg font-label-md text-label-md flex items-center gap-2 hover:opacity-90 active:scale-95 transition-all card-shadow" onclick="openModal('modal-transfer')">
            <span class="material-symbols-outlined text-[20px]">swap_horiz</span>
            Élève transféré
        </button>
        <button class="flex items-center gap-2 px-6 py-2.5 bg-primary text-on-primary px-6 py-2.5 rounded-lg font-label-md text-label-md flex items-center gap-2 hover:opacity-90 active:scale-95 transition-all card-shadow" onclick="openModal('modal-standard')">
            <span class="material-symbols-outlined text-[20px]">person_add</span>
            Nouvel élève
        </button>
    </div>
</div>

<!-- Stats Grid -->
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

<!-- Data Table Section -->
<div class="glass-card rounded-xl overflow-hidden shadow-[0_4px_12px_rgba(55,48,163,0.04)]">
    <div class="px-6 py-4 border-b border-surface-subtle bg-surface-container-low flex justify-between items-center">
        <h4 class="font-headline-md text-headline-md text-primary">Liste des élèves</h4>
    </div>

    <form method="GET" action="{{ route('client.eleve') }}" class="px-6 py-4 border-b border-surface-subtle grid grid-cols-1 md:grid-cols-4 gap-3 bg-white">
        <input type="text" name="search" value="{{ request('search') }}" class="rounded-lg border-outline-variant" placeholder="Rechercher nom ou matricule">
        <select name="niveau_id" class="rounded-lg border-outline-variant">
            <option value="">Tous les niveaux</option>
            @foreach($levels ?? [] as $level)
                <option value="{{ $level['id'] }}" @selected((string) request('niveau_id') === (string) $level['id'])>{{ $level['name'] }}</option>
            @endforeach
        </select>
        <select name="classe_id" class="rounded-lg border-outline-variant">
            <option value="">Toutes les classes</option>
            @foreach($classes ?? [] as $classe)
                <option value="{{ $classe['id'] }}" @selected((string) request('classe_id') === (string) $classe['id'])>{{ $classe['name'] }}</option>
            @endforeach
        </select>
        <button class="bg-primary text-white rounded-lg px-4 py-2 font-label-md" type="submit">Filtrer</button>
    </form>

    @if(($students ?? collect())->isEmpty())
    <div class="min-h-[200px] flex flex-col items-center justify-center text-center p-9">
        <div class="w-24 h-24 bg-surface-container rounded-full flex items-center justify-center mb-5">
            <span class="material-symbols-outlined text-primary text-5xl">school</span>
        </div>
        <h3 class="font-headline-md text-headline-md text-on-surface mb-2">Aucun élève enregistré pour le moment</h3>
        <p class="text-body-md text-text-muted max-w-sm">Commencez par ajouter votre premier élève en utilisant les boutons d'action ci-dessus.</p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-surface-container-low text-label-sm uppercase tracking-wider text-text-muted">
                <tr>
                    <th class="px-6 py-4">#</th>
                    <th class="px-6 py-4">Nom &amp; Prénoms</th>
                    <th class="px-6 py-4">Niveau</th>
                    <th class="px-6 py-4">Matricule</th>
                    <th class="px-6 py-4">Date de naissance</th>
                    <th class="px-6 py-4">Parents</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-subtle">
                @foreach($students as $student)
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-6 py-4 text-body-sm">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary-fixed flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-[18px]">person</span>
                            </div>
                            <span class="font-body-md font-medium">{{ $student['lastname'] }} {{ $student['firstname'] }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-label-sm bg-secondary-container/20 text-on-secondary-container">
                            {{ $student['level'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $student['matricule'] ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $student['birthdate'] }}</td>
                    <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $student['parent_name'] }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <button class="p-2 text-primary hover:bg-primary-fixed rounded-lg transition-colors" onclick="viewStudent({{ $student['id'] }})" title="Voir">
                                <span class="material-symbols-outlined">visibility</span>
                            </button>
                            <button class="p-2 text-warning-amber hover:bg-warning-amber/10 rounded-lg transition-colors" onclick="editStudent({{ json_encode($student) }})" title="Modifier">
                                <span class="material-symbols-outlined">edit</span>
                            </button>
                            <form action="{{ route('client.eleve.destroy', $student['id']) }}" method="POST" class="inline delete-student-form">
                                @csrf
                                @method('DELETE')
                                <button class="p-2 text-alert-red hover:bg-error-container/20 rounded-lg transition-colors delete-student-btn" data-name="{{ $student['firstname'] }} {{ $student['lastname'] }}" title="Supprimer" type="button">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-surface-subtle bg-surface-container-low/30 flex items-center justify-between">
        <span class="text-label-sm text-text-muted">
            Affichage de {{ $students->firstItem() ?? 0 }} à {{ $students->lastItem() ?? 0 }} sur {{ $students->total() ?? 0 }} élèves
        </span>
        <div class="flex gap-2">
            {{ $students->links() ?? '' }}
        </div>
    </div>
    @endif
</div>

<!-- Modal: Standard Addition -->
<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="modal-standard">
    <div class="absolute inset-0 modal-overlay backdrop-blur-md bg-black/30" onclick="closeModal('modal-standard')"></div>
    <div class="relative glass-card w-full max-w-4xl max-h-[90vh] overflow-y-auto rounded-xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0" id="modal-standard-content">
        <div class="p-6 border-b border-outline-variant flex justify-between items-center bg-primary text-white sticky top-0">
            <h3 class="font-headline-md text-headline-md">AJOUT D'UN ÉLÈVE</h3>
            <button class="hover:bg-white/20 rounded-full p-1 transition-colors" onclick="closeModal('modal-standard')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form class="p-8" id="form-standard" action="{{ route('client.eleve.store') }}" method="POST">
            @csrf
            <input type="hidden" name="type_eleve" value="nouveau">
            <input type="hidden" name="nom" id="stdLastnameHidden">
            <input type="hidden" name="prenom" id="stdFirstnameHidden">
            <input type="hidden" name="date_naissance" id="stdBirthdateHidden">
            <input type="hidden" name="lieu_naissance" id="stdBirthPlaceHidden">
            <input type="hidden" name="niveau_id" id="stdLevelHidden">
            <input type="hidden" name="parent_nom" id="parentLastnameHidden">
            <input type="hidden" name="parent_prenom" id="parentFirstnameHidden">
            <input type="hidden" name="parent_telephone" id="parentPhoneHidden">
            <input type="hidden" name="parent_email" id="parentEmailHidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Section Élève -->
                <div class="space-y-6">
                    <h4 class="font-label-md text-primary flex items-center gap-2 border-b border-primary-fixed pb-2">
                        <span class="material-symbols-outlined">person</span>
                        INFORMATIONS DE L'ÉLÈVE
                    </h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Nom <span class="text-alert-red">*</span></label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-primary focus:border-primary" id="stdLastname" placeholder="Ex: TRAORE" required type="text">
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Prénom <span class="text-alert-red">*</span></label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-primary focus:border-primary" id="stdFirstname" placeholder="Ex: Moussa" required type="text">
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Date de naissance <span class="text-alert-red">*</span></label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-primary focus:border-primary" id="birthdate-std" required type="date">
                            <p class="hidden text-[11px] text-alert-red mt-1 flex items-center gap-1" id="age-warning-std">
                                <span class="material-symbols-outlined text-[14px]">warning</span>
                                L'âge minimum requis est de 5 ans.
                            </p>
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Lieu de naissance</label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-primary focus:border-primary" id="stdBirthPlace" placeholder="Ex: Abidjan" type="text">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-label-sm text-on-surface mb-1.5">Niveau</label>
                                <select class="w-full rounded-lg border-outline-variant focus:ring-primary focus:border-primary" id="stdLevel">
                                    @foreach($levels ?? ['CP1', 'CP2', 'CE1', 'CE2', 'CM1', 'CM2'] as $level)
                                    <option value="{{ is_array($level) ? $level['id'] : $level }}">{{ is_array($level) ? $level['name'] : $level }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-label-sm text-on-surface mb-1.5">Année Académique</label>
                                <input class="w-full rounded-lg bg-surface-subtle border-outline-variant text-text-muted" disabled type="text" value="{{ $currentAcademicYear ?? '2024-2025' }}">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Section Parent -->
                <div class="space-y-6">
                    <h4 class="font-label-md text-primary flex items-center gap-2 border-b border-primary-fixed pb-2">
                        <span class="material-symbols-outlined">family_restroom</span>
                        INFORMATIONS DU PARENT
                    </h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Nom du Parent <span class="text-alert-red">*</span></label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-primary focus:border-primary" id="parentLastname" required type="text">
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Prénom du Parent <span class="text-alert-red">*</span></label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-primary focus:border-primary" id="parentFirstname" required type="text">
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Type de Parent</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 text-label-sm cursor-pointer">
                                    <input class="text-primary focus:ring-primary" name="parent_type" type="radio" value="pere"> Père
                                </label>
                                <label class="flex items-center gap-2 text-label-sm cursor-pointer">
                                    <input class="text-primary focus:ring-primary" name="parent_type" type="radio" value="mere"> Mère
                                </label>
                                <label class="flex items-center gap-2 text-label-sm cursor-pointer">
                                    <input checked class="text-primary focus:ring-primary" name="parent_type" type="radio" value="tuteur"> Tuteur
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Téléphone <span class="text-alert-red">*</span></label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-primary focus:border-primary" id="parentPhone" placeholder="+225 00 00 00 00 00" required type="tel">
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Email</label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-primary focus:border-primary" id="parentEmail" placeholder="parent@email.com" type="email">
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-10 flex justify-end gap-4 pt-6 border-t border-outline-variant">
                <button class="px-6 py-2 text-on-surface-variant hover:bg-surface-subtle rounded-lg font-label-md transition-all" onclick="closeModal('modal-standard')" type="button">
                    Annuler
                </button>
                <button class="px-8 py-2 bg-primary text-white rounded-lg font-label-md hover:bg-primary/90 transition-all active:scale-95 shadow-md" type="submit">
                    Enregistrer l'élève
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Transferred Addition -->
<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="modal-transfer">
    <div class="absolute inset-0 modal-overlay backdrop-blur-md bg-black/30" onclick="closeModal('modal-transfer')"></div>
    <div class="relative glass-card w-full max-w-4xl max-h-[90vh] overflow-y-auto rounded-xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0" id="modal-transfer-content">
        <div class="p-6 border-b border-outline-variant flex justify-between items-center bg-success-green text-white sticky top-0">
            <h3 class="font-headline-md text-headline-md">AJOUT D'UN ÉLÈVE TRANSFÉRÉ</h3>
            <button class="hover:bg-white/20 rounded-full p-1 transition-colors" onclick="closeModal('modal-transfer')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form class="p-8" id="form-transfer" action="{{ route('client.eleve.store') }}" method="POST">
            @csrf
            <input type="hidden" name="type_eleve" value="transfere">
            <input type="hidden" name="nom" id="trfLastnameHidden">
            <input type="hidden" name="prenom" id="trfFirstnameHidden">
            <input type="hidden" name="matricule" id="trfMatriculeHidden">
            <input type="hidden" name="date_naissance" id="trfBirthdateHidden">
            <input type="hidden" name="ancien_etablissement" id="trfPreviousSchoolHidden">
            <input type="hidden" name="niveau_id" id="trfLevelHidden">
            <input type="hidden" name="parent_nom" id="trfParentLastnameHidden">
            <input type="hidden" name="parent_prenom" id="trfParentFirstnameHidden">
            <input type="hidden" name="parent_telephone" id="trfParentPhoneHidden">
            <input type="hidden" name="parent_email" id="trfParentEmailHidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Section Élève -->
                <div class="space-y-6">
                    <h4 class="font-label-md text-success-green flex items-center gap-2 border-b border-secondary-container pb-2">
                        <span class="material-symbols-outlined">person</span>
                        INFORMATIONS DE L'ÉLÈVE
                    </h4>
                    <div class="space-y-4">
                        <div class="col-span-2">
                            <label class="block text-label-sm text-on-surface mb-1.5">Nom complet <span class="text-alert-red">*</span></label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-success-green focus:border-success-green" id="trfFullname" placeholder="Ex: KOFFI Kouassi" required type="text">
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Matricule National <span class="text-alert-red">*</span></label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-success-green focus:border-success-green" id="trfMatricule" placeholder="Ex: 12345678A" required type="text">
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Date de naissance <span class="text-alert-red">*</span></label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-success-green focus:border-success-green" id="birthdate-trf" required type="date">
                            <p class="hidden text-[11px] text-alert-red mt-1 flex items-center gap-1" id="age-warning-trf">
                                <span class="material-symbols-outlined text-[14px]">warning</span>
                                L'âge minimum requis est de 5 ans.
                            </p>
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Ancien Établissement <span class="text-alert-red">*</span></label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-success-green focus:border-success-green" id="trfPreviousSchool" placeholder="Nom de l'école précédente" required type="text">
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Niveau Actuel</label>
                            <select class="w-full rounded-lg border-outline-variant focus:ring-success-green focus:border-success-green" id="trfLevel">
                                @foreach($levels ?? ['CP1', 'CP2', 'CE1', 'CE2', 'CM1', 'CM2'] as $level)
                                <option value="{{ is_array($level) ? $level['id'] : $level }}">{{ is_array($level) ? $level['name'] : $level }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <!-- Section Parent -->
                <div class="space-y-6">
                    <h4 class="font-label-md text-success-green flex items-center gap-2 border-b border-secondary-container pb-2">
                        <span class="material-symbols-outlined">family_restroom</span>
                        INFORMATIONS DU PARENT
                    </h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Nom &amp; Prénom du Parent <span class="text-alert-red">*</span></label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-success-green focus:border-success-green" id="trfParentName" required type="text">
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Email du Parent</label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-success-green focus:border-success-green" id="trfParentEmail" type="email">
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Téléphone <span class="text-alert-red">*</span></label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-success-green focus:border-success-green" id="trfParentPhone" required type="tel">
                        </div>
                        <div class="bg-surface-container-low p-4 rounded-lg">
                            <p class="text-body-sm text-on-secondary-container">
                                <strong>Note :</strong> Pour les élèves transférés, le dossier scolaire complet doit être numérisé et joint ultérieurement dans la fiche de l'élève.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-10 flex justify-end gap-4 pt-6 border-t border-outline-variant">
                <button class="px-6 py-2 text-on-surface-variant hover:bg-surface-subtle rounded-lg font-label-md transition-all" onclick="closeModal('modal-transfer')" type="button">
                    Annuler
                </button>
                <button class="px-8 py-2 bg-success-green text-white rounded-lg font-label-md hover:opacity-90 transition-all active:scale-95 shadow-md" type="submit">
                    Valider le transfert
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    .glass-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(226, 232, 240, 1);
    }
    .modal-overlay {
        transition: backdrop-filter 0.3s ease;
    }
    
    /* Animation styles for modals */
    #modal-standard, #modal-transfer {
        transition: opacity 0.3s ease;
    }
</style>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const standardForm = document.getElementById('form-standard');
        if (standardForm) {
            standardForm.addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                if (!validateAge($('#birthdate-std').val(), 'age-warning-std')) return;
                document.getElementById('stdLastnameHidden').value = document.getElementById('stdLastname').value;
                document.getElementById('stdFirstnameHidden').value = document.getElementById('stdFirstname').value;
                document.getElementById('stdBirthdateHidden').value = document.getElementById('birthdate-std').value;
                document.getElementById('stdBirthPlaceHidden').value = document.getElementById('stdBirthPlace').value;
                document.getElementById('stdLevelHidden').value = document.getElementById('stdLevel').value;
                document.getElementById('parentLastnameHidden').value = document.getElementById('parentLastname').value;
                document.getElementById('parentFirstnameHidden').value = document.getElementById('parentFirstname').value;
                document.getElementById('parentPhoneHidden').value = document.getElementById('parentPhone').value;
                document.getElementById('parentEmailHidden').value = document.getElementById('parentEmail').value;
                HTMLFormElement.prototype.submit.call(standardForm);
            }, true);
        }

        const transferForm = document.getElementById('form-transfer');
        if (transferForm) {
            transferForm.addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                if (!validateAge($('#birthdate-trf').val(), 'age-warning-trf')) return;
                const names = document.getElementById('trfFullname').value.trim().split(/\s+/);
                document.getElementById('trfLastnameHidden').value = names.shift() || '';
                document.getElementById('trfFirstnameHidden').value = names.join(' ');
                document.getElementById('trfMatriculeHidden').value = document.getElementById('trfMatricule').value;
                document.getElementById('trfBirthdateHidden').value = document.getElementById('birthdate-trf').value;
                document.getElementById('trfPreviousSchoolHidden').value = document.getElementById('trfPreviousSchool').value;
                document.getElementById('trfLevelHidden').value = document.getElementById('trfLevel').value;
                const parentNames = document.getElementById('trfParentName').value.trim().split(/\s+/);
                document.getElementById('trfParentLastnameHidden').value = parentNames.shift() || '';
                document.getElementById('trfParentFirstnameHidden').value = parentNames.join(' ');
                document.getElementById('trfParentPhoneHidden').value = document.getElementById('trfParentPhone').value;
                document.getElementById('trfParentEmailHidden').value = document.getElementById('trfParentEmail').value;
                HTMLFormElement.prototype.submit.call(transferForm);
            }, true);
        }

        document.querySelectorAll('.delete-student-btn').forEach((button) => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                if (confirm(`Supprimer l'élève "${this.dataset.name}" ?`)) {
                    this.closest('form').submit();
                }
            }, true);
        });
    });

    // Modal functions with animation
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

    function viewStudent(id) {
        Swal.fire({
            title: 'Fiche élève',
            text: `Affichage des détails de l'élève ID: ${id}`,
            icon: 'info',
            confirmButtonColor: '#1f108e',
            borderRadius: '12px'
        });
    }

    function editStudent(student) {
        Swal.fire({
            title: 'Modifier',
            text: `Modification de l'élève ${student.firstname} ${student.lastname}`,
            icon: 'info',
            confirmButtonColor: '#D97706',
            borderRadius: '12px'
        });
    }

    // Age Validation Logic
    function validateAge(inputDate, warningId) {
        if (!inputDate) return true;
        
        const birth = new Date(inputDate);
        const today = new Date();
        let age = today.getFullYear() - birth.getFullYear();
        const m = today.getMonth() - birth.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
            age--;
        }
        
        if (age < 5) {
            $(`#${warningId}`).removeClass('hidden');
            return false;
        } else {
            $(`#${warningId}`).addClass('hidden');
            return true;
        }
    }

    // Confirm Delete
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Êtes-vous sûr ?',
            text: `L'élève "${name}" sera définitivement supprimé.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ba1a1a',
            cancelButtonColor: '#64748B',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler',
            borderRadius: '12px'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Supprimé !',
                    text: 'L\'élève a été supprimé avec succès.',
                    icon: 'success',
                    confirmButtonColor: '#1f108e',
                    borderRadius: '12px'
                });
            }
        });
    }

    $(document).ready(function() {
        // Birthdate changes
        $('#birthdate-std').on('change', function() {
            validateAge($(this).val(), 'age-warning-std');
        });
        $('#birthdate-trf').on('change', function() {
            validateAge($(this).val(), 'age-warning-trf');
        });

        // Form Submissions
        $('#form-standard').on('submit', function(e) {
            e.preventDefault();
            if (!validateAge($('#birthdate-std').val(), 'age-warning-std')) {
                Swal.fire({
                    title: 'Âge non valide',
                    text: 'L\'élève doit avoir au moins 5 ans.',
                    icon: 'error',
                    confirmButtonColor: '#3730a3',
                    borderRadius: '12px'
                });
                return;
            }
            
            Swal.fire({
                title: 'Succès !',
                text: "L'élève a été enregistré avec succès.",
                icon: 'success',
                confirmButtonColor: '#3730a3',
                borderRadius: '12px'
            }).then(() => {
                closeModal('modal-standard');
                this.reset();
            });
        });

        $('#form-transfer').on('submit', function(e) {
            e.preventDefault();
            if (!validateAge($('#birthdate-trf').val(), 'age-warning-trf')) {
                Swal.fire({
                    title: 'Âge non valide',
                    text: 'L\'élève doit avoir au moins 5 ans.',
                    icon: 'error',
                    confirmButtonColor: '#059669',
                    borderRadius: '12px'
                });
                return;
            }

            Swal.fire({
                title: 'Transfert Validé !',
                text: "L'élève transféré a été ajouté à la file d'attente de validation.",
                icon: 'success',
                confirmButtonColor: '#059669',
                borderRadius: '12px'
            }).then(() => {
                closeModal('modal-transfer');
                this.reset();
            });
        });

        // Close modal on escape key
        $(document).on('keydown', function(e) {
            if (e.key === "Escape") {
                const modals = ['modal-standard', 'modal-transfer'];
                modals.forEach(id => {
                    const modal = document.getElementById(id);
                    if (modal && modal.classList.contains('flex')) {
                        closeModal(id);
                    }
                });
            }
        });
    });
</script>
@endpush
