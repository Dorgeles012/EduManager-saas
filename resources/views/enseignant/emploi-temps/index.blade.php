@extends('enseignant.layouts.app')

@section('title', 'Emploi du temps')

@section('content')
<div class="mb-6 flex items-center justify-between gap-3 flex-wrap">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-primary">Mon Emploi du temps</h2>
        <p class="text-sm text-on-surface-variant">Consultation en lecture seule</p>
    </div>
    @if($enseignant)
    <div class="flex gap-4 flex-wrap">
        <a class="inline-flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-lg" href="{{ route('enseignant.emploi-temps.pdf') }}" title="Télécharger PDF">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="12" y1="18" x2="12" y2="12"/>
                <line x1="8" y1="15" x2="15" y2="15"/>
            </svg>
            <span>Télécharger</span>
        </a>
        <a class="inline-flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg" target="_blank" href="{{ route('enseignant.emploi-temps.print') }}" title="Imprimer">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 6 2 18 2 18 9"/>
                <path d="M18 9H6"/>
                <path d="M18 9l3 3v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-6l3-3"/>
                <line x1="9" y1="14" x2="15" y2="14"/>
            </svg>
            <span>Imprimer</span>
        </a>
    </div>
    @endif
</div>

@if($enseignant)
    <!-- Grille de l'emploi du temps en lecture seule -->
    <div class="schedule-wrapper">
        <div class="schedule-scroll-container">
            <table class="schedule-table">
                <thead>
                    <tr>
                        <th class="col-horaire">Horaires</th>
                        @foreach($days as $day)
                            <th class="col-day">{{ ucfirst($day) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($slots as $slot)
                        @if(isset($slot['break']))
                            <!-- Pause -->
                            <tr class="break-row">
                                <th colspan="{{ count($days) + 1 }}">{{ $slot['break'] }}</th>
                            </tr>
                        @else
                            @php
                                $slotKey = $slot['key'] ?? $slot[0] . '-' . $slot[1];
                                $startTime = $slot['start'] ?? $slot[0];
                                $endTime = $slot['end'] ?? $slot[1];
                            @endphp
                            <tr>
                                <th class="schedule-time-display">
                                    {{ $startTime }} - {{ $endTime }}
                                </th>

                                @foreach($days as $day)
                                    @php
                                        $entry = $grid[$day][$slotKey] ?? null;
                                        $hasData = $entry && ($entry->classe_id || $entry->matiere_id);
                                    @endphp
                                    <td class="{{ $hasData ? 'has-data' : 'empty-cell' }}" data-day="{{ $day }}" data-slot="{{ $slotKey }}">
                                        @if($hasData)
                                            <div class="cell-content-readonly">
                                                <div class="cell-info-readonly">
                                                    <span class="cell-class-display-readonly">{{ $entry->classe->nom ?? '—' }}</span>
                                                    <span class="cell-subject-display-readonly">{{ $entry->matiere->nom ?? '—' }}</span>
                                                    @if($entry->serie)
                                                        <span class="cell-serie-display-readonly">{{ $entry->serie->nom_serie }}</span>
                                                    @endif
                                                    @if($entry->salle)
                                                        <span class="cell-salle-display-readonly">Salle: {{ $entry->salle }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <div class="empty-cell-readonly">
                                                <span class="empty-dot-readonly"></span>
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="{{ count($days) + 1 }}" class="text-center py-12">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-surface-container-low rounded-full flex items-center justify-center mb-3 text-outline">
                                        <span class="material-symbols-outlined text-4xl">calendar_month</span>
                                    </div>
                                    <h3 class="font-headline-md text-on-surface mb-1">Aucun emploi du temps</h3>
                                    <p class="text-on-surface-variant font-body-md">Aucun cours n'a été programmé pour vous.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($totalSeances > 0)
    <div class="mt-4 text-sm text-on-surface-variant">
        <span class="font-medium">Total :</span> {{ $totalSeances }} séance(s) programmée(s)
        @if($school)
            · Établissement : {{ $school->nom }}
        @endif
        @if($year)
            · Année : {{ $year->libelle }}
        @endif
    </div>
    @endif
@else
    <div class="bg-surface-container-lowest rounded-xl p-12 text-center">
        <div class="w-20 h-20 bg-surface-container rounded-full flex items-center justify-center mx-auto mb-6">
            <span class="material-symbols-outlined text-4xl text-outline">person_off</span>
        </div>
        <h3 class="font-headline-md text-on-surface mb-2">{{ $message ?? 'Aucun profil enseignant trouvé.' }}</h3>
        <p class="text-on-surface-variant">Veuillez contacter l'administration pour associer votre compte à un profil enseignant.</p>
    </div>
@endif
@endsection

@push('styles')
<style>
    /* Container principal du tableau */
    .schedule-wrapper {
        background: white;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        overflow: hidden;
        position: relative;
    }

    .schedule-scroll-container {
        overflow-x: auto;
        overflow-y: visible;
        padding: 0;
        -webkit-overflow-scrolling: touch;
    }

    .schedule-scroll-container::-webkit-scrollbar {
        height: 10px;
        width: 10px;
    }

    .schedule-scroll-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 5px;
    }

    .schedule-scroll-container::-webkit-scrollbar-thumb {
        background: #c1c7cd;
        border-radius: 5px;
    }

    .schedule-scroll-container::-webkit-scrollbar-thumb:hover {
        background: #1a3a6b;
    }

    .schedule-scroll-container::-webkit-scrollbar-corner {
        background: #f1f1f1;
    }

    .schedule-scroll-container {
        scrollbar-width: thin;
        scrollbar-color: #c1c7cd #f1f1f1;
    }

    .schedule-table {
        width: 100%;
        min-width: 1200px;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.875rem;
        table-layout: fixed;
    }

    .schedule-table thead th {
        background: linear-gradient(135deg, #1a3a6b 0%, #1e4d8a 100%);
        color: white;
        text-align: center;
        padding: 16px 12px;
        font-weight: 600;
        font-size: 0.85rem;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        border: none;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .schedule-table thead th.col-horaire {
        min-width: 160px;
        width: 160px;
        max-width: 160px;
        position: sticky;
        left: 0;
        z-index: 20;
    }

    .schedule-table thead th.col-day {
        min-width: 200px;
        width: 200px;
        max-width: 200px;
    }

    .schedule-table td,
    .schedule-table th {
        border: 1px solid #e5e7eb;
        padding: 10px 8px;
        vertical-align: top;
    }

    .schedule-table tbody>tr>th {
        background: #f8fafc;
        width: 160px;
        min-width: 160px;
        max-width: 160px;
        text-align: center;
        border-right: 2px solid #e5e7eb;
        padding: 12px 8px;
        vertical-align: middle;
        position: sticky;
        left: 0;
        z-index: 5;
        font-weight: 600;
        color: #1f2937;
        font-size: 13px;
    }

    .schedule-time-display {
        background: #f8fafc;
        font-weight: 600;
        color: #1f2937;
        font-size: 13px;
    }

    .schedule-table tbody>tr>td {
        min-width: 200px;
        width: 200px;
        max-width: 200px;
        background: white;
        padding: 8px 6px;
        min-height: 120px;
        height: auto;
        position: relative;
        cursor: default;
    }

    .schedule-table td:hover {
        background: #f8fafc;
    }

    .schedule-table td.empty-cell {
        display: table-cell;
        vertical-align: middle;
        text-align: center;
        min-height: 100px;
    }

    .empty-cell-readonly {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        min-height: 80px;
    }

    .empty-dot-readonly {
        display: inline-block;
        width: 8px;
        height: 8px;
        background: #d1d5db;
        border-radius: 50%;
        opacity: 0.5;
    }

    .schedule-table td.has-data {
        background: #fafcff;
        border-left: 3px solid #1a3a6b;
    }

    .cell-content-readonly {
        display: flex;
        flex-direction: column;
        gap: 4px;
        width: 100%;
    }

    .cell-info-readonly {
        display: flex;
        flex-direction: column;
        gap: 2px;
        padding: 4px;
    }

    .cell-class-display-readonly {
        font-weight: 600;
        font-size: 13px;
        color: #1f2937;
    }

    .cell-subject-display-readonly {
        font-size: 12px;
        color: #4b5563;
    }

    .cell-serie-display-readonly {
        font-size: 10px;
        color: #6b7280;
        background: #f3f4f6;
        padding: 2px 8px;
        border-radius: 4px;
        display: inline-block;
        align-self: flex-start;
    }

    .cell-salle-display-readonly {
        font-size: 10px;
        color: #1f108e;
        background: #e2dfff;
        padding: 2px 8px;
        border-radius: 4px;
        display: inline-block;
        align-self: flex-start;
    }

    .break-row th {
        background: linear-gradient(90deg, #f3f4f6 0%, #e5e7eb 100%) !important;
        color: #4b5563;
        padding: 12px;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        text-align: center;
        font-weight: 700;
        font-size: 0.75rem;
        border: 1px solid #d1d5db;
        border-left: none;
        border-right: none;
    }

    .break-row th:first-child {
        border-left: 1px solid #d1d5db;
    }

    .break-row th:last-child {
        border-right: 1px solid #d1d5db;
    }

    @media (max-width: 768px) {
        .schedule-table tbody>tr>th {
            width: 120px;
            min-width: 120px;
            max-width: 120px;
            padding: 8px 4px;
            font-size: 11px;
        }

        .schedule-table tbody>tr>td {
            min-width: 160px;
            width: 160px;
            max-width: 160px;
            padding: 6px 4px;
        }

        .schedule-table thead th.col-horaire {
            min-width: 120px;
            width: 120px;
            max-width: 120px;
        }

        .schedule-table thead th.col-day {
            min-width: 160px;
            width: 160px;
            max-width: 160px;
            font-size: 0.7rem;
            padding: 10px 6px;
        }

        .schedule-table {
            min-width: 1000px;
        }

        .schedule-scroll-container::-webkit-scrollbar {
            height: 8px;
            width: 8px;
        }

        .cell-class-display-readonly {
            font-size: 11px;
        }

        .cell-subject-display-readonly {
            font-size: 10px;
        }
    }

    .schedule-table tbody tr:not(.break-row):hover td {
        background: #fafcff;
    }

    .schedule-table tbody tr:not(.break-row):hover th {
        background: #f1f5f9;
    }
</style>
@endpush

