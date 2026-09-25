@extends('client.layouts.app')
@section('title', 'Créer un enseignant')

@section('content')

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
    <div class="flex items-center gap-3">
        <a href="{{ route('client.enseignant') }}" class="w-9 h-9 rounded-xl bg-white border border-gray-100 hover:bg-gray-100 flex items-center justify-center transition flex-shrink-0">
            <span class="material-symbols-outlined text-gray-600 text-base">arrow_back</span>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Créer un enseignant</h2>
            <p class="text-sm text-gray-500 mt-0.5">Ajoutez un enseignant et ses affectations en quelques clics.</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
            <span class="material-symbols-outlined text-base">assignment_ind</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-900">Nouvel enseignant</h3>
            <p class="text-[11px] text-gray-500">Remplissez les informations ci-dessous</p>
        </div>
    </div>

    <div class="p-5">
        <form method="POST" action="{{ route('client.enseignants.store') }}" enctype="multipart/form-data" id="teacherForm">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                <div class="bg-gray-50/50 rounded-2xl border border-gray-100 p-4 space-y-4">
                    <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                        <div class="w-6 h-6 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600">
                            <span class="material-symbols-outlined text-sm">person</span>
                        </div>
                        <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider">Informations personnelles</h4>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Nom <span class="text-rose-500">*</span></label>
                            <input name="nom" type="text" value="{{ old('nom') }}" required placeholder="Entrez le nom"
                                   class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                            @error('nom')<div class="text-rose-500 text-[10px] mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Prénoms <span class="text-rose-500">*</span></label>
                            <input name="prenoms" type="text" value="{{ old('prenoms') }}" required placeholder="Entrez les prénoms"
                                   class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                            @error('prenoms')<div class="text-rose-500 text-[10px] mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Matricule <span class="text-rose-500">*</span></label>
                            <input name="matricule" type="text" value="{{ old('matricule') }}" required placeholder="ENS-2024-001"
                                   class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                            @error('matricule')<div class="text-rose-500 text-[10px] mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Années d'enseignement <span class="text-rose-500">*</span></label>
                            <input name="nombre_annees_enseignement" type="number" min="0" max="80" value="{{ old('nombre_annees_enseignement') }}" required placeholder="0"
                                   class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                            @error('nombre_annees_enseignement')<div class="text-rose-500 text-[10px] mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Email <span class="text-rose-500">*</span></label>
                        <input name="email" type="email" value="{{ old('email') }}" required placeholder="exemple@mail.com"
                               class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                        @error('email')<div class="text-rose-500 text-[10px] mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Téléphone <span class="text-rose-500">*</span></label>
                        <input name="telephone" type="tel" value="{{ old('telephone') }}" required placeholder="01 02 03 04 05"
                               class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                        @error('telephone')<div class="text-rose-500 text-[10px] mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Sexe <span class="text-rose-500">*</span></label>
                        <div class="flex gap-2">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="sexe" value="Masculin" {{ old('sexe')==='Masculin'?'checked':'' }} required class="peer sr-only">
                                <div class="flex items-center justify-center gap-1.5 py-2 rounded-lg border border-gray-200 bg-white text-xs font-semibold text-gray-600 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 transition">
                                    <span class="material-symbols-outlined text-sm">male</span>
                                    Masculin
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="sexe" value="Féminin" {{ old('sexe')==='Féminin'?'checked':'' }} class="peer sr-only">
                                <div class="flex items-center justify-center gap-1.5 py-2 rounded-lg border border-gray-200 bg-white text-xs font-semibold text-gray-600 peer-checked:border-pink-500 peer-checked:bg-pink-50 peer-checked:text-pink-700 transition">
                                    <span class="material-symbols-outlined text-sm">female</span>
                                    Féminin
                                </div>
                            </label>
                        </div>
                        @error('sexe')<div class="text-rose-500 text-[10px] mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Photo de profil</label>
                        <div class="flex items-center gap-3">
                            <div id="photoPreview" class="w-16 h-16 rounded-2xl bg-gray-100 border border-dashed border-gray-300 flex items-center justify-center overflow-hidden flex-shrink-0">
                                <span class="material-symbols-outlined text-2xl text-gray-400">person</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 text-xs font-semibold rounded-lg transition">
                                    <span class="material-symbols-outlined text-sm">upload</span>
                                    Choisir une photo
                                    <input name="photo" type="file" accept="image/*" class="hidden" onchange="previewPhoto(this)">
                                </label>
                                <p class="text-[10px] text-gray-400 mt-1">PNG, JPG, WEBP — max 2 MB</p>
                            </div>
                        </div>
                        @error('photo')<div class="text-rose-500 text-[10px] mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="bg-gray-50/50 rounded-2xl border border-gray-100 p-4 space-y-4">
                    <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                        <div class="w-6 h-6 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">
                            <span class="material-symbols-outlined text-sm">school</span>
                        </div>
                        <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider">Affectations pédagogiques</h4>
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Matières enseignées <span class="text-rose-500">*</span></label>
                        <div class="relative" id="matiereContainer">
                            <div class="flex flex-wrap gap-1.5 p-2 border border-gray-200 rounded-lg focus-within:ring-2 focus-within:ring-indigo-100 focus-within:border-indigo-500 transition bg-white min-h-[42px]" id="matiereTags">
                                <input type="text" id="matiereSearch" placeholder="Tapez pour rechercher..."
                                       class="flex-1 min-w-[120px] border-0 outline-none bg-transparent text-xs p-1">
                            </div>
                            <div id="matiereSuggestions" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto hidden">
                                @foreach($matieres as $m)
                                    <div class="suggestion-item px-3 py-2 hover:bg-indigo-50 cursor-pointer text-xs flex items-center justify-between" data-id="{{ $m->id }}" data-name="{{ $m->nom }}">
                                        <span>{{ $m->nom }}</span>
                                        <span class="text-xs text-gray-400">+</span>
                                    </div>
                                @endforeach
                            </div>
                            <input type="hidden" id="matiereIds" value="{{ implode(',', old('matiere_ids', [])) }}">
                        </div>
                        @error('matiere_ids')<div class="text-rose-500 text-[10px] mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Classes affectées <span class="text-rose-500">*</span></label>
                        <div class="relative" id="classeContainer">
                            <div class="flex flex-wrap gap-1.5 p-2 border border-gray-200 rounded-lg focus-within:ring-2 focus-within:ring-indigo-100 focus-within:border-indigo-500 transition bg-white min-h-[42px]" id="classeTags">
                                <input type="text" id="classeSearch" placeholder="Tapez pour rechercher..."
                                       class="flex-1 min-w-[120px] border-0 outline-none bg-transparent text-xs p-1">
                            </div>
                            <div id="classeSuggestions" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto hidden">
                                @foreach($classes as $c)
                                    <div class="suggestion-item px-3 py-2 hover:bg-indigo-50 cursor-pointer text-xs flex items-center justify-between" data-id="{{ $c->id }}" data-name="{{ $c->nom }}">
                                        <span>{{ $c->nom }}</span>
                                        <span class="text-xs text-gray-400">+</span>
                                    </div>
                                @endforeach
                            </div>
                            <input type="hidden" id="classeIds" value="{{ implode(',', old('classe_ids', [])) }}">
                        </div>
                        @error('classe_ids')<div class="text-rose-500 text-[10px] mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Séries affectées</label>
                        <div class="relative" id="serieContainer">
                            <div class="flex flex-wrap gap-1.5 p-2 border border-gray-200 rounded-lg focus-within:ring-2 focus-within:ring-indigo-100 focus-within:border-indigo-500 transition bg-white min-h-[42px]" id="serieTags">
                                <input type="text" id="serieSearch" placeholder="Tapez pour rechercher..."
                                       class="flex-1 min-w-[120px] border-0 outline-none bg-transparent text-xs p-1">
                            </div>
                            <div id="serieSuggestions" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto hidden">
                                @foreach($series as $s)
                                    <div class="suggestion-item px-3 py-2 hover:bg-indigo-50 cursor-pointer text-xs flex items-center justify-between" data-id="{{ $s->id }}" data-name="{{ $s->nom_serie ?? $s->nom }}">
                                        <span>{{ $s->nom_serie ?? $s->nom }}</span>
                                        <span class="text-xs text-gray-400">+</span>
                                    </div>
                                @endforeach
                            </div>
                            <input type="hidden" id="serieIds" value="{{ implode(',', old('serie_ids', [])) }}">
                        </div>
                        @error('serie_ids')<div class="text-rose-500 text-[10px] mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="p-3 bg-indigo-50 border border-indigo-100 rounded-lg flex items-start gap-2">
                        <span class="material-symbols-outlined text-indigo-500 text-sm flex-shrink-0 mt-0.5">info</span>
                        <p class="text-[11px] text-indigo-700 leading-relaxed">
                            Mot de passe par défaut :
                            <span class="font-mono bg-indigo-100 px-1.5 py-0.5 rounded text-[10px] font-bold">12345678</span>
                            <span class="text-[10px] text-indigo-600/80 ml-1">(modifiable à la 1ʳᵉ connexion)</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-2 pt-4 border-t border-gray-100">
                <a href="{{ route('client.enseignant') }}"
                   class="w-full sm:w-auto text-center px-5 py-2.5 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    Annuler
                </a>
                <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-xs font-semibold transition shadow-sm">
                    <span class="material-symbols-outlined text-sm">save</span>
                    Enregistrer l'enseignant
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
<style>
    .tag-item {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #eef2ff;
        color: #4f46e5;
        padding: 2px 6px 2px 8px;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 600;
        animation: tagFadeIn 0.15s ease-out;
    }
    .tag-item .remove-tag {
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        transition: all 0.2s;
        font-size: 14px;
        line-height: 1;
        color: #4f46e5;
    }
    .tag-item .remove-tag:hover { background: #c7d2fe; color: #3730a3; }
    @keyframes tagFadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
    .suggestion-item:hover .text-gray-400 { color: #4f46e5 !important; }
    #matiereSuggestions::-webkit-scrollbar,
    #classeSuggestions::-webkit-scrollbar,
    #serieSuggestions::-webkit-scrollbar { width: 5px; }
    #matiereSuggestions::-webkit-scrollbar-track,
    #classeSuggestions::-webkit-scrollbar-track,
    #serieSuggestions::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    #matiereSuggestions::-webkit-scrollbar-thumb,
    #classeSuggestions::-webkit-scrollbar-thumb,
    #serieSuggestions::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
    #matiereSuggestions::-webkit-scrollbar-thumb:hover,
    #classeSuggestions::-webkit-scrollbar-thumb:hover,
    #serieSuggestions::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const swalConfig = {
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold text-white mx-1',
            title: 'text-base font-semibold',
            htmlContainer: 'text-xs text-gray-500'
        },
        buttonsStyling: false
    };

    @if(session('success'))
        Swal.fire({
            ...swalConfig,
            icon: 'success',
            title: 'Succès',
            text: 'Enseignant créé avec succès.',
            confirmButtonText: 'OK',
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then(() => { window.location.href = @json(route('client.enseignant')); });
    @endif

    const configs = [
        { searchId: 'matiereSearch', suggestionsId: 'matiereSuggestions', tagsId: 'matiereTags', hiddenId: 'matiereIds', containerId: 'matiereContainer',
          allItems: @json($matieres->map(fn($m) => ['id' => $m->id, 'name' => $m->nom])) },
        { searchId: 'classeSearch', suggestionsId: 'classeSuggestions', tagsId: 'classeTags', hiddenId: 'classeIds', containerId: 'classeContainer',
          allItems: @json($classes->map(fn($c) => ['id' => $c->id, 'name' => $c->nom])) },
        { searchId: 'serieSearch', suggestionsId: 'serieSuggestions', tagsId: 'serieTags', hiddenId: 'serieIds', containerId: 'serieContainer',
          allItems: @json($series->map(fn($s) => ['id' => $s->id, 'name' => $s->nom_serie ?? $s->nom])) }
    ];

    configs.forEach(config => initAutocomplete(config));

    function initAutocomplete(config) {
        const searchInput = document.getElementById(config.searchId);
        const suggestions = document.getElementById(config.suggestionsId);
        const tagsContainer = document.getElementById(config.tagsId);
        const hiddenInput = document.getElementById(config.hiddenId);
        const container = document.getElementById(config.containerId);
        let selectedItems = new Set();
        let allItems = config.allItems || [];

        if (hiddenInput.value) {
            hiddenInput.value.split(',').filter(id => id).forEach(id => {
                const item = allItems.find(i => String(i.id) === String(id));
                if (item) { selectedItems.add(String(item.id)); addTag(item.id, item.name); }
            });
        }

        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            if (query.length === 0) { suggestions.classList.add('hidden'); return; }
            const filtered = allItems.filter(item => item.name.toLowerCase().includes(query) && !selectedItems.has(String(item.id)));
            suggestions.innerHTML = filtered.length === 0
                ? `<div class="px-3 py-3 text-[11px] text-gray-400 text-center">Aucun résultat</div>`
                : filtered.map(item => `<div class="suggestion-item px-3 py-2 hover:bg-indigo-50 cursor-pointer text-xs flex items-center justify-between" data-id="${item.id}" data-name="${item.name}"><span>${item.name}</span><span class="text-xs text-gray-400">+</span></div>`).join('');
            suggestions.classList.remove('hidden');
        });

        suggestions.addEventListener('click', function(e) {
            const item = e.target.closest('.suggestion-item');
            if (!item) return;
            const id = item.dataset.id, name = item.dataset.name;
            if (!selectedItems.has(String(id))) {
                selectedItems.add(String(id));
                addTag(id, name);
                updateHiddenInput();
                searchInput.value = '';
                suggestions.classList.add('hidden');
            }
        });

        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); suggestions.querySelector('.suggestion-item')?.click(); }
            if (e.key === 'Escape') { suggestions.classList.add('hidden'); this.blur(); }
            if (e.key === 'Backspace' && this.value === '' && selectedItems.size > 0) {
                tagsContainer.querySelector('.tag-item:last-child .remove-tag')?.click();
            }
        });

        document.addEventListener('click', function(e) {
            if (!container.contains(e.target)) suggestions.classList.add('hidden');
        });

        function addTag(id, name) {
            const tag = document.createElement('span');
            tag.className = 'tag-item';
            tag.dataset.id = id;
            tag.innerHTML = `${name}<span class="remove-tag" data-id="${id}">×</span>`;
            tag.querySelector('.remove-tag').addEventListener('click', function() {
                const rid = this.dataset.id;
                selectedItems.delete(String(rid));
                tag.remove();
                updateHiddenInput();
                if (searchInput.value.toLowerCase().trim()) searchInput.dispatchEvent(new Event('input'));
            });
            tagsContainer.insertBefore(tag, searchInput);
        }

        function updateHiddenInput() {
            hiddenInput.value = Array.from(selectedItems).join(',');
            hiddenInput.dispatchEvent(new Event('change'));
        }
    }

    window.previewPhoto = function(input) {
        const preview = document.getElementById('photoPreview');
        if (!preview) return;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" alt="Photo" class="w-full h-full object-cover">`;
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.innerHTML = `<span class="material-symbols-outlined text-2xl text-gray-400">person</span>`;
        }
    };

    document.getElementById('teacherForm').addEventListener('submit', function(e) {
        const matiereIds = document.getElementById('matiereIds').value;
        const classeIds = document.getElementById('classeIds').value;

        if (!matiereIds || matiereIds.split(',').filter(id => id).length === 0) {
            e.preventDefault();
            Swal.fire({ ...swalConfig, icon: 'error', title: 'Champ requis', text: 'Veuillez sélectionner au moins une matière.', confirmButtonText: 'OK' });
            document.getElementById('matiereSearch').focus();
            return false;
        }
        if (!classeIds || classeIds.split(',').filter(id => id).length === 0) {
            e.preventDefault();
            Swal.fire({ ...swalConfig, icon: 'error', title: 'Champ requis', text: 'Veuillez sélectionner au moins une classe.', confirmButtonText: 'OK' });
            document.getElementById('classeSearch').focus();
            return false;
        }

        document.querySelectorAll('.relation-id-input').forEach(input => input.remove());
        [['matiere_ids', matiereIds], ['classe_ids', classeIds], ['serie_ids', document.getElementById('serieIds').value]].forEach(([field, ids]) =>
            ids.split(',').filter(Boolean).forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `${field}[]`;
                input.value = id;
                input.className = 'relation-id-input';
                this.appendChild(input);
            })
        );
        return true;
    });
});
</script>
@endpush