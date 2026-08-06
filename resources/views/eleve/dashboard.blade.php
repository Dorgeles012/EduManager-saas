@extends('eleve.layouts.app')

@section('title', 'Tableau de bord Élève')

@section('content')
<div class="mb-6">
    <h2 class="font-headline-lg text-headline-lg text-primary">Tableau de bord</h2>
    <p class="text-sm text-on-surface-variant">Bienvenue dans votre espace élève.</p>
</div>

<!-- Statistiques -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-surface-container-lowest p-6 rounded-xl custom-shadow border border-outline-variant flex items-center gap-4">
        <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center text-primary">
            <span class="material-symbols-outlined text-2xl">fact_check</span>
        </div>
        <div>
            <p class="text-label-sm text-text-muted uppercase tracking-wider font-bold">Mes notes</p>
            <h3 class="font-headline-md text-headline-md">{{ $counts['notes'] }}</h3>
        </div>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-xl custom-shadow border border-outline-variant flex items-center gap-4">
        <div class="w-12 h-12 bg-secondary/10 rounded-full flex items-center justify-center text-secondary">
            <span class="material-symbols-outlined text-2xl">description</span>
        </div>
        <div>
            <p class="text-label-sm text-text-muted uppercase tracking-wider font-bold">Bulletins</p>
            <h3 class="font-headline-md text-headline-md">{{ $counts['bulletins'] }}</h3>
        </div>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-xl custom-shadow border border-outline-variant flex items-center gap-4">
        <div class="w-12 h-12 bg-warning-amber/10 rounded-full flex items-center justify-center text-warning-amber">
            <span class="material-symbols-outlined text-2xl">payments</span>
        </div>
        <div>
            <p class="text-label-sm text-text-muted uppercase tracking-wider font-bold">Scolarités</p>
            <h3 class="font-headline-md text-headline-md">{{ $counts['scolarites'] }}</h3>
        </div>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-xl custom-shadow border border-outline-variant flex items-center gap-4">
        <div class="w-12 h-12 bg-success-green/10 rounded-full flex items-center justify-center text-success-green">
            <span class="material-symbols-outlined text-2xl">calendar_month</span>
        </div>
        <div>
            <p class="text-label-sm text-text-muted uppercase tracking-wider font-bold">Cours</p>
            <h3 class="font-headline-md text-headline-md">{{ $counts['cours'] }}</h3>
        </div>
    </div>
</div>

<!-- Mes informations -->
<div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant overflow-hidden">
    <div class="p-6 border-b border-outline-variant">
        <h4 class="font-headline-md text-headline-md flex items-center gap-2">Mes informations</h4>
    </div>
    <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full overflow-hidden border border-outline-variant">
                <img class="w-full h-full object-cover" src="{{ $eleve->photo_url ?: 'https://ui-avatars.com/api/?background=1f108e&color=fff&name='.urlencode($eleve->nom.' '.$eleve->prenom) }}" alt="Photo">
            </div>
            <div>
                <p class="text-label-sm text-text-muted uppercase tracking-wider">Nom</p>
                <p class="font-body-md font-medium">{{ $eleve->nom }} {{ $eleve->prenom }}</p>
            </div>
        </div>
        <div>
            <p class="text-label-sm text-text-muted uppercase tracking-wider">Matricule</p>
            <p class="font-body-md font-medium">{{ $eleve->matricule }}</p>
        </div>
        <div>
            <p class="text-label-sm text-text-muted uppercase tracking-wider">Classe</p>
            <p class="font-body-md font-medium">{{ $eleve->classe?->nom ?? '—' }}</p>
        </div>
        <div>
            <p class="text-label-sm text-text-muted uppercase tracking-wider">Niveau</p>
            <p class="font-body-md font-medium">{{ $eleve->niveau?->nom ?? $eleve->classe?->niveau?->nom ?? '—' }}</p>
        </div>
        <div>
            <p class="text-label-sm text-text-muted uppercase tracking-wider">Série</p>
            <p class="font-body-md font-medium">{{ $eleve->serie?->nom_serie ?? '—' }}</p>
        </div>
        <div>
            <p class="text-label-sm text-text-muted uppercase tracking-wider">Établissement</p>
            <p class="font-body-md font-medium">{{ $eleve->etablissement?->nom ?? '—' }}</p>
        </div>
    </div>
</div>
@endsection
