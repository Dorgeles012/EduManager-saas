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
        <button class="flex items-center gap-2 px-6 py-2.5 bg-success-green text-on-primary rounded-lg font-label-md hover:opacity-90 transition-all active:scale-95" onclick="openModal('modal-transfer')">
            <span class="material-symbols-outlined text-[20px]">swap_horiz</span>
            Élève transféré
        </button>
        <button class="flex items-center gap-2 px-6 py-2.5 bg-primary-container text-on-primary rounded-lg font-label-md hover:bg-primary transition-all active:scale-95 shadow-md" onclick="openModal('modal-standard')">
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
                            <button class="p-2 text-alert-red hover:bg-error-container/20 rounded-lg transition-colors" onclick="confirmDelete({{ $student['id'] }}, '{{ $student['firstname'] }} {{ $student['lastname'] }}')" title="Supprimer">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
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
        <form class="p-8" id="form-standard">
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
                                    <option>{{ $level }}</option>
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
        <form class="p-8" id="form-transfer">
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
                                <option>{{ $level }}</option>
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