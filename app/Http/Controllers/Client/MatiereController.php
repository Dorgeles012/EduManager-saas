<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Matiere;
use App\Models\Series;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class MatiereController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;

        $series = Series::query()
            ->where('tenant_id', $tenantId)
            ->orderBy('nom_serie')
            ->get(['id', 'tenant_id', 'nom_serie']);

        // Chargement initial : toutes les séries.
        // Le filtrage principal doit se faire côté serveur via l’AJAX (voir getBySerie).
        $subjects = Matiere::query()
            ->select(['id', 'tenant_id', 'nom', 'coefficient', 'serie'])
            ->where('tenant_id', $tenantId)
            ->when(true, fn ($q) => $q->latest())
            ->get();

        $seriesById = $series->keyBy('id');
        $seriesByNom = $series->keyBy('nom_serie');

        $normalizedSubjects = $subjects->map(function ($matiere) use ($seriesById, $seriesByNom) {
            $rawSerie = $matiere->serie;

            $serieModel = is_numeric($rawSerie)
                ? ($seriesById->get((int) $rawSerie) ?? null)
                : ($seriesById->get((int) 0) ?? null);

            if (!$serieModel && is_string($rawSerie)) {
                $serieModel = $seriesByNom->get($rawSerie) ?? null;
            }

            $serieId = $serieModel?->id ?? 0;
            $serieNom = $serieModel?->nom_serie;

            return [
                'id' => $matiere->id,
                'name' => $matiere->nom,
                'coefficient' => (int) $matiere->coefficient,
                'serie' => $serieNom,
                'serie_id' => (int) $serieId,
                'status' => 'active',
            ];
        });

        // Nombre d'enseignants DISTINCTS ayant au moins une affectation (pivot enseignant_matiere)
        $assignedTeachersCount = \App\Models\Enseignant::query()
            ->where('tenant_id', $tenantId)
            ->whereIn('id', function ($q) {
                $q->select('enseignant_id')
                    ->from('enseignant_matiere')
                    ->groupBy('enseignant_id');
            })
            ->distinct('id')
            ->count('id');

        return view('client.matiere', [
            'subjects' => $normalizedSubjects,
            'series' => $series,
            'totalSubjects' => $normalizedSubjects->count(),
            'totalCoefficient' => $subjects->sum('coefficient'),
            'assignedTeachersCount' => (int) $assignedTeachersCount,
        ]);
    }

    public function getBySerie(Request $request, int $serieId)
    {
        $tenantId = auth()->user()->tenant_id;

        // Endpoint strict par série.
        // IMPORTANT : matieres.serie peut être un id (cas récent) OU un nom (cas historique).
        // On ne retourne jamais des matières d’une autre série.

        $matieres = Matiere::query()
            ->select(['id', 'nom', 'coefficient', 'serie'])
            ->where('tenant_id', $tenantId)
            // Cas récent : matieres.serie = series.id
            ->where('serie', $serieId)
            ->orderByDesc('id')
            ->get();

        if ($matieres->isEmpty()) {
            // Cas historique : on résout le nom de série puis on filtre par ce nom.
            $serieModel = Series::query()
                ->where('tenant_id', $tenantId)
                ->where('id', $serieId)
                ->first(['id', 'nom_serie']);

            if ($serieModel) {
                $matieres = Matiere::query()
                    ->select(['id', 'nom', 'coefficient', 'serie'])
                    ->where('tenant_id', $tenantId)
                    // Cas historique : matieres.serie = series.nom_serie
                    ->where('serie', $serieModel->nom_serie)
                    ->orderByDesc('id')
                    ->get();
            }
        }

        // normalisation série_name/id
        $serieName = Series::query()
            ->where('tenant_id', $tenantId)
            ->where('id', $serieId)
            ->value('nom_serie');

        return response()->json([
            'data' => $matieres->map(function ($matiere) use ($serieId, $serieName) {
                return [
                    'id' => $matiere->id,
                    'name' => $matiere->nom,
                    'coefficient' => (int) $matiere->coefficient,
                    'serie_id' => $serieId,
                    'serie_name' => $serieName ?? $matiere->serie,
                ];
            })->values(),
        ]);
    }

    public function getAll(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $matieres = Matiere::query()
            ->select(['id', 'nom', 'coefficient', 'serie'])
            ->where('tenant_id', $tenantId)
            ->orderByDesc('id')
            ->get();

        // Résoudre optionnellement nom/id de série si possible (cas id vs nom)
        $series = Series::query()
            ->where('tenant_id', $tenantId)
            ->get(['id', 'nom_serie'])
            ->keyBy('id');

        return response()->json([
            'data' => $matieres->map(function ($matiere) use ($series) {
                $raw = $matiere->serie;
                $serieId = is_numeric($raw) ? (int) $raw : null;

                $serieName = null;
                if ($serieId !== null && $series->has($serieId)) {
                    $serieName = $series->get($serieId)->nom_serie;
                }

                return [
                    'id' => $matiere->id,
                    'name' => $matiere->nom,
                    'coefficient' => (int) $matiere->coefficient,
                    'serie_id' => $serieId ?? 0,
                    'serie_name' => $serieName ?? $matiere->serie,
                ];
            })->values(),
        ]);
    }


    public function store(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $validated = $request->validate([
            'nom' => [
                'required',
                'string',
                'max:255',
                Rule::unique('matieres', 'nom')->where(fn ($q) => $q->where('tenant_id', $tenantId)),
            ],
            'coefficient' => ['required', 'integer', 'min:1', 'max:10'],
            'serie' => [
                'required',
                'integer',
                'min:1',
                Rule::exists('series', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId)),
            ],
        ]);

        Matiere::create($validated + ['tenant_id' => $tenantId]);

        return back()->with('success', 'Matière créée avec succès.');
    }


    public function update(Request $request, Matiere $matiere)
    {
        $this->authorizeTenant($matiere);
        $tenantId = auth()->user()->tenant_id;

        $validated = $request->validate([
            'nom' => [
                'required',
                'string',
                'max:255',
                Rule::unique('matieres', 'nom')
                    ->ignore($matiere->id)
                    ->where(fn ($q) => $q->where('tenant_id', $tenantId)),
            ],
            'coefficient' => ['required', 'integer', 'min:1', 'max:10'],
            'serie' => [
                'required',
                'integer',
                'min:1',
                Rule::exists('series', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId)),
            ],
        ]);

        $matiere->update($validated);

        return back()->with('success', 'Matière mise à jour avec succès.');
    }


    public function destroy(Matiere $matiere)
    {
        $this->authorizeTenant($matiere);
        $matiere->delete();

        return back()->with('success', 'Matière supprimée avec succès.');
    }






    private function authorizeTenant(Matiere $matiere): void
    {
        abort_unless($matiere->tenant_id === auth()->user()->tenant_id, 403);
    }
}
