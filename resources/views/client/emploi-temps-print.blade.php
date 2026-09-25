<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Emploi du temps — {{ $enseignant->nom }} {{ $enseignant->prenoms }}</title>
    <style>
        * { box-sizing: border-box; }

        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'DejaVu Sans', Arial, sans-serif;
            color: #111827;
            font-size: 11px;
            background: #ffffff;
            margin: 0;
            padding: 24px;
            line-height: 1.4;
        }

        /* ============ HEADER ============ */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 18px;
            padding-bottom: 16px;
            border-bottom: 3px solid #4f46e5;
        }

        .header-left h1 {
            font-size: 18px;
            font-weight: 800;
            color: #4f46e5;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 0 0 4px;
        }

        .header-left .subtitle {
            font-size: 12px;
            color: #374151;
            font-weight: 600;
        }

        .header-left .subtitle strong {
            color: #4f46e5;
        }

        .header-right {
            text-align: right;
        }

        .header-right .badge {
            display: inline-block;
            padding: 6px 14px;
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: #ffffff;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* ============ TEACHER INFO ============ */
        .teacher-info {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 18px;
        }

        .info-item {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 10px 12px;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .info-item .label {
            font-size: 9px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .info-item .value {
            font-size: 12px;
            font-weight: 700;
            color: #111827;
        }

        .info-item .value.accent {
            color: #4f46e5;
        }

        /* ============ TABLE ============ */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 10px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        thead th {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: #ffffff;
            padding: 11px 8px;
            font-weight: 700;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            text-align: center;
            border: none;
            border-right: 1px solid rgba(255, 255, 255, 0.15);
        }

        thead th:last-child {
            border-right: none;
        }

        thead th.time-col-header {
            background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
        }

        th, td {
            border-top: 1px solid #f1f5f9;
            border-right: 1px solid #f1f5f9;
            padding: 6px 6px;
            vertical-align: middle;
        }

        td:last-child {
            border-right: none;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        /* Colonne des horaires */
        .time-col {
            background: #f9fafb;
            text-align: center;
            width: 90px;
            min-width: 90px;
            font-weight: 800;
            font-size: 10px;
            color: #4f46e5 !important;
            vertical-align: middle;
            letter-spacing: 0.02em;
        }

        /* Cellules */
        .cell {
            min-height: 50px;
            height: auto;
            padding: 6px;
            vertical-align: middle;
            text-align: left;
        }

        /* Cellule vide */
        .empty {
            background: #fafafa;
            color: #cbd5e1;
            text-align: center;
            font-style: italic;
            vertical-align: middle;
            font-size: 9px;
        }

        /* Ligne de pause */
        .break-row td {
            background: linear-gradient(90deg, #eef2ff 0%, #e0e7ff 100%) !important;
            color: #4338ca;
            text-align: center;
            font-weight: 800;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 9px;
            border-top: 1px solid #c7d2fe;
            border-bottom: 1px solid #c7d2fe;
        }

        /* ============ COULEURS PAR MATIÈRE ============ */
        .slot-color-1 { background: #dbeafe; border-left: 4px solid #1e3a8a; }
        .slot-color-2 { background: #dcfce7; border-left: 4px solid #166534; }
        .slot-color-3 { background: #ffedd5; border-left: 4px solid #9a3412; }
        .slot-color-4 { background: #ede9fe; border-left: 4px solid #5b21b6; }
        .slot-color-5 { background: #fee2e2; border-left: 4px solid #991b1b; }
        .slot-color-6 { background: #cffafe; border-left: 4px solid #155e75; }
        .slot-color-7 { background: #fef9c3; border-left: 4px solid #854d0e; }
        .slot-color-8 { background: #e5e7eb; border-left: 4px solid #374151; }

        /* ============ CONTENU CELLULE ============ */
        .cell-content {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .cell-content .matiere {
            font-weight: 800;
            font-size: 11px;
            display: block;
            color: #111827;
            line-height: 1.2;
        }

        .cell-content .classe {
            font-weight: 600;
            font-size: 10px;
            display: block;
            color: #374151;
        }

        .cell-content .serie {
            font-size: 9px;
            display: inline-block;
            background: rgba(17, 24, 39, 0.06);
            padding: 2px 8px;
            border-radius: 999px;
            margin-top: 2px;
            align-self: flex-start;
            font-weight: 600;
            color: #374151;
        }

        .cell-content .salle {
            font-size: 9px;
            color: #4f46e5;
            background: rgba(79, 70, 229, 0.08);
            padding: 2px 8px;
            border-radius: 999px;
            margin-top: 2px;
            align-self: flex-start;
            font-weight: 600;
        }

        .cell-empty-text {
            font-size: 9px;
            color: #cbd5e1;
            font-style: italic;
        }

        /* ============ LÉGENDE ============ */
        .legend {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px 14px;
            margin-top: 14px;
            padding: 10px 14px;
            background: #f9fafb;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            font-size: 10px;
        }

        .legend-title {
            font-weight: 800;
            color: #4f46e5;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #374151;
            font-weight: 600;
        }

        .legend-color {
            width: 14px;
            height: 14px;
            border-radius: 4px;
            border: 1px solid rgba(0, 0, 0, 0.08);
        }

        /* ============ FOOTER ============ */
        .footer {
            margin-top: 16px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 9px;
            color: #9ca3af;
        }

        .footer strong {
            color: #4f46e5;
            font-weight: 700;
        }

        .footer .footer-center {
            font-weight: 600;
            color: #6b7280;
        }

        /* ============ IMPRESSION ============ */
        @media print {
            body {
                padding: 0;
                font-size: 10px;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .header {
                margin-bottom: 14px;
                padding-bottom: 12px;
            }

            .header-left h1 { font-size: 15px; }

            .teacher-info {
                margin-bottom: 12px;
                gap: 8px;
            }

            .info-item {
                padding: 8px 10px;
            }

            .info-item .label { font-size: 8px; }
            .info-item .value { font-size: 11px; }

            thead th {
                padding: 8px 6px;
                font-size: 9px;
            }

            th, td {
                padding: 5px;
            }

            .time-col {
                width: 75px;
                min-width: 75px;
                font-size: 9px;
            }

            .cell-content .matiere { font-size: 10px; }
            .cell-content .classe { font-size: 9px; }
            .cell-content .serie,
            .cell-content .salle { font-size: 8px; padding: 1px 6px; }

            .break-row td {
                padding: 7px;
                font-size: 9px;
            }

            .legend {
                margin-top: 10px;
                padding: 8px 12px;
                font-size: 9px;
            }

            .legend-color { width: 12px; height: 12px; }

            .footer {
                margin-top: 12px;
                padding-top: 8px;
                font-size: 8px;
            }

            tr { page-break-inside: avoid; }
            thead { display: table-header-group; }

            .no-print { display: none !important; }
        }

        /* ============ MOBILE ============ */
        @media (max-width: 768px) {
            body { padding: 14px; }

            .header {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }

            .header-right { text-align: center; }

            .teacher-info {
                grid-template-columns: 1fr 1fr;
                gap: 6px;
            }

            .time-col {
                width: 65px;
                min-width: 65px;
                font-size: 9px;
            }

            .cell-content .matiere { font-size: 10px; }
            .cell-content .classe { font-size: 9px; }
        }
    </style>
</head>
<body>

    {{-- ============ HEADER ============ --}}
    <div class="header">
        <div class="header-left">
            <h1>Emploi du temps</h1>
            <div class="subtitle">
                Professeur : <strong>{{ $enseignant->nom }} {{ $enseignant->prenoms }}</strong>
            </div>
        </div>
        <div class="header-right">
            <div class="badge">Planning hebdomadaire</div>
        </div>
    </div>

    {{-- ============ TEACHER INFO ============ --}}
    <div class="teacher-info">
        <div class="info-item">
            <span class="label">Nom</span>
            <span class="value">{{ $enseignant->nom }}</span>
        </div>
        <div class="info-item">
            <span class="label">Prénoms</span>
            <span class="value">{{ $enseignant->prenoms }}</span>
        </div>
        <div class="info-item">
            <span class="label">Matricule</span>
            <span class="value">{{ $enseignant->matricule ?? '—' }}</span>
        </div>
        <div class="info-item">
            <span class="label">Discipline(s)</span>
            <span class="value">{{ $enseignant->matieres->pluck('nom')->join(', ') ?: '—' }}</span>
        </div>
        <div class="info-item">
            <span class="label">Contact</span>
            <span class="value">{{ $enseignant->telephone ?? $enseignant->email ?? '—' }}</span>
        </div>
        <div class="info-item">
            <span class="label">Total séances</span>
            <span class="value accent">{{ $totalSeances ?? $entries->count() ?? 0 }}</span>
        </div>
    </div>

    {{-- ============ TABLEAU ============ --}}
    <table>
        <thead>
            <tr>
                <th class="time-col-header">Horaires</th>
                @foreach($days as $day)
                    <th>{{ ucfirst($day) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($slots as $slot)
                @if(isset($slot['break']))
                    {{-- Ligne de pause --}}
                    <tr class="break-row">
                        <td colspan="{{ count($days) + 1 }}">{{ $slot['break'] }}</td>
                    </tr>
                @else
                    @php
                        $slotKey = $slot['key'] ?? $slot[0] . '-' . $slot[1];
                        $startTime = $slot['start'] ?? $slot[0];
                        $endTime = $slot['end'] ?? $slot[1];
                    @endphp
                    <tr>
                        <td class="time-col">
                            {{ str_replace(':', 'h', $startTime) }} - {{ str_replace(':', 'h', $endTime) }}
                        </td>
                        @foreach($days as $day)
                            @php
                                $entry = $grid[$day][$slotKey] ?? null;
                                $hasData = $entry && ($entry->classe_id || $entry->matiere_id);
                            @endphp
                            <td class="cell {{ $hasData ? ($slot['color'] ?? 'slot-color-1') : 'empty' }}">
                                @if($hasData)
                                    <div class="cell-content">
                                        <span class="matiere">{{ $entry->matiere?->nom ?? '—' }}</span>
                                        <span class="classe">{{ $entry->classe?->nom ?? '—' }}</span>
                                        @if($entry->serie)
                                            <span class="serie">{{ preg_replace('/^Série\s*/iu', '', $entry->serie->nom_serie) }}</span>
                                        @endif
                                        @if(isset($entry->salle) && $entry->salle)
                                            <span class="salle">{{ $entry->salle }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="cell-empty-text">Rien programmé</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    {{-- ============ LÉGENDE ============ --}}
    @if(isset($matieres) && $matieres->count() > 0)
        <div class="legend">
            <span class="legend-title">Légende</span>
            @foreach($matieres as $matiere)
                <div class="legend-item">
                    <span class="legend-color" style="background:{{ $matiere->color ?? '#dbeafe' }};"></span>
                    {{ $matiere->nom }}
                </div>
            @endforeach
        </div>
    @endif

    {{-- ============ FOOTER ============ --}}
    <div class="footer">
        <span>Document généré le <strong>{{ date('d/m/Y à H:i') }}</strong></span>
        <span class="footer-center">Emploi du temps — {{ $enseignant->nom }} {{ $enseignant->prenoms }}</span>
        <span>© {{ date('Y') }} — Tous droits réservés</span>
    </div>

    {{-- ============ IMPRESSION AUTO ============ --}}
    <script>
        window.addEventListener('load', function() {
            setTimeout(function() { window.print(); }, 300);
        });
    </script>

</body>
</html>