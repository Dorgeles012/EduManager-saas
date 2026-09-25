@extends('client.layouts.app')
@section('title', 'EduManager - Classe')
@section('content')

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Gestion des Classes</h2>
        <p class="text-sm text-gray-500 mt-1">Gérez les différentes classes de votre établissement</p>
    </div>
    <button onclick="openModal('modal-add')" class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-xs font-semibold transition shadow-sm">
        <span class="material-symbols-outlined text-sm">add</span>
        Ajouter une classe
    </button>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mb-6">
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                <span class="material-symbols-outlined text-lg">groups</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Total</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $totalClasses ?? 0 }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Classes disponibles</p>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                <span class="material-symbols-outlined text-lg">account_tree</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Niveaux</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $totalLevels ?? 0 }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Niveaux actifs</p>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow col-span-2 lg:col-span-1">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                <span class="material-symbols-outlined text-lg">domain</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Établissement</span>
        </div>
        <h3 class="text-base sm:text-lg font-bold text-gray-900 truncate">{{ $schoolName ?? 'Mon Établissement' }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Établissement actif</p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
            <span class="material-symbols-outlined text-base">list_alt</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-900">Liste des classes</h3>
            <p class="text-[11px] text-gray-500">{{ $classes->count() }} classe(s) enregistrée(s)</p>
        </div>
    </div>

    <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">N°</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Nom</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Établissement</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Niveau</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider text-center">Effectif</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($classes ?? [] as $class)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-4 py-3 text-xs text-gray-500 font-semibold">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <span class="text-xs rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 text-[11px] font-semibold text-gray-900">{{ $class['name'] }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-700">{{ $class['school'] }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-100 text-[10px] font-bold text-gray-700 uppercase">
                            {{ $class['level'] }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-xs font-semibold text-gray-900">{{ $class['student_count'] ?? 0 }}</span>
                        <span class="text-[10px] text-gray-400">élèves</span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-1.5">
                            <button onclick='openEditModal(@json($class))'
                                    class="w-8 h-8 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-600 flex items-center justify-center transition"
                                    title="Modifier">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </button>
                            <form action="{{ route('client.classe.destroy', $class['id']) }}" method="POST" class="inline delete-class-form">
                                @csrf @method('DELETE')
                                <button type="button"
                                        data-name="{{ $class['name'] }}"
                                        class="delete-class-btn w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition"
                                        title="Supprimer">
                                    <span class="material-symbols-outlined text-base">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-16 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center mb-3">
                                <span class="material-symbols-outlined text-2xl text-gray-300">co_present</span>
                            </div>
                            <p class="text-sm font-semibold text-gray-700">Aucune classe disponible</p>
                            <p class="text-xs text-gray-400 mt-1">Commencez par ajouter votre première classe.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="md:hidden divide-y divide-gray-100">
        @forelse($classes ?? [] as $class)
        <div class="p-4 space-y-3">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-sm font-bold flex-shrink-0">
                        {{ strtoupper(substr($class['name'], 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $class['name'] }}</p>
                        <p class="text-[11px] text-gray-500 truncate">{{ $class['school'] }}</p>
                    </div>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-100 text-[10px] font-bold text-gray-700 uppercase flex-shrink-0">
                    {{ $class['level'] }}
                </span>
            </div>

            <div class="grid grid-cols-2 gap-2 py-2">
                <div>
                    <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Effectif</p>
                    <p class="text-sm font-bold text-gray-900 mt-0.5">{{ $class['student_count'] ?? 0 }} élèves</p>
                </div>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <button onclick='openEditModal(@json($class))'
                        class="flex-1 h-9 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold flex items-center justify-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-sm">edit</span>
                    Modifier
                </button>
                <form action="{{ route('client.classe.destroy', $class['id']) }}" method="POST" class="flex-1 delete-class-form">
                    @csrf @method('DELETE')
                    <button type="button"
                            data-name="{{ $class['name'] }}"
                            class="delete-class-btn w-full h-9 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-semibold flex items-center justify-center gap-1.5 transition">
                        <span class="material-symbols-outlined text-sm">delete</span>
                        Supprimer
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="py-16 text-center">
            <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-2xl text-gray-300">co_present</span>
            </div>
            <p class="text-sm font-semibold text-gray-700">Aucune classe disponible</p>
            <p class="text-xs text-gray-400 mt-1">Commencez par ajouter votre première classe.</p>
        </div>
        @endforelse
    </div>

    @if(isset($classes) && method_exists($classes, 'links'))
    <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between">
        <span class="text-[11px] text-gray-500">
            {{ $classes->firstItem() ?? 0 }} - {{ $classes->lastItem() ?? 0 }} sur {{ $classes->count() }}
        </span>
        <div class="text-xs">
            {{ $classes->links() ?? '' }}
        </div>
    </div>
    @endif
</div>

<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="modal-add">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('modal-add')"></div>
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0 relative z-10 max-h-[90vh] flex flex-col" id="modal-add-content">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center flex-shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <span class="material-symbols-outlined text-base">add_circle</span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Ajouter une classe</h3>
                    <p class="text-[11px] text-gray-500">Créez une nouvelle structure pédagogique</p>
                </div>
            </div>
            <button onclick="closeModal('modal-add')" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center transition">
                <span class="material-symbols-outlined text-gray-500">close</span>
            </button>
        </div>

        <form class="p-5 space-y-4 overflow-y-auto flex-1" action="{{ route('client.classe.store') }}" method="POST" id="addClassForm">
            @csrf
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Nom de la classe</label>
                <input type="text" name="nom" id="className" required placeholder="Ex: 6ème A, Terminale S1..."
                       class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
            </div>

            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Établissement</label>
                <select name="etablissement_id" id="classSchool" required
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                    <option value="">Sélectionner un établissement</option>
                    @foreach($schools ?? [['id' => 1, 'name' => 'Mon Établissement Principal']] as $school)
                    <option value="{{ $school['id'] }}">{{ $school['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Niveau</label>
                    <select name="niveau_id" id="classLevel" required
                            class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                        <option value="">Sélectionner</option>
                        @foreach($levels ?? [['id' => 1, 'name' => 'Primaire'], ['id' => 2, 'name' => 'Collège'], ['id' => 3, 'name' => 'Lycée']] as $level)
                        <option value="{{ $level['id'] }}">{{ $level['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Effectif max</label>
                    <input type="number" name="capacite" id="classMaxStudents" placeholder="40"
                           class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                <button type="button" onclick="closeModal('modal-add')"
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

<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="modal-edit">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('modal-edit')"></div>
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0 relative z-10 max-h-[90vh] flex flex-col" id="modal-edit-content">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center flex-shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                    <span class="material-symbols-outlined text-base">edit</span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Modifier la classe</h3>
                    <p class="text-[11px] text-gray-500">Mise à jour des informations</p>
                </div>
            </div>
            <button onclick="closeModal('modal-edit')" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center transition">
                <span class="material-symbols-outlined text-gray-500">close</span>
            </button>
        </div>

        <form class="p-5 space-y-4 overflow-y-auto flex-1" action="" method="POST" id="editClassForm">
            @csrf @method('PUT')
            <input type="hidden" id="editClassId">
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Nom de la classe</label>
                <input type="text" name="nom" id="editClassName" required
                       class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition">
            </div>

            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Établissement</label>
                <select name="etablissement_id" id="editClassSchool" required
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition">
                    @foreach($schools ?? [['id' => 1, 'name' => 'Mon Établissement Principal']] as $school)
                    <option value="{{ $school['id'] }}">{{ $school['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Niveau</label>
                    <select name="niveau_id" id="editClassLevel" required
                            class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition">
                        @foreach($levels ?? [['id' => 1, 'name' => 'Primaire'], ['id' => 2, 'name' => 'Collège'], ['id' => 3, 'name' => 'Lycée']] as $level)
                        <option value="{{ $level['id'] }}">{{ $level['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Effectif max</label>
                    <input type="number" name="capacite" id="editClassMaxStudents"
                           class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition">
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                <button type="button" onclick="closeModal('modal-edit')"
                        class="px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    Annuler
                </button>
                <button type="submit"
                        class="bg-amber-600 hover:bg-amber-700 text-white px-5 py-2 rounded-lg text-xs font-semibold transition shadow-sm">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
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

    @if(session('success'))
        Swal.fire({ ...swalConfig, icon: 'success', title: 'Succès', text: @json(session('success')), timer: 2500, showConfirmButton: false });
    @endif
    @if(session('error'))
        Swal.fire({ ...swalConfig, icon: 'error', title: 'Erreur', text: @json(session('error')), confirmButtonText: 'OK' });
    @endif

    function openModal(id) {
        const modal = document.getElementById(id);
        const content = document.getElementById(id + '-content');
        modal.classList.remove('hidden'); modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        setTimeout(() => { content.classList.remove('scale-95', 'opacity-0'); content.classList.add('scale-100', 'opacity-100'); }, 10);
    }
    function closeModal(id) {
        const modal = document.getElementById(id);
        const content = document.getElementById(id + '-content');
        content.classList.remove('scale-100', 'opacity-100'); content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => { modal.classList.remove('flex'); modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
    }

    function openEditModal(classData) {
        document.getElementById('editClassForm').action = `{{ url('/client/classe') }}/${classData.id}`;
        document.getElementById('editClassId').value = classData.id;
        document.getElementById('editClassName').value = classData.name;
        document.getElementById('editClassMaxStudents').value = classData.max_students || '';
        document.getElementById('editClassSchool').value = classData.school_id;
        document.getElementById('editClassLevel').value = classData.level_id;
        openModal('modal-edit');
    }

    document.querySelectorAll('.delete-class-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            Swal.fire({
                ...swalConfig,
                title: 'Supprimer cette classe ?',
                html: `La classe <strong class="text-rose-600">"${this.dataset.name}"</strong> sera définitivement supprimée.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
                iconColor: '#e11d48'
            }).then(r => { if (r.isConfirmed) form.submit(); });
        });
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') ['modal-add', 'modal-edit'].forEach(id => {
            const m = document.getElementById(id);
            if (m && m.classList.contains('flex')) closeModal(id);
        });
    });
</script>
@endpush