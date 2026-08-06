@extends('personnel.layouts.app')

@section('title', 'Emplois du temps')

@section('content')
<div class="mb-6">
    <h2 class="font-headline-lg text-headline-lg text-primary">Emplois du temps</h2>
    <p class="text-sm text-on-surface-variant">Gérez les emplois du temps des classes</p>
</div>

<!-- Filtres -->
<div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant p-4 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="space-y-1">
            <label class="font-label-md text-label-md text-on-surface-variant">Classe</label>
            <select name="classe_id" class="w-full bg-white border border-outline-variant rounded-lg px-4 py-2.5 text-body-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none">
                <option value="">Toutes les classes</option>
                @foreach($classes as $classe)
                <option value="{{ $classe->id }}" {{ $selectedClass == $classe->id ? 'selected' : '' }}>{{ $classe->nom }} ({{ $classe->niveau?->nom ?? '—' }})</option>
                @endforeach
            </select>
        </div>
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

<!-- Liste des classes avec emploi du temps -->
<div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant overflow-hidden">
    <div class="p-6 border-b border-outline-variant flex justify-between items-center">
        <h4 class="font-headline-md text-headline-md">Emplois du temps des classes</h4>
        <a href="{{ route('personnel.emploi-temps.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-on-primary rounded-lg font-label-md hover:bg-primary/90 transition-colors">
            <span class="material-symbols-outlined text-lg">add</span> Nouvel emploi du temps
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-surface-container-low border-b border-outline-variant">
                <tr>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Classe</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Niveau</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Statut</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px] text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($classes as $classe)
                <tr class="border-b border-outline-variant/50 hover:bg-surface-container-low transition-colors">
                    <td class="px-6 py-4 font-body-md font-medium">{{ $classe->nom }}</td>
                    <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $classe->niveau?->nom ?? '—' }}</td>
                    <td class="px-6 py-4">
                        @php($hasSchedule = in_array($classe->id, $classesWithSchedule))
                        @if($hasSchedule)
                            <span class="px-3 py-1 rounded-full text-label-sm bg-success-green/10 text-success-green">Emploi du temps créé</span>
                        @else
                            <span class="px-3 py-1 rounded-full text-label-sm bg-surface-container text-on-surface-variant">Non créé</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            @if($hasSchedule)
                            <a class="p-2 text-primary hover:bg-primary-fixed rounded-lg transition-colors" href="{{ route('personnel.emploi-temps.show', ['classe_id' => $classe->id, 'annee_academique_id' => $selectedYear]) }}" title="Voir">
                                <span class="material-symbols-outlined">visibility</span>
                            </a>
                            <a class="p-2 text-warning-amber hover:bg-warning-amber/10 rounded-lg transition-colors" href="{{ route('personnel.emploi-temps.edit', ['classe_id' => $classe->id, 'annee_academique_id' => $selectedYear]) }}" title="Modifier">
                                <span class="material-symbols-outlined">edit</span>
                            </a>
                            @else
                            <a class="p-2 text-secondary hover:bg-secondary-container/20 rounded-lg transition-colors" href="{{ route('personnel.emploi-temps.edit', ['classe_id' => $classe->id, 'annee_academique_id' => $selectedYear]) }}" title="Créer">
                                <span class="material-symbols-outlined">add_circle</span>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-10 px-6 text-center text-on-surface-variant">Aucune classe disponible.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
