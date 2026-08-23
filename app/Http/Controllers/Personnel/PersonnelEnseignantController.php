<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Models\{Classe, EmploiTemps, Enseignant, Etablissement, Matiere, Series, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PersonnelEnseignantController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Enseignant::with(['matieres', 'classes', 'series'])
            ->where('tenant_id', $user->tenant_id)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search');
                $q->where(function ($sub) use ($search) {
                    $sub->where('nom', 'like', "%{$search}%")
                        ->orWhere('prenoms', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('matricule', 'like', "%{$search}%");
                });
            })
            ->latest();

        $enseignants = $query->paginate(10)->withQueryString();
        $matieres = $this->matieresFor($user);

        $totalSchedules = EmploiTemps::where('tenant_id', $user->tenant_id)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->distinct('enseignant_id')
            ->count('enseignant_id');

        return view('personnel.enseignants.index', [
            'teachers' => $enseignants->through(fn ($teacher) => $this->teacherPayload($teacher)),
            'subjects' => $matieres->map(fn ($m) => ['id' => $m->id, 'name' => $m->nom]),
            'classes' => $this->classesFor($user),
            'series' => $this->seriesFor($user),
            'totalTeachers' => Enseignant::where('tenant_id', $user->tenant_id)
                ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
                ->count(),
            'totalSubjects' => $matieres->count(),
            'totalSchedules' => $totalSchedules,
            'avgPerSubject' => $matieres->count() ? round($enseignants->total() / $matieres->count(), 1) : 0,
        ]);
    }

    public function create()
    {
        $user = auth()->user();

        return view('personnel.enseignants.create', [
            'matieres' => $this->matieresFor($user),
            'classes' => $this->classesFor($user),
            'series' => $this->seriesFor($user),
        ]);
    }

    public function store(Request $request)
    {
        $this->normaliseRelationIds($request);
        $validated = $this->validateEnseignant($request);
        $user = auth()->user();

        $teacher = DB::transaction(function () use ($validated, $user, $request) {
            $attrs = $this->attributes($validated, $user, $request);
            $teacher = Enseignant::create($attrs);

            // Créer ou associer le compte utilisateur pour l'enseignant
            $appUser = User::create([
                'tenant_id' => $attrs['tenant_id'],
                'etablissement_id' => $attrs['etablissement_id'],
                'nom' => $validated['nom'],
                'prenom' => $validated['prenoms'],
                'email' => $validated['email'],
                'telephone' => $validated['telephone'],
                'password' => Hash::make('12345678'),
                'must_change_password' => true,
                'role' => 'enseignant',
                'statut' => 'actif',
                'client_id' => $user->client_id ?? $user->id,
            ]);

            $teacher->update(['user_id' => $appUser->id]);
            $teacher->matieres()->sync($validated['matiere_ids']);
            $teacher->classes()->sync($validated['classe_ids']);
            $teacher->series()->sync($validated['serie_ids'] ?? []);

            return $teacher->load(['matieres', 'classes', 'series']);
        });

        return redirect()->route('personnel.enseignants.index')->with('success', 'Enseignant créé avec succès.');
    }

    public function edit(Enseignant $enseignant)
    {
        $this->authorizeTenant($enseignant);
        $user = auth()->user();

        $enseignant->load(['matieres', 'classes', 'series']);

        return view('personnel.enseignants.edit', [
            'enseignant' => $enseignant,
            'matieres' => $this->matieresFor($user),
            'classes' => $this->classesFor($user),
            'series' => $this->seriesFor($user),
        ]);
    }

    public function update(Request $request, Enseignant $enseignant)
    {
        $this->authorizeTenant($enseignant);
        $this->normaliseRelationIds($request);
        $validated = $this->validateEnseignant($request, $enseignant);

        DB::transaction(function () use ($validated, $enseignant, $request) {
            $enseignant->update($this->attributes($validated, null, $request, $enseignant));

            // Synchroniser le compte User correspondant
            if ($enseignant->user_id) {
                User::where('id', $enseignant->user_id)->update([
                    'nom' => $validated['nom'],
                    'prenom' => $validated['prenoms'],
                    'email' => $validated['email'],
                    'telephone' => $validated['telephone'],
                ]);
            }

            $enseignant->matieres()->sync($validated['matiere_ids']);
            $enseignant->classes()->sync($validated['classe_ids']);
            $enseignant->series()->sync($validated['serie_ids'] ?? []);
        });

        return redirect()->route('personnel.enseignants.index')->with('success', 'Enseignant mis à jour avec succès.');
    }

    public function destroy(Enseignant $enseignant)
    {
        $this->authorizeTenant($enseignant);

        DB::transaction(function () use ($enseignant) {
            $enseignant->matieres()->detach();
            $enseignant->classes()->detach();
            $enseignant->series()->detach();

            if ($enseignant->photo && Storage::disk('public')->exists($enseignant->photo)) {
                Storage::disk('public')->delete($enseignant->photo);
            }

            $appUserId = $enseignant->user_id;
            $enseignant->delete();

            if ($appUserId) {
                User::where('id', $appUserId)->delete();
            }
        });

        return redirect()->route('personnel.enseignants.index')->with('success', 'Enseignant supprimé avec succès.');
    }

    private function attributes(array $v, $user = null, ?Request $request = null, ?Enseignant $teacher = null): array
    {
        $first = (int) $v['matiere_ids'][0];

        $data = [
            'nom' => $v['nom'],
            'prenoms' => $v['prenoms'],
            'email' => $v['email'],
            'telephone' => $v['telephone'],
            'matricule' => $v['matricule'],
            'nombre_annees_enseignement' => $v['nombre_annees_enseignement'],
            'sexe' => $v['sexe'],
            'matiere_id' => $first,
            'specialite' => Matiere::find($first)?->nom,
        ];

        if ($user) {
            $data += [
                'tenant_id' => $user->tenant_id,
                'etablissement_id' => $user->etablissement_id ?? Etablissement::where('tenant_id', $user->tenant_id)->value('id'),
                'password' => Hash::make('12345678'),
                'statut' => 'active',
            ];
        }

        if ($request?->hasFile('photo')) {
            if ($teacher?->photo && Storage::disk('public')->exists($teacher->photo)) {
                Storage::disk('public')->delete($teacher->photo);
            }
            $data['photo'] = $request->file('photo')->store('enseignants', 'public');
        }

        return $data;
    }

    private function validateEnseignant(Request $request, ?Enseignant $teacher = null): array
    {
        $user = auth()->user();
        $ignoreId = $teacher?->id;

        return $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'prenoms' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('enseignants', 'email')->ignore($ignoreId),
                Rule::unique('users', 'email')->ignore($teacher?->user_id),
            ],
            'telephone' => ['required', 'string', 'max:50'],
            'matricule' => [
                'required',
                'string',
                'max:100',
                Rule::unique('enseignants', 'matricule')->ignore($ignoreId),
            ],
            'nombre_annees_enseignement' => ['required', 'integer', 'min:0', 'max:80'],
            'sexe' => ['required', 'in:Masculin,Féminin'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'matiere_ids' => ['required', 'array', 'min:1', 'max:2'],
            'matiere_ids.*' => [Rule::exists('matieres', 'id')->where(fn ($q) => $q->where('tenant_id', $user->tenant_id))],
            'classe_ids' => ['required', 'array', 'min:1'],
            'classe_ids.*' => [Rule::exists('classes', 'id')->where(fn ($q) => $q->where('tenant_id', $user->tenant_id))],
            'serie_ids' => ['nullable', 'array'],
            'serie_ids.*' => [Rule::exists('series', 'id')->where(fn ($q) => $q->where('tenant_id', $user->tenant_id))],
        ]);
    }

    private function teacherPayload(Enseignant $t): array
    {
        return [
            'id' => $t->id,
            'firstname' => $t->prenoms ?? '',
            'lastname' => $t->nom,
            'email' => $t->email,
            'phone' => $t->telephone,
            'matricule' => $t->matricule,
            'teaching_years' => $t->nombre_annees_enseignement,
            'sexe' => $t->sexe,
            'photo' => $t->photo,
            'subject_ids' => $t->matieres->pluck('id')->all(),
            'subject' => ($t->matieres->pluck('nom')->join(', ') ?: 'Non assignée'),
            'status' => $t->statut,
            'class_ids' => $t->classes->pluck('id')->all(),
            'classes' => $t->classes->pluck('nom')->join(', ') ?: 'Non assignée',
            'serie_ids' => $t->series->pluck('id')->all(),
        ];
    }

    private function authorizeTenant(Enseignant $teacher): void
    {
        $user = auth()->user();
        abort_unless(
            (int) $teacher->tenant_id === (int) $user->tenant_id
            && (! $user->etablissement_id || (int) $teacher->etablissement_id === (int) $user->etablissement_id),
            403
        );
    }

    private function normaliseRelationIds(Request $request): void
    {
        $values = [];
        foreach (['matiere_ids', 'classe_ids', 'serie_ids'] as $field) {
            $items = $request->input($field, []);
            $items = is_array($items) ? $items : [$items];
            $values[$field] = collect($items)
                ->flatMap(fn ($item) => preg_split('/\s*,\s*/', (string) $item, -1, PREG_SPLIT_NO_EMPTY))
                ->filter(fn ($id) => ctype_digit((string) $id))
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->all();
        }
        $request->merge($values);
    }

    private function matieresFor($user)
    {
        return Matiere::query()
            ->where('tenant_id', $user->tenant_id)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->orderBy('nom')
            ->get(['id', 'nom']);
    }

    private function classesFor($user)
    {
        return Classe::query()
            ->where('tenant_id', $user->tenant_id)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->orderBy('nom')
            ->get(['id', 'nom']);
    }

    private function seriesFor($user)
    {
        return Series::query()
            ->where('tenant_id', $user->tenant_id)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->orderBy('nom_serie')
            ->get(['id', 'nom_serie']);
    }
}
