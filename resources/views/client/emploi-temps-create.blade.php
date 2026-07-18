@extends('client.layouts.app')

@section('title', $editing ? 'Modifier un emploi du temps' : 'Créer un emploi du temps')

@section('content')
    <!-- En-tête -->
    <div class="mb-6 flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h2 class="font-headline-lg text-headline-lg text-primary">
                {{ $editing ? 'Modifier' : 'Créer' }} l’emploi du temps
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
                <p><b>Années d’enseignement :</b> {{ $enseignant->nombre_annees_enseignement ?? '—' }}</p>
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
        <div class="bg-white rounded-xl border custom-shadow overflow-x-auto">
            <table class="schedule-table w-full min-w-[1050px] border-collapse">
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
                            <!-- Pause -->
                            <tr class="break-row">
                                <th colspan="6">{{ $slot['break'] }}</th>
                            </tr>
                        @else
                            @php($slotKey = $slot['key'])
                            <tr>
                                <th class="schedule-time-editor">
                                    <label class="sr-only" for="start-{{ $slotKey }}">Début</label>
                                    <input id="start-{{ $slotKey }}" type="time" name="slots[{{ $slotKey }}][heure_debut]" value="{{ $slot['start'] }}" required>
                                    <span>à</span>
                                    <label class="sr-only" for="end-{{ $slotKey }}">Fin</label>
                                    <input id="end-{{ $slotKey }}" type="time" name="slots[{{ $slotKey }}][heure_fin]" value="{{ $slot['end'] }}" required>
                                </th>

                                @foreach($days as $day)
                                    @php
                                        $entry = $grid[$day][$slotKey] ?? null;
                                        $key = $day . '|' . $slotKey;
                                    @endphp
                                    <td>
                                        <!-- Classe -->
                                        <select name="cells[{{ $key }}][classe_id]" class="cell-class">
                                            <option value="">— Libre —</option>
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}" @selected($entry?->classe_id === $class->id)>
                                                    {{ $class->nom }}
                                                </option>
                                            @endforeach
                                        </select>

                                        <!-- Matière -->
                                        <select name="cells[{{ $key }}][matiere_id]" class="cell-subject">
                                            <option value="">Matière</option>
                                            @foreach($subjects as $subject)
                                                <option value="{{ $subject->id }}" @selected($entry?->matiere_id === $subject->id)>
                                                    {{ $subject->nom }}
                                                </option>
                                            @endforeach
                                        </select>

                                        <!-- Série -->
                                        <select name="cells[{{ $key }}][serie_id]" class="cell-serie">
                                            <option value="">Série (facultatif)</option>
                                            @foreach($series as $serie)
                                                <option value="{{ $serie->id }}" @selected($entry?->serie_id === $serie->id)>
                                                    {{ $serie->nom_serie }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                @endforeach
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('client.enseignant') }}" class="px-5 py-2.5 border rounded-lg">Annuler</a>
            <button class="px-6 py-2.5 bg-primary text-white rounded-lg" type="submit">
                Enregistrer l’emploi du temps
            </button>
        </div>
    </form>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.css" rel="stylesheet">
    <style>
        .schedule-table th,
        .schedule-table td {
            border: 1px solid #dbe2ea;
            padding: 8px;
            vertical-align: top;
        }
        .schedule-table thead th {
            background: #173b78;
            color: #fff;
            text-align: center;
            padding: 13px;
        }
        .schedule-table tbody>tr>th {
            background: #f4f7fb;
            width: 135px;
            text-align: center;
        }
        .schedule-time-editor input {
            display: inline-block;
            width: 88px;
            margin: 0;
            text-align: center;
        }
        .schedule-table td {
            width: 18%;
            height: 112px;
        }
        .schedule-table select,
        .schedule-table input {
            display: block;
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 5px;
            padding: 5px;
            font-size: 12px;
            margin-bottom: 5px;
            background: white;
        }
        .schedule-table input {
            margin-bottom: 0;
        }
        .schedule-table .ts-wrapper {
            margin-bottom: 5px;
        }
        .schedule-table .ts-control {
            min-height: 30px;
            padding: 4px 7px;
            font-size: 12px;
        }
        .break-row th {
            background: #d1d5db !important;
            color: #4b5563;
            padding: 9px;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
        .custom-shadow {
            box-shadow: 0 4px 12px rgba(55,48,163,.04);
        }
        @media (max-width: 768px) {
            .schedule-time-editor input { width: 78px; }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Initialisation de Tom Select pour tous les champs de classe
        document.querySelectorAll('.cell-class').forEach(el => {
            new TomSelect(el, {
                create: false,
                allowEmptyOption: true,
                maxOptions: 100,
                placeholder: 'Rechercher une classe…'
            });
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
                    text: data.message
                });
                window.location = data.redirect;
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Conflit ou erreur',
                    text: Object.values(data.errors || {}).flat().join('\n') ||
                          data.message ||
                          'Veuillez vérifier la grille.'
                });
            }
        });
    </script>
@endpush
