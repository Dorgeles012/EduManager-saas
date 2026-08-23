@extends('personnel.layouts.app')
@section('title', 'EduManager - Modifier un enseignant')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('personnel.enseignants.index') }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white border border-outline-variant hover:bg-surface-container transition-all text-on-surface-variant flex-shrink-0">
            <span class="material-symbols-outlined text-xl">arrow_back</span>
        </a>
        <div>
            <h2 class="font-headline-md text-headline-md text-primary">Modifier l'enseignant</h2>
            <p class="text-xs text-text-muted">Mettez à jour les informations et affectations de l'enseignant.</p>
        </div>
    </div>
</div>

<div class="glass-card rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-surface-subtle bg-surface-container-low flex items-center gap-2">
        <span class="material-symbols-outlined text-amber-600">edit</span>
        <h4 class="font-headline-sm text-headline-sm text-on-surface text-base">Édition : {{ $enseignant->nom }} {{ $enseignant->prenoms }}</h4>
    </div>

    <div class="p-6">
        <form method="POST" action="{{ route('personnel.enseignants.update', $enseignant->id) }}" enctype="multipart/form-data" id="teacherForm">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Colonne gauche -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-text-muted mb-1">Nom <span class="text-alert-red">*</span></label>
                        <input name="nom" type="text" value="{{ old('nom', $enseignant->nom) }}" 
                               class="w-full px-3 py-2 text-sm border border-outline-variant rounded-lg focus:ring-primary focus:border-primary bg-white" required>
                        @error('nom')<div class="text-alert-red text-xs mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-text-muted mb-1">Prénoms <span class="text-alert-red">*</span></label>
                        <input name="prenoms" type="text" value="{{ old('prenoms', $enseignant->prenoms) }}" 
                               class="w-full px-3 py-2 text-sm border border-outline-variant rounded-lg focus:ring-primary focus:border-primary bg-white" required>
                        @error('prenoms')<div class="text-alert-red text-xs mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-text-muted mb-1">Matricule <span class="text-alert-red">*</span></label>
                        <input name="matricule" type="text" value="{{ old('matricule', $enseignant->matricule) }}" 
                               class="w-full px-3 py-2 text-sm border border-outline-variant rounded-lg focus:ring-primary focus:border-primary bg-white font-mono" required>
                        @error('matricule')<div class="text-alert-red text-xs mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-text-muted mb-1">Email <span class="text-alert-red">*</span></label>
                        <input name="email" type="email" value="{{ old('email', $enseignant->email) }}" 
                               class="w-full px-3 py-2 text-sm border border-outline-variant rounded-lg focus:ring-primary focus:border-primary bg-white" required>
                        @error('email')<div class="text-alert-red text-xs mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-text-muted mb-1">Téléphone <span class="text-alert-red">*</span></label>
                        <input name="telephone" type="tel" value="{{ old('telephone', $enseignant->telephone) }}" 
                               class="w-full px-3 py-2 text-sm border border-outline-variant rounded-lg focus:ring-primary focus:border-primary bg-white" required>
                        @error('telephone')<div class="text-alert-red text-xs mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-text-muted mb-1">Années d'expérience <span class="text-alert-red">*</span></label>
                        <input name="nombre_annees_enseignement" type="number" min="0" max="80" 
                               value="{{ old('nombre_annees_enseignement', $enseignant->nombre_annees_enseignement ?? 0) }}" 
                               class="w-full px-3 py-2 text-sm border border-outline-variant rounded-lg focus:ring-primary focus:border-primary bg-white" required>
                        @error('nombre_annees_enseignement')<div class="text-alert-red text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <!-- Colonne droite -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-text-muted mb-1">Sexe <span class="text-alert-red">*</span></label>
                        <div class="flex gap-6 pt-1">
                            <label class="flex items-center gap-2 cursor-pointer text-sm">
                                <input type="radio" name="sexe" value="Masculin" {{ old('sexe', $enseignant->sexe)==='Masculin'?'checked':'' }} required 
                                       class="w-4 h-4 text-primary focus:ring-primary">
                                <span>Masculin</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-sm">
                                <input type="radio" name="sexe" value="Féminin" {{ old('sexe', $enseignant->sexe)==='Féminin'?'checked':'' }} 
                                       class="w-4 h-4 text-primary focus:ring-primary">
                                <span>Féminin</span>
                            </label>
                        </div>
                        @error('sexe')<div class="text-alert-red text-xs mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-text-muted mb-1">Photo de profil</label>
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-full bg-surface-container border-2 border-dashed border-outline-variant flex items-center justify-center overflow-hidden flex-shrink-0" id="photoPreview">
                                @if($enseignant->photo)
                                    <img src="{{ asset('storage/' . $enseignant->photo) }}" class="w-full h-full object-cover">
                                @else
                                    <span class="material-symbols-outlined text-text-muted text-3xl">person</span>
                                @endif
                            </div>
                            <div>
                                <label class="cursor-pointer inline-flex items-center px-3 py-1.5 bg-surface-container hover:bg-surface-container-high text-on-surface text-xs font-medium rounded-lg transition-all">
                                    <span class="material-symbols-outlined mr-1 text-sm">upload</span>
                                    Changer la photo
                                    <input name="photo" type="file" accept="image/*" class="hidden" onchange="previewPhoto(this)">
                                </label>
                                <p class="text-[11px] text-text-muted mt-1">PNG, JPG, WEBP • Max 2Mo</p>
                            </div>
                        </div>
                        @error('photo')<div class="text-alert-red text-xs mt-1">{{ $message }}</div>@enderror
                    </div>

                    <!-- Matières -->
                    <div>
                        <label class="block text-xs font-semibold uppercase text-text-muted mb-1">Matières enseignées (1 à 2) <span class="text-alert-red">*</span></label>
                        <div class="relative" id="matiereContainer">
                            <div class="flex flex-wrap gap-1.5 p-2 border border-outline-variant rounded-lg bg-white min-h-[42px]" id="matiereTags">
                                <input type="text" id="matiereSearch" placeholder="Rechercher une matière..." class="flex-1 min-w-[120px] border-0 outline-none bg-transparent text-sm p-0.5">
                            </div>
                            <div id="matiereSuggestions" class="absolute z-50 w-full mt-1 bg-white border border-outline-variant rounded-lg shadow-lg max-h-48 overflow-y-auto hidden">
                                @foreach($matieres as $m)
                                    <div class="suggestion-item px-3 py-2 hover:bg-primary-fixed/30 cursor-pointer text-sm flex items-center justify-between" data-id="{{ $m->id }}" data-name="{{ $m->nom }}">
                                        <span>{{ $m->nom }}</span>
                                        <span class="text-xs text-text-muted">+</span>
                                    </div>
                                @endforeach
                            </div>
                            <input type="hidden" id="matiereIds" name="matiere_ids" value="{{ implode(',', old('matiere_ids', $enseignant->matieres->pluck('id')->all())) }}">
                        </div>
                        @error('matiere_ids')<div class="text-alert-red text-xs mt-1">{{ $message }}</div>@enderror
                    </div>

                    <!-- Classes -->
                    <div>
                        <label class="block text-xs font-semibold uppercase text-text-muted mb-1">Classes affectées <span class="text-alert-red">*</span></label>
                        <div class="relative" id="classeContainer">
                            <div class="flex flex-wrap gap-1.5 p-2 border border-outline-variant rounded-lg bg-white min-h-[42px]" id="classeTags">
                                <input type="text" id="classeSearch" placeholder="Rechercher une classe..." class="flex-1 min-w-[120px] border-0 outline-none bg-transparent text-sm p-0.5">
                            </div>
                            <div id="classeSuggestions" class="absolute z-50 w-full mt-1 bg-white border border-outline-variant rounded-lg shadow-lg max-h-48 overflow-y-auto hidden">
                                @foreach($classes as $c)
                                    <div class="suggestion-item px-3 py-2 hover:bg-primary-fixed/30 cursor-pointer text-sm flex items-center justify-between" data-id="{{ $c->id }}" data-name="{{ $c->nom }}">
                                        <span>{{ $c->nom }}</span>
                                        <span class="text-xs text-text-muted">+</span>
                                    </div>
                                @endforeach
                            </div>
                            <input type="hidden" id="classeIds" name="classe_ids" value="{{ implode(',', old('classe_ids', $enseignant->classes->pluck('id')->all())) }}">
                        </div>
                        @error('classe_ids')<div class="text-alert-red text-xs mt-1">{{ $message }}</div>@enderror
                    </div>

                    <!-- Séries -->
                    <div>
                        <label class="block text-xs font-semibold uppercase text-text-muted mb-1">Séries associées</label>
                        <div class="relative" id="serieContainer">
                            <div class="flex flex-wrap gap-1.5 p-2 border border-outline-variant rounded-lg bg-white min-h-[42px]" id="serieTags">
                                <input type="text" id="serieSearch" placeholder="Rechercher une série..." class="flex-1 min-w-[120px] border-0 outline-none bg-transparent text-sm p-0.5">
                            </div>
                            <div id="serieSuggestions" class="absolute z-50 w-full mt-1 bg-white border border-outline-variant rounded-lg shadow-lg max-h-48 overflow-y-auto hidden">
                                @foreach($series as $s)
                                    <div class="suggestion-item px-3 py-2 hover:bg-primary-fixed/30 cursor-pointer text-sm flex items-center justify-between" data-id="{{ $s->id }}" data-name="{{ $s->nom_serie ?? $s->nom }}">
                                        <span>{{ $s->nom_serie ?? $s->nom }}</span>
                                        <span class="text-xs text-text-muted">+</span>
                                    </div>
                                @endforeach
                            </div>
                            <input type="hidden" id="serieIds" name="serie_ids" value="{{ implode(',', old('serie_ids', $enseignant->series->pluck('id')->all())) }}">
                        </div>
                        @error('serie_ids')<div class="text-alert-red text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-surface-subtle flex justify-end gap-3">
                <a href="{{ route('personnel.enseignants.index') }}" class="px-4 py-2 border border-outline-variant text-on-surface text-sm rounded-lg hover:bg-surface-container transition-all">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:opacity-90 active:scale-95 transition-all flex items-center gap-1.5 shadow-md">
                    <span class="material-symbols-outlined text-base">save</span>
                    Mettre à jour
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
        background: #e0e7ff;
        color: #3730a3;
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .tag-item .remove-tag {
        cursor: pointer;
        font-size: 14px;
        line-height: 1;
        color: #4f46e5;
    }
    .tag-item .remove-tag:hover {
        color: #ba1a1a;
    }
</style>
@endpush

@push('scripts')
<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('photoPreview').innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const configs = [
        {
            searchId: 'matiereSearch',
            suggestionsId: 'matiereSuggestions',
            tagsId: 'matiereTags',
            hiddenId: 'matiereIds',
            containerId: 'matiereContainer',
            allItems: @json($matieres->map(fn($m) => ['id' => $m->id, 'name' => $m->nom])),
        },
        {
            searchId: 'classeSearch',
            suggestionsId: 'classeSuggestions',
            tagsId: 'classeTags',
            hiddenId: 'classeIds',
            containerId: 'classeContainer',
            allItems: @json($classes->map(fn($c) => ['id' => $c->id, 'name' => $c->nom])),
        },
        {
            searchId: 'serieSearch',
            suggestionsId: 'serieSuggestions',
            tagsId: 'serieTags',
            hiddenId: 'serieIds',
            containerId: 'serieContainer',
            allItems: @json($series->map(fn($s) => ['id' => $s->id, 'name' => $s->nom_serie ?? $s->nom])),
        }
    ];

    configs.forEach(config => {
        const searchInput = document.getElementById(config.searchId);
        const suggestions = document.getElementById(config.suggestionsId);
        const tagsContainer = document.getElementById(config.tagsId);
        const hiddenInput = document.getElementById(config.hiddenId);
        const container = document.getElementById(config.containerId);
        
        let selectedItems = new Set();
        let allItems = config.allItems || [];
        
        if (hiddenInput.value) {
            const ids = hiddenInput.value.split(',').filter(id => id);
            ids.forEach(id => {
                const item = allItems.find(i => String(i.id) === String(id));
                if (item) {
                    selectedItems.add(String(item.id));
                    addTag(item.id, item.name);
                }
            });
        }

        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            if (!query) {
                suggestions.classList.add('hidden');
                return;
            }
            
            const filtered = allItems.filter(item => 
                item.name.toLowerCase().includes(query) && 
                !selectedItems.has(String(item.id))
            );
            
            if (filtered.length === 0) {
                suggestions.innerHTML = `<div class="px-3 py-2 text-xs text-text-muted text-center">Aucun résultat</div>`;
            } else {
                suggestions.innerHTML = filtered.map(item => `
                    <div class="suggestion-item px-3 py-2 hover:bg-primary-fixed/30 cursor-pointer text-xs flex items-center justify-between" 
                         data-id="${item.id}" data-name="${item.name}">
                        <span>${item.name}</span>
                        <span class="text-xs text-primary font-bold">+</span>
                    </div>
                `).join('');
            }
            suggestions.classList.remove('hidden');
        });

        suggestions.addEventListener('click', function(e) {
            const item = e.target.closest('.suggestion-item');
            if (!item) return;
            const id = item.dataset.id;
            const name = item.dataset.name;
            
            if (!selectedItems.has(String(id))) {
                selectedItems.add(String(id));
                addTag(id, name);
                updateHidden();
                searchInput.value = '';
                suggestions.classList.add('hidden');
            }
        });

        document.addEventListener('click', function(e) {
            if (!container.contains(e.target)) suggestions.classList.add('hidden');
        });

        function addTag(id, name) {
            const tag = document.createElement('span');
            tag.className = 'tag-item';
            tag.dataset.id = id;
            tag.innerHTML = `<span>${name}</span><span class="remove-tag material-symbols-outlined">close</span>`;
            tag.querySelector('.remove-tag').addEventListener('click', function() {
                selectedItems.delete(String(id));
                tag.remove();
                updateHidden();
            });
            tagsContainer.insertBefore(tag, searchInput);
        }

        function updateHidden() {
            hiddenInput.value = Array.from(selectedItems).join(',');
        }
    });
});
</script>
@endpush
