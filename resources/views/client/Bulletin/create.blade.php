@extends('client.layouts.app')
@section('title', 'EduManager - Générer un bulletin')

@section('content')
    @php($noSidebar = true)

    <!-- Barre de retour -->
    <div class="container-fluid px-0">
        <div class="row mx-0 mb-3">
            <div class="col-12 px-0">
                <a
                   
                    class="btn btn-outline-primary ms-3 mt-3"
                >
                    <i class="bi bi-arrow-left"></i> Retour
                </a>
            </div>
        </div>

        <!-- Formulaire centré sur toute la largeur -->
        <div class="row justify-content-center mx-0">
            <div class="col-12 col-lg-10">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100/50 overflow-hidden">
                    <form method="POST" action="" id="bulletinForm" novalidate>
                        @csrf

                        <div class="bg-white rounded-xl shadow-sm border border-gray-100/50 overflow-hidden">
                            <div class="p-3">

            <!-- Selection - 3 columns mini -->
            <div class="grid grid-cols-3 gap-2 mb-3">
                <div>
                    <label class="block text-[10px] text-on-surface-variant mb-0.5 font-medium flex items-center gap-0.5">
                        <span class="material-symbols-outlined text-[14px]">person</span>
                        Élève
                    </label>
                    <select class="w-full bg-gray-50 border border-gray-200 rounded-lg px-2 py-1.5 text-xs focus:border-primary focus:ring-1 focus:ring-primary/10 @error('eleve_id') border-red-400 @enderror" name="eleve_id" id="eleve_id" required>
                        <option value="">-- Sélectionner --</option>
                        @foreach($eleves as $eleve)
                            <option value="{{ $eleve->id }}" data-matricule="{{ $eleve->matricule }}" data-photo="{{ $eleve->photo ? $eleve->getPhotoUrlAttribute() : '' }}">
                                {{ $eleve->nom }} {{ $eleve->prenom }}
                            </option>
                        @endforeach
                    </select>
                    @error('eleve_id')
                        <p class="text-red-500 text-[10px] mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-[10px] text-on-surface-variant mb-0.5 font-medium flex items-center gap-0.5">
                        <span class="material-symbols-outlined text-[14px]">event_note</span>
                        Année
                    </label>
                    <select class="w-full bg-gray-50 border border-gray-200 rounded-lg px-2 py-1.5 text-xs focus:border-primary focus:ring-1 focus:ring-primary/10 @error('annee_academique_id') border-red-400 @enderror" name="annee_academique_id" id="annee_academique_id" required>
                        <option value="">-- Sélectionner --</option>
                        @foreach($annees as $annee)
                            <option value="{{ $annee->id }}">{{ $annee->libelle }}</option>
                        @endforeach
                    </select>
                    @error('annee_academique_id')
                        <p class="text-red-500 text-[10px] mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-[10px] text-on-surface-variant mb-0.5 font-medium flex items-center gap-0.5">
                        <span class="material-symbols-outlined text-[14px]">calendar_month</span>
                        Période
                    </label>
                    <select class="w-full bg-gray-50 border border-gray-200 rounded-lg px-2 py-1.5 text-xs focus:border-primary focus:ring-1 focus:ring-primary/10 @error('trimestre') border-red-400 @enderror" name="trimestre" id="trimestre" required>
                        <option value="t1">T1</option>
                        <option value="t2">T2</option>
                        <option value="t3">T3</option>
                        <option value="s1">S1</option>
                        <option value="s2">S2</option>
                        <option value="an">Annuel</option>
                    </select>
                    @error('trimestre')
                        <p class="text-red-500 text-[10px] mt-0.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Hidden Fields -->
            <input type="hidden" name="etablissement_id" value="{{ $etablissement?->id }}" id="etablissement_id">

            <!-- Bulletin Header - Mini -->
            <div class="bg-gray-50 rounded-lg border border-gray-200 p-2 mb-3">
                <div class="flex items-center gap-3">
                    <!-- Logo -->
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-lg bg-white shadow-sm border border-gray-200 flex items-center justify-center overflow-hidden">
                            <img id="logo_etablissement" 
                                 src="" 
                                 alt="Logo" 
                                 class="w-8 h-8 object-contain" />
                        </div>
                    </div>
                    
                    <!-- School Info -->
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-bold text-on-surface truncate">{{ $etablissement?->nom ?? 'Établissement' }}</h3>
                        <div class="flex flex-wrap gap-x-3 gap-y-0 text-[10px] text-on-surface-variant">
                            <span class="truncate">{{ $etablissement?->adresse ?? 'Adresse' }}</span>
                            <span>{{ $etablissement?->telephone ?? 'Tél' }}</span>
                        </div>
                    </div>
                    
                    <!-- Ministere -->
                    <div class="text-right flex-shrink-0">
                        <div class="text-[10px] font-semibold text-on-surface">Côte d'Ivoire</div>
                        <div class="text-[9px] text-on-surface-variant">Ministère</div>
                        <div class="text-[10px] font-medium text-primary" id="annee_scolaire">--</div>
                    </div>
                </div>

                <!-- Student Info - Mini Grid -->
                <div class="grid grid-cols-4 gap-1.5 mt-2 pt-2 border-t border-gray-200">
                    <div class="flex justify-between items-center px-1.5 py-1 bg-white rounded border border-gray-100 text-[10px]">
                        <span class="font-medium text-on-surface-variant">Élève</span>
                        <span class="font-medium text-on-surface truncate max-w-[60px]" id="nom_prenoms">--</span>
                    </div>
                    <div class="flex justify-between items-center px-1.5 py-1 bg-white rounded border border-gray-100 text-[10px]">
                        <span class="font-medium text-on-surface-variant">Mat.</span>
                        <span class="font-medium" id="matricule">--</span>
                    </div>
                    <div class="flex justify-between items-center px-1.5 py-1 bg-white rounded border border-gray-100 text-[10px]">
                        <span class="font-medium text-on-surface-variant">Classe</span>
                        <span class="font-medium" id="classe">--</span>
                    </div>
                    <div class="flex justify-between items-center px-1.5 py-1 bg-white rounded border border-gray-100 text-[10px]">
                        <span class="font-medium text-on-surface-variant">Eff.</span>
                        <span class="font-medium" id="effectif">--</span>
                    </div>
                    <div class="flex justify-between items-center px-1.5 py-1 bg-white rounded border border-gray-100 text-[10px]">
                        <span class="font-medium text-on-surface-variant">Sexe</span>
                        <span class="font-medium" id="sexe">--</span>
                    </div>
                    <div class="flex justify-between items-center px-1.5 py-1 bg-white rounded border border-gray-100 text-[10px]">
                        <span class="font-medium text-on-surface-variant">Nat.</span>
                        <span class="font-medium" id="nationalite">--</span>
                    </div>
                    <div class="flex justify-between items-center px-1.5 py-1 bg-white rounded border border-gray-100 text-[10px]">
                        <span class="font-medium text-on-surface-variant">Nais.</span>
                        <span class="font-medium" id="date_naissance">--</span>
                    </div>
                    <div class="flex justify-between items-center px-1.5 py-1 bg-white rounded border border-gray-100 text-[10px]">
                        <span class="font-medium text-on-surface-variant">Lieu</span>
                        <span class="font-medium truncate max-w-[40px]" id="lieu_naissance">--</span>
                    </div>
                </div>
            </div>

            <!-- Disciplines - Ultra Compact Table -->
            <div class="mb-3">
                <div class="flex items-center justify-between mb-1.5">
                    <h4 class="text-xs font-bold text-on-surface flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">library_books</span>
                        Disciplines
                    </h4>
                    <button type="button" class="bg-primary text-on-primary px-2 py-0.5 rounded text-[10px] font-medium flex items-center gap-0.5 hover:shadow-md hover:scale-105 transition-all duration-200" id="addDisciplineBtn">
                        <span class="material-symbols-outlined text-sm">add</span>
                        Ajouter
                    </button>
                </div>

                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="w-full text-[10px]" id="disciplinesTable">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-1 py-1.5 text-left font-semibold text-on-surface-variant text-[8px] uppercase tracking-wider min-w-[100px]">Discipline</th>
                                <th class="px-1 py-1.5 text-left font-semibold text-on-surface-variant text-[8px] uppercase tracking-wider w-[50px]">Moy.</th>
                                <th class="px-1 py-1.5 text-left font-semibold text-on-surface-variant text-[8px] uppercase tracking-wider w-[50px]">Coef.</th>
                                <th class="px-1 py-1.5 text-left font-semibold text-on-surface-variant text-[8px] uppercase tracking-wider w-[60px]">M×C</th>
                                <th class="px-1 py-1.5 text-left font-semibold text-on-surface-variant text-[8px] uppercase tracking-wider w-[40px]">Rang</th>
                                <th class="px-1 py-1.5 text-left font-semibold text-on-surface-variant text-[8px] uppercase tracking-wider min-w-[80px]">Appréciation</th>
                                <th class="px-1 py-1.5 text-left font-semibold text-on-surface-variant text-[8px] uppercase tracking-wider min-w-[80px]">Prof</th>
                                <th class="px-1 py-1.5 text-left font-semibold text-on-surface-variant text-[8px] uppercase tracking-wider w-[70px]">Signature</th>
                            </tr>
                        </thead>
                        <tbody id="disciplinesBody" class="divide-y divide-gray-100">
                        </tbody>
                    </table>
                </div>
                <p class="text-on-surface-variant text-[9px] mt-1 flex items-center gap-0.5">
                    <span class="material-symbols-outlined text-[12px]">info</span>
                    Modifiables
                </p>
            </div>

            <!-- Footer - Compact 2 columns -->
            <div class="grid grid-cols-2 gap-2 mb-3">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[9px] text-on-surface-variant mb-0.5 font-medium flex items-center gap-0.5">
                            <span class="material-symbols-outlined text-[12px]">schedule</span>
                            Heures
                        </label>
                        <input type="number" step="0.01" name="total_heures" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-1.5 py-1 text-xs focus:border-primary focus:ring-1 focus:ring-primary/10" value="{{ old('total_heures') }}" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-[9px] text-on-surface-variant mb-0.5 font-medium flex items-center gap-0.5">
                            <span class="material-symbols-outlined text-[12px]">event_busy</span>
                            Abs.
                        </label>
                        <input type="number" name="absences" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-1.5 py-1 text-xs focus:border-primary focus:ring-1 focus:ring-primary/10" value="{{ old('absences') }}" placeholder="0">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[9px] text-on-surface-variant mb-0.5 font-medium flex items-center gap-0.5">
                            <span class="material-symbols-outlined text-[12px]">military_tech</span>
                            Rang
                        </label>
                        <input type="number" name="rang" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-1.5 py-1 text-xs focus:border-primary focus:ring-1 focus:ring-primary/10" value="{{ old('rang') }}" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-[9px] text-on-surface-variant mb-0.5 font-medium flex items-center gap-0.5">
                            <span class="material-symbols-outlined text-[12px]">calculate</span>
                            Moy.
                        </label>
                        <input type="text" class="w-full bg-gray-100 border border-gray-200 rounded-lg px-1.5 py-1 text-xs font-bold text-primary" id="moyenne_generale_display" value="--" disabled>
                    </div>
                </div>
            </div>

            <!-- Result & Decision - Compact -->
            <div class="grid grid-cols-2 gap-2 mb-3">
                <div>
                    <label class="block text-[9px] text-on-surface-variant mb-0.5 font-medium flex items-center gap-0.5">
                        <span class="material-symbols-outlined text-[12px]">assessment</span>
                        Résultat
                    </label>
                    <input type="text" name="resultat_classe" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-2 py-1 text-xs focus:border-primary focus:ring-1 focus:ring-primary/10" value="{{ old('resultat_classe') }}" placeholder="Admis">
                </div>
                <div>
                    <label class="block text-[9px] text-on-surface-variant mb-0.5 font-medium flex items-center gap-0.5">
                        <span class="material-symbols-outlined text-[12px]">gavel</span>
                        Décision
                    </label>
                    <input type="text" name="decision" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-2 py-1 text-xs focus:border-primary focus:ring-1 focus:ring-primary/10" value="{{ old('decision') }}" placeholder="Admis">
                </div>
            </div>

            <!-- Observation & Date -->
            <div class="grid grid-cols-2 gap-2 mb-3">
                <div>
                    <label class="block text-[9px] text-on-surface-variant mb-0.5 font-medium flex items-center gap-0.5">
                        <span class="material-symbols-outlined text-[12px]">comment</span>
                        Observation
                    </label>
                    <input type="text" name="observation_conseil" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-2 py-1 text-xs focus:border-primary focus:ring-1 focus:ring-primary/10" value="{{ old('observation_conseil') }}" placeholder="Observations...">
                </div>
                <div>
                    <label class="block text-[9px] text-on-surface-variant mb-0.5 font-medium flex items-center gap-0.5">
                        <span class="material-symbols-outlined text-[12px]">calendar_today</span>
                        Date
                    </label>
                    <input type="date" name="date" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-2 py-1 text-xs focus:border-primary focus:ring-1 focus:ring-primary/10" value="{{ old('date') }}">
                </div>
            </div>

            <!-- Distinctions - Mini tags -->
            <div class="bg-gradient-to-br from-blue-50/50 to-purple-50/50 rounded-lg border border-blue-100 p-2 mb-3">
                <h5 class="text-[10px] font-bold text-on-surface flex items-center gap-1 mb-1.5">
                    <span class="material-symbols-outlined text-sm">stars</span>
                    Distinctions
                </h5>
                <div class="flex flex-wrap gap-1.5">
                    @php
                        $distinctions = [
                            'honneur' => 'Honneur',
                            'encouragement' => 'Encouragement',
                            'felicitations' => 'Félicitations',
                            'avertissement' => 'Avertissement',
                            'blame' => 'Blâme'
                        ];
                    @endphp
                    @foreach($distinctions as $value => $label)
                    <label class="flex items-center gap-1 px-2 py-0.5 bg-white rounded border border-gray-200 hover:border-primary/30 hover:bg-primary/5 cursor-pointer transition-all duration-200 text-[10px]">
                        <input type="checkbox" name="distinctions[]" value="{{ $value }}" id="dist_{{ $value }}" class="w-3 h-3 rounded border-gray-300 text-primary focus:ring-primary/20 focus:ring-1">
                        <span class="font-medium text-on-surface">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- Signatures - Compact -->
            <div class="grid grid-cols-2 gap-2 mb-3">
                <div>
                    <label class="block text-[9px] text-on-surface-variant mb-0.5 font-medium flex items-center gap-0.5">
                        <span class="material-symbols-outlined text-[12px]">edit_note</span>
                        Sign. Prof
                    </label>
                    <input type="text" name="signature_professeur_principal" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-2 py-1 text-xs focus:border-primary focus:ring-1 focus:ring-primary/10" value="{{ old('signature_professeur_principal') }}" placeholder="Nom">
                </div>
                <div>
                    <label class="block text-[9px] text-on-surface-variant mb-0.5 font-medium flex items-center gap-0.5">
                        <span class="material-symbols-outlined text-[12px]">edit_note</span>
                        Sign. Dir
                    </label>
                    <input type="text" name="signature_directeur" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-2 py-1 text-xs focus:border-primary focus:ring-1 focus:ring-primary/10" value="{{ old('signature_directeur') }}" placeholder="Nom">
                </div>
            </div>

            <!-- Submit - Mini -->
            <div class="flex justify-end pt-2 border-t border-gray-200">
                <button type="submit" class="bg-gradient-to-r from-primary to-primary/90 text-on-primary px-4 py-1.5 rounded-lg text-xs font-medium flex items-center gap-1.5 hover:shadow-lg hover:shadow-primary/25 hover:scale-105 transition-all duration-200">
                    <span class="material-symbols-outlined text-sm">save</span>
                    Enregistrer
                </button>
            </div>
        </div>
    </form>
</div>

<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        vertical-align: middle;
    }
    
    body {
        background: linear-gradient(135deg, #f8f9ff 0%, #f0f1ff 100%);
    }
    
    #disciplinesTable input,
    #disciplinesTable select {
        background: transparent;
        border: none;
        padding: 0.15rem 0.1rem;
        font-size: 0.65rem;
        width: 100%;
        transition: all 0.2s;
    }
    
    #disciplinesTable input:focus,
    #disciplinesTable select:focus {
        outline: none;
        background: rgba(31, 16, 142, 0.05);
        border-radius: 0.2rem;
    }
    
    #disciplinesTable input:hover,
    #disciplinesTable select:hover {
        background: rgba(31, 16, 142, 0.03);
        border-radius: 0.2rem;
    }
    
    #disciplinesTable td {
        padding: 0.1rem 0.2rem;
        vertical-align: middle;
    }
    
    #disciplinesTable .moyenne-coef-output {
        font-weight: 600;
        color: #1f108e;
    }
</style>

@push('scripts')
<script>
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

    let disciplinesIndex = 0;

    function buildDisciplineRow(rowData = {}) {
        const idx = disciplinesIndex++;
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50/50 transition-colors duration-150';
        tr.innerHTML = `
            <td class="px-1 py-0.5">
                <input type="text" class="discipline-input font-medium text-[10px]" name="disciplines[${idx}][discipline]" value="${rowData.discipline ?? defaultDisciplines[idx] ?? ''}" required placeholder="Discipline" />
            </td>
            <td class="px-1 py-0.5">
                <input type="number" step="0.01" min="0" max="20" class="moyenne-input text-center text-[10px]" name="disciplines[${idx}][moyenne]" value="${rowData.moyenne ?? ''}" placeholder="0" />
            </td>
            <td class="px-1 py-0.5">
                <input type="number" step="0.1" min="0" max="10" class="coefficient-input text-center font-semibold text-[10px]" name="disciplines[${idx}][coefficient]" value="${rowData.coefficient ?? 1}" required placeholder="1" />
            </td>
            <td class="px-1 py-0.5">
                <input type="text" class="moyenne-coef-output text-center text-primary font-bold text-[10px]" name="disciplines[${idx}][moyenne_coefficient]" value="${rowData.moyenne_coefficient ?? ''}" disabled />
            </td>
            <td class="px-1 py-0.5">
                <input type="number" min="0" class="rang-input text-center text-[10px]" name="disciplines[${idx}][rang]" value="${rowData.rang ?? ''}" placeholder="-" />
            </td>
            <td class="px-1 py-0.5">
                <input type="text" class="appreciation-input text-[10px]" name="disciplines[${idx}][appréciation]" value="${rowData.appreciation ?? ''}" placeholder="Appréciation" />
            </td>
            <td class="px-1 py-0.5">
                <input type="text" class="professeur-input text-[10px]" name="disciplines[${idx}][professeur]" value="${rowData.professeur ?? ''}" placeholder="Nom" />
            </td>
            <td class="px-1 py-0.5">
                <input type="text" class="signature-input text-[10px]" name="disciplines[${idx}][signature]" value="${rowData.signature ?? ''}" placeholder="Signature" />
            </td>
        `;
        return tr;
    }

    function recomputeRow(tr) {
        const moyenne = parseFloat(tr.querySelector('.moyenne-input')?.value);
        const coefficient = parseFloat(tr.querySelector('.coefficient-input')?.value);
        const out = tr.querySelector('.moyenne-coef-output');

        if (!isNaN(moyenne) && !isNaN(coefficient) && coefficient > 0) {
            const v = (moyenne * coefficient).toFixed(2);
            out.value = v;
            tr.dataset.hasMoyenne = '1';
        } else {
            out.value = '';
            tr.dataset.hasMoyenne = '0';
        }
    }

    function recomputeTotals() {
        let totCoeff = 0;
        let totPoints = 0;

        document.querySelectorAll('#disciplinesBody tr').forEach(tr => {
            const moyenne = parseFloat(tr.querySelector('.moyenne-input')?.value);
            const coefficient = parseFloat(tr.querySelector('.coefficient-input')?.value);

            if (!isNaN(moyenne) && !isNaN(coefficient) && coefficient > 0) {
                totCoeff += coefficient;
                totPoints += moyenne * coefficient;
            }
        });

        const moyenneGenerale = totCoeff > 0 ? (totPoints / totCoeff) : null;
        const display = document.getElementById('moyenne_generale_display');
        display.value = moyenneGenerale !== null ? moyenneGenerale.toFixed(2) : '--';
        display.style.color = moyenneGenerale !== null && moyenneGenerale >= 10 ? '#059669' : '#E11D48';
    }

    function initTable() {
        const tbody = document.getElementById('disciplinesBody');
        tbody.innerHTML = '';
        disciplinesIndex = 0;

        defaultDisciplines.forEach((name) => {
            const tr = buildDisciplineRow({ discipline: name, coefficient: 1 });
            tbody.appendChild(tr);
        });

        document.querySelectorAll('#disciplinesBody tr').forEach(tr => {
            tr.querySelectorAll('.moyenne-input, .coefficient-input').forEach(el => {
                el.addEventListener('input', () => {
                    recomputeRow(tr);
                    recomputeTotals();
                });
                el.addEventListener('change', () => {
                    recomputeRow(tr);
                    recomputeTotals();
                });
            });
        });

        recomputeTotals();
    }

    function addDiscipline() {
        const tbody = document.getElementById('disciplinesBody');
        const tr = buildDisciplineRow({ discipline: '', coefficient: 1 });
        tbody.appendChild(tr);

        tr.querySelectorAll('.moyenne-input, .coefficient-input').forEach(el => {
            el.addEventListener('input', () => {
                recomputeRow(tr);
                recomputeTotals();
            });
            el.addEventListener('change', () => {
                recomputeRow(tr);
                recomputeTotals();
            });
        });

        tr.querySelector('.discipline-input')?.focus();
        recomputeTotals();
    }

    async function fetchStudentData(eleveId) {
        const anneeId = document.getElementById('annee_academique_id')?.value || '';
        const trimestreText = document.getElementById('trimestre')?.value || '';

        const url = `{{ route('client.bulletin.studentData') }}?eleve_id=${encodeURIComponent(eleveId)}&annee_academique_id=${encodeURIComponent(anneeId)}&trimestre=${encodeURIComponent(trimestreText)}`;

        const res = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        if (!res.ok) {
            throw new Error('Erreur lors du chargement des données élève');
        }

        return await res.json();
    }

    function fillHeader(data) {
        document.getElementById('nom_prenoms').textContent = [data.nom, data.prenoms].filter(Boolean).join(' ') || '--';
        document.getElementById('matricule').textContent = data.matricule || '--';
        document.getElementById('classe').textContent = data.classe || '--';
        document.getElementById('sexe').textContent = data.sexe || '--';
        document.getElementById('nationalite').textContent = data.nationalite || '--';
        document.getElementById('effectif').textContent = data.effectif ?? '--';
        document.getElementById('date_naissance').textContent = data.date_naissance || '--';
        document.getElementById('lieu_naissance').textContent = data.lieu_naissance || '--';
        document.getElementById('annee_scolaire').textContent = data.annee_academique?.libelle || '--';
    }

    document.addEventListener('DOMContentLoaded', () => {
        initTable();

        const eleveSelect = document.getElementById('eleve_id');
        eleveSelect.addEventListener('change', async () => {
            const eleveId = eleveSelect.value;
            if (!eleveId) return;

            try {
                const data = await fetchStudentData(eleveId);
                fillHeader(data);
            } catch (e) {
                console.error(e);
            }
        });

        document.getElementById('addDisciplineBtn').addEventListener('click', () => addDiscipline());

        if (eleveSelect.value) {
            eleveSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endpush