@extends(($pdfMode ?? false) ? 'client.layouts.pdf' : 'client.layouts.app')

@section('title', 'Bulletin de notes')

@section('content')
@php
    $school = $bulletin->etablissement;
    $student = $bulletin->eleve;
    $isPdf = $pdfMode ?? false;
    $schoolLogo = $isPdf ? ($pdfSchoolLogo ?? null) : $school?->logo_url;
    $photo = $isPdf ? ($pdfStudentPhoto ?? null) : ($student?->photo_url ?: asset('vendor/adminlte/dist/img/AdminLTELogo.png'));
    $terms = ['t1' => 'Trim1', 't2' => 'Trim2', 't3' => 'Trim3'];
    $cards = collect($annualBulletins ?? [$bulletin->trimestre => $bulletin]);
    $disciplines = $cards->flatMap(fn ($card) => $card->disciplines)->unique('discipline')->sortBy('discipline')->values();
    $termDiscipline = fn ($term, $name) => $cards->get($term)?->disciplines->firstWhere('discipline', $name);
    $annualAverage = $cards->pluck('moyenne_generale')->filter(fn ($value) => $value !== null)->avg();
@endphp

@if(! $isPdf)
    {{-- Header d'actions (écran uniquement) --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('client.bulletin.index') }}" class="w-9 h-9 rounded-xl bg-white border border-gray-100 hover:bg-gray-100 flex items-center justify-center transition flex-shrink-0" title="Retour">
                <span class="material-symbols-outlined text-gray-600 text-base">arrow_back</span>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Bulletin de notes</h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $student?->nom }} {{ $student?->prenom }} — {{ $bulletin->classe?->nom ?? 'N/A' }} — {{ strtoupper($bulletin->trimestre) }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('client.bulletin.download-pdf', $bulletin) }}"
               class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-xs font-semibold transition shadow-sm">
                <span class="material-symbols-outlined text-sm">download</span>
                Télécharger
            </a>
            <button type="button" onclick="window.print()"
                    class="inline-flex items-center gap-1.5 bg-white border border-gray-200 hover:bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-xs font-semibold transition">
                <span class="material-symbols-outlined text-sm">print</span>
                Imprimer
            </button>
        </div>
    </div>

    {{-- Stats rapides --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <span class="material-symbols-outlined text-lg">calculate</span>
                </div>
                <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Moyenne</span>
            </div>
            <h3 class="text-xl sm:text-2xl font-bold {{ ($annualAverage ?? $bulletin->moyenne_generale) >= 10 ? 'text-emerald-600' : 'text-rose-600' }}">
                {{ number_format((float) ($annualAverage ?? $bulletin->moyenne_generale), 2, ',', ' ') }}
            </h3>
            <p class="text-xs text-gray-500 mt-0.5">/20</p>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                    <span class="material-symbols-outlined text-lg">military_tech</span>
                </div>
                <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Rang</span>
            </div>
            <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $bulletin->rang ?: '—' }}</h3>
            <p class="text-xs text-gray-500 mt-0.5">sur {{ $bulletin->classe?->capacite ?? '—' }}</p>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                    <span class="material-symbols-outlined text-lg">menu_book</span>
                </div>
                <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Disciplines</span>
            </div>
            <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $disciplines->count() }}</h3>
            <p class="text-xs text-gray-500 mt-0.5">Évaluées</p>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 rounded-xl bg-violet-50 flex items-center justify-center text-violet-600">
                    <span class="material-symbols-outlined text-lg">event_busy</span>
                </div>
                <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Absences</span>
            </div>
            <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $bulletin->absences ?? 0 }}</h3>
            <p class="text-xs text-gray-500 mt-0.5">Non justifiées</p>
        </div>
    </div>

    {{-- Carte bulletin écran --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                <span class="material-symbols-outlined text-base">description</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Aperçu du bulletin</h3>
                <p class="text-[11px] text-gray-500">Format officiel — {{ $bulletin->anneeAcademique?->libelle ?? 'N/A' }}</p>
            </div>
        </div>

        <div class="p-4 sm:p-6 bg-gray-50/50">
            <article class="bulletin mx-auto">
                <table class="header-table">
                    <colgroup>
                        <col width="16%">
                        <col width="42%">
                        <col width="42%">
                    </colgroup>
                    <tr>
                        <td class="center">
                            @if($schoolLogo)
                                <img class="logo" src="{{ $schoolLogo }}" alt="Logo">
                            @endif
                        </td>
                        <td class="center">
                            <span class="school-name">{{ $school?->nom ?? 'Établissement' }}</span>
                            <br><br>
                            <strong>Adresse :</strong> {{ $school?->adresse ?? '—' }}
                            <br>
                            <strong>Téléphone :</strong> {{ $school?->telephone ?? '—' }}
                        </td>
                        <td class="center">
                            <strong>REPUBLIQUE DE CÔTE D'IVOIRE</strong>
                            <br>
                            MINISTERE DE L'ÉDUCATION NATIONALE
                            <br>
                            ET DE L'ENSEIGNEMENT TECHNIQUE
                            <br><br>
                            <em>Union – Discipline – Travail</em>
                        </td>
                    </tr>
                </table>

                <div class="period-band">
                    Bulletin du {{ strtoupper($bulletin->trimestre) }}
                    <span style="float:right">
                        Année scolaire&nbsp;&nbsp; {{ $bulletin->anneeAcademique?->libelle ?? '—' }}
                    </span>
                </div>

                <table class="student-table">
                    <colgroup>
                        <col width="33%">
                        <col width="33%">
                        <col width="20%">
                        <col width="14%">
                    </colgroup>
                    <tr>
                        <td colspan="3">
                            <strong>Nom et Prénoms de l'élève :</strong> {{ $student?->nom }} {{ $student?->prenom }}
                        </td>
                        <td rowspan="5" class="student-photo-cell">
                            @if($photo)
                                <img class="photo" src="{{ $photo }}" alt="Photo">
                            @else
                                Photo
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><strong>N° Mle :</strong> {{ $student?->matricule ?? '—' }}</td>
                        <td>Interne : {{ $student?->interne ? 'Oui' : 'Non' }}</td>
                        <td>Affecté(e) : {{ $student?->affecte ? 'Oui' : 'Non' }}</td>
                    </tr>
                    <tr>
                        <td>Classe : {{ $bulletin->classe?->nom ?? '—' }}</td>
                        <td>Sexe : {{ $student?->sexe ?? '—' }}</td>
                        <td>Nationalité : {{ $student?->nationalite ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>Effectif : {{ $bulletin->classe?->capacite ?? '—' }}</td>
                        <td colspan="2">Né(e) le : {{ $student?->date_naissance?->format('d/m/Y') ?? '—' }} à {{ $student?->lieu_naissance ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>Absences : {{ $bulletin->absences ?? 0 }}</td>
                        <td colspan="2">Total d'heures : {{ $bulletin->total_heures ?? 0 }}</td>
                    </tr>
                </table>

                <table class="grades">
                    <colgroup>
                        <col width="27%">
                        <col width="8.5%">
                        <col width="6.5%">
                        <col width="8.5%">
                        <col width="6%">
                        <col width="14%">
                        <col width="20%">
                        <col width="10%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>DISCIPLINES</th>
                            <th>Moy.</th>
                            <th>Coef</th>
                            <th>Moy*Coef</th>
                            <th>Rang</th>
                            <th>Appréciations</th>
                            <th>PROFESSEURS</th>
                            <th>Signature</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($disciplines as $discipline)
                            @php
                                $latest = $cards->flatMap(fn ($card) => $card->disciplines)->where('discipline', $discipline->discipline)->last();
                                $entry = $bulletin->disciplines->firstWhere('discipline', $discipline->discipline) ?? $latest;
                            @endphp
                            <tr>
                                <td class="subject strong">{{ $discipline->discipline }}</td>
                                <td class="center">{{ $entry?->moyenne !== null ? number_format($entry->moyenne, 2, ',', ' ') : '—' }}</td>
                                <td class="center">{{ $entry?->coefficient ?? '—' }}</td>
                                <td class="center">{{ $entry?->moyenne_coefficient !== null ? number_format($entry->moyenne_coefficient, 2, ',', ' ') : '—' }}</td>
                                <td class="center">{{ $entry?->rang ?: '—' }}</td>
                                <td class="center">{{ $entry?->mention ?? '—' }}</td>
                                <td>{{ $entry?->professeur ?? '—' }}</td>
                                <td>{{ $entry?->signature ?? '' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="center">Aucune discipline enregistrée.</td>
                            </tr>
                        @endforelse
                        <tr class="subtotal">
                            <td>MOYENNE</td>
                            <td class="center">{{ number_format((float) $bulletin->moyenne_generale, 2, ',', ' ') }}</td>
                            <td class="center">{{ number_format((float) $bulletin->total_coefficients, 2, ',', ' ') }}</td>
                            <td class="center">{{ number_format((float) $bulletin->total_points, 2, ',', ' ') }}</td>
                            <td class="center">{{ $bulletin->rang ?: '—' }}</td>
                            <td colspan="3"></td>
                        </tr>
                        <tr class="total">
                            <td>TOTAUX</td>
                            <td colspan="7"></td>
                        </tr>
                    </tbody>
                </table>

                <table class="summary">
                    <colgroup>
                        <col width="34%">
                        <col width="32%">
                        <col width="34%">
                    </colgroup>
                    <tr>
                        <td>
                            <span class="summary-title">Assiduité</span>
                            Total d'heures d'absence : {{ $bulletin->total_heures ?? 0 }}
                            <br>
                            Non justifiées : {{ $bulletin->absences ?? 0 }}
                        </td>
                        <td class="center">
                            <span class="summary-title">Moyenne trimestrielle</span>
                            <strong style="font-size:16px">{{ number_format((float) ($annualAverage ?? $bulletin->moyenne_generale), 2, ',', ' ') }} /20</strong>
                            <br><br>
                            Rang : <strong>{{ $bulletin->rang ?: '—' }}</strong> sur {{ $bulletin->classe?->capacite ?? '—' }}
                        </td>
                        <td>
                            <span class="summary-title">Résultats de classe</span>
                            {{ $bulletin->resultat_classe ?? $bulletin->observation_conseil ?? '—' }}
                        </td>
                    </tr>
                </table>

                <table class="bottom">
                    <colgroup>
                        <col width="34%">
                        <col width="32%">
                        <col width="34%">
                    </colgroup>
                    <tr>
                        <td>
                            <span class="summary-title">Mentions du conseil de classe</span>
                            <strong>DISTINCTIONS</strong>
                            <br>
                            {{ collect($bulletin->distinctions ?? [])->filter(fn($item) => is_scalar($item))->implode(' · ') ?: 'Aucune distinction' }}
                        </td>
                        <td class="center">
                            <span class="summary-title">Décision de fin d'année</span>
                            <div class="signature-space"></div>
                            <strong><em>{{ $bulletin->decision ?? '—' }}</em></strong>
                        </td>
                        <td class="center">
                            <span class="summary-title">Directeur</span>
                            Fait le {{ $bulletin->date?->format('d/m/Y') ?? now()->format('d/m/Y') }}
                        </td>
                    </tr>
                </table>

                <div class="footer">
                    {{ $school?->nom ? strtoupper($school->nom) : '' }} — {{ $school?->adresse ?? '' }}
                </div>
            </article>
        </div>
    </div>
@else
    {{-- Mode PDF : rendu strict identique à l'original --}}
    <article class="bulletin">
        <table class="header-table">
            <colgroup>
                <col width="16%">
                <col width="42%">
                <col width="42%">
            </colgroup>
            <tr>
                <td class="center">
                    @if($schoolLogo)
                        <img class="logo" src="{{ $schoolLogo }}" alt="Logo">
                    @endif
                </td>
                <td class="center">
                    <span class="school-name">{{ $school?->nom ?? 'Établissement' }}</span>
                    <br><br>
                    <strong>Adresse :</strong> {{ $school?->adresse ?? '—' }}
                    <br>
                    <strong>Téléphone :</strong> {{ $school?->telephone ?? '—' }}
                </td>
                <td class="center">
                    <strong>REPUBLIQUE DE CÔTE D'IVOIRE</strong>
                    <br>
                    MINISTERE DE L'ÉDUCATION NATIONALE
                    <br>
                    ET DE L'ENSEIGNEMENT TECHNIQUE
                    <br><br>
                    <em>Union – Discipline – Travail</em>
                </td>
            </tr>
        </table>

        <div class="period-band">
            Bulletin du {{ strtoupper($bulletin->trimestre) }}
            <span style="float:right">
                Année scolaire&nbsp;&nbsp; {{ $bulletin->anneeAcademique?->libelle ?? '—' }}
            </span>
        </div>

        <table class="student-table">
            <colgroup>
                <col width="33%">
                <col width="33%">
                <col width="20%">
                <col width="14%">
            </colgroup>
            <tr>
                <td colspan="3">
                    <strong>Nom et Prénoms de l'élève :</strong> {{ $student?->nom }} {{ $student?->prenom }}
                </td>
                <td rowspan="5" class="student-photo-cell">
                    @if($photo)
                        <img class="photo" src="{{ $photo }}" alt="Photo">
                    @else
                        Photo
                    @endif
                </td>
            </tr>
            <tr>
                <td><strong>N° Mle :</strong> {{ $student?->matricule ?? '—' }}</td>
                <td>Interne : {{ $student?->interne ? 'Oui' : 'Non' }}</td>
                <td>Affecté(e) : {{ $student?->affecte ? 'Oui' : 'Non' }}</td>
            </tr>
            <tr>
                <td>Classe : {{ $bulletin->classe?->nom ?? '—' }}</td>
                <td>Sexe : {{ $student?->sexe ?? '—' }}</td>
                <td>Nationalité : {{ $student?->nationalite ?? '—' }}</td>
            </tr>
            <tr>
                <td>Effectif : {{ $bulletin->classe?->capacite ?? '—' }}</td>
                <td colspan="2">Né(e) le : {{ $student?->date_naissance?->format('d/m/Y') ?? '—' }} à {{ $student?->lieu_naissance ?? '—' }}</td>
            </tr>
            <tr>
                <td>Absences : {{ $bulletin->absences ?? 0 }}</td>
                <td colspan="2">Total d'heures : {{ $bulletin->total_heures ?? 0 }}</td>
            </tr>
        </table>

        <table class="grades">
            <colgroup>
                <col width="27%">
                <col width="8.5%">
                <col width="6.5%">
                <col width="8.5%">
                <col width="6%">
                <col width="14%">
                <col width="20%">
                <col width="10%">
            </colgroup>
            <thead>
                <tr>
                    <th>DISCIPLINES</th>
                    <th>Moy.</th>
                    <th>Coef</th>
                    <th>Moy*Coef</th>
                    <th>Rang</th>
                    <th>Appréciations</th>
                    <th>PROFESSEURS</th>
                    <th>Signature</th>
                </tr>
            </thead>
            <tbody>
                @forelse($disciplines as $discipline)
                    @php
                        $latest = $cards->flatMap(fn ($card) => $card->disciplines)->where('discipline', $discipline->discipline)->last();
                        $entry = $bulletin->disciplines->firstWhere('discipline', $discipline->discipline) ?? $latest;
                    @endphp
                    <tr>
                        <td class="subject strong">{{ $discipline->discipline }}</td>
                        <td class="center">{{ $entry?->moyenne !== null ? number_format($entry->moyenne, 2, ',', ' ') : '—' }}</td>
                        <td class="center">{{ $entry?->coefficient ?? '—' }}</td>
                        <td class="center">{{ $entry?->moyenne_coefficient !== null ? number_format($entry->moyenne_coefficient, 2, ',', ' ') : '—' }}</td>
                        <td class="center">{{ $entry?->rang ?: '—' }}</td>
                        <td class="center">{{ $entry?->mention ?? '—' }}</td>
                        <td>{{ $entry?->professeur ?? '—' }}</td>
                        <td>{{ $entry?->signature ?? '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="center">Aucune discipline enregistrée.</td>
                    </tr>
                @endforelse
                <tr class="subtotal">
                    <td>MOYENNE</td>
                    <td class="center">{{ number_format((float) $bulletin->moyenne_generale, 2, ',', ' ') }}</td>
                    <td class="center">{{ number_format((float) $bulletin->total_coefficients, 2, ',', ' ') }}</td>
                    <td class="center">{{ number_format((float) $bulletin->total_points, 2, ',', ' ') }}</td>
                    <td class="center">{{ $bulletin->rang ?: '—' }}</td>
                    <td colspan="3"></td>
                </tr>
                <tr class="total">
                    <td>TOTAUX</td>
                    <td colspan="7"></td>
                </tr>
            </tbody>
        </table>

        <table class="summary">
            <colgroup>
                <col width="34%">
                <col width="32%">
                <col width="34%">
            </colgroup>
            <tr>
                <td>
                    <span class="summary-title">Assiduité</span>
                    Total d'heures d'absence : {{ $bulletin->total_heures ?? 0 }}
                    <br>
                    Non justifiées : {{ $bulletin->absences ?? 0 }}
                </td>
                <td class="center">
                    <span class="summary-title">Moyenne trimestrielle</span>
                    <strong style="font-size:16px">{{ number_format((float) ($annualAverage ?? $bulletin->moyenne_generale), 2, ',', ' ') }} /20</strong>
                    <br><br>
                    Rang : <strong>{{ $bulletin->rang ?: '—' }}</strong> sur {{ $bulletin->classe?->capacite ?? '—' }}
                </td>
                <td>
                    <span class="summary-title">Résultats de classe</span>
                    {{ $bulletin->resultat_classe ?? $bulletin->observation_conseil ?? '—' }}
                </td>
            </tr>
        </table>

        <table class="bottom">
            <colgroup>
                <col width="34%">
                <col width="32%">
                <col width="34%">
            </colgroup>
            <tr>
                <td>
                    <span class="summary-title">Mentions du conseil de classe</span>
                    <strong>DISTINCTIONS</strong>
                    <br>
                    {{ collect($bulletin->distinctions ?? [])->filter(fn($item) => is_scalar($item))->implode(' · ') ?: 'Aucune distinction' }}
                </td>
                <td class="center">
                    <span class="summary-title">Décision de fin d'année</span>
                    <div class="signature-space"></div>
                    <strong><em>{{ $bulletin->decision ?? '—' }}</em></strong>
                </td>
                <td class="center">
                    <span class="summary-title">Directeur</span>
                    Fait le {{ $bulletin->date?->format('d/m/Y') ?? now()->format('d/m/Y') }}
                </td>
            </tr>
        </table>

        <div class="footer">
            {{ $school?->nom ? strtoupper($school->nom) : '' }} — {{ $school?->adresse ?? '' }}
        </div>
    </article>
@endif

{{-- Styles bulletin (obligatoires pour le rendu PDF ET pour l'aperçu écran) --}}
<style>
    @if($isPdf)
    @page { margin: 8mm; size: A4 portrait; }
    @endif
    .bulletin { width: 190mm; max-width: 100%; margin: 0 auto; font-size: 10px; line-height: 1.15; font-family: "Times New Roman", Times, serif; color: #111; background: #fff; }
    .bulletin table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .bulletin td, .bulletin th { border: 1px solid #111; padding: 3px 4px; vertical-align: middle; }
    .bulletin th { background: #d9d9d9; color: #111; font-family: Arial, Helvetica, sans-serif; font-size: 9px; font-weight: bold; text-align: center; }
    .bulletin .center { text-align: center; } .bulletin .right { text-align: right; } .bulletin .strong { font-weight: bold; }
    .bulletin .logo { width: 72px; height: 72px; object-fit: contain; } .bulletin .photo { width: 78px; height: 94px; object-fit: cover; border: 1px solid #111; }
    .bulletin .header-table td { height: 76px; } .bulletin .school-name { font-size: 15px; font-weight: bold; }
    .bulletin .bulletin-title { font-family: Arial, Helvetica, sans-serif; font-size: 16px; font-weight: bold; }
    .bulletin .period-band { background: #d9d9d9; font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: bold; padding: 5px; border: 1px solid #111; border-top: 0; }
    .bulletin .student-table td { height: 22px; } .bulletin .student-photo-cell { text-align: center; }
    .bulletin .grades td { height: 19px; } .bulletin .grades .subject { font-family: Arial, Helvetica, sans-serif; font-size: 9px; }
    .bulletin .subtotal td, .bulletin .total td { background: #d9d9d9; font-weight: bold; }
    .bulletin .summary td { height: 78px; vertical-align: top; } .bulletin .summary-title { display: block; font-weight: bold; text-decoration: underline; text-align: center; margin-bottom: 5px; }
    .bulletin .bottom td { height: 100px; vertical-align: top; }
    .bulletin .signature-line { border-top: 1px solid #111; margin: 35px 12px 6px; } .bulletin .signature-space { height: 35px; }
    .bulletin .footer { margin-top: 8px; text-align: center; font-family: Arial, Helvetica, sans-serif; font-size: 8px; }

    @if($isPdf)
    body { margin: 0; padding: 0; color: #111; background: #fff; font-family: "Times New Roman", Times, serif; }
    * { box-sizing: border-box; }
    @endif
    @media print {
        .no-print, nav, aside, header { display: none !important; }
        body, main { margin: 0 !important; padding: 0 !important; background: #fff !important; }
        .bulletin { width: 190mm; }
        .bg-gray-50\/50 { background: #fff !important; padding: 0 !important; }
        .bg-white.rounded-2xl { border: none !important; box-shadow: none !important; }
    }
</style>

@if($printMode ?? false)
<script>
    window.addEventListener('load', function () { window.print(); });
</script>
@endif
@endsection