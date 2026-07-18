<div class="timetable-card bg-white rounded-xl border custom-shadow p-6 overflow-x-auto">
    <div class="text-center border-b pb-4 mb-5">
        <h3 class="font-bold text-xl uppercase">Emploi du temps du professeur</h3>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm mb-5">
        <p><b>Nom :</b> {{ $enseignant->nom }}</p>
        <p><b>Prénoms :</b> {{ $enseignant->prenoms }}</p>
        <p><b>Matricule :</b> {{ $enseignant->matricule ?? '—' }}</p>
        <p><b>Discipline(s) :</b> {{ $enseignant->matieres->pluck('nom')->join(', ') }}</p>
        <p><b>Classe(s) :</b> {{ $enseignant->classes->pluck('nom')->join(', ') }}</p>
        <p><b>Série(s) :</b> {{ $enseignant->series->pluck('nom_serie')->map(fn ($serie) => preg_replace('/^Série\s*/iu', '', $serie))->join(', ') }}</p>
    </div>

    <table class="schedule-table w-full min-w-[960px] border-collapse">
        <thead><tr><th>Horaires</th>@foreach($days as $day)<th>{{ ucfirst($day) }}</th>@endforeach</tr></thead>
        <tbody>
            @foreach($slots as $slot)
                @if(isset($slot['break']))
                    <tr class="break-row"><th colspan="6">{{ $slot['break'] }}</th></tr>
                @else
                    <tr>
                        <th>{{ str_replace(':', 'h', $slot['start']) }} - {{ str_replace(':', 'h', $slot['end']) }}</th>
                        @foreach($days as $day)
                            @php($entry = $grid[$day][$slot['key']] ?? null)
                            <td class="{{ $entry ? $slot['color'] : 'cell-empty' }}">
                                @if($entry)
                                    <strong>{{ $entry->matiere?->nom }}</strong><br>
                                    <span>{{ $entry->classe?->nom }}</span>
                                    @if($entry->serie)
                                        <br><small>{{ preg_replace('/^Série\s*/iu', '', $entry->serie->nom_serie) }}</small>
                                    @endif
                                @else
                                    <span class="empty-slot">Rien programmé</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>

@push('styles')
<style>
    .schedule-table th,.schedule-table td{border:1px solid #dbe2ea;padding:9px;vertical-align:top}
    .schedule-table thead th{background:#173b78;color:#fff;text-align:center;padding:13px}
    .schedule-table tbody>tr>th{background:#f4f7fb;width:135px;text-align:center}
    .schedule-table td{width:18%;height:78px;transition:background-color .2s ease}
    .break-row th{background:#d1d5db!important;color:#4b5563;padding:9px;letter-spacing:.08em;text-transform:uppercase}
    .cell-empty{background:#f8fafc;text-align:center;vertical-align:middle!important}
    .empty-slot{color:#94a3b8;font-style:italic;font-size:.8rem}
    .slot-color-1{background:#dbeafe;color:#1e3a8a}.slot-color-2{background:#dcfce7;color:#166534}.slot-color-3{background:#ffedd5;color:#9a3412}.slot-color-4{background:#ede9fe;color:#5b21b6}.slot-color-5{background:#fee2e2;color:#991b1b}.slot-color-6{background:#cffafe;color:#155e75}.slot-color-7{background:#fef9c3;color:#854d0e}.slot-color-8{background:#e5e7eb;color:#374151}
    .custom-shadow{box-shadow:0 4px 12px rgba(55,48,163,.04)}
    @media (max-width:768px){.timetable-card{padding:1rem}.schedule-table td{min-height:72px}}
</style>
@endpush
