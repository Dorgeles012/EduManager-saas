<?php

namespace App\Http\Controllers\Parent;

use App\Models\Eleve;
use App\Models\AnneeAcademique;
use Illuminate\View\View;

class ParentEnfantScolariteController extends ParentController
{
    /**
     * Scolarité d'un enfant (consultation seule).
     */
    public function show(Eleve $eleve): View
    {
        $this->childBelongsToParent($eleve);

        $eleve->load(['classe.niveau', 'niveau', 'serie', 'etablissement', 'scolarites.versements']);

        $anneeActive = AnneeAcademique::where('tenant_id', auth()->user()->tenant_id)
            ->where('statut', 'active')
            ->orderByDesc('date_debut')
            ->first();

        return view('parent.scolarite', [
            'eleve' => $eleve,
            'anneeActive' => $anneeActive,
        ]);
    }
}
