@extends('sadmin.layouts.app')

@section('content')
<div class="flex flex-col gap-6 max-w-3xl mx-auto w-full">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('sadmin.clients.index') }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-surface-container-lowest hover:bg-surface-container-high transition-colors shadow-sm">
                <span class="material-symbols-outlined text-[18px] text-on-surface-variant hover:text-primary">arrow_back</span>
            </a>
            <h2 class="font-headline-md text-[18px] text-on-surface">Modifier le client</h2>
        </div>
    </div>

    <form method="POST" action="{{ route('sadmin.clients.update', $client) }}" class="bg-surface-container-lowest rounded-xl card-shadow border border-outline-variant p-5" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="font-label-md text-[11px] text-on-surface-variant font-medium mb-1 block">Nom</label>
                <input name="nom" value="{{ old('nom', $client->nom) }}" class="w-full px-3 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none text-[13px]" type="text">
                @error('nom')<p class="text-alert-red text-[11px] mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="font-label-md text-[11px] text-on-surface-variant font-medium mb-1 block">Prénom</label>
                <input name="prenom" value="{{ old('prenom', $client->prenom) }}" class="w-full px-3 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none text-[13px]" type="text">
                @error('prenom')<p class="text-alert-red text-[11px] mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="font-label-md text-[11px] text-on-surface-variant font-medium mb-1 block">Téléphone</label>
                <input name="telephone" value="{{ old('telephone', $client->telephone) }}" class="w-full px-3 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none text-[13px]" type="tel">
                @error('telephone')<p class="text-alert-red text-[11px] mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="font-label-md text-[11px] text-on-surface-variant font-medium mb-1 block">Email</label>
                <input name="email" value="{{ old('email', $client->email) }}" class="w-full px-3 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none text-[13px]" type="email">
                @error('email')<p class="text-alert-red text-[11px] mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2">
                <label class="font-label-md text-[11px] text-on-surface-variant font-medium mb-1 block">Adresse</label>
                <textarea name="adresse" rows="3" class="w-full px-3 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none text-[13px]">{{ old('adresse', $client->adresse) }}</textarea>
                @error('adresse')<p class="text-alert-red text-[11px] mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="font-label-md text-[11px] text-on-surface-variant font-medium mb-1 block">Statut</label>
                <select name="status" class="w-full px-3 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none text-[13px] appearance-none bg-white">
                    <option value="actif" {{ old('status', $client->status) === 'actif' ? 'selected' : '' }}>✅ Actif</option>
                    <option value="bloqué" {{ old('status', $client->status) === 'bloqué' ? 'selected' : '' }}>❌ Bloqué</option>
                </select>
                @error('status')<p class="text-alert-red text-[11px] mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="font-label-md text-[11px] text-on-surface-variant font-medium mb-1 block">Nouveau mot de passe (optionnel)</label>
                <input name="password" class="w-full px-3 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none text-[13px]" type="password">
                @error('password')<p class="text-alert-red text-[11px] mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="font-label-md text-[11px] text-on-surface-variant font-medium mb-1 block">Ville</label>
                <input name="ville" value="{{ old('ville', $client->ville) }}" class="w-full px-3 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none text-[13px]" type="text">
                @error('ville')<p class="text-alert-red text-[11px] mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="font-label-md text-[11px] text-on-surface-variant font-medium mb-1 block">Établissement</label>
                <select name="etablissement_id" class="w-full px-3 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none text-[13px] appearance-none bg-white">
                    <option value="">Sélectionner un établissement</option>
                    @foreach($etablissements as $etablissement)
                        <option value="{{ $etablissement->id }}" {{ (string) old('etablissement_id', $client->etablissement_id) === (string) $etablissement->id ? 'selected' : '' }}>
                            {{ $etablissement->nom }}
                        </option>
                    @endforeach
                </select>
                @error('etablissement_id')<p class="text-alert-red text-[11px] mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2">
                <label class="font-label-md text-[11px] text-on-surface-variant font-medium mb-1 block">Photo (optionnel)</label>
                <input name="photo" type="file" accept="image/*" class="w-full px-3 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none text-[13px]">
                @error('photo')<p class="text-alert-red text-[11px] mt-1">{{ $message }}</p>@enderror

                @if($client->photo)
                    <div class="mt-3">
                        <div class="text-text-muted text-[12px] mb-2">Photo actuelle :</div>
                        <img
                            src="{{ \Illuminate\Support\Facades\Storage::url($client->photo) }}"
                            alt="Photo client"
                            style="width:90px;height:90px;object-fit:cover;border-radius:12px;"
                        >
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('sadmin.clients.index') }}" class="px-4 py-1.5 rounded-lg border border-outline-variant text-on-surface-variant font-label-md text-[12px] hover:bg-white">Annuler</a>
            <button type="submit" class="px-4 py-1.5 rounded-lg bg-gradient-to-r from-primary to-primary-container text-white font-label-md text-[12px] hover:opacity-90 shadow-sm">Mettre à jour</button>
        </div>
    </form>
</div>
@endsection

