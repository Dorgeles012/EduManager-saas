@extends('personnel.layouts.app')

@section('title', 'Disciplines de la série '.$serie->nom_serie)

@section('content')
<div class="flex flex-col gap-6 max-w-7xl mx-auto w-full">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('personnel.series.index') }}" class="inline-flex items-center gap-2 text-sm text-primary hover:text-primary/80 transition-colors group">
                    <span class="material-symbols-outlined text-[18px] group-hover:-translate-x-1 transition-transform">arrow_back</span>
                    Retour aux séries
                </a>
            </div>
            <div class="flex items-center gap-3">
                <h2 class="font-headline-lg text-headline-lg text-on-surface">Disciplines</h2>
                <span class="px-3 py-1 bg-primary/10 text-primary rounded-full text-sm font-medium">{{ $serie->nom_serie }}</span>
            </div>
            <p class="text-text-muted mt-1 flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">info</span>
                Chaque coefficient défini ici sera utilisé automatiquement dans les bulletins.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-5">
            <div class="bg-white rounded-2xl shadow-sm border border-outline-variant/60 p-8 sticky top-6">
                <div class="flex items-center gap-2 mb-6">
                    <span class="material-symbols-outlined text-primary text-[28px]">add_circle</span>
                    <h3 class="font-headline-md text-[22px] text-on-surface">Ajouter une discipline</h3>
                </div>
                <form action="{{ route('personnel.series.disciplines.store', $serie) }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label for="matiere_id" class="block text-sm font-medium text-on-surface mb-2">Matière <span class="text-alert-red">*</span></label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-text-muted text-[22px]">book</span>
                            <select id="matiere_id" name="matiere_id" required class="w-full pl-12 pr-4 py-3.5 rounded-xl border border-outline-variant bg-white focus:ring-2 focus:ring-primary/30 focus:border-primary outline-none transition-all appearance-none text-[15px]">
                                <option value="">Sélectionner une matière</option>
                                @foreach($matieres as $matiere)
                                    <option value="{{ $matiere->id }}" data-coefficient="{{ max(1, (int) $matiere->coefficient) }}" @selected(old('matiere_id') == $matiere->id)>{{ $matiere->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="coefficient" class="block text-sm font-medium text-on-surface mb-2">Coefficient <span class="text-alert-red">*</span></label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-text-muted text-[22px]">calculate</span>
                            <input id="coefficient" name="coefficient" type="number" min="1" max="100" step="1" required value="{{ old('coefficient', 1) }}" class="w-full pl-12 pr-4 py-3.5 rounded-xl border border-outline-variant bg-white focus:ring-2 focus:ring-primary/30 focus:border-primary outline-none transition-all text-[15px]">
                        </div>
                    </div>
                    <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-primary to-primary-container text-white font-medium hover:shadow-lg hover:opacity-90 transition-all flex items-center justify-center gap-2 text-[14px]">
                        <span class="material-symbols-outlined text-[20px]">add</span>
                        Ajouter la discipline
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-7">
            <div class="bg-white rounded-2xl shadow-sm border border-outline-variant/60 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-outline-variant/60 bg-surface-container-low/30 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[18px]">format_list_bulleted</span>
                        <h3 class="font-headline-md text-[14px] text-on-surface">Disciplines associées</h3>
                    </div>
                    <span class="px-2.5 py-0.5 bg-primary/10 text-primary rounded-full text-xs font-medium">{{ $disciplines->count() }}</span>
                </div>
                <div class="overflow-x-auto">
                    @if($disciplines->isNotEmpty())
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-surface-container-low/30 border-b border-outline-variant/30">
                                    <th class="px-5 py-2.5 text-[11px] font-semibold text-text-muted uppercase tracking-wider">Matière</th>
                                    <th class="px-5 py-2.5 text-[11px] font-semibold text-text-muted uppercase tracking-wider">Coeff.</th>
                                    <th class="px-5 py-2.5 text-[11px] font-semibold text-text-muted uppercase tracking-wider text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-outline-variant/20">
                                @foreach($disciplines as $discipline)
                                    <tr class="hover:bg-surface-container-low/30 transition-colors">
                                        <td class="px-5 py-3">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-7 h-7 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                                                    <span class="material-symbols-outlined text-primary text-[16px]">school</span>
                                                </div>
                                                <span class="font-medium text-on-surface text-[14px]">{{ $discipline->nom }}</span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3">
                                            <form action="{{ route('personnel.series.disciplines.update', [$serie, $discipline]) }}" method="POST" class="flex items-center gap-2">
                                                @csrf @method('PUT')
                                                <input name="coefficient" type="number" min="1" max="100" step="1" value="{{ $discipline->pivot->coefficient }}" required class="w-16 px-2 py-1 rounded-lg border border-outline-variant bg-white focus:ring-2 focus:ring-primary/30 focus:border-primary outline-none transition-all text-center text-[13px]">
                                                <button type="submit" class="p-1.5 rounded-lg text-primary hover:bg-primary/10 transition-all" title="Mettre à jour">
                                                    <span class="material-symbols-outlined text-[18px]">save</span>
                                                </button>
                                            </form>
                                        </td>
                                        <td class="px-5 py-3 text-right">
                                            <form action="{{ route('personnel.series.disciplines.destroy', [$serie, $discipline]) }}" method="POST" class="delete-discipline-form inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg text-text-muted hover:text-alert-red hover:bg-alert-red/10 transition-all" title="Retirer">
                                                    <span class="material-symbols-outlined text-[18px]">delete_outline</span>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="flex flex-col items-center justify-center py-12 px-4">
                            <div class="w-16 h-16 rounded-full bg-surface-container-low flex items-center justify-center mb-3">
                                <span class="material-symbols-outlined text-3xl text-text-muted">book</span>
                            </div>
                            <h4 class="text-base font-medium text-on-surface mb-0.5">Aucune discipline</h4>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('matiere_id')?.addEventListener('change', function () {
            const option = this.options[this.selectedIndex];
            if (option?.dataset.coefficient) document.getElementById('coefficient').value = option.dataset.coefficient;
        });
        document.querySelectorAll('form.delete-discipline-form').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                Swal.fire({ title: 'Retirer la discipline ?', text: 'Cette discipline sera retirée de la série.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', cancelButtonColor: '#6b7280', confirmButtonText: 'Oui, retirer', cancelButtonText: 'Annuler' }).then(function (result) { if (result.isConfirmed) form.submit(); });
            });
        });
        @if(session('success'))
            Swal.fire({ title: 'Succès', text: @json(session('success')), icon: 'success', timer: 3000 });
        @endif
    </script>
@endpush
@endsection
