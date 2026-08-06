@extends('parent.layouts.app')

@section('title', 'Bulletins de '.$eleve->nom.' '.$eleve->prenom)

@section('content')
<div class="mb-6 flex items-center justify-between gap-3 flex-wrap">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-primary">Bulletins</h2>
        <p class="text-sm text-on-surface-variant">Bulletins de {{ $eleve->nom }} {{ $eleve->prenom }} ({{ $eleve->classe?->nom ?? '—' }})</p>
    </div>
    <a class="inline-flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-primary rounded-lg" href="{{ route('parent.enfants') }}">
        <span class="material-symbols-outlined text-lg">arrow_back</span> Retour
    </a>
</div>

<div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant overflow-hidden">
    <div class="p-6 border-b border-outline-variant">
        <h4 class="font-headline-md text-headline-md flex items-center gap-2">Liste des bulletins</h4>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-surface-container-low border-b border-outline-variant">
                <tr>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Enfant</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Classe</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Année académique</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Période</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Date</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px] text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bulletins as $bulletin)
                <tr class="border-b border-outline-variant/50 hover:bg-surface-container-low transition-colors">
                    <td class="px-6 py-4 font-body-md font-medium">{{ $eleve->nom }} {{ $eleve->prenom }}</td>
                    <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $bulletin->classe?->nom ?? '—' }}</td>
                    <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $bulletin->anneeAcademique?->libelle ?? '—' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-label-sm bg-primary-fixed text-primary">{{ strtoupper($bulletin->trimestre ?? '—') }}</span>
                    </td>
                    <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $bulletin->date?->format('d/m/Y') ?? '—' }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a class="p-2 text-primary hover:bg-primary-fixed rounded-lg transition-colors" href="{{ route('parent.enfant.bulletins.show', [$eleve, $bulletin]) }}" title="Voir">
                                <span class="material-symbols-outlined">visibility</span>
                            </a>
                            <a class="p-2 text-secondary hover:bg-secondary-container/20 rounded-lg transition-colors" href="{{ route('parent.enfant.bulletins.print', [$eleve, $bulletin]) }}" target="_blank" title="Imprimer">
                                <span class="material-symbols-outlined">print</span>
                            </a>
                            <a class="p-2 text-warning-amber hover:bg-warning-amber/10 rounded-lg transition-colors" href="{{ route('parent.enfant.bulletins.download-pdf', [$eleve, $bulletin]) }}" title="Télécharger">
                                <span class="material-symbols-outlined">download</span>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-10 px-6 text-center">
                        <div class="flex flex-col items-center max-w-xs mx-auto">
                            <div class="w-20 h-20 bg-surface-container rounded-full flex items-center justify-center mb-6">
                                <span class="material-symbols-outlined text-5xl text-outline-variant">description</span>
                            </div>
                            <h5 class="font-headline-md text-headline-md text-on-surface mb-2">Aucun bulletin disponible</h5>
                            <p class="text-sm text-text-muted text-center">Les bulletins de votre enfant apparaîtront ici dès leur émission.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
