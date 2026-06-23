<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Etablissement;
use App\Models\Niveau;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

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
'classe' => $student->classe?->nom ?? 'Non assignée',
                // Debug photo: valeur DB, chemin physique, URL générée, existence fichier
                'photo' => $student->photo ? $this->resolvePhotoUrl($student->photo) : null,
                'photo_debug' => $student->photo ? $this->buildPhotoDebug($student->photo) : null,

                'matricule' => $student->matricule,
                'birthdate' => $student->date_naissance?->format('d/m/Y') ?? 'N/A',
                'birthdate_raw' => $student->date_naissance?->format('Y-m-d'),
                'birthplace' => $student->lieu_naissance,
'parent_lastname' => $student->parent?->nom ?? '',
                'parent_firstname' => $student->parent?->prenom ?? '',
                'parent_phone' => $student->parent?->telephone ?? '',
                'parent_email' => $student->parent?->email ?? '',
'status' => $student->statut,
                'sexe' => $student->sexe,
                'created_at' => optional($student->created_at)->toDateTimeString(),
                'updated_at' => optional($student->updated_at)->toDateTimeString(),
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

        DB::transaction(function () use ($validated, $user, $request) {
            // Créer le parent en tant qu'utilisateur (role=parent)
            $parentEmail = $validated['parent_email'] ?? null;

            $parent = User::query()->when(
                $parentEmail,
                fn ($q) => $q->where('email', $parentEmail)
            )
                ->where('tenant_id', $user->tenant_id)
                ->whereRaw('LOWER(role) = ?', ['parent'])
                ->first();

            if (! $parent) {
                $parent = User::create([
                    'tenant_id' => $user->tenant_id,
                    'nom' => $validated['parent_nom'],
                    'prenom' => $validated['parent_prenom'] ?? null,
                    'email' => $parentEmail,
                    'telephone' => $validated['parent_telephone'],
                    'password' => Hash::make(Str::random(12)),
                    'role' => 'parent',
                ]);
            } else {
                $parent->update([
                    'nom' => $validated['parent_nom'],
                    'prenom' => $validated['parent_prenom'] ?? $parent->prenom,
                    'email' => $parentEmail ?? $parent->email,
                    'telephone' => $validated['parent_telephone'],
                ]);
            }

            $photoPath = null;
            if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                $photoPath = $request->file('photo')->store('eleves', 'public');
            }

            Eleve::create([
                'tenant_id' => $user->tenant_id,
                'etablissement_id' => $validated['etablissement_id'],
                'classe_id' => $validated['classe_id'] ?? null,
                'niveau_id' => $validated['niveau_id'],
                'parent_id' => $parent->id,
                'matricule' => $validated['matricule'] ?? $this->generateMatricule($user->tenant_id),
                'nom' => $validated['nom'],
                'prenom' => $validated['prenom'] ?? null,
                'sexe' => $validated['sexe'],
                'date_naissance' => $validated['date_naissance'],
                'lieu_naissance' => $validated['lieu_naissance'] ?? null,
                'ancien_etablissement' => $validated['ancien_etablissement'] ?? null,
                'photo' => $photoPath,
                'statut' => ($validated['type_eleve'] ?? 'nouveau') === 'transfere' ? 'transfert' : 'actif',
            ]);



        });

        return back()->with('success', 'Élève enregistré avec succès.');
    }

    public function update(Request $request, Eleve $eleve)
    {
        $this->authorizeTenant($eleve);
        $validated = $this->validateEleve($request, $eleve);

        DB::transaction(function () use ($validated, $eleve, $request) {
            if ($eleve->parent) {
                $eleve->parent->update([
                    'nom' => $validated['parent_nom'],
                    'prenom' => $validated['parent_prenom'] ?? $eleve->parent->prenom,
                    'email' => $validated['parent_email'] ?? $eleve->parent->email,
                    'telephone' => $validated['parent_telephone'],
                ]);
            }

            $photoPath = $eleve->photo;

            if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                if ($eleve->photo) {
                    $oldPath = $this->normalizePhotoPath($eleve->photo);
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }

                $photoPath = $request->file('photo')->store('eleves', 'public');
            }

            $eleve->update([
                'etablissement_id' => $validated['etablissement_id'],
                'classe_id' => $validated['classe_id'] ?? null,
                'niveau_id' => $validated['niveau_id'],
                'matricule' => $validated['matricule'] ?? $eleve->matricule,
                'nom' => $validated['nom'],
                'prenom' => $validated['prenom'] ?? null,
                'sexe' => $validated['sexe'],
                'date_naissance' => $validated['date_naissance'],
                'lieu_naissance' => $validated['lieu_naissance'] ?? null,
                'ancien_etablissement' => $validated['ancien_etablissement'] ?? null,
                'photo' => $photoPath,
                'statut' => ($validated['type_eleve'] ?? 'nouveau') === 'transfere' ? 'transfert' : 'actif',
            ]);


        });

        return back()->with('success', 'Élève mis à jour avec succès.');
    }

    public function destroy(Eleve $eleve)
    {
        $this->authorizeTenant($eleve);

        DB::transaction(function () use ($eleve) {
            // Parent lié à l'élève (filtré sur role = 'parent' dans la relation Eloquent)
            $parent = $eleve->parent;

            // Cas 2 (parent partagé) : on vérifie avant suppression si d'autres élèves utilisent encore ce parent.
            $remainingStudentsForParent = $parent
                ? $parent->eleves()->count()
                : 0;

            // Supprimer le fichier photo de l'élève si présent
            if ($eleve->photo) {
                $oldPath = $this->normalizePhotoPath($eleve->photo);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $eleve->delete();

            // Si le parent n'a plus aucun élève rattaché, on le supprime.

            if ($parent && $remainingStudentsForParent <= 1 && strtolower($parent->role) === 'parent') {
                $parent->delete();
            }
        });

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
            'sexe' => ['required', 'in:Masculin,Féminin'],

'matricule' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('eleves', 'matricule')
                    ->where(fn ($q) => $q->where('tenant_id', $user->tenant_id))
                    ->ignore($eleve?->getKey()),
            ],
            'date_naissance' => ['required', 'date', 'before_or_equal:' . now()->subYears(5)->format('Y-m-d')],
            'lieu_naissance' => ['nullable', 'string', 'max:255'],
            'ancien_etablissement' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
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

    private function normalizePhotoPath(string $photo): string
    {
        $normalized = ltrim($photo, '/');

        if (str_starts_with($normalized, 'storage/')) {
            $normalized = substr($normalized, strlen('storage/'));
        }

        return $normalized;
    }

    private function buildPhotoDebug(string $photo): array
    {
        $normalized = $this->normalizePhotoPath($photo);
        $disk = Storage::disk('public');

        $physicalPath = storage_path('app/public/' . $normalized);
        $exists = $disk->exists($normalized);
        $url = $exists ? $disk->url($normalized) : null;

        return [
            'photo_db' => $photo,
            'normalized_path' => $normalized,
            'physical_path' => $physicalPath,
            'exists' => $exists,
            'url_generated' => $url,
        ];
    }

    private function resolvePhotoUrl(string $photo): ?string
    {
        // Objectif: garantir que $student.photo renvoie une URL directement exploitable dans <img src="...">
        if (empty($photo)) {
            return null;
        }

        // Si c'est déjà une URL complète
        if (str_starts_with($photo, 'http://') || str_starts_with($photo, 'https://')) {
            return $photo;
        }

        // Variantes possibles en base: "eleves/xxx.jpg", "storage/eleves/xxx.jpg", "/storage/eleves/xxx.jpg"...
        $normalized = ltrim($photo, '/');

        if (str_starts_with($normalized, 'storage/')) {
            $normalized = substr($normalized, strlen('storage/'));
        }

        $disk = Storage::disk('public');

        // Si on a un chemin relatif à "public" et que le fichier existe, on génère une URL.
        if ($disk->exists($normalized)) {
            return $disk->url($normalized); // => /storage/eleves/xxx.jpg
        }

        // Fallback: parfois la base peut contenir déjà un chemin avec "eleves/..." mais avec des préfixes inattendus.
        // On tente une normalisation "à la main".
        if (str_contains($normalized, 'eleves/')) {
            $candidate = strstr($normalized, 'eleves/');
            if (is_string($candidate) && $disk->exists($candidate)) {
                return $disk->url($candidate);
            }
        }

        return null;
    }
}
