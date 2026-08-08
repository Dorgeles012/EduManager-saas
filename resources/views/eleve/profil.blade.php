@extends('eleve.layouts.app')

@section('title', 'Mon profil')

@section('content')
<div class="mb-6">
    <h2 class="font-headline-lg text-headline-lg text-primary">Mon profil</h2>
    <p class="text-sm text-on-surface-variant">Mes informations personnelles et scolaires</p>
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
            <img class="w-28 h-28 rounded-full object-cover border-4 border-primary-fixed shadow" src="{{ $eleve->photo_url ?: 'https://ui-avatars.com/api/?background=1f108e&color=fff&name='.urlencode($eleve->nom.' '.$eleve->prenom) }}" alt="Photo">
            <h3 class="font-headline-md text-headline-md mt-4">{{ $eleve->prenom }} {{ $eleve->nom }}</h3>
            <p class="text-sm text-text-muted">{{ $eleve->matricule }}</p>
        </div>
        <div class="mt-6 space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-text-muted">Classe</span><span class="font-medium">{{ $eleve->classe?->nom ?? '—' }}</span></div>
            <div class="flex justify-between"><span class="text-text-muted">Niveau</span><span class="font-medium">{{ $eleve->niveau?->nom ?? $eleve->classe?->niveau?->nom ?? '—' }}</span></div>
            <div class="flex justify-between"><span class="text-text-muted">Série</span><span class="font-medium">{{ $eleve->serie?->nom_serie ?? '—' }}</span></div>
            <div class="flex justify-between"><span class="text-text-muted">Statut</span><span class="font-medium">{{ ucfirst($eleve->statut) }}</span></div>
        </div>
    </div>

    <!-- Informations détaillées -->
    <div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant p-6 lg:col-span-2">
        <h4 class="font-headline-md text-headline-md mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">badge</span> Informations personnelles
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-text-muted">Nom</p>
                <p class="font-medium">{{ $eleve->nom }}</p>
            </div>
            <div>
                <p class="text-text-muted">Prénom</p>
                <p class="font-medium">{{ $eleve->prenom }}</p>
            </div>
            <div>
                <p class="text-text-muted">Matricule</p>
                <p class="font-medium">{{ $eleve->matricule }}</p>
            </div>
            <div>
                <p class="text-text-muted">Sexe</p>
                <p class="font-medium">{{ $eleve->sexe }}</p>
            </div>
            <div>
                <p class="text-text-muted">Date de naissance</p>
                <p class="font-medium">{{ $eleve->date_naissance?->format('d/m/Y') ?? '—' }}</p>
            </div>
            <div>
                <p class="text-text-muted">Lieu de naissance</p>
                <p class="font-medium">{{ $eleve->lieu_naissance ?? '—' }}</p>
            </div>
            <div>
                <p class="text-text-muted">Nationalité</p>
                <p class="font-medium">{{ $eleve->nationalite ?? '—' }}</p>
            </div>
            <div>
                <p class="text-text-muted">Établissement</p>
                <p class="font-medium">{{ $eleve->etablissement?->nom ?? '—' }}</p>
            </div>
        </div>
    </div>

    <!-- Mot de passe -->
    <div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant p-6 lg:col-span-3">
        <h4 class="font-headline-md text-headline-md mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">lock</span> Mot de passe
        </h4>
        <form method="POST" action="{{ route('eleve.profil.password') }}" class="space-y-4 max-w-2xl">
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
                    <label class="font-label-md text-label-md text-on-surface-variant">Confirmer</label>
                    <input type="password" name="password_confirmation" required autocomplete="new-password" class="w-full bg-white border border-outline-variant rounded-lg px-4 py-2.5 text-body-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none">
                </div>
            </div>

            <button type="submit" class="bg-primary text-on-primary px-4 py-2.5 rounded-lg text-sm font-medium hover:opacity-90">Modifier mon mot de passe</button>
        </form>
    </div>
</div>
@endsection
