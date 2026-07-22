<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Emploi du temps</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        body {
            font-family: 'DejaVu Sans', 'Segoe UI', Arial, sans-serif;
            color: #172033;
            font-size: 10px;
            background: white;
            margin: 0;
            padding: 0;
        }

        /* En-tête principal */
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #173b78;
        }

        .header h2 {
            font-size: 18px;
            font-weight: 700;
            color: #173b78;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin: 0 0 8px 0;
        }

        .header .subtitle {
            font-size: 12px;
            color: #4b5563;
        }

        .header .subtitle span {
            color: #173b78;
            font-weight: 600;
        }

        /* Informations du professeur */
        .teacher-info {
            display: flex;
            flex-wrap: wrap;
            background: #f8fafc;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 18px;
            border: 1px solid #e5e7eb;
            font-size: 10px;
        }

        .teacher-info .info-item {
            flex: 1;
            min-width: 30%;
            padding: 3px 0;
        }

        .teacher-info .info-item .label {
            font-weight: 600;
            color: #4b5563;
        }

        .teacher-info .info-item .value {
            color: #1f2937;
            font-weight: 500;
        }

        /* Tableau principal */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border-radius: 8px;
            overflow: hidden;
        }

        thead th {
            background: #e8edf4;
            color: #000000;
            text-align: center;
            padding: 10px 8px;
            font-weight: 700;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #173b78;
        }

        th, td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            vertical-align: top;
        }

        /* Colonne des horaires */
        .time-col {
            background: #f4f7fb;
            text-align: center;
            width: 85px;
            min-width: 85px;
            font-weight: 700;
            font-size: 9px;
            color: #000000 !important;
            vertical-align: middle;
            border-right: 2px solid #d1d5db;
        }

        /* Cellules des jours */
        .cell {
            min-height: 45px;
            height: auto;
            padding: 6px 6px;
            vertical-align: middle;
            text-align: left;
        }

        /* Cellule vide */
        .empty {
            color: #94a3b8;
            text-align: center;
            font-style: italic;
            vertical-align: middle;
            font-size: 8px;
        }

        /* Ligne de pause */
        .break-row {
            background: #e5e7eb !important;
        }

        .break-row td {
            background: #e5e7eb !important;
            text-align: center;
            font-weight: 700;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #4b5563;
            padding: 8px;
            border: 1px solid #d1d5db;
        }

        /* Couleurs pour les matières */
        .slot-color-1 {
            background: #dbeafe;
            color: #1e3a8a;
            border-left: 4px solid #1e3a8a;
        }
        .slot-color-2 {
            background: #dcfce7;
            color: #166534;
            border-left: 4px solid #166534;
        }
        .slot-color-3 {
            background: #ffedd5;
            color: #9a3412;
            border-left: 4px solid #9a3412;
        }
        .slot-color-4 {
            background: #ede9fe;
            color: #5b21b6;
            border-left: 4px solid #5b21b6;
        }
        .slot-color-5 {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #991b1b;
        }
        .slot-color-6 {
            background: #cffafe;
            color: #155e75;
            border-left: 4px solid #155e75;
        }
        .slot-color-7 {
            background: #fef9c3;
            color: #854d0e;
            border-left: 4px solid #854d0e;
        }
        .slot-color-8 {
            background: #e5e7eb;
            color: #374151;
            border-left: 4px solid #374151;
        }

        /* Contenu des cellules */
        .cell-content {
            display: flex;
            flex-direction: column;
            gap: 1px;
        }

        .cell-content .matiere {
            font-weight: 700;
            font-size: 10px;
            display: block;
        }

        .cell-content .classe {
            font-weight: 500;
            font-size: 9px;
            display: block;
            color: #374151;
        }

        .cell-content .serie {
            font-size: 8px;
            display: inline-block;
            background: rgba(0, 0, 0, 0.05);
            padding: 1px 6px;
            border-radius: 10px;
            margin-top: 2px;
            align-self: flex-start;
        }

        /* Pied de page */
        .footer {
            margin-top: 18px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
            display: flex;
            justify-content: space-between;
        }

        .footer span {
            color: #173b78;
            font-weight: 600;
        }

        /* Légende */
        .legend {
            display: flex;
            flex-wrap: wrap;
            gap: 6px 12px;
            margin-top: 12px;
            padding: 8px 12px;
            background: #f8fafc;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            font-size: 8px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 3px;
            border: 1px solid #d1d5db;
        }

        /* Responsive et impression */
        @media print {
            body {
                font-size: 9px;
            }

            .header h2 {
                font-size: 15px;
            }

            .teacher-info {
                padding: 8px 12px;
                font-size: 9px;
            }

            .time-col {
                width: 75px;
                min-width: 75px;
                font-size: 8px;
            }

            th, td {
                padding: 5px 5px;
            }

            .cell-content .matiere {
                font-size: 9px;
            }

            .cell-content .classe {
                font-size: 8px;
            }

            .break-row td {
                font-size: 8px;
                padding: 6px;
            }

            .footer {
                font-size: 7px;
            }

            .legend {
                font-size: 7px;
                padding: 6px 10px;
            }

            .legend-color {
                width: 10px;
                height: 10px;
            }

            /* Éviter les coupures de page */
            tr {
                page-break-inside: avoid;
            }

            thead {
                display: table-header-group;
            }

            .no-print {
                display: none !important;
            }
        }

        @media (max-width: 768px) {
            .teacher-info .info-item {
                min-width: 48%;
            }

            .time-col {
                width: 65px;
                min-width: 65px;
                font-size: 8px;
            }

            .cell-content .matiere {
                font-size: 8px;
            }

            .cell-content .classe {
                font-size: 7px;
            }
        }
    </style>
</head>
<body>

    <!-- En-tête -->
    <div class="header">
        <h2>📋 Emploi du temps du professeur</h2>
        <div class="subtitle">
            Année académique : <span>{{ $year->libelle ?? 'Non définie' }}</span>
            @if(isset($school))
                — {{ $school->nom }}
            @endif
        </div>
    </div>

    <!-- Informations du professeur -->
    <div class="teacher-info">
        <div class="info-item">
            <span class="label">Nom :</span>
            <span class="value">{{ $enseignant->nom }}</span>
        </div>
        <div class="info-item">
            <span class="label">Prénoms :</span>
            <span class="value">{{ $enseignant->prenoms }}</span>
        </div>
        <div class="info-item">
            <span class="label">Matricule :</span>
            <span class="value">{{ $enseignant->matricule ?? '—' }}</span>
        </div>
        <div class="info-item">
            <span class="label">Discipline(s) :</span>
            <span class="value">{{ $enseignant->matieres->pluck('nom')->join(', ') ?: '—' }}</span>
        </div>
        <div class="info-item">
            <span class="label">Contact :</span>
            <span class="value">{{ $enseignant->telephone ?? $enseignant->email ?? '—' }}</span>
        </div>
        <div class="info-item">
            <span class="label">Total séances :</span>
            <span class="value">{{ $totalSeances ?? $entries->count() ?? 0 }}</span>
        </div>
    </div>

    <!-- Tableau principal -->
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
                                            <span style="font-size:7px;color:#6b7280;margin-top:2px;">
                                                📍 {{ $entry->salle }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span style="font-size:7px;">Rien programmé</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <!-- Légende des couleurs -->
    @if(isset($matieres) && $matieres->count() > 0)
        <div class="legend">
            <span style="font-weight:600;color:#4b5563;font-size:9px;">Légende :</span>
            @foreach($matieres as $matiere)
                <div class="legend-item">
                    <span class="legend-color" style="background:{{ $matiere->color ?? '#dbeafe' }};"></span>
                    {{ $matiere->nom }}
                </div>
            @endforeach
        </div>
    @endif

    <!-- Pied de page -->
    <div class="footer">
        <span>Document généré le {{ date('d/m/Y à H:i') }}</span>
        <span>Emploi du temps - Professeur</span>
        <span>© {{ date('Y') }} - Tous droits réservés</span>
    </div>

    <!-- Impression automatique -->
    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 300);
        });
    </script>

</body>
</html>
