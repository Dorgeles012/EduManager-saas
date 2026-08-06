<?php

namespace App\Http\Controllers\Parent;

use App\Models\Eleve;
use App\Models\AnneeAcademique;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParentEnfantNotesController extends ParentController
{
    /**
     * Notes d'un enfant (lecture seule), avec filtres année / période / matière.
     */
    public function index(Request $request, Eleve $eleve): View
    {
        $this->childBelongsToParent($eleve);

$periode = $request->input('periode');
        $matiereId = $request->integer('matiere_id');

        $query = $eleve->notes()->with(['classe', 'matiere']);

        $notes = $query
            ->when($periode, fn ($q) => $q->where('periode', $periode))
            ->when($matiereId, fn ($q) => $q->where('matiere_id', $matiereId))
            ->latest()
            ->get();

        // Appréciations recalculées (source unique de vérité)
        $notes->each(function ($note) {
            $note->appreciation = \App\Services\BulletinService::noteAppreciation((float) $note->note);
        });

        $years = AnneeAcademique::where('tenant_id', auth()->user()->tenant_id)
            ->orderByDesc('date_debut')
            ->get();

        $periodes = $eleve->notes()->distinct('periode')->whereNotNull('periode')->pluck('periode')->filter();

        $matieres = $eleve->notes()->with('matiere')->get()->pluck('matiere')->unique('id')->values();

        return view('parent.notes', [
            'eleve' => $eleve,
            'notes' => $notes,
            'years' => $years,
            'periodes' => $periodes,
            'matieres' => $matieres,
            'selectedPeriode' => $periode,
            'selectedMatiere' => $matiereId,
        ]);
    }
}
