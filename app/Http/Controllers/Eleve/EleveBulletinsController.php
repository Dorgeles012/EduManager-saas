<?php

namespace App\Http\Controllers\Eleve;

use App\Models\AnneeAcademique;
use App\Models\Bulletin;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EleveBulletinsController extends EleveController
{
    public function index(Request $request): View
    {
        $eleve = $this->currentEleve();
        $user = auth()->user();

        $yearId = $request->integer('annee_academique_id');

        // Uniquement les bulletins publiés
        $bulletins = Bulletin::with(['classe', 'anneeAcademique', 'etablissement', 'disciplines'])
            ->where('tenant_id', $user->tenant_id)
            ->where('eleve_id', $eleve->id)
            ->where('statut', Bulletin::STATUT_PUBLIE)
            ->when($yearId, fn ($q) => $q->where('annee_academique_id', $yearId))
            ->latest('id')
            ->get();

        $years = AnneeAcademique::where('tenant_id', $user->tenant_id)
            ->orderByDesc('date_debut')
            ->get();

        return view('eleve.bulletins', [
            'eleve' => $eleve,
            'bulletins' => $bulletins,
            'years' => $years,
            'selectedYear' => $yearId,
        ]);
    }

    public function show(Bulletin $bulletin): View
    {
        $eleve = $this->currentEleve();
        $user = auth()->user();

        abort_unless(
            (int) $bulletin->tenant_id === (int) $user->tenant_id
            && (int) $bulletin->eleve_id === (int) $eleve->id
            && $bulletin->statut === Bulletin::STATUT_PUBLIE,
            403,
            'Bulletin non disponible ou non publié.'
        );

        $bulletin->load(['eleve', 'classe', 'anneeAcademique', 'etablissement', 'disciplines']);

        return view('eleve.bulletin-show', [
            'eleve' => $eleve,
            'bulletin' => $bulletin,
        ]);
    }

    public function print(Bulletin $bulletin)
    {
        return $this->guardAndRender($bulletin, 'eleve.bulletin-show', ['printMode' => true]);
    }

    public function downloadPdf(Bulletin $bulletin)
    {
        $eleve = $this->currentEleve();
        $user = auth()->user();

        abort_unless(
            (int) $bulletin->tenant_id === (int) $user->tenant_id
            && (int) $bulletin->eleve_id === (int) $eleve->id
            && $bulletin->statut === Bulletin::STATUT_PUBLIE,
            403,
            'Bulletin non disponible ou non publié.'
        );

        $bulletin->load(['eleve', 'classe', 'anneeAcademique', 'etablissement', 'disciplines']);

        return \Barryvdh\DomPDF\Facade\Pdf::loadView('eleve.bulletin-show', [
            'eleve' => $eleve,
            'bulletin' => $bulletin,
            'printMode' => true,
        ])
            ->setPaper('a4')
            ->download('bulletin-'.$bulletin->id.'.pdf');
    }

    private function guardAndRender(Bulletin $bulletin, string $view, array $extra = [])
    {
        $eleve = $this->currentEleve();
        $user = auth()->user();

        abort_unless(
            (int) $bulletin->tenant_id === (int) $user->tenant_id
            && (int) $bulletin->eleve_id === (int) $eleve->id
            && $bulletin->statut === Bulletin::STATUT_PUBLIE,
            403,
            'Bulletin non disponible ou non publié.'
        );

        $bulletin->load(['eleve', 'classe', 'anneeAcademique', 'etablissement', 'disciplines']);

        return view($view, array_merge([
            'eleve' => $eleve,
            'bulletin' => $bulletin,
        ], $extra));
    }
}
