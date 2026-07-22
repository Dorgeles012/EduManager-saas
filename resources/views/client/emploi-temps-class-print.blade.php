<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Emploi du temps de classe</title>
    <style>
        /* Reset et styles de base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', 'Segoe UI', Arial, sans-serif;
            font-size: 12px;
            background: white;
            padding: 30px 20px;
            color: #1f2937;
        }

        /* En-tête */
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 3px solid #1a3a6b;
        }

        .header h2 {
            font-size: 20px;
            font-weight: 700;
            color: #1a3a6b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .header .subtitle {
            font-size: 13px;
            color: #4b5563;
            font-weight: 500;
        }

        .header .subtitle span {
            color: #1a3a6b;
            font-weight: 600;
        }

        .header .school-info {
            font-size: 11px;
            color: #6b7280;
            margin-top: 5px;
        }

        /* Métadonnées */
        .meta-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 10px 15px;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            font-size: 11px;
            flex-wrap: wrap;
            gap: 8px;
        }

        .meta-info .label {
            font-weight: 600;
            color: #4b5563;
        }

        .meta-info .value {
            color: #1f2937;
            font-weight: 500;
        }

        /* Tableau */
        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 11px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border-radius: 12px;
            overflow: hidden;
        }

        th {
            background: #e8edf4;
            color: #000000;
            padding: 12px 10px;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: center;
            border: 1px solid #1a3a6b;
        }

        td {
            padding: 10px 8px;
            border: 1px solid #e5e7eb;
            vertical-align: middle;
            text-align: center;
        }

        /* Colonne des horaires */
        .col-horaire {
            background: #f8fafc;
            font-weight: 700;
            color: #000000 !important;
            width: 100px;
            min-width: 100px;
            text-align: center;
            border-right: 2px solid #d1d5db;
        }

        /* Cellules des jours */
        .cell-day {
            min-width: 120px;
            padding: 8px 6px;
            min-height: 60px;
            vertical-align: middle;
        }

        /* Cellule vide */
        .cell-empty {
            color: #d1d5db;
            font-size: 10px;
            font-style: italic;
        }

        /* Cellule avec données */
        .cell-has-data {
            background: #fafcff;
        }
        .slot-color-1{background:#dbeafe}.slot-color-2{background:#dcfce7}.slot-color-3{background:#ffedd5}.slot-color-4{background:#ede9fe}.slot-color-5{background:#fee2e2}.slot-color-6{background:#cffafe}.slot-color-7{background:#fef9c3}.slot-color-8{background:#e5e7eb}

        .cell-content {
            display: flex;
            flex-direction: column;
            gap: 2px;
            align-items: center;
        }

        .cell-matiere {
            font-weight: 700;
            font-size: 12px;
            color: #1f2937;
        }

        .cell-enseignant {
            font-size: 10px;
            color: #4b5563;
        }

        .cell-salle {
            font-size: 9px;
            color: #6b7280;
            background: #f3f4f6;
            padding: 1px 8px;
            border-radius: 10px;
            display: inline-block;
        }

        /* Alternance des lignes */
        tbody tr:nth-child(even) td:not(.col-horaire) {
            background: #fafcff;
        }

        tbody tr:nth-child(even) .col-horaire {
            background: #f1f5f9;
        }

        /* Ligne de pause */
        .break-row td {
            background: #e5e7eb !important;
            color: #4b5563;
            font-weight: 700;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: center;
            padding: 8px;
        }

        /* Badge */
        .badge-matiere {
            display: inline-block;
            padding: 2px 8px;
            background: #e5e7eb;
            border-radius: 10px;
            font-size: 9px;
            font-weight: 500;
            color: #1f2937;
        }

        /* Pied de page */
        .footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
        }

        .footer span {
            color: #1a3a6b;
            font-weight: 600;
        }

        /* Pour l'impression */
        @media print {
            body {
                padding: 15px;
                font-size: 10px;
            }

            .header h2 {
                font-size: 16px;
            }

            th {
                padding: 8px 8px;
                font-size: 9px;
            }

            td {
                padding: 6px 6px;
                font-size: 9px;
            }

            .meta-info {
                padding: 8px 12px;
                font-size: 9px;
            }

            .col-horaire {
                width: 80px;
                min-width: 80px;
                font-size: 9px;
            }

            .cell-day {
                min-width: 100px;
                padding: 5px 4px;
            }

            .cell-matiere {
                font-size: 10px;
            }

            .cell-enseignant {
                font-size: 9px;
            }

            tbody tr:nth-child(even) td:not(.col-horaire) {
                background: #f7f9fc;
            }

            .break-row td {
                font-size: 8px;
                padding: 5px;
            }

            /* Éviter les coupures de page */
            tr {
                page-break-inside: avoid;
            }

            thead {
                display: table-header-group;
            }
        }

        @media (max-width: 768px) {
            .meta-info {
                flex-direction: column;
                gap: 4px;
            }

            .col-horaire {
                width: 70px;
                min-width: 70px;
                font-size: 9px;
                padding: 6px 4px;
            }

            .cell-day {
                min-width: 80px;
                padding: 4px 3px;
            }

            table {
                font-size: 9px;
            }

            th {
                font-size: 9px;
                padding: 8px 5px;
            }

            th, td {
                padding: 5px 4px;
            }

            .cell-matiere {
                font-size: 9px;
            }

            .cell-enseignant {
                font-size: 8px;
            }
        }
    </style>
</head>
<body>

    <!-- En-tête -->
    <div class="header">
        <h2>📚 Emploi du temps de classe</h2>
        <div class="subtitle">
            Classe : <span>{{ $classe->nom }}</span>
        </div>
        <div class="school-info">
            {{ $school?->nom ?? 'Établissement' }}
            @if($year)
                — {{ $year->libelle }}
            @endif
        </div>
    </div>

    <!-- Métadonnées -->
    <div class="meta-info">
        <div>
            <span class="label">Classe :</span>
            <span class="value">{{ $classe->nom }}</span>
        </div>
        <div>
            <span class="label">Effectif :</span>
            <span class="value">{{ $classe->effectif ?? '—' }}</span>
        </div>
        <div>
            <span class="label">Année académique :</span>
            <span class="value">{{ $year->libelle ?? 'Non définie' }}</span>
        </div>
        <div>
            <span class="label">Total séances :</span>
            <span class="value">{{ $totalSeances ?? $entries->count() ?? 0 }}</span>
        </div>
    </div>

    <!-- Tableau -->
    <table>
        <thead>
            <tr>
                <th>Horaires</th>
                @foreach($days as $day)
                    <th>{{ ucfirst($day) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($slots as $slot)
                @if(isset($slot['break']))
                    <!-- Ligne de pause -->
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
                                            <span class="cell-salle">📍 {{ $entry->salle }}</span>
                                        @endif
                                        @if($entry->serie)
                                            <span class="badge-matiere">{{ $entry->serie->nom_serie }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span style="font-size:9px;color:#94a3b8;font-style:italic;">Rien programmé</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <!-- Pied de page -->
    <div class="footer">
        Document généré le <span>{{ date('d/m/Y à H:i') }}</span>
        — Tous droits réservés
    </div>

</body>
</html>
