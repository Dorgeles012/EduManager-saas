<?php

namespace App\Http\Controllers\Enseignant;

use App\Http\Controllers\Controller;
use App\Models\{Classe, Eleve, Enseignant, Matiere, Series};
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
            ->with(['matieres', 'classes', 'series'])
            ->first();
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
                'selectedClass' => null,
                'selectedSubject' => null,
                'selectedPeriode' => null,
                'totalStudents' => 0,
                'totalSubjects' => 0,
                'totalGrades' => 0,
            ]);
        }

        $assignedClassIds = $enseignant->classes->pluck('id');
        $assignedSubjectIds = $enseignant->matieres->pluck('id');

        $selectedClass = $request->integer('classe_id');
        $selectedSubject = $request->integer('matiere_id');
        $selectedPeriode = $request->input('periode');

        $query = DB::table('notes')
            ->join('eleves', 'notes.eleve_id', '=', 'eleves.id')
            ->join('classes', 'notes.classe_id', '=', 'classes.id')
            ->join('matieres', 'notes.matiere_id', '=', 'matieres.id')
            ->where('notes.tenant_id', $tenantId)
            ->whereIn('notes.classe_id', $assignedClassIds)
            ->whereIn('notes.matiere_id', $assignedSubjectIds);

        if ($selectedClass) {
            $query->where('notes.classe_id', $selectedClass);
        }
        if ($selectedSubject) {
            $query->where('notes.matiere_id', $selectedSubject);
        }
        if ($selectedPeriode) {
            $query->where('notes.periode', $selectedPeriode);
        }

        $grades = $query->select(
            'notes.id',
            'notes.eleve_id',
            'notes.classe_id',
            'notes.matiere_id',
            'notes.note',
            'notes.periode',
            'notes.appreciation',
            'notes.created_at',
            'eleves.nom as student_nom',
            'eleves.prenom as student_prenom',
            'eleves.matricule as student_matricule',
            'classes.nom as class_name',
            'matieres.nom as subject_name'
        )->orderBy('notes.created_at', 'desc')
        ->paginate(15);

        $classes = Classe::whereIn('id', $assignedClassIds)
            ->where('tenant_id', $tenantId)
            ->orderBy('nom')
            ->get();

        $subjects = Matiere::whereIn('id', $assignedSubjectIds)
            ->where('tenant_id', $tenantId)
            ->orderBy('nom')
            ->get();

        $students = Eleve::where('tenant_id', $tenantId)
            ->whereIn('classe_id', $assignedClassIds)
            ->orderBy('nom')
            ->get();

        $totalGrades = DB::table('notes')
            ->where('tenant_id', $tenantId)
            ->whereIn('classe_id', $assignedClassIds)
            ->whereIn('matiere_id', $assignedSubjectIds)
            ->count();

        return view('enseignant.notes.index', [
            'grades' => $grades,
            'classes' => $classes,
            'subjects' => $subjects,
            'students' => $students,
            'selectedClass' => $selectedClass,
            'selectedSubject' => $selectedSubject,
            'selectedPeriode' => $selectedPeriode,
            'totalStudents' => $students->count(),
            'totalSubjects' => $subjects->count(),
            'totalGrades' => $totalGrades,
        ]);
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

        $subjects = $enseignant->matieres()
            ->where('matieres.tenant_id', $tenantId)
            ->orderBy('matieres.nom')
            ->get(['matieres.id', 'matieres.nom']);

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
     * Enregistre une nouvelle note
     */
    public function store(Request $request)
    {
        $enseignant = $this->getEnseignant();
        $tenantId = auth()->user()->tenant_id;

        if (!$enseignant) {
            return response()->json(['message' => 'Enseignant non trouvé.'], 404);
        }

        $validated = $request->validate([
            'eleve_id' => ['required', 'integer'],
            'classe_id' => ['required', 'integer'],
            'matiere_id' => ['required', 'integer'],
            'note' => ['required', 'numeric', 'min:0', 'max:20'],
            'periode' => ['nullable', 'string', 'max:100'],
            'appreciation' => ['nullable', 'string', 'max:500'],
        ]);

        // Vérifier que la matière et la classe sont bien assignées à l'enseignant
        $assignedSubjectIds = $enseignant->matieres->pluck('id')->toArray();
        $assignedClassIds = $enseignant->classes->pluck('id')->toArray();

        if (!in_array((int) $validated['matiere_id'], $assignedSubjectIds)) {
            throw ValidationException::withMessages(['matiere_id' => 'Cette matière ne vous est pas assignée.']);
        }
        if (!in_array((int) $validated['classe_id'], $assignedClassIds)) {
            throw ValidationException::withMessages(['classe_id' => 'Cette classe ne vous est pas assignée.']);
        }

        // Vérifier que l'élève appartient bien à la classe
        $eleve = Eleve::where('tenant_id', $tenantId)
            ->where('id', $validated['eleve_id'])
            ->where('classe_id', $validated['classe_id'])
            ->first();

        if (!$eleve) {
            throw ValidationException::withMessages(['eleve_id' => 'Cet élève n\'existe pas dans cette classe.']);
        }

        $note = DB::table('notes')->insertGetId([
            'tenant_id' => $tenantId,
            'eleve_id' => $validated['eleve_id'],
            'classe_id' => $validated['classe_id'],
            'matiere_id' => $validated['matiere_id'],
            'note' => $validated['note'],
            'periode' => $validated['periode'] ?? null,
            'appreciation' => $validated['appreciation'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Note enregistrée avec succès.', 'id' => $note]);
        }

        return redirect()->route('enseignant.notes.index')
            ->with('success', 'Note enregistrée avec succès.');
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

        $validated = $request->validate([
            'note' => ['required', 'numeric', 'min:0', 'max:20'],
            'appreciation' => ['nullable', 'string', 'max:500'],
        ]);

        $note = DB::table('notes')
            ->where('tenant_id', $tenantId)
            ->where('id', $id)
            ->first();

        if (!$note) {
            return response()->json(['message' => 'Note non trouvée.'], 404);
        }

        // Vérifier que la matière est assignée à l'enseignant
        $assignedSubjectIds = $enseignant->matieres->pluck('id')->toArray();
        if (!in_array((int) $note->matiere_id, $assignedSubjectIds)) {
            return response()->json(['message' => 'Vous ne pouvez pas modifier cette note.'], 403);
        }

        DB::table('notes')
            ->where('tenant_id', $tenantId)
            ->where('id', $id)
            ->update([
                'note' => $validated['note'],
                'appreciation' => $validated['appreciation'] ?? null,
                'updated_at' => now(),
            ]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Note mise à jour avec succès.']);
        }

        return redirect()->route('enseignant.notes.index')
            ->with('success', 'Note mise à jour avec succès.');
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

        $note = DB::table('notes')
            ->where('tenant_id', $tenantId)
            ->where('id', $id)
            ->first();

        if (!$note) {
            return response()->json(['message' => 'Note non trouvée.'], 404);
        }

        // Vérifier que la matière est assignée à l'enseignant
        $assignedSubjectIds = $enseignant->matieres->pluck('id')->toArray();
        if (!in_array((int) $note->matiere_id, $assignedSubjectIds)) {
            return response()->json(['message' => 'Vous ne pouvez pas supprimer cette note.'], 403);
        }

        DB::table('notes')
            ->where('tenant_id', $tenantId)
            ->where('id', $id)
            ->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Note supprimée avec succès.']);
        }

        return redirect()->route('enseignant.notes.index')
            ->with('success', 'Note supprimée avec succès.');
    }
}

