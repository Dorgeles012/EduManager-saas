@extends('personnel.layouts.app')

@section('title', 'Emploi du temps - '.$classe->nom)

@section('content')
<div class="mb-6 flex items-center justify-between gap-3 flex-wrap">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-primary">Emploi du temps</h2>
        <p class="text-sm text-on-surface-variant">
            Classe : <strong>{{ $classe->nom }}</strong>
            @if($year) · {{ $year->libelle }} @endif
            · {{ $school?->nom ?? '' }}
        </p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('personnel.emploi-temps.edit', ['classe_id' => $classe->id, 'annee_academique_id' => $year?->id]) }}" class="inline-flex items-center gap-2 px-3 py-2 bg-primary text-on-primary rounded-lg text-sm hover:bg-primary/90 transition-colors">
            <span class="material-symbols-outlined text-lg">edit</span> Modifier
        </a>
        <a href="{{ route('personnel.emploi-temps.print', ['classe_id' => $classe->id, 'annee_academique_id' => $year?->id]) }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 text-sm text-secondary hover:bg-secondary-container/20 rounded-lg">
            <span class="material-symbols-outlined">print</span> Imprimer
        </a>
        <a href="{{ route('personnel.emploi-temps.pdf', ['classe_id' => $classe->id, 'annee_academique_id' => $year?->id]) }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm text-warning-amber hover:bg-warning-amber/10 rounded-lg">
            <span class="material-symbols-outlined">download</span> Télécharger
        </a>
    </div>
</div>

@php
    $grid = $grid ?? [];
@endphp

<div class="bg-white rounded-xl border border-outline-variant custom-shadow p-6 overflow-x-auto">
    @if($entries->isEmpty())
        <div class="text-center py-12">
            <div class="w-20 h-20 bg-surface-container rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="material-symbols-outlined text-5xl text-outline-variant">calendar_month</span>
            </div>
            <h5 class="font-headline-md text-headline-md text-on-surface mb-2">Aucun emploi du temps</h5>
            <p class="text-sm text-text-muted">Cet emploi du temps n'a pas encore été configuré.</p>
            <a href="{{ route('personnel.emploi-temps.edit', ['classe_id' => $classe->id, 'annee_academique_id' => $year?->id]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-on-primary rounded-lg text-sm mt-4">
                <span class="material-symbols-outlined text-lg">add</span> Créer
            </a>
        </div>
    @else
    <table class="w-full min-w-[960px] border-collapse">
        <thead>
            <tr>
                <th class="bg-primary text-white text-center p-3 font-label-md border border-primary">Horaires</th>
                @foreach($days as $day)
                <th class="bg-primary text-white text-center p-3 font-label-md uppercase border border-primary">{{ $day }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($slots as $slot)
                @if(isset($slot['break']))
                    <tr>
                        <th class="bg-surface-container border border-outline-variant p-3 text-center font-label-md text-on-surface-variant uppercase" colspan="6">{{ $slot['break'] }}</th>
                    </tr>
                @else
                    <tr>
                        <th class="bg-surface-container-low border border-outline-variant p-3 text-center font-label-md whitespace-nowrap">{{ str_replace(':', 'h', $slot['start']) }} - {{ str_replace(':', 'h', $slot['end']) }}</th>
                        @foreach($days as $day)
                            @php($entry = $grid[$day][$slot['key']] ?? null)
                            <td class="border border-outline-variant p-3 text-center align-top h-[78px] {{ $entry ? 'bg-primary-fixed/20' : 'bg-surface-container-lowest' }}">
                                @if($entry)
                                    <span class="block font-bold text-primary">{{ $entry->matiere?->nom ?? '—' }}</span>
                                    <span class="block text-xs text-on-surface-variant">{{ $entry->enseignant?->prenoms }} {{ $entry->enseignant?->nom }}</span>
                                    @if($entry->salle)
                                        <span class="block text-xs text-text-muted">Salle : {{ $entry->salle }}</span>
                                    @endif
                                @else
                                    <span class="text-xs italic text-outline">—</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection
