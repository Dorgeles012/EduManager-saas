<?php

namespace App\Http\Controllers\Enseignant;

use App\Http\Controllers\Controller;
use App\Models\AnneeAcademique;
use Illuminate\View\View;

class EnseignantAnneeAcademiqueController extends Controller
{
    /**
     * Affiche l'année académique en cours (lecture seule).
     */
    public function index(): View
    {
        $user = auth()->user();
        $tenantId = $user?->tenant_id;
        $etablissementId = $user?->etablissement_id;

        $academicYears = AnneeAcademique::query()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->when($etablissementId, fn ($q) => $q->where('etablissement_id', $etablissementId))
            ->orderByDesc('id')
            ->get();

        $activeYear = $academicYears->firstWhere('statut', 'active');

        return view('enseignant.annee-academique.index', [
            'totalYears' => $academicYears->count(),
            'academicYears' => $academicYears,
            'activeYear' => $activeYear,
        ]);
    }
}

