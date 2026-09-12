<?php

namespace App\Http\Controllers\Personnel;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Etablissement;
use App\Models\Niveau;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PersonnelNiveauController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $niveaux = Niveau::with('etablissement')
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

        return view('personnel.niveaux.index', [
            'levels' => $niveaux->map(fn ($niveau) => [
                'id' => $niveau->id,
                'name' => $niveau->nom,
                'school_id' => $niveau->etablissement_id,
                'school' => $niveau->etablissement?->nom ?? 'Non assigné',
                'date' => $niveau->created_at?->format('d/m/Y') ?? 'N/A',
                'icon' => 'auto_stories',
            ]),
            'schools' => $schools,
            'classes' => collect(),
            'totalLevels' => $niveaux->count(),
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        try {
            $validated = $request->validate([
                'nom' => ['required', 'string', 'max:255'],
                'etablissement_id' => [
                    $user->etablissement_id ? 'nullable' : 'required',
                    Rule::exists('etablissements', 'id')->where(fn ($q) => $q->where('tenant_id', $user->tenant_id)),
                ],
            ]);

            $etablissementId = $user->etablissement_id
                ? (int) $user->etablissement_id
                : (int) ($validated['etablissement_id'] ?? $user->etablissement_id);

            if ($etablissementId) {
                abort_unless(
                    Etablissement::where('tenant_id', $user->tenant_id)->where('id', $etablissementId)->exists(),
                    403
                );
            }

            $niveau = Niveau::create([
                'tenant_id' => $user->tenant_id,
                'etablissement_id' => $etablissementId ?: null,
                'nom' => $validated['nom'],
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Le niveau "' . $validated['nom'] . '" a été créé avec succès !',
                    'level' => [
                        'id' => $niveau->id,
                        'name' => $niveau->nom,
                        'school_id' => $niveau->etablissement_id,
                        'school' => $niveau->etablissement?->nom ?? 'Non assigné',
                        'date' => $niveau->created_at?->format('d/m/Y') ?? 'N/A',
                    ],
                ], 201);
            }

            return redirect()
                ->route('personnel.niveaux.index')
                ->with('success', 'Le niveau "' . $validated['nom'] . '" a été créé avec succès !');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Les données ne sont pas valides.',
                    'errors' => $e->validator->errors(),
                ], 422);
            }

            return redirect()
                ->route('personnel.niveaux.index')
                ->withErrors($e->validator)
                ->withInput();

        } catch (\Exception $e) {
            return redirect()
                ->route('personnel.niveaux.index')
                ->with('error', 'Une erreur est survenue lors de la création du niveau. Veuillez réessayer.');
        }
    }

    public function update(Request $request, Niveau $niveau)
    {
        try {
            $this->authorizeTenant($niveau);
            $user = Auth::user();

            $validated = $request->validate([
                'nom' => ['required', 'string', 'max:255'],
                'etablissement_id' => [
                    $user->etablissement_id ? 'nullable' : 'required',
                    Rule::exists('etablissements', 'id')->where(fn ($q) => $q->where('tenant_id', $user->tenant_id)),
                ],
            ]);

            $etablissementId = $user->etablissement_id
                ? (int) $user->etablissement_id
                : (int) ($validated['etablissement_id'] ?? $niveau->etablissement_id);

            $niveau->update([
                'nom' => $validated['nom'],
                'etablissement_id' => $etablissementId ?: null,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Le niveau "' . $validated['nom'] . '" a été modifié avec succès !',
                    'level' => [
                        'id' => $niveau->id,
                        'name' => $niveau->nom,
                        'school_id' => $niveau->etablissement_id,
                        'school' => $niveau->etablissement?->nom ?? 'Non assigné',
                    ],
                ]);
            }

            return redirect()
                ->route('personnel.niveaux.index')
                ->with('success', 'Le niveau "' . $validated['nom'] . '" a été modifié avec succès !');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Les données ne sont pas valides.',
                    'errors' => $e->validator->errors(),
                ], 422);
            }

            return redirect()
                ->route('personnel.niveaux.index')
                ->withErrors($e->validator)
                ->withInput();

        } catch (\Exception $e) {
            return redirect()
                ->route('personnel.niveaux.index')
                ->with('error', 'Une erreur est survenue lors de la modification du niveau. Veuillez réessayer.');
        }
    }

    public function destroy(Niveau $niveau)
    {
        try {
            $this->authorizeTenant($niveau);

            $niveauName = $niveau->nom;
            $niveau->delete();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Le niveau "' . $niveauName . '" a été supprimé avec succès !',
                ]);
            }

            return redirect()
                ->route('personnel.niveaux.index')
                ->with('success', 'Le niveau "' . $niveauName . '" a été supprimé avec succès !');

        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la suppression du niveau. Veuillez réessayer.',
                ], 500);
            }

            return redirect()
                ->route('personnel.niveaux.index')
                ->with('error', 'Une erreur est survenue lors de la suppression du niveau. Veuillez réessayer.');
        }
    }

    private function authorizeTenant(Niveau $niveau): void
    {
        $user = Auth::user();

        abort_unless(
            $niveau->tenant_id === $user->tenant_id
            && (! $user->etablissement_id || $niveau->etablissement_id === $user->etablissement_id),
            403
        );
    }
}
