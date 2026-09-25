@extends('client.layouts.app')
@section('title', 'EduManager - Année académique')
@section('content')

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Années Académiques</h2>
        <p class="text-sm text-gray-500 mt-1">Gérez les années scolaires et universitaires</p>
    </div>
    <button type="button" onclick="openAddModal()" class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-xs font-semibold transition shadow-sm">
        <span class="material-symbols-outlined text-sm">add</span>
        Ajouter une année
    </button>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                <span class="material-symbols-outlined text-lg">calendar_month</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Total</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $totalYears ?? 0 }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Enregistrées</p>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                <span class="material-symbols-outlined text-lg">update</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Récent</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $academicYears?->count() ?? 0 }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Ajouts récents</p>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-gray-50 flex items-center justify-center text-gray-500">
                <span class="material-symbols-outlined text-lg">history</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Archives</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $academicYears?->where('statut','inactive')->count() ?? 0 }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Inactives</p>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                <span class="material-symbols-outlined text-lg">check_circle</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Actif</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold text-gray-900 truncate">{{ $activeYear ?? '—' }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Année en cours</p>
    </div>
</div>

<div id="addModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 transform transition-all duration-300 scale-95 opacity-0 max-h-[90vh] flex flex-col" id="addModalContent">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center flex-shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <span class="material-symbols-outlined text-base">calendar_add_on</span>
                </div>
                <h4 class="text-base font-bold text-gray-900">Ajouter une année académique</h4>
            </div>
            <button onclick="closeAddModal()" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center transition">
                <span class="material-symbols-outlined text-gray-500">close</span>
            </button>
        </div>

        <div class="p-5 overflow-y-auto flex-1">
            <form method="POST" action="{{ route('client.annee.store') }}" id="addForm">
                @csrf
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider" for="libelle">Libellé</label>
                    <input type="text" name="libelle" id="libelle" value="{{ old('libelle') }}" placeholder="Ex: 2025-2026" required class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition" />
                    <p class="text-[11px] text-gray-400 mt-1.5">Utilisez le format AAAA-AAAA (ex: 2025-2026).</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4">
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider" for="date_debut">Date début</label>
                        <input type="date" name="date_debut" id="date_debut" value="{{ old('date_debut') }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition" />
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider" for="date_fin">Date fin</label>
                        <input type="date" name="date_fin" id="date_fin" value="{{ old('date_fin') }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition" />
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider" for="statut">Statut</label>
                    <select name="statut" id="statut" class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                        <option value="active">Active</option>
                        <option value="inactive" selected>Inactive</option>
                    </select>
                    <p class="text-[11px] text-gray-400 mt-1.5">Une seule année peut être « active » à la fois.</p>
                </div>

                <div class="flex gap-2 pt-5">
                    <button type="button" onclick="closeAddModal()" class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-700 rounded-lg text-xs font-semibold hover:bg-gray-50 transition">Annuler</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold transition shadow-sm">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="editModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 transform transition-all duration-300 scale-95 opacity-0 max-h-[90vh] flex flex-col" id="editModalContent">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center flex-shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                    <span class="material-symbols-outlined text-base">edit_calendar</span>
                </div>
                <h4 class="text-base font-bold text-gray-900">Modifier l'année académique</h4>
            </div>
            <button onclick="closeEditModal()" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center transition">
                <span class="material-symbols-outlined text-gray-500">close</span>
            </button>
        </div>

        <div class="p-5 overflow-y-auto flex-1">
            <form method="POST" action="" id="editForm">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider" for="edit_libelle">Libellé</label>
                    <input type="text" name="libelle" id="edit_libelle" placeholder="Ex: 2026-2027" required class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition" />
                    <p class="text-[11px] text-gray-400 mt-1.5">Utilisez le format AAAA-AAAA (ex: 2025-2026).</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4">
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider" for="edit_date_debut">Date début</label>
                        <input type="date" name="date_debut" id="edit_date_debut" class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition" />
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider" for="edit_date_fin">Date fin</label>
                        <input type="date" name="date_fin" id="edit_date_fin" class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition" />
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider" for="edit_statut">Statut</label>
                    <select name="statut" id="edit_statut" class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <p class="text-[11px] text-gray-400 mt-1.5">Une seule année peut être « active » à la fois.</p>
                </div>

                <div class="flex gap-2 pt-5">
                    <button type="button" onclick="closeEditModal()" class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-700 rounded-lg text-xs font-semibold hover:bg-gray-50 transition">Annuler</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold transition shadow-sm">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
            <span class="material-symbols-outlined text-base">list_alt</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-900">Liste des Années Académiques</h3>
            <p class="text-[11px] text-gray-500">{{ $academicYears?->count() ?? 0 }} année(s) enregistrée(s)</p>
        </div>
    </div>

    <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">N°</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Libellé</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Période</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($academicYears ?? collect() as $year)
                    @php $isActive = $year->statut === 'active'; @endphp
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-4 py-3 text-xs text-gray-500 font-semibold">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 flex-shrink-0">
                                    <span class="material-symbols-outlined text-sm">calendar_today</span>
                                </div>
                                <span class="text-xs font-semibold text-gray-900">{{ $year->libelle }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">
                            @if($year->date_debut && $year->date_fin)
                                {{ \Carbon\Carbon::parse($year->date_debut)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($year->date_fin)->format('d/m/Y') }}
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($isActive)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <button type="button" onclick="openEditModal({{ $year->id }})" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center transition" title="Modifier">
                                    <span class="material-symbols-outlined text-base">edit</span>
                                </button>
                                <form action="{{ route('client.annee.destroy', $year->id) }}" method="POST" id="deleteForm{{ $year->id }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete({{ $year->id }}, '{{ addslashes($year->libelle) }}')" class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition" title="Supprimer">
                                        <span class="material-symbols-outlined text-base">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center mb-3">
                                    <span class="material-symbols-outlined text-2xl text-gray-300">calendar_add_on</span>
                                </div>
                                <p class="text-sm font-semibold text-gray-700">Aucune année enregistrée</p>
                                <p class="text-xs text-gray-400 mt-1">Commencez par ajouter votre première année académique.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="md:hidden divide-y divide-gray-100">
        @forelse($academicYears ?? collect() as $year)
            @php $isActive = $year->statut === 'active'; @endphp
            <div class="p-4 space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 flex-shrink-0">
                            <span class="material-symbols-outlined text-base">calendar_today</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $year->libelle }}</p>
                            <p class="text-[11px] text-gray-500 truncate">
                                @if($year->date_debut && $year->date_fin)
                                    {{ \Carbon\Carbon::parse($year->date_debut)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($year->date_fin)->format('d/m/Y') }}
                                @else
                                    Aucune période définie
                                @endif
                            </p>
                        </div>
                    </div>
                    @if($isActive)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 flex-shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-600 flex-shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                            Inactive
                        </span>
                    @endif
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <button type="button" onclick="openEditModal({{ $year->id }})" class="flex-1 h-9 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold flex items-center justify-center gap-1.5 transition">
                        <span class="material-symbols-outlined text-sm">edit</span>
                        Modifier
                    </button>
                    <form action="{{ route('client.annee.destroy', $year->id) }}" method="POST" id="deleteFormMobile{{ $year->id }}" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confirmDeleteMobile({{ $year->id }}, '{{ addslashes($year->libelle) }}')" class="w-full h-9 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-semibold flex items-center justify-center gap-1.5 transition">
                            <span class="material-symbols-outlined text-sm">delete</span>
                            Supprimer
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="py-16 text-center">
                <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-3">
                    <span class="material-symbols-outlined text-2xl text-gray-300">calendar_add_on</span>
                </div>
                <p class="text-sm font-semibold text-gray-700">Aucune année enregistrée</p>
                <p class="text-xs text-gray-400 mt-1">Commencez par ajouter votre première année académique.</p>
            </div>
        @endforelse
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let academicYearsData = @json($academicYears ?? collect());

    function openAddModal() {
        const modal = document.getElementById('addModal');
        const modalContent = document.getElementById('addModalContent');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
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
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }, 300);
    }

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
            modal.classList.add('flex');
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
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }, 300);
    }

    document.getElementById('addModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeAddModal();
    });
    document.getElementById('editModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeEditModal();
    });

    const swalConfig = {
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold text-white',
            cancelButton: 'px-4 py-2 rounded-lg text-xs font-semibold text-white',
            title: 'text-base font-semibold',
            htmlContainer: 'text-xs text-gray-500'
        },
        buttonsStyling: false,
        reverseButtons: true
    };

    function confirmDelete(id, libelle) {
        Swal.fire({
            ...swalConfig,
            title: 'Confirmation de suppression',
            html: `Voulez-vous vraiment supprimer <strong class="text-rose-600">"${libelle}"</strong> ?<br><span class="text-gray-400">Cette action est irréversible.</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler',
            iconColor: '#e11d48'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm' + id);
                if (form) form.submit();
            }
        });
    }

    function confirmDeleteMobile(id, libelle) {
        Swal.fire({
            ...swalConfig,
            title: 'Confirmation de suppression',
            html: `Voulez-vous vraiment supprimer <strong class="text-rose-600">"${libelle}"</strong> ?<br><span class="text-gray-400">Cette action est irréversible.</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler',
            iconColor: '#e11d48'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteFormMobile' + id);
                if (form) form.submit();
            }
        });
    }

    @if(session('success'))
        Swal.fire({
            ...swalConfig,
            title: 'Succès',
            text: @json(session('success')),
            icon: 'success',
            confirmButtonText: 'OK',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
    @endif

    @if($errors->any())
        Swal.fire({
            ...swalConfig,
            title: 'Erreur de validation',
            html: `<ul class="text-left text-xs list-disc pl-5 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>`,
            icon: 'error',
            confirmButtonText: 'Corriger'
        });
        openAddModal();
    @endif

    @if(session('error'))
        Swal.fire({
            ...swalConfig,
            title: 'Erreur',
            text: @json(session('error')),
            icon: 'error',
            confirmButtonText: 'OK'
        });
    @endif
</script>

@endsection