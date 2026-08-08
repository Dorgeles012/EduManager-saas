@extends('parent.layouts.app')

@section('title', 'Profil de '.$eleve->nom.' '.$eleve->prenom)

@section('content')
<div class="mb-6 flex items-center justify-between gap-3 flex-wrap">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-primary">Profil de l'enfant</h2>
        <p class="text-sm text-on-surface-variant">Informations scolaires et personnelles de {{ $eleve->nom }} {{ $eleve->prenom }}</p>
    </div>
    <a class="inline-flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-primary rounded-lg" href="{{ route('parent.enfants') }}">
        <span class="material-symbols-outlined text-lg">arrow_back</span> Retour
    </a>
</div>

<!-- Statistiques -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-surface-container-lowest p-6 rounded-xl custom-shadow border border-outline-variant flex items-center gap-4">
        <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center text-primary">
            <span class="material-symbols-outlined text-2xl">fact_check</span>
        </div>
        <div>
            <p class="text-label-sm text-text-muted uppercase tracking-wider font-bold">Notes</p>
            <h3 class="font-headline-md text-headline-md">{{ $totalNotes }}</h3>
        </div>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-xl custom-shadow border border-outline-variant flex items-center gap-4">
        <div class="w-12 h-12 bg-secondary/10 rounded-full flex items-center justify-center text-secondary">
            <span class="material-symbols-outlined text-2xl">description</span>
        </div>
        <div>
            <p class="text-label-sm text-text-muted uppercase tracking-wider font-bold">Bulletins</p>
            <h3 class="font-headline-md text-headline-md">{{ $totalBulletins }}</h3>
        </div>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-xl custom-shadow border border-outline-variant flex items-center gap-4">
        <div class="w-12 h-12 bg-warning-amber/10 rounded-full flex items-center justify-center text-warning-amber">
            <span class="material-symbols-outlined text-2xl">school</span>
        </div>
        <div>
            <p class="text-label-sm text-text-muted uppercase tracking-wider font-bold">Classe</p>
            <h3 class="font-headline-md text-headline-md truncate">{{ $eleve->classe?->nom ?? '—' }}</h3>
        </div>
    </div>
</div>

<!-- Informations -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant overflow-hidden">
        <div class="bg-gradient-to-br from-primary/10 to-primary-fixed/20 p-6 flex items-center gap-4">
            <img class="w-20 h-20 rounded-full object-cover border-2 border-white shadow" src="{{ $eleve->photo_url ?: 'https://ui-avatars.com/api/?background=1f108e&color=fff&name='.urlencode($eleve->nom.' '.$eleve->prenom) }}" alt="Photo">
            <div>
                <h3 class="font-headline-md text-headline-md text-primary">{{ $eleve->nom }} {{ $eleve->prenom }}</h3>
                <p class="text-sm text-text-muted">Matricule : {{ $eleve->matricule }}</p>
            </div>
        </div>
        <div class="p-6 space-y-3 text-sm">
            <div class="flex justify-between"><span class="text-text-muted">Sexe</span><span class="font-medium">{{ $eleve->sexe }}</span></div>
            <div class="flex justify-between"><span class="text-text-muted">Date de naissance</span><span class="font-medium">{{ $eleve->date_naissance?->format('d/m/Y') ?? '—' }}</span></div>
            <div class="flex justify-between"><span class="text-text-muted">Lieu de naissance</span><span class="font-medium">{{ $eleve->lieu_naissance ?? '—' }}</span></div>
            <div class="flex justify-between"><span class="text-text-muted">Nationalité</span><span class="font-medium">{{ $eleve->nationalite ?? '—' }}</span></div>
            <div class="flex justify-between"><span class="text-text-muted">Statut</span><span class="font-medium">{{ ucfirst($eleve->statut) }}</span></div>
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant p-6">
        <h4 class="font-headline-md text-headline-md mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">school</span> Parcours scolaire
        </h4>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between"><span class="text-text-muted">Établissement</span><span class="font-medium">{{ $eleve->etablissement?->nom ?? '—' }}</span></div>
            <div class="flex justify-between"><span class="text-text-muted">Classe</span><span class="font-medium">{{ $eleve->classe?->nom ?? '—' }}</span></div>
            <div class="flex justify-between"><span class="text-text-muted">Niveau</span><span class="font-medium">{{ $eleve->niveau?->nom ?? $eleve->classe?->niveau?->nom ?? '—' }}</span></div>
            <div class="flex justify-between"><span class="text-text-muted">Série / Filière</span><span class="font-medium">{{ $eleve->serie?->nom_serie ?? '—' }}</span></div>
            <div class="flex justify-between"><span class="text-text-muted">Année académique</span><span class="font-medium">{{ now()->year }}-{{ now()->addYear()->year }}</span></div>
        </div>
    </div>
</div>

<!-- Accès rapides -->
<div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant p-6">
    <h4 class="font-headline-md text-headline-md mb-4 flex items-center gap-2">
        <span class="material-symbols-outlined text-primary">quick_links</span> Accès rapides
    </h4>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <a class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-lg bg-primary text-on-primary text-sm font-medium hover:opacity-90" href="{{ route('parent.enfant.notes', $eleve) }}">
            <span class="material-symbols-outlined">fact_check</span> Notes
        </a>
        <a class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-lg bg-surface-container text-on-surface text-sm font-medium hover:bg-surface-container-high" href="{{ route('parent.enfant.bulletins', $eleve) }}">
            <span class="material-symbols-outlined">description</span> Bulletins
        </a>
        <a class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-lg bg-surface-container text-on-surface text-sm font-medium hover:bg-surface-container-high" href="{{ route('parent.enfant.emploi-temps', $eleve) }}">
            <span class="material-symbols-outlined">calendar_month</span> Emploi du temps
        </a>
        <a class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-lg bg-surface-container text-on-surface text-sm font-medium hover:bg-surface-container-high" href="{{ route('parent.enfant.scolarite', $eleve) }}">
            <span class="material-symbols-outlined">payments</span> Scolarité
        </a>
    </div>
</div>
@endsection
