@extends('parent.layouts.app')

@section('title', 'Mes enfants')

@section('content')
<div class="mb-6">
    <h2 class="font-headline-lg text-headline-lg text-primary">Mes enfants</h2>
    <p class="text-sm text-on-surface-variant">Liste des enfants qui vous sont affiliés</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($eleves as $eleve)
    <div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant overflow-hidden hover:shadow-lg transition-shadow">
        <div class="bg-gradient-to-br from-primary/10 to-primary-fixed/20 p-6 flex items-center gap-4">
            <img class="w-16 h-16 rounded-full object-cover border-2 border-white shadow" src="{{ $eleve->photo_url ?: 'https://ui-avatars.com/api/?background=1f108e&color=fff&name='.urlencode($eleve->nom.' '.$eleve->prenom) }}" alt="Photo">
            <div>
                <h3 class="font-headline-md text-headline-md text-primary">{{ $eleve->nom }} {{ $eleve->prenom }}</h3>
                <p class="text-sm text-text-muted">Matricule : {{ $eleve->matricule }}</p>
            </div>
        </div>
        <div class="p-6 space-y-3">
            <div class="flex items-center justify-between text-sm">
                <span class="text-text-muted">Classe</span>
                <span class="font-medium px-3 py-1 rounded-full bg-secondary-container/20 text-on-secondary-container">{{ $eleve->classe?->nom ?? 'Non assignée' }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-text-muted">Niveau</span>
                <span class="font-medium">{{ $eleve->niveau?->nom ?? $eleve->classe?->niveau?->nom ?? '—' }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-text-muted">Série / Filière</span>
                <span class="font-medium">{{ $eleve->serie?->nom_serie ?? '—' }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-text-muted">Établissement</span>
                <span class="font-medium">{{ $eleve->etablissement?->nom ?? '—' }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-text-muted">Année académique</span>
                <span class="font-medium">{{ now()->year }}-{{ now()->addYear()->year }}</span>
            </div>

            <div class="pt-4 border-t border-outline-variant flex flex-wrap gap-2">
                <a class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-primary text-on-primary text-sm hover:opacity-90" href="{{ route('parent.enfant.scolarite', $eleve) }}">
                    <span class="material-symbols-outlined text-base">school</span> Scolarité
                </a>
                <a class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-surface-container text-on-surface text-sm hover:bg-surface-container-high" href="{{ route('parent.enfant.notes', $eleve) }}">
                    <span class="material-symbols-outlined text-base">fact_check</span> Notes
                </a>
                <a class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-surface-container text-on-surface text-sm hover:bg-surface-container-high" href="{{ route('parent.enfant.bulletins', $eleve) }}">
                    <span class="material-symbols-outlined text-base">description</span> Bulletins
                </a>
                <a class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-surface-container text-on-surface text-sm hover:bg-surface-container-high" href="{{ route('parent.enfant.emploi-temps', $eleve) }}">
                    <span class="material-symbols-outlined text-base">calendar_month</span> Emploi du temps
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full bg-surface-container-lowest rounded-xl p-12 text-center">
        <div class="w-20 h-20 bg-surface-container rounded-full flex items-center justify-center mx-auto mb-6">
            <span class="material-symbols-outlined text-4xl text-outline">person_off</span>
        </div>
        <h3 class="font-headline-md text-headline-md text-on-surface mb-2">Aucun enfant affilié</h3>
        <p class="text-on-surface-variant">Contactez l'administration pour associer un enfant à votre compte.</p>
    </div>
    @endforelse
</div>
@endsection
