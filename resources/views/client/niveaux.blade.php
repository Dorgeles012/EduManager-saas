@extends('client.layouts.app')


@section('content')
<!-- Page Header -->
<div class="flex justify-between items-end mb-8">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-primary">Gestion des Niveaux</h2>
        <p class="text-body-md text-on-surface-variant">Liste complète des niveaux d'enseignement de votre réseau.</p>
    </div>
    <button class="bg-primary text-on-primary px-6 py-2.5 rounded-lg font-label-md text-label-md flex items-center gap-2 hover:opacity-90 active:scale-95 transition-all card-shadow" onclick="toggleModal('add-level-modal')">
        <span class="material-symbols-outlined text-[20px]">add</span>
        Ajouter un niveau
    </button>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-gutter-desktop mb-8">
    <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant card-shadow">
        <div class="flex justify-between items-start mb-4">
            <div class="p-2 bg-primary-fixed rounded-lg text-primary">
                <span class="material-symbols-outlined">layers</span>
            </div>
            <span class="text-success-green flex items-center text-label-sm font-bold bg-success-green/10 px-2 py-0.5 rounded-full">
                +12%
            </span>
        </div>
        <h3 class="text-on-surface-variant text-label-md mb-1 uppercase tracking-wider">Total des niveaux</h3>
        <p class="font-headline-xl text-headline-xl text-primary">{{ $totalLevels ?? 2 }}</p>
    </div>
</div>

<!-- Main Table Container -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant card-shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-white/50">
        <h3 class="font-headline-md text-headline-md text-primary">Liste des Niveaux</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-surface-container-low">
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">N°</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Niveau</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Établissement</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase ">Date de création</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse($levels ?? [
                    ['id' => 1, 'name' => 'CM2 B', 'school' => 'saint françois xavier', 'date' => '26-05-2026', 'icon' => 'auto_stories'],
                    ['id' => 2, 'name' => 'Tle D', 'school' => 'saint françois xavier', 'date' => '23-05-2026', 'icon' => 'workspace_premium']
                ] as $level)
                <tr class="hover:bg-surface-bright transition-colors group">
                    <td class="px-6 py-4 text-body-sm font-medium text-on-surface-variant">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-primary-fixed flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                                <span class="material-symbols-outlined text-[18px]">{{ $level['icon'] }}</span>
                            </div>
                            <span class="font-body-md font-semibold text-primary">{{ $level['name'] }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-secondary-container/20 text-on-secondary-container px-3 py-1 rounded-full text-label-sm font-medium border border-secondary-container/30">
                            {{ $level['school'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $level['date'] }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <button class="p-2 text-primary hover:bg-primary-fixed rounded-lg transition-colors" onclick="openEditModal('{{ $level['id'] }}', '{{ $level['name'] }}', '{{ $level['school'] }}')" title="Modifier">
                                <span class="material-symbols-outlined">edit</span>
                            </button>
                            <button class="p-2 text-error hover:bg-error-container/20 rounded-lg transition-colors" onclick="confirmDelete({{ $level['id'] }})" title="Supprimer">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-on-surface-variant">
                        <div class="flex flex-col items-center gap-4">
                            <span class="material-symbols-outlined text-[48px] text-outline">layers</span>
                            <p>Aucun niveau trouvé</p>
                            <button class="text-primary hover:underline" onclick="toggleModal('add-level-modal')">Ajouter un niveau</button>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 bg-surface-container-low border-t border-outline-variant flex justify-between items-center">
        <p class="text-label-sm text-on-surface-variant">Affichage de 1 à {{ count($levels ?? [1,2]) }} sur {{ count($levels ?? [1,2]) }} niveaux</p>
        <div class="flex gap-1">
            <button class="w-8 h-8 flex items-center justify-center rounded hover:bg-surface-container-high transition-colors disabled:opacity-50" disabled>
                <span class="material-symbols-outlined">chevron_left</span>
            </button>
            <button class="w-8 h-8 flex items-center justify-center rounded bg-primary text-on-primary font-label-sm">1</button>
            <button class="w-8 h-8 flex items-center justify-center rounded hover:bg-surface-container-high transition-colors">
                <span class="material-symbols-outlined">chevron_right</span>
            </button>
        </div>
    </div>
</div>

<!-- Modal: Add Level -->
<div class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4" id="add-level-modal">
    <div class="absolute inset-0 modal-backdrop" onclick="toggleModal('add-level-modal')"></div>
    <div class="relative bg-surface-container-lowest w-full max-w-md rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-primary-fixed/30">
            <h3 class="font-headline-md text-headline-md text-primary">Nouveau Niveau</h3>
            <button class="p-1 hover:bg-surface-container-high rounded-full transition-colors" onclick="toggleModal('add-level-modal')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form class="p-6 space-y-5" id="addLevelForm">
            <div class="space-y-2">
                <label class="font-label-md text-label-md text-on-surface-variant block">Nom du niveau</label>
                <input class="w-full border-outline-variant rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-container focus:border-primary outline-none transition-all" id="levelName" placeholder="Ex: CP, 6ème, CM2 B" type="text" required>
            </div>
            <div class="space-y-2">
                <label class="font-label-md text-label-md text-on-surface-variant block">Établissement</label>
                <select class="w-full border-outline-variant rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-container focus:border-primary outline-none transition-all bg-white" id="levelSchool" required>
                    <option value="">Sélectionner un établissement</option>
                    @foreach($schools ?? [['id' => 1, 'name' => 'saint françois xavier']] as $school)
                    <option value="{{ $school['id'] }}">{{ $school['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="pt-4 flex gap-3">
                <button class="flex-1 py-3 border border-outline text-on-surface-variant font-label-md rounded-lg hover:bg-surface-container-high transition-colors" onclick="toggleModal('add-level-modal')" type="button">Annuler</button>
                <button class="flex-1 py-3 bg-primary text-on-primary font-label-md rounded-lg hover:opacity-95 transition-opacity shadow-lg shadow-primary/20" type="submit">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Level -->
<div class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4" id="edit-level-modal">
    <div class="absolute inset-0 modal-backdrop" onclick="toggleModal('edit-level-modal')"></div>
    <div class="relative bg-surface-container-lowest w-full max-w-md rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-primary-fixed/30">
            <h3 class="font-headline-md text-headline-md text-primary">Modifier le Niveau</h3>
            <button class="p-1 hover:bg-surface-container-high rounded-full transition-colors" onclick="toggleModal('edit-level-modal')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form class="p-6 space-y-5" id="editLevelForm">
            <input type="hidden" id="editLevelId">
            <div class="space-y-2">
                <label class="font-label-md text-label-md text-on-surface-variant block">Nom du niveau</label>
                <input class="w-full border-outline-variant rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-container focus:border-primary outline-none transition-all" id="editLevelName" type="text" required>
            </div>
            <div class="space-y-2">
                <label class="font-label-md text-label-md text-on-surface-variant block">Établissement</label>
                <select class="w-full border-outline-variant rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-container focus:border-primary outline-none transition-all bg-white" id="editLevelSchool" required>
                    <option value="">Sélectionner un établissement</option>
                    @foreach($schools ?? [['id' => 1, 'name' => 'saint françois xavier']] as $school)
                    <option value="{{ $school['id'] }}">{{ $school['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="pt-4 flex gap-3">
                <button class="flex-1 py-3 border border-outline text-on-surface-variant font-label-md rounded-lg hover:bg-surface-container-high transition-colors" onclick="toggleModal('edit-level-modal')" type="button">Annuler</button>
                <button class="flex-1 py-3 bg-primary text-on-primary font-label-md rounded-lg hover:opacity-95 transition-opacity shadow-lg shadow-primary/20" type="submit">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    .modal-backdrop {
        background-color: rgba(17, 28, 45, 0.4);
        backdrop-filter: blur(4px);
    }
    .card-shadow {
        box-shadow: 0 4px 12px rgba(55, 48, 163, 0.04);
    }
</style>
@endpush

@push('scripts')
<script>
    function toggleModal(id) {
        const modal = document.getElementById(id);
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        } else {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }

    function openEditModal(id, name, school) {
        document.getElementById('editLevelId').value = id;
        document.getElementById('editLevelName').value = name;
        
        // Sélectionner l'option correspondante dans le select
        const select = document.getElementById('editLevelSchool');
        for(let i = 0; i < select.options.length; i++) {
            if(select.options[i].text.toLowerCase() === school.toLowerCase()) {
                select.selectedIndex = i;
                break;
            }
        }
        
        toggleModal('edit-level-modal');
    }

    // Add Level Form Submission
    const addForm = document.getElementById('addLevelForm');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const levelName = document.getElementById('levelName').value;
            const levelSchool = document.getElementById('levelSchool').options[document.getElementById('levelSchool').selectedIndex]?.text;
            
            Swal.fire({
                title: 'Confirmation',
                text: `Souhaitez-vous ajouter le niveau "${levelName}" à l'établissement "${levelSchool}" ?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Oui, ajouter',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#1f108e',
                cancelButtonColor: '#64748B'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Succès !',
                        text: 'Le niveau a été ajouté avec succès.',
                        icon: 'success',
                        confirmButtonColor: '#1f108e'
                    });
                    toggleModal('add-level-modal');
                    addForm.reset();
                }
            });
        });
    }

    // Edit Level Form Submission
    const editForm = document.getElementById('editLevelForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const levelName = document.getElementById('editLevelName').value;
            
            Swal.fire({
                title: 'Confirmation',
                text: `Souhaitez-vous modifier le niveau "${levelName}" ?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Oui, modifier',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#1f108e',
                cancelButtonColor: '#64748B'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Modifié !',
                        text: 'Le niveau a été modifié avec succès.',
                        icon: 'success',
                        confirmButtonColor: '#1f108e'
                    });
                    toggleModal('edit-level-modal');
                }
            });
        });
    }

    // Delete Logic
    function confirmDelete(id) {
        Swal.fire({
            title: 'Êtes-vous sûr ?',
            text: "Cette action est irréversible et pourrait affecter les données liées.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ba1a1a',
            cancelButtonColor: '#64748B',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Supprimé !',
                    text: "Le niveau a été supprimé avec succès.",
                    icon: 'success',
                    confirmButtonColor: '#1f108e'
                });
            }
        });
    }
</script>
@endpush