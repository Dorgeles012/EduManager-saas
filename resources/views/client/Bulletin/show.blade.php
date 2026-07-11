@extends('client.layouts.app')
@section('title', 'Bulletin de notes')
@section('content')

@php
    $school = $bulletin->etablissement;
    $student = $bulletin->eleve;
    $photo = $student?->photo_url ?: asset('vendor/adminlte/dist/img/AdminLTELogo.png');
    $schoolLogo = $school?->logo_url ?? asset('images/default-school.png');
    
    $terms = ['t1' => 'Trim1', 't2' => 'Trim2', 't3' => 'Trim3'];
    
    $disciplines = collect($annualBulletins ?? [$bulletin->trimestre => $bulletin])
        ->flatMap(fn($card) => $card->disciplines)
        ->unique('discipline')
        ->sortBy('discipline')
        ->values();
        
    $termDiscipline = fn($term, $name) => $annualBulletins?->get($term)?->disciplines->firstWhere('discipline', $name);
    
    $annualAverage = collect($annualBulletins ?? [])
        ->pluck('moyenne_generale')
        ->filter(fn($value) => $value !== null)
        ->avg();
@endphp

<style>
    {{-- Reset pour l'impression --}}
    * { box-sizing: border-box; }
    
    .annual-sheet { 
        max-width: 1100px; margin: auto; background: #fff; color: #111; padding: 4px 10px; 
        font-family: "Times New Roman", Times, serif; font-size: 13px; 
    }
    
    .annual-sheet table.grades-table { 
        width: 100%; border-collapse: collapse; margin-top: 5px; 
    }
    .annual-sheet table.grades-table th, 
    .annual-sheet table.grades-table td { 
        border: 1px solid #222; padding: 3px 4px; vertical-align: middle; line-height: 1.12; 
    }
    .annual-sheet table.grades-table th { 
        font-family: Arial, sans-serif; font-size: 11px; font-weight: 700; text-align: center; 
    }
    .annual-sheet table.grades-table .annual-subtotal td { 
        background: #d9d9d9; font-weight: 700; font-size: 13px; 
    }
    
    {{-- RÉDUCTION DE LA POLICE DE L'EN-TÊTE --}}
    .annual-title { 
        font-family: "Times New Roman", Times, serif; font-size: 14px; font-weight: 800; text-align: center; 
    }
    .annual-header { 
        display: flex; justify-content: space-between; align-items: stretch; 
        border-bottom: 2px solid #222; min-height: 70px; 
    }
    .annual-header > div { padding: 4px; display: flex; flex-direction: column; justify-content: center; align-items: center; }
    
    .annual-header .header-left { flex: 1.2; padding-left: 0; align-items: flex-start; }
    .annual-header .header-center { flex: 0.5; }
    .annual-header .header-right { flex: 1.5; align-items: flex-end; }
    
    .annual-header .school-logo {
        max-width: 70px; max-height: 70px; object-fit: contain; margin-bottom: 3px;
        border: 1px solid #ddd; padding: 3px; background: #fff;
    }
    .annual-header .header-right {
        font-size: 11px; 
        font-weight: bold;
        font-family: "Times New Roman", Times, serif;
        text-align: right;
    }
    .annual-header .header-right .devise {
        font-weight: normal; font-style: italic; margin-top: 4px; font-size: 10px;
    }
    
    .annual-school { padding: 12px 8px; text-align: center; border-bottom: 2px solid #222; }
    .school-name-large { font-size: 20px; font-weight: bold; display: block; margin-bottom: 4px; font-family: "Times New Roman", Times, serif; }
    .school-address-large { font-size: 15px; display: block; font-family: "Times New Roman", Times, serif; }
    
    .annual-student { 
        display: flex; justify-content: space-between; padding: 8px 12px; border-bottom: 2px solid #222; font-size: 14px; 
    }
    .annual-student .student-col { flex: 1; padding: 0 5px; }
    .annual-student .student-col:nth-child(1) { flex: 1.45; }
    .annual-student .student-col:nth-child(2) { flex: 1; }
    .annual-student .student-col:nth-child(3) { flex: 0.9; }
    .annual-student p { margin: 0 0 7px; }
    .annual-photo { width: 65px; height: 78px; object-fit: cover; border: 1px solid #333; float: right; }
    
    .annual-summary { 
        display: flex; justify-content: space-between; text-align: center; 
        border-bottom: 2px solid #222; padding: 15px 0; 
    }
    .annual-summary > div { flex: 1; padding: 0 10px; }
    .annual-summary strong { font-size: 16px; }
    
    .annual-bottom { 
        display: flex; justify-content: space-between; text-align: center; padding: 15px 0; 
    }
    .annual-bottom > section { flex: 1; padding: 0 10px; }
    
    .font-large-title {
        font-size: 18px; font-weight: bold; text-align: center; text-decoration: underline; margin: 0 0 10px 0; display: block;
    }
    .font-big-text { font-size: 18px; }
    .annual-bottom p { margin: 5px 0; }
    .annual-sign { margin-top: 30px; text-align: center; font-weight: bold; }
    
    .no-print { margin-bottom: 18px; }
    
    {{-- CORRECTION DU DÉCALAGE ET IMPRESSION OPTIMALE --}}
    @media print {
        .no-print, nav, aside, header { display: none !important; }
        body { margin: 0 !important; padding: 0 !important; }
        main { padding: 0 !important; margin: 0 !important; }
        
        .annual-sheet { 
            border: none; 
            max-width: 100%; 
            width: 100%; 
            padding: 0 !important;
            margin: 0 !important;
            position: absolute !important;
            top: 2mm !important; /* Petite marge de sécurité pour que l'imprimante imprime bien le haut */
            left: 0 !important;
        }
        
        {{-- Force l'impression des bordures --}}
        .annual-sheet table.grades-table th, 
        .annual-sheet table.grades-table td {
            border: 1px solid #000 !important;
        }
        .annual-header, .annual-school, .annual-student, .annual-summary {
            border-bottom: 2px solid #000 !important;
        }
        
        @page { 
            size: A4 portrait; 
            margin: 0 !important;
        }
    }
</style>

<div class="no-print" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; position:relative; z-index:10;">
    <div>
        <a href="{{ route('client.bulletin.index') }}"
           class="btn btn-outline-secondary"
           title="Retour"
           aria-label="Retour"
           style="display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; padding:0; border-radius:50%;">
            <span class="material-symbols-outlined" style="font-size:20px;">arrow_back</span>
        </a>
    </div>
    <div style="display:flex; gap:8px;">
        {{-- ATTENTION : Retrait de l'attribut 'download' pour éviter les erreurs de serveur --}}
        <a href="{{ route('client.bulletin.download-pdf', $bulletin) }}"
           class="btn btn-success"
           title="Télécharger le PDF"
           aria-label="Télécharger"
           target="_blank"
           rel="noopener noreferrer"
           style="display:inline-flex; align-items:center; gap:6px;">
            <span class="material-symbols-outlined" style="font-size:18px;">download</span>
            Télécharger
        </a>
        <button type="button"
                class="btn btn-outline-primary"
                onclick="window.print(); return false;"
                title="Imprimer"
                aria-label="Imprimer"
                style="display:inline-flex; align-items:center; gap:6px;">
            <span class="material-symbols-outlined" style="font-size:18px;">print</span>
            Imprimer
        </button>
    </div>
</div>

<article class="annual-sheet">
    
    <section class="annual-header">
        <div class="header-left">
            @if($schoolLogo)
                <img src="{{ $schoolLogo }}" alt="Logo établissement" class="school-logo">
            @endif
            <br>
        </div>
        <div class="header-center">
            <div class="annual-title">BULLETIN DE NOTES</div>
        </div>
        <div class="header-right">
            REPUBLIQUE DE COTE D'IVOIRE<br>
            MINISTERE DE L'EDUCATION NATIONALE<br> ET DE L'ENSEIGNEMENT TECHNIQUE
            <div class="devise">Union - Discipline - Travail</div>
        </div>
    </section>

    <section class="annual-school">
        <span class="school-name-large">{{ $school?->nom ?? '—' }}</span>
        <span class="school-address-large">
            Adresse postale : {{ $school?->adresse ?? '—' }} 
            @if($school?->telephone) — Téléphone : {{ $school->telephone }} @endif
        </span>
    </section>

    <section class="annual-student">
        <div class="student-col">
            <p><strong>{{ $student?->nom }} {{ $student?->prenom }}</strong></p>
            <p><strong>Matricule : {{ $student?->matricule ?? '—' }}</strong></p>
            <p>Classe : {{ $bulletin->classe?->nom ?? '—' }}</p>
            <p>Effectif : {{ $bulletin->classe?->capacite ?? '—' }}</p>
        </div>
        <div class="student-col">
            <p>Sexe : {{ $student?->sexe ?? '—' }}</p>
            <p>Né(e) le : {{ $student?->date_naissance?->format('d/m/Y') ?? '—' }}</p>
            <p>Lieu de naissance : {{ $student?->lieu_naissance ?? '—' }}</p>
            <p>Nationalité : {{ $student?->nationalite ?? '—' }}</p>
        </div>
        <div class="student-col">
            <img class="annual-photo" src="{{ $photo }}" alt="Photo élève">
            <p>Période éditée : {{ strtoupper($bulletin->trimestre) }}</p>
            <p>Absences : {{ $bulletin->absences ?? 0 }}</p>
        </div>
    </section>

    <table class="grades-table">
        <thead>
            <tr>
                <th>DISCIPLINES</th>
                <th>Moyenne</th>
                <th>Coef.</th>
                <th>M×C</th>
                <th>Rang</th>
                <th>Mention</th>
                <th>Professeur</th>
                <th>Signature</th>
            </tr>
        </thead>
        <tbody>
            @forelse($disciplines as $discipline)
                @php
                    $values = collect($terms)->map(fn($_label, $term) => $termDiscipline($term, $discipline->discipline));
                    $mean = $values->filter()->avg('moyenne');
                    $latest = $values->filter()->last();
                    $disciplineEntry = $bulletin->disciplines->firstWhere('discipline', $discipline->discipline) ?? $latest;
                @endphp
                <tr>
                    <td><strong>{{ $discipline->discipline }}</strong></td>
                    <td class="text-center">
                        <strong>
                            {{ $disciplineEntry?->moyenne !== null ? number_format($disciplineEntry->moyenne, 2, ',', ' ') : ($mean !== null ? number_format($mean, 2, ',', ' ') : '—') }}
                        </strong>
                    </td>
                    <td class="text-center">{{ $disciplineEntry?->coefficient ?? ($latest?->coefficient ?? '—') }}</td>
                    <td class="text-center">
                        {{ $disciplineEntry?->moyenne_coefficient !== null ? number_format($disciplineEntry->moyenne_coefficient, 2, ',', ' ') : ($latest?->moyenne_coefficient !== null ? number_format($latest->moyenne_coefficient, 2, ',', ' ') : '—') }}
                    </td>
                    <td class="text-center">{{ $disciplineEntry?->rang ?: ($latest?->rang ?: '—') }}</td>
                    <td class="text-center">{{ $disciplineEntry?->mention ?? ($latest?->mention ?? '—') }}</td>
                    <td>{{ $disciplineEntry?->professeur ?? ($latest?->professeur ?? '—') }}</td>
                    <td>{{ $disciplineEntry?->signature ?? ($latest?->signature ?? '') }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center">Aucune discipline enregistrée.</td></tr>
            @endforelse
            <tr class="annual-subtotal">
                <td>RÉSUMÉ</td>
                <td class="text-center">
                    {{ $annualAverage !== null ? number_format($annualAverage, 2, ',', ' ') : number_format((float) $bulletin->moyenne_generale, 2, ',', ' ') }}
                </td>
                <td></td><td></td>
                <td class="text-center">{{ $bulletin->rang ?: '—' }}</td>
                <td></td><td colspan="2"></td>
            </tr>
        </tbody>
    </table>

    <section class="annual-summary">
        <div>
            <span class="font-large-title">Assiduité trimestrielle</span>
            Total d'heures d'absence : {{ $bulletin->total_heures ?? 0 }}<br>
            Justifiées : 0<br>
            Non justifiées : {{ $bulletin->absences ?? 0 }}
        </div>
        <div>
            <span class="font-large-title">Moyenne trimestrielle</span>
            <strong>
                {{ $annualAverage !== null ? number_format($annualAverage, 2, ',', ' ') : number_format((float) $bulletin->moyenne_generale, 2, ',', ' ') }} 
                <small>/20</small>
            </strong><br>
            Rang : <strong>{{ $bulletin->rang ?: '—' }}</strong> sur {{ $bulletin->classe?->capacite ?? '—' }}
        </div>
        <div>
            <span class="font-large-title">Observation du conseil</span>
            {{ $bulletin->observation_conseil ?? 'Aucune observation' }}
        </div>
    </section>

    <section class="annual-bottom">
        <section>
            <span class="font-large-title">Mentions du conseil de classe</span>
            <p><strong>DISTINCTIONS</strong></p>
            <p>{{ collect($bulletin->distinctions ?? [])->implode(' · ') ?: 'Aucune distinction' }}</p>
        </section>
        <section>
            <span class="font-large-title">Décision de fin d'année</span>
            <p class="text-center font-big-text" style="margin-top:15px">
                <em>{{ $bulletin->decision ?? '—' }}</em>
            </p>
        </section>
        <section>
            <span class="font-large-title">Directeur</span>
            <p>Fait le {{ $bulletin->date?->format('d/m/Y') ?? now()->format('d/m/Y') }}</p>
            <p class="annual-sign" style="margin-top:60px">
                {{ $bulletin->signature_directeur ?? 'Nom / signature du directeur' }}
            </p>
        </section>
    </section>

    <p class="text-center" style="margin:12px 0 3px; font-family:'Times New Roman', Times, serif;">
        {{ $school?->nom ? strtoupper($school->nom) : '' }}
    </p>
</article>

@if($printMode ?? false)
    <script>
        window.addEventListener('load', () => window.print())
    </script>
@endif
@endsection