@extends('client.layouts.app')

@section('title', 'Disciplines de la série '.$serie->nom_serie)

@section('content')

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
    <div>
        <div class="flex items-center gap-2 mb-2">
            <a href="{{ route('client.series.index') }}" class="inline-flex items-center gap-1 text-xs text-gray-500 hover:text-indigo-600 transition-colors">
                <span class="material-symbols-outlined text-base">arrow_back</span>
                Retour aux séries
            </a>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Disciplines</h2>
            <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-bold uppercase tracking-wider">
                {{ $serie->nom_serie }}
            </span>
        </div>
        <p class="text-sm text-gray-500 mt-1">Chaque coefficient défini ici sera utilisé automatiquement dans les bulletins.</p>
    </div>
    <div class="flex items-center gap-2">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-100 text-gray-700 text-xs font-semibold">
            <span class="material-symbols-outlined text-sm">menu_book</span>
            {{ $disciplines->count() }} discipline(s)
        </span>
    </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mb-6">
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                <span class="material-symbols-outlined text-lg">menu_book</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Disciplines</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $disciplines->count() }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Associées</p>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                <span class="material-symbols-outlined text-lg">calculate</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Coeff. total</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $disciplines->sum('pivot.coefficient') }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Somme des coefficients</p>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow col-span-2 lg:col-span-1">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                <span class="material-symbols-outlined text-lg">school</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Série</span>
        </div>
        <h3 class="text-base sm:text-lg font-bold text-gray-900 truncate">{{ $serie->nom_serie }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Série en cours</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
    <div class="lg:col-span-5">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden lg:sticky lg:top-6">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <span class="material-symbols-outlined text-base">add_circle</span>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">Ajouter une discipline</h3>
                    <p class="text-[11px] text-gray-500">Associez une matière à cette série</p>
                </div>
            </div>

            <form action="{{ route('client.series.disciplines.store', $serie) }}" method="POST" class="p-5 space-y-4">
                @csrf

                <div>
                    <label for="matiere_id" class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">
                        Matière <span class="text-rose-500">*</span>
                    </label>
                    <select id="matiere_id" name="matiere_id" required
                            class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                        <option value="">Sélectionner une matière</option>
                        @foreach($matieres as $matiere)
                        <option value="{{ $matiere->id }}" data-coefficient="{{ max(1, (int) $matiere->coefficient) }}" @selected(old('matiere_id') == $matiere->id)>
                            {{ $matiere->nom }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="coefficient" class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">
                        Coefficient <span class="text-rose-500">*</span>
                    </label>
                    <input id="coefficient" name="coefficient" type="number" min="1" max="100" step="1" required
                           value="{{ old('coefficient', 1) }}"
                           class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                    <p class="text-[10px] text-gray-400 mt-1.5">Valeur comprise entre 1 et 100</p>
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-lg text-xs font-semibold transition shadow-sm inline-flex items-center justify-center gap-1.5">
                        <span class="material-symbols-outlined text-sm">add</span>
                        Ajouter la discipline
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="lg:col-span-7">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <span class="material-symbols-outlined text-base">format_list_bulleted</span>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-gray-900">Disciplines associées</h3>
                    <p class="text-[11px] text-gray-500">{{ $disciplines->count() }} matière(s) associée(s)</p>
                </div>
            </div>

            @if($disciplines->isNotEmpty())
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/50">
                            <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Matière</th>
                            <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Coefficient</th>
                            <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($disciplines as $discipline)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 text-[11px] font-bold flex-shrink-0">
                                        {{ strtoupper(substr($discipline->nom, 0, 1)) }}
                                    </div>
                                    <span class="text-xs font-semibold text-gray-900">{{ $discipline->nom }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <form action="{{ route('client.series.disciplines.update', [$serie, $discipline]) }}" method="POST" class="inline-flex items-center gap-2">
                                    @csrf @method('PUT')
                                    <input name="coefficient" type="number" min="1" max="100" step="1"
                                           value="{{ $discipline->pivot->coefficient }}" required
                                           class="w-16 bg-gray-50 border border-gray-200 rounded-lg text-xs py-1.5 px-2 text-center font-semibold focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                                    <button type="submit"
                                            class="w-8 h-8 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-600 flex items-center justify-center transition"
                                            title="Mettre à jour">
                                        <span class="material-symbols-outlined text-base">save</span>
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end">
                                    <form action="{{ route('client.series.disciplines.destroy', [$serie, $discipline]) }}" method="POST" class="delete-discipline-form inline">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition"
                                                title="Retirer la discipline">
                                            <span class="material-symbols-outlined text-base">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="md:hidden divide-y divide-gray-100">
                @foreach($disciplines as $discipline)
                <div class="p-4 space-y-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-sm font-bold flex-shrink-0">
                            {{ strtoupper(substr($discipline->nom, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $discipline->nom }}</p>
                            <p class="text-[11px] text-gray-500">Coefficient actuel : {{ $discipline->pivot->coefficient }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <form action="{{ route('client.series.disciplines.update', [$serie, $discipline]) }}" method="POST" class="flex-1 inline-flex items-center gap-2">
                            @csrf @method('PUT')
                            <input name="coefficient" type="number" min="1" max="100" step="1"
                                   value="{{ $discipline->pivot->coefficient }}" required
                                   class="flex-1 bg-gray-50 border border-gray-200 rounded-lg text-xs py-2 px-3 text-center font-semibold focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                            <button type="submit"
                                    class="h-9 px-3 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-600 flex items-center justify-center gap-1.5 transition text-xs font-semibold">
                                <span class="material-symbols-outlined text-sm">save</span>
                                Enreg.
                            </button>
                        </form>
                        <form action="{{ route('client.series.disciplines.destroy', [$serie, $discipline]) }}" method="POST" class="delete-discipline-form">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="w-9 h-9 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition">
                                <span class="material-symbols-outlined text-base">delete</span>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <span class="text-[11px] text-gray-500">
                    Total : <strong class="text-gray-900">{{ $disciplines->count() }}</strong>
                </span>
                <span class="text-[11px] text-gray-500">
                    Coefficients : <strong class="text-gray-900">{{ $disciplines->sum('pivot.coefficient') }}</strong>
                </span>
            </div>
            @else
            <div class="py-16 text-center">
                <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-3">
                    <span class="material-symbols-outlined text-2xl text-gray-300">book</span>
                </div>
                <p class="text-sm font-semibold text-gray-700">Aucune discipline</p>
                <p class="text-xs text-gray-400 mt-1">Associez une première matière à cette série.</p>
            </div>
            @endif
        </div>
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

    document.getElementById('matiere_id')?.addEventListener('change', function () {
        const option = this.options[this.selectedIndex];
        if (option?.dataset.coefficient) {
            document.getElementById('coefficient').value = option.dataset.coefficient;
        }
    });

    document.querySelectorAll('form.delete-discipline-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                ...swalConfig,
                title: 'Retirer la discipline ?',
                text: 'Cette discipline sera retirée de la série.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, retirer',
                cancelButtonText: 'Annuler',
                iconColor: '#e11d48'
            }).then(function (result) {
                if (result.isConfirmed) form.submit();
            });
        });
    });

    @if(session('success'))
        Swal.fire({ ...swalConfig, icon: 'success', title: 'Succès', text: @json(session('success')), timer: 2500, showConfirmButton: false });
    @endif

    @if($errors->any())
        Swal.fire({
            ...swalConfig,
            icon: 'error',
            title: 'Erreur',
            html: `<ul class="text-left text-xs list-disc pl-5 space-y-0.5">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>`,
            confirmButtonText: 'Corriger'
        });
    @endif
</script>
@endpush