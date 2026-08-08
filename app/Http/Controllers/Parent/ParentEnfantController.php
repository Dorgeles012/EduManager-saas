<?php

namespace App\Http\Controllers\Parent;

use App\Models\Eleve;
use Illuminate\View\View;

class ParentEnfantController extends ParentController
{
    /**
     * Liste des enfants affiliés au parent connecté.
     */
    public function index(): View
    {
        $parent = auth()->user();

        $eleves = Eleve::with(['classe.niveau', 'niveau', 'serie', 'etablissement'])
            ->where('tenant_id', $parent->tenant_id)
            ->where('parent_id', $parent->id)
            ->orderBy('nom')
            ->get();

        return view('parent.enfants', [
            'eleves' => $eleves,
        ]);
    }

    /**
     * Détail d'un enfant affilié au parent connecté (sécurisé).
     */
    public function show(Eleve $eleve): View
    {
        $this->childBelongsToParent($eleve);

        $eleve->load(['classe.niveau', 'niveau', 'serie', 'etablissement', 'notes.matiere']);

        $totalNotes = $eleve->notes()->count();
        $totalBulletins = $eleve->bulletins()->count();

        return view('parent.enfant', [
            'eleve' => $eleve,
            'totalNotes' => $totalNotes,
            'totalBulletins' => $totalBulletins,
        ]);
    }
}
