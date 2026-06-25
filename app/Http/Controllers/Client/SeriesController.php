<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Matiere;
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
            ->orderBy('nom_serie')
            ->get();

        return view('client.series', [
            'series' => $series->map(fn ($s) => [
                'id' => $s->id,
                'nom_serie' => $s->nom_serie,
            ]),
            'totalSeries' => $series->count(),
        ]);
    }

    public function store(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $validated = $request->validate([
            'nom_serie' => [
                'required',
                'string',
                'max:255',
                Rule::unique('series', 'nom_serie')
                    ->where(fn ($q) => $q->where('tenant_id', $tenantId)),
            ],
        ]);

        Series::create([
            'tenant_id' => $tenantId,
            'nom_serie' => $validated['nom_serie'],
        ]);

        return back()->with('success', 'Série créée avec succès.');
    }

    public function update(Request $request, Series $series)
    {
        $this->authorizeTenant($series);
        $tenantId = auth()->user()->tenant_id;

        $validated = $request->validate([
            'nom_serie' => [
                'required',
                'string',
                'max:255',
                Rule::unique('series', 'nom_serie')
                    ->ignore($series->id)
                    ->where(fn ($q) => $q->where('tenant_id', $tenantId)),
            ],
        ]);

        $series->update($validated);

        return back()->with('success', 'Série mise à jour avec succès.');
    }

    public function destroy(Series $series)
    {
        $this->authorizeTenant($series);

        $tenantId = auth()->user()->tenant_id;

        $count = Matiere::query()
            ->where('tenant_id', $tenantId)
            ->where('serie', $series->id)
            ->count();


        if ($count > 0) {
            return back()->with('error', 'Impossible de supprimer la série : elle est associée à des matières.');
        }

        $series->delete();

        return back()->with('success', 'Série supprimée avec succès.');
    }

    private function authorizeTenant(Series $series): void
    {
        abort_unless($series->tenant_id === auth()->user()->tenant_id, 403);
    }
}

