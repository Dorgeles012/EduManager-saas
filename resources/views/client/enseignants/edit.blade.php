@extends('client.layouts.app')
@section('title', 'Modifier un enseignant')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-4 mb-1">
        <a href="{{ route('client.enseignant') }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg hover:bg-gray-100 transition-all text-gray-600 hover:text-primary flex-shrink-0">
            <span class="material-symbols-outlined text-2xl">arrow_back</span>
        </a>
        <div>
            <h2 class="font-headline-lg text-headline-lg text-primary">
                Modifier un enseignant
            </h2>
            <p class="text-sm text-gray-500 mt-0.5">Mettez à jour les informations et affectations.</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-8 py-5 border-b border-gray-100">
        <h4 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">assignment_ind</span>
            Modifier l'enseignant
        </h4>
    </div>

    <div class="p-8">
        <form method="POST" action="{{ route('client.enseignants.update', $enseignant->id) }}" enctype="multipart/form-data" id="teacherForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" value="{{ $enseignant->id }}" />

            <!-- Formulaire en grid simple -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Colonne gauche -->
                <div class="space-y-5">
                    <!-- Nom -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom <span class="text-red-500">*</span></label>
                        <input name="nom" type="text" value="{{ old('nom', $enseignant->nom) }}" 
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-gray-50/30" 
                               placeholder="Entrez le nom" required>
                        @error('nom')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>

                    <!-- Prénoms -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Prénoms <span class="text-red-500">*</span></label>
                        <input name="prenoms" type="text" value="{{ old('prenoms', $enseignant->prenoms) }}" 
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-gray-50/30" 
                               placeholder="Entrez les prénoms" required>
                        @error('prenoms')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>

                    <!-- Matricule -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Matricule <span class="text-red-500">*</span></label>
                        <input name="matricule" type="text" value="{{ old('matricule', $enseignant->matricule) }}" 
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-gray-50/30" 
                               placeholder="Ex: ENS-2024-001" required>
                        @error('matricule')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                        <input name="email" type="email" value="{{ old('email', $enseignant->email) }}" 
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-gray-50/30" 
                               placeholder="exemple@mail.com" required>
                        @error('email')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>

                    <!-- Téléphone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Téléphone <span class="text-red-500">*</span></label>
                        <input name="telephone" type="tel" value="{{ old('telephone', $enseignant->telephone) }}" 
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-gray-50/30" 
                               placeholder="01 02 03 04 05" required>
                        @error('telephone')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>

                    <!-- Années d'enseignement -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Années d'enseignement <span class="text-red-500">*</span></label>
                        <input name="nombre_annees_enseignement" type="number" min="0" max="80" 
                               value="{{ old('nombre_annees_enseignement', $enseignant->nombre_annees_enseignement) }}" 
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-gray-50/30" 
                               placeholder="0" required>
                        @error('nombre_annees_enseignement')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <!-- Colonne droite -->
                <div class="space-y-5">
                    <!-- Sexe -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Sexe <span class="text-red-500">*</span></label>
                        <div class="flex gap-6 pt-1">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="sexe" value="Masculin" {{ old('sexe', $enseignant->sexe) === 'Masculin' ? 'checked' : '' }} required 
                                       class="w-4 h-4 text-primary focus:ring-primary">
                                <span class="text-sm text-gray-700">👨 Masculin</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="sexe" value="Féminin" {{ old('sexe', $enseignant->sexe) === 'Féminin' ? 'checked' : '' }} 
                                       class="w-4 h-4 text-primary focus:ring-primary">
                                <span class="text-sm text-gray-700">👩 Féminin</span>
                            </label>
                        </div>
                        @error('sexe')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>

                    <!-- Photo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Photo de profil</label>
                        <div class="flex items-center gap-4">
                            <div class="w-20 h-20 rounded-full bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden flex-shrink-0" id="photoPreview">
                                @if($enseignant->photo)
                                    <img src="{{ asset('storage/' . $enseignant->photo) }}" alt="Photo" class="w-full h-full object-cover">
                                @else
                                    <span class="material-symbols-outlined text-gray-400 text-4xl">person</span>
                                @endif
                            </div>
                            <div>
                                <label class="cursor-pointer inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-all">
                                    <span class="material-symbols-outlined mr-2 text-base">upload</span>
                                    Changer
                                    <input name="photo" type="file" accept="image/*" class="hidden" onchange="previewPhoto(this)">
                                </label>
                                <p class="text-xs text-gray-400 mt-1">PNG, JPG, WEBP • Max 2MB</p>
                                @if($enseignant->photo)
                                    <p class="text-xs text-gray-400 mt-0.5">Photo actuelle conservée si vous ne changez pas</p>
                                @endif
                            </div>
                        </div>
                        @error('photo')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>

                    <!-- Matières -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Matières enseignées <span class="text-red-500">*</span></label>
                        <div class="relative" id="matiereContainer">
                            <div class="flex flex-wrap gap-2 p-2 border border-gray-200 rounded-lg focus-within:ring-2 focus-within:ring-primary/20 focus-within:border-primary transition-all bg-white min-h-[42px]" id="matiereTags">
                                <input type="text" id="matiereSearch" 
                                       placeholder="Tapez pour rechercher..." 
                                       class="flex-1 min-w-[120px] border-0 outline-none bg-transparent text-sm p-1">
                            </div>
                            <div id="matiereSuggestions" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto hidden">
                                @foreach($matieres as $m)
                                    <div class="suggestion-item px-4 py-2 hover:bg-primary/5 cursor-pointer text-sm flex items-center justify-between" 
                                         data-id="{{ $m->id }}" data-name="{{ $m->nom }}">
                                        <span>{{ $m->nom }}</span>
                                        <span class="text-xs text-gray-400">+</span>
                                    </div>
                                @endforeach
                            </div>
                            <input type="hidden" id="matiereIds" 
                                   value="{{ implode(',', old('matiere_ids', $enseignant->matieres->pluck('id')->all())) }}">
                        </div>
                        @error('matiere_ids')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>

                    <!-- Classes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Classes affectées <span class="text-red-500">*</span></label>
                        <div class="relative" id="classeContainer">
                            <div class="flex flex-wrap gap-2 p-2 border border-gray-200 rounded-lg focus-within:ring-2 focus-within:ring-primary/20 focus-within:border-primary transition-all bg-white min-h-[42px]" id="classeTags">
                                <input type="text" id="classeSearch" 
                                       placeholder="Tapez pour rechercher..." 
                                       class="flex-1 min-w-[120px] border-0 outline-none bg-transparent text-sm p-1">
                            </div>
                            <div id="classeSuggestions" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto hidden">
                                @foreach($classes as $c)
                                    <div class="suggestion-item px-4 py-2 hover:bg-primary/5 cursor-pointer text-sm flex items-center justify-between" 
                                         data-id="{{ $c->id }}" data-name="{{ $c->nom }}">
                                        <span>{{ $c->nom }}</span>
                                        <span class="text-xs text-gray-400">+</span>
                                    </div>
                                @endforeach
                            </div>
                            <input type="hidden" id="classeIds" 
                                   value="{{ implode(',', old('classe_ids', $enseignant->classes->pluck('id')->all())) }}">
                        </div>
                        @error('classe_ids')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>

                    <!-- Séries -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Séries affectées</label>
                        <div class="relative" id="serieContainer">
                            <div class="flex flex-wrap gap-2 p-2 border border-gray-200 rounded-lg focus-within:ring-2 focus-within:ring-primary/20 focus-within:border-primary transition-all bg-white min-h-[42px]" id="serieTags">
                                <input type="text" id="serieSearch" 
                                       placeholder="Tapez pour rechercher..." 
                                       class="flex-1 min-w-[120px] border-0 outline-none bg-transparent text-sm p-1">
                            </div>
                            <div id="serieSuggestions" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto hidden">
                                @foreach($series as $s)
                                    <div class="suggestion-item px-4 py-2 hover:bg-primary/5 cursor-pointer text-sm flex items-center justify-between" 
                                         data-id="{{ $s->id }}" data-name="{{ $s->nom_serie ?? $s->nom }}">
                                        <span>{{ $s->nom_serie ?? $s->nom }}</span>
                                        <span class="text-xs text-gray-400">+</span>
                                    </div>
                                @endforeach
                            </div>
                            <input type="hidden" id="serieIds" 
                                   value="{{ implode(',', old('serie_ids', $enseignant->series->pluck('id')->all())) }}">
                        </div>
                        @error('serie_ids')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="mt-8 pt-6 border-t border-gray-200 flex flex-col sm:flex-row gap-3 justify-end">
                <a href="{{ route('client.enseignant') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium text-sm rounded-lg hover:bg-gray-50 transition-all text-center">
                    Annuler
                </a>
                <button type="submit" class="px-8 py-2.5 bg-primary text-white font-medium text-sm rounded-lg hover:bg-primary/90 shadow-sm active:scale-95 transition-all flex items-center justify-center gap-2">
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
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    
    /* Tags styles - épurés */
    .tag-item {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #eef2ff;
        color: #4f46e5;
        padding: 2px 8px 2px 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 500;
        animation: tagFadeIn 0.15s ease-out;
    }
    
    .tag-item .remove-tag {
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        transition: all 0.2s;
        font-size: 16px;
        line-height: 1;
        color: #4f46e5;
    }
    
    .tag-item .remove-tag:hover {
        background: #c7d2fe;
        color: #3730a3;
    }
    
    @keyframes tagFadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
    
    .suggestion-item:hover .text-gray-400 {
        color: #4f46e5 !important;
    }
    
    /* Suggestions scroll */
    #matiereSuggestions::-webkit-scrollbar,
    #classeSuggestions::-webkit-scrollbar,
    #serieSuggestions::-webkit-scrollbar {
        width: 5px;
    }
    #matiereSuggestions::-webkit-scrollbar-track,
    #classeSuggestions::-webkit-scrollbar-track,
    #serieSuggestions::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    #matiereSuggestions::-webkit-scrollbar-thumb,
    #classeSuggestions::-webkit-scrollbar-thumb,
    #serieSuggestions::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 10px;
    }
    #matiereSuggestions::-webkit-scrollbar-thumb:hover,
    #classeSuggestions::-webkit-scrollbar-thumb:hover,
    #serieSuggestions::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Succès',
            text: @json(session('success')),
            timer: 1000,
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                setTimeout(() => {
                    window.location.href = @json(route('client.enseignant'));
                }, 800);
            }
        });
    @endif
    
    // Configuration des autocomplete
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

    configs.forEach(config => initAutocomplete(config));

    function initAutocomplete(config) {
        const searchInput = document.getElementById(config.searchId);
        const suggestions = document.getElementById(config.suggestionsId);
        const tagsContainer = document.getElementById(config.tagsId);
        const hiddenInput = document.getElementById(config.hiddenId);
        const container = document.getElementById(config.containerId);
        
        let selectedItems = new Set();
        let allItems = config.allItems || [];
        
        // Charger les valeurs existantes
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

        // Filtrer et afficher les suggestions
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            if (query.length === 0) {
                suggestions.classList.add('hidden');
                return;
            }
            
            const filtered = allItems.filter(item => 
                item.name.toLowerCase().includes(query) && 
                !selectedItems.has(String(item.id))
            );
            
            if (filtered.length === 0) {
                suggestions.innerHTML = `
                    <div class="px-4 py-2 text-sm text-gray-400 text-center">
                        Aucun résultat
                    </div>
                `;
            } else {
                suggestions.innerHTML = filtered.map(item => `
                    <div class="suggestion-item px-4 py-2 hover:bg-primary/5 cursor-pointer text-sm flex items-center justify-between" 
                         data-id="${item.id}" data-name="${item.name}">
                        <span>${item.name}</span>
                        <span class="text-xs text-gray-400">+</span>
                    </div>
                `).join('');
            }
            
            suggestions.classList.remove('hidden');
        });

        // Gestion des clics sur les suggestions
        suggestions.addEventListener('click', function(e) {
            const item = e.target.closest('.suggestion-item');
            if (!item) return;
            
            const id = item.dataset.id;
            const name = item.dataset.name;
            
            if (!selectedItems.has(String(id))) {
                selectedItems.add(String(id));
                addTag(id, name);
                updateHiddenInput();
                searchInput.value = '';
                suggestions.classList.add('hidden');
            }
        });

        // Raccourcis clavier
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const firstSuggestion = suggestions.querySelector('.suggestion-item');
                if (firstSuggestion) firstSuggestion.click();
            }
            if (e.key === 'Escape') {
                suggestions.classList.add('hidden');
                this.blur();
            }
            if (e.key === 'Backspace' && this.value === '' && selectedItems.size > 0) {
                const lastTag = tagsContainer.querySelector('.tag-item:last-child');
                if (lastTag) {
                    const removeBtn = lastTag.querySelector('.remove-tag');
                    if (removeBtn) removeBtn.click();
                }
            }
        });

        // Cacher les suggestions
        document.addEventListener('click', function(e) {
            if (!container.contains(e.target)) {
                suggestions.classList.add('hidden');
            }
        });

        function addTag(id, name) {
            const tag = document.createElement('span');
            tag.className = 'tag-item';
            tag.dataset.id = id;
            tag.innerHTML = `
                ${name}
                <span class="remove-tag" data-id="${id}">×</span>
            `;
            
            tag.querySelector('.remove-tag').addEventListener('click', function() {
                const id = this.dataset.id;
                selectedItems.delete(String(id));
                tag.remove();
                updateHiddenInput();
                const query = searchInput.value.toLowerCase().trim();
                if (query) searchInput.dispatchEvent(new Event('input'));
            });
            
            tagsContainer.insertBefore(tag, searchInput);
        }

        function updateHiddenInput() {
            hiddenInput.value = Array.from(selectedItems).join(',');
            hiddenInput.dispatchEvent(new Event('change'));
        }
    }

    // Prévisualisation de la photo
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
            preview.innerHTML = `<span class="material-symbols-outlined text-gray-400 text-4xl">person</span>`;
        }
    };

    // Validation du formulaire
    document.getElementById('teacherForm').addEventListener('submit', function(e) {
        const matiereIds = document.getElementById('matiereIds').value;
        const classeIds = document.getElementById('classeIds').value;
        
        if (!matiereIds || matiereIds.split(',').filter(id => id).length === 0) {
            e.preventDefault();
            Swal.fire({ 
                icon: 'error', 
                title: 'Erreur', 
                text: 'Veuillez sélectionner au moins une matière.',
                showConfirmButton: false,
                timer: 3000
            });
            document.getElementById('matiereSearch').focus();
            return false;
        }
        
        if (!classeIds || classeIds.split(',').filter(id => id).length === 0) {
            e.preventDefault();
            Swal.fire({ 
                icon: 'error', 
                title: 'Erreur', 
                text: 'Veuillez sélectionner au moins une classe.',
                showConfirmButton: false,
                timer: 3000
            });
            document.getElementById('classeSearch').focus();
            return false;
        }
        
        document.querySelectorAll('.relation-id-input').forEach(input => input.remove());
        [['matiere_ids', matiereIds], ['classe_ids', classeIds], ['serie_ids', document.getElementById('serieIds').value]].forEach(([field, ids]) => ids.split(',').filter(Boolean).forEach(id => { const input = document.createElement('input'); input.type = 'hidden'; input.name = `${field}[]`; input.value = id; input.className = 'relation-id-input'; this.appendChild(input); }));
        return true;
    });
});
</script>
@endpush