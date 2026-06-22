<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Etablissement;
use App\Models\Niveau;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NiveauxController extends Controller
{
    public function index()
    {
        $user = auth()->user();

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

        return view('client.niveaux', [
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
        $user = auth()->user();

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
                : (int) $validated['etablissement_id'];

            abort_unless(
                Etablissement::where('tenant_id', $user->tenant_id)->where('id', $etablissementId)->exists(),
                403
            );

            $niveau = Niveau::create([
                'tenant_id' => $user->tenant_id,
                'etablissement_id' => $etablissementId,
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
                ->route('client.niveaux')
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
                ->route('client.niveaux')
                ->withErrors($e->validator)
                ->withInput();

        } catch (\Exception $e) {
            return redirect()
                ->route('client.niveaux')
                ->with('error', ' Une erreur est survenue lors de la création du niveau. Veuillez réessayer.');
        }
    }

    public function update(Request $request, Niveau $niveau)
    {
        try {
            $this->authorizeTenant($niveau);
            $user = auth()->user();

            $validated = $request->validate([
                'nom' => ['required', 'string', 'max:255'],
                'etablissement_id' => [
                    'required',
                    Rule::exists('etablissements', 'id')->where(fn ($q) => $q->where('tenant_id', $user->tenant_id)),
                ],
            ]);

            if ($user->etablissement_id) {
                abort_unless((int) $validated['etablissement_id'] === (int) $user->etablissement_id, 403);
            }

            $niveau->update($validated);

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
                ->route('client.niveaux')
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
                ->route('client.niveaux')
                ->withErrors($e->validator)
                ->withInput();

        } catch (\Exception $e) {
            return redirect()
                ->route('client.niveaux')
                ->with('error', ' Une erreur est survenue lors de la modification du niveau. Veuillez réessayer.');
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
                ->route('client.niveaux')
                ->with('success', 'Le niveau "' . $niveauName . '" a été supprimé avec succès !');

        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la suppression du niveau. Veuillez réessayer.',
                ], 500);
            }

            return redirect()
                ->route('client.niveaux')
                ->with('error', 'Une erreur est survenue lors de la suppression du niveau. Veuillez réessayer.');
        }
    }

    private function authorizeTenant(Niveau $niveau): void
    {
        $user = auth()->user();

        abort_unless(
            $niveau->tenant_id === $user->tenant_id
            && (!$user->etablissement_id || $niveau->etablissement_id === $user->etablissement_id),
            403
        );
    }
}