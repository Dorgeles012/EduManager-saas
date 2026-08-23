<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Emploi du temps - {{ $enseignant->nom }} {{ $enseignant->prenoms }}</title>
    <style>
        @page { size: A4 landscape; margin: 10mm; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #1e293b; font-size: 11px; margin: 0; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #1f108e; padding-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; color: #1f108e; text-transform: uppercase; margin: 0 0 4px 0; }
        .subtitle { font-size: 12px; color: #64748b; margin: 0; }
        .info-grid { width: 100%; margin-bottom: 12px; border-collapse: collapse; }
        .info-grid td { padding: 4px 8px; font-size: 11px; }
        .schedule-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .schedule-table th, .schedule-table td { border: 1px solid #cbd5e1; padding: 6px; text-align: center; vertical-align: top; }
        .schedule-table th { background-color: #1f108e; color: #ffffff; font-weight: 600; font-size: 10px; text-transform: uppercase; }
        .schedule-table th.time-col { width: 120px; background-color: #f1f5f9; color: #1e293b; font-weight: bold; }
        .break-row { background-color: #f8fafc; font-weight: bold; color: #64748b; }
        .course-card { background: #eef2ff; border-radius: 4px; padding: 4px; text-align: left; }
        .course-class { font-weight: bold; color: #1f108e; font-size: 11px; }
        .course-sub { font-size: 10px; color: #334155; }
        .course-room { font-size: 9px; color: #64748b; }
        .footer { margin-top: 15px; font-size: 9px; text-align: right; color: #94a3b8; }
    </style>
</head>
<body onload="if(window.location.href.indexOf('print') > -1) { window.print(); }">
    <div class="header">
        <h1 class="title">{{ $school->nom ?? 'EduManager' }}</h1>
        <p class="subtitle">Emploi du temps enseignant • Année Académique {{ $academicYear->libelle ?? date('Y') }}</p>
    </div>

    <table class="info-grid">
        <tr>
            <td><strong>Enseignant :</strong> {{ $enseignant->nom }} {{ $enseignant->prenoms }}</td>
            <td><strong>Matricule :</strong> {{ $enseignant->matricule ?? '—' }}</td>
            <td><strong>Discipline(s) :</strong> {{ $enseignant->matieres->pluck('nom')->join(', ') }}</td>
            <td><strong>Classes :</strong> {{ $enseignant->classes->pluck('nom')->join(', ') }}</td>
        </tr>
    </table>

    <table class="schedule-table">
        <thead>
            <tr>
                <th class="time-col">Créneau</th>
                @foreach($days as $day)
                    <th>{{ ucfirst($day) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($slots as $slot)
                @if(isset($slot['break']))
                    <tr class="break-row">
                        <td colspan="{{ count($days) + 1 }}">☕ {{ $slot['break'] }}</td>
                    </tr>
                @else
                    @php($slotKey = $slot['key'])
                    <tr>
                        <td class="time-col">{{ $slot['start'] }} - {{ $slot['end'] }}</td>
                        @foreach($days as $day)
                            @php($cell = $grid[$day][$slotKey] ?? null)
                            <td>
                                @if($cell)
                                    <div class="course-card">
                                        <div class="course-class">{{ $cell['classe'] }}</div>
                                        <div class="course-sub">{{ $cell['matiere'] }}</div>
                                        @if(!empty($cell['salle']))
                                            <div class="course-room">Salle : {{ $cell['salle'] }}</div>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Généré le {{ date('d/m/Y H:i') }} par EduManager
    </div>
</body>
</html>
