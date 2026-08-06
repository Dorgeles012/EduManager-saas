<?php

namespace App\Http\Controllers\Parent;

use App\Models\AnneeAcademique;
use App\Models\Eleve;
use App\Models\EmploiTemps;
use App\Models\EmploiTempsSlot;
use App\Models\Etablissement;
use App\Services\EmploiTempsService;
use Illuminate\View\View;

class ParentEnfantEmploiTempsController extends ParentController
{
    /**
     * Emploi du temps d'un enfant (lecture seule).
     */
    public function index(Eleve $eleve): View
    {
        $this->childBelongsToParent($eleve);

        $user = auth()->user();
        $service = new EmploiTempsService();

        $anneeActive = AnneeAcademique::where('tenant_id', $user->tenant_id)
            ->where('statut', 'active')
            ->orderByDesc('date_debut')
            ->first();

        $entries = EmploiTemps::with(['matiere', 'enseignant', 'classe', 'serie'])
            ->where('tenant_id', $user->tenant_id)
            ->where('classe_id', $eleve->classe_id)
            ->when($eleve->id_serie, fn ($q) => $q->where('serie_id', $eleve->id_serie))
            ->when($anneeActive, fn ($q) => $q->where('annee_academique_id', $anneeActive->id))
            ->get();

        $savedSlots = EmploiTempsSlot::where('tenant_id', $user->tenant_id)
            ->whereIn('enseignant_id', $entries->pluck('enseignant_id')->unique())
            ->get()
            ->groupBy('slot_key')
            ->map->first();

        $slots = $service->slotsFor($entries, $savedSlots);
        $grid = $service->buildGrid($entries);

        return view('parent.emploi-temps', [
            'eleve' => $eleve,
            'entries' => $entries,
            'grid' => $grid,
            'days' => $service->days(),
            'slots' => $slots,
            'school' => Etablissement::find($eleve->etablissement_id),
            'year' => $anneeActive,
            'totalSeances' => $entries->count(),
        ]);
    }

    /**
     * Impression (lecture seule).
     */
    public function print(Eleve $eleve): View
    {
        $this->childBelongsToParent($eleve);

        $user = auth()->user();
        $service = new EmploiTempsService();

        $anneeActive = AnneeAcademique::where('tenant_id', $user->tenant_id)
            ->where('statut', 'active')
            ->orderByDesc('date_debut')
            ->first();

        $entries = EmploiTemps::with(['matiere', 'enseignant', 'classe', 'serie'])
            ->where('tenant_id', $user->tenant_id)
            ->where('classe_id', $eleve->classe_id)
            ->when($eleve->id_serie, fn ($q) => $q->where('serie_id', $eleve->id_serie))
            ->when($anneeActive, fn ($q) => $q->where('annee_academique_id', $anneeActive->id))
            ->get();

        $savedSlots = EmploiTempsSlot::where('tenant_id', $user->tenant_id)
            ->whereIn('enseignant_id', $entries->pluck('enseignant_id')->unique())
            ->get()
            ->groupBy('slot_key')
            ->map->first();

        return view('parent.emploi-temps-print', [
            'eleve' => $eleve,
            'entries' => $entries,
            'grid' => $service->buildGrid($entries),
            'days' => $service->days(),
            'slots' => $service->slotsFor($entries, $savedSlots),
            'school' => Etablissement::find($eleve->etablissement_id),
            'year' => $anneeActive,
            'totalSeances' => $entries->count(),
            'printMode' => true,
        ]);
    }

    /**
     * Téléchargement PDF (lecture seule).
     */
    public function pdf(Eleve $eleve)
    {
        $this->childBelongsToParent($eleve);

        $user = auth()->user();
        $service = new EmploiTempsService();

        $anneeActive = AnneeAcademique::where('tenant_id', $user->tenant_id)
            ->where('statut', 'active')
            ->orderByDesc('date_debut')
            ->first();

        $entries = EmploiTemps::with(['matiere', 'enseignant', 'classe', 'serie'])
            ->where('tenant_id', $user->tenant_id)
            ->where('classe_id', $eleve->classe_id)
            ->when($eleve->id_serie, fn ($q) => $q->where('serie_id', $eleve->id_serie))
            ->when($anneeActive, fn ($q) => $q->where('annee_academique_id', $anneeActive->id))
            ->get();

        $savedSlots = EmploiTempsSlot::where('tenant_id', $user->tenant_id)
            ->whereIn('enseignant_id', $entries->pluck('enseignant_id')->unique())
            ->get()
            ->groupBy('slot_key')
            ->map->first();

        return \Barryvdh\DomPDF\Facade\Pdf::loadView('parent.emploi-temps-print', [
            'eleve' => $eleve,
            'entries' => $entries,
            'grid' => $service->buildGrid($entries),
            'days' => $service->days(),
            'slots' => $service->slotsFor($entries, $savedSlots),
            'school' => Etablissement::find($eleve->etablissement_id),
            'year' => $anneeActive,
            'totalSeances' => $entries->count(),
            'printMode' => true,
        ])->setPaper('a4', 'landscape')
            ->download('emploi-du-temps-'.$eleve->id.'.pdf');
    }
}
