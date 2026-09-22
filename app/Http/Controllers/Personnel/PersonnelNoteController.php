<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Enseignant;
use App\Models\Matiere;
use App\Models\Note;
use App\Services\BulletinService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PersonnelNoteController extends Controller
{
    /**
     * Tableau de bord de vérification et validation des notes soumises par les enseignants.
     */
    public function index(Request $request): View
    {
        $tenantId = auth()->user()->tenant_id;

        $selectedClass = $request->integer('classe_id');
        $selectedSubject = $request->integer('matiere_id');
        $selectedPeriode = $request->input('periode');
        $selectedStatus = $request->input('statut', Note::STATUT_SOUMIS);

        // Groupements par lots de soumission
        $batchesQuery = Note::query()
            ->select(
                'notes.classe_id',
                'notes.matiere_id',
                'notes.periode',
                'notes.enseignant_id',
                'notes.annee_academique_id',
                'notes.statut',
                DB::raw('COUNT(notes.id) as total_notes'),
                DB::raw('AVG(notes.note) as moyenne_classe'),
                DB::raw('MAX(notes.soumis_le) as derniere_soumission'),
                DB::raw('MAX(notes.rejet_motif) as dernier_motif')
            )
            ->join('classes', 'notes.classe_id', '=', 'classes.id')
            ->join('matieres', 'notes.matiere_id', '=', 'matieres.id')
            ->leftJoin('enseignants', 'notes.enseignant_id', '=', 'enseignants.id')
            ->where('notes.tenant_id', $tenantId)
            ->groupBy(
                'notes.classe_id',
                'notes.matiere_id',
                'notes.periode',
                'notes.enseignant_id',
                'notes.annee_academique_id',
                'notes.statut'
            );

        if ($selectedClass) {
            $batchesQuery->where('notes.classe_id', $selectedClass);
        }
        if ($selectedSubject) {
            $batchesQuery->where('notes.matiere_id', $selectedSubject);
        }
        if ($selectedPeriode) {
            $batchesQuery->where('notes.periode', $selectedPeriode);
        }
        if ($selectedStatus) {
            $batchesQuery->where('notes.statut', $selectedStatus);
        }

        $batches = $batchesQuery->orderByDesc('derniere_soumission')->paginate(15)->withQueryString();

        // Charger les relations associées
        $classes = Classe::where('tenant_id', $tenantId)->orderBy('nom')->get();
        $subjects = Matiere::where('tenant_id', $tenantId)->orderBy('nom')->get();
        $enseignants = Enseignant::where('tenant_id', $tenantId)->orderBy('nom')->get();

        // Statistiques
        $totalNotes = Note::where('tenant_id', $tenantId)->count();
        $pendingPersonnelCount = Note::where('tenant_id', $tenantId)->where('statut', Note::STATUT_SOUMIS)->count();
        $approvedPersonnelCount = Note::where('tenant_id', $tenantId)->where('statut', Note::STATUT_APPROUVE_PERSONNEL)->count();
        $rejectedPersonnelCount = Note::where('tenant_id', $tenantId)->where('statut', Note::STATUT_REJETE_PERSONNEL)->count();
        $publishedCount = Note::where('tenant_id', $tenantId)->where('statut', Note::STATUT_PUBLIE)->count();

        return view('personnel.notes.index', [
            'batches' => $batches,
            'classes' => $classes,
            'subjects' => $subjects,
            'enseignants' => $enseignants,
            'selectedClass' => $selectedClass,
            'selectedSubject' => $selectedSubject,
            'selectedPeriode' => $selectedPeriode,
            'selectedStatus' => $selectedStatus,
            'totalNotes' => $totalNotes,
            'pendingPersonnelCount' => $pendingPersonnelCount,
            'approvedPersonnelCount' => $approvedPersonnelCount,
            'rejectedPersonnelCount' => $rejectedPersonnelCount,
            'publishedCount' => $publishedCount,
        ]);
    }

    /**
     * Revue détaillée d'un lot de notes pour vérification.
     */
    public function review(Request $request, BulletinService $bulletinService): View
    {
        $tenantId = auth()->user()->tenant_id;

        $classeId = $request->integer('classe_id');
        $matiereId = $request->integer('matiere_id');
        $periode = $request->input('periode');
        $anneeId = $request->integer('annee_academique_id');

        $classe = Classe::where('tenant_id', $tenantId)->findOrFail($classeId);
        $matiere = Matiere::where('tenant_id', $tenantId)->findOrFail($matiereId);
        $annee = AnneeAcademique::where('tenant_id', $tenantId)->find($anneeId);

        $notes = Note::with(['eleve', 'enseignant'])
            ->where('tenant_id', $tenantId)
            ->where('classe_id', $classeId)
            ->where('matiere_id', $matiereId)
            ->where('periode', $periode)
            ->orderBy('created_at')
            ->get();

        // Grouper par élève et calculer la moyenne
        $eleves = Eleve::where('tenant_id', $tenantId)->where('classe_id', $classeId)->orderBy('nom')->get();
        $notesGrouped = $notes->groupBy('eleve_id');

        $elevesSummaries = [];
        $totalClassePoints = 0.0;
        $totalClasseNotesCount = 0;

        foreach ($eleves as $eleve) {
            $studentNotes = $notesGrouped->get($eleve->id, collect());
            $avg = $studentNotes->isNotEmpty() ? round((float) $studentNotes->avg('note'), 2) : null;

            if ($avg !== null) {
                $totalClassePoints += $avg;
                $totalClasseNotesCount++;
            }

            $elevesSummaries[] = [
                'eleve' => $eleve,
                'notes' => $studentNotes,
                'moyenne' => $avg,
                'appreciation' => BulletinService::noteAppreciation($avg),
            ];
        }

        $moyenneGeneraleClasse = $totalClasseNotesCount > 0 ? round($totalClassePoints / $totalClasseNotesCount, 2) : null;

        return view('personnel.notes.review', [
            'classe' => $classe,
            'matiere' => $matiere,
            'periode' => $periode,
            'annee' => $annee,
            'notes' => $notes,
            'elevesSummaries' => $elevesSummaries,
            'moyenneGeneraleClasse' => $moyenneGeneraleClasse,
        ]);
    }

    /**
     * Approuve un lot de notes vérifiées (passe à l'étape client).
     */
    public function approuver(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $validated = $request->validate([
            'classe_id' => ['required', 'integer'],
            'matiere_id' => ['required', 'integer'],
            'periode' => ['required', 'string'],
            'note_ids' => ['nullable', 'array'],
        ]);

        $query = Note::where('tenant_id', $tenantId)
            ->where('classe_id', $validated['classe_id'])
            ->where('matiere_id', $validated['matiere_id'])
            ->where('periode', $validated['periode'])
            ->where('statut', Note::STATUT_SOUMIS);

        if (!empty($validated['note_ids'])) {
            $query->whereIn('id', $validated['note_ids']);
        }

        $count = $query->update([
            'statut' => Note::STATUT_APPROUVE_PERSONNEL,
            'approuve_personnel_le' => Carbon::now(),
            'approuve_personnel_id' => auth()->id(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()->route('personnel.notes.index')->with('success', "{$count} note(s) approuvée(s) par le Personnel avec succès. Le lot est transmis à la Direction / Client pour validation finale.");
    }

    /**
     * Rejette un lot de notes avec un motif obligatoire.
     */
    public function rejeter(Request $request, NotificationService $notifications)
    {
        $tenantId = auth()->user()->tenant_id;

        $validated = $request->validate([
            'classe_id' => ['required', 'integer'],
            'matiere_id' => ['required', 'integer'],
            'periode' => ['required', 'string'],
            'rejet_motif' => ['required', 'string', 'min:3', 'max:1000'],
            'note_ids' => ['nullable', 'array'],
        ]);

        $query = Note::where('tenant_id', $tenantId)
            ->where('classe_id', $validated['classe_id'])
            ->where('matiere_id', $validated['matiere_id'])
            ->where('periode', $validated['periode'])
            ->whereIn('statut', [Note::STATUT_SOUMIS, Note::STATUT_APPROUVE_PERSONNEL]);

        if (!empty($validated['note_ids'])) {
            $query->whereIn('id', $validated['note_ids']);
        }

        $notesToReject = $query->get();
        $count = $query->update([
            'statut' => Note::STATUT_REJETE_PERSONNEL,
            'rejet_motif' => $validated['rejet_motif'],
            'rejet_par' => 'personnel',
            'updated_at' => Carbon::now(),
        ]);

        // Notifier l'enseignant si rattaché
        $enseignantIds = $notesToReject->pluck('enseignant_id')->unique()->filter();
        $enseignants = Enseignant::with('user')->whereIn('id', $enseignantIds)->get();
        $usersToNotify = $enseignants->pluck('user')->filter();

        if ($usersToNotify->isNotEmpty()) {
            $notifications->sendToUsers(
                auth()->user(),
                $usersToNotify,
                'Notes rejetées pour correction',
                "Vos notes pour la période {$validated['periode']} ont été rejetées par le personnel. Motif : {$validated['rejet_motif']}",
                'notes'
            );
        }

        return redirect()->route('personnel.notes.index')->with('warning', "{$count} note(s) rejetée(s). L'enseignant a été notifié pour correction.");
    }

    /**
     * Valide (approuve) en une seule action toutes les notes actuellement en attente de validation (statut = soumis).
     */
    public function validerTout(Request $request, BulletinService $bulletinService)
    {
        $user = Auth::user();
        $tenantId = (int) $user->tenant_id;
        $etablissementId = $user->etablissement_id ? (int) $user->etablissement_id : null;

        DB::transaction(function () use ($tenantId, $etablissementId, $user, $bulletinService) {
            $query = Note::query()
                ->where('tenant_id', $tenantId)
                ->where('statut', Note::STATUT_SOUMIS);

            if ($etablissementId) {
                $query->where(function ($q) use ($etablissementId) {
                    $q->where('etablissement_id', $etablissementId)
                      ->orWhereHas('classe', fn ($cq) => $cq->where('etablissement_id', $etablissementId));
                });
            }

            $notes = $query->lockForUpdate()->get();

            if ($notes->isEmpty()) {
                return;
            }

            $now = Carbon::now();
            $noteIds = $notes->pluck('id')->all();

            // Mettre à jour automatiquement le statut des notes validées
            Note::whereIn('id', $noteIds)->update([
                'statut' => Note::STATUT_APPROUVE_PERSONNEL,
                'approuve_personnel_le' => $now,
                'approuve_personnel_id' => $user->id,
                'updated_at' => $now,
            ]);

            // Après validation, recalculer les moyennes concernées
            $groups = $notes->map(function (Note $n) use ($tenantId) {
                $anneeId = $n->annee_academique_id
                    ?? AnneeAcademique::where('tenant_id', $tenantId)->latest('id')->value('id');
                return [
                    'classe_id' => $n->classe_id,
                    'periode' => $n->periode,
                    'annee_academique_id' => $anneeId,
                ];
            })->unique(fn ($item) => $item['classe_id'].'-'.$item['periode'].'-'.$item['annee_academique_id']);

            foreach ($groups as $group) {
                if (!empty($group['classe_id']) && !empty($group['periode'])) {
                    $bulletins = Bulletin::where('tenant_id', $tenantId)
                        ->where('classe_id', $group['classe_id'])
                        ->where('trimestre', $group['periode'])
                        ->get();

                    if ($bulletins->isNotEmpty()) {
                        $rangs = $bulletinService->calculerRangsClasse(
                            $group['classe_id'],
                            $group['periode'],
                            $group['annee_academique_id'],
                            null
                        );

                        foreach ($bulletins as $bulletin) {
                            $bilan = $bulletinService->calculerBilanEleve(
                                $bulletin->eleve_id,
                                $group['periode'],
                                $group['annee_academique_id'],
                                null
                            );

                            if ($bilan['moyenne_generale'] !== null) {
                                $bulletin->update([
                                    'moyenne_generale' => $bilan['moyenne_generale'],
                                    'total_coefficients' => $bilan['total_coefficients'],
                                    'total_points' => $bilan['total_points'],
                                    'rang' => $rangs[$bulletin->eleve_id] ?? $bulletin->rang,
                                    'mention' => $bilan['mention'],
                                    'decision' => $bilan['decision'],
                                    'observation_conseil' => $bilan['observation_conseil'],
                                ]);
                            }
                        }
                    }
                }
            }
        });

        return redirect()->route('personnel.notes.index')
            ->with('success', 'Toutes les notes en attente ont été validées avec succès.');
    }
}
