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


@endsection
