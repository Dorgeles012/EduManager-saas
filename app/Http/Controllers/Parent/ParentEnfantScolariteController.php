<?php

namespace App\Http\Controllers\Parent;

use App\Models\Eleve;
use App\Services\ScolariteService;
use Illuminate\View\View;

class ParentEnfantScolariteController extends ParentController
{
    /**
     * Scolarité d'un enfant (consultation seule).
     */
    public function show(Eleve $eleve, ScolariteService $scolariteService): View
    {
        $this->childBelongsToParent($eleve);

        $eleve->load(['classe.niveau', 'niveau', 'serie', 'etablissement']);

        $situation = $scolariteService->situation($eleve);

        return view('parent.scolarite', [
            'eleve' => $eleve,
            'situation' => $situation,
        ]);
    }
}

