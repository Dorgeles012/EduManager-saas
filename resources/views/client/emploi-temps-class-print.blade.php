<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Emploi du temps — {{ $classe->nom }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            background: #ffffff;
            padding: 24px;
            color: #111827;
            line-height: 1.4;
        }

        /* ============ HEADER ============ */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 3px solid #4f46e5;
        }

        .header-left h1 {
            font-size: 18px;
            font-weight: 800;
            color: #4f46e5;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
        }

        .header-left .subtitle {
            font-size: 13px;
            color: #374151;
            font-weight: 600;
        }

        .header-left .subtitle strong {
            color: #4f46e5;
        }

        .header-left .school-info {
            font-size: 11px;
            color: #6b7280;
            margin-top: 4px;
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
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .header-right .year {
            margin-top: 6px;
            font-size: 11px;
            color: #6b7280;
            font-weight: 600;
        }

        /* ============ META INFO ============ */
        .meta-info {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .meta-item {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 10px 12px;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .meta-item .label {
            font-size: 9px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .meta-item .value {
            font-size: 13px;
            font-weight: 700;
            color: #111827;
        }

        .meta-item .value.accent {
            color: #4f46e5;
        }

        /* ============ TABLE ============ */
        table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            font-size: 11px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        thead th {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: #ffffff;
            padding: 12px 8px;
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

        thead th.col-horaire {
            background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
        }

        td {
            padding: 8px 6px;
            border-top: 1px solid #f1f5f9;
            border-right: 1px solid #f1f5f9;
            vertical-align: middle;
            text-align: center;
        }

        td:last-child {
            border-right: none;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        /* Colonne des horaires */
        .col-horaire {
            background: #f9fafb;
            font-weight: 700;
            color: #4f46e5 !important;
            width: 100px;
            min-width: 100px;
            font-size: 11px;
            letter-spacing: 0.02em;
        }

        /* Cellules des jours */
        .cell-day {
            min-width: 120px;
            padding: 8px 6px;
            min-height: 64px;
            vertical-align: middle;
        }

        .cell-has-data {
            background: #ffffff;
        }

        /* Couleurs de slot */
        .slot-color-1 { background: #dbeafe; }
        .slot-color-2 { background: #dcfce7; }
        .slot-color-3 { background: #ffedd5; }
        .slot-color-4 { background: #ede9fe; }
        .slot-color-5 { background: #fee2e2; }
        .slot-color-6 { background: #cffafe; }
        .slot-color-7 { background: #fef9c3; }
        .slot-color-8 { background: #e5e7eb; }

        .cell-content {
            display: flex;
            flex-direction: column;
            gap: 3px;
            align-items: center;
        }

        .cell-matiere {
            font-weight: 800;
            font-size: 11px;
            color: #111827;
            line-height: 1.2;
        }

        .cell-enseignant {
            font-size: 10px;
            color: #4b5563;
            font-weight: 500;
        }

        .cell-salle {
            font-size: 9px;
            color: #4f46e5;
            background: rgba(79, 70, 229, 0.08);
            padding: 2px 8px;
            border-radius: 999px;
            display: inline-block;
            font-weight: 600;
        }

        .badge-matiere {
            display: inline-block;
            padding: 2px 8px;
            background: rgba(17, 24, 39, 0.06);
            border-radius: 999px;
            font-size: 9px;
            font-weight: 600;
            color: #374151;
        }

        /* Cellule vide */
        .cell-empty {
            background: #fafafa;
        }

        .cell-empty-text {
            font-size: 9px;
            color: #cbd5e1;
            font-style: italic;
        }

        /* Ligne de pause */
        .break-row td {
            background: linear-gradient(90deg, #eef2ff 0%, #e0e7ff 100%) !important;
            color: #4338ca;
            font-weight: 800;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            text-align: center;
            padding: 10px;
            border-top: 1px solid #c7d2fe;
            border-bottom: 1px solid #c7d2fe;
        }

        /* Alternance de lignes */
        tbody tr:nth-child(even) td:not(.col-horaire):not(.cell-has-data):not(.cell-empty) {
            background: #fafbfc;
        }

        /* ============ FOOTER ============ */
        .footer {
            margin-top: 20px;
            padding-top: 14px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 10px;
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
            .header-left .subtitle { font-size: 11px; }

            .meta-info { margin-bottom: 14px; gap: 8px; }
            .meta-item { padding: 8px 10px; }
            .meta-item .label { font-size: 8px; }
            .meta-item .value { font-size: 11px; }

            thead th {
                padding: 9px 6px;
                font-size: 9px;
            }

            td {
                padding: 6px 4px;
            }

            .col-horaire {
                width: 80px;
                min-width: 80px;
                font-size: 10px;
            }

            .cell-day {
                min-width: 100px;
                padding: 5px 4px;
            }

            .cell-matiere { font-size: 10px; }
            .cell-enseignant { font-size: 9px; }
            .cell-salle { font-size: 8px; padding: 1px 6px; }

            .break-row td {
                padding: 7px;
                font-size: 9px;
            }

            tr { page-break-inside: avoid; }
            thead { display: table-header-group; }

            .footer {
                margin-top: 14px;
                padding-top: 10px;
                font-size: 9px;
            }
        }

     
        @media (max-width: 768px) {
            body { padding: 14px; }

            .header {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }

            .header-right { text-align: center; }

            .meta-info {
                grid-template-columns: 1fr 1fr;
                gap: 6px;
            }

            .col-horaire {
                width: 70px;
                min-width: 70px;
                font-size: 9px;
                padding: 6px 4px;
            }

            .cell-day {
                min-width: 80px;
                padding: 5px 3px;
            }

            table { font-size: 9px; }

            thead th {
                font-size: 8px;
                padding: 7px 4px;
            }

            .cell-matiere { font-size: 9px; }
            .cell-enseignant { font-size: 8px; }
        }
    </style>
</head>
<body>

 
    <div class="header">
        <div class="header-left">
            <h1>Emploi du temps</h1>
            <div class="subtitle">Classe : <strong>{{ $classe->nom }}</strong></div>
            <div class="school-info">
                {{ $school?->nom ?? 'Établissement' }}
            </div>
        </div>
        <div class="header-right">
            <div class="badge">Planning hebdomadaire</div>
            @if($year)
                <div class="year">Année académique {{ $year->libelle }}</div>
            @endif
        </div>
    </div>


    <div class="meta-info">
        <div class="meta-item">
            <span class="label">Classe</span>
            <span class="value accent">{{ $classe->nom }}</span>
        </div>
        <div class="meta-item">
            <span class="label">Effectif</span>
            <span class="value">{{ $classe->effectif ?? '—' }}</span>
        </div>
        <div class="meta-item">
            <span class="label">Année académique</span>
            <span class="value">{{ $year->libelle ?? 'Non définie' }}</span>
        </div>
        <div class="meta-item">
            <span class="label">Total séances</span>
            <span class="value accent">{{ $totalSeances ?? $entries->count() ?? 0 }}</span>
        </div>
    </div>

    {{-- ============ TABLEAU ============ --}}
    <table>
        <thead>
            <tr>
                <th class="col-horaire">Horaires</th>
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
                        <td class="col-horaire">
                            {{ str_replace(':', 'h', $startTime) }} - {{ str_replace(':', 'h', $endTime) }}
                        </td>

                        @foreach($days as $day)
                            @php
                                $entry = $grid[$day][$slotKey] ?? null;
                                $hasData = $entry && ($entry->classe_id || $entry->matiere_id);
                            @endphp
                            <td class="cell-day {{ $hasData ? 'cell-has-data ' . ($slot['color'] ?? 'slot-color-1') : 'cell-empty' }}">
                                @if($hasData)
                                    <div class="cell-content">
                                        <span class="cell-matiere">{{ $entry->matiere?->nom ?? '—' }}</span>
                                        <span class="cell-enseignant">{{ $entry->enseignant?->nom ?? '' }} {{ $entry->enseignant?->prenoms ?? '' }}</span>
                                        @if($entry->salle)
                                            <span class="cell-salle">{{ $entry->salle }}</span>
                                        @endif
                                        @if($entry->serie)
                                            <span class="badge-matiere">{{ $entry->serie->nom_serie }}</span>
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


    <div class="footer">
        <span>Document généré le <strong>{{ date('d/m/Y à H:i') }}</strong></span>
        <span class="footer-center">Emploi du temps — {{ $classe->nom }}</span>
        <span>© {{ date('Y') }} — Tous droits réservés</span>
    </div>

</body>
</html>