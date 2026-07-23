@extends('personnel.layouts.app')
@php($directeur = trim(collect([auth()->user()?->nom, auth()->user()?->prenom])->filter()->implode(' ')) ?: auth()->user()?->name)
@php($isEditing = isset($bulletin) && $bulletin)

@section('title', 'EduManager - Générer un bulletin')

@section('content')
    <div class="container-fluid px-0">
        <div class="row mx-0 mb-4">
            <div class="col-12 px-0">
                <a href="{{ route('personnel.bulletin.index') }}" class="btn btn-outline-primary ms-3 mt-3 d-inline-flex align-items-center gap-2" style="font-size: 1rem; padding: 0.6rem 1.2rem;">
                    <span class="material-symbols-outlined" style="font-size: 1.4rem;">arrow_back</span>
                </a>
            </div>
        </div>

        <div class="row justify-content-center mx-0">
            <div class="col-12 col-lg-10">
                <div class="card edu-bulletin-card">
                    <form method="POST" action="{{ $isEditing ? route('personnel.bulletin.update', $bulletin) : route('personnel.bulletin.store') }}" id="bulletinForm" novalidate>
                        @csrf
                        @if($isEditing) @method('PUT') @endif

                        <div class="edu-bulletin-block p-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-6 gap-3 mb-4">
                                <div style="order: 4">
                                    <label class="edu-label"><span class="material-symbols-outlined edu-icon">person</span> Élève</label>
                                    <select class="edu-input edu-select @error('eleve_id') edu-error-border @enderror" name="eleve_id" id="eleve_id" required disabled>
                                        <option value="">-- Choisir niveau, classe et série --</option>
                                        @foreach($eleves as $eleve)
                                            <option value="{{ $eleve->id }}"
                                                    data-matricule="{{ $eleve->matricule }}"
                                                    data-classe="{{ $eleve->classe?->nom ?? '' }}"
                                                    data-niveau="{{ $eleve->classe?->niveau?->nom ?? '' }}"
                                                    data-serie="{{ $eleve->serie?->nom_serie ?? '' }}"
                                                    data-niveau-id="{{ $eleve->niveau_id ?? $eleve->classe?->niveau_id }}"
                                                    data-classe-id="{{ $eleve->classe_id }}"
                                                    data-serie-id="{{ $eleve->id_serie }}">
                                                {{ $eleve->nom }} {{ $eleve->prenom }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('eleve_id')<p class="edu-error">{{ $message }}</p>@enderror
                                </div>
                                <div style="order: 2">
                                    <label class="edu-label"><span class="material-symbols-outlined edu-icon">class</span> Classe</label>
                                    <select class="edu-input edu-select" id="classe_select" disabled>
                                        <option value="">-- Choisir un niveau --</option>
                                        @foreach($classes as $classe)
                                            <option value="{{ $classe->id }}" data-niveau-id="{{ $classe->niveau_id }}">{{ $classe->nom }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div style="order: 1">
                                    <label class="edu-label"><span class="material-symbols-outlined edu-icon">leaderboard</span> Niveau</label>
                                    <select class="edu-input edu-select @error('niveau_id') edu-error-border @enderror" name="niveau_id" id="niveau_id">
                                        <option value="">-- Sélectionner un niveau --</option>
                                        @foreach($niveaux ?? [] as $niveau)
                                            <option value="{{ $niveau->id }}" {{ old('niveau_id') == $niveau->id ? 'selected' : '' }}>{{ $niveau->nom }}</option>
                                        @endforeach
                                    </select>
                                    @error('niveau_id')<p class="edu-error">{{ $message }}</p>@enderror
                                </div>
                                <div style="order: 3">
                                    <label class="edu-label"><span class="material-symbols-outlined edu-icon">category</span> Série</label>
                                    <select class="edu-input edu-select @error('serie_id') edu-error-border @enderror" id="serie_select" disabled>
                                        <option value="">-- Sélectionner --</option>
                                        @foreach($series ?? [] as $serie)
                                            <option value="{{ $serie->id }}" data-classe-ids="{{ $serie->classes->pluck('id')->implode(',') }}" {{ old('serie_id') == $serie->id ? 'selected' : '' }}>{{ $serie->nom_serie }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="serie_id" id="serie_id" value="{{ old('serie_id') }}">
                                    @error('serie_id')<p class="edu-error">{{ $message }}</p>@enderror
                                </div>
                                <div style="order: 5">
                                    <label class="edu-label"><span class="material-symbols-outlined edu-icon">event_note</span> Année</label>
                                    <select class="edu-input edu-select @error('annee_academique_id') edu-error-border @enderror" name="annee_academique_id" id="annee_academique_id" required>
                                        <option value="">-- Sélectionner --</option>
                                        @foreach($anneesAcademiques as $annee)
                                            <option value="{{ $annee->id }}" {{ old('annee_academique_id', $bulletin?->annee_academique_id) == $annee->id ? 'selected' : '' }}>{{ $annee->libelle }}</option>
                                        @endforeach
                                    </select>
                                    @error('annee_academique_id')<p class="edu-error">{{ $message }}</p>@enderror
                                </div>
                                <div style="order: 6">
                                    <label class="edu-label"><span class="material-symbols-outlined edu-icon">calendar_month</span> Période</label>
                                    <select class="edu-input edu-select @error('trimestre') edu-error-border @enderror" name="trimestre" id="trimestre" required>
                                        @foreach(['t1' => 'T1', 't2' => 'T2', 't3' => 'T3', 's1' => 'S1', 's2' => 'S2', 'an' => 'Annuel'] as $value => $label)
                                            <option value="{{ $value }}" {{ old('trimestre', $bulletin?->trimestre) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('trimestre')<p class="edu-error">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <input type="hidden" name="etablissement_id" value="{{ $etablissement?->id }}" id="etablissement_id">
                            <input type="hidden" name="classe_id" value="{{ old('classe_id', $classeInitial?->id ?? '') }}" id="classe_id">

                            <div class="edu-bulletin-header p-3 mb-4">
                                <div class="flex items-center gap-4">
                                    <div class="flex-shrink-0">
                                        <div class="edu-logo" style="width: 50px; height: 50px;">
                                            <img id="logo_etablissement" src="{{ $etablissement?->logo_url ?? asset('images/default-school.png') }}" alt="Logo" style="width: 60px; height: 60px;" />
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-base font-bold text-on-surface truncate">{{ $etablissement?->nom ?? 'Établissement' }}</h3>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <div class="text-sm font-semibold text-on-surface">Côte d'Ivoire</div>
                                        <div class="text-sm font-medium text-primary" id="annee_scolaire">--</div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-5 gap-2 mt-3 pt-3 border-t border-gray-200">
                                    <div class="edu-student-chip" style="padding: 0.5rem 0.7rem; font-size: 0.8rem;">
                                        <span class="edu-chip-title">Élève</span><span class="edu-chip-value" id="nom_prenoms">--</span>
                                    </div>
                                    <div class="edu-student-chip" style="padding: 0.5rem 0.7rem; font-size: 0.8rem;">
                                        <span class="edu-chip-title">Mat.</span><span class="edu-chip-value" id="matricule">--</span>
                                    </div>
                                    <div class="edu-student-chip" style="padding: 0.5rem 0.7rem; font-size: 0.8rem;">
                                        <span class="edu-chip-title">Classe</span><span class="edu-chip-value" id="classe">--</span>
                                    </div>
                                    <div class="edu-student-chip" style="padding: 0.5rem 0.7rem; font-size: 0.8rem;">
                                        <span class="edu-chip-title">Niveau</span><span class="edu-chip-value" id="niveau_display">--</span>
                                    </div>
                                    <div class="edu-student-chip" style="padding: 0.5rem 0.7rem; font-size: 0.8rem;">
                                        <span class="edu-chip-title">Série</span><span class="edu-chip-value" id="serie_display">--</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="edu-section-title" style="font-size: 1rem;">
                                        <span class="material-symbols-outlined">library_books</span> Disciplines
                                    </h4>
                                </div>
                                @error('disciplines')<p class="edu-error">{{ $message }}</p>@enderror
                                <div class="overflow-x-auto rounded-lg border border-gray-200">
                                    <table class="w-full text-sm" id="disciplinesTable">
                                        <thead>
                                            <tr class="bg-gray-50 border-b border-gray-200">
                                                <th class="px-3 py-2 text-left font-semibold text-on-surface-variant text-xs uppercase tracking-wider">Discipline</th>
                                                <th class="px-3 py-2 text-left font-semibold text-on-surface-variant text-xs uppercase tracking-wider">Moyenne</th>
                                                <th class="px-3 py-2 text-left font-semibold text-on-surface-variant text-xs uppercase tracking-wider">Coef.</th>
                                                <th class="px-3 py-2 text-left font-semibold text-on-surface-variant text-xs uppercase tracking-wider">M×C</th>
                                                <th class="px-3 py-2 text-left font-semibold text-on-surface-variant text-xs uppercase tracking-wider">Rang</th>
                                                <th class="px-3 py-2 text-left font-semibold text-on-surface-variant text-xs uppercase tracking-wider">Mention</th>
                                                <th class="px-3 py-2 text-left font-semibold text-on-surface-variant text-xs uppercase tracking-wider">Professeur</th>
                                                <th class="px-3 py-2 text-left font-semibold text-on-surface-variant text-xs uppercase tracking-wider">Signature</th>
                                            </tr>
                                        </thead>
                                        <tbody id="disciplinesBody" class="divide-y divide-gray-100"></tbody>
                                    </table>
                                </div>
                                <p class="text-on-surface-variant text-sm mt-2 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-base">info</span>
                                    Total coefficients : <strong id="totalCoefficientsDisplay">0</strong>
                                    — Total points : <strong id="totalPointsDisplay">0.00</strong>
                                </p>
                            </div>

                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="edu-label" style="font-size: 0.85rem;">
                                            <span class="material-symbols-outlined edu-icon">schedule</span> Heures
                                        </label>
                                        <input type="number" step="0.01" name="total_heures" class="edu-input" style="font-size: 0.85rem; padding: 0.5rem;" value="{{ old('total_heures') }}" placeholder="0">
                                    </div>
                                    <div>
                                        <label class="edu-label" style="font-size: 0.85rem;">
                                            <span class="material-symbols-outlined edu-icon">event_busy</span> Abs.
                                        </label>
                                        <input type="number" name="absences" class="edu-input" style="font-size: 0.85rem; padding: 0.5rem;" value="{{ old('absences') }}" placeholder="0">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="edu-label" style="font-size: 0.85rem;">
                                            <span class="material-symbols-outlined edu-icon">military_tech</span> Rang
                                        </label>
                                        <input type="number" name="rang" class="edu-input" style="font-size: 0.85rem; padding: 0.5rem;" value="{{ old('rang') }}" placeholder="0">
                                    </div>
                                    <div>
                                        <label class="edu-label" style="font-size: 0.85rem;">
                                            <span class="material-symbols-outlined edu-icon">calculate</span> Moy.
                                        </label>
                                        <input type="text" class="edu-input edu-disabled" id="moyenne_generale_display" value="--" disabled style="font-size: 0.85rem; padding: 0.5rem;">
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <div>
                                    <label class="edu-label" style="font-size: 0.85rem;">
                                        <span class="material-symbols-outlined edu-icon">workspace_premium</span> Mention
                                    </label>
                                    <input type="text" id="mention" class="edu-input edu-disabled" value="--" readonly style="font-size: 0.85rem; padding: 0.5rem;">
                                </div>
                                <div>
                                    <label class="edu-label" style="font-size: 0.85rem;">
                                        <span class="material-symbols-outlined edu-icon">gavel</span> Décision
                                    </label>
                                    <input type="text" id="decision" class="edu-input edu-disabled" value="--" readonly style="font-size: 0.85rem; padding: 0.5rem;">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <div>
                                    <label class="edu-label" style="font-size: 0.85rem;">
                                        <span class="material-symbols-outlined edu-icon">comment</span> Observation
                                    </label>
                                    <textarea id="observation_conseil" class="edu-input edu-disabled" rows="3" readonly style="font-size: 0.85rem; padding: 0.5rem;">--</textarea>
                                </div>
                                <div>
                                    <label class="edu-label" style="font-size: 0.85rem;">
                                        <span class="material-symbols-outlined edu-icon">calendar_today</span> Date
                                    </label>
                                    <input type="text" id="date_bulletin" class="edu-input edu-disabled" style="font-size: 0.85rem; padding: 0.5rem;" value="{{ now()->format('d/m/Y') }}" readonly>
                                </div>
                            </div>

                            <div class="edu-distinction" style="padding: 1rem;">
                                <h5 class="edu-section-title" style="font-size: 1rem;">
                                    <span class="material-symbols-outlined">stars</span> Distinctions
                                </h5>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    @foreach(['honneur' => 'Honneur', 'encouragement' => 'Encouragement', 'felicitations' => 'Félicitations', 'avertissement' => 'Avertissement', 'blame' => 'Blâme'] as $value => $label)
                                        <label class="edu-checkbox" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                                            <input type="checkbox" name="distinctions[]" value="{{ $value }}" class="w-4 h-4 rounded border-gray-300 text-primary" {{ in_array($value, old('distinctions', [])) ? 'checked' : '' }}>
                                            <span class="font-medium">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <div>
                                    <label class="edu-label" style="font-size: 0.85rem;">
                                        <span class="material-symbols-outlined edu-icon">edit_note</span> Professeur Principal
                                    </label>
                                    <input type="text" name="signature_professeur_principal" class="edu-input" style="font-size: 0.85rem; padding: 0.5rem;" value="{{ old('signature_professeur_principal') }}" placeholder="Nom">
                                </div>
                                <div>
                                    <label class="edu-label" style="font-size: 0.85rem;">
                                        <span class="material-symbols-outlined edu-icon">edit_note</span> Directeur
                                    </label>
                                    <input type="text" id="signature_directeur" class="edu-input edu-disabled" style="font-size: 0.85rem; padding: 0.5rem;" value="{{ $directeur }}" readonly>
                                </div>
                            </div>

                            <div class="flex justify-end pt-3 border-t border-gray-200">
                                <button type="submit" class="edu-submit-btn" style="font-size: 0.9rem; padding: 0.7rem 1.2rem;">
                                    <span class="material-symbols-outlined">save</span>
                                    Enregistrer le bulletin
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        :root { --edu-primary: #1f108e; }
        .edu-bulletin-card { background: #fff; border-radius: 16px; box-shadow: 0 10px 30px rgba(31, 16, 142, 0.06); border: 1px solid rgba(31, 16, 142, 0.08); }
        .edu-bulletin-block { background: rgba(255,255,255,1); }
        .edu-label { display:flex; align-items:center; gap:.3rem; font-size:0.85rem; color: rgba(0,0,0,0.58); margin-bottom:.4rem; font-weight:600; }
        .edu-icon { font-size:1.2rem; color: var(--edu-primary); }
        .edu-input { width: 100%; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: .5rem .6rem; font-size: 0.85rem; outline: none; transition: border-color .15s ease, box-shadow .15s ease; }
        .edu-input:focus { border-color: var(--edu-primary); box-shadow: 0 0 0 3px rgba(31,16,142,.12); }
        .edu-select { padding: .5rem .6rem; }
        .edu-error { color: #ef4444; font-size: 0.8rem; margin-top: .25rem; }
        .edu-error-border { border-color: #ef4444 !important; }
        .edu-disabled { background: #f3f4f6; color: var(--edu-primary); font-weight:700; }
        .edu-logo { width: 50px; height: 50px; border-radius: 12px; background: #fff; border:1px solid #e5e7eb; display:flex; align-items:center; justify-content:center; overflow:hidden; }
        .edu-logo img { width: 36px; height: 36px; object-fit: contain; }
        .edu-bulletin-header { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: .75rem; }
        .edu-student-chip { display:flex; justify-content: space-between; align-items:center; gap:.4rem; padding: .5rem .7rem; background: #fff; border: 1px solid #f3f4f6; border-radius: 10px; font-size:0.8rem; }
        .edu-chip-title { color: rgba(0,0,0,0.56); font-weight:700; }
        .edu-chip-value { font-weight:700; }
        .edu-section-title { font-size: 1rem; font-weight: 800; color: rgba(0,0,0,0.66); display:flex; align-items:center; gap:.4rem; }
        .edu-distinction { background: linear-gradient(135deg, rgba(31,16,142,0.05), rgba(99,102,241,0.05)); border:1px solid rgba(31,16,142,0.12); border-radius: 12px; padding: 1rem; margin-bottom: 1rem; }
        .edu-checkbox { display:flex; align-items:center; gap:.3rem; padding: .4rem .8rem; background:#fff; border:1px solid #e5e7eb; border-radius: 10px; cursor:pointer; transition: all .15s ease; font-size:0.85rem; }
        .edu-checkbox:hover { border-color: rgba(31,16,142,0.35); }
        .edu-submit-btn { background: linear-gradient(90deg, var(--edu-primary), rgba(31,16,142,.88)); color: #fff; border: none; border-radius: 12px; font-size: 0.9rem; font-weight: 800; padding: .7rem 1.2rem; display:flex; align-items:center; gap:.4rem; cursor:pointer; transition: transform .15s ease, box-shadow .15s ease; }
        .edu-submit-btn:hover { box-shadow: 0 14px 25px rgba(31,16,142,.22); transform: scale(1.02); }
        #disciplinesTable { min-width: 1000px; width: 100%; border-collapse: separate; border-spacing: 0; }
        #disciplinesTable th, #disciplinesTable td { padding: 0.6rem 0.6rem; vertical-align: middle; }
        #disciplinesTable .discipline-input { min-width: 190px; width: 100%; font-size: 0.95rem; padding: 0.5rem 0.7rem; font-weight: 600; background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 8px; }
        #disciplinesTable .moyenne-input { min-width: 75px; width: 100%; font-size: 1rem; padding: 0.5rem 0.4rem; font-weight: 700; text-align: center; background: #f0fdf4; border: 2px solid #bbf7d0; border-radius: 8px; }
        #disciplinesTable .coef-input { min-width: 70px; width: 100%; font-size: 0.95rem; padding: 0.5rem 0.4rem; text-align: center; font-weight: 600; background: #fef3c7; border: 2px solid #fcd34d; border-radius: 8px; }
        #disciplinesTable .mc-output { min-width: 70px; width: 100%; font-size: 0.95rem; padding: 0.5rem 0.4rem; text-align: center; font-weight: 800; color: var(--edu-primary); background: rgba(31, 16, 142, 0.08); border: 2px solid rgba(31, 16, 142, 0.15); border-radius: 8px; }
        #disciplinesTable .rang-input { min-width: 60px; width: 100%; font-size: 0.9rem; padding: 0.5rem 0.3rem; text-align: center; background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 8px; }
        #disciplinesTable thead th { padding: 0.7rem 0.6rem; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap; background: #f1f5f9; border-bottom: 2px solid #cbd5e1; color: #334155; font-weight: 700; }
        #moyenne_generale_display { font-size: 1.1rem !important; font-weight: 800 !important; text-align: center; }
        @media (max-width: 1200px) { .grid-cols-5 { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 768px) { .grid-cols-5 { grid-template-columns: 1fr 1fr; } }
    </style>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const disciplinesBody = document.getElementById('disciplinesBody');
            const moyenneDisplay = document.getElementById('moyenne_generale_display');
            const totalCoefficientsDisplay = document.getElementById('totalCoefficientsDisplay');
            const totalPointsDisplay = document.getElementById('totalPointsDisplay');

            let idx = 0;
            const qs = (sel, root=document) => root.querySelector(sel);

            function computeMC(tr) {
                const moyenne = parseFloat(qs('.moyenne-input', tr)?.value);
                const coef = parseFloat(qs('.coef-input', tr)?.value);
                const out = qs('.mc-output', tr);
                const ok = Number.isFinite(moyenne) && Number.isFinite(coef) && coef > 0;
                if (!ok) { out.value = ''; return null; }
                const v = moyenne * coef;
                out.value = v.toFixed(2);
                return v;
            }

            function recomputeTotals() {
                let totalCoef = 0, totalPoints = 0;
                disciplinesBody.querySelectorAll('tr[data-row]').forEach(tr => {
                    const moyenne = parseFloat(qs('.moyenne-input', tr)?.value);
                    const coef = parseFloat(qs('.coef-input', tr)?.value);
                    if (Number.isFinite(moyenne) && Number.isFinite(coef) && coef > 0) {
                        totalCoef += coef;
                        totalPoints += (moyenne * coef);
                    }
                });
                if (totalCoef <= 0) {
                    totalCoefficientsDisplay.textContent = '0';
                    totalPointsDisplay.textContent = '0.00';
                    moyenneDisplay.value = '--';
                    return;
                }
                const moyenneGenerale = totalPoints / totalCoef;
                totalCoefficientsDisplay.textContent = totalCoef.toFixed(2);
                totalPointsDisplay.textContent = totalPoints.toFixed(2);
                moyenneDisplay.value = moyenneGenerale.toFixed(2);
                updateAutomaticFields(moyenneGenerale);
            }

            function updateAutomaticFields(moyenne) {
                const mention = document.getElementById('mention');
                const decision = document.getElementById('decision');
                const observation = document.getElementById('observation_conseil');
                if (!Number.isFinite(moyenne)) return;
                const levels = [[19, 'Excellent'], [18, 'Excellent'], [17, 'Très Bien'], [16, 'Très Bien'], [15, 'Bien'], [14, 'Assez Bien'], [13, 'Assez Bien'], [12, 'Passable'], [11, 'Passable'], [10, 'Passable'], [9, 'Insuffisant'], [8, 'Faible'], [7, 'Très Faible'], [5, 'Très Faible'], [0, 'Très Insuffisant']];
                const level = levels.find(([minimum]) => moyenne >= minimum);
                if (mention) mention.value = level[1];
                if (decision) decision.value = moyenne >= 10 ? 'Admis(e)' : 'Refusé(e)';
                if (observation) observation.value = 'Observation du conseil de classe.';
            }

            function buildRow(rowData={}) {
                const rowIndex = idx++;
                const tr = document.createElement('tr');
                tr.setAttribute('data-row', rowIndex);
                const disciplineName = rowData.discipline ?? '';
                tr.innerHTML = `
                    <td>
                        <input type="hidden" name="disciplines[${rowIndex}][matiere_id]" value="${rowData.matiere_id ?? ''}" />
                        <input type="text" class="discipline-input font-medium" required name="disciplines[${rowIndex}][discipline]" value="${escapeHtml(disciplineName)}" readonly placeholder="Discipline..." />
                    </td>
                    <td><input type="number" step="0.01" min="0" max="20" class="moyenne-input text-center" name="disciplines[${rowIndex}][moyenne]" value="${rowData.moyenne ?? ''}" placeholder="0.00" /></td>
                    <td><input type="number" step="1" min="1" max="100" class="coef-input text-center font-semibold" name="disciplines[${rowIndex}][coefficient]" value="${rowData.coefficient ?? 1}" readonly required /></td>
                    <td><input type="text" class="mc-output text-center" name="disciplines[${rowIndex}][moyenne_coefficient]" value="" disabled /></td>
                    <td><input type="number" min="0" class="rang-input text-center" name="disciplines[${rowIndex}][rang]" value="${rowData.rang ?? ''}" placeholder="0" /></td>
                    <td><input type="text" class="edu-disabled" value="${escapeHtml(rowData.mention ?? '')}" readonly /></td>
                    <td><input type="text" class="prof-input" name="disciplines[${rowIndex}][professeur]" value="${escapeHtml(rowData.professeur ?? '')}" placeholder="Professeur" /></td>
                    <td><input type="text" class="sig-input" name="disciplines[${rowIndex}][signature]" value="${escapeHtml(rowData.signature ?? '')}" placeholder="Signature" /></td>
                `;
                const moyenneInput = qs('.moyenne-input', tr);
                const coefInput = qs('.coef-input', tr);
                moyenneInput.addEventListener('input', () => { computeMC(tr); recomputeTotals(); });
                coefInput.addEventListener('input', () => { computeMC(tr); recomputeTotals(); });
                computeMC(tr);
                return tr;
            }

            function escapeHtml(str) { return String(str ?? '').replace(/[&<>\"']/g, s => ({'&':'&amp;','<':'<','>':'>','\"':'"','\'':'&#039;'}[s])); }

            function fillDisciplinesTable(disciplines = []) {
                disciplinesBody.innerHTML = '';
                idx = 0;
                if (!Array.isArray(disciplines) || disciplines.length === 0) { recomputeTotals(); return; }
                disciplines.forEach((d) => { disciplinesBody.appendChild(buildRow({ matiere_id: d.matiere_id ?? '', discipline: d.discipline ?? '', moyenne: d.moyenne ?? '', coefficient: d.coefficient ?? 1, rang: d.rang ?? '', mention: d.mention ?? '', professeur: d.professeur ?? '', signature: d.signature ?? '' })); });
                recomputeTotals();
            }

            async function fetchStudentData(eleveId) {
                const anneeId = qs('#annee_academique_id')?.value ?? '';
                const trimestre = qs('#trimestre')?.value ?? '';
                const url = `{{ route('personnel.bulletin.student-data') }}?eleve_id=${encodeURIComponent(eleveId)}&annee_academique_id=${encodeURIComponent(anneeId)}&trimestre=${encodeURIComponent(trimestre)}`;
                const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
                if (!res.ok) throw new Error('Erreur');
                return res.json();
            }

            // Event listeners for filtering logic
            const niveauSelect = qs('#niveau_id');
            const classeSelect = qs('#classe_select');
            const serieSelect = qs('#serie_select');
            const eleveSelect = qs('#eleve_id');

            niveauSelect.addEventListener('change', () => {
                const niveauId = niveauSelect.value;
                classeSelect.value = '';
                classeSelect.disabled = !niveauId;
                filterOptions(classeSelect, option => String(option.dataset.niveauId) === String(niveauId));
            });

            function filterOptions(select, predicate) {
                Array.from(select.options).forEach((option, index) => {
                    if (index === 0) return;
                    const visible = predicate(option);
                    option.hidden = !visible;
                    option.disabled = !visible;
                });
            }

            eleveSelect.addEventListener('change', async (e) => {
                const eleveId = e.target.value;
                if (!eleveId) return;
                try {
                    const data = await fetchStudentData(eleveId);
                    if (data.disciplines) fillDisciplinesTable(data.disciplines);
                    if (data.moyenne_generale != null) {
                        moyenneDisplay.value = Number(data.moyenne_generale).toFixed(2);
                        updateAutomaticFields(Number(data.moyenne_generale));
                    }
                } catch (err) { console.error(err); }
            });

            const initialNiveauId = @json(old('niveau_id', $bulletin?->eleve?->niveau_id));
            const initialClasseId = @json(old('classe_id', $bulletin?->classe_id));
            const initialSerieId = @json(old('serie_id', $bulletin?->eleve?->id_serie));
            const initialEleveId = @json(old('eleve_id', $bulletin?->eleve_id));
            if (initialNiveauId) {
                niveauSelect.value = initialNiveauId;
                niveauSelect.dispatchEvent(new Event('change'));
                if (initialClasseId) {
                    classeSelect.value = initialClasseId;
                    if (initialSerieId) {
                        serieSelect.value = initialSerieId;
                        serieSelect.dispatchEvent(new Event('change'));
                    }
                }
                if (initialEleveId && !eleveSelect.disabled) eleveSelect.value = initialEleveId;
            }
        });
    </script>
    @endpush
@endsection
