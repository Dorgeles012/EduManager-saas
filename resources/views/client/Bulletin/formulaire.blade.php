@extends('client.layouts.app')
@php($directeur = trim(collect([auth()->user()?->nom, auth()->user()?->prenom])->filter()->implode(' ')) ?: auth()->user()?->name)
@php($isEditing = isset($bulletin) && $bulletin)

@section('title', 'EduManager - Générer un bulletin')

@section('content')

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
    <div class="flex items-center gap-3">
        <a href="{{ route('client.bulletin.index') }}" class="w-9 h-9 rounded-xl bg-white border border-gray-100 hover:bg-gray-100 flex items-center justify-center transition flex-shrink-0">
            <span class="material-symbols-outlined text-gray-600 text-base">arrow_back</span>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $isEditing ? 'Modifier le bulletin' : 'Générer un bulletin' }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">Sélectionnez un élève pour générer son bulletin</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <form method="POST" action="{{ $isEditing ? route('client.bulletin.update', $bulletin) : route('client.bulletin.store') }}" id="bulletinForm" novalidate>
        @csrf
        @if($isEditing) @method('PUT') @endif

        <div class="p-5">

            {{-- Sélection --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3 mb-5">
                <div style="order: 1">
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Niveau</label>
                    <select name="niveau_id" id="niveau_id" class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition @error('niveau_id') border-rose-300 @enderror">
                        <option value="">-- Sélectionner un niveau --</option>
                        @foreach($niveaux ?? [] as $niveau)
                            <option value="{{ $niveau->id }}" {{ old('niveau_id') == $niveau->id ? 'selected' : '' }}>{{ $niveau->nom }}</option>
                        @endforeach
                    </select>
                    @error('niveau_id')<p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>@enderror
                </div>

                <div style="order: 2">
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Classe</label>
                    <select id="classe_select" disabled class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition disabled:bg-gray-100 disabled:text-gray-400">
                        <option value="">-- Choisir un niveau --</option>
                        @foreach($classes as $classe)
                            <option value="{{ $classe->id }}" data-niveau-id="{{ $classe->niveau_id }}">{{ $classe->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="order: 3">
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Série</label>
                    <select id="serie_select" disabled class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition disabled:bg-gray-100 disabled:text-gray-400">
                        <option value="">-- Sélectionner --</option>
                        @foreach($series ?? [] as $serie)
                            <option value="{{ $serie->id }}" data-classe-ids="{{ $serie->classes->pluck('id')->implode(',') }}" {{ old('serie_id') == $serie->id ? 'selected' : '' }}>{{ $serie->nom_serie }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="serie_id" id="serie_id" value="{{ old('serie_id') }}">
                    @error('serie_id')<p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>@enderror
                </div>

                <div style="order: 4">
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Élève</label>
                    <select name="eleve_id" id="eleve_id" required disabled
                            class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition disabled:bg-gray-100 disabled:text-gray-400 @error('eleve_id') border-rose-300 @enderror">
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
                    @error('eleve_id')<p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>@enderror
                </div>

                <div style="order: 5">
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Année</label>
                    <select name="annee_academique_id" id="annee_academique_id" required
                            class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition @error('annee_academique_id') border-rose-300 @enderror">
                        <option value="">-- Sélectionner --</option>
                        @foreach($anneesAcademiques as $annee)
                            <option value="{{ $annee->id }}" {{ old('annee_academique_id', $bulletin?->annee_academique_id) == $annee->id ? 'selected' : '' }}>{{ $annee->libelle }}</option>
                        @endforeach
                    </select>
                    @error('annee_academique_id')<p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>@enderror
                </div>

                <div style="order: 6">
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Période</label>
                    <select name="trimestre" id="trimestre" required
                            class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition @error('trimestre') border-rose-300 @enderror">
                        @foreach(['t1' => 'T1', 't2' => 'T2', 't3' => 'T3', 's1' => 'S1', 's2' => 'S2', 'an' => 'Annuel'] as $value => $label)
                            <option value="{{ $value }}" {{ old('trimestre', $bulletin?->trimestre) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('trimestre')<p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <input type="hidden" name="etablissement_id" value="{{ $etablissement?->id }}" id="etablissement_id">
            <input type="hidden" name="classe_id" value="{{ old('classe_id', $classeInitial?->id ?? '') }}" id="classe_id">

            {{-- Header Bulletin --}}
            <div class="bg-gradient-to-br from-indigo-50/60 to-white border border-indigo-100 rounded-2xl p-4 mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-white border border-gray-100 flex items-center justify-center overflow-hidden flex-shrink-0">
                        <img id="logo_etablissement" src="{{ $etablissement?->logo_url ?? asset('images/default-school.png') }}" alt="Logo" class="w-10 h-10 object-contain">
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-bold text-gray-900 truncate">{{ $etablissement?->nom ?? 'Établissement' }}</h3>
                        <p class="text-[11px] text-gray-500">République de Côte d'Ivoire — Ministère de l'Éducation</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Année scolaire</p>
                        <p class="text-sm font-bold text-indigo-600" id="annee_scolaire">--</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2 mt-4 pt-4 border-t border-indigo-100">
                    <div class="bg-white rounded-lg px-2.5 py-1.5 flex items-center justify-between gap-2">
                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Élève</span>
                        <span class="text-xs font-bold text-gray-900 truncate" id="nom_prenoms">--</span>
                    </div>
                    <div class="bg-white rounded-lg px-2.5 py-1.5 flex items-center justify-between gap-2">
                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Mat.</span>
                        <span class="text-xs font-bold text-gray-900" id="matricule">--</span>
                    </div>
                    <div class="bg-white rounded-lg px-2.5 py-1.5 flex items-center justify-between gap-2">
                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Classe</span>
                        <span class="text-xs font-bold text-gray-900" id="classe">--</span>
                    </div>
                    <div class="bg-white rounded-lg px-2.5 py-1.5 flex items-center justify-between gap-2">
                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Niveau</span>
                        <span class="text-xs font-bold text-gray-900" id="niveau_display">--</span>
                    </div>
                    <div class="bg-white rounded-lg px-2.5 py-1.5 flex items-center justify-between gap-2">
                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Série</span>
                        <span class="text-xs font-bold text-gray-900" id="serie_display">--</span>
                    </div>
                    <div class="bg-white rounded-lg px-2.5 py-1.5 flex items-center justify-between gap-2">
                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Eff.</span>
                        <span class="text-xs font-bold text-gray-900" id="effectif">--</span>
                    </div>
                    <div class="bg-white rounded-lg px-2.5 py-1.5 flex items-center justify-between gap-2">
                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Sexe</span>
                        <span class="text-xs font-bold text-gray-900" id="sexe">--</span>
                    </div>
                    <div class="bg-white rounded-lg px-2.5 py-1.5 flex items-center justify-between gap-2">
                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Nat.</span>
                        <span class="text-xs font-bold text-gray-900" id="nationalite">--</span>
                    </div>
                    <div class="bg-white rounded-lg px-2.5 py-1.5 flex items-center justify-between gap-2">
                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Nais.</span>
                        <span class="text-xs font-bold text-gray-900" id="date_naissance">--</span>
                    </div>
                    <div class="bg-white rounded-lg px-2.5 py-1.5 flex items-center justify-between gap-2">
                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Lieu</span>
                        <span class="text-xs font-bold text-gray-900 truncate max-w-[80px]" id="lieu_naissance">--</span>
                    </div>
                </div>
            </div>

            {{-- Disciplines --}}
            <div class="mb-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                            <span class="material-symbols-outlined text-base">library_books</span>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-900">Disciplines</h4>
                            <p class="text-[11px] text-gray-500">Notes, coefficients et mentions</p>
                        </div>
                    </div>
                    <button type="button" id="addDisciplineBtn" class="hidden inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-[11px] font-semibold transition shadow-sm">
                        <span class="material-symbols-outlined text-sm">add</span>
                        Ajouter
                    </button>
                </div>

                @error('disciplines')<p class="text-rose-500 text-[10px] mb-2">{{ $message }}</p>@enderror

                <div class="overflow-x-auto rounded-xl border border-gray-100">
                    <table class="w-full text-sm" id="disciplinesTable">
                        <thead>
                            <tr class="bg-gray-50/70 border-b border-gray-100">
                                <th class="px-3 py-2.5 text-left text-[10px] font-semibold text-gray-500 uppercase tracking-wider" style="min-width: 200px; width: 22%;">Discipline</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-semibold text-gray-500 uppercase tracking-wider" style="min-width: 80px; width: 10%;">Moyenne</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-semibold text-gray-500 uppercase tracking-wider" style="min-width: 70px; width: 9%;">Coef.</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-semibold text-gray-500 uppercase tracking-wider" style="min-width: 70px; width: 9%;">M×C</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-semibold text-gray-500 uppercase tracking-wider" style="min-width: 65px; width: 8%;">Rang</th>
                                <th class="px-3 py-2.5 text-left text-[10px] font-semibold text-gray-500 uppercase tracking-wider" style="min-width: 120px; width: 12%;">Mention</th>
                                <th class="px-3 py-2.5 text-left text-[10px] font-semibold text-gray-500 uppercase tracking-wider" style="min-width: 140px; width: 12%;">Professeur</th>
                                <th class="px-3 py-2.5 text-left text-[10px] font-semibold text-gray-500 uppercase tracking-wider" style="min-width: 130px; width: 11%;">Signature</th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-semibold text-gray-500 uppercase tracking-wider" style="width: 5%;"></th>
                            </tr>
                        </thead>
                        <tbody id="disciplinesBody" class="divide-y divide-gray-100"></tbody>
                    </table>
                </div>

                <div class="mt-2 flex flex-wrap items-center gap-3 text-[11px] text-gray-500">
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-indigo-500 text-sm">info</span>
                        Coefficients issus de la série
                    </span>
                    <span class="flex items-center gap-1">
                        Total coefficients :
                        <strong class="text-gray-900" id="totalCoefficientsDisplay">0</strong>
                    </span>
                    <span class="flex items-center gap-1">
                        Total points :
                        <strong class="text-gray-900" id="totalPointsDisplay">0.00</strong>
                    </span>
                </div>
            </div>

            {{-- Totaux/infos --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Heures</label>
                    <input type="number" step="0.01" name="total_heures" value="{{ old('total_heures') }}" placeholder="0"
                           class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                    @error('total_heures')<p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Absences</label>
                    <input type="number" name="absences" value="{{ old('absences') }}" placeholder="0"
                           class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                    @error('absences')<p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Rang</label>
                    <input type="number" name="rang" value="{{ old('rang') }}" placeholder="0"
                           class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                    @error('rang')<p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Moyenne générale</label>
                    <input type="text" id="moyenne_generale_display" value="--" disabled
                           class="w-full bg-gray-100 border border-gray-200 rounded-lg text-sm py-2.5 px-3 text-center font-bold text-indigo-700 cursor-not-allowed">
                </div>
            </div>

            {{-- Résultat / mention / décision --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Mention</label>
                    <input type="text" id="mention" value="--" readonly
                           class="w-full bg-gray-100 border border-gray-200 rounded-lg text-xs py-2.5 px-3 font-bold text-indigo-700 cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Décision</label>
                    <input type="text" id="decision" value="--" readonly
                           class="w-full bg-gray-100 border border-gray-200 rounded-lg text-xs py-2.5 px-3 font-bold text-indigo-700 cursor-not-allowed">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Observation du conseil</label>
                    <textarea id="observation_conseil" rows="3" readonly
                              class="w-full bg-gray-100 border border-gray-200 rounded-lg text-xs py-2.5 px-3 text-gray-700 resize-none cursor-not-allowed">--</textarea>
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Date</label>
                    <input type="text" id="date_bulletin" value="{{ now()->format('d/m/Y') }}" readonly
                           class="w-full bg-gray-100 border border-gray-200 rounded-lg text-xs py-2.5 px-3 font-semibold text-gray-700 cursor-not-allowed">
                </div>
            </div>

            {{-- Distinctions --}}
            <div class="bg-gradient-to-br from-indigo-50/60 to-white border border-indigo-100 rounded-2xl p-4 mb-5">
                <div class="flex items-center gap-2.5 mb-3">
                    <div class="w-7 h-7 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600">
                        <span class="material-symbols-outlined text-base">stars</span>
                    </div>
                    <h5 class="text-xs font-bold text-gray-900 uppercase tracking-wider">Distinctions</h5>
                </div>

                <div class="flex flex-wrap gap-2">
                    @foreach([
                        'honneur' => 'Honneur',
                        'encouragement' => 'Encouragement',
                        'felicitations' => 'Félicitations',
                        'avertissement' => 'Avertissement',
                        'blame' => 'Blâme',
                    ] as $value => $label)
                        <label class="cursor-pointer">
                            <input type="checkbox" name="distinctions[]" value="{{ $value }}" class="peer sr-only" {{ in_array($value, old('distinctions', [])) ? 'checked' : '' }}>
                            <div class="px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 transition">
                                {{ $label }}
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
            @error('distinctions')<p class="text-rose-500 text-[10px] mb-3">{{ $message }}</p>@enderror

            {{-- Signatures --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Professeur principal</label>
                    <input type="text" name="signature_professeur_principal" value="{{ old('signature_professeur_principal') }}" placeholder="Nom"
                           class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                    @error('signature_professeur_principal')<p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Directeur</label>
                    <input type="text" id="signature_directeur" value="{{ $directeur }}" readonly
                           class="w-full bg-gray-100 border border-gray-200 rounded-lg text-xs py-2.5 px-3 font-semibold text-gray-700 cursor-not-allowed">
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 pt-4 border-t border-gray-100">
                <a href="{{ route('client.bulletin.index') }}"
                   class="w-full sm:w-auto text-center px-5 py-2.5 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    Annuler
                </a>
                <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-xs font-semibold transition shadow-sm">
                    <span class="material-symbols-outlined text-sm">save</span>
                    {{ $isEditing ? 'Mettre à jour' : 'Enregistrer le bulletin' }}
                </button>
            </div>
        </div>
    </form>
</div>

@endsection

@push('styles')
<style>
    /* Styles spécifiques au tableau des disciplines */
    #disciplinesTable { border-collapse: separate; border-spacing: 0; }
    #disciplinesTable th, #disciplinesTable td { padding: 0.5rem 0.4rem; vertical-align: middle; }

    #disciplinesTable .discipline-input,
    #disciplinesTable .moyenne-input,
    #disciplinesTable .coef-input,
    #disciplinesTable .rang-input,
    #disciplinesTable .mention-input,
    #disciplinesTable .prof-input,
    #disciplinesTable .sig-input,
    #disciplinesTable .mc-output {
        width: 100%;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 0.75rem;
        padding: 0.5rem 0.4rem;
        outline: none;
        transition: all 0.15s ease;
    }
    #disciplinesTable .discipline-input { font-weight: 600; text-align: left; padding-left: 0.6rem; }
    #disciplinesTable .moyenne-input { font-weight: 700; text-align: center; }
    #disciplinesTable .coef-input { font-weight: 600; text-align: center; background: #fffbeb; border-color: #fde68a; }
    #disciplinesTable .rang-input { text-align: center; }
    #disciplinesTable .mention-input { background: #f3f4f6; color: #4f46e5; font-weight: 700; text-align: left; padding-left: 0.6rem; cursor: not-allowed; }
    #disciplinesTable .prof-input { text-align: left; padding-left: 0.6rem; }
    #disciplinesTable .sig-input { text-align: left; padding-left: 0.6rem; }
    #disciplinesTable .mc-output {
        text-align: center;
        font-weight: 800;
        color: #4f46e5;
        background: #eef2ff;
        border-color: #c7d2fe;
    }

    #disciplinesTable input:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
        background: #ffffff;
    }

    #disciplinesTable input::placeholder { color: #cbd5e1; font-weight: 400; }

    #disciplinesTable [data-remove-row] {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: transparent;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    #disciplinesTable [data-remove-row]:hover { background: #fee2e2; }

    #disciplinesTable tbody tr { transition: background 0.15s ease; }
    #disciplinesTable tbody tr:hover { background: #f8fafc; }

    #moyenne_generale_display {
        font-size: 1rem !important;
        font-weight: 800 !important;
    }

    /* Scrollbar fine */
    .overflow-x-auto::-webkit-scrollbar { height: 6px; }
    .overflow-x-auto::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
    .overflow-x-auto::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .overflow-x-auto::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
@endpush

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
        'Composition Français', 'Orthographe-Grammaire', 'Lecture-Expression Orale',
        'Histoire-Géographie', 'Anglais', 'Allemand', 'Mathématiques',
        'Physique-Chimie', 'SVT', 'EDHC', 'EPS', 'Arts Plastiques', 'Conduite'
    ];

    const qs = (sel, root = document) => root.querySelector(sel);

    function computeMC(tr) {
        const moyenne = parseFloat(qs('.moyenne-input', tr)?.value);
        const coef = parseFloat(qs('.coef-input', tr)?.value);
        const out = qs('.mc-output', tr);
        const ok = Number.isFinite(moyenne) && Number.isFinite(coef) && coef > 0;
        if (!ok) { out.value = ''; return null; }
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
            moyenneDisplay.style.color = '#dc2626';
            return;
        }
        const moyenneGenerale = totalPoints / totalCoef;
        totalCoefficientsDisplay.textContent = totalCoef.toFixed(2);
        totalPointsDisplay.textContent = totalPoints.toFixed(2);
        moyenneDisplay.value = moyenneGenerale.toFixed(2);
        moyenneDisplay.style.color = moyenneGenerale >= 10 ? '#059669' : '#dc2626';
        updateAutomaticFields(moyenneGenerale);
        const hidden = document.getElementById('moyenne_generale_hidden') || createHiddenMoyenne();
        hidden.value = moyenneGenerale.toFixed(2);
    }

    function updateAutomaticFields(moyenne) {
        const mention = document.getElementById('mention');
        const decision = document.getElementById('decision');
        const observation = document.getElementById('observation_conseil');
        if (!Number.isFinite(moyenne)) return;
        const levels = [
            [19, 'Excellent', 'Travail exceptionnel. Résultats remarquables. Félicitations du jury. Continuez ainsi.'],
            [18, 'Excellent', 'Excellent travail. Très grande maîtrise des apprentissages. Toutes nos félicitations.'],
            [17, 'Très Bien', 'Très bon travail. Élève sérieux, appliqué et régulier. Félicitations.'],
            [16, 'Très Bien', 'Très bons résultats. Continuez vos efforts.'],
            [15, 'Bien', 'Bon travail. Ensemble satisfaisant. Encourageant pour la suite.'],
            [14, 'Assez Bien', 'Bons résultats. Quelques efforts supplémentaires permettront de progresser davantage.'],
            [13, 'Assez Bien', 'Travail satisfaisant. Continuez avec plus de régularité.'],
            [12, 'Passable', 'Résultats corrects. Des efforts sont encore attendus.'],
            [11, 'Passable', 'Ensemble acceptable mais irrégulier. Il faut travailler davantage.'],
            [10, 'Passable', 'Moyenne acquise de justesse. Les efforts doivent être poursuivis.'],
            [9, 'Insuffisant', 'Résultats insuffisants. Un travail plus sérieux est indispensable.'],
            [8, 'Faible', 'Travail faible. Il est nécessaire de fournir davantage d\u2019efforts.'],
            [7, 'Très Faible', 'Résultats très insuffisants. Réaction rapide attendue.'],
            [5, 'Très Faible', 'Grandes difficultés. Travail insuffisant. Beaucoup plus d\u2019investissement est nécessaire.'],
            [0, 'Très Insuffisant', 'Résultats très préoccupants. Une remise au travail est indispensable.'],
        ];
        const level = levels.find(([minimum]) => moyenne >= minimum);
        mention.value = level[1];
        decision.value = moyenne >= 10 ? 'Admis(e)' : 'Refusé(e)';
        observation.value = level[2];
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

    function buildRow(rowData = {}) {
        const rowIndex = idx++;
        const tr = document.createElement('tr');
        tr.setAttribute('data-row', rowIndex);
        const disciplineName = rowData.discipline ?? defaultDisciplines[rowIndex] ?? '';

        tr.innerHTML = `
            <td>
                <input type="hidden" name="disciplines[${rowIndex}][matiere_id]" value="${rowData.matiere_id ?? ''}" />
                <input type="text" class="discipline-input" required name="disciplines[${rowIndex}][discipline]"
                       value="${escapeHtml(disciplineName)}" readonly placeholder="Saisir la discipline..." />
            </td>
            <td>
                <input type="number" step="0.01" min="0" max="20" class="moyenne-input"
                       name="disciplines[${rowIndex}][moyenne]" value="${rowData.moyenne ?? ''}" placeholder="0,00" />
            </td>
            <td>
                <input type="number" step="1" min="1" max="100" class="coef-input"
                       name="disciplines[${rowIndex}][coefficient]" value="${rowData.coefficient ?? 1}" readonly required />
            </td>
            <td>
                <input type="text" class="mc-output" name="disciplines[${rowIndex}][moyenne_coefficient]" value="" disabled />
            </td>
            <td>
                <input type="number" min="0" class="rang-input" name="disciplines[${rowIndex}][rang]" value="${rowData.rang ?? ''}" placeholder="0" />
            </td>
            <td>
                <input type="text" class="mention-input" value="${escapeHtml(rowData.mention ?? '')}" readonly />
            </td>
            <td>
                <input type="text" class="prof-input" name="disciplines[${rowIndex}][professeur]" value="${escapeHtml(rowData.professeur ?? '')}" placeholder="Nom du prof." />
            </td>
            <td>
                <input type="text" class="sig-input" name="disciplines[${rowIndex}][signature]" value="${escapeHtml(rowData.signature ?? '')}" placeholder="Signature" />
            </td>
            <td class="text-center">
                <button type="button" class="hidden" data-remove-row title="Supprimer">
                    <span class="material-symbols-outlined text-rose-600 text-base">delete</span>
                </button>
            </td>
        `;

        const moyenneInput = qs('.moyenne-input', tr);
        const coefInput = qs('.coef-input', tr);

        moyenneInput.addEventListener('input', () => { computeMC(tr); recomputeTotals(); });
        coefInput.addEventListener('input', () => { computeMC(tr); recomputeTotals(); });
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
        return String(str ?? '').replace(/[&<>\"']/g, s => ({ '&': '&amp;', '<': '<', '>': '>', '\"': '"', '\'': '&#039;' }[s]));
    }

    function seedDefaults() { disciplinesBody.innerHTML = ''; idx = 0; }

    addDisciplineBtn.addEventListener('click', () => {
        disciplinesBody.appendChild(buildRow({ coefficient: 1, discipline: '' }));
        recomputeTotals();
    });

    seedDefaults();

    function fillDisciplinesTable(disciplines = []) {
        disciplinesBody.innerHTML = '';
        idx = 0;
        if (!Array.isArray(disciplines) || disciplines.length === 0) { recomputeTotals(); return; }
        disciplines.forEach((d) => {
            const row = buildRow({
                matiere_id: d.matiere_id ?? '',
                discipline: d.discipline ?? '',
                moyenne: d.moyenne ?? '',
                coefficient: d.coefficient ?? 1,
                rang: d.rang ?? '',
                mention: d.mention ?? '',
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

    const initialNiveauId = @json(old('niveau_id', $bulletin?->eleve?->niveau_id));
    const initialClasseId = @json(old('classe_id', $bulletin?->classe_id));
    const initialSerieId = @json(old('serie_id', $bulletin?->eleve?->id_serie));
    const initialEleveId = @json(old('eleve_id', $bulletin?->eleve_id));
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
        const selectedOption = eleveSelect.options[eleveSelect.selectedIndex];
        document.getElementById('niveau_display').textContent = selectedOption.dataset.niveau || '--';
        document.getElementById('serie_display').textContent = selectedOption.dataset.serie || '--';

        try {
            const data = await fetchStudentData(eleveId);
            fillHeader(data);
            if (data.disciplines) fillDisciplinesTable(data.disciplines);
            if (data.bulletin_existant && data.moyenne_generale != null) {
                moyenneDisplay.value = Number(data.moyenne_generale).toFixed(2);
                moyenneDisplay.style.color = Number(data.moyenne_generale) >= 10 ? '#059669' : '#dc2626';
                const hidden = document.getElementById('moyenne_generale_hidden') || createHiddenMoyenne();
                hidden.value = Number(data.moyenne_generale).toFixed(2);
            }
            if (data.moyenne_generale != null) updateAutomaticFields(Number(data.moyenne_generale));
            document.getElementById('mention').value = data.mention ?? document.getElementById('mention').value;
            document.getElementById('decision').value = data.decision ?? document.getElementById('decision').value;
            document.getElementById('observation_conseil').value = data.observation_conseil ?? document.getElementById('observation_conseil').value;
            document.getElementById('date_bulletin').value = data.date ?? document.getElementById('date_bulletin').value;
            document.getElementById('signature_directeur').value = data.directeur ?? document.getElementById('signature_directeur').value;
        } catch (err) { console.error(err); }
    });

    if (eleveSelect.value) eleveSelect.dispatchEvent(new Event('change'));
});
</script>
@endpush