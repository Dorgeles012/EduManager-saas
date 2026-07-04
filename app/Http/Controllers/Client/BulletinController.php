<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreBulletinRequest;
use App\Models\AnneeAcademique;
use App\Models\Bulletin;
use App\Models\BulletinDiscipline;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Etablissement;
use App\Models\Enseignant;
use App\Models\Matiere;
use App\Models\Niveau;
use App\Models\Series;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class BulletinController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $query = Bulletin::where('tenant_id', $tenantId)->with(['eleve', 'classe', 'anneeAcademique']);
        $query->when($request->filled('classe_id'), fn ($q) => $q->where('classe_id', $request->integer('classe_id')))
            ->when($request->filled('trimestre'), fn ($q) => $q->where('trimestre', $request->string('trimestre')))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = (string) $request->string('q');
                $q->whereHas('eleve', fn ($student) => $student->where('nom', 'like', "%{$term}%")
                    ->orWhere('prenom', 'like', "%{$term}%")->orWhere('matricule', 'like', "%{$term}%"));
            });

        $reportCards = $query->latest()->paginate(15)->withQueryString()->through(fn (Bulletin $item) => [
            'id' => $item->id, 'student_name' => trim($item->eleve?->nom.' '.$item->eleve?->prenom),
            'matricule' => $item->eleve?->matricule, 'class_name' => $item->classe?->nom,
            'period' => strtoupper($item->trimestre), 'average' => $item->moyenne_generale,
            'appreciation' => $item->decision ?? $item->observation_conseil ?? 'Non renseignée', 'rank' => $item->rang,
        ]);

        return view('client.bulletin', [
            'reportCards' => $reportCards,
            'classes' => Classe::where('tenant_id', $tenantId)->orderBy('nom')->get()->map(fn ($c) => ['id' => $c->id, 'name' => $c->nom]),
            'totalStudents' => Eleve::where('tenant_id', $tenantId)->count(),
            'totalClasses' => Classe::where('tenant_id', $tenantId)->count(),
            'totalPeriods' => Bulletin::where('tenant_id', $tenantId)->distinct()->count('trimestre'),
        ]);
    }

    public function show(Bulletin $bulletin)
    {
        // Non utilisé actuellement par les routes existantes
        $this->ensureTenantOwns($bulletin);
        return response()->json($bulletin->load(['eleve', 'classe', 'anneeAcademique', 'disciplines']));
    }

    public function edit($bulletin)
    {
        return redirect()->route('client.bulletin.show', ['bulletin' => $bulletin]);
    }

    public function update(Request $request, $bulletin)
    {
        return redirect()->route('client.bulletin.show', ['bulletin' => $bulletin])
            ->with('warning', 'Mise à jour non disponible pour le moment.');
    }

    public function destroy(Bulletin $bulletin)
    {
        $this->ensureTenantOwns($bulletin);
        $bulletin->delete();
        return redirect()->route('client.bulletin.index')
            ->with('success', 'Bulletin supprimé avec succès.');
    }

    public function download($bulletin)
    {
        return redirect()->route('client.bulletin.show', ['bulletin' => $bulletin])
            ->with('warning', 'Téléchargement non disponible pour le moment.');
    }


    public function create()
    {
        $tenantId = auth()->user()->tenant_id;

        $etablissement = Etablissement::query()
            ->where('tenant_id', $tenantId)
            ->latest('id')
            ->first();

        $anneesAcademiques = AnneeAcademique::query()
            ->where('tenant_id', $tenantId)
            ->orderBy('id')
            ->get();

        $classes = Classe::query()
            ->where('tenant_id', $tenantId)
            ->when($etablissement?->id, fn ($q) => $q->where('etablissement_id', $etablissement->id))
            ->with('niveau')
            ->orderBy('id')
            ->get();

        $niveaux = Niveau::query()
            ->where('tenant_id', $tenantId)
            ->when($etablissement?->id, fn ($q) => $q->where('etablissement_id', $etablissement->id))
            ->orderBy('nom')
            ->get();

        $eleves = Eleve::query()
            ->where('tenant_id', $tenantId)
            ->when($etablissement?->id, fn ($q) => $q->where('etablissement_id', $etablissement->id))
            ->with(['classe.niveau', 'serie'])
            ->orderBy('nom')
            ->get();

        // Valeur initiale utile si tu veux filtrer par classe plus tard
        $classeInitial = null;

        // Données additionnelles (pour extensibilité)
        $matieres = Matiere::query()->where('tenant_id', $tenantId)->get();
        $enseignants = Enseignant::query()->where('tenant_id', $tenantId)->get();
        $series = Series::query()->where('tenant_id', $tenantId)
            ->whereHas('classes', fn ($q) => $q->whereIn('classes.id', $classes->pluck('id')))
            ->with('classes:id')
            ->orderBy('nom_serie')->get();

        return view('client.bulletin.formulaire', [
            'etablissement' => $etablissement,
            'eleves' => $eleves,
            'anneesAcademiques' => $anneesAcademiques,
            'classes' => $classes,
            'niveaux' => $niveaux,
            'classeInitial' => $classeInitial,
            'matieres' => $matieres,
            'enseignants' => $enseignants,
            'series' => $series,
        ]);
    }

    /**
     * Retourne les données nécessaires au remplissage automatique du header.
     * Exemple attendu côté JS : { nom, prenoms, matricule, classe, ... }
     */
    public function studentData(Request $request)

    {
        $data = $request->validate([
            'eleve_id' => ['required', 'integer'],
            'annee_academique_id' => ['nullable', 'integer'],
            'trimestre' => ['nullable', 'string'],
        ]);

        $tenantId = auth()->user()->tenant_id;

        $eleve = Eleve::query()
            ->where('tenant_id', $tenantId)
            ->with(['classe.niveau', 'serie'])
            ->findOrFail($data['eleve_id']);

        $etablissement = Etablissement::query()
            ->where('tenant_id', $tenantId)
            ->find($eleve->etablissement_id);

        $annee = isset($data['annee_academique_id'])
            ? AnneeAcademique::query()->where('tenant_id', $tenantId)->find($data['annee_academique_id'])
            : null;

        $logoUrl = null;
        if ($etablissement?->logo) {
            // Petit fallback: si getLogoUrlAttribute n'existe pas, on renvoie un chemin Storage.
            if (method_exists($etablissement, 'getLogoUrlAttribute')) {
                $logoUrl = $etablissement->getLogoUrlAttribute();
            } else {
                $path = ltrim($etablissement->logo, '/');
                $logoUrl = Storage::disk('public')->exists($path)
                    ? Storage::url($path)
                    : null;
            }
        }

        // effectif : capacité de la classe associée à l'élève
        $effectif = $eleve->classe?->capacite ?? null;

        // 1) Si un bulletin existe déjà pour (élève + année académique + période), on charge depuis DB.
        $bulletin = null;
        $disciplinesFromBulletin = collect();

        if (!empty($data['annee_academique_id']) && !empty($data['trimestre'])) {
            $bulletin = Bulletin::query()
                ->where('tenant_id', $tenantId)
                ->where('eleve_id', $eleve->id)
                ->where('annee_academique_id', (int) $data['annee_academique_id'])
                ->where('trimestre', $data['trimestre'])
                ->with('disciplines')
                ->first();

            if ($bulletin) {
                $disciplinesFromBulletin = $bulletin->disciplines
                    ->map(fn ($d) => [
                        'matiere_id' => $d->matiere_id,
                        'discipline' => $d->discipline,
                        'interrogation' => $d->interrogation,
                        'devoir' => $d->devoir,
                        'composition' => $d->composition,
                        'moyenne' => $d->moyenne,
                        'coefficient' => $d->coefficient,
                        'moyenne_coefficient' => $d->moyenne_coefficient,
                        'rang' => $d->rang,
                        'appréciation' => $d->appréciation,
                        'professeur' => $d->professeur,
                        'signature' => $d->signature,
                    ]);
            }
        }

        // 2) Si aucun bulletin n'existe : on génère les disciplines depuis la table `notes`.
        //    NB: La table notes actuelle ne contient pas de `classe_id` proprement liée au nom de la période,
        //    on utilise donc `periode = trimestre` et on filtre sur eleve_id + classe_id.
        //    Rang/Appréciation: calculés approximativement à partir de la moyenne générale.
        $generatedFromNotes = false;
        if (!$bulletin && empty($disciplinesFromBulletin->all()) && !empty($data['trimestre'])) {
            $classeId = $eleve->classe_id;

            // matières + coefficients (via table matieres)
            $matieres = $eleve->serie
                ? $eleve->serie->matieres()->where('matieres.tenant_id', $tenantId)->orderBy('matieres.nom')->get()
                : collect();

            $notes = DB::table('notes')
                ->where('tenant_id', $tenantId)
                ->where('eleve_id', $eleve->id)
                ->where('classe_id', $classeId)
                ->where('periode', $data['trimestre'])
                ->get();

            // Map note par matiere_id
            $notesByMatiere = $notes->keyBy('matiere_id');

            $disciplinesRows = [];
            $totalCoef = 0.0;
            $totalPoints = 0.0;

            foreach ($matieres as $matiere) {
                $noteRow = $notesByMatiere->get($matiere->id);
                $noteValue = $noteRow?->note;

                $coef = (float) $matiere->pivot->coefficient;
                $moyenne = $noteValue !== null ? (float) $noteValue : null;

                $mc = null;
                if ($moyenne !== null && $coef > 0) {
                    $mc = $moyenne * $coef;
                    $totalCoef += $coef;
                    $totalPoints += $mc;
                }

                $disciplinesRows[] = [
                    'matiere_id' => $matiere->id,
                    'discipline' => $matiere->nom,
                    'moyenne' => $moyenne,
                    'coefficient' => $coef,
                    'moyenne_coefficient' => $mc !== null ? round($mc, 2) : null,
                    'rang' => 0,
                    'appréciation' => $noteRow?->appreciation,
                    'professeur' => null,
                    'signature' => null,
                ];
            }

            $disciplinesFromBulletin = collect($disciplinesRows);
            $generatedFromNotes = true;
        }

        $moyenneGeneraleFromGenerated = null;
        if (!$bulletin && $generatedFromNotes) {
            $totalCoef = 0.0;
            $totalPoints = 0.0;
            foreach ($disciplinesFromBulletin as $d) {
                $coef = isset($d['coefficient']) ? (float) $d['coefficient'] : 0.0;
                $moyenne = isset($d['moyenne']) && $d['moyenne'] !== null ? (float) $d['moyenne'] : null;
                if ($moyenne !== null && $coef > 0) {
                    $totalCoef += $coef;
                    $totalPoints += $moyenne * $coef;
                }
            }
            $moyenneGeneraleFromGenerated = $totalCoef > 0 ? round($totalPoints / $totalCoef, 2) : null;

            // Rang: approximation sur tous les élèves de la même classe et période.
            // (On calcule leur moyenne à partir de notes, puis on classe.)
            $rang = null;
            if ($moyenneGeneraleFromGenerated !== null) {
                $classeId = $eleve->classe_id;
                $notesAll = DB::table('notes')
                    ->where('tenant_id', $tenantId)
                    ->where('classe_id', $classeId)
                    ->whereIn('eleve_id', Eleve::query()
                        ->where('tenant_id', $tenantId)
                        ->where('classe_id', $classeId)
                        ->where('id_serie', $eleve->id_serie)
                        ->select('id'))
                    ->where('periode', $data['trimestre'])
                    ->get()
                    ->groupBy('eleve_id');

                // coefficients par matiere
                $coefByMatiere = $matieres->mapWithKeys(fn ($m) => [(int) $m->id => (float) $m->pivot->coefficient]);

                $moyennes = [];
                foreach ($notesAll as $eleveId => $rows) {
                    $tCoef = 0.0;
                    $tPoints = 0.0;
                    foreach ($rows as $r) {
                        $coef = $coefByMatiere->get((int)$r->matiere_id, 1.0);
                        $note = $r->note !== null ? (float)$r->note : null;
                        if ($note !== null && $coef > 0) {
                            $tCoef += $coef;
                            $tPoints += $note * $coef;
                        }
                    }
                    $moyennes[(int)$eleveId] = $tCoef > 0 ? ($tPoints / $tCoef) : null;
                }

                // Trier décroissant, gérer null
                $sorted = collect($moyennes)->filter(fn ($v) => $v !== null)->sortDesc()->values();
                $rankIndex = $sorted->search(function ($v) use ($moyenneGeneraleFromGenerated) {
                    return ((float)$v) === ((float)$moyenneGeneraleFromGenerated);
                });
                $rang = $rankIndex === false ? null : ($rankIndex + 1);
            }

        }

        $payload = [
            'etablissement_logo_url' => $logoUrl,
            'etablissement' => [
                'nom' => $etablissement?->nom,
                'adresse' => $etablissement?->adresse,
                'telephone' => $etablissement?->telephone,
                'email' => $etablissement?->email,
                'devise' => null,
            ],
            'annee_academique' => $annee?->libelle,

            'nom' => $eleve->nom,
            'prenoms' => $eleve->prenom,
            'matricule' => $eleve->matricule,
            'classe_id' => $eleve->classe_id,
            'classe' => $eleve->classe?->nom,
            'niveau' => $eleve->classe?->niveau?->nom,
            'serie' => $eleve->serie?->nom_serie,
            'effectif' => $effectif,
            'sexe' => $eleve->sexe,
            'nationalite' => $eleve->nationalite ?? null,
            'date_naissance' => optional($eleve->date_naissance)->format('Y-m-d'),
            'lieu_naissance' => $eleve->lieu_naissance,
            'photo_url' => $eleve->getPhotoUrlAttribute(),

            // bulletin
            'bulletin_existant' => (bool) $bulletin,
            'moyenne_generale' => $bulletin?->moyenne_generale,
            'total_coefficients' => $bulletin?->total_coefficients,
            'total_points' => $bulletin?->total_points,
            'rang' => $bulletin?->rang,
            'appreciation' => $bulletin?->appreciation,
            'disciplines' => $disciplinesFromBulletin->values()->all(),
        ];

        // Pour conserver la compatibilité JS actuelle (noms attendus)
        $payload['niveau_id'] = $eleve->niveau_id ?? null;
        $payload['serie_id'] = $eleve->id_serie;
        if (! $bulletin && isset($rang)) {
            $payload['rang'] = $rang;
            $payload['moyenne_generale'] = $moyenneGeneraleFromGenerated;
        }

        return response()->json($payload);
    }

    public function store(StoreBulletinRequest $request)
    {
        $payload = $request->validated();

        $tenantId = auth()->user()->tenant_id;

        return DB::transaction(function () use ($payload, $tenantId) {

            $eleve = Eleve::query()->where('tenant_id', $tenantId)->findOrFail($payload['eleve_id']);

            $classeId = (int) ($payload['classe_id'] ?? $eleve->classe_id);
            $etablissementId = (int) $payload['etablissement_id'];

            $totalCoef = 0;
            $totalPoints = 0;

            $disciplines = Arr::get($payload, 'disciplines', []);

            // La configuration de la série est la source de vérité : toutes ses
            // matières sont conservées et leurs coefficients ne viennent jamais du navigateur.
            $configuredMatieres = $eleve->serie
                ? $eleve->serie->matieres()
                    ->where('matieres.tenant_id', $tenantId)
                    ->orderBy('matieres.nom')
                    ->get()
                : collect();

            if ($configuredMatieres->isNotEmpty()) {
                $submittedByMatiere = collect($disciplines)->keyBy(fn ($row) => (int) ($row['matiere_id'] ?? 0));
                $disciplines = $configuredMatieres->map(function (Matiere $matiere) use ($submittedByMatiere) {
                    $submitted = (array) $submittedByMatiere->get($matiere->id, []);

                    return array_merge($submitted, [
                        'matiere_id' => $matiere->id,
                        'discipline' => $matiere->nom,
                        'coefficient' => (float) $matiere->pivot->coefficient,
                    ]);
                })->values()->all();
            }

            foreach ($disciplines as $i => $disc) {
                $evaluations = collect(['interrogation', 'devoir', 'composition'])
                    ->filter(fn ($field) => isset($disc[$field]) && $disc[$field] !== '')
                    ->map(fn ($field) => (float) $disc[$field]);
                $moyenne = $evaluations->isNotEmpty()
                    ? round($evaluations->avg(), 2)
                    : (isset($disc['moyenne']) && $disc['moyenne'] !== '' ? (float) $disc['moyenne'] : null);
                $coef = isset($disc['coefficient']) ? (float) $disc['coefficient'] : null;
                $disciplines[$i]['moyenne'] = $moyenne;

                if ($moyenne !== null && $coef !== null && $coef > 0) {
                    $mc = $moyenne * $coef;
                    $totalCoef += $coef;
                    $totalPoints += $mc;

                    $disciplines[$i]['moyenne_coefficient'] = round($mc, 2);
                } else {
                    $disciplines[$i]['moyenne_coefficient'] = null;
                }

                // rang par discipline optionnel
                if (isset($disc['rang'])) {
                    $disciplines[$i]['rang'] = (int) $disc['rang'];
                }
            }

            $moyenneGenerale = $totalCoef > 0 ? round($totalPoints / $totalCoef, 2) : null;

            // Résultat de classe / décision : si déjà fournis par l'utilisateur, on les garde.
            $bulletin = Bulletin::updateOrCreate([
                'tenant_id' => $tenantId,
                'eleve_id' => (int) $payload['eleve_id'],
                'annee_academique_id' => (int) $payload['annee_academique_id'],
                'trimestre' => $payload['trimestre'],
            ], [
                'etablissement_id' => $etablissementId,
                'classe_id' => $classeId,

                'total_heures' => (float) ($payload['total_heures'] ?? 0),
                'absences' => (int) ($payload['absences'] ?? 0),
                'rang' => (int) ($payload['rang'] ?? 0),
                'moyenne_generale' => $moyenneGenerale,
                'total_coefficients' => round($totalCoef, 2),
                'total_points' => round($totalPoints, 2),

                'resultat_classe' => $payload['resultat_classe'] ?? null,
                'decision' => $payload['decision'] ?? null,
                'observation_conseil' => $payload['observation_conseil'] ?? null,
                'date' => $payload['date'] ?? null,

                'signature_professeur_principal' => $payload['signature_professeur_principal'] ?? null,
                'signature_directeur' => $payload['signature_directeur'] ?? null,
                'distinctions' => $payload['distinctions'] ?? null,
            ]);

            $bulletin->disciplines()->delete();

            foreach ($disciplines as $disc) {
                BulletinDiscipline::create([
                    'tenant_id' => $tenantId,
                    'bulletin_id' => $bulletin->id,
                    'matiere_id' => $disc['matiere_id'] ?? null,
                    'discipline' => $disc['discipline'],
                    'interrogation' => isset($disc['interrogation']) ? (float) $disc['interrogation'] : null,
                    'devoir' => isset($disc['devoir']) ? (float) $disc['devoir'] : null,
                    'composition' => isset($disc['composition']) ? (float) $disc['composition'] : null,
                    'moyenne' => isset($disc['moyenne']) ? (float) $disc['moyenne'] : null,
                    'coefficient' => (float) $disc['coefficient'],
                    'moyenne_coefficient' => isset($disc['moyenne_coefficient']) ? (float) $disc['moyenne_coefficient'] : null,
                    'rang' => isset($disc['rang']) ? (int) $disc['rang'] : 0,
                    'appréciation' => $disc['appréciation'] ?? null,
                    'professeur' => $disc['professeur'] ?? null,
                    'signature' => $disc['signature'] ?? null,
                ]);
            }

            // Optionnel: redirection vers la liste
            return redirect()->route('client.bulletin.index')
                ->with('success', 'Bulletin enregistré avec succès.');
        });
    }

    private function ensureTenantOwns(Bulletin $bulletin): void
    {
        abort_unless((int) $bulletin->tenant_id === (int) auth()->user()->tenant_id, 404);
    }
}
