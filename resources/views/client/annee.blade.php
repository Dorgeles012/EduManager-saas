@extends('client.layouts.app')
@section('title', 'EduManager - Année académique')
@section('content')

<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-primary">Gestion des Années Académiques</h2>
        <p class="font-body-md text-text-muted mt-1">Gérez les années scolaires et universitaires</p>
    </div>
        <button type="button" onclick="openAddModal()" class="bg-primary text-on-primary px-6 py-2.5 rounded-lg font-label-md text-label-md flex items-center gap-2 hover:opacity-90 active:scale-95 transition-all card-shadow">
            <span class="material-symbols-outlined">add</span>
            Ajouter une année
        </button>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-gutter-desktop mb-10">
    <div class="bg-surface-container-lowest p-6 rounded-xl shadow-[4px_4px_12px_rgba(55,48,163,0.04)] border border-outline-variant/30 flex items-center gap-5">
        <div class="w-12 h-12 rounded-full bg-primary-fixed flex items-center justify-center">
            <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">calendar_month</span>
        </div>
        <div>
            <p class="font-label-sm text-text-muted uppercase tracking-wider">Années enregistrées</p>
            <h3 class="font-headline-md text-headline-md text-on-surface">{{ $totalYears ?? 0 }}</h3>
        </div>
    </div>

    <div class="bg-surface-container-lowest p-6 rounded-xl shadow-[4px_4px_12px_rgba(55,48,163,0.04)] border border-outline-variant/30 flex items-center gap-5 opacity-40">
        <div class="w-12 h-12 rounded-full bg-secondary-container/30 flex items-center justify-center">
            <span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1;">update</span>
        </div>
        <div>
            <p class="font-label-sm text-text-muted uppercase tracking-wider">Ajouts récents</p>
            <h3 class="font-headline-md text-headline-md text-on-surface">{{ $academicYears?->count() ?? 0 }}</h3>
        </div>
    </div>

    <div class="bg-surface-container-lowest p-6 rounded-xl shadow-[4px_4px_12px_rgba(55,48,163,0.04)] border border-outline-variant/30 flex items-center gap-5 opacity-40">
        <div class="w-12 h-12 rounded-full bg-surface-subtle flex items-center justify-center">
            <span class="material-symbols-outlined text-outline">history</span>
        </div>
        <div>
            <p class="font-label-sm text-text-muted uppercase tracking-wider">Archives</p>
            <h3 class="font-headline-md text-headline-md text-on-surface">{{ $academicYears?->where('statut','inactive')->count() ?? 0 }}</h3>
        </div>
    </div>

    <div class="bg-surface-container-lowest p-6 rounded-xl shadow-[4px_4px_12px_rgba(55,48,163,0.04)] border border-outline-variant/30 flex items-center gap-5">
        <div class="w-12 h-12 rounded-full bg-secondary-container/30 flex items-center justify-center">
            <span class="material-symbols-outlined text-secondary">check_circle</span>
        </div>
        <div>
            <p class="font-label-sm text-text-muted uppercase tracking-wider">Statut Actif</p>
            <h3 class="font-headline-md text-headline-md text-on-surface">{{ $activeYear ?? '—' }}</h3>
        </div>
    </div>
</div>

<!-- Modal Ajouter -->
<div id="addModal" class="fixed inset-0 bg-black/60 backdrop-blur-md flex items-center justify-center z-50 hidden transition-all duration-300">
    <div class="bg-surface-container-lowest rounded-xl shadow-2xl w-full max-w-2xl mx-4 transform transition-all duration-300 scale-95 opacity-0" id="addModalContent">
        <div class="p-6 border-b border-surface-subtle flex justify-between items-center">
            <h4 class="font-headline-md text-headline-md text-on-surface">Ajouter une année académique</h4>
            <button onclick="closeAddModal()" class="p-2 hover:bg-surface-subtle rounded-lg transition-all">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('client.annee.store') }}" id="addForm">
                @csrf
                <div>
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-2" for="libelle">Libellé</label>
                    <input type="text" name="libelle" id="libelle" value="{{ old('libelle') }}" placeholder="Ex: 2025-2026" required class="w-full px-4 py-3 bg-white border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none transition-all font-body-md" />
                    <p class="text-label-sm text-text-muted mt-2">Utilisez le format AAAA-AAAA (ex: 2025-2026).</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-2" for="date_debut">Date début</label>
                        <input type="date" name="date_debut" id="date_debut" value="{{ old('date_debut') }}" class="w-full px-4 py-3 bg-white border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none transition-all font-body-md" />
                    </div>
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-2" for="date_fin">Date fin</label>
                        <input type="date" name="date_fin" id="date_fin" value="{{ old('date_fin') }}" class="w-full px-4 py-3 bg-white border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none transition-all font-body-md" />
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-2" for="statut">Statut</label>
                    <select name="statut" id="statut" class="w-full px-4 py-3 bg-white border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none transition-all font-body-md">
                        <option value="active">active</option>
                        <option value="inactive" selected>inactive</option>
                    </select>
                    <p class="text-label-sm text-text-muted mt-2">Règle: une seule année peut être « active » à la fois.</p>
                </div>

                <div class="flex gap-3 pt-6">
                    <button type="button" onclick="closeAddModal()" class="flex-1 px-4 py-3 border border-outline-variant text-on-surface-variant rounded-lg font-label-md hover:bg-surface-subtle transition-all">Annuler</button>
                    <button type="submit" class="flex-1 px-4 py-3 bg-primary text-white rounded-lg font-label-md hover:shadow-lg transition-all active:scale-95">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modifier -->
<div id="editModal" class="fixed inset-0 bg-black/60 backdrop-blur-md flex items-center justify-center z-50 hidden transition-all duration-300">
    <div class="bg-surface-container-lowest rounded-xl shadow-2xl w-full max-w-2xl mx-4 transform transition-all duration-300 scale-95 opacity-0" id="editModalContent">
        <div class="p-6 border-b border-surface-subtle flex justify-between items-center">
            <h4 class="font-headline-md text-headline-md text-on-surface">Modifier l'année académique</h4>
            <button onclick="closeEditModal()" class="p-2 hover:bg-surface-subtle rounded-lg transition-all">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div class="p-6">
            <form method="POST" action="" id="editForm">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-2" for="edit_libelle">Libellé</label>
                    <input type="text" name="libelle" id="edit_libelle" placeholder="Ex: 2026-2027" required class="w-full px-4 py-3 bg-white border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none transition-all font-body-md" />
                    <p class="text-label-sm text-text-muted mt-2">Utilisez le format AAAA-AAAA (ex: 2025-2026).</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-2" for="edit_date_debut">Date début</label>
                        <input type="date" name="date_debut" id="edit_date_debut" class="w-full px-4 py-3 bg-white border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none transition-all font-body-md" />
                    </div>
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-2" for="edit_date_fin">Date fin</label>
                        <input type="date" name="date_fin" id="edit_date_fin" class="w-full px-4 py-3 bg-white border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none transition-all font-body-md" />
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-2" for="edit_statut">Statut</label>
                    <select name="statut" id="edit_statut" class="w-full px-4 py-3 bg-white border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none transition-all font-body-md">
                        <option value="active">active</option>
                        <option value="inactive">inactive</option>
                    </select>
                    <p class="text-label-sm text-text-muted mt-2">Règle: une seule année peut être « active » à la fois.</p>
                </div>

                <div class="flex gap-3 pt-6">
                    <button type="button" onclick="closeEditModal()" class="flex-1 px-4 py-3 border border-outline-variant text-on-surface-variant rounded-lg font-label-md hover:bg-surface-subtle transition-all">Annuler</button>
                    <button type="submit" class="flex-1 px-4 py-3 bg-primary text-white rounded-lg font-label-md hover:shadow-lg transition-all active:scale-95">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Tableau principal -->
<div class="bg-surface-container-lowest rounded-xl shadow-[4px_4px_12px_rgba(55,48,163,0.04)] border border-outline-variant/30 overflow-hidden">
    <div class="p-6 border-b border-surface-subtle flex justify-between items-center">
        <h4 class="font-headline-md text-headline-md text-on-surface">Liste des Années Académiques</h4>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-subtle/50">
                    <th class="px-6 py-4 font-label-sm text-label-sm text-text-muted uppercase tracking-wider">N°</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-text-muted uppercase tracking-wider">Libellé de l'année</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-text-muted uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-text-muted uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-subtle">
                @forelse($academicYears ?? collect() as $year)
                    <tr class="hover:bg-surface-subtle/20 transition-colors" id="row-{{ $year->id }}">
                        <td class="px-6 py-4 font-body-sm text-body-sm text-on-surface">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded bg-primary-fixed flex items-center justify-center">
                                    <span class="material-symbols-outlined text-primary text-[18px]">calendar_today</span>
                                </div>
                                <span class="font-label-md text-label-md text-on-surface">{{ $year->libelle }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php $isActive = $year->statut === 'active'; @endphp
                            <span class="px-3 py-1 {{ $isActive ? 'bg-green-500/20 text-green-700' : 'bg-gray-500/20 text-gray-600' }} text-[12px] font-bold rounded-full">
                                {{ $isActive ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button type="button" onclick="openEditModal({{ $year->id }})" class="p-2 text-primary hover:bg-primary-fixed rounded-lg transition-all" title="Modifier">
                                    <span class="material-symbols-outlined">edit</span>
                                </button>
                                <form action="{{ route('client.annee.destroy', $year->id) }}" method="POST" id="deleteForm{{ $year->id }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete({{ $year->id }}, '{{ addslashes($year->libelle) }}')" class="p-2 text-red-500 hover:bg-red-100 rounded-lg transition-all" title="Supprimer">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-48 h-48 bg-surface-subtle rounded-full flex items-center justify-center mb-6">
                                    <span class="material-symbols-outlined text-[80px] text-outline-variant">calendar_add_on</span>
                                </div>
                                <h5 class="font-headline-md text-headline-md text-on-surface">Aucune donnée trouvée</h5>
                                <p class="text-text-muted mt-2 max-w-sm">Commencez par ajouter votre première année académique.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

    // DONNÉES

    let academicYearsData = @json($academicYears ?? collect());


    // MODAL AJOUT

    function openAddModal() {
        const modal = document.getElementById('addModal');
        const modalContent = document.getElementById('addModalContent');
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
        document.body.style.overflow = 'hidden';
    }
    
    function closeAddModal() {
        const modal = document.getElementById('addModal');
        const modalContent = document.getElementById('addModalContent');
        
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }, 300);
    }


    // MODAL MODIFICATION

    function openEditModal(id) {
        const year = academicYearsData.find(y => y.id === id);
        
        if (year) {
            const form = document.getElementById('editForm');
            form.action = "{{ url('client/annee') }}/" + id;
            
            document.getElementById('edit_libelle').value = year.libelle || '';
            document.getElementById('edit_date_debut').value = year.date_debut || '';
            document.getElementById('edit_date_fin').value = year.date_fin || '';
            document.getElementById('edit_statut').value = year.statut || 'inactive';
            
            const modal = document.getElementById('editModal');
            const modalContent = document.getElementById('editModalContent');
            
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
            document.body.style.overflow = 'hidden';
        }
    }
    
    function closeEditModal() {
        const modal = document.getElementById('editModal');
        const modalContent = document.getElementById('editModalContent');
        
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }, 300);
    }


    // FERMETURE PAR CLIC EXTÉRIEUR

    document.getElementById('addModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeAddModal();
    });
    document.getElementById('editModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeEditModal();
    });


    // SWEET ALERT - SUPPRESSION

    function confirmDelete(id, libelle) {
        Swal.fire({
            title: 'Confirmation de suppression',
            html: `
                <div class="text-center">
                    <p>Vous êtes sur le point de supprimer :</p>
                    <p class="font-bold text-red-600 my-2">"${libelle}"</p>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm' + id);
                if (form) {
                    Swal.fire({
                        title: 'Suppression en cours...',
                        text: 'Veuillez patienter',
                        icon: 'info',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                    form.submit();
                }
            }
        });
    }


    // SWEET ALERT - AJOUT RÉUSSI

    @if(session('success') && (str_contains(session('success'), 'ajoutée') || str_contains(session('success'), 'Ajoutée')))
        Swal.fire({
            title: 'Ajout réussie !',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonColor: '#4f46e5',
            confirmButtonText: 'OK',
            timer: 3000,
        });
    @endif


    // SWEET ALERT - MODIFICATION RÉUSSIE

    @if(session('success') && (str_contains(session('success'), 'modifiée') || str_contains(session('success'), 'Modifiée')))
        Swal.fire({
            title: '✓ Modification réussie !',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonColor: '#4f46e5',
            confirmButtonText: 'OK',
            timer: 3000,
        });
    @endif


    // SWEET ALERT - SUPPRESSION RÉUSSIE

    @if(session('success') && (str_contains(session('success'), 'supprimée') || str_contains(session('success'), 'Supprimée')))
        Swal.fire({
            title: 'Suppression réussie !',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonColor: '#4f46e5',
            confirmButtonText: 'OK',
            timer: 3000,

        });
    @endif


    // SWEET ALERT - ERREURS DE VALIDATION

    @if($errors->any())
        Swal.fire({
            title: 'Erreur de validation',
            html: `
                <div class="text-left max-h-60 overflow-y-auto">
                    <ul class="list-disc pl-5 text-red-600">
                        @foreach($errors->all() as $error)
                            <li class="mb-1">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            `,
            icon: 'error',
            confirmButtonColor: '#4f46e5',
            confirmButtonText: 'Fermer',
            customClass: {
                popup: 'rounded-xl',
                confirmButton: 'px-4 py-2 rounded-lg font-medium'
            }
        });
        openAddModal();
    @endif


    // SWEET ALERT - ERREURS GÉNÉRIQUES

    @if(session('error'))
        Swal.fire({
            title: 'Erreur !',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonColor: '#4f46e5',
            confirmButtonText: 'OK'
        });
    @endif
</script>

<style>
    /* Animation personnalisée pour les SweetAlert */
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translate3d(0, -20px, 0);
        }
        to {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }
    }
    .animate__animated {
        animation-duration: 0.3s;
    }
    .animate__fadeInDown {
        animation-name: fadeInDown;
    }
    .animate__fadeOutUp {
        animation-name: fadeInDown;
        animation-direction: reverse;
    }
</style>

@endsection