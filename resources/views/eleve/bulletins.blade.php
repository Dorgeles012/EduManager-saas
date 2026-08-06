@extends('eleve.layouts.app')

@section('title', 'Mes bulletins')

@section('content')
<div class="mb-6">
    <h2 class="font-headline-lg text-headline-lg text-primary">Mes bulletins</h2>
    <p class="text-sm text-on-surface-variant">Consultation de mes bulletins (lecture seule)</p>
</div>

<!-- Filtre année -->
<div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant p-4 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4" action="{{ route('eleve.bulletins') }}">
        <div class="space-y-1">
            <label class="font-label-md text-label-md text-on-surface-variant">Année académique</label>
            <select name="annee_academique_id" class="w-full bg-white border border-outline-variant rounded-lg px-4 py-2.5 text-body-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none">
                <option value="">Toutes les années</option>
                @foreach($years as $year)
                <option value="{{ $year->id }}" {{ $selectedYear == $year->id ? 'selected' : '' }}>{{ $year->libelle }}</option>
                @endforeach
            </select>
        </div>
        <div class="space-y-1 flex items-end">
            <button type="submit" class="w-full bg-primary text-on-primary px-4 py-2.5 rounded-lg font-label-md hover:bg-primary/90 transition-colors">Filtrer</button>
        </div>
    </form>
</div>

<div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant overflow-hidden">
    <div class="p-6 border-b border-outline-variant">
        <h4 class="font-headline-md text-headline-md flex items-center gap-2">Liste de mes bulletins</h4>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-surface-container-low border-b border-outline-variant">
                <tr>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Classe</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Année académique</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Période</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Moyenne</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Date</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px] text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bulletins as $bulletin)
                <tr class="border-b border-outline-variant/50 hover:bg-surface-container-low transition-colors">
                    <td class="px-6 py-4 font-body-md font-medium">{{ $bulletin->classe?->nom ?? '—' }}</td>
                    <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $bulletin->anneeAcademique?->libelle ?? '—' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-label-sm bg-primary-fixed text-primary">{{ strtoupper($bulletin->trimestre ?? '—') }}</span>
                    </td>
                    <td class="px-6 py-4 text-body-sm font-bold">{{ $bulletin->moyenne_generale !== null ? number_format((float) $bulletin->moyenne_generale, 2, ',', ' ') : '—' }}</td>
                    <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $bulletin->date?->format('d/m/Y') ?? '—' }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a class="p-2 text-primary hover:bg-primary-fixed rounded-lg transition-colors" href="{{ route('eleve.bulletins.show', $bulletin) }}" title="Voir">
                                <span class="material-symbols-outlined">visibility</span>
                            </a>
                            <a class="p-2 text-secondary hover:bg-secondary-container/20 rounded-lg transition-colors" href="{{ route('eleve.bulletins.print', $bulletin) }}" target="_blank" title="Imprimer">
                                <span class="material-symbols-outlined">print</span>
                            </a>
                            <a class="p-2 text-warning-amber hover:bg-warning-amber/10 rounded-lg transition-colors" href="{{ route('eleve.bulletins.download-pdf', $bulletin) }}" title="Télécharger">
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
                            <p class="text-sm text-text-muted text-center">Vos bulletins apparaîtront ici dès leur émission.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
