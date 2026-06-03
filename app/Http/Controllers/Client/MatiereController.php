<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Matiere;
use Illuminate\Support\Facades\View;

class MatiereController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()?->tenant_id;

        $subjects = Matiere::query()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->orderByDesc('id')
            ->get(['id', 'nom', 'coefficient'])
            ->map(function ($matiere) {
                return [
                    'id' => $matiere->id,
                    'name' => $matiere->nom,
                    'coefficient' => (int) $matiere->coefficient,
                    // vue s'attend à `status` parfois (dans le template c'est hardcodé via Actif)
                    'status' => 'active',
                ];
            });

        $totalSubjects = $subjects->count();
        $totalCoefficient = $subjects->sum('coefficient');

        return view('client.matiere', [
            'subjects' => $subjects,
            'totalSubjects' => $totalSubjects,
            'totalCoefficient' => $totalCoefficient,
        ]);
    }
}


