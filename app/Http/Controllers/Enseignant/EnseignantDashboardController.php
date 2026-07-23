<?php

namespace App\Http\Controllers\Enseignant;

use App\Http\Controllers\Controller;
use App\Models\{AnneeAcademique, Classe, Eleve, EmploiTemps, Enseignant, Matiere, Etablissement, Series};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EnseignantDashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $tenantId = $user?->tenant_id;

        // Récupérer l'enseignant associé à cet utilisateur
        $enseignant = Enseignant::where('tenant_id', $tenantId)
            ->where('user_id', $user->id)
            ->with(['matieres', 'classes', 'series'])
            ->first();

        if (empty($tenantId) || !$enseignant) {
            return view('enseignant.dashboard.index', [
                'counts' => [],
                'activeYear' => null,
                'enseignant' => null,
                'assignedClasses' => collect(),
                'assignedSubjects' => collect(),
                'assignedSeries' => collect(),
            ]);
        }

        $schoolId = $user->etablissement_id ?? $enseignant->etablissement_id;
        $assignedClassIds = $enseignant->classes->pluck('id');
        $assignedSubjectIds = $enseignant->matieres->pluck('id');
        $assignedSerieIds = $enseignant->series->pluck('id');

        $totalStudents = Eleve::where('tenant_id', $tenantId)
            ->whereIn('classe_id', $assignedClassIds)
            ->count();

        $totalNotes = DB::table('notes')
            ->where('tenant_id', $tenantId)
            ->whereIn('matiere_id', $assignedSubjectIds)
            ->whereIn('classe_id', $assignedClassIds)
            ->count();

        $avgGrade = DB::table('notes')
            ->where('tenant_id', $tenantId)
            ->whereIn('matiere_id', $assignedSubjectIds)
            ->whereIn('classe_id', $assignedClassIds)
            ->avg('note');

        $activeYear = AnneeAcademique::where('tenant_id', $tenantId)
            ->when($schoolId, fn ($q) => $q->where('etablissement_id', $schoolId))
            ->where('statut', 'active')
            ->orderByDesc('date_debut')
            ->first();

        $totalSeances = EmploiTemps::where('tenant_id', $tenantId)
            ->where('enseignant_id', $enseignant->id)
            ->count();

        return view('enseignant.dashboard.index', [
            'counts' => [
                'classes' => $assignedClassIds->count(),
                'matieres' => $assignedSubjectIds->count(),
                'series' => $assignedSerieIds->count(),
                'eleves' => $totalStudents,
                'notes' => $totalNotes,
                'notes_saisies' => $totalNotes,
                'seances' => $totalSeances,
                'moyenne_generale' => $avgGrade ? round($avgGrade, 2) : 0,
                'enseignant_nom' => $enseignant->nom . ' ' . ($enseignant->prenoms ?? ''),
                'enseignant_matricule' => $enseignant->matricule ?? '—',
            ],
            'activeYear' => $activeYear,
            'enseignant' => $enseignant,
            'assignedClasses' => $enseignant->classes,
            'assignedSubjects' => $enseignant->matieres,
            'assignedSeries' => $enseignant->series,
        ]);
    }
}

