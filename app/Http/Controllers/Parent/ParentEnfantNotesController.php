<?php

namespace App\Http\Controllers\Parent;

use App\Models\AnneeAcademique;
use App\Models\Eleve;
use App\Models\Note;
use App\Services\BulletinService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParentEnfantNotesController extends ParentController
{
    /**
     * Notes d'un enfant (lecture seule, uniquement les notes publiées),
     * avec calcul des moyennes par matière et de la moyenne générale.
     */
    public function index(Request $request, Eleve $eleve, BulletinService $bulletinService): View
    {
        $this->childBelongsToParent($eleve);

        $periode = $request->input('periode');
        $matiereId = $request->integer('matiere_id');
        $anneeId = $request->integer('annee_academique_id');

        $query = $eleve->notes()
            ->with(['classe', 'matiere', 'enseignant'])
            ->where('statut', Note::STATUT_PUBLIE);

        $notes = $query
            ->when($periode, fn ($q) => $q->where('periode', $periode))
            ->when($matiereId, fn ($q) => $q->where('matiere_id', $matiereId))
            ->when($anneeId, fn ($q) => $q->where('annee_academique_id', $anneeId))
            ->latest('id')
            ->get();

        // Appréciations recalculées
        $notes->each(function (Note $note) {
            $note->appreciation = BulletinService::noteAppreciation((float) $note->note);
            $note->enseignant_label = $note->enseignant
                ? trim(($note->enseignant->prenoms ?? '').' '.$note->enseignant->nom)
                : ($note->matiere?->enseignants()->first() ? trim(($note->matiere->enseignants()->first()->prenoms ?? '').' '.$note->matiere->enseignants()->first()->nom) : null);
        });

        $years = AnneeAcademique::where('tenant_id', auth()->user()->tenant_id)
            ->orderByDesc('date_debut')
            ->get();

        $periodes = $eleve->notes()
            ->where('statut', Note::STATUT_PUBLIE)
            ->distinct('periode')
            ->whereNotNull('periode')
            ->pluck('periode')
            ->filter();

        $matieres = $eleve->notes()
            ->where('statut', Note::STATUT_PUBLIE)
            ->with('matiere')
            ->get()
            ->pluck('matiere')
            ->unique('id')
            ->values();

        $bilanPeriode = null;
        if ($periode) {
            $bilanPeriode = $bulletinService->calculerBilanEleve($eleve, $periode, $anneeId, Note::STATUT_PUBLIE);
        }

        return view('parent.notes', [
            'eleve' => $eleve,
            'notes' => $notes,
            'years' => $years,
            'periodes' => $periodes->isNotEmpty() ? $periodes : collect(['t1', 't2', 't3']),
            'matieres' => $matieres,
            'selectedPeriode' => $periode,
            'selectedMatiere' => $matiereId,
            'selectedAnnee' => $anneeId,
            'bilanPeriode' => $bilanPeriode,
        ]);
    }
}
