@extends('client.layouts.app')
@section('title', 'EduManager - Séries')
@section('content')

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Gestion des Séries</h2>
        <p class="text-sm text-gray-500 mt-1">Créez et gérez les séries du secondaire</p>
    </div>
    <button onclick="openModal('addModal')" class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-xs font-semibold transition shadow-sm">
        <span class="material-symbols-outlined text-sm">add</span>
        Ajouter une série
    </button>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mb-6">
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                <span class="material-symbols-outlined text-lg">school</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Total</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $totalSeries ?? 0 }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Séries disponibles</p>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                <span class="material-symbols-outlined text-lg">class</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Classes</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $classes->count() ?? 0 }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Classes associées</p>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow col-span-2 lg:col-span-1">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                <span class="material-symbols-outlined text-lg">assignment</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Disciplines</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $series->sum('matieres_count') ?? 0 }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Associations matière</p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
            <span class="material-symbols-outlined text-base">filter_alt</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-900">Liste des séries</h3>
            <p class="text-[11px] text-gray-500">{{ $series->count() ?? 0 }} série(s) enregistrée(s)</p>
        </div>
    </div>

    <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">N°</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Nom de la série</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Classes</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider text-center">Disciplines</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($series ?? [] as $s)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-4 py-3 text-xs text-gray-500 font-semibold">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 text-[11px] font-bold flex-shrink-0">
                                {{ strtoupper(substr($s['nom_serie'], 0, 2)) }}
                            </div>
                        
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-100 text-[10px] font-bold text-gray-700">
                            {{ $s['classe'] }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-xs font-bold text-gray-900">{{ $s['matieres_count'] }}</span>
                        <span class="text-[10px] text-gray-400"> matières</span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="{{ route('client.series.disciplines', $s['id']) }}"
                               class="w-8 h-8 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-600 flex items-center justify-center transition"
                               title="Gérer les disciplines">
                                <span class="material-symbols-outlined text-base">menu_book</span>
                            </a>
                            <button onclick="editSeries({{ $s['id'] }}, @js($s['nom_serie']), @js($s['id_classes']))"
                                    class="w-8 h-8 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-600 flex items-center justify-center transition"
                                    title="Modifier">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </button>
                            <form action="{{ route('client.series.destroy', $s['id']) }}" method="POST" class="inline delete-series-form">
                                @csrf @method('DELETE')
                                <button type="button"
                                        data-name="{{ $s['nom_serie'] }}"
                                        class="delete-series-btn w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition"
                                        title="Supprimer">
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
                                <span class="material-symbols-outlined text-2xl text-gray-300">filter_alt</span>
                            </div>
                            <p class="text-sm font-semibold text-gray-700">Aucune série enregistrée</p>
                            <p class="text-xs text-gray-400 mt-1">Commencez par ajouter votre première série.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="md:hidden divide-y divide-gray-100">
        @forelse($series ?? [] as $s)
        <div class="p-4 space-y-3">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-sm font-bold flex-shrink-0">
                        {{ strtoupper(substr($s['nom_serie'], 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $s['nom_serie'] }}</p>
                        <p class="text-[11px] text-gray-500 truncate">{{ $s['classe'] }}</p>
                    </div>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-100 text-[10px] font-bold text-gray-700 flex-shrink-0">
                    {{ $s['matieres_count'] }} mat.
                </span>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <a href="{{ route('client.series.disciplines', $s['id']) }}"
                   class="flex-1 h-9 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-600 text-xs font-semibold flex items-center justify-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-sm">menu_book</span>
                    Disciplines
                </a>
                <button onclick="editSeries({{ $s['id'] }}, @js($s['nom_serie']), @js($s['id_classes']))"
                        class="flex-1 h-9 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold flex items-center justify-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-sm">edit</span>
                    Modifier
                </button>
                <form action="{{ route('client.series.destroy', $s['id']) }}" method="POST" class="delete-series-form">
                    @csrf @method('DELETE')
                    <button type="button"
                            data-name="{{ $s['nom_serie'] }}"
                            class="delete-series-btn w-9 h-9 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition">
                        <span class="material-symbols-outlined text-base">delete</span>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="py-16 text-center">
            <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-2xl text-gray-300">filter_alt</span>
            </div>
            <p class="text-sm font-semibold text-gray-700">Aucune série enregistrée</p>
            <p class="text-xs text-gray-400 mt-1">Commencez par ajouter votre première série.</p>
        </div>
        @endforelse
    </div>
</div>

<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="addModal">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('addModal')"></div>
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0 relative z-10 max-h-[90vh] flex flex-col" id="addModalContent">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center flex-shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <span class="material-symbols-outlined text-base">add_circle</span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Ajouter une série</h3>
                    <p class="text-[11px] text-gray-500">Nouvelle série pédagogique</p>
                </div>
            </div>
            <button onclick="closeModal('addModal')" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center transition">
                <span class="material-symbols-outlined text-gray-500">close</span>
            </button>
        </div>

        <form class="p-5 space-y-4 overflow-y-auto flex-1" id="addSeriesForm" action="{{ route('client.series.store') }}" method="POST">
            @csrf
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Classes</label>
                <select name="id_classes[]" multiple required size="5"
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                    @foreach($classes as $classe)
                    <option value="{{ $classe->id }}" @selected(collect(old('id_classes'))->contains($classe->id))>{{ $classe->nom }}</option>
                    @endforeach
                </select>
                <p class="text-[10px] text-gray-400 mt-1.5">Maintenez Ctrl/Cmd pour sélectionner plusieurs classes.</p>
            </div>

            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Nom de la série</label>
                <input type="text" name="nom_serie" required placeholder="Ex: Série A1"
                       class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                <p class="text-[10px] text-gray-400 mt-1.5">Exemples: A1, A2, B, C, D, F2, F3, G1, G2</p>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                <button type="button" onclick="closeModal('addModal')"
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

<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="editModal">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('editModal')"></div>
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0 relative z-10 max-h-[90vh] flex flex-col" id="editModalContent">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center flex-shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                    <span class="material-symbols-outlined text-base">edit</span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Modifier la série</h3>
                    <p class="text-[11px] text-gray-500">Mise à jour des informations</p>
                </div>
            </div>
            <button onclick="closeModal('editModal')" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center transition">
                <span class="material-symbols-outlined text-gray-500">close</span>
            </button>
        </div>

        <form class="p-5 space-y-4 overflow-y-auto flex-1" id="editSeriesForm" method="POST">
            @csrf @method('PUT')
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Classes</label>
                <select id="editSerieClasses" name="id_classes[]" multiple required size="5"
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2 px-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition">
                    @foreach($classes as $classe)
                    <option value="{{ $classe->id }}">{{ $classe->nom }}</option>
                    @endforeach
                </select>
                <p class="text-[10px] text-gray-400 mt-1.5">Maintenez Ctrl/Cmd pour sélectionner plusieurs classes.</p>
            </div>

            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Nom de la série</label>
                <input type="text" id="editNomSerie" name="nom_serie" required
                       class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition">
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                <button type="button" onclick="closeModal('editModal')"
                        class="px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    Annuler
                </button>
                <button type="submit"
                        class="bg-amber-600 hover:bg-amber-700 text-white px-5 py-2 rounded-lg text-xs font-semibold transition shadow-sm">
                    Appliquer
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

    function openModal(id) {
        const modal = document.getElementById(id);
        const content = document.getElementById(id + 'Content');
        modal.classList.remove('hidden'); modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        setTimeout(() => { content.classList.remove('scale-95', 'opacity-0'); content.classList.add('scale-100', 'opacity-100'); }, 10);
    }
    function closeModal(id) {
        const modal = document.getElementById(id);
        const content = document.getElementById(id + 'Content');
        content.classList.remove('scale-100', 'opacity-100'); content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => { modal.classList.remove('flex'); modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
    }

    function editSeries(id, nomSerie, classeIds) {
        document.getElementById('editSeriesForm').action = `{{ url('/client/series') }}/${id}`;
        document.getElementById('editNomSerie').value = nomSerie;

        const select = document.getElementById('editSerieClasses');
        if (select) {
            const selectedIds = (classeIds ?? []).map(String);
            Array.from(select.options).forEach(opt => {
                opt.selected = selectedIds.includes(String(opt.value));
            });
        }
        openModal('editModal');
    }

    document.querySelectorAll('.delete-series-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            Swal.fire({
                ...swalConfig,
                title: 'Supprimer cette série ?',
                html: `La série <strong class="text-rose-600">"${this.dataset.name}"</strong> sera définitivement supprimée.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
                iconColor: '#e11d48'
            }).then(r => { if (r.isConfirmed) form.submit(); });
        });
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') ['addModal', 'editModal'].forEach(id => {
            const m = document.getElementById(id);
            if (m && !m.classList.contains('hidden')) closeModal(id);
        });
    });

    @if(session('success'))
        Swal.fire({ ...swalConfig, icon: 'success', title: 'Succès', text: @json(session('success')), timer: 2500, showConfirmButton: false });
    @endif
    @if(session('error'))
        Swal.fire({ ...swalConfig, icon: 'error', title: 'Erreur', text: @json(session('error')), confirmButtonText: 'OK' });
    @endif
</script>
@endpush