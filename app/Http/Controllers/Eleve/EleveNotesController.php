<?php

namespace App\Http\Controllers\Eleve;

use App\Models\AnneeAcademique;
use App\Services\BulletinService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EleveNotesController extends EleveController
{
    /**
     * Périodes standard de l'établissement.
     */
    public const PERIODES = [
        'Trimestre 1',
        'Trimestre 2',
        'Trimestre 3',
        'Semestre 1',
        'Semestre 2',
        'Annuel',
    ];

    public function index(Request $request): View
    {
        $eleve = $this->currentEleve();
        $user = auth()->user();

        $anneeId = $request->integer('annee_academique_id');
        $periode = $request->input('periode');
        $matiereId = $request->integer('matiere_id');

        $notesQuery = $eleve->notes()->with(['matiere', 'classe', 'anneeAcademique']);

        // Filtre par année académique
        if ($anneeId) {
            $notesQuery->where('annee_academique_id', $anneeId);
        }

        // Filtre par période
        if ($periode) {
            $notesQuery->where('periode', $periode);
        }

        // Filtre par matière
        if ($matiereId) {
            $notesQuery->where('matiere_id', $matiereId);
        }

        $notes = $notesQuery->latest()->get();

        // Appréciation recalculée (source unique de vérité)
        $notes->each(function ($note) {
            $note->appreciation = BulletinService::noteAppreciation((float) $note->note);
        });

        // Enseignant via la matière (première affiliation)
        $notes->each(function ($note) {
            $note->enseignant_label = $note->matiere?->enseignants()->first()
                ? trim(($note->matiere->enseignants()->first()->prenoms ?? '').' '.$note->matiere->enseignants()->first()->nom)
                : null;
        });

        $years = AnneeAcademique::where('tenant_id', $user->tenant_id)
            ->orderByDesc('date_debut')
            ->get(['id', 'libelle', 'date_debut', 'date_fin']);

        // Périodes disponibles dans les notes de l'élève (pour l'historique réel)
        $availablePeriodes = $eleve->notes()
            ->whereNotNull('periode')
            ->distinct('periode')
            ->pluck('periode')
            ->filter()
            ->values();

        // Périodes pour le filtre : standard + celles présentes en base
        $periodes = collect(self::PERIODES)
            ->merge($availablePeriodes)
            ->unique()
            ->values();

        // Matières disponibles pour cet élève
        $matieres = $eleve->notes()->with('matiere')->get()->pluck('matiere')->unique('id')->values();

        return view('eleve.notes', [
            'eleve' => $eleve,
            'notes' => $notes,
            'years' => $years,
            'periodes' => $periodes,
            'matieres' => $matieres,
            'selectedAnnee' => $anneeId,
            'selectedPeriode' => $periode,
'selectedMatiere' => $matiereId,
        ]);
    }
}
