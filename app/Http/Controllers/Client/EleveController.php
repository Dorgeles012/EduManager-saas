<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Etablissement;
use App\Models\Niveau;
use App\Models\ParentEleve;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EleveController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Eleve::with(['classe.niveau', 'niveau', 'parent'])
            ->where('tenant_id', $user->tenant_id)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search');
                $q->where(function ($sub) use ($search) {
                    $sub->where('nom', 'like', "%{$search}%")
                        ->orWhere('prenom', 'like', "%{$search}%")
                        ->orWhere('matricule', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('niveau_id'), fn ($q) => $q->where('niveau_id', $request->integer('niveau_id')))
            ->when($request->filled('classe_id'), fn ($q) => $q->where('classe_id', $request->integer('classe_id')))
            ->latest();

        $students = $query->paginate(10)->withQueryString();

        $levels = Niveau::query()
            ->where('tenant_id', $user->tenant_id)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->orderBy('nom')
            ->get(['id', 'nom']);

        $classes = Classe::with('niveau')
            ->where('tenant_id', $user->tenant_id)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->orderBy('nom')
            ->get(['id', 'nom', 'niveau_id']);

        return view('client.eleve', [
            'students' => $students->through(fn ($student) => [
                'id' => $student->id,
                'lastname' => $student->nom,
                'firstname' => $student->prenom,
                'level_id' => $student->niveau_id,
                'class_id' => $student->classe_id,
                'level' => $student->niveau?->nom ?? $student->classe?->niveau?->nom ?? 'Non assigné',
                'class' => $student->classe?->nom ?? 'Non assignée',
                'matricule' => $student->matricule,
                'birthdate' => $student->date_naissance?->format('d/m/Y') ?? 'N/A',
                'birthdate_raw' => $student->date_naissance?->format('Y-m-d'),
                'birthplace' => $student->lieu_naissance,
                'parent_name' => trim(($student->parent?->nom ?? '') . ' ' . ($student->parent?->prenom ?? '')) ?: 'N/A',
                'status' => $student->statut,
            ]),
            'totalStudents' => Eleve::where('tenant_id', $user->tenant_id)->count(),
            'activeClasses' => $classes->count(),
            'levels' => $levels->map(fn ($level) => ['id' => $level->id, 'name' => $level->nom]),
            'classes' => $classes->map(fn ($classe) => ['id' => $classe->id, 'name' => $classe->nom, 'level_id' => $classe->niveau_id]),
            'currentAcademicYear' => now()->year . '-' . now()->addYear()->year,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateEleve($request);
        $user = auth()->user();

        DB::transaction(function () use ($validated, $user) {
            $parent = ParentEleve::create([
                'tenant_id' => $user->tenant_id,
                'nom' => $validated['parent_nom'],
                'prenom' => $validated['parent_prenom'] ?? null,
                'email' => $validated['parent_email'] ?? null,
                'telephone' => $validated['parent_telephone'],
            ]);

            Eleve::create([
                'tenant_id' => $user->tenant_id,
                'etablissement_id' => $validated['etablissement_id'],
                'classe_id' => $validated['classe_id'] ?? null,
                'niveau_id' => $validated['niveau_id'],
                'parent_id' => $parent->id,
                'matricule' => $validated['matricule'] ?? $this->generateMatricule($user->tenant_id),
                'nom' => $validated['nom'],
                'prenom' => $validated['prenom'] ?? null,
                'date_naissance' => $validated['date_naissance'],
                'lieu_naissance' => $validated['lieu_naissance'] ?? null,
                'ancien_etablissement' => $validated['ancien_etablissement'] ?? null,
                'statut' => ($validated['type_eleve'] ?? 'nouveau') === 'transfere' ? 'transfert' : 'actif',
            ]);
        });

        return back()->with('success', 'Élève enregistré avec succès.');
    }

    public function update(Request $request, Eleve $eleve)
    {
        $this->authorizeTenant($eleve);
        $validated = $this->validateEleve($request, $eleve);

        DB::transaction(function () use ($validated, $eleve) {
            $eleve->parent?->update([
                'nom' => $validated['parent_nom'],
                'prenom' => $validated['parent_prenom'] ?? null,
                'email' => $validated['parent_email'] ?? null,
                'telephone' => $validated['parent_telephone'],
            ]);

            $eleve->update([
                'etablissement_id' => $validated['etablissement_id'],
                'classe_id' => $validated['classe_id'] ?? null,
                'niveau_id' => $validated['niveau_id'],
                'matricule' => $validated['matricule'] ?? $eleve->matricule,
                'nom' => $validated['nom'],
                'prenom' => $validated['prenom'] ?? null,
                'date_naissance' => $validated['date_naissance'],
                'lieu_naissance' => $validated['lieu_naissance'] ?? null,
                'ancien_etablissement' => $validated['ancien_etablissement'] ?? null,
                'statut' => ($validated['type_eleve'] ?? 'nouveau') === 'transfere' ? 'transfert' : 'actif',
            ]);
        });

        return back()->with('success', 'Élève mis à jour avec succès.');
    }

    public function destroy(Eleve $eleve)
    {
        $this->authorizeTenant($eleve);
        $eleve->delete();

        return back()->with('success', 'Élève supprimé avec succès.');
    }

    private function validateEleve(Request $request, ?Eleve $eleve = null): array
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id
            ?? Etablissement::where('tenant_id', $user->tenant_id)->value('id');

        $validated = $request->validate([
            'type_eleve' => ['nullable', 'in:nouveau,transfere'],
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['nullable', 'string', 'max:255'],
            'matricule' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('eleves', 'matricule')->ignore($eleve?->id),
            ],
            'date_naissance' => ['required', 'date', 'before_or_equal:' . now()->subYears(5)->format('Y-m-d')],
            'lieu_naissance' => ['nullable', 'string', 'max:255'],
            'ancien_etablissement' => ['nullable', 'string', 'max:255'],
            'niveau_id' => [
                'required',
                Rule::exists('niveaux', 'id')->where(fn ($q) => $q->where('tenant_id', $user->tenant_id)),
            ],
            'classe_id' => [
                'nullable',
                Rule::exists('classes', 'id')->where(fn ($q) => $q->where('tenant_id', $user->tenant_id)),
            ],
            'parent_nom' => ['required', 'string', 'max:255'],
            'parent_prenom' => ['nullable', 'string', 'max:255'],
            'parent_email' => ['nullable', 'email', 'max:255'],
            'parent_telephone' => ['required', 'string', 'max:50'],
        ]);

        $validated['etablissement_id'] = $etablissementId;

        return $validated;
    }

    private function generateMatricule(int $tenantId): string
    {
        do {
            $matricule = 'ELV-' . $tenantId . '-' . now()->format('ymdHis') . random_int(10, 99);
        } while (Eleve::where('matricule', $matricule)->exists());

        return $matricule;
    }

    private function authorizeTenant(Eleve $eleve): void
    {
        $user = auth()->user();

        abort_unless(
            $eleve->tenant_id === $user->tenant_id
            && (!$user->etablissement_id || $eleve->etablissement_id === $user->etablissement_id),
            403
        );
    }
}
