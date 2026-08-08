@extends('parent.layouts.app')

@section('title', 'Mon profil')

@section('content')
<div class="mb-6">
    <h2 class="font-headline-lg text-headline-lg text-primary">Mon profil</h2>
    <p class="text-sm text-on-surface-variant">Gérez vos informations personnelles et votre sécurité</p>
</div>

@if(session('success'))
    <div class="mb-4 rounded-lg border border-success-green/20 bg-success-green/10 px-4 py-3 text-success-green">
        {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Photo & identité -->
    <div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant p-6 lg:col-span-1 h-fit">
        <div class="flex flex-col items-center text-center">
            <img class="w-28 h-28 rounded-full object-cover border-4 border-primary-fixed shadow" src="{{ $parent->image ? asset('storage/'.$parent->image) : 'https://ui-avatars.com/api/?background=1f108e&color=fff&name='.urlencode($parent->name ?? 'Parent') }}" alt="Photo de profil">
            <h3 class="font-headline-md text-headline-md mt-4">{{ $parent->prenom }} {{ $parent->nom }}</h3>
            <p class="text-sm text-text-muted">{{ $parent->email }}</p>
            <p class="text-sm text-text-muted">{{ $parent->telephone }}</p>
        </div>

        <form method="POST" action="{{ route('parent.profil.photo') }}" enctype="multipart/form-data" class="mt-6 space-y-3 border-t border-outline-variant pt-6">
            @csrf
            <label class="block">
                <span class="font-label-md text-label-md text-on-surface-variant">Changer ma photo</span>
                <input type="file" name="image" accept="image/*" class="mt-1 block w-full text-sm text-on-surface-variant file:mr-4 file:rounded-lg file:border-0 file:bg-primary/10 file:px-4 file:py-2 file:text-primary" required>
            </label>
            <x-input-error :messages="$errors->get('image')" class="mt-2" />
            <button type="submit" class="w-full bg-primary text-on-primary px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90">Mettre à jour la photo</button>
        </form>
    </div>

    <!-- Informations -->
    <div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant p-6 lg:col-span-2">
        <h4 class="font-headline-md text-headline-md mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">badge</span> Mes informations
        </h4>
        <form method="POST" action="{{ route('parent.profil.update') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="font-label-md text-label-md text-on-surface-variant">Nom</label>
                    <input type="text" name="nom" value="{{ old('nom', $parent->nom) }}" required class="w-full bg-white border border-outline-variant rounded-lg px-4 py-2.5 text-body-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none">
                    <x-input-error :messages="$errors->get('nom')" class="mt-2" />
                </div>
                <div class="space-y-1">
                    <label class="font-label-md text-label-md text-on-surface-variant">Prénom</label>
                    <input type="text" name="prenom" value="{{ old('prenom', $parent->prenom) }}" class="w-full bg-white border border-outline-variant rounded-lg px-4 py-2.5 text-body-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none">
                    <x-input-error :messages="$errors->get('prenom')" class="mt-2" />
                </div>
                <div class="space-y-1">
                    <label class="font-label-md text-label-md text-on-surface-variant">Téléphone</label>
                    <input type="text" name="telephone" value="{{ old('telephone', $parent->telephone) }}" class="w-full bg-white border border-outline-variant rounded-lg px-4 py-2.5 text-body-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none">
                    <x-input-error :messages="$errors->get('telephone')" class="mt-2" />
                </div>
                <div class="space-y-1">
                    <label class="font-label-md text-label-md text-on-surface-variant">Adresse</label>
                    <input type="text" name="adresse" value="{{ old('adresse', $parent->adresse) }}" class="w-full bg-white border border-outline-variant rounded-lg px-4 py-2.5 text-body-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none">
                    <x-input-error :messages="$errors->get('adresse')" class="mt-2" />
                </div>
                <div class="space-y-1">
                    <label class="font-label-md text-label-md text-on-surface-variant">Ville</label>
                    <input type="text" name="ville" value="{{ old('ville', $parent->ville) }}" class="w-full bg-white border border-outline-variant rounded-lg px-4 py-2.5 text-body-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none">
                    <x-input-error :messages="$errors->get('ville')" class="mt-2" />
                </div>
            </div>

            <button type="submit" class="bg-primary text-on-primary px-4 py-2.5 rounded-lg text-sm font-medium hover:opacity-90">Enregistrer mes informations</button>
        </form>
    </div>

    <!-- Mot de passe -->
    <div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant p-6 lg:col-span-3">
        <h4 class="font-headline-md text-headline-md mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">lock</span> Sécurité & mot de passe
        </h4>
        <form method="POST" action="{{ route('parent.profil.password') }}" class="space-y-4 max-w-2xl">
            @csrf
            @method('PUT')

            <div class="space-y-1">
                <label class="font-label-md text-label-md text-on-surface-variant">Mot de passe actuel</label>
                <input type="password" name="current_password" required autocomplete="current-password" class="w-full bg-white border border-outline-variant rounded-lg px-4 py-2.5 text-body-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none">
                <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="font-label-md text-label-md text-on-surface-variant">Nouveau mot de passe</label>
                    <input type="password" name="password" required autocomplete="new-password" class="w-full bg-white border border-outline-variant rounded-lg px-4 py-2.5 text-body-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none">
                    <p class="text-xs text-text-muted">Minimum 8 caractères.</p>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                <div class="space-y-1">
                    <label class="font-label-md text-label-md text-on-surface-variant">Confirmer le nouveau mot de passe</label>
                    <input type="password" name="password_confirmation" required autocomplete="new-password" class="w-full bg-white border border-outline-variant rounded-lg px-4 py-2.5 text-body-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none">
                </div>
            </div>

            <button type="submit" class="bg-primary text-on-primary px-4 py-2.5 rounded-lg text-sm font-medium hover:opacity-90">Modifier mon mot de passe</button>
        </form>
    </div>
</div>
@endsection
