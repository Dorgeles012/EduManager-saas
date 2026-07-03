@extends('sadmin.layouts.app')

@section('content')
<div class="flex flex-col gap-6 max-w-3xl mx-auto w-full">
    <!-- En-tête avec bouton retour -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('sadmin.etablissement') }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-surface-container-lowest hover:bg-surface-container-high transition-colors shadow-sm">
                <span class="material-symbols-outlined text-[18px] text-on-surface-variant hover:text-primary">arrow_back</span>
            </a>
            <h2 class="font-headline-md text-[18px] text-on-surface">Modifier un établissement</h2>
        </div>
    
    </div>

    <form method="POST" action="{{ route('sadmin.etablissements.update', $etablissement) }}" enctype="multipart/form-data" class="bg-surface-container-lowest rounded-xl card-shadow border border-outline-variant p-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Colonne 1 -->
            <div>
                <label class="font-label-md text-[11px] text-on-surface-variant font-medium mb-1 block">Nom</label>
                <input name="nom" value="{{ old('nom', $etablissement->nom) }}" class="w-full px-3 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none text-[13px]" type="text">
                @error('nom')<p class="text-alert-red text-[11px] mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="font-label-md text-[11px] text-on-surface-variant font-medium mb-1 block">Acronyme</label>
                <input name="acronyme" value="{{ old('acronyme', $etablissement->acronyme) }}" class="w-full px-3 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none text-[13px]" type="text">
                @error('acronyme')<p class="text-alert-red text-[11px] mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="font-label-md text-[11px] text-on-surface-variant font-medium mb-1 block">Type d'établissement</label>
                <select name="type_etablissement" class="w-full px-3 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none text-[13px] appearance-none bg-white">
                    @foreach(['primaire' => 'Primaire', 'college' => 'Collège', 'lycee' => 'Lycée', 'universite' => 'Université', 'grande_ecole' => 'Grande École'] as $key => $label)
                        <option value="{{ $key }}" {{ old('type_etablissement', $etablissement->type_etablissement) === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('type_etablissement')<p class="text-alert-red text-[11px] mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="font-label-md text-[11px] text-on-surface-variant font-medium mb-1 block">Statut</label>
                <select name="statut" class="w-full px-3 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none text-[13px] appearance-none bg-white">
                    <option value="active" {{ old('statut', $etablissement->statut) === 'active' ? 'selected' : '' }}>✅ Actif</option>
                    <option value="inactive" {{ old('statut', $etablissement->statut) === 'inactive' ? 'selected' : '' }}>❌ Inactif</option>
                </select>
                @error('statut')<p class="text-alert-red text-[11px] mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Colonne 2 -->
            <div>
                <label class="font-label-md text-[11px] text-on-surface-variant font-medium mb-1 block">Email</label>
                <input name="email" value="{{ old('email', $etablissement->email) }}" class="w-full px-3 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none text-[13px]" type="email">
                @error('email')<p class="text-alert-red text-[11px] mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="font-label-md text-[11px] text-on-surface-variant font-medium mb-1 block">Téléphone</label>
                <input name="telephone" value="{{ old('telephone', $etablissement->telephone) }}" class="w-full px-3 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none text-[13px]" type="tel">
                @error('telephone')<p class="text-alert-red text-[11px] mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Adresse -->
            <div class="md:col-span-2">
                <label class="font-label-md text-[11px] text-on-surface-variant font-medium mb-1 block">Adresse</label>
                <textarea name="adresse" class="w-full px-3 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none text-[13px]" rows="3">{{ old('adresse', $etablissement->adresse) }}</textarea>
                @error('adresse')<p class="text-alert-red text-[11px] mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <!-- Bloc Logo (aperçu + upload) -->
        <div class="mt-5 border border-outline-variant rounded-xl p-4 bg-white/30">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <label class="font-label-md text-[11px] text-on-surface-variant font-medium mb-1 block">Logo</label>
                    <p class="text-[11px] text-text-muted">Laisser vide pour conserver le logo actuel</p>
                </div>
            </div>

            <div class="mt-3 flex flex-col md:flex-row gap-4 items-start">
                <!-- Aperçu du logo avec gestion d'icône -->
                <div class="w-20 h-20 rounded-xl overflow-hidden border border-outline-variant bg-surface-container-low/30 flex items-center justify-center flex-shrink-0">
                    @php
                        $hasLogo = $etablissement->logo_url && 
                                   $etablissement->logo_url !== asset('images/default-school.png') && 
                                   !str_contains($etablissement->logo_url, 'default-school');
                    @endphp
                    
                    @if($hasLogo)
                        <img id="logo-preview-edit"
                             src="{{ $etablissement->logo_url }}"
                             alt="Logo établissement"
                             class="w-full h-full object-cover">
                    @else
                        <span id="logo-icon-edit" class="material-symbols-outlined text-3xl text-text-muted">photo</span>
                        <img id="logo-preview-edit" 
                             src="#" 
                             alt="Logo" 
                             class="w-full h-full object-cover hidden">
                    @endif
                </div>

                <div class="flex-1">
                    <label class="cursor-pointer block">
                        <div class="flex items-center gap-3 px-4 py-2.5 border border-outline-variant rounded-lg hover:border-primary hover:bg-surface-container-low transition-all">
                            <span class="material-symbols-outlined text-primary text-[20px]">upload_file</span>
                            <span class="text-body-sm text-on-surface font-body-sm">Choisir un fichier</span>
                        </div>
                        <input id="logo-input-edit" name="logo" type="file" accept="image/*" class="hidden" onchange="previewLogoEdit(event)" />
                    </label>
                    <p id="logo-filename-edit" class="text-[12px] text-text-muted mt-1.5">Aucun fichier sélectionné</p>
                    @error('logo')<p class="text-alert-red text-[11px] mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('sadmin.etablissement') }}" class="px-4 py-1.5 rounded-lg border border-outline-variant text-on-surface-variant font-label-md text-[12px] hover:bg-white">Annuler</a>
            <button type="submit" class="px-4 py-1.5 rounded-lg bg-gradient-to-r from-primary to-primary-container text-white font-label-md text-[12px] hover:opacity-90 shadow-sm">Mettre à jour</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function previewLogoEdit(event) {
        const file = event.target.files && event.target.files[0] ? event.target.files[0] : null;
        const preview = document.getElementById('logo-preview-edit');
        const icon = document.getElementById('logo-icon-edit');
        const filename = document.getElementById('logo-filename-edit');

        if (!file) {
            // Réinitialiser à l'état initial
            if (preview) {
                preview.src = '#';
                preview.classList.add('hidden');
            }
            if (icon) {
                icon.classList.remove('hidden');
            }
            if (filename) filename.textContent = 'Aucun fichier sélectionné';
            return;
        }

        // Afficher l'image et cacher l'icône
        if (preview) {
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('hidden');
        }
        if (icon) {
            icon.classList.add('hidden');
        }
        if (filename) filename.textContent = file.name;
    }

    // Initialisation : s'assurer que l'icône est visible si pas de logo
    document.addEventListener('DOMContentLoaded', function() {
        const preview = document.getElementById('logo-preview-edit');
        const icon = document.getElementById('logo-icon-edit');
        
        if (preview && icon) {
            // Si l'image n'a pas de source valide ou est cachée, afficher l'icône
            if (preview.classList.contains('hidden') || !preview.src || preview.src === '#') {
                icon.classList.remove('hidden');
            }
        }
    });
</script>
@endsection
