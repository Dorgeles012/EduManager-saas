<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\AnneeAcademique;
use App\Models\Eleve;
use App\Models\Etablissement;
use App\Models\FraisScolarite;
use App\Models\Niveau;
use App\Models\Series;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class PersonnelEleveController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $searchTerms = preg_split('/\s+/', trim((string) $request->input('search', '')), -1, PREG_SPLIT_NO_EMPTY);

        $query = Eleve::with(['classe.niveau', 'niveau', 'serie', 'parent', 'user'])
            ->where('tenant_id', $user->tenant_id)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->when($searchTerms, function ($q) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    $q->where(function ($sub) use ($term) {
                        $sub->where('nom', 'like', "%{$term}%")
                            ->orWhere('prenom', 'like', "%{$term}%")
                            ->orWhere('matricule', 'like', "%{$term}%");
                    });
                }
            })
            ->when($request->filled('niveau_id'), fn ($q) => $q->where('niveau_id', $request->integer('niveau_id')))
            ->when($request->filled('classe_id'), fn ($q) => $q->where('classe_id', $request->integer('classe_id')))
            ->when($request->filled('id_serie'), fn ($q) => $q->where('id_serie', $request->integer('id_serie')))
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

        return view('personnel.eleves.index', [
            'students' => $students->through(fn ($student) => [
                'id' => $student->id,
                'lastname' => $student->nom,
                'firstname' => $student->prenom,
                'level_id' => $student->niveau_id,
                'class_id' => $student->classe_id,
                'serie_id' => $student->id_serie,
                'serie' => $student->serie?->nom_serie,
                'level' => $student->niveau?->nom ?? $student->classe?->niveau?->nom ?? 'Non assigné',
                'classe' => $student->classe?->nom ?? 'Non assignée',
                'photo' => $student->photo ? $this->resolvePhotoUrl($student->photo, $student->id) : null,
                'photo_url' => $student->photo ? $this->resolvePhotoUrl($student->photo, $student->id) : null,
                'photo_path' => $student->photo ? $this->normalizePhotoPath($student->photo) : null,
                'matricule' => $student->matricule,
                'birthdate' => $student->date_naissance?->format('d/m/Y') ?? 'N/A',
                'birthdate_raw' => $student->date_naissance?->format('Y-m-d'),
                'birthplace' => $student->lieu_naissance,
                'parent_lastname' => $student->parent?->nom ?? '',
                'parent_firstname' => $student->parent?->prenom ?? '',
                'parent_phone' => $student->parent?->telephone ?? '',
                'parent_email' => $student->parent?->email ?? '',
                'status' => $student->statut,
                'montant_inscription' => (int) $student->montant_inscription,
                'sexe' => $student->sexe,
                'nationalite' => $student->nationalite,
                'interne' => $student->interne,
                'affecte' => $student->affecte,
                'created_at' => optional($student->created_at)->toDateTimeString(),
                'updated_at' => optional($student->updated_at)->toDateTimeString(),
                'eleve_email' => $student->user?->email ?? null,
                'eleve_must_change_pwd' => (bool) ($student->user?->must_change_password ?? false),
                'has_account' => $student->user !== null,
            ]),
            'totalStudents' => Eleve::where('tenant_id', $user->tenant_id)->count(),
            'activeClasses' => $classes->count(),
            'levels' => $levels->map(fn ($level) => ['id' => $level->id, 'name' => $level->nom]),
            'classes' => $classes->map(fn ($classe) => ['id' => $classe->id, 'name' => $classe->nom, 'level_id' => $classe->niveau_id]),
            'series' => Series::query()->where('tenant_id', $user->tenant_id)
                ->with('classes:id')
                ->orderBy('nom_serie')->get(['id', 'nom_serie']),
            'currentAcademicYear' => now()->year . '-' . now()->addYear()->year,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateEleve($request);
        $user = auth()->user();

        DB::transaction(function () use ($validated, $user, $request) {
            $parentEmail = !empty($validated['parent_email']) ? $validated['parent_email'] : null;
            $parentPhone = $validated['parent_telephone'];

            $parent = User::query()
                ->where('tenant_id', $user->tenant_id)
                ->whereRaw('LOWER(role) = ?', ['parent'])
                ->where(function ($q) use ($parentEmail, $parentPhone) {
                    if ($parentEmail) {
                        $q->where('email', $parentEmail);
                    }
                    if ($parentPhone) {
                        $q->orWhere('telephone', $parentPhone);
                    }
                })
                ->first();

            $loginEmail = $parentEmail ?: (preg_replace('/[^0-9]/', '', $parentPhone) . '@parent.local');

            if (! $parent) {
                $parent = User::create([
                    'tenant_id' => $user->tenant_id,
                    'etablissement_id' => $user->etablissement_id,
                    'nom' => $validated['parent_nom'],
                    'prenom' => $validated['parent_prenom'] ?? null,
                    'email' => $loginEmail,
                    'telephone' => $parentPhone,
                    'password' => Hash::make('12345678'),
                    'must_change_password' => true,
                    'role' => 'parent',
                    'statut' => 'actif',
                    // Évite le blocage par MustVerifyEmail (compte créé automatiquement)
                    'email_verified_at' => now(),
                ]);
            } else {
                $parent->update([
                    'nom' => $validated['parent_nom'],
                    'prenom' => $validated['parent_prenom'] ?? $parent->prenom,
                    'email' => $parentEmail ?: $parent->email,
                    'telephone' => $parentPhone,
                ]);
            }

            $photoPath = null;
            if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                $photoPath = $request->file('photo')->store('eleves', 'public');
            }

$eleve = Eleve::create([
                'tenant_id' => $user->tenant_id,
                'etablissement_id' => $validated['etablissement_id'],
                'classe_id' => $validated['classe_id'] ?? null,
                'id_serie' => $validated['id_serie'] ?? null,
                'niveau_id' => $validated['niveau_id'],
                'parent_id' => $parent->id,
                'matricule' => $validated['matricule'] ?? $this->generateMatricule($user->tenant_id),
                'nom' => $validated['nom'],
                'prenom' => $validated['prenom'] ?? null,
                'sexe' => $validated['sexe'],
                'date_naissance' => $validated['date_naissance'],
                'lieu_naissance' => $validated['lieu_naissance'] ?? null,
                'nationalite' => $validated['nationalite'] ?? null,
                'interne' => $validated['interne'],
                'affecte' => $validated['affecte'],
                'ancien_etablissement' => $validated['ancien_etablissement'] ?? null,
                'photo' => $photoPath,
                'statut' => ($validated['type_eleve'] ?? 'nouveau') === 'transfere' ? 'transfert' : 'actif',
                'montant_inscription' => $validated['montant_inscription'] ?? 0,
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
                'id_serie' => $validated['id_serie'] ?? null,
                'niveau_id' => $validated['niveau_id'],
                'matricule' => $validated['matricule'] ?? $eleve->matricule,
                'nom' => $validated['nom'],
                'prenom' => $validated['prenom'] ?? null,
                'sexe' => $validated['sexe'],
                'date_naissance' => $validated['date_naissance'],
                'lieu_naissance' => $validated['lieu_naissance'] ?? null,
                'nationalite' => $validated['nationalite'] ?? null,
                'interne' => $validated['interne'],
                'affecte' => $validated['affecte'],
'ancien_etablissement' => $validated['ancien_etablissement'] ?? null,
                'photo' => $photoPath,
                'statut' => ($validated['type_eleve'] ?? 'nouveau') === 'transfere' ? 'transfert' : 'actif',
                'montant_inscription' => $validated['montant_inscription'],
            ]);

        });

        return back()->with('success', 'Élève mis à jour avec succès.');
    }

    public function destroy(Eleve $eleve)
    {
        $this->authorizeTenant($eleve);

        if ($eleve->user()->exists()) {
            abort(409, 'Cet élève possède encore un ancien compte de connexion. Vérifiez ce compte avant de supprimer son dossier.');
        }

        if ($eleve->notes()->exists() || $eleve->bulletins()->exists() || $eleve->scolarites()->exists()) {
            abort(409, 'Ce dossier possède des notes, bulletins ou données de scolarité. Archivez ou transférez ces historiques avant de supprimer l’élève.');
        }

        DB::transaction(function () use ($eleve) {
            if ($eleve->photo) {
                $oldPath = $this->normalizePhotoPath($eleve->photo);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $eleve->delete();
        });

        return back()->with('success', 'Élève supprimé avec succès.');
    }

    public function photo(Eleve $eleve)
    {
        $this->authorizeTenant($eleve);
        abort_unless($eleve->photo, 404);

        $path = $this->normalizePhotoPath($eleve->photo);
        abort_unless(Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path);
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
            'nationalite' => ['nullable', 'string', 'max:255'],
            'interne' => ['required', 'boolean'],
            'affecte' => ['required', 'boolean'],
            'ancien_etablissement' => ['nullable', 'string', 'max:255'],
            'montant_inscription' => ['nullable', 'integer', 'min:0'],
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
            'id_serie' => [
                'nullable',
                Rule::exists('series', 'id')->where(fn ($q) => $q->where('tenant_id', $user->tenant_id)),
            ],
            'parent_prenom' => ['nullable', 'string', 'max:255'],
            'parent_email' => ['nullable', 'email', 'max:255'],
            'parent_telephone' => ['required', 'string', 'max:50'],
        ]);

        $validated['etablissement_id'] = $etablissementId;

        $frais = $this->fraisDuNiveau($user, (int) $validated['niveau_id']);
        if (! $frais) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'niveau_id' => "Aucun tarif d'inscription n'est configuré pour ce niveau et l'année académique active.",
            ]);
        }
        $montantMinimum = (int) $frais->inscription;
        $montant = (int) ($validated['montant_inscription'] ?? $montantMinimum);
        if ($montant < $montantMinimum) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'montant_inscription' => "Le montant ne peut pas être inférieur au tarif normal de "
                    . number_format($montantMinimum, 0, ',', ' ') . " FCFA pour ce niveau.",
            ]);
        }
        $validated['montant_inscription'] = $montant;

        return $validated;
    }

    private function fraisDuNiveau($user, int $niveauId): ?FraisScolarite
    {
        $anneeId = AnneeAcademique::query()
            ->where('tenant_id', $user->tenant_id)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->where('statut', 'active')
            ->orderByDesc('date_debut')
            ->value('id');

        return FraisScolarite::query()
            ->where('tenant_id', $user->tenant_id)
            ->where('niveau_id', $niveauId)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->when($anneeId, fn ($q) => $q->where('annee_academique_id', $anneeId))
            ->latest()
            ->first();
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

    private function resolvePhotoUrl(string $photo, int $eleveId): ?string
    {
        if (empty($photo)) {
            return null;
        }

        if (str_starts_with($photo, 'http://') || str_starts_with($photo, 'https://')) {
            return $photo;
        }

        $normalized = ltrim($photo, '/');
        if (str_starts_with($normalized, 'storage/')) {
            $normalized = substr($normalized, strlen('storage/'));
        }

        $disk = Storage::disk('public');

        if ($disk->exists($normalized)) {
            return route('personnel.eleves.photo', $eleveId, false);
        }

        if (str_contains($normalized, 'eleves/')) {
            $candidate = strstr($normalized, 'eleves/');
            if (is_string($candidate) && $disk->exists($candidate)) {
                return route('personnel.eleves.photo', $eleveId, false);
            }
        }

        return null;
    }
}
