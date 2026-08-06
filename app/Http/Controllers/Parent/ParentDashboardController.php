<?php

namespace App\Http\Controllers\Parent;

use App\Models\Bulletin;
use App\Models\Eleve;
use Illuminate\View\View;

class ParentDashboardController extends ParentController
{
    public function index(): View
    {
        $parent = auth()->user();

        $eleves = Eleve::with(['classe', 'niveau', 'serie', 'etablissement'])
            ->where('tenant_id', $parent->tenant_id)
            ->where('parent_id', $parent->id)
            ->get();

        $totalEnfants = $eleves->count();
        $totalBulletins = Bulletin::where('tenant_id', $parent->tenant_id)
            ->whereIn('eleve_id', $eleves->pluck('id'))
            ->count();
        $totalNotes = 0;
        foreach ($eleves as $eleve) {
            $totalNotes += $eleve->notes()->count();
        }

        return view('parent.dashboard', [
            'eleves' => $eleves,
            'totalEnfants' => $totalEnfants,
            'totalBulletins' => $totalBulletins,
            'totalNotes' => $totalNotes,
        ]);
    }
}
