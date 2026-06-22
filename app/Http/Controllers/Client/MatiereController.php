<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Matiere;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MatiereController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;

        $subjects = Matiere::query()
            ->where('tenant_id', $tenantId)
            ->latest()
            ->get();

        return view('client.matiere', [
            'subjects' => $subjects->map(fn ($matiere) => [
                'id' => $matiere->id,
                'name' => $matiere->nom,
                'coefficient' => (int) $matiere->coefficient,
                'status' => 'active',
            ]),
            'totalSubjects' => $subjects->count(),
            'totalCoefficient' => $subjects->sum('coefficient'),
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
