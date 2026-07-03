@extends('client.layouts.app')
@php($noSidebar = true)

@section('title', 'EduManager - Générer un bulletin')

@section('content')
    <div class="container-fluid px-0">
        <div class="row mx-0 mb-4">
            <div class="col-12 px-0">
                <a href="{{ route('client.bulletin.index') }}"
                   class="btn btn-outline-primary ms-3 mt-3 d-inline-flex align-items-center gap-2"
                   style="font-size: 1rem; padding: 0.6rem 1.2rem;">
                    <span class="material-symbols-outlined" style="font-size: 1.4rem;">arrow_back</span>
                </a>
            </div>
        </div>

        <div class="row justify-content-center mx-0">
            <div class="col-12 col-lg-10">
                <div class="card edu-bulletin-card">
                    <form method="POST" action="{{ route('client.bulletin.store') }}" id="bulletinForm" novalidate>
                        @csrf

                        <div class="edu-bulletin-block p-4">
                            {{-- Sélection --}}
                            <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-6 gap-3 mb-4">
                                <div style="order: 4">
                                    <label class="edu-label">
                                        <span class="material-symbols-outlined edu-icon">person</span>
                                        Élève
                                    </label>
                                    <select class="edu-input edu-select @error('eleve_id') edu-error-border @enderror"
                                            name="eleve_id" id="eleve_id" required disabled>
                                        <option value="">-- Choisir niveau, classe et série --</option>
                                        @foreach($eleves as $eleve)
                                            <option value="{{ $eleve->id }}"
                                                    data-matricule="{{ $eleve->matricule }}"
                                                    data-classe="{{ $eleve->classe?->nom ?? '' }}"
                                                    data-niveau="{{ $eleve->classe?->niveau?->nom ?? '' }}"
                                                    data-serie="{{ $eleve->serie?->nom_serie ?? '' }}"
                                                    data-niveau-id="{{ $eleve->niveau_id ?? $eleve->classe?->niveau_id }}"
                                                    data-classe-id="{{ $eleve->classe_id }}"
                                                    data-serie-id="{{ $eleve->id_serie }}"
                                                    data-logo="{{ $etablissement?->logo ? $etablissement?->getLogoUrlAttribute() : '' }}">
                                                {{ $eleve->nom }} {{ $eleve->prenom }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('eleve_id')
                                        <p class="edu-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div style="order: 2">
                                    <label class="edu-label">
                                        <span class="material-symbols-outlined edu-icon">class</span>
                                        Classe
                                    </label>
                                    <select class="edu-input edu-select" id="classe_select" disabled>
                                        <option value="">-- Choisir un niveau --</option>
                                        @foreach($classes as $classe)
                                            <option value="{{ $classe->id }}" data-niveau-id="{{ $classe->niveau_id }}">{{ $classe->nom }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div style="order: 1">
                                    <label class="edu-label">
                                        <span class="material-symbols-outlined edu-icon">leaderboard</span>
                                        Niveau
                                    </label>
                                    <select class="edu-input edu-select @error('niveau_id') edu-error-border @enderror"
                                            name="niveau_id" id="niveau_id">
                                        <option value="">-- Sélectionner un niveau --</option>
                                        @foreach($niveaux ?? [] as $niveau)
                                            <option value="{{ $niveau->id }}" {{ old('niveau_id') == $niveau->id ? 'selected' : '' }}>
                                                {{ $niveau->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('niveau_id')
                                        <p class="edu-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div style="order: 3">
                                    <label class="edu-label">
                                        <span class="material-symbols-outlined edu-icon">category</span>
                                        Série
                                    </label>
                                    <select class="edu-input edu-select @error('serie_id') edu-error-border @enderror"
                                            id="serie_select" disabled>
                                        <option value="">-- Sélectionner --</option>
                                        @foreach($series ?? [] as $serie)
                                            <option value="{{ $serie->id }}" data-classe-ids="{{ $serie->classes->pluck('id')->implode(',') }}" {{ old('serie_id') == $serie->id ? 'selected' : '' }}>
                                                {{ $serie->nom_serie }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="serie_id" id="serie_id" value="{{ old('serie_id') }}">
                                    @error('serie_id')
                                        <p class="edu-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div style="order: 5">
                                    <label class="edu-label">
                                        <span class="material-symbols-outlined edu-icon">event_note</span>
                                        Année
                                    </label>
                                    <select class="edu-input edu-select @error('annee_academique_id') edu-error-border @enderror"
                                            name="annee_academique_id" id="annee_academique_id" required>
                                        <option value="">-- Sélectionner --</option>
                                        @foreach($anneesAcademiques as $annee)
                                            <option value="{{ $annee->id }}" {{ old('annee_academique_id') == $annee->id ? 'selected' : '' }}>
                                                {{ $annee->libelle }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('annee_academique_id')
                                        <p class="edu-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div style="order: 6">
                                    <label class="edu-label">
                                        <span class="material-symbols-outlined edu-icon">calendar_month</span>
                                        Période
                                    </label>
                                    <select class="edu-input edu-select @error('trimestre') edu-error-border @enderror"
                                            name="trimestre" id="trimestre" required>
                                        <option value="t1" {{ old('trimestre') === 't1' ? 'selected' : '' }}>T1</option>
                                        <option value="t2" {{ old('trimestre') === 't2' ? 'selected' : '' }}>T2</option>
                                        <option value="t3" {{ old('trimestre') === 't3' ? 'selected' : '' }}>T3</option>
                                        <option value="s1" {{ old('trimestre') === 's1' ? 'selected' : '' }}>S1</option>
                                        <option value="s2" {{ old('trimestre') === 's2' ? 'selected' : '' }}>S2</option>
                                        <option value="an" {{ old('trimestre') === 'an' ? 'selected' : '' }}>Annuel</option>
                                    </select>
                                    @error('trimestre')
                                        <p class="edu-error">{{ $message }}</p>
                                    @enderror
                                </div>

                            </div>

                            <input type="hidden" name="etablissement_id" value="{{ $etablissement?->id }}" id="etablissement_id">
                            <input type="hidden" name="classe_id" value="{{ old('classe_id', $classeInitial?->id ?? '') }}" id="classe_id">

                            {{-- Header Bulletin --}}
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
                                        <div class="text-xs text-on-surface-variant">Ministère</div>
                                        <div class="text-sm font-medium text-primary" id="annee_scolaire">--</div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-5 gap-2 mt-3 pt-3 border-t border-gray-200">
                                    <div class="edu-student-chip" style="padding: 0.5rem 0.7rem; font-size: 0.8rem;">
                                        <span class="edu-chip-title">Élève</span>
                                        <span class="edu-chip-value" id="nom_prenoms">--</span>
                                    </div>
                                    <div class="edu-student-chip" style="padding: 0.5rem 0.7rem; font-size: 0.8rem;">
                                        <span class="edu-chip-title">Mat.</span>
                                        <span class="edu-chip-value" id="matricule">--</span>
                                    </div>
                                    <div class="edu-student-chip" style="padding: 0.5rem 0.7rem; font-size: 0.8rem;">
                                        <span class="edu-chip-title">Classe</span>
                                        <span class="edu-chip-value" id="classe">--</span>
                                    </div>
                                    <div class="edu-student-chip" style="padding: 0.5rem 0.7rem; font-size: 0.8rem;">
                                        <span class="edu-chip-title">Niveau</span>
                                        <span class="edu-chip-value" id="niveau_display">--</span>
                                    </div>
                                    <div class="edu-student-chip" style="padding: 0.5rem 0.7rem; font-size: 0.8rem;">
                                        <span class="edu-chip-title">Série</span>
                                        <span class="edu-chip-value" id="serie_display">--</span>
                                    </div>
                                    <div class="edu-student-chip" style="padding: 0.5rem 0.7rem; font-size: 0.8rem;">
                                        <span class="edu-chip-title">Eff.</span>
                                        <span class="edu-chip-value" id="effectif">--</span>
                                    </div>
                                    <div class="edu-student-chip" style="padding: 0.5rem 0.7rem; font-size: 0.8rem;">
                                        <span class="edu-chip-title">Sexe</span>
                                        <span class="edu-chip-value" id="sexe">--</span>
                                    </div>
                                    <div class="edu-student-chip" style="padding: 0.5rem 0.7rem; font-size: 0.8rem;">
                                        <span class="edu-chip-title">Nat.</span>
                                        <span class="edu-chip-value" id="nationalite">--</span>
                                    </div>
                                    <div class="edu-student-chip" style="padding: 0.5rem 0.7rem; font-size: 0.8rem;">
                                        <span class="edu-chip-title">Nais.</span>
                                        <span class="edu-chip-value" id="date_naissance">--</span>
                                    </div>
                                    <div class="edu-student-chip" style="padding: 0.5rem 0.7rem; font-size: 0.8rem;">
                                        <span class="edu-chip-title">Lieu</span>
                                        <span class="edu-chip-value truncate max-w-[60px]" id="lieu_naissance">--</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Disciplines --}}
                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="edu-section-title" style="font-size: 1rem;">
                                        <span class="material-symbols-outlined">library_books</span>
                                        Disciplines
                                    </h4>

                                    <button type="button" class="edu-primary-btn hidden" id="addDisciplineBtn" style="font-size: 0.85rem; padding: 0.5rem 0.9rem;">
                                        <span class="material-symbols-outlined">add</span>
                                        Ajouter
                                    </button>
                                </div>

                                @error('disciplines')
                                    <p class="edu-error">{{ $message }}</p>
                                @enderror

                                <div class="overflow-x-auto rounded-lg border border-gray-200">
                                    <table class="w-full text-sm" id="disciplinesTable">
                                        <thead>
                                            <tr class="bg-gray-50 border-b border-gray-200">
                                                <th class="px-3 py-2 text-left font-semibold text-on-surface-variant text-xs uppercase tracking-wider" style="min-width: 200px; width: 22%;">Discipline</th>
                                                <th class="px-3 py-2 text-left font-semibold text-on-surface-variant text-xs uppercase tracking-wider">Interro</th>
                                                <th class="px-3 py-2 text-left font-semibold text-on-surface-variant text-xs uppercase tracking-wider">Devoir</th>
                                                <th class="px-3 py-2 text-left font-semibold text-on-surface-variant text-xs uppercase tracking-wider">Composition</th>
                                                <th class="px-3 py-2 text-left font-semibold text-on-surface-variant text-xs uppercase tracking-wider" style="min-width: 80px; width: 10%;">Moyenne</th>
                                                <th class="px-3 py-2 text-left font-semibold text-on-surface-variant text-xs uppercase tracking-wider" style="min-width: 80px; width: 10%;">Coef.</th>
                                                <th class="px-3 py-2 text-left font-semibold text-on-surface-variant text-xs uppercase tracking-wider" style="min-width: 80px; width: 10%;">M×C</th>
                                                <th class="px-3 py-2 text-left font-semibold text-on-surface-variant text-xs uppercase tracking-wider" style="min-width: 65px; width: 8%;">Rang</th>
                                                <th class="px-3 py-2 text-left font-semibold text-on-surface-variant text-xs uppercase tracking-wider" style="min-width: 160px; width: 16%;">Appréciation</th>
                                                <th class="px-3 py-2 text-left font-semibold text-on-surface-variant text-xs uppercase tracking-wider" style="min-width: 140px; width: 12%;">Professeur</th>
                                                <th class="px-3 py-2 text-left font-semibold text-on-surface-variant text-xs uppercase tracking-wider" style="min-width: 140px; width: 12%;">Signature</th>
                                                <th class="px-3 py-2 text-left font-semibold text-on-surface-variant text-xs uppercase tracking-wider" style="min-width: 50px; width: 5%; text-align: center;"> </th>
                                            </tr>
                                        </thead>
                                        <tbody id="disciplinesBody" class="divide-y divide-gray-100"></tbody>
                                    </table>
                                </div>

                                <p class="text-on-surface-variant text-sm mt-2 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-base">info</span>
                                    Coefficients issus de la série — Total coefficients : <strong id="totalCoefficientsDisplay">0</strong>
                                    — Total points : <strong id="totalPointsDisplay">0.00</strong>
                                </p>
                            </div>

                            {{-- Totaux/infos --}}
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="edu-label" style="font-size: 0.85rem;">
                                            <span class="material-symbols-outlined edu-icon">schedule</span>
                                            Heures
                                        </label>
                                        <input type="number" step="0.01" name="total_heures" class="edu-input" style="font-size: 0.85rem; padding: 0.5rem;"
                                               value="{{ old('total_heures') }}" placeholder="0">
                                        @error('total_heures')<p class="edu-error">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="edu-label" style="font-size: 0.85rem;">
                                            <span class="material-symbols-outlined edu-icon">event_busy</span>
                                            Abs.
                                        </label>
                                        <input type="number" name="absences" class="edu-input" style="font-size: 0.85rem; padding: 0.5rem;" value="{{ old('absences') }}" placeholder="0">
                                        @error('absences')<p class="edu-error">{{ $message }}</p>@enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="edu-label" style="font-size: 0.85rem;">
                                            <span class="material-symbols-outlined edu-icon">military_tech</span>
                                            Rang
                                        </label>
                                        <input type="number" name="rang" class="edu-input" style="font-size: 0.85rem; padding: 0.5rem;" value="{{ old('rang') }}" placeholder="0">
                                        @error('rang')<p class="edu-error">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="edu-label" style="font-size: 0.85rem;">
                                            <span class="material-symbols-outlined edu-icon">calculate</span>
                                            Moy.
                                        </label>
                                        <input type="text" class="edu-input edu-disabled" id="moyenne_generale_display" value="--" disabled style="font-size: 0.85rem; padding: 0.5rem;">
                                    </div>
                                </div>
                            </div>

                            {{-- Résultat / décision / observation / date --}}
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <div>
                                    <label class="edu-label" style="font-size: 0.85rem;">
                                        <span class="material-symbols-outlined edu-icon">assessment</span>
                                        Résultat
                                    </label>
                                    <input type="text" name="resultat_classe" class="edu-input" style="font-size: 0.85rem; padding: 0.5rem;" value="{{ old('resultat_classe') }}" placeholder="Admis">
                                    @error('resultat_classe')<p class="edu-error">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="edu-label" style="font-size: 0.85rem;">
                                        <span class="material-symbols-outlined edu-icon">gavel</span>
                                        Décision
                                    </label>
                                    <input type="text" name="decision" class="edu-input" style="font-size: 0.85rem; padding: 0.5rem;" value="{{ old('decision') }}" placeholder="Admis">
                                    @error('decision')<p class="edu-error">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <div>
                                    <label class="edu-label" style="font-size: 0.85rem;">
                                        <span class="material-symbols-outlined edu-icon">comment</span>
                                        Observation du conseil
                                    </label>
                                    <input type="text" name="observation_conseil" class="edu-input" style="font-size: 0.85rem; padding: 0.5rem;" value="{{ old('observation_conseil') }}" placeholder="Observations...">
                                    @error('observation_conseil')<p class="edu-error">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="edu-label" style="font-size: 0.85rem;">
                                        <span class="material-symbols-outlined edu-icon">calendar_today</span>
                                        Date
                                    </label>
                                    <input type="date" name="date" class="edu-input" style="font-size: 0.85rem; padding: 0.5rem;" value="{{ old('date') }}">
                                    @error('date')<p class="edu-error">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            {{-- Distinctions --}}
                            <div class="edu-distinction" style="padding: 1rem;">
                                <h5 class="edu-section-title" style="font-size: 1rem;">
                                    <span class="material-symbols-outlined">stars</span>
                                    Distinctions
                                </h5>

                                <div class="flex flex-wrap gap-2 mt-2">
                                    @foreach([
                                        'honneur' => 'Honneur',
                                        'encouragement' => 'Encouragement',
                                        'felicitations' => 'Félicitations',
                                        'avertissement' => 'Avertissement',
                                        'blame' => 'Blâme',
                                    ] as $value => $label)
                                        <label class="edu-checkbox" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                                            <input type="checkbox" name="distinctions[]" value="{{ $value }}"
                                                   class="w-4 h-4 rounded border-gray-300 text-primary" {{ in_array($value, old('distinctions', [])) ? 'checked' : '' }}>
                                            <span class="font-medium">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            @error('distinctions')<p class="edu-error">{{ $message }}</p>@enderror

                            {{-- Signatures --}}
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <div>
                                    <label class="edu-label" style="font-size: 0.85rem;">
                                        <span class="material-symbols-outlined edu-icon">edit_note</span>
                                        Professeur Principal
                                    </label>
                                    <input type="text" name="signature_professeur_principal" class="edu-input" style="font-size: 0.85rem; padding: 0.5rem;"
                                           value="{{ old('signature_professeur_principal') }}" placeholder="Nom">
                                    @error('signature_professeur_principal')<p class="edu-error">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label class="edu-label" style="font-size: 0.85rem;">
                                        <span class="material-symbols-outlined edu-icon">edit_note</span>
                                        Directeur
                                    </label>
                                    <input type="text" name="signature_directeur" class="edu-input" style="font-size: 0.85rem; padding: 0.5rem;"
                                           value="{{ old('signature_directeur') }}" placeholder="Nom">
                                    @error('signature_directeur')<p class="edu-error">{{ $message }}</p>@enderror
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

        .edu-input {
            width: 100%;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: .5rem .6rem;
            font-size: 0.85rem;
            outline: none;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .edu-input:focus { border-color: var(--edu-primary); box-shadow: 0 0 0 3px rgba(31,16,142,.12); }
        .edu-select { padding: .5rem .6rem; }
        .edu-error { color: #ef4444; font-size: 0.8rem; margin-top: .25rem; }
        .edu-error-border { border-color: #ef4444 !important; }

        .edu-disabled { background: #f3f4f6; color: var(--edu-primary); font-weight:700; }

        .edu-logo { width: 50px; height: 50px; border-radius: 12px; background: #fff; border:1px solid #e5e7eb; display:flex; align-items:center; justify-content:center; overflow:hidden; }
        .edu-logo img { width: 36px; height: 36px; object-fit: contain; }

        .edu-small { display:flex; flex-wrap:wrap; gap:.5rem 1.5rem; font-size:0.85rem; color: rgba(0,0,0,0.62); }

        .edu-bulletin-header { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: .75rem; }
        .edu-student-chip { display:flex; justify-content: space-between; align-items:center; gap:.4rem; padding: .5rem .7rem; background: #fff; border: 1px solid #f3f4f6; border-radius: 10px; font-size:0.8rem; }
        .edu-chip-title { color: rgba(0,0,0,0.56); font-weight:700; }
        .edu-chip-value { font-weight:700; }

        .edu-section-title { font-size: 1rem; font-weight: 800; color: rgba(0,0,0,0.66); display:flex; align-items:center; gap:.4rem; }

        .edu-primary-btn {
            background: var(--edu-primary);
            color: #fff;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 700;
            padding: .5rem .9rem;
            display:flex;
            align-items:center;
            gap:.3rem;
            border:none;
            cursor:pointer;
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .edu-primary-btn:hover { box-shadow: 0 8px 20px rgba(31,16,142,.18); transform: scale(1.02); }

        .edu-distinction { background: linear-gradient(135deg, rgba(31,16,142,0.05), rgba(99,102,241,0.05)); border:1px solid rgba(31,16,142,0.12); border-radius: 12px; padding: 1rem; margin-bottom: 1rem; }
        .edu-checkbox { display:flex; align-items:center; gap:.3rem; padding: .4rem .8rem; background:#fff; border:1px solid #e5e7eb; border-radius: 10px; cursor:pointer; transition: all .15s ease; font-size:0.85rem; }
        .edu-checkbox:hover { border-color: rgba(31,16,142,0.35); box-shadow: 0 10px 20px rgba(31,16,142,.06); }

        .edu-submit-btn {
            background: linear-gradient(90deg, var(--edu-primary), rgba(31,16,142,.88));
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 800;
            padding: .7rem 1.2rem;
            display:flex;
            align-items:center;
            gap:.4rem;
            cursor:pointer;
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .edu-submit-btn:hover { box-shadow: 0 14px 25px rgba(31,16,142,.22); transform: scale(1.02); }

        /* ===== STYLES OPTIMISÉS POUR LE TABLEAU DES DISCIPLINES ===== */
        #disciplinesTable {
            min-width: 1000px;
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        #disciplinesTable th,
        #disciplinesTable td {
            padding: 0.6rem 0.6rem;
            vertical-align: middle;
        }

        #disciplinesTable .discipline-input {
            min-width: 190px;
            width: 100%;
            font-size: 0.95rem;
            padding: 0.5rem 0.7rem;
            font-weight: 600;
            color: #1a1a2e;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        #disciplinesTable .discipline-input:focus {
            border-color: var(--edu-primary);
            box-shadow: 0 0 0 3px rgba(31, 16, 142, 0.1);
            background: #ffffff;
        }

        #disciplinesTable .moyenne-input {
            min-width: 75px;
            width: 100%;
            font-size: 1rem;
            padding: 0.5rem 0.4rem;
            font-weight: 700;
            text-align: center;
            background: #f0fdf4;
            border: 2px solid #bbf7d0;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        #disciplinesTable .moyenne-input:focus {
            border-color: #059669;
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
            background: #ffffff;
        }
        #disciplinesTable .moyenne-input:hover {
            background: #dcfce7;
            border-color: #86efac;
        }

        #disciplinesTable .coef-input {
            min-width: 70px;
            width: 100%;
            font-size: 0.95rem;
            padding: 0.5rem 0.4rem;
            text-align: center;
            font-weight: 600;
            background: #fef3c7;
            border: 2px solid #fcd34d;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        #disciplinesTable .coef-input:focus {
            border-color: #d97706;
            box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.1);
            background: #ffffff;
        }
        #disciplinesTable .coef-input:hover {
            background: #fde68a;
            border-color: #fbbf24;
        }

        #disciplinesTable .mc-output {
            min-width: 70px;
            width: 100%;
            font-size: 0.95rem;
            padding: 0.5rem 0.4rem;
            text-align: center;
            font-weight: 800;
            color: var(--edu-primary);
            background: rgba(31, 16, 142, 0.08);
            border: 2px solid rgba(31, 16, 142, 0.15);
            border-radius: 8px;
        }

        #disciplinesTable .rang-input {
            min-width: 60px;
            width: 100%;
            font-size: 0.9rem;
            padding: 0.5rem 0.3rem;
            text-align: center;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        #disciplinesTable .rang-input:focus {
            border-color: var(--edu-primary);
            box-shadow: 0 0 0 3px rgba(31, 16, 142, 0.1);
            background: #ffffff;
        }

        #disciplinesTable .app-input {
            min-width: 150px;
            width: 100%;
            font-size: 0.9rem;
            padding: 0.5rem 0.6rem;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        #disciplinesTable .app-input:focus {
            border-color: var(--edu-primary);
            box-shadow: 0 0 0 3px rgba(31, 16, 142, 0.1);
            background: #ffffff;
        }

        #disciplinesTable .prof-input {
            min-width: 130px;
            width: 100%;
            font-size: 0.9rem;
            padding: 0.5rem 0.6rem;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        #disciplinesTable .prof-input:focus {
            border-color: var(--edu-primary);
            box-shadow: 0 0 0 3px rgba(31, 16, 142, 0.1);
            background: #ffffff;
        }

        #disciplinesTable .sig-input {
            min-width: 130px;
            width: 100%;
            font-size: 0.9rem;
            padding: 0.5rem 0.6rem;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        #disciplinesTable .sig-input:focus {
            border-color: var(--edu-primary);
            box-shadow: 0 0 0 3px rgba(31, 16, 142, 0.1);
            background: #ffffff;
        }

        #disciplinesTable td:last-child {
            width: 50px;
            text-align: center;
            padding: 0.3rem 0.2rem;
        }

        #disciplinesTable [data-remove-row] {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: transparent;
            transition: all 0.2s ease;
            border: none;
        }
        #disciplinesTable [data-remove-row]:hover {
            background: #fee2e2;
            transform: scale(1.05);
        }
        #disciplinesTable [data-remove-row] .material-symbols-outlined {
            font-size: 1.3rem;
        }

        #disciplinesTable thead th {
            padding: 0.7rem 0.6rem;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            white-space: nowrap;
            background: #f1f5f9;
            border-bottom: 2px solid #cbd5e1;
            color: #334155;
            font-weight: 700;
        }

        #disciplinesTable thead th:first-child {
            border-top-left-radius: 10px;
        }
        #disciplinesTable thead th:last-child {
            border-top-right-radius: 10px;
        }

        #disciplinesBody tr:nth-child(even) {
            background: #fafbfc;
        }
        #disciplinesBody tr:nth-child(odd) {
            background: #ffffff;
        }
        #disciplinesBody tr:hover {
            background: #eef2ff;
            transition: background 0.2s ease;
        }

        #moyenne_generale_display {
            font-size: 1.1rem !important;
            font-weight: 800 !important;
            text-align: center;
            transition: all 0.3s ease;
            padding: 0.6rem !important;
            background: #f8fafc !important;
            border: 2px solid #e2e8f0 !important;
            border-radius: 10px !important;
        }

        .edu-bulletin-block .overflow-x-auto {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 4px;
        }

        #disciplinesTable input::placeholder {
            color: #94a3b8;
            font-weight: 400;
            opacity: 0.7;
        }

        #disciplinesTable input {
            transition: all 0.2s ease;
        }

        #disciplinesTable {
            border-radius: 10px;
            overflow: hidden;
        }

        /* Ajustement pour les écrans moyens */
        @media (max-width: 1200px) {
            .grid-cols-5 {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            .grid-cols-5 {
                grid-template-columns: 1fr 1fr;
            }
            .edu-bulletin-header .grid-cols-5 {
                grid-template-columns: repeat(3, 1fr);
            }
        }
    </style>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const disciplinesBody = document.getElementById('disciplinesBody');
            const addDisciplineBtn = document.getElementById('addDisciplineBtn');
            const moyenneDisplay = document.getElementById('moyenne_generale_display');
            const totalCoefficientsDisplay = document.getElementById('totalCoefficientsDisplay');
            const totalPointsDisplay = document.getElementById('totalPointsDisplay');

            let idx = 0;
            const defaultDisciplines = [
                'Composition Français',
                'Orthographe-Grammaire',
                'Lecture-Expression Orale',
                'Histoire-Géographie',
                'Anglais',
                'Allemand',
                'Mathématiques',
                'Physique-Chimie',
                'SVT',
                'EDHC',
                'EPS',
                'Arts Plastiques',
                'Conduite'
            ];

            const qs = (sel, root=document) => root.querySelector(sel);

            function computeMC(tr) {
                const moyenne = parseFloat(qs('.moyenne-input', tr)?.value);
                const coef = parseFloat(qs('.coef-input', tr)?.value);
                const out = qs('.mc-output', tr);

                const ok = Number.isFinite(moyenne) && Number.isFinite(coef) && coef > 0;
                if (!ok) {
                    out.value = '';
                    return null;
                }

                const v = moyenne * coef;
                out.value = v.toFixed(2);

                const moyInput = qs('.moyenne-input', tr);
                if (moyenne >= 10) {
                    moyInput.style.color = '#059669';
                    moyInput.style.borderColor = '#86efac';
                } else {
                    moyInput.style.color = '#dc2626';
                    moyInput.style.borderColor = '#fca5a5';
                }

                return v;
            }

            function computeSubjectAverage(tr) {
                const values = Array.from(tr.querySelectorAll('.evaluation-input'))
                    .map(input => input.value === '' ? null : Number(input.value))
                    .filter(value => Number.isFinite(value));
                const moyenneInput = qs('.moyenne-input', tr);
                if (!values.length) return;
                moyenneInput.value = (values.reduce((sum, value) => sum + value, 0) / values.length).toFixed(2);
            }

            function recomputeTotals() {
                let totalCoef = 0;
                let totalPoints = 0;

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
                    moyenneDisplay.style.color = '#dc2626';
                    return;
                }

                const moyenneGenerale = totalPoints / totalCoef;
                totalCoefficientsDisplay.textContent = totalCoef.toFixed(2);
                totalPointsDisplay.textContent = totalPoints.toFixed(2);
                moyenneDisplay.value = moyenneGenerale.toFixed(2);
                moyenneDisplay.style.color = moyenneGenerale >= 10 ? '#059669' : '#E11D48';

                const hidden = document.getElementById('moyenne_generale_hidden') || createHiddenMoyenne();
                hidden.value = moyenneGenerale.toFixed(2);
            }

            function createHiddenMoyenne() {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'moyenne_generale';
                input.id = 'moyenne_generale_hidden';
                input.value = '';
                document.getElementById('bulletinForm').appendChild(input);
                return input;
            }

            function buildRow(rowData={}) {
                const rowIndex = idx++;

                const tr = document.createElement('tr');
                tr.setAttribute('data-row', rowIndex);

                const disciplineName = rowData.discipline ?? defaultDisciplines[rowIndex] ?? '';

                tr.innerHTML = `
                    <td>
                        <input type="hidden" name="disciplines[${rowIndex}][matiere_id]" value="${rowData.matiere_id ?? ''}" />
                        <input type="text" class="discipline-input font-medium" required 
                               name="disciplines[${rowIndex}][discipline]" 
                               value="${escapeHtml(disciplineName)}" 
                               readonly
                               placeholder="Saisir la discipline..." />
                    </td>
                    <td><input type="number" step="0.01" min="0" max="20" class="evaluation-input text-center" name="disciplines[${rowIndex}][interrogation]" value="${rowData.interrogation ?? ''}" /></td>
                    <td><input type="number" step="0.01" min="0" max="20" class="evaluation-input text-center" name="disciplines[${rowIndex}][devoir]" value="${rowData.devoir ?? ''}" /></td>
                    <td><input type="number" step="0.01" min="0" max="20" class="evaluation-input text-center" name="disciplines[${rowIndex}][composition]" value="${rowData.composition ?? ''}" /></td>
                    <td>
                        <input type="number" step="0.01" min="0" max="20" 
                               class="moyenne-input text-center" 
                               name="disciplines[${rowIndex}][moyenne]" 
                               value="${rowData.moyenne ?? ''}" 
                               readonly
                               placeholder="0,00" />
                    </td>
                    <td>
                        <input type="number" step="1" min="1" max="100"
                               class="coef-input text-center font-semibold" 
                               name="disciplines[${rowIndex}][coefficient]" 
                               value="${rowData.coefficient ?? 1}" 
                               readonly
                               required />
                    </td>
                    <td>
                        <input type="text" class="mc-output out text-center" 
                               name="disciplines[${rowIndex}][moyenne_coefficient]" 
                               value="" disabled />
                    </td>
                    <td>
                        <input type="number" min="0" class="rang-input text-center" 
                               name="disciplines[${rowIndex}][rang]" 
                               value="${rowData.rang ?? ''}" 
                               placeholder="0" />
                    </td>
                    <td>
                        <input type="text" class="app-input" 
                               name="disciplines[${rowIndex}][appréciation]" 
                               value="${escapeHtml(rowData.appreciation ?? rowData.appréciation ?? '')}" 
                               placeholder="Appréciation..." />
                    </td>
                    <td>
                        <input type="text" class="prof-input" 
                               name="disciplines[${rowIndex}][professeur]" 
                               value="${escapeHtml(rowData.professeur ?? '')}" 
                               placeholder="Nom du prof." />
                    </td>
                    <td>
                        <input type="text" class="sig-input" 
                               name="disciplines[${rowIndex}][signature]" 
                               value="${escapeHtml(rowData.signature ?? '')}" 
                               placeholder="Signature" />
                    </td>
                    <td class="text-center">
                        <button type="button" class="hidden text-red-600 hover:text-red-700"
                                data-remove-row title="Supprimer">
                            <span class="material-symbols-outlined">delete</span>
                        </button>
                    </td>
                `;

                const moyenneInput = qs('.moyenne-input', tr);
                const coefInput = qs('.coef-input', tr);

                tr.querySelectorAll('.evaluation-input').forEach(input => {
                    input.addEventListener('input', () => {
                        computeSubjectAverage(tr);
                        computeMC(tr);
                        recomputeTotals();
                    });
                });

                moyenneInput.addEventListener('input', () => {
                    computeMC(tr);
                    recomputeTotals();
                });
                coefInput.addEventListener('input', () => {
                    computeMC(tr);
                    recomputeTotals();
                });

                moyenneInput.addEventListener('blur', () => {
                    let val = parseFloat(moyenneInput.value);
                    if (!isNaN(val) && val < 0) moyenneInput.value = 0;
                    if (!isNaN(val) && val > 20) moyenneInput.value = 20;
                    computeMC(tr);
                    recomputeTotals();
                });

                tr.querySelector('[data-remove-row]').addEventListener('click', () => {
                    tr.remove();
                    recomputeTotals();
                });

                computeMC(tr);
                return tr;
            }

            function escapeHtml(str) {
                return String(str ?? '').replace(/[&<>\"']/g, s => ({'&':'&amp;','<':'<','>':'>','\"':'"','\'':'&#039;'}[s]));
            }

            function seedDefaults() {
                disciplinesBody.innerHTML = '';
                idx = 0;
            }

            addDisciplineBtn.addEventListener('click', () => {
                disciplinesBody.appendChild(buildRow({ coefficient: 1, discipline: '' }));
                recomputeTotals();
            });

            seedDefaults();

            function fillDisciplinesTable(disciplines = []) {
                disciplinesBody.innerHTML = '';
                idx = 0;

                if (!Array.isArray(disciplines) || disciplines.length === 0) {
                    // Si aucune donnée en DB, on garde le comportement “manuel” via bouton Ajouter.
                    recomputeTotals();
                    return;
                }

                disciplines.forEach((d) => {
                    const row = buildRow({
                        matiere_id: d.matiere_id ?? '',
                        discipline: d.discipline ?? '',
                        interrogation: d.interrogation ?? '',
                        devoir: d.devoir ?? '',
                        composition: d.composition ?? '',
                        moyenne: d.moyenne ?? '',
                        coefficient: d.coefficient ?? 1,
                        rang: d.rang ?? '',
                        appreciation: d.appréciation ?? d.appréciation ?? d.appreciation ?? '',
                        professeur: d.professeur ?? '',
                        signature: d.signature ?? '',
                    });
                    disciplinesBody.appendChild(row);
                });

                recomputeTotals();
            }


            async function fetchStudentData(eleveId) {
                const anneeId = qs('#annee_academique_id')?.value ?? '';
                const trimestre = qs('#trimestre')?.value ?? '';

                const url = `{{ route('client.bulletin.student-data') }}?eleve_id=${encodeURIComponent(eleveId)}&annee_academique_id=${encodeURIComponent(anneeId)}&trimestre=${encodeURIComponent(trimestre)}`;

                const res = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                if (!res.ok) throw new Error('Erreur lors du chargement des données élève');
                return res.json();
            }

            function fillHeader(data) {
                document.getElementById('logo_etablissement').src = data.etablissement_logo_url || '';
                document.getElementById('nom_prenoms').textContent = [data.nom, data.prenoms].filter(Boolean).join(' ') || '--';
                document.getElementById('matricule').textContent = data.matricule || '--';
                document.getElementById('classe').textContent = data.classe || '--';
                document.getElementById('niveau_display').textContent = data.niveau || '--';
                document.getElementById('serie_display').textContent = data.serie || '--';
                document.getElementById('effectif').textContent = data.effectif ?? '--';
                document.getElementById('sexe').textContent = data.sexe || '--';
                document.getElementById('nationalite').textContent = data.nationalite || '--';
                document.getElementById('date_naissance').textContent = data.date_naissance || '--';
                document.getElementById('lieu_naissance').textContent = data.lieu_naissance || '--';

                document.getElementById('annee_scolaire').textContent = data.annee_academique?.libelle || '--';

                const hiddenClasse = document.getElementById('classe_id');
                if (hiddenClasse) hiddenClasse.value = data.classe_id ?? '';

                // Mettre à jour les selects Niveau et Série si présents
                if (data.niveau_id) {
                    const niveauSelect = document.getElementById('niveau_id');
                    if (niveauSelect) niveauSelect.value = data.niveau_id;
                }
                if (data.serie_id) {
                    const serieSelect = document.getElementById('serie_select');
                    if (serieSelect) serieSelect.value = data.serie_id;
                    document.getElementById('serie_id').value = data.serie_id;
                }
            }

            const niveauSelect = qs('#niveau_id');
            const classeSelect = qs('#classe_select');
            const serieSelect = qs('#serie_select');
            const eleveSelect = qs('#eleve_id');

            function filterOptions(select, predicate) {
                Array.from(select.options).forEach((option, index) => {
                    if (index === 0) return;
                    const visible = predicate(option);
                    option.hidden = !visible;
                    option.disabled = !visible;
                });
            }

            function resetEleves(message) {
                eleveSelect.value = '';
                eleveSelect.options[0].textContent = message;
                eleveSelect.disabled = true;
                filterOptions(eleveSelect, () => false);
            }

            function filterEleves() {
                const classeId = classeSelect.value;
                const serieId = serieSelect.value;
                const hasSeries = Array.from(serieSelect.options).some((option, index) => index > 0 && !option.hidden && !option.disabled);
                filterOptions(eleveSelect, option => {
                    if (String(option.dataset.classeId) !== String(classeId)) return false;
                    return hasSeries ? String(option.dataset.serieId) === String(serieId) : !option.dataset.serieId;
                });
                const hasStudents = Array.from(eleveSelect.options).some((option, index) => index > 0 && !option.hidden && !option.disabled);
                eleveSelect.options[0].textContent = hasStudents ? '-- Sélectionner un élève --' : '-- Aucun élève correspondant --';
                eleveSelect.disabled = !hasStudents || (hasSeries && !serieId);
                if (eleveSelect.disabled) eleveSelect.value = '';
            }

            niveauSelect.addEventListener('change', () => {
                const niveauId = niveauSelect.value;
                classeSelect.value = '';
                classeSelect.disabled = !niveauId;
                classeSelect.options[0].textContent = niveauId ? '-- Sélectionner une classe --' : '-- Choisir un niveau --';
                filterOptions(classeSelect, option => String(option.dataset.niveauId) === String(niveauId));
                serieSelect.value = '';
                serieSelect.disabled = true;
                filterOptions(serieSelect, () => false);
                document.getElementById('serie_id').value = '';
                document.getElementById('classe_id').value = '';
                resetEleves('-- Choisir une classe --');
            });

            classeSelect.addEventListener('change', () => {
                const classeId = classeSelect.value;
                document.getElementById('classe_id').value = classeId;
                serieSelect.value = '';
                filterOptions(serieSelect, option => (option.dataset.classeIds ?? '').split(',').includes(String(classeId)));
                const hasSeries = Array.from(serieSelect.options).some((option, index) => index > 0 && !option.hidden && !option.disabled);
                serieSelect.disabled = !hasSeries;
                serieSelect.options[0].textContent = hasSeries ? '-- Sélectionner une série --' : '-- Aucune série pour cette classe --';
                document.getElementById('serie_id').value = '';
                filterEleves();
            });

            serieSelect.addEventListener('change', () => {
                document.getElementById('serie_id').value = serieSelect.value;
                filterEleves();
            });

            filterOptions(classeSelect, () => false);
            filterOptions(serieSelect, () => false);
            resetEleves('-- Choisir niveau, classe et série --');

            const initialNiveauId = @json(old('niveau_id'));
            const initialClasseId = @json(old('classe_id'));
            const initialSerieId = @json(old('serie_id'));
            const initialEleveId = @json(old('eleve_id'));
            if (initialNiveauId) {
                niveauSelect.value = initialNiveauId;
                niveauSelect.dispatchEvent(new Event('change'));
                classeSelect.value = initialClasseId || '';
                classeSelect.dispatchEvent(new Event('change'));
                if (initialSerieId) {
                    serieSelect.value = initialSerieId;
                    serieSelect.dispatchEvent(new Event('change'));
                }
                if (initialEleveId && !eleveSelect.disabled) eleveSelect.value = initialEleveId;
            }

            eleveSelect.addEventListener('change', async (e) => {
                const eleveId = e.target.value;
                if (!eleveId) return;

                // Remplir les données depuis les attributs data
                const selectedOption = eleveSelect.options[eleveSelect.selectedIndex];
                document.getElementById('niveau_display').textContent = selectedOption.dataset.niveau || '--';
                document.getElementById('serie_display').textContent = selectedOption.dataset.serie || '--';

                try {
                    const data = await fetchStudentData(eleveId);
                    fillHeader(data);
                    if (data.disciplines) {
                        fillDisciplinesTable(data.disciplines);
                    }
                    if (data.bulletin_existant && data.moyenne_generale != null) {
                        moyenneDisplay.value = Number(data.moyenne_generale).toFixed(2);
                        moyenneDisplay.style.color = Number(data.moyenne_generale) >= 10 ? '#059669' : '#E11D48';
                        const hidden = document.getElementById('moyenne_generale_hidden') || createHiddenMoyenne();
                        hidden.value = Number(data.moyenne_generale).toFixed(2);
                    }
                    // Champ décision/appreciation depuis le bulletin existant (si présent)
                    if (data.bulletin_existant) {
                        const decisionInput = document.querySelector('input[name="decision"]');
                        if (decisionInput) decisionInput.value = data.appreciation ?? '';
                    }

                } catch (err) {
                    console.error(err);
                }
            });

            if (eleveSelect.value) {
                eleveSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
    @endpush
@endsection
