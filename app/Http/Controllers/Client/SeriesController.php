<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Matiere;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Series;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SeriesController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;

        $series = Series::query()
            ->where('tenant_id', $tenantId)
            ->withCount('matieres')
            ->with(['classes' => function ($q) {
                $q->select('classes.id', 'classes.nom');
            }])
            ->orderBy('nom_serie')
            ->get();


        return view('client.series', [
            'series' => $series->map(function ($s) {
                $classIds = $s->classes->pluck('id')->all();
                return [
                    'id' => $s->id,
                    'nom_serie' => $s->nom_serie,
                    // Pour compat UI (édition) on prend la 1ère classe si existante
                    'id_classe' => $classIds[0] ?? null,
                    'classe' => $s->classes->isEmpty() ? 'Non assignée' : $s->classes->pluck('nom')->join(', '),
                    'id_classes' => $classIds,
                    'matieres_count' => $s->matieres_count,
                ];
            }),
            'totalSeries' => $series->count(),
            'classes' => Classe::query()->where('tenant_id', $tenantId)
                ->when(auth()->user()->etablissement_id, fn ($q, $id) => $q->where('etablissement_id', $id))
                ->orderBy('nom')->get(['id', 'nom']),
        ]);
    }

    public function store(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $validated = $request->validate([
            'id_classes' => ['required', 'array', 'min:1'],
            'id_classes.*' => [
                'integer',
                Rule::exists('classes', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId)),
            ],
            'nom_serie' => [
                'required',
                'string',
                'max:255',
                Rule::unique('series', 'nom_serie')
                    ->where(fn ($q) => $q->where('tenant_id', $tenantId)),
            ],
        ]);

        $series = Series::create([
            'tenant_id' => $tenantId,
            // Backward-compat: conserve la 1ère classe dans l'ancienne colonne
            'id_classe' => $validated['id_classes'][0] ?? null,
            'nom_serie' => $validated['nom_serie'],
        ]);

        $series->classes()->sync($validated['id_classes']);

        return back()->with('success', 'Série créée avec succès.');
    }


    public function update(Request $request, Series $series)
    {
        $this->authorizeTenant($series);
        $tenantId = auth()->user()->tenant_id;

        $validated = $request->validate([
            'id_classes' => ['required', 'array', 'min:1'],
            'id_classes.*' => [
                'integer',
                Rule::exists('classes', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId)),
            ],
            'nom_serie' => [
                'required',
                'string',
                'max:255',
                Rule::unique('series', 'nom_serie')
                    ->ignore($series->id)
                    ->where(fn ($q) => $q->where('tenant_id', $tenantId)),
            ],
        ]);

        $series->update([
            'nom_serie' => $validated['nom_serie'],
            // compat 1ère classe pour les modules non migrés
            'id_classe' => $validated['id_classes'][0] ?? null,
        ]);

        $series->classes()->sync($validated['id_classes']);

        return back()->with('success', 'Série mise à jour avec succès.');
    }


    public function destroy(Series $series)
    {
        $this->authorizeTenant($series);

        $tenantId = auth()->user()->tenant_id;

        $count = $series->matieres()->count();

        $studentCount = Eleve::query()
            ->where('tenant_id', $tenantId)
            ->where('id_serie', $series->id)
            ->count();


        if ($count > 0 || $studentCount > 0) {
            return back()->with('error', 'Impossible de supprimer la série : elle est associée à des matières ou à des élèves.');
        }

        $series->delete();

        return back()->with('success', 'Série supprimée avec succès.');
    }

    public function byClasse(Classe $classe)
    {
        abort_unless((int) $classe->tenant_id === (int) auth()->user()->tenant_id, 404);

        return response()->json(
            $classe->series()->orderBy('nom_serie')->get(['series.id', 'series.nom_serie'])
        );
    }

    public function disciplines(Series $series)
    {
        $this->authorizeTenant($series);
        $tenantId = auth()->user()->tenant_id;

        $series->load(['matieres' => fn ($query) => $query->orderBy('nom')]);

        return view('client.series-disciplines', [
            'serie' => $series,
            'disciplines' => $series->matieres,
            'matieres' => Matiere::query()
                ->where('tenant_id', $tenantId)
                ->orderBy('nom')
                ->get(['id', 'nom', 'coefficient']),
        ]);
    }

    public function storeDiscipline(Request $request, Series $series)
    {
        $this->authorizeTenant($series);
        $tenantId = auth()->user()->tenant_id;

        $validated = $request->validate([
            'matiere_id' => [
                'required',
                Rule::exists('matieres', 'id')->where(fn ($query) => $query->where('tenant_id', $tenantId)),
                Rule::unique('serie_matieres', 'matiere_id')->where(fn ($query) => $query->where('serie_id', $series->id)),
            ],
            'coefficient' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $matiere = Matiere::query()->where('tenant_id', $tenantId)->findOrFail($validated['matiere_id']);
        $series->matieres()->attach($matiere->id, [
            'coefficient' => $validated['coefficient'] ?? max(1, (int) $matiere->coefficient),
        ]);

        return back()->with('success', 'Discipline ajoutée à la série.');
    }

    public function updateDiscipline(Request $request, Series $series, Matiere $matiere)
    {
        $this->authorizeTenant($series);
        abort_unless((int) $matiere->tenant_id === (int) auth()->user()->tenant_id, 404);
        abort_unless($series->matieres()->whereKey($matiere->id)->exists(), 404);

        $validated = $request->validate([
            'coefficient' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $series->matieres()->updateExistingPivot($matiere->id, [
            'coefficient' => $validated['coefficient'],
        ]);

        return back()->with('success', 'Coefficient mis à jour.');
    }

    public function destroyDiscipline(Series $series, Matiere $matiere)
    {
        $this->authorizeTenant($series);
        abort_unless((int) $matiere->tenant_id === (int) auth()->user()->tenant_id, 404);

        $series->matieres()->detach($matiere->id);

        return back()->with('success', 'Discipline retirée de la série.');
    }

    private function authorizeTenant(Series $series): void
    {
        abort_unless($series->tenant_id === auth()->user()->tenant_id, 403);
    }
}

