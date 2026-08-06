<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Models\{AnneeAcademique, Classe, EmploiTemps, EmploiTempsSlot, Enseignant, Etablissement, Matiere, Niveau, Series};
use App\Services\EmploiTempsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PersonnelEmploiTempsController extends Controller
{
    protected EmploiTempsService $service;

    public function __construct()
    {
        $this->service = new EmploiTempsService();
    }

    /**
     * Liste des emplois du temps des classes (avec filtres).
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $tenantId = $user->tenant_id;

        $classes = Classe::with('niveau')
            ->where('tenant_id', $tenantId)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->orderBy('nom')
            ->get();

        $years = AnneeAcademique::where('tenant_id', $tenantId)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->orderByDesc('date_debut')
            ->get();

        $classId = $request->integer('classe_id');
        $yearId = $request->integer('annee_academique_id');

        $entries = EmploiTemps::with(['matiere', 'enseignant', 'classe', 'serie', 'anneeAcademique'])
            ->where('tenant_id', $tenantId)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->when($classId, fn ($q) => $q->where('classe_id', $classId))
            ->when($yearId, fn ($q) => $q->where('annee_academique_id', $yearId))
            ->orderBy('heure_debut')
            ->get();

        // Classes qui ont déjà un emploi du temps (pour l'année sélectionnée)
        $classesWithSchedule = EmploiTemps::where('tenant_id', $tenantId)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->when($yearId, fn ($q) => $q->where('annee_academique_id', $yearId))
            ->distinct()
            ->pluck('classe_id')
            ->all();

        return view('personnel.emploi-temps.index', [
            'entries' => $entries,
            'classes' => $classes,
            'years' => $years,
            'selectedClass' => $classId,
            'selectedYear' => $yearId,
            'classesWithSchedule' => $classesWithSchedule,
            'establishments' => Etablissement::where('tenant_id', $tenantId)->get(),
        ]);
    }

    /**
     * Édition de l'emploi du temps d'une classe (création si absent, sinon édition).
     */
    public function edit(Request $request)
    {
        $user = auth()->user();
        $tenantId = $user->tenant_id;

        $validated = $request->validate([
            'etablissement_id' => ['nullable', 'integer'],
            'annee_academique_id' => ['nullable', 'integer'],
            'niveau_id' => ['nullable', 'integer'],
            'serie_id' => ['nullable', 'integer'],
            'classe_id' => ['required', 'integer'],
        ]);

        $classe = Classe::with(['niveau', 'series'])
            ->where('tenant_id', $tenantId)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->findOrFail($validated['classe_id']);

        $anneeId = $validated['annee_academique_id'] ?? null;
        $serieId = $validated['serie_id'] ?? null;

        $entries = EmploiTemps::with(['matiere', 'enseignant', 'classe', 'serie'])
            ->where('tenant_id', $tenantId)
            ->where('classe_id', $classe->id)
            ->when($anneeId, fn ($q) => $q->where('annee_academique_id', $anneeId))
            ->get();

        $savedSlots = EmploiTempsSlot::where('tenant_id', $tenantId)
            ->whereIn('enseignant_id', $entries->pluck('enseignant_id')->unique())
            ->get()
            ->groupBy('slot_key')
            ->map->first();

        $slots = $this->service->slotsFor($entries, $savedSlots);
        $grid = $this->service->buildGrid($entries);

        // Matières et enseignants cohérents avec la classe (affiliations existantes)
        $matieres = $this->matieresForClasse($classe, $serieId);
        $enseignants = $classe->enseignants()->orderBy('nom')->get();

        $annees = AnneeAcademique::where('tenant_id', $tenantId)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->orderByDesc('date_debut')
            ->get();

        return view('personnel.emploi-temps.create', [
            'classe' => $classe,
            'anneeId' => $anneeId,
            'serieId' => $serieId,
            'entries' => $entries,
            'grid' => $grid,
            'days' => $this->service->days(),
            'slots' => $slots,
            'matieres' => $matieres,
            'enseignants' => $enseignants,
            'annees' => $annees,
            'school' => Etablissement::find($classe->etablissement_id),
            'totalSeances' => $entries->count(),
            'existing' => $entries->isNotEmpty(),
            'establishments' => Etablissement::where('tenant_id', $tenantId)->get(),
        ]);
    }

    /**
     * Enregistre l'emploi du temps d'une classe.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $tenantId = $user->tenant_id;

        $data = $request->validate([
            'classe_id' => ['required', 'integer'],
            'annee_academique_id' => ['nullable', 'integer'],
            'serie_id' => ['nullable', 'integer'],
            'cells' => ['nullable', 'array'],
            'cells.*.matiere_id' => ['nullable', 'integer'],
            'cells.*.enseignant_id' => ['nullable', 'integer'],
            'cells.*.salle' => ['nullable', 'string', 'max:100'],
        ]);

        $classe = Classe::where('tenant_id', $tenantId)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->findOrFail($data['classe_id']);

        $anneeId = $data['annee_academique_id'] ?? null;
        $serieId = $data['serie_id'] ?? null;

        $matieres = $this->matieresForClasse($classe, $serieId)->pluck('id')->all();
        $enseignants = $classe->enseignants()->pluck('enseignants.id')->all();

        $entries = [];
        foreach ($data['cells'] ?? [] as $key => $cell) {
            if (empty($cell['matiere_id']) || empty($cell['enseignant_id'])) {
                continue;
            }
            $parts = explode('|', $key);
            if (count($parts) !== 2 || ! in_array($parts[0], $this->service->days(), true)) {
                continue;
            }

            $matiereId = (int) $cell['matiere_id'];
            $enseignantId = (int) $cell['enseignant_id'];

            if (! in_array($matiereId, $matieres, true)) {
                throw ValidationException::withMessages(['cells' => 'La matière choisie n\'est pas cohérente avec cette classe.']);
            }
            if (! in_array($enseignantId, $enseignants, true)) {
                throw ValidationException::withMessages(['cells' => 'Cet enseignant n\'est pas affecté à cette classe.']);
            }

            $slotDef = collect($this->service->defaultSlots())->firstWhere('key', $parts[1]);
            if (! $slotDef) {
                continue;
            }

            $entries[] = [
                'tenant_id' => $tenantId,
                'etablissement_id' => $classe->etablissement_id,
                'annee_academique_id' => $anneeId,
                'classe_id' => $classe->id,
                'serie_id' => $serieId,
                'matiere_id' => $matiereId,
                'enseignant_id' => $enseignantId,
                'jour' => $parts[0],
                'heure_debut' => $slotDef['start'],
                'heure_fin' => $slotDef['end'],
                'salle' => trim($cell['salle'] ?? '') ?: null,
                'slot_key' => $parts[1],
            ];
        }

        $this->validateConflicts($tenantId, $entries, $classe->id, $anneeId);

        DB::transaction(function () use ($tenantId, $classe, $anneeId, $entries) {
            EmploiTemps::where('tenant_id', $tenantId)
                ->where('classe_id', $classe->id)
                ->when($anneeId, fn ($q) => $q->where('annee_academique_id', $anneeId))
                ->delete();

            foreach ($entries as $entry) {
                EmploiTemps::create($entry);
            }
        });

        return redirect()->route('personnel.emploi-temps.show', [
            'classe_id' => $classe->id,
            'annee_academique_id' => $anneeId,
        ])->with('success', 'Emploi du temps '.($entries->isEmpty() ? 'réinitialisé' : 'enregistré').' avec succès.');
    }

    /**
     * Affiche l'emploi du temps d'une classe.
     */
    public function show(Request $request)
    {
        $user = auth()->user();
        $tenantId = $user->tenant_id;

        $classe = Classe::with(['niveau', 'series'])
            ->where('tenant_id', $tenantId)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->findOrFail($request->integer('classe_id'));

        $anneeId = $request->integer('annee_academique_id');

        $entries = EmploiTemps::with(['matiere', 'enseignant', 'classe', 'serie'])
            ->where('tenant_id', $tenantId)
            ->where('classe_id', $classe->id)
            ->when($anneeId, fn ($q) => $q->where('annee_academique_id', $anneeId))
            ->get();

        $savedSlots = EmploiTempsSlot::where('tenant_id', $tenantId)
            ->whereIn('enseignant_id', $entries->pluck('enseignant_id')->unique())
            ->get()
            ->groupBy('slot_key')
            ->map->first();

        return view('personnel.emploi-temps.show', [
            'classe' => $classe,
            'entries' => $entries,
            'grid' => $this->service->buildGrid($entries),
            'days' => $this->service->days(),
            'slots' => $this->service->slotsFor($entries, $savedSlots),
            'school' => Etablissement::find($classe->etablissement_id),
            'year' => $anneeId ? AnneeAcademique::where('tenant_id', $tenantId)->find($anneeId) : null,
            'totalSeances' => $entries->count(),
        ]);
    }

    /**
     * Impression de l'emploi du temps d'une classe.
     */
    public function print(Request $request)
    {
        return view('personnel.emploi-temps.print', $this->classScheduleData($request) + ['printMode' => true]);
    }

    /**
     * Téléchargement PDF de l'emploi du temps d'une classe.
     */
    public function pdf(Request $request)
    {
        $data = $this->classScheduleData($request);
        return Pdf::loadView('personnel.emploi-temps.print', $data + ['printMode' => true, 'pdfMode' => true])
            ->setPaper('a4', 'landscape')
            ->download('emploi-du-temps-'.$data['classe']->id.'.pdf');
    }

    /**
     * Supprime l'emploi du temps d'une classe pour une année.
     */
    public function destroy(Request $request)
    {
        $user = auth()->user();
        $tenantId = $user->tenant_id;

        $classe = Classe::where('tenant_id', $tenantId)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->findOrFail($request->integer('classe_id'));

        $anneeId = $request->integer('annee_academique_id');

        EmploiTemps::where('tenant_id', $tenantId)
            ->where('classe_id', $classe->id)
            ->when($anneeId, fn ($q) => $q->where('annee_academique_id', $anneeId))
            ->delete();

        return redirect()->route('personnel.emploi-temps.index')
            ->with('success', 'Emploi du temps supprimé avec succès.');
    }

    /**
     * Données partagées pour show/print/pdf.
     */
    private function classScheduleData(Request $request): array
    {
        $user = auth()->user();
        $tenantId = $user->tenant_id;

        $classe = Classe::with(['niveau', 'series'])
            ->where('tenant_id', $tenantId)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->findOrFail($request->integer('classe_id'));

        $anneeId = $request->integer('annee_academique_id');

        $entries = EmploiTemps::with(['matiere', 'enseignant', 'classe', 'serie'])
            ->where('tenant_id', $tenantId)
            ->where('classe_id', $classe->id)
            ->when($anneeId, fn ($q) => $q->where('annee_academique_id', $anneeId))
            ->get();

        $savedSlots = EmploiTempsSlot::where('tenant_id', $tenantId)
            ->whereIn('enseignant_id', $entries->pluck('enseignant_id')->unique())
            ->get()
            ->groupBy('slot_key')
            ->map->first();

        return [
            'classe' => $classe,
            'entries' => $entries,
            'grid' => $this->service->buildGrid($entries),
            'days' => $this->service->days(),
            'slots' => $this->service->slotsFor($entries, $savedSlots),
            'school' => Etablissement::find($classe->etablissement_id),
            'year' => $anneeId ? AnneeAcademique::where('tenant_id', $tenantId)->find($anneeId) : null,
            'totalSeances' => $entries->count(),
        ];
    }

    /**
     * Matières cohérentes avec une classe (via la série / affiliations).
     */
    private function matieresForClasse(Classe $classe, ?int $serieId): \Illuminate\Support\Collection
    {
        $tenantId = auth()->user()->tenant_id;

        if ($serieId) {
            $serie = Series::where('tenant_id', $tenantId)->with('matieres')->find($serieId);
            if ($serie && $serie->matieres->isNotEmpty()) {
                return $serie->matieres()->where('matieres.tenant_id', $tenantId)->orderBy('matieres.nom')->get();
            }
        }

        // Repli : matières des enseignants affectés à la classe
        $matiereIds = $classe->enseignants()->pluck('enseignants.matiere_id')->filter()->unique();

        return Matiere::where('tenant_id', $tenantId)
            ->whereIn('id', $matiereIds)
            ->orderBy('nom')
            ->get();
    }

    /**
     * Vérifie les conflits (enseignant à deux classes au même moment, salle...).
     */
    private function validateConflicts(int $tenantId, array $entries, int $classeId, ?int $anneeId): void
    {
        foreach ($entries as $a) {
            $base = EmploiTemps::query()
                ->where('emploi_temps.tenant_id', $tenantId)
                ->where('emploi_temps.jour', $a['jour'])
                ->where('emploi_temps.heure_debut', '<', $a['heure_fin'])
                ->where('emploi_temps.heure_fin', '>', $a['heure_debut'])
                ->when($anneeId, fn ($q) => $q->where('emploi_temps.annee_academique_id', $anneeId));

            // Conflit enseignant : déjà affecté à une AUTRE classe à ce moment
            if ((clone $base)->where('emploi_temps.enseignant_id', $a['enseignant_id'])
                ->where('emploi_temps.classe_id', '<>', $classeId)
                ->exists()) {
                throw ValidationException::withMessages(['cells' => 'Cet enseignant est déjà affecté à une autre classe sur ce créneau.']);
            }

            // Conflit salle
            if (! empty($a['salle']) && (clone $base)->where('emploi_temps.salle', $a['salle'])
                ->where('emploi_temps.classe_id', '<>', $classeId)
                ->exists()) {
                throw ValidationException::withMessages(['cells' => 'Cette salle est déjà occupée par une autre classe sur ce créneau.']);
            }
        }
    }
}
