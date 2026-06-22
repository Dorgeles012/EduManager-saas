@extends('client.layouts.app')
@section('title', 'EduManager - Matieres')
@section('content')
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
            <p class="text-3xl font-headline-md text-primary">{{ $totalSubjects ?? 0 }}</p>
        </div>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-xl shadow-[4px_4px_12px_rgba(55,48,163,0.04)] border border-outline-variant/30 flex items-center gap-5">
        <div class="w-14 h-14 rounded-full bg-secondary-container/10 flex items-center justify-center text-secondary">
            <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">calculate</span>
        </div>
        <div>
            <p class="text-text-muted text-sm font-medium">Coefficient total</p>
            <p class="text-3xl font-headline-md text-secondary">{{ $totalCoefficient ?? 0 }}</p>
        </div>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-xl shadow-[4px_4px_12px_rgba(55,48,163,0.04)] border border-outline-variant/30 flex items-center gap-5">
        <div class="w-14 h-14 rounded-full bg-warning-amber/10 flex items-center justify-center text-warning-amber">
            <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">person_search</span>
        </div>
        <div>
            <p class="text-text-muted text-sm font-medium">Enseignants à assigner</p>
            <p class="text-3xl font-headline-md text-warning-amber">0</p>
        </div>
    </div>
</div>

<!-- Data Table Container -->
<div class="bg-surface-container-lowest rounded-xl shadow-[0_4px_24px_rgba(55,48,163,0.04)] border border-outline-variant overflow-hidden">
    <div class="px-6 py-5 border-b border-surface-subtle flex justify-between items-center bg-white">
        <h3 class="font-headline-md text-headline-md text-on-surface">Liste des Matières</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left zebra-table">
            <thead>
                <tr class="bg-surface-subtle/50 text-slate-600">
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">N°</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Nom de la matière</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px] text-center">Coefficient</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Statut</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px] text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/20">
                @forelse($subjects ?? [] as $subject)
                <tr class="hover:bg-primary/5 transition-colors group">
                    <td class="px-6 py-4 text-on-surface-variant">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded bg-primary/10 flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-lg">menu_book</span>
                            </div>
                            <span class="font-medium text-on-surface">{{ $subject['name'] }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 bg-surface-container-high text-primary rounded-full font-bold text-xs">{{ $subject['coefficient'] }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-success-green"></div>
                            <span class="text-sm font-medium text-success-green">Active</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <button class="p-2 text-primary hover:bg-primary/10 rounded-lg transition-all" onclick="editMatiere({{ $subject['id'] }}, @js($subject['name']), {{ $subject['coefficient'] }})" title="Modifier">
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
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-on-surface-variant">Aucune matière enregistrée</td>
                </tr>
                @endforelse
                @if(false)
                <!-- Row 1 -->
                <tr class="hover:bg-primary/5 transition-colors group">
                    <td class="px-6 py-4 text-on-surface-variant">01</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded bg-primary/10 flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-lg">menu_book</span>
                            </div>
                            <span class="font-medium text-on-surface">Mathématiques Appliquées</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 bg-surface-container-high text-primary rounded-full font-bold text-xs">5</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-success-green animate-pulse"></div>
                            <span class="text-sm font-medium text-success-green">Active</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <button class="p-2 text-primary hover:bg-primary/10 rounded-lg transition-all" onclick="editMatiere('Mathématiques Appliquées', 5)" title="Modifier">
                                <span class="material-symbols-outlined">edit</span>
                            </button>
                            <button class="p-2 text-alert-red hover:bg-alert-red/10 rounded-lg transition-all" onclick="deleteMatiere('Mathématiques Appliquées')" title="Supprimer">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                <!-- Row 2 -->
                <tr class="hover:bg-primary/5 transition-colors group">
                    <td class="px-6 py-4 text-on-surface-variant">02</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded bg-primary/10 flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-lg">language</span>
                            </div>
                            <span class="font-medium text-on-surface">Langue Française</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 bg-surface-container-high text-primary rounded-full font-bold text-xs">3</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-success-green"></div>
                            <span class="text-sm font-medium text-success-green">Active</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <button class="p-2 text-primary hover:bg-primary/10 rounded-lg transition-all" onclick="editMatiere('Langue Française', 3)" title="Modifier">
                                <span class="material-symbols-outlined">edit</span>
                            </button>
                            <button class="p-2 text-alert-red hover:bg-alert-red/10 rounded-lg transition-all" onclick="deleteMatiere('Langue Française')" title="Supprimer">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                <!-- Row 3 -->
                <tr class="hover:bg-primary/5 transition-colors group">
                    <td class="px-6 py-4 text-on-surface-variant">03</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded bg-primary/10 flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-lg">history_edu</span>
                            </div>
                            <span class="font-medium text-on-surface">Histoire et Géographie</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 bg-surface-container-high text-primary rounded-full font-bold text-xs">3</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-success-green"></div>
                            <span class="text-sm font-medium text-success-green">Active</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <button class="p-2 text-primary hover:bg-primary/10 rounded-lg transition-all" onclick="editMatiere('Histoire et Géographie', 3)" title="Modifier">
                                <span class="material-symbols-outlined">edit</span>
                            </button>
                            <button class="p-2 text-alert-red hover:bg-alert-red/10 rounded-lg transition-all" onclick="deleteMatiere('Histoire et Géographie')" title="Supprimer">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 bg-surface-container-low/30 border-t border-outline-variant flex justify-between items-center text-sm text-text-muted">
        <span>Affichage de 1 à 3 sur 3 matières</span>
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
</style>

<script>
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

        document.querySelectorAll('.delete-subject-btn').forEach((button) => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                if (confirm(`Supprimer la matière "${this.dataset.name}" ?`)) {
                    this.closest('form').submit();
                }
            }, true);
        });
    });

    function editMatiere(id, name, coeff) {
        document.getElementById('editSubjectForm').action = `{{ url('/client/matiere') }}/${id}`;
        document.getElementById('editName').value = name;
        document.getElementById('editCoeff').value = coeff;
        openModal('editModal');
    }

    function deleteMatiere(name) {
        Swal.fire({
            title: 'Êtes-vous sûr?',
            text: `Voulez-vous vraiment supprimer la matière "${name}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1f108e',
            cancelButtonColor: '#E11D48',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler',
            customClass: {
                container: 'font-body-md'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Supprimé!',
                    text: 'La matière a été supprimée avec succès.',
                    icon: 'success',
                    confirmButtonColor: '#1f108e'
                });
            }
        });
    }

    function handleAction(e, type) {
        e.preventDefault();
        const modalId = type === 'add' ? 'addModal' : 'editModal';
        const actionText = type === 'add' ? 'ajoutée' : 'modifiée';
        
        closeModal(modalId);
        
        Swal.fire({
            title: 'Succès!',
            text: `La matière a été ${actionText} avec succès.`,
            icon: 'success',
            confirmButtonColor: '#1f108e',
            timer: 2000
        });
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
    
    // Active Navigation Logic based on Page Intent
    window.addEventListener('DOMContentLoaded', () => {
        // Logic to highlight "Academic" would go here if not already static in HTML
    });
</script>
@endsection
