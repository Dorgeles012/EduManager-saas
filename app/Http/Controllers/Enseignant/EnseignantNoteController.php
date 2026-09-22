<?php

namespace App\Http\Controllers\Enseignant;

use App\Http\Controllers\Controller;
use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Enseignant;
use App\Models\Matiere;
use App\Models\Note;
use App\Services\BulletinService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class EnseignantNoteController extends Controller
{
    /**
     * Récupère l'enseignant connecté
     */
    private function getEnseignant(): ?Enseignant
    {
        $user = auth()->user();
        return Enseignant::where('tenant_id', $user->tenant_id)
            ->where('user_id', $user->id)
            ->with(['matieres', 'classes', 'series', 'matiere'])
            ->first();
    }

    /**
     * Récupère les matières assignées à l'enseignant
     */
    private function getAssignedSubjects(Enseignant $enseignant, int $tenantId)
    {
        $assignedSubjectIds = $enseignant->getAssignedSubjectIds();

        if (empty($assignedSubjectIds)) {
            return collect();
        }

        return Matiere::whereIn('id', $assignedSubjectIds)
            ->where('tenant_id', $tenantId)
            ->orderBy('nom')
            ->get();
    }

    /**
     * Affiche la liste des notes pour les classes/matières de l'enseignant
     */
    public function index(Request $request): View
    {
        $enseignant = $this->getEnseignant();
        $tenantId = auth()->user()->tenant_id;

        if (!$enseignant) {
            return view('enseignant.notes.index', [
                'grades' => collect(),
                'classes' => collect(),
                'subjects' => collect(),
                'students' => collect(),
                'years' => collect(),
                'selectedClass' => null,
                'selectedSubject' => null,
                'selectedStudent' => null,
                'selectedPeriode' => null,
                'selectedStatus' => null,
                'totalStudents' => 0,
                'totalSubjects' => 0,
                'totalClasses' => 0,
                'totalGrades' => 0,
                'draftCount' => 0,
                'submittedCount' => 0,
                'rejectedCount' => 0,
                'publishedCount' => 0,
                'studentAverages' => collect(),
            ]);
        }

        $assignedClassIds = $enseignant->classes->pluck('id')->map(fn ($id) => (int) $id)->toArray();
        $assignedSubjectIds = $enseignant->getAssignedSubjectIds();

        $selectedClass = $request->integer('classe_id');
        $selectedSubject = $request->integer('matiere_id');
        $selectedStudent = $request->integer('eleve_id');
        $selectedPeriode = $request->input('periode');
        $selectedStatus = $request->input('statut');

        // Sécurité : l'enseignant ne peut jamais filtrer ou voir une matière qui ne lui appartient pas
        if ($selectedSubject && !in_array($selectedSubject, $assignedSubjectIds, true)) {
            abort(403, "Accès refusé : Vous n'êtes pas l'enseignant responsable de cette matière.");
        }

        // Si l'enseignant n'a qu'une seule matière, elle est sélectionnée par défaut automatiquement
        if (!$selectedSubject && count($assignedSubjectIds) === 1) {
            $selectedSubject = $assignedSubjectIds[0];
        }

        $query = Note::query()
            ->with(['eleve', 'classe', 'matiere', 'anneeAcademique'])
            ->where('tenant_id', $tenantId)
            ->where('enseignant_id', $enseignant->id)
            ->whereIn('classe_id', $assignedClassIds)
            ->whereIn('matiere_id', $assignedSubjectIds);

        if ($selectedClass) {
            $query->where('classe_id', $selectedClass);
        }
        if ($selectedSubject) {
            $query->where('matiere_id', $selectedSubject);
        }
        if ($selectedStudent) {
            $query->where('eleve_id', $selectedStudent);
        }
        if ($selectedPeriode) {
            $query->where('periode', $selectedPeriode);
        }
        if ($selectedStatus) {
            $query->where('statut', $selectedStatus);
        }

        $grades = $query->latest('id')->paginate(20)->withQueryString();

        // Calcul des appréciations
        $grades->getCollection()->transform(function (Note $grade) {
            $grade->appreciation = BulletinService::noteAppreciation((float) $grade->note);
            return $grade;
        });

        $classes = Classe::whereIn('id', $assignedClassIds)
            ->where('tenant_id', $tenantId)
            ->orderBy('nom')
            ->get();

        $subjects = $this->getAssignedSubjects($enseignant, $tenantId);

        $studentsQuery = Eleve::where('tenant_id', $tenantId)
            ->whereIn('classe_id', $assignedClassIds);
        if ($selectedClass) {
            $studentsQuery->where('classe_id', $selectedClass);
        }
        $students = $studentsQuery->orderBy('nom')->get();

        $years = AnneeAcademique::where('tenant_id', $tenantId)->latest('id')->get();

        // Statistiques
        $baseStatQuery = Note::where('tenant_id', $tenantId)
            ->where('enseignant_id', $enseignant->id)
            ->whereIn('classe_id', $assignedClassIds)
            ->whereIn('matiere_id', $assignedSubjectIds);

        $totalGrades = (clone $baseStatQuery)->count();
        $draftCount = (clone $baseStatQuery)->where('statut', Note::STATUT_BROUILLON)->count();
        $submittedCount = (clone $baseStatQuery)->where('statut', Note::STATUT_SOUMIS)->count();
        $rejectedCount = (clone $baseStatQuery)->whereIn('statut', [Note::STATUT_REJETE_PERSONNEL, Note::STATUT_REJETE_CLIENT])->count();
        $publishedCount = (clone $baseStatQuery)->where('statut', Note::STATUT_PUBLIE)->count();

        // Calcul des moyennes par élève dans la classe/matière/période sélectionnée
        $studentAverages = collect();
        if ($selectedClass && $selectedSubject && $selectedPeriode) {
            $notesGrouped = Note::where('tenant_id', $tenantId)
                ->where('enseignant_id', $enseignant->id)
                ->where('classe_id', $selectedClass)
                ->where('matiere_id', $selectedSubject)
                ->where('periode', $selectedPeriode)
                ->get()
                ->groupBy('eleve_id');

            foreach ($students as $student) {
                $studentNotes = $notesGrouped->get($student->id, collect());
                $avg = $studentNotes->isNotEmpty() ? round((float) $studentNotes->avg('note'), 2) : null;
                $studentAverages->put($student->id, [
                    'student' => $student,
                    'count' => $studentNotes->count(),
                    'average' => $avg,
                    'appreciation' => BulletinService::noteAppreciation($avg),
                    'notes' => $studentNotes,
                ]);
            }
        }

        return view('enseignant.notes.index', [
            'grades' => $grades,
            'classes' => $classes,
            'subjects' => $subjects,
            'students' => $students,
            'years' => $years,
            'selectedClass' => $selectedClass,
            'selectedSubject' => $selectedSubject,
            'selectedStudent' => $selectedStudent,
            'selectedPeriode' => $selectedPeriode,
            'selectedStatus' => $selectedStatus,
            'totalStudents' => $students->count(),
            'totalSubjects' => $subjects->count(),
            'totalClasses' => $classes->count(),
            'totalGrades' => $totalGrades,
            'draftCount' => $draftCount,
            'submittedCount' => $submittedCount,
            'rejectedCount' => $rejectedCount,
            'publishedCount' => $publishedCount,
            'studentAverages' => $studentAverages,
        ]);
    }

    /**
     * Vérifie si l'enseignant a le droit d'accéder à une note.
     */
    private function noteAccessible(Enseignant $enseignant, Note $note, int $tenantId): bool
    {
        if ((int) $note->tenant_id !== (int) $tenantId) {
            return false;
        }

        $assignedClassIds = $enseignant->classes->pluck('id')->map(fn ($id) => (int) $id)->toArray();
        $assignedSubjectIds = $enseignant->getAssignedSubjectIds();

        // L'enseignant ne peut accéder qu'aux notes de sa propre matière et de ses classes assignées
        if (!in_array((int) $note->classe_id, $assignedClassIds, true)) {
            return false;
        }

        if (!in_array((int) $note->matiere_id, $assignedSubjectIds, true)) {
            return false;
        }

        // Vérification stricte que la note appartient à cet enseignant
        if ($note->enseignant_id !== null && (int) $note->enseignant_id !== (int) $enseignant->id) {
            return false;
        }

        return true;
    }

    /**
     * Retourne les données JSON pour les filtres dynamiques
     */
    public function data(Request $request)
    {
        $enseignant = $this->getEnseignant();
        $tenantId = auth()->user()->tenant_id;

        if (!$enseignant) {
            return response()->json(['students' => [], 'subjects' => []]);
        }

        $classId = $request->integer('classe_id');
        $subjects = $this->getAssignedSubjects($enseignant, $tenantId)->map(fn ($m) => ['id' => $m->id, 'nom' => $m->nom]);

        $students = collect();
        if ($classId) {
            $students = Eleve::where('tenant_id', $tenantId)
                ->where('classe_id', $classId)
                ->orderBy('nom')
                ->get(['id', 'nom', 'prenom', 'matricule']);
        }

        return response()->json([
            'subjects' => $subjects,
            'students' => $students,
        ]);
    }

    /**
     * Retourne les données d'une note (JSON)
     */
    public function edit(Request $request, $id)
    {
        $enseignant = $this->getEnseignant();
        $tenantId = auth()->user()->tenant_id;

        if (!$enseignant) {
            return response()->json(['message' => 'Enseignant non trouvé.'], 404);
        }

        $note = Note::with(['eleve', 'classe', 'matiere'])->where('tenant_id', $tenantId)->find($id);

        if (!$note) {
            return response()->json(['message' => 'Note non trouvée.'], 404);
        }

        if (!$this->noteAccessible($enseignant, $note, $tenantId)) {
            return response()->json(['message' => 'Vous n\'êtes pas autorisé à modifier cette note car cette matière ne vous est pas assignée.'], 403);
        }

        return response()->json([
            'note' => array_merge($note->toArray(), [
                'student_nom' => $note->eleve?->nom,
                'student_prenom' => $note->eleve?->prenom,
                'class_name' => $note->classe?->nom,
                'subject_name' => $note->matiere?->nom,
                'appreciation' => BulletinService::noteAppreciation((float) $note->note),
            ]),
        ]);
    }

    /**
     * Enregistre une nouvelle note
     */
    public function store(Request $request)
    {
        $enseignant = $this->getEnseignant();
        $tenantId = auth()->user()->tenant_id;

        if (!$enseignant) {
            return response()->json(['message' => 'Enseignant non trouvé.'], 404);
        }

        $assignedSubjectIds = $enseignant->getAssignedSubjectIds();
        $assignedClassIds = $enseignant->classes->pluck('id')->map(fn ($id) => (int) $id)->toArray();

        // Liaison automatique de la matière si non fournie ou si 1 seule matière
        $matiereId = $request->integer('matiere_id');
        if (!$matiereId && count($assignedSubjectIds) === 1) {
            $matiereId = $assignedSubjectIds[0];
            $request->merge(['matiere_id' => $matiereId]);
        }

        $validated = $request->validate([
            'eleve_id' => ['required', 'integer'],
            'classe_id' => ['required', 'integer'],
            'matiere_id' => ['required', 'integer'],
            'annee_academique_id' => ['nullable', 'integer'],
            'titre_evaluation' => ['nullable', 'string', 'max:150'],
            'type_evaluation' => ['nullable', 'string', 'in:interrogation,devoir,composition,autre'],
            'note' => ['required', 'numeric', 'min:0', 'max:20'],
            'periode' => ['required', 'string', 'max:100'],
            'appreciation' => ['nullable', 'string', 'max:500'],
        ]);

        if (!in_array((int) $validated['matiere_id'], $assignedSubjectIds, true)) {
            throw ValidationException::withMessages([
                'matiere_id' => "Accès refusé : Vous n'êtes pas l'enseignant responsable de cette matière. Seul l'enseignant affecté peut saisir des notes pour cette matière."
            ]);
        }

        if (!in_array((int) $validated['classe_id'], $assignedClassIds, true)) {
            throw ValidationException::withMessages(['classe_id' => 'Cette classe ne vous est pas assignée.']);
        }

        $eleve = Eleve::where('tenant_id', $tenantId)
            ->where('id', $validated['eleve_id'])
            ->where('classe_id', $validated['classe_id'])
            ->first();

        if (!$eleve) {
            throw ValidationException::withMessages(['eleve_id' => 'Cet élève n\'existe pas dans cette classe.']);
        }

        $anneeId = $validated['annee_academique_id'] ?? AnneeAcademique::where('tenant_id', $tenantId)->latest('id')->value('id');

        $note = Note::create([
            'tenant_id' => $tenantId,
            'etablissement_id' => $enseignant->etablissement_id ?? $eleve->etablissement_id,
            'eleve_id' => $validated['eleve_id'],
            'classe_id' => $validated['classe_id'],
            'matiere_id' => $validated['matiere_id'],
            'enseignant_id' => $enseignant->id,
            'annee_academique_id' => $anneeId,
            'titre_evaluation' => $validated['titre_evaluation'] ?? 'Évaluation',
            'type_evaluation' => $validated['type_evaluation'] ?? Note::TYPE_DEVOIR,
            'note' => $validated['note'],
            'periode' => $validated['periode'],
            'appreciation' => BulletinService::noteAppreciation((float) $validated['note']),
            'statut' => Note::STATUT_BROUILLON,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Note enregistrée avec succès (Brouillon).', 'id' => $note->id]);
        }

        return redirect()->route('enseignant.notes.index', [
            'classe_id' => $validated['classe_id'],
            'matiere_id' => $validated['matiere_id'],
            'periode' => $validated['periode'],
        ])->with('success', 'Note enregistrée avec succès en brouillon.');
    }

    /**
     * Saisie groupée de notes pour une classe entière
     */
    public function bulkStore(Request $request)
    {
        $enseignant = $this->getEnseignant();
        $tenantId = auth()->user()->tenant_id;

        if (!$enseignant) {
            return redirect()->back()->with('error', 'Enseignant non trouvé.');
        }

        $assignedSubjectIds = $enseignant->getAssignedSubjectIds();
        $assignedClassIds = $enseignant->classes->pluck('id')->map(fn ($id) => (int) $id)->toArray();

        // Liaison automatique de la matière si non fournie ou si 1 seule matière
        $matiereId = $request->integer('matiere_id');
        if (!$matiereId && count($assignedSubjectIds) === 1) {
            $matiereId = $assignedSubjectIds[0];
            $request->merge(['matiere_id' => $matiereId]);
        }

        $validated = $request->validate([
            'classe_id' => ['required', 'integer'],
            'matiere_id' => ['required', 'integer'],
            'annee_academique_id' => ['nullable', 'integer'],
            'titre_evaluation' => ['required', 'string', 'max:150'],
            'type_evaluation' => ['required', 'string', 'in:interrogation,devoir,composition,autre'],
            'periode' => ['required', 'string', 'max:100'],
            'notes' => ['required', 'array'],
            'notes.*' => ['nullable', 'numeric', 'min:0', 'max:20'],
        ]);

        if (!in_array((int) $validated['matiere_id'], $assignedSubjectIds, true)) {
            return redirect()->back()->with('error', "Accès refusé : Vous n'êtes pas l'enseignant responsable de cette matière. Seul l'enseignant affecté peut saisir les notes de cette matière.");
        }

        if (!in_array((int) $validated['classe_id'], $assignedClassIds, true)) {
            return redirect()->back()->with('error', 'Cette classe ne vous est pas assignée.');
        }

        $anneeId = $validated['annee_academique_id'] ?? AnneeAcademique::where('tenant_id', $tenantId)->latest('id')->value('id');
        $createdCount = 0;

        DB::transaction(function () use ($validated, $tenantId, $enseignant, $anneeId, &$createdCount) {
            foreach ($validated['notes'] as $eleveId => $noteValue) {
                if ($noteValue !== null && $noteValue !== '') {
                    Note::create([
                        'tenant_id' => $tenantId,
                        'etablissement_id' => $enseignant->etablissement_id,
                        'eleve_id' => (int) $eleveId,
                        'classe_id' => (int) $validated['classe_id'],
                        'matiere_id' => (int) $validated['matiere_id'],
                        'enseignant_id' => $enseignant->id,
                        'annee_academique_id' => $anneeId,
                        'titre_evaluation' => $validated['titre_evaluation'],
                        'type_evaluation' => $validated['type_evaluation'],
                        'note' => (float) $noteValue,
                        'periode' => $validated['periode'],
                        'appreciation' => BulletinService::noteAppreciation((float) $noteValue),
                        'statut' => Note::STATUT_BROUILLON,
                    ]);
                    $createdCount++;
                }
            }
        });

        return redirect()->route('enseignant.notes.index', [
            'classe_id' => $validated['classe_id'],
            'matiere_id' => $validated['matiere_id'],
            'periode' => $validated['periode'],
        ])->with('success', "{$createdCount} notes enregistrées avec succès en brouillon.");
    }

    /**
     * Soumet un ensemble de notes pour validation par le personnel
     */
    public function soumettre(Request $request)
    {
        $enseignant = $this->getEnseignant();
        $tenantId = auth()->user()->tenant_id;

        if (!$enseignant) {
            return redirect()->back()->with('error', 'Enseignant non trouvé.');
        }

        $assignedSubjectIds = $enseignant->getAssignedSubjectIds();

        // Liaison automatique de la matière si non fournie ou si 1 seule matière
        $matiereId = $request->integer('matiere_id');
        if (!$matiereId && count($assignedSubjectIds) === 1) {
            $matiereId = $assignedSubjectIds[0];
            $request->merge(['matiere_id' => $matiereId]);
        }

        $validated = $request->validate([
            'classe_id' => ['required', 'integer'],
            'matiere_id' => ['required', 'integer'],
            'periode' => ['required', 'string'],
            'note_ids' => ['nullable', 'array'],
        ]);

        if (!in_array((int) $validated['matiere_id'], $assignedSubjectIds, true)) {
            abort(403, "Action non autorisée : Vous ne pouvez soumettre que les notes de votre propre matière.");
        }

        $query = Note::where('tenant_id', $tenantId)
            ->where('classe_id', $validated['classe_id'])
            ->where('matiere_id', $validated['matiere_id'])
            ->where('enseignant_id', $enseignant->id)
            ->where('periode', $validated['periode'])
            ->whereIn('statut', [Note::STATUT_BROUILLON, Note::STATUT_REJETE_PERSONNEL, Note::STATUT_REJETE_CLIENT]);

        if (!empty($validated['note_ids'])) {
            $query->whereIn('id', $validated['note_ids']);
        }

        $updated = $query->update([
            'statut' => Note::STATUT_SOUMIS,
            'soumis_le' => Carbon::now(),
            'rejet_motif' => null,
            'rejet_par' => null,
            'updated_at' => Carbon::now(),
        ]);

        if ($updated === 0) {
            return redirect()->back()->with('warning', 'Aucune note en attente de soumission trouvée.');
        }

        return redirect()->route('enseignant.notes.index', [
            'classe_id' => $validated['classe_id'],
            'matiere_id' => $validated['matiere_id'],
            'periode' => $validated['periode'],
        ])->with('success', "{$updated} note(s) soumise(s) avec succès pour validation par le Personnel.");
    }

    /**
     * Modifie une note existante
     */
    public function update(Request $request, $id)
    {
        $enseignant = $this->getEnseignant();
        $tenantId = auth()->user()->tenant_id;

        if (!$enseignant) {
            return response()->json(['message' => 'Enseignant non trouvé.'], 404);
        }

        $note = Note::where('tenant_id', $tenantId)->find($id);

        if (!$note) {
            return response()->json(['message' => 'Note non trouvée.'], 404);
        }

        if (!$this->noteAccessible($enseignant, $note, $tenantId)) {
            return response()->json(['message' => 'Vous n\'êtes pas autorisé à modifier cette note car vous n\'êtes pas l\'enseignant responsable de cette matière.'], 403);
        }

        $assignedSubjectIds = $enseignant->getAssignedSubjectIds();

        $validated = $request->validate([
            'eleve_id' => ['required', 'integer'],
            'classe_id' => ['required', 'integer'],
            'matiere_id' => ['required', 'integer'],
            'titre_evaluation' => ['nullable', 'string', 'max:150'],
            'type_evaluation' => ['nullable', 'string', 'in:interrogation,devoir,composition,autre'],
            'note' => ['required', 'numeric', 'min:0', 'max:20'],
            'periode' => ['required', 'string', 'max:100'],
            'appreciation' => ['nullable', 'string', 'max:500'],
        ]);

        if (!in_array((int) $validated['matiere_id'], $assignedSubjectIds, true)) {
            return response()->json(['message' => 'Action non autorisée : vous ne pouvez pas changer la matière vers une matière qui ne vous est pas assignée.'], 403);
        }

        $wasValidated = $note->isValidee();
        $oldBulletinData = [
            $note->tenant_id, $note->classe_id, $note->eleve_id, $note->periode, $note->annee_academique_id,
        ];
        $oldData = $note->only([
            'eleve_id', 'classe_id', 'matiere_id', 'enseignant_id', 'titre_evaluation',
            'type_evaluation', 'note', 'periode', 'appreciation', 'statut', 'rejet_motif',
            'rejet_par', 'soumis_le', 'approuve_personnel_le', 'approuve_personnel_id',
            'publie_le', 'publie_par_id',
        ]);

        DB::transaction(function () use ($note, $validated, $enseignant, $wasValidated, $oldData) {
            $note->update([
                'eleve_id' => $validated['eleve_id'],
                'classe_id' => $validated['classe_id'],
                'matiere_id' => $validated['matiere_id'],
                'enseignant_id' => $enseignant->id,
                'titre_evaluation' => $validated['titre_evaluation'] ?? $note->titre_evaluation,
                'type_evaluation' => $validated['type_evaluation'] ?? $note->type_evaluation,
                'note' => $validated['note'],
                'periode' => $validated['periode'],
                'appreciation' => BulletinService::noteAppreciation((float) $validated['note']),
                'statut' => $wasValidated ? Note::STATUT_SOUMIS : $note->statut,
                'rejet_motif' => $wasValidated ? null : $note->rejet_motif,
                'rejet_par' => $wasValidated ? null : $note->rejet_par,
                'soumis_le' => $wasValidated ? Carbon::now() : $note->soumis_le,
                'approuve_personnel_le' => $wasValidated ? null : $note->approuve_personnel_le,
                'approuve_personnel_id' => $wasValidated ? null : $note->approuve_personnel_id,
                'publie_le' => $wasValidated ? null : $note->publie_le,
                'publie_par_id' => $wasValidated ? null : $note->publie_par_id,
            ]);

            $note->historiques()->create([
                'tenant_id' => $note->tenant_id,
                'action' => 'modification',
                'acteur_id' => auth()->id(),
                'avant' => $oldData,
                'apres' => $note->only(array_keys($oldData)),
            ]);
        });

        if ($wasValidated) {
            $bulletinService = app(BulletinService::class);
            $bulletinService->recalculerBulletinEnAttente(...$oldBulletinData);
            $bulletinService->recalculerBulletinEnAttente(
                $note->tenant_id, $note->classe_id, $note->eleve_id, $note->periode, $note->annee_academique_id
            );
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Note mise à jour avec succès.']);
        }

        return redirect()->route('enseignant.notes.index')
            ->with('success', 'Note modifiée avec succès.');
    }

    /**
     * Supprime une note
     */
    public function destroy(Request $request, $id)
    {
        $enseignant = $this->getEnseignant();
        $tenantId = auth()->user()->tenant_id;

        if (!$enseignant) {
            return response()->json(['message' => 'Enseignant non trouvé.'], 404);
        }

        $note = Note::where('tenant_id', $tenantId)->find($id);

        if (!$note) {
            return response()->json(['message' => 'Note non trouvée.'], 404);
        }

        if (!$this->noteAccessible($enseignant, $note, $tenantId)) {
            return response()->json(['message' => 'Vous n\'êtes pas autorisé à supprimer cette note car vous n\'êtes pas l\'enseignant responsable de cette matière.'], 403);
        }

        $wasValidated = $note->isValidee();
        $oldData = $note->toArray();
        $bulletinData = [$note->tenant_id, $note->classe_id, $note->eleve_id, $note->periode, $note->annee_academique_id];

        DB::transaction(function () use ($note, $oldData) {
            $note->historiques()->create([
                'tenant_id' => $note->tenant_id,
                'action' => 'suppression',
                'acteur_id' => auth()->id(),
                'avant' => $oldData,
                'apres' => null,
            ]);
            $note->delete();
        });

        if ($wasValidated) {
            app(BulletinService::class)->recalculerBulletinEnAttente(...$bulletinData);
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Note supprimée avec succès.']);
        }

        return redirect()->route('enseignant.notes.index')
            ->with('success', 'Note supprimée avec succès.');
    }
}
