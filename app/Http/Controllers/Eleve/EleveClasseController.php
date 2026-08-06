<?php

namespace App\Http\Controllers\Eleve;

use App\Models\AnneeAcademique;
use App\Models\Eleve;
use Illuminate\View\View;

class EleveClasseController extends EleveController
{
    public function index(): View
    {
        $eleve = $this->currentEleve();
        $user = auth()->user();

        $anneeActive = AnneeAcademique::where('tenant_id', $user->tenant_id)
            ->where('statut', 'active')
            ->orderByDesc('date_debut')
            ->first();

        $classmates = collect();
        if ($eleve->classe_id) {
            $classmates = Eleve::with(['classe.niveau', 'serie'])
                ->where('tenant_id', $user->tenant_id)
                ->where('classe_id', $eleve->classe_id)
                ->when($anneeActive, fn ($q) => $q) // l'année n'est pas sur eleves ; on garde l'affiliation classe
                ->orderBy('nom')
                ->orderBy('prenom')
                ->get();
        }

        return view('eleve.classe', [
            'eleve' => $eleve,
            'classmates' => $classmates,
            'anneeActive' => $anneeActive,
        ]);
    }
}
