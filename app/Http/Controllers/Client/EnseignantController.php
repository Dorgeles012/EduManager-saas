<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Enseignant;
use App\Models\Etablissement;
use App\Models\Matiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EnseignantController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $enseignants = Enseignant::with('matiere')
            ->where('tenant_id', $user->tenant_id)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->latest()
            ->paginate(10);

        $matieres = Matiere::query()
            ->where('tenant_id', $user->tenant_id)
            ->orderBy('nom')
            ->get(['id', 'nom']);

        return view('client.enseignant', [
            'teachers' => $enseignants->through(fn ($teacher) => [
                'id' => $teacher->id,
                'firstname' => $teacher->prenoms ?? '',
                'lastname' => $teacher->nom,
                'email' => $teacher->email,
                'phone' => $teacher->telephone,
                'subject_id' => $teacher->matiere_id,
                'subject' => $teacher->matiere?->nom ?? 'Non assignée',
                'status' => $teacher->statut,
            ]),
            'subjects' => $matieres->map(fn ($matiere) => ['id' => $matiere->id, 'name' => $matiere->nom]),
            'totalTeachers' => $enseignants->total(),
            'totalSubjects' => $matieres->count(),
            'avgPerSubject' => $matieres->count() > 0 ? round($enseignants->total() / $matieres->count(), 1) : 0,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateEnseignant($request);
        $user = auth()->user();

        DB::transaction(function () use ($validated, $user) {
            $etablissementId = $user->etablissement_id
                ?? Etablissement::where('tenant_id', $user->tenant_id)->value('id');

            $enseignant = Enseignant::create([
                'tenant_id' => $user->tenant_id,
                'etablissement_id' => $etablissementId,
                'nom' => $validated['nom'],
                'prenoms' => $validated['prenoms'],
                'email' => $validated['email'],
                'telephone' => $validated['telephone'],
                'password' => Hash::make('12345678'),
                'matiere_id' => $validated['matiere_id'],
                'specialite' => Matiere::find($validated['matiere_id'])?->nom,
                'statut' => 'active',
            ]);

            $enseignant->matieres()->sync([$validated['matiere_id']]);
        });

        $teacher = Enseignant::with('matiere')
            ->where('tenant_id', $user->tenant_id)
            ->orderByDesc('id')
            ->first();

        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Enseignant créé avec succès.',
                'teacher' => [
                    'id' => $teacher?->id,
                    'firstname' => $teacher?->prenoms ?? '',
                    'lastname' => $teacher?->nom ?? '',
                    'email' => $teacher?->email ?? '',
                    'phone' => $teacher?->telephone ?? '',
                    'subject_id' => $teacher?->matiere_id,
                    'subject' => $teacher?->matiere?->nom ?? 'Non assignée',
                    'status' => $teacher?->statut ?? 'active',
                ],
            ]);
        }

        return back()->with('success', 'Enseignant créé avec succès.');
    }

    public function update(Request $request, Enseignant $enseignant)
    {
        $this->authorizeTenant($enseignant);
        $validated = $this->validateEnseignant($request, $enseignant);

        DB::transaction(function () use ($validated, $enseignant) {
            $enseignant->update([
                'nom' => $validated['nom'],
                'prenoms' => $validated['prenoms'],
                'email' => $validated['email'],
                'telephone' => $validated['telephone'],
                'matiere_id' => $validated['matiere_id'],
                'specialite' => Matiere::find($validated['matiere_id'])?->nom,
            ]);

            $enseignant->matieres()->sync([$validated['matiere_id']]);
        });

        $teacher = Enseignant::with('matiere')->find($enseignant->id);

        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Enseignant mis à jour avec succès.',
                'teacher' => [
                    'id' => $teacher?->id,
                    'firstname' => $teacher?->prenoms ?? '',
                    'lastname' => $teacher?->nom ?? '',
                    'email' => $teacher?->email ?? '',
                    'phone' => $teacher?->telephone ?? '',
                    'subject_id' => $teacher?->matiere_id,
                    'subject' => $teacher?->matiere?->nom ?? 'Non assignée',
                    'status' => $teacher?->statut ?? 'active',
                ],
            ]);
        }

        return back()->with('success', 'Enseignant mis à jour avec succès.');
    }

    public function destroy(Enseignant $enseignant)
    {
        $this->authorizeTenant($enseignant);
        $enseignant->matieres()->detach();
        $enseignant->delete();

        if (request()->wantsJson() || request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Enseignant supprimé avec succès.',
            ]);
        }

        return back()->with('success', 'Enseignant supprimé avec succès.');
    }

    private function validateEnseignant(Request $request, ?Enseignant $enseignant = null): array
    {
        $user = auth()->user();

        return $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'prenoms' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('enseignants', 'email')->ignore($enseignant?->id),
            ],
            'telephone' => ['required', 'string', 'max:50'],
            'matiere_id' => [
                'required',
                Rule::exists('matieres', 'id')->where(fn ($q) => $q->where('tenant_id', $user->tenant_id)),
            ],
        ]);
    }

    private function authorizeTenant(Enseignant $enseignant): void
    {
        $user = auth()->user();

        abort_unless(
            $enseignant->tenant_id === $user->tenant_id
            && (!$user->etablissement_id || $enseignant->etablissement_id === $user->etablissement_id),
            403
        );
    }
}
