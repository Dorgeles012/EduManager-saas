@extends('client.layouts.app')

@section('title', $editing ? 'Modifier un emploi du temps' : 'Créer un emploi du temps')

@section('content')
    <!-- En-tête -->
    <div class="mb-6 flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h2 class="font-headline-lg text-headline-lg text-primary">
                {{ $editing ? 'Modifier' : 'Créer' }} l'emploi du temps
            </h2>
            <p class="text-sm text-on-surface-variant">Emploi du temps du professeur</p>
        </div>
        <a href="{{ route('client.enseignant') }}" class="px-4 py-2 border rounded-lg">Retour</a>
    </div>

    <!-- Formulaire -->
    <form id="scheduleForm"
          action="{{ $editing ? route('client.emploi-temps.teacher.update', $enseignant) : route('client.emploi-temps.teacher.store', $enseignant) }}"
          method="POST">

        @csrf
        @if($editing) @method('PUT') @endif

        <input type="hidden" name="etablissement_id" value="{{ $establishmentId }}">

        <!-- Informations du professeur -->
        <div class="bg-white rounded-xl border custom-shadow p-6 mb-6">
            <div class="text-center border-b pb-4 mb-5">
                <h3 class="font-bold text-xl uppercase">Emploi du temps du professeur</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                <p><b>Nom :</b> {{ $enseignant->nom }}</p>
                <p><b>Prénoms :</b> {{ $enseignant->prenoms }}</p>
                <p><b>Matricule :</b> {{ $enseignant->matricule ?? '—' }}</p>
                <p><b>Discipline(s) :</b> {{ $enseignant->matieres->pluck('nom')->join(', ') }}</p>
                <p><b>Contact :</b> {{ $enseignant->telephone ?? $enseignant->email ?? '—' }}</p>
                <p><b>Années d'enseignement :</b> {{ $enseignant->nombre_annees_enseignement ?? '—' }}</p>
            </div>

            <!-- Année académique -->
            <div class="mt-5 max-w-md">
                <label class="block text-sm font-semibold mb-1">Année académique</label>
                <select name="annee_academique_id" class="w-full border rounded-lg px-3 py-2">
                    <option value="">Non précisée</option>
                    @foreach($years as $year)
                        <option value="{{ $year->id }}">{{ $year->libelle }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Grille de l'emploi du temps -->
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
                        @foreach($slots as $slot)
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
                                    <th class="schedule-time-editor">
                                        <label class="sr-only" for="start-{{ $slotKey }}">Début</label>
                                        <input id="start-{{ $slotKey }}" type="time" name="slots[{{ $slotKey }}][heure_debut]" value="{{ $startTime }}" required>
                                        <span>à</span>
                                        <label class="sr-only" for="end-{{ $slotKey }}">Fin</label>
                                        <input id="end-{{ $slotKey }}" type="time" name="slots[{{ $slotKey }}][heure_fin]" value="{{ $endTime }}" required>
                                    </th>

                                    @foreach($days as $day)
                                        @php
                                            $entry = $grid[$day][$slotKey] ?? null;
                                            $key = $day . '|' . $slotKey;
                                            $hasData = $entry && ($entry->classe_id || $entry->matiere_id);
                                        @endphp
                                        <td class="{{ $hasData ? 'has-data' : 'empty-cell' }}" data-day="{{ $day }}" data-slot="{{ $slotKey }}">
                                            @if($hasData)
                                                <!-- Affichage des données existantes -->
                                                <div class="cell-content">
                                                    <div class="cell-info">
                                                        <span class="cell-class-display">{{ $entry->classe->nom ?? '—' }}</span>
                                                        <span class="cell-subject-display">{{ $entry->matiere->nom ?? '—' }}</span>
                                                        @if($entry->serie)
                                                            <span class="cell-serie-display">{{ $entry->serie->nom_serie }}</span>
                                                        @endif
                                                    </div>
                                                    <button type="button" class="edit-cell-btn" data-key="{{ $key }}" data-day="{{ $day }}" data-slot="{{ $slotKey }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                        </svg>
                                                        Modifier
                                                    </button>
                                                </div>
                                                
                                                <!-- Champs cachés pour les données existantes -->
                                                <input type="hidden" name="cells[{{ $key }}][classe_id]" value="{{ $entry->classe_id }}">
                                                <input type="hidden" name="cells[{{ $key }}][matiere_id]" value="{{ $entry->matiere_id }}">
                                                <input type="hidden" name="cells[{{ $key }}][serie_id]" value="{{ $entry->serie_id }}">
                                            @else
                                                <!-- Cellule vide avec point cliquable -->
                                                <button type="button" class="add-cell-btn" data-key="{{ $key }}" data-day="{{ $day }}" data-slot="{{ $slotKey }}">
                                                    <span class="add-dot"></span>
                                                </button>
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

        <!-- Modal pour ajouter/modifier une cellule -->
        <div id="cellModal" class="modal-overlay" style="display:none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 id="modalTitle">Ajouter un cours</h3>
                    <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="modalCellKey">
                    <input type="hidden" id="modalDay">
                    <input type="hidden" id="modalSlot">
                    
                    <div class="form-group">
                        <label for="modalClasse">Classe</label>
                        <select id="modalClasse" class="modal-select">
                            <option value="">— Libre —</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="modalMatiere">Matière</label>
                        <select id="modalMatiere" class="modal-select">
                            <option value="">Matière</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="modalSerie">Série (facultatif)</label>
                        <select id="modalSerie" class="modal-select">
                            <option value="">Série (facultatif)</option>
                            @foreach($series as $serie)
                                <option value="{{ $serie->id }}">{{ $serie->nom_serie }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal()">Annuler</button>
                    <button type="button" class="btn-primary" id="saveCellBtn">Enregistrer</button>
                    @if($editing)
                        <button type="button" class="btn-danger" id="deleteCellBtn">Supprimer</button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('client.enseignant') }}" class="px-5 py-2.5 border rounded-lg">Annuler</a>
            <button class="px-6 py-2.5 bg-primary text-white rounded-lg" type="submit">
                Enregistrer l'emploi du temps
            </button>
        </div>
    </form>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.css" rel="stylesheet">
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

        /* Conteneur de défilement horizontal */
        .schedule-scroll-container {
            overflow-x: auto;
            overflow-y: visible;
            padding: 0;
            -webkit-overflow-scrolling: touch;
        }

        /* Scrollbar simple et native */
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
            transition: all 0.3s ease;
        }

        .schedule-scroll-container::-webkit-scrollbar-thumb:hover {
            background: #1a3a6b;
        }

        .schedule-scroll-container::-webkit-scrollbar-corner {
            background: #f1f1f1;
        }

        /* Pour Firefox */
        .schedule-scroll-container {
            scrollbar-width: thin;
            scrollbar-color: #c1c7cd #f1f1f1;
        }

        .schedule-scroll-container:hover {
            scrollbar-color: #1a3a6b #f1f1f1;
        }

        .schedule-table {
            width: 100%;
            min-width: 1200px;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.875rem;
            table-layout: fixed;
        }

        /* En-tête du tableau */
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

        /* Cellules du corps */
        .schedule-table td,
        .schedule-table th {
            border: 1px solid #e5e7eb;
            padding: 10px 8px;
            vertical-align: top;
        }

        /* Colonne des horaires */
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
        }

        /* Cellules des jours */
        .schedule-table tbody>tr>td {
            min-width: 200px;
            width: 200px;
            max-width: 200px;
            background: white;
            padding: 8px 6px;
            min-height: 120px;
            height: auto;
            transition: all 0.3s ease;
            position: relative;
            cursor: pointer;
        }

        .schedule-table td:hover {
            background: #f8fafc;
        }

        .schedule-time-editor {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .schedule-time-editor input {
            width: 74px;
            padding: 4px 6px;
            border: 1.5px solid #d1d5db;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 500;
            color: #1f2937;
            background: white;
            transition: all 0.2s ease;
            text-align: center;
        }

        .schedule-time-editor input:focus {
            outline: none;
            border-color: #1a3a6b;
            box-shadow: 0 0 0 3px rgba(26, 58, 107, 0.1);
        }

        .schedule-time-editor input:hover {
            border-color: #6b7280;
        }

        .schedule-time-editor span {
            font-weight: 700;
            color: #6b7280;
            font-size: 12px;
        }

        /* Style pour les cellules vides */
        .schedule-table td.empty-cell {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            min-height: 100px;
        }

        .add-cell-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            min-height: 80px;
            transition: all 0.3s ease;
        }

        .add-dot {
            display: inline-block;
            width: 12px;
            height: 12px;
            background: #d1d5db;
            border-radius: 50%;
            transition: all 0.3s ease;
            position: relative;
        }

        .add-dot::before {
            content: '+';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 10px;
            font-weight: bold;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .empty-cell:hover .add-dot {
            background: #1a3a6b;
            transform: scale(1.3);
            box-shadow: 0 0 20px rgba(26, 58, 107, 0.2);
        }

        .empty-cell:hover .add-dot::before {
            opacity: 1;
        }

        /* Style pour les cellules avec données */
        .schedule-table td.has-data {
            background: #fafcff;
            border-left: 3px solid #1a3a6b;
        }

        .cell-content {
            display: flex;
            flex-direction: column;
            gap: 4px;
            width: 100%;
        }

        .cell-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
            padding: 4px;
        }

        .cell-class-display {
            font-weight: 600;
            font-size: 12px;
            color: #1f2937;
        }

        .cell-subject-display {
            font-size: 11px;
            color: #4b5563;
        }

        .cell-serie-display {
            font-size: 10px;
            color: #6b7280;
            background: #f3f4f6;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
            align-self: flex-start;
        }

        .edit-cell-btn {
            background: none;
            border: none;
            color: #6b7280;
            font-size: 11px;
            cursor: pointer;
            padding: 4px 8px;
            display: flex;
            align-items: center;
            gap: 4px;
            border-radius: 4px;
            transition: all 0.2s ease;
            margin-top: 4px;
        }

        .edit-cell-btn:hover {
            background: #e5e7eb;
            color: #1a3a6b;
        }

        .edit-cell-btn svg {
            width: 14px;
            height: 14px;
        }

        /* Ligne de pause */
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

        /* Modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            animation: fadeIn 0.3s ease;
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease;
        }

        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-header h3 {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            color: #6b7280;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .modal-close:hover {
            background: #f3f4f6;
            color: #1f2937;
        }

        .modal-body {
            padding: 24px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 4px;
        }

        .modal-select {
            width: 100%;
            padding: 8px 12px;
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            font-size: 13px;
            color: #1f2937;
            background: white;
            transition: all 0.2s ease;
        }

        .modal-select:focus {
            outline: none;
            border-color: #1a3a6b;
            box-shadow: 0 0 0 3px rgba(26, 58, 107, 0.08);
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        .btn-secondary {
            padding: 8px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: white;
            color: #4b5563;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-secondary:hover {
            background: #f3f4f6;
        }

        .btn-primary {
            padding: 8px 20px;
            border: none;
            border-radius: 8px;
            background: #1a3a6b;
            color: white;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background: #1e4d8a;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(26, 58, 107, 0.2);
        }

        .btn-danger {
            padding: 8px 16px;
            border: 1px solid #ef4444;
            border-radius: 8px;
            background: white;
            color: #ef4444;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-right: auto;
        }

        .btn-danger:hover {
            background: #fee2e2;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .schedule-time-editor {
                flex-direction: column;
                gap: 2px;
            }

            .schedule-time-editor input {
                width: 100%;
                max-width: 100px;
            }

            .schedule-table tbody>tr>th {
                width: 120px;
                min-width: 120px;
                max-width: 120px;
                padding: 8px 4px;
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
            }

            .schedule-table {
                min-width: 1000px;
            }

            .modal-content {
                width: 95%;
                max-width: 100%;
                margin: 10px;
            }

            .schedule-scroll-container::-webkit-scrollbar {
                height: 8px;
                width: 8px;
            }
        }

        .custom-shadow {
            box-shadow: 0 4px 12px rgba(55,48,163,.04);
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            border: 0;
        }

        /* Hover sur les lignes */
        .schedule-table tbody tr:not(.break-row):hover td {
            background: #fafcff;
        }

        .schedule-table tbody tr:not(.break-row):hover th {
            background: #f1f5f9;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Variables globales
        let currentCellKey = null;
        let currentDay = null;
        let currentSlot = null;
        let isEditing = false;

        // Gestion des clics sur les cellules vides
        document.querySelectorAll('.add-cell-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const key = this.dataset.key;
                const day = this.dataset.day;
                const slot = this.dataset.slot;
                
                openModal(key, day, slot, false);
            });
        });

        // Gestion des clics sur les boutons de modification
        document.querySelectorAll('.edit-cell-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const key = this.dataset.key;
                const day = this.dataset.day;
                const slot = this.dataset.slot;
                
                // Récupérer les données existantes
                const cell = this.closest('td');
                
                // Trouver les IDs correspondants
                const classId = cell.querySelector('input[name*="[classe_id]"]')?.value || '';
                const matiereId = cell.querySelector('input[name*="[matiere_id]"]')?.value || '';
                const serieId = cell.querySelector('input[name*="[serie_id]"]')?.value || '';
                
                openModal(key, day, slot, true, classId, matiereId, serieId);
            });
        });

        // Fonction pour ouvrir le modal
        function openModal(key, day, slot, editMode = false, classId = '', matiereId = '', serieId = '') {
            currentCellKey = key;
            currentDay = day;
            currentSlot = slot;
            isEditing = editMode;

            const modal = document.getElementById('cellModal');
            const title = document.getElementById('modalTitle');
            const deleteBtn = document.getElementById('deleteCellBtn');
            
            title.textContent = editMode ? 'Modifier le cours' : 'Ajouter un cours';
            
            if (deleteBtn) {
                deleteBtn.style.display = editMode ? 'inline-block' : 'none';
            }

            // Remplir les champs
            document.getElementById('modalClasse').value = classId;
            document.getElementById('modalMatiere').value = matiereId;
            document.getElementById('modalSerie').value = serieId;

            modal.style.display = 'flex';
        }

        // Fonction pour fermer le modal
        function closeModal() {
            document.getElementById('cellModal').style.display = 'none';
            currentCellKey = null;
            currentDay = null;
            currentSlot = null;
            isEditing = false;
        }

        // Sauvegarder la cellule
        document.getElementById('saveCellBtn').addEventListener('click', function() {
            const classId = document.getElementById('modalClasse').value;
            const matiereId = document.getElementById('modalMatiere').value;
            const serieId = document.getElementById('modalSerie').value;

            if (!classId && !matiereId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Information',
                    text: 'Veuillez sélectionner au moins une classe ou une matière'
                });
                return;
            }

            // Mettre à jour la cellule
            const cell = document.querySelector(`td[data-day="${currentDay}"][data-slot="${currentSlot}"]`);
            
            if (cell) {
                // Créer le contenu de la cellule
                const classText = classId ? document.querySelector(`#modalClasse option[value="${classId}"]`)?.textContent || '—' : '—';
                const matiereText = matiereId ? document.querySelector(`#modalMatiere option[value="${matiereId}"]`)?.textContent || '—' : '—';
                const serieText = serieId ? document.querySelector(`#modalSerie option[value="${serieId}"]`)?.textContent || '' : '';

                let html = `
                    <div class="cell-content">
                        <div class="cell-info">
                            <span class="cell-class-display">${classText}</span>
                            <span class="cell-subject-display">${matiereText}</span>
                            ${serieText ? `<span class="cell-serie-display">${serieText}</span>` : ''}
                        </div>
                        <button type="button" class="edit-cell-btn" data-key="${currentCellKey}" data-day="${currentDay}" data-slot="${currentSlot}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                            Modifier
                        </button>
                    </div>
                `;

                // Ajouter les champs cachés
                html += `
                    <input type="hidden" name="cells[${currentCellKey}][classe_id]" value="${classId}">
                    <input type="hidden" name="cells[${currentCellKey}][matiere_id]" value="${matiereId}">
                    <input type="hidden" name="cells[${currentCellKey}][serie_id]" value="${serieId}">
                `;

                cell.innerHTML = html;
                cell.className = 'has-data';
                
                // Réattacher l'événement au nouveau bouton modifier
                const newEditBtn = cell.querySelector('.edit-cell-btn');
                if (newEditBtn) {
                    newEditBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const key = this.dataset.key;
                        const day = this.dataset.day;
                        const slot = this.dataset.slot;
                        
                        const cellData = this.closest('td');
                        
                        // Récupérer les IDs depuis les champs cachés
                        const classIdInput = cellData.querySelector('input[name*="[classe_id]"]');
                        const matiereIdInput = cellData.querySelector('input[name*="[matiere_id]"]');
                        const serieIdInput = cellData.querySelector('input[name*="[serie_id]"]');
                        
                        openModal(
                            key, day, slot, true,
                            classIdInput ? classIdInput.value : '',
                            matiereIdInput ? matiereIdInput.value : '',
                            serieIdInput ? serieIdInput.value : ''
                        );
                    });
                }
            }

            closeModal();
            
            Swal.fire({
                icon: 'success',
                title: 'Succès',
                text: 'Le cours a été ajouté avec succès',
                timer: 1500,
                showConfirmButton: false
            });
        });

        // Supprimer la cellule
        document.getElementById('deleteCellBtn')?.addEventListener('click', function() {
            Swal.fire({
                title: 'Supprimer le cours',
                text: 'Êtes-vous sûr de vouloir supprimer ce cours ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    const cell = document.querySelector(`td[data-day="${currentDay}"][data-slot="${currentSlot}"]`);
                    if (cell) {
                        // Réinitialiser la cellule
                        cell.innerHTML = `
                            <button type="button" class="add-cell-btn" data-key="${currentCellKey}" data-day="${currentDay}" data-slot="${currentSlot}">
                                <span class="add-dot"></span>
                            </button>
                        `;
                        cell.className = 'empty-cell';
                        
                        // Réattacher l'événement au nouveau bouton ajouter
                        const newAddBtn = cell.querySelector('.add-cell-btn');
                        if (newAddBtn) {
                            newAddBtn.addEventListener('click', function(e) {
                                e.stopPropagation();
                                const key = this.dataset.key;
                                const day = this.dataset.day;
                                const slot = this.dataset.slot;
                                openModal(key, day, slot, false);
                            });
                        }
                        
                        closeModal();
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Supprimé',
                            text: 'Le cours a été supprimé avec succès',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                }
            });
        });

        // Fermer le modal en cliquant à l'extérieur
        document.getElementById('cellModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Fermer le modal avec la touche Echap
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        // Gestion de la soumission du formulaire
        document.getElementById('scheduleForm').addEventListener('submit', async e => {
            e.preventDefault();
            const form = e.currentTarget;

            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: new FormData(form)
            });

            const data = await response.json().catch(() => ({}));

            if (response.ok) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Succès',
                    text: data.message || 'Emploi du temps enregistré avec succès'
                });
                if (data.redirect) {
                    window.location = data.redirect;
                }
            } else {
                let errorMessage = 'Veuillez vérifier la grille.';
                if (data.errors) {
                    errorMessage = Object.values(data.errors).flat().join('\n');
                } else if (data.message) {
                    errorMessage = data.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: errorMessage
                });
            }
        });
    </script>
@endpush