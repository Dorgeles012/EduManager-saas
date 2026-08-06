<?php

namespace App\Http\Controllers\Parent;

use App\Models\Bulletin;
use App\Models\Eleve;
use Illuminate\View\View;

class ParentEnfantBulletinsController extends ParentController
{
    /**
     * Liste des bulletins d'un enfant (lecture seule).
     */
    public function index(Eleve $eleve): View
    {
        $this->childBelongsToParent($eleve);

        $bulletins = Bulletin::with(['classe', 'anneeAcademique', 'etablissement', 'eleve'])
            ->where('tenant_id', auth()->user()->tenant_id)
            ->where('eleve_id', $eleve->id)
            ->latest()
            ->get();

        return view('parent.bulletins', [
            'eleve' => $eleve,
            'bulletins' => $bulletins,
        ]);
    }

    /**
     * Affiche un bulletin (vérifie que l'enfant appartient au parent).
     */
    public function show(Eleve $eleve, Bulletin $bulletin): View
    {
        $this->childBelongsToParent($eleve);
        $this->ensureChildBulletin($eleve, $bulletin);

        $bulletin->load(['eleve', 'classe', 'anneeAcademique', 'etablissement', 'disciplines']);

        $annualBulletins = Bulletin::query()
            ->where('tenant_id', $bulletin->tenant_id)
            ->where('eleve_id', $bulletin->eleve_id)
            ->where('annee_academique_id', $bulletin->annee_academique_id)
            ->whereIn('trimestre', ['t1', 't2', 't3'])
            ->with('disciplines')
            ->get()
            ->keyBy('trimestre');

        if ($annualBulletins->isEmpty()) {
            $annualBulletins = collect([$bulletin->trimestre => $bulletin]);
        }

        return view('parent.bulletin-show', [
            'bulletin' => $bulletin,
            'annualBulletins' => $annualBulletins,
            'eleve' => $eleve,
            'printMode' => false,
        ]);
    }

    /**
     * Impression d'un bulletin (lecture seule).
     */
    public function print(Eleve $eleve, Bulletin $bulletin): View
    {
        $this->childBelongsToParent($eleve);
        $this->ensureChildBulletin($eleve, $bulletin);

        $bulletin->load(['eleve', 'classe', 'anneeAcademique', 'etablissement', 'disciplines']);

        $annualBulletins = Bulletin::query()
            ->where('tenant_id', $bulletin->tenant_id)
            ->where('eleve_id', $bulletin->eleve_id)
            ->where('annee_academique_id', $bulletin->annee_academique_id)
            ->whereIn('trimestre', ['t1', 't2', 't3'])
            ->with('disciplines')
            ->get()
            ->keyBy('trimestre');

        if ($annualBulletins->isEmpty()) {
            $annualBulletins = collect([$bulletin->trimestre => $bulletin]);
        }

        return view('parent.bulletin-show', [
            'bulletin' => $bulletin,
            'annualBulletins' => $annualBulletins,
            'eleve' => $eleve,
            'printMode' => true,
        ]);
    }

    /**
     * Téléchargement PDF d'un bulletin (réutilise la vue de show).
     */
    public function downloadPdf(Eleve $eleve, Bulletin $bulletin)
    {
        $this->childBelongsToParent($eleve);
        $this->ensureChildBulletin($eleve, $bulletin);

        $bulletin->load(['eleve', 'classe', 'anneeAcademique', 'etablissement', 'disciplines']);

        $annualBulletins = Bulletin::query()
            ->where('tenant_id', $bulletin->tenant_id)
            ->where('eleve_id', $bulletin->eleve_id)
            ->where('annee_academique_id', $bulletin->annee_academique_id)
            ->whereIn('trimestre', ['t1', 't2', 't3'])
            ->with('disciplines')
            ->get()
            ->keyBy('trimestre');

        if ($annualBulletins->isEmpty()) {
            $annualBulletins = collect([$bulletin->trimestre => $bulletin]);
        }

        return \Barryvdh\DomPDF\Facade\Pdf::loadView('parent.bulletin-show', [
            'bulletin' => $bulletin,
            'annualBulletins' => $annualBulletins,
            'eleve' => $eleve,
            'printMode' => false,
            'pdfMode' => true,
        ])->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'Times-Roman')
            ->setOption('dpi', 96)
            ->setOption('isRemoteEnabled', false)
            ->download("bulletin-{$bulletin->id}.pdf");
    }

    /**
     * Vérifie que le bulletin appartient bien à l'enfant du parent.
     */
    private function ensureChildBulletin(Eleve $eleve, Bulletin $bulletin): void
    {
        abort_unless(
            (int) $bulletin->eleve_id === (int) $eleve->id
            && (int) $bulletin->tenant_id === (int) auth()->user()->tenant_id,
            403,
            'Accès interdit.'
        );
    }
}
