@extends('personnel.layouts.app')
@section('title', 'EduManager - Classes')
@section('content')
<div class="flex flex-col md:flex-row md:items-end justify-between mb-6 gap-4">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-primary">Gestion des Classes</h2>
        <p class="text-text-muted font-body-md">Gérez les différentes classes de votre établissement</p>
    </div>
    <button class="bg-primary text-on-primary px-6 py-2.5 rounded-lg font-label-md text-label-md flex items-center gap-2 hover:opacity-90 active:scale-95 transition-all card-shadow" onclick="openModal('modal-add')">
        <span class="material-symbols-outlined text-lg">add</span>
        Ajouter une classe
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-surface-container-lowest p-6 rounded-xl custom-shadow border border-outline-variant flex items-center gap-4">
        <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center text-primary">
            <span class="material-symbols-outlined text-2xl">groups</span>
        </div>
        <div>
            <p class="text-label-sm text-text-muted uppercase tracking-wider font-bold">Classes disponibles</p>
            <h3 class="font-headline-md text-headline-md">{{ $totalClasses ?? 0 }}</h3>
        </div>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-xl custom-shadow border border-outline-variant flex items-center gap-4">
        <div class="w-12 h-12 bg-secondary/10 rounded-full flex items-center justify-center text-secondary">
            <span class="material-symbols-outlined text-2xl">account_tree</span>
        </div>
        <div>
            <p class="text-label-sm text-text-muted uppercase tracking-wider font-bold">Niveaux</p>
            <h3 class="font-headline-md text-headline-md">{{ $totalLevels ?? 0 }}</h3>
        </div>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-xl custom-shadow border border-outline-variant flex items-center gap-4">
        <div class="w-12 h-12 bg-warning-amber/10 rounded-full flex items-center justify-center text-warning-amber">
            <span class="material-symbols-outlined text-2xl">location_away</span>
        </div>
        <div>
            <p class="text-label-sm text-text-muted uppercase tracking-wider font-bold">Mon Établissement</p>
            <h3 class="font-headline-md text-headline-md">{{ $schoolName ?? 'Mon Établissement' }}</h3>
        </div>
    </div>
</div>

<div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant overflow-hidden">
    <div class="p-6 border-b border-outline-variant flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <h4 class="font-headline-md text-headline-md flex items-center gap-2">Liste des classes</h4>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-surface-container-low border-b border-outline-variant">
                <tr>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">N°</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Nom de la classe</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Établissement</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Niveau</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Effectif</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px] text-right">Actions</th>
                </tr>
            </thead>    
            <tbody>
                @forelse($classes ?? [] as $class)
                <tr class="border-b border-outline-variant/50 hover:bg-surface-container-low transition-colors">
                    <td class="px-6 py-4 text-body-sm">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-primary-fixed flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-[18px]">class</span>
                            </div>
                            <span class="font-body-md font-medium">{{ $class['name'] }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $class['school'] }}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-label-sm bg-secondary-container/20 text-on-secondary-container">{{ $class['level'] }}</span>
                    </td>
                    <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $class['student_count'] ?? 0 }} élèves</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <button class="p-2 text-primary hover:bg-primary-fixed rounded-lg transition-colors" onclick="openEditModal({{ json_encode($class) }})" title="Modifier">
                                <span class="material-symbols-outlined">edit</span>
                            </button>
                            <form action="{{ route('personnel.classes.destroy', $class['id']) }}" method="POST" class="inline delete-class-form">
                                @csrf
                                @method('DELETE')
                                <button class="p-2 text-alert-red hover:bg-error-container/20 rounded-lg transition-colors delete-class-btn" data-name="{{ $class['name'] }}" title="Supprimer" type="button">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td class="py-10 px-6 text-center" colspan="6">
                        <div class="flex flex-col items-center max-w-xs mx-auto">
                            <div class="w-20 h-20 bg-surface-container rounded-full flex items-center justify-center mb-6">
                                <span class="material-symbols-outlined text-5xl text-outline-variant">co_present</span>
                            </div>
                            <h5 class="font-headline-md text-headline-md text-on-surface mb-2">Aucune classe disponible</h5>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-6 border-t border-outline-variant bg-surface-container-low/30 flex items-center justify-between">
        <span class="text-label-sm text-text-muted">Affichage de 0 à {{ $classes?->count() ?? 0 }} sur {{ $classes?->count() ?? 0 }} entrées</span>
    </div>
</div>

<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="modal-add">
    <div class="absolute inset-0 modal-overlay backdrop-blur-md bg-black/30" onclick="closeModal('modal-add')"></div>
    <div class="relative bg-surface-container-lowest w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="modal-add-content">
        <div class="bg-primary p-6 text-white flex items-center justify-between">
            <div>
                <h3 class="font-headline-md text-headline-md">Ajouter une classe</h3>
                <p class="text-white/80 text-sm">Créez une nouvelle structure pédagogique</p>
            </div>
            <button class="hover:bg-white/10 p-2 rounded-full transition-colors" onclick="closeModal('modal-add')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form class="p-8 space-y-6" id="addClassForm" action="{{ route('personnel.classes.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 gap-6">
                <div class="space-y-1.5">
                    <label class="font-label-md text-label-md text-on-surface-variant">Nom de la classe</label>
                    <input class="w-full bg-white border border-outline-variant rounded-lg px-4 py-3 text-body-md focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none" name="nom" placeholder="Ex: 6ème A, Terminale S1..." type="text" required>
                </div>
                <div class="space-y-1.5">
                    <label class="font-label-md text-label-md text-on-surface-variant">Établissement</label>
                    <select class="w-full bg-white border border-outline-variant rounded-lg px-4 py-3 text-body-md focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none" name="etablissement_id" required>
                        <option value="">Sélectionner un établissement</option>
                        @foreach($schools ?? [['id' => 1, 'name' => 'Mon Établissement Principal']] as $school)
                        <option value="{{ $school['id'] }}">{{ $school['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="font-label-md text-label-md text-on-surface-variant">Niveau</label>
                        <select class="w-full bg-white border border-outline-variant rounded-lg px-4 py-3 text-body-md focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none" name="niveau_id" required>
                            <option value="">Sélectionner un niveau</option>
                            @foreach($levels ?? [['id' => 1, 'name' => 'Primaire'], ['id' => 2, 'name' => 'Collège'], ['id' => 3, 'name' => 'Lycée']] as $level)
                            <option value="{{ $level['id'] }}">{{ $level['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="font-label-md text-label-md text-on-surface-variant">Effectif Max</label>
                        <input class="w-full bg-white border border-outline-variant rounded-lg px-4 py-3 text-body-md focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none" name="capacite" placeholder="40" type="number">
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-outline-variant">
                <button class="px-6 py-2.5 rounded-lg font-label-md text-on-surface-variant hover:bg-surface-container-low transition-colors" onclick="closeModal('modal-add')" type="button">Annuler</button>
                <button class="bg-primary text-white px-8 py-2.5 rounded-lg font-label-md hover:bg-primary/90 shadow-md" type="submit">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="modal-edit">
    <div class="absolute inset-0 modal-overlay backdrop-blur-md bg-black/30" onclick="closeModal('modal-edit')"></div>
    <div class="relative bg-surface-container-lowest w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="modal-edit-content">
        <div class="bg-warning-amber p-6 text-white flex items-center justify-between">
            <div>
                <h3 class="font-headline-md text-headline-md">Modifier la classe</h3>
                <p class="text-white/80 text-sm">Mise à jour des informations de classe</p>
            </div>
            <button class="hover:bg-white/10 p-2 rounded-full transition-colors" onclick="closeModal('modal-edit')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form class="p-8 space-y-6" id="editClassForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="editClassId">
            <div class="grid grid-cols-1 gap-6">
                <div class="space-y-1.5">
                    <label class="font-label-md text-label-md text-on-surface-variant">Nom de la classe</label>
                    <input class="w-full bg-white border border-outline-variant rounded-lg px-4 py-3 text-body-md focus:border-warning-amber focus:ring-4 focus:ring-warning-amber/10 transition-all outline-none" id="editClassName" name="nom" type="text" required>
                </div>
                <div class="space-y-1.5">
                    <label class="font-label-md text-label-md text-on-surface-variant">Établissement</label>
                    <select class="w-full bg-white border border-outline-variant rounded-lg px-4 py-3 text-body-md focus:border-warning-amber focus:ring-4 focus:ring-warning-amber/10 transition-all outline-none" id="editClassSchool" name="etablissement_id" required>
                        @foreach($schools ?? [['id' => 1, 'name' => 'Mon Établissement Principal']] as $school)
                        <option value="{{ $school['id'] }}">{{ $school['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="font-label-md text-label-md text-on-surface-variant">Niveau</label>
                        <select class="w-full bg-white border border-outline-variant rounded-lg px-4 py-3 text-body-md focus:border-warning-amber focus:ring-4 focus:ring-warning-amber/10 transition-all outline-none" id="editClassLevel" name="niveau_id" required>
                            @foreach($levels ?? [['id' => 1, 'name' => 'Primaire'], ['id' => 2, 'name' => 'Collège'], ['id' => 3, 'name' => 'Lycée']] as $level)
                            <option value="{{ $level['id'] }}">{{ $level['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="font-label-md text-label-md text-on-surface-variant">Effectif Max</label>
                        <input class="w-full bg-white border border-outline-variant rounded-lg px-4 py-3 text-body-md focus:border-warning-amber focus:ring-4 focus:ring-warning-amber/10 transition-all outline-none" id="editClassMaxStudents" name="capacite" type="number">
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-outline-variant">
                <button class="px-6 py-2.5 rounded-lg font-label-md text-on-surface-variant hover:bg-surface-container-low transition-colors" onclick="closeModal('modal-edit')" type="button">Annuler</button>
                <button class="bg-warning-amber text-white px-8 py-2.5 rounded-lg font-label-md hover:bg-warning-amber/90 shadow-md" type="submit">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    body { font-family: 'Inter', sans-serif; background-color: #f9f9ff; }
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    .custom-shadow { box-shadow: 0 4px 12px rgba(55, 48, 163, 0.04); }
    .modal-overlay { transition: backdrop-filter 0.3s ease; }
    #modal-add, #modal-edit { transition: opacity 0.3s ease; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            Swal.fire({ icon: 'success', title: 'Succès', text: @json(session('success')), timer: 2000, showConfirmButton: false });
        @endif

        @if(session('error'))
            Swal.fire({ icon: 'error', title: 'Erreur', text: @json(session('error')), timer: 3000, showConfirmButton: false });
        @endif

        document.querySelectorAll('.delete-class-btn').forEach((button) => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                Swal.fire({
                    title: 'Êtes-vous sûr ?',
                    text: `La classe "${this.dataset.name}" sera définitivement supprimée.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ba1a1a',
                    cancelButtonColor: '#64748B',
                    confirmButtonText: 'Oui, supprimer',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    });

    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        const contentId = modalId + '-content';
        const content = document.getElementById(contentId);
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        const contentId = modalId + '-content';
        const content = document.getElementById(contentId);
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }, 300);
    }

    function openEditModal(classData) {
        document.getElementById('editClassForm').action = `{{ url('/personnel/classes') }}/${classData.id}`;
        document.getElementById('editClassId').value = classData.id;
        document.getElementById('editClassName').value = classData.name;
        document.getElementById('editClassMaxStudents').value = classData.max_students || '';
        document.getElementById('editClassSchool').value = classData.school_id;
        document.getElementById('editClassLevel').value = classData.level_id;
        openModal('modal-edit');
    }
</script>
@endpush
