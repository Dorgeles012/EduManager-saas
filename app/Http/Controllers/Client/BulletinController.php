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
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class BulletinController extends Controller
{
    public function index()
    {
        return view('client.bulletin');
    }

    public function show($bulletin)
    {
        // Non utilisé actuellement par les routes existantes
        return view('client.bulletin', compact('bulletin'));
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

    public function destroy($bulletin)
    {
        return redirect()->route('client.bulletin.index')
            ->with('warning', 'Suppression non disponible pour le moment.');
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
            ->orderBy('id')
            ->get();

        $eleves = Eleve::query()
            ->where('tenant_id', $tenantId)
            ->when($etablissement?->id, fn ($q) => $q->where('etablissement_id', $etablissement->id))
            ->with('classe')
            ->orderBy('nom')
            ->get();

        // Valeur initiale utile si tu veux filtrer par classe plus tard
        $classeInitial = null;

        // Données additionnelles (pour extensibilité)
        $matieres = Matiere::query()->where('tenant_id', $tenantId)->get();
        $enseignants = Enseignant::query()->where('tenant_id', $tenantId)->get();

return view('client.Bulletin.formulaire', [
            'etablissement' => $etablissement,
            'eleves' => $eleves,
            'anneesAcademiques' => $anneesAcademiques,
            'classes' => $classes,
            'classeInitial' => $classeInitial,
            'matieres' => $matieres,
            'enseignants' => $enseignants,
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
            ->with(['classe'])
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

        // effectif : disponible via classe_id (pas de modèle de classe/ effectif dans le repo, donc calcul minimal)
        $effectif = $eleve->classe?->effectif ?? null;

        return response()->json([
            'etablissement_logo_url' => $logoUrl,
            'etablissement' => [
                'nom' => $etablissement?->nom,
                'adresse' => $etablissement?->adresse,
                'telephone' => $etablissement?->telephone,
            ],
            'annee_academique' => $annee,
            'nom' => $eleve->nom,
            'prenoms' => $eleve->prenom,
            'matricule' => $eleve->matricule,
            'classe_id' => $eleve->classe_id,
            'classe' => $eleve->classe?->nom,
            'effectif' => $effectif,
            'sexe' => $eleve->sexe,
            // champs non présents dans le modèle Eleve actuel => on renvoie null par défaut
            'nationalite' => $eleve->nationalite ?? null,
            'date_naissance' => optional($eleve->date_naissance)->format('Y-m-d'),
            'lieu_naissance' => $eleve->lieu_naissance,
        ]);
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

            foreach ($disciplines as $i => $disc) {
                $moyenne = isset($disc['moyenne']) ? (float) $disc['moyenne'] : null;
                $coef = isset($disc['coefficient']) ? (float) $disc['coefficient'] : null;

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
            $bulletin = Bulletin::create([
                'tenant_id' => $tenantId,
                'etablissement_id' => $etablissementId,
                'annee_academique_id' => (int) $payload['annee_academique_id'],
                'eleve_id' => (int) $payload['eleve_id'],
                'classe_id' => $classeId,
                'trimestre' => $payload['trimestre'],

                'total_heures' => (float) ($payload['total_heures'] ?? 0),
                'absences' => (int) ($payload['absences'] ?? 0),
                'rang' => (int) ($payload['rang'] ?? 0),
                'moyenne_generale' => $moyenneGenerale,

                'resultat_classe' => $payload['resultat_classe'] ?? null,
                'decision' => $payload['decision'] ?? null,
                'observation_conseil' => $payload['observation_conseil'] ?? null,
                'date' => $payload['date'] ?? null,

                'signature_professeur_principal' => $payload['signature_professeur_principal'] ?? null,
                'signature_directeur' => $payload['signature_directeur'] ?? null,
                'distinctions' => $payload['distinctions'] ?? null,
            ]);

            foreach ($disciplines as $disc) {
                BulletinDiscipline::create([
                    'tenant_id' => $tenantId,
                    'bulletin_id' => $bulletin->id,
                    'discipline' => $disc['discipline'],
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
}


