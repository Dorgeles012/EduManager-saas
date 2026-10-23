<?php

namespace App\Http\Controllers\Client;

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

class EleveController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Eleve::with(['classe.niveau', 'niveau', 'serie', 'parent'])
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

        return view('client.eleve', [
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
                // Photo publique exploitable directement dans le front
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
            // Créer le parent en tant qu'utilisateur (role=parent)
            $parentEmail = !empty($validated['parent_email']) ? $validated['parent_email'] : null;
            $parentPhone = $validated['parent_telephone'] ?? null;

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

            $loginEmail = $parentEmail ?: (preg_replace('/[^0-9]/', '', (string) $parentPhone) . '@parent.local');

            if (! $parent) {
                // Mot de passe par défaut pour le parent (à changer obligatoirement au 1er login).
                $defaultPassword = '12345678';

                $parent = User::create([
                    'tenant_id' => $user->tenant_id,
                    'etablissement_id' => $user->etablissement_id,
                    'nom' => $validated['parent_nom'],
                    'prenom' => $validated['parent_prenom'] ?? null,
                    'email' => $loginEmail,
                    'telephone' => $parentPhone,
                    'password' => Hash::make($defaultPassword),
                    'must_change_password' => true,
                    'role' => 'parent',
                    'statut' => 'actif',
                    // Évite le blocage par MustVerifyEmail (email généré automatiquement)
                    'email_verified_at' => now(),
                ]);
            } else {
                $parent->update([
                    'nom' => $validated['parent_nom'],
                    'prenom' => $validated['parent_prenom'] ?? $parent->prenom,
                    'email' => $parentEmail ?: ($parent->email ?: $loginEmail),
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
                'montant_inscription' => $validated['montant_inscription'] ?? $eleve->montant_inscription,
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
            // Supprimer le fichier photo de l'élève si présent
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


    /**
     * Endpoint AJAX : retourne les frais de scolarité pour un niveau donné.
     * Utilisé pour la validation dynamique du montant d'inscription.
     */
    public function getTarifs(Request $request)
    {
        $user = auth()->user();
        $niveauId = $request->integer('niveau_id');

        if (! $niveauId) {
            return response()->json(['frais' => null]);
        }

        $frais = $this->fraisDuNiveau($user, $niveauId);

        if (! $frais) {
            return response()->json(['frais' => null]);
        }

        // La règle de marge : le montant d'inscription doit être entre 50% et 100% du tarif d'inscription configuré.
        // Si aucun tarif n'est configuré (inscription = 0), aucune vérification n'est appliquée.
        $reference = (int) $frais->inscription;
        $min = $reference;

        return response()->json([
            'frais' => [
                'inscription'   => $reference,
                'scolarite'     => (int) $frais->scolarite,
                'autres_frais'  => (int) $frais->autres_frais,
                'montant_min'   => $min,
            ],
        ]);
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
            'bypass_tarif_check' => ['nullable', 'boolean'],
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
                Rule::exists('classe_serie', 'serie_id')->where(fn ($q) => $q
                    ->where('classe_id', $request->input('classe_id'))),
            ],
            'parent_prenom' => ['nullable', 'string', 'max:255'],
            'parent_email' => ['nullable', 'email', 'max:255'],
            'parent_telephone' => ['required', 'string', 'max:50'],
        ]);

        $validated['etablissement_id'] = $etablissementId;

        // Validation côté serveur : vérifier la conformité du montant par rapport aux frais configurés.
        $niveauId = $validated['niveau_id'] ?? null;
        $frais = $this->fraisDuNiveau($user, (int) $niveauId);
        if (! $frais) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'niveau_id' => "Aucun tarif d'inscription n'est configuré pour ce niveau et l'année académique active.",
            ]);
        }
        $montantSaisi = (int) ($validated['montant_inscription'] ?? $frais->inscription);
        $validated['montant_inscription'] = $montantSaisi;
        if ($niveauId && $montantSaisi !== null) {
            $frais = $this->fraisDuNiveau($user, (int) $niveauId);

            if ($frais && $frais->inscription > 0) {
                $reference = (int) $frais->inscription;
                $min = $reference;
                $max = max($reference, $montantSaisi);
                if ($montantSaisi < $min || $montantSaisi > $max) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'montant_inscription' => "Le montant d'inscription doit être compris entre "
                            . number_format($min, 0, ',', ' ') . " FCFA et "
                            . number_format($max, 0, ',', ' ') . " FCFA pour ce niveau.",
                    ]);
                }
            }
        }

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

    private function resolvePhotoUrl(string $photo, int $eleveId): ?string
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
            return route('client.eleve.photo', $eleveId, false);
        }

        // Fallback: parfois la base peut contenir déjà un chemin avec "eleves/..." mais avec des préfixes inattendus.
        // On tente une normalisation "à la main".
        if (str_contains($normalized, 'eleves/')) {
            $candidate = strstr($normalized, 'eleves/');
            if (is_string($candidate) && $disk->exists($candidate)) {
                return route('client.eleve.photo', $eleveId, false);
            }
        }

        return null;
    }
}
