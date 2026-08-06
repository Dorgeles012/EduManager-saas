<?php

namespace App\Http\Controllers\Eleve;

use App\Models\AnneeAcademique;
use App\Models\EmploiTemps;
use App\Models\EmploiTempsSlot;
use App\Models\Etablissement;
use App\Services\EmploiTempsService;
use Illuminate\View\View;

class EleveEmploiTempsController extends EleveController
{
    protected function resolveSchedule()
    {
        $eleve = $this->currentEleve();
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

        return [
            'eleve' => $eleve,
            'entries' => $entries,
            'grid' => $service->buildGrid($entries),
            'days' => $service->days(),
            'slots' => $service->slotsFor($entries, $savedSlots),
            'school' => Etablissement::find($eleve->etablissement_id),
            'year' => $anneeActive,
            'totalSeances' => $entries->count(),
        ];
    }

    public function index(): View
    {
        return view('eleve.emploi-temps', $this->resolveSchedule());
    }

    public function print(): View
    {
        return view('eleve.emploi-temps-print', $this->resolveSchedule() + ['printMode' => true]);
    }

    public function pdf()
    {
        $data = $this->resolveSchedule();
        return \Barryvdh\DomPDF\Facade\Pdf::loadView('eleve.emploi-temps-print', $data + ['printMode' => true])
            ->setPaper('a4', 'landscape')
            ->download('mon-emploi-du-temps-'.$data['eleve']->id.'.pdf');
    }
}
