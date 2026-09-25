@extends('client.layouts.app')

@section('title', 'Emploi du temps — ' . $enseignant->nom . ' ' . $enseignant->prenoms)

@section('content')

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
    <div class="flex items-center gap-3">
        <a href="{{ route('client.enseignant') }}" class="w-9 h-9 rounded-xl bg-white border border-gray-100 hover:bg-gray-100 flex items-center justify-center transition flex-shrink-0">
            <span class="material-symbols-outlined text-gray-600 text-base">arrow_back</span>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Emploi du temps</h2>
            <p class="text-sm text-gray-500 mt-0.5">
                <strong class="text-gray-900">{{ $enseignant->nom }} {{ $enseignant->prenoms }}</strong>
                @if($enseignant->matieres->count())
                    · {{ $enseignant->matieres->pluck('nom')->join(', ') }}
                @endif
            </p>
        </div>
    </div>

    <div class="flex items-center gap-2 flex-wrap">
        <a href="{{ route('client.emploi-temps.teacher.pdf', $enseignant) }}"
           class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-xs font-semibold transition shadow-sm">
            <span class="material-symbols-outlined text-sm">download</span>
            Télécharger
        </a>
        <a href="{{ route('client.emploi-temps.teacher.print', $enseignant) }}" target="_blank"
           class="inline-flex items-center gap-1.5 bg-white border border-gray-200 hover:bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-xs font-semibold transition">
            <span class="material-symbols-outlined text-sm">print</span>
            Imprimer
        </a>
    </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                <span class="material-symbols-outlined text-lg">person</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Matricule</span>
        </div>
        <h3 class="text-lg font-bold text-gray-900 truncate">{{ $enseignant->matricule ?? '—' }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Identifiant</p>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                <span class="material-symbols-outlined text-lg">schedule</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Séances</span>
        </div>
        <h3 class="text-2xl font-bold text-gray-900">{{ $entries->count() ?? 0 }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Cette semaine</p>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                <span class="material-symbols-outlined text-lg">menu_book</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Matières</span>
        </div>
        <h3 class="text-2xl font-bold text-gray-900">{{ $enseignant->matieres->count() }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Enseignées</p>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-violet-50 flex items-center justify-center text-violet-600">
                <span class="material-symbols-outlined text-lg">event_note</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Jours</span>
        </div>
        <h3 class="text-2xl font-bold text-gray-900">{{ count($days) }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Ouvrés</p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
            <span class="material-symbols-outlined text-base">calendar_view_week</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-900">Planning hebdomadaire</h3>
            <p class="text-[11px] text-gray-500">Consultation en lecture seule</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[1200px] border-collapse text-xs">
            <thead>
                <tr class="bg-gradient-to-r from-indigo-600 to-indigo-500">
                    <th class="sticky left-0 z-20 bg-gradient-to-r from-indigo-700 to-indigo-600 text-white text-left px-4 py-3 text-[10px] font-bold uppercase tracking-wider w-[140px] min-w-[140px] max-w-[140px]">
                        Horaires
                    </th>
                    @foreach($days as $day)
                        <th class="text-white text-center px-4 py-3 text-[10px] font-bold uppercase tracking-wider min-w-[180px]">
                            {{ ucfirst($day) }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($slots as $slot)
                    @if(isset($slot['break']))
                        <tr>
                            <td colspan="{{ count($days) + 1 }}" class="bg-gradient-to-r from-indigo-50 to-indigo-100 text-indigo-700 text-center py-2.5 text-[10px] font-bold uppercase tracking-widest border-y border-indigo-200">
                                {{ $slot['break'] }}
                            </td>
                        </tr>
                    @else
                        @php
                            $slotKey = $slot['key'] ?? $slot[0] . '-' . $slot[1];
                            $startTime = $slot['start'] ?? $slot[0];
                            $endTime = $slot['end'] ?? $slot[1];
                        @endphp
                        <tr class="border-t border-gray-100 hover:bg-gray-50/30 transition-colors">
                            <th class="sticky left-0 z-10 bg-gray-50/95 backdrop-blur-sm text-indigo-700 font-bold text-xs text-center py-3 px-3 border-r border-gray-100">
                                {{ str_replace(':', 'h', $startTime) }} - {{ str_replace(':', 'h', $endTime) }}
                            </th>

                            @foreach($days as $day)
                                @php
                                    $entry = $grid[$day][$slotKey] ?? null;
                                    $hasData = $entry && ($entry->classe_id || $entry->matiere_id);
                                @endphp
                                <td class="align-top p-2 min-h-[110px]">
                                    @if($hasData)
                                        <div class="bg-indigo-50/50 border border-indigo-100 rounded-lg p-3 h-full space-y-1.5 border-l-[3px] border-l-indigo-500">
                                            <div class="flex items-start justify-between gap-2">
                                                <span class="text-xs font-bold text-gray-900 leading-tight">{{ $entry->matiere->nom ?? '—' }}</span>
                                            </div>
                                            <p class="text-[11px] text-gray-600 font-medium">{{ $entry->classe->nom ?? '—' }}</p>
                                            @if($entry->serie)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-100 text-[10px] font-bold text-gray-700 uppercase tracking-wider">
                                                    {{ $entry->serie->nom_serie }}
                                                </span>
                                            @endif
                                            @if($entry->salle)
                                                <p class="text-[10px] text-indigo-600 font-semibold flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-[12px]">place</span>
                                                    {{ $entry->salle }}
                                                </p>
                                            @endif
                                        </div>
                                    @else
                                        <div class="h-full min-h-[100px] flex items-center justify-center">
                                            <span class="w-2 h-2 rounded-full bg-gray-200"></span>
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection