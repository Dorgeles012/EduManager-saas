<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Emploi du temps</title>
    <style>
        @page { size: A4 landscape; margin: 10mm; }
        body{font-family:DejaVu Sans,sans-serif;color:#172033;font-size:10px}h2{text-align:center;margin:0 0 16px}.info{margin-bottom:14px}.info span{display:inline-block;width:32%;margin:3px 0}table{width:100%;border-collapse:collapse}th,td{border:1px solid #aab4c2;padding:7px;vertical-align:top}thead th{background:#173b78;color:#fff;text-align:center}.time{background:#f4f7fb;text-align:center;width:105px}.break{background:#d1d5db;text-align:center;font-weight:bold;text-transform:uppercase;letter-spacing:1px}.cell{height:45px}.empty{color:#94a3b8;text-align:center;font-style:italic;vertical-align:middle}.slot-color-1{background:#dbeafe;color:#1e3a8a}.slot-color-2{background:#dcfce7;color:#166534}.slot-color-3{background:#ffedd5;color:#9a3412}.slot-color-4{background:#ede9fe;color:#5b21b6}.slot-color-5{background:#fee2e2;color:#991b1b}.slot-color-6{background:#cffafe;color:#155e75}.slot-color-7{background:#fef9c3;color:#854d0e}.slot-color-8{background:#e5e7eb;color:#374151}
        @media print { body > :not(.print-table),.print-heading,.print-info { display:none!important; } }
    </style>
</head>
<body>
<main class="print-table">
    <h2 class="print-heading">EMPLOI DU TEMPS DU PROFESSEUR</h2>
    <div class="info print-info"><span><b>Nom :</b> {{ $enseignant->nom }}</span><span><b>Prénoms :</b> {{ $enseignant->prenoms }}</span><span><b>Matricule :</b> {{ $enseignant->matricule ?? '—' }}</span></div>
    <table><thead><tr><th>Horaires</th>@foreach($days as $day)<th>{{ ucfirst($day) }}</th>@endforeach</tr></thead><tbody>
        @foreach($slots as $slot)
            @if(isset($slot['break']))
                <tr><td class="break" colspan="6">{{ $slot['break'] }}</td></tr>
            @else
                <tr><td class="time">{{ str_replace(':','h',$slot['start']) }} - {{ str_replace(':','h',$slot['end']) }}</td>
                    @foreach($days as $day)
                        @php($entry = $grid[$day][$slot['key']] ?? null)
                        <td class="cell {{ $entry ? $slot['color'] : 'empty' }}">
                            @if($entry)
                                <b>{{ $entry->matiere?->nom }}</b><br>{{ $entry->classe?->nom }}
                                @if($entry->serie)<br>{{ preg_replace('/^Série\s*/iu', '', $entry->serie->nom_serie) }}@endif
                            @else
                                Rien programmé
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endif
        @endforeach
    </tbody></table>
</main>
<script>window.addEventListener('load', () => window.setTimeout(() => window.print(), 150));</script>
</body>
</html>
