<div class="bg-white rounded-xl border custom-shadow p-6 overflow-x-auto">
    <div class="text-center border-b pb-4 mb-5"><h3 class="font-bold text-xl uppercase">Emploi du temps du professeur</h3></div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm mb-5">
        <p><b>Nom :</b> {{ $enseignant->nom }}</p><p><b>Prénoms :</b> {{ $enseignant->prenoms }}</p><p><b>Matricule :</b> {{ $enseignant->matricule ?? '—' }}</p><p><b>Discipline(s) :</b> {{ $enseignant->matieres->pluck('nom')->join(', ') }}</p><p><b>Classe(s) :</b> {{ $enseignant->classes->pluck('nom')->join(', ') }}</p><p><b>Série(s) :</b> {{ $enseignant->series->pluck('nom_serie')->join(', ') }}</p>
    </div>
    <table class="schedule-table w-full min-w-[960px] border-collapse"><thead><tr><th>Horaires</th>@foreach($days as $day)<th>{{ ucfirst($day) }}</th>@endforeach</tr></thead><tbody>@foreach($slots as $slot)
        @if(isset($slot['break']))<tr class="break-row"><th colspan="6">{{ $slot['break'] }}</th></tr>
        @else @php($slotKey = $slot[0].'-'.$slot[1])<tr><th>{{ str_replace(':','h',$slot[0]) }} - {{ str_replace(':','h',$slot[1]) }}</th>@foreach($days as $day)@php($entry = $grid[$day][$slotKey] ?? null)<td>@if($entry)<strong>{{ $entry->matiere?->nom }}</strong><br><span>{{ $entry->classe?->nom }}</span>@if($entry->serie)<br><small>Série : {{ $entry->serie->nom_serie }}</small>@endif@if($entry->salle)<br><small>Salle : {{ $entry->salle }}</small>@endif@endif</td>@endforeach</tr>
        @endif
    @endforeach</tbody></table>
</div>
@push('styles')<style>.schedule-table th,.schedule-table td{border:1px solid #dbe2ea;padding:9px;vertical-align:top}.schedule-table thead th{background:#173b78;color:#fff;text-align:center;padding:13px}.schedule-table tbody>tr>th{background:#f4f7fb;width:135px;text-align:center}.schedule-table td{width:18%;height:78px}.break-row th{background:#d1d5db!important;color:#4b5563;padding:9px;letter-spacing:.08em;text-transform:uppercase}.custom-shadow{box-shadow:0 4px 12px rgba(55,48,163,.04)}</style>@endpush
