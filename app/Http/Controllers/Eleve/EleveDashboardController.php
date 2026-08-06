<?php

namespace App\Http\Controllers\Eleve;

use App\Models\AnneeAcademique;
use App\Models\Bulletin;
use App\Models\EmploiTemps;
use App\Models\Scolarite;
use Illuminate\View\View;

class EleveDashboardController extends EleveController
{
    public function index(): View
    {
        $eleve = $this->currentEleve();
        $user = auth()->user();

        $anneeActive = AnneeAcademique::where('tenant_id', $user->tenant_id)
            ->where('statut', 'active')
            ->orderByDesc('date_debut')
            ->first();

        return view('eleve.dashboard', [
            'eleve' => $eleve,
            'counts' => [
                'notes' => $eleve->notes()->count(),
                'bulletins' => Bulletin::where('tenant_id', $user->tenant_id)->where('eleve_id', $eleve->id)->count(),
                'scolarites' => Scolarite::where('tenant_id', $user->tenant_id)->where('eleve_id', $eleve->id)->count(),
                'cours' => EmploiTemps::where('tenant_id', $user->tenant_id)
                    ->where('classe_id', $eleve->classe_id)
                    ->when($eleve->id_serie, fn ($q) => $q->where('serie_id', $eleve->id_serie))
                    ->when($anneeActive, fn ($q) => $q->where('annee_academique_id', $anneeActive->id))
                    ->count(),
            ],
            'anneeActive' => $anneeActive,
        ]);
    }
}
