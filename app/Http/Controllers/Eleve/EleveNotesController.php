<?php

namespace App\Http\Controllers\Eleve;

use App\Models\AnneeAcademique;
use App\Models\Note;
use App\Services\BulletinService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EleveNotesController extends EleveController
{
    public const PERIODES = [
        't1' => '1er Trimestre',
        't2' => '2ème Trimestre',
        't3' => '3ème Trimestre',
    ];

    public function index(Request $request, BulletinService $bulletinService): View
    {
        $eleve = $this->currentEleve();
        $user = auth()->user();

        $anneeId = $request->integer('annee_academique_id');
        $periode = $request->input('periode');
        $matiereId = $request->integer('matiere_id');

        // Uniquement les notes publiées !
        $notesQuery = $eleve->notes()
            ->with(['matiere', 'classe', 'anneeAcademique', 'enseignant'])
            ->where('statut', Note::STATUT_PUBLIE);

        if ($anneeId) {
            $notesQuery->where('annee_academique_id', $anneeId);
        }

        if ($periode) {
            $notesQuery->where('periode', $periode);
        }

        if ($matiereId) {
            $notesQuery->where('matiere_id', $matiereId);
        }

        $notes = $notesQuery->latest('id')->get();

        // Calcul des appréciations
        $notes->each(function (Note $note) {
            $note->appreciation = BulletinService::noteAppreciation((float) $note->note);
            $note->enseignant_label = $note->enseignant
                ? trim(($note->enseignant->prenoms ?? '').' '.$note->enseignant->nom)
                : ($note->matiere?->enseignants()->first() ? trim(($note->matiere->enseignants()->first()->prenoms ?? '').' '.$note->matiere->enseignants()->first()->nom) : null);
        });

        $years = AnneeAcademique::where('tenant_id', $user->tenant_id)
            ->orderByDesc('date_debut')
            ->get(['id', 'libelle', 'date_debut', 'date_fin']);

        // Périodes disponibles dans les notes publiées de l'élève
        $availablePeriodes = $eleve->notes()
            ->where('statut', Note::STATUT_PUBLIE)
            ->whereNotNull('periode')
            ->distinct('periode')
            ->pluck('periode')
            ->filter()
            ->values();

        $matieres = $eleve->notes()
            ->where('statut', Note::STATUT_PUBLIE)
            ->with('matiere')
            ->get()
            ->pluck('matiere')
            ->unique('id')
            ->values();

        // Calcul du bilan de l'élève pour la période sélectionnée
        $bilanPeriode = null;
        if ($periode) {
            $bilanPeriode = $bulletinService->calculerBilanEleve($eleve, $periode, $anneeId, Note::STATUT_PUBLIE);
        }

        return view('eleve.notes', [
            'eleve' => $eleve,
            'notes' => $notes,
            'years' => $years,
            'periodes' => $availablePeriodes->isNotEmpty() ? $availablePeriodes : collect(['t1', 't2', 't3']),
            'matieres' => $matieres,
            'selectedAnnee' => $anneeId,
            'selectedPeriode' => $periode,
            'selectedMatiere' => $matiereId,
            'bilanPeriode' => $bilanPeriode,
        ]);
    }
}
