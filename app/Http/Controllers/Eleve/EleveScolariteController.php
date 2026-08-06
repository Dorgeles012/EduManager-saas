<?php

namespace App\Http\Controllers\Eleve;

use App\Models\AnneeAcademique;
use Illuminate\View\View;

class EleveScolariteController extends EleveController
{
    public function index(): View
    {
        $eleve = $this->currentEleve();
        $user = auth()->user();

        $anneeActive = AnneeAcademique::where('tenant_id', $user->tenant_id)
            ->where('statut', 'active')
            ->orderByDesc('date_debut')
            ->first();

        $scolarites = $eleve->scolarites()->latest()->get();

        return view('eleve.scolarite', [
            'eleve' => $eleve,
            'scolarites' => $scolarites,
            'anneeActive' => $anneeActive,
        ]);
    }
}
