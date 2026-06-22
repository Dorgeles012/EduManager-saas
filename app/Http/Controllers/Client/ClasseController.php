<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\Etablissement;
use App\Models\Niveau;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClasseController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $classes = Classe::with(['etablissement', 'niveau'])
            ->withCount('eleves')
            ->where('tenant_id', $user->tenant_id)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->latest()
            ->get();

        $schools = Etablissement::query()
            ->where('tenant_id', $user->tenant_id)
            ->when($user->etablissement_id, fn ($q) => $q->where('id', $user->etablissement_id))
            ->orderBy('nom')
            ->get(['id', 'nom'])
            ->map(fn ($school) => ['id' => $school->id, 'name' => $school->nom]);

        $levels = Niveau::query()
            ->where('tenant_id', $user->tenant_id)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->orderBy('nom')
            ->get(['id', 'nom'])
            ->map(fn ($level) => ['id' => $level->id, 'name' => $level->nom]);

        return view('client.classe', [
            'classes' => $classes->map(fn ($classe) => [
                'id' => $classe->id,
                'name' => $classe->nom,
                'school_id' => $classe->etablissement_id,
                'level_id' => $classe->niveau_id,
                'school' => $classe->etablissement?->nom ?? 'Non assigné',
                'level' => $classe->niveau?->nom ?? 'Non assigné',
                'student_count' => $classe->eleves_count,
                'max_students' => $classe->capacite,
            ]),
            'totalClasses' => $classes->count(),
            'totalLevels' => $levels->count(),
            'schoolName' => $schools->first()['name'] ?? 'Mon Établissement',
            'levels' => $levels,
            'schools' => $schools,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateClasse($request);
        $user = auth()->user();

        $classe = Classe::create([
            'tenant_id' => $user->tenant_id,
            'etablissement_id' => $validated['etablissement_id'],
            'niveau_id' => $validated['niveau_id'],
            'nom' => $validated['nom'],
            'capacite' => $validated['capacite'] ?? 50,
        ]);

        $classe->loadMissing(['etablissement', 'niveau']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Classe créée avec succès.',
                'class' => [
                    'id' => $classe->id,
                    'name' => $classe->nom,
                    'school_id' => $classe->etablissement_id,
                    'school' => $classe->etablissement?->nom ?? 'Non assigné',
                    'level_id' => $classe->niveau_id,
                    'level' => $classe->niveau?->nom ?? 'Non assigné',
                    'max_students' => $classe->capacite,
                ],
            ], 201);
        }

        return back()->with('success', 'Classe créée avec succès.');
    }

    public function update(Request $request, Classe $classe)
    {
        $this->authorizeTenant($classe);
        $validated = $this->validateClasse($request);

        $classe->update([
            'etablissement_id' => $validated['etablissement_id'],
            'niveau_id' => $validated['niveau_id'],
            'nom' => $validated['nom'],
            'capacite' => $validated['capacite'] ?? 50,
        ]);

        $classe->refresh()->loadMissing(['etablissement', 'niveau']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Classe mise à jour avec succès.',
                'class' => [
                    'id' => $classe->id,
                    'name' => $classe->nom,
                    'school_id' => $classe->etablissement_id,
                    'school' => $classe->etablissement?->nom ?? 'Non assigné',
                    'level_id' => $classe->niveau_id,
                    'level' => $classe->niveau?->nom ?? 'Non assigné',
                    'max_students' => $classe->capacite,
                ],
            ]);
        }

        return back()->with('success', 'Classe mise à jour avec succès.');
    }

    public function destroy(Classe $classe)
    {
        $this->authorizeTenant($classe);
        $classe->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Classe supprimée avec succès.',
            ]);
        }

        return back()->with('success', 'Classe supprimée avec succès.');
    }

    private function validateClasse(Request $request): array
    {
        $user = auth()->user();

        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'etablissement_id' => [
                'required',
                Rule::exists('etablissements', 'id')->where(fn ($q) => $q->where('tenant_id', $user->tenant_id)),
            ],
            'niveau_id' => [
                'required',
                Rule::exists('niveaux', 'id')->where(fn ($q) => $q->where('tenant_id', $user->tenant_id)),
            ],
            'capacite' => ['nullable', 'integer', 'min:1', 'max:500'],
        ]);

        if ($user->etablissement_id) {
            abort_unless((int) $validated['etablissement_id'] === (int) $user->etablissement_id, 403);
        }

        return $validated;
    }

    private function authorizeTenant(Classe $classe): void
    {
        $user = auth()->user();

        abort_unless(
            $classe->tenant_id === $user->tenant_id
            && (!$user->etablissement_id || $classe->etablissement_id === $user->etablissement_id),
            403
        );
    }
}
