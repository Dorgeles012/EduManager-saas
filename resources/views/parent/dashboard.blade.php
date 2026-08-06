@extends('parent.layouts.app')

@section('title', 'Tableau de bord Parent')

@section('content')
<div class="mb-6">
    <h2 class="font-headline-lg text-headline-lg text-primary">Tableau de bord</h2>
    <p class="text-sm text-on-surface-variant">Bienvenue dans votre espace parent. Consultez le suivi scolaire de vos enfants.</p>
</div>

<!-- Statistiques -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-surface-container-lowest p-6 rounded-xl custom-shadow border border-outline-variant flex items-center gap-4">
        <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center text-primary">
            <span class="material-symbols-outlined text-2xl">group</span>
        </div>
        <div>
            <p class="text-label-sm text-text-muted uppercase tracking-wider font-bold">Mes enfants</p>
            <h3 class="font-headline-md text-headline-md">{{ $totalEnfants }}</h3>
        </div>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-xl custom-shadow border border-outline-variant flex items-center gap-4">
        <div class="w-12 h-12 bg-secondary/10 rounded-full flex items-center justify-center text-secondary">
            <span class="material-symbols-outlined text-2xl">fact_check</span>
        </div>
        <div>
            <p class="text-label-sm text-text-muted uppercase tracking-wider font-bold">Notes</p>
            <h3 class="font-headline-md text-headline-md">{{ $totalNotes }}</h3>
        </div>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-xl custom-shadow border border-outline-variant flex items-center gap-4">
        <div class="w-12 h-12 bg-warning-amber/10 rounded-full flex items-center justify-center text-warning-amber">
            <span class="material-symbols-outlined text-2xl">description</span>
        </div>
        <div>
            <p class="text-label-sm text-text-muted uppercase tracking-wider font-bold">Bulletins</p>
            <h3 class="font-headline-md text-headline-md">{{ $totalBulletins }}</h3>
        </div>
    </div>
</div>

<!-- Liste rapide des enfants -->
<div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant overflow-hidden">
    <div class="p-6 border-b border-outline-variant">
        <h4 class="font-headline-md text-headline-md flex items-center gap-2">Mes enfants</h4>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-surface-container-low border-b border-outline-variant">
                <tr>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Élève</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Matricule</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Classe</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px] text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($eleves as $eleve)
                <tr class="border-b border-outline-variant/50 hover:bg-surface-container-low transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <img class="w-10 h-10 rounded-full object-cover border border-outline-variant" src="{{ $eleve->photo_url ?: 'https://ui-avatars.com/api/?background=1f108e&color=fff&name='.urlencode($eleve->nom.' '.$eleve->prenom) }}" alt="Photo">
                            <div>
                                <span class="font-body-md font-medium">{{ $eleve->nom }} {{ $eleve->prenom }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $eleve->matricule }}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-label-sm bg-secondary-container/20 text-on-secondary-container">{{ $eleve->classe?->nom ?? 'Non assignée' }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a class="inline-flex px-3 py-1.5 rounded-lg bg-primary text-on-primary text-sm font-medium hover:opacity-90" href="{{ route('parent.enfant.scolarite', $eleve) }}">Consulter</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-10 px-6 text-center">
                        <div class="flex flex-col items-center max-w-xs mx-auto">
                            <div class="w-20 h-20 bg-surface-container rounded-full flex items-center justify-center mb-6">
                                <span class="material-symbols-outlined text-5xl text-outline-variant">group</span>
                            </div>
                            <h5 class="font-headline-md text-headline-md text-on-surface mb-2">Aucun enfant affilié</h5>
                            <p class="text-sm text-text-muted text-center">Contactez l'administration pour associer un enfant à votre compte parent.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
