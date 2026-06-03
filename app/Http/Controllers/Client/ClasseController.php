<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\Etablissement;
use App\Models\Matiere;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class ClasseController extends Controller
{
    public function index()
    {
        // IMPORTANT : les vues attendent historiquement des tableaux stylés.
        // Ici, on fournit une collection de tableaux avec des clés attendues par Blade.
        // Tenant scoping : on filtre par tenant_id si disponible.
        $tenantId = auth()->user()?->tenant_id;

        $classesQuery = Classe::query();
        if ($tenantId) {
            $classesQuery->where('tenant_id', $tenantId);
        }

        $classes = $classesQuery
            ->orderByDesc('id')
            ->get()
            ->map(function ($classe) {
                // La vue utilise des clés : name, school, level, student_count, id, max_students
                // Ces colonnes n'existent pas forcément telles quelles en DB (selon ton schéma).
                // On reconstruit donc avec des valeurs sûres.
                $niveauLabel = $classe->niveau_id ? ('Niveau #' . $classe->niveau_id) : 'N/A';
                $schoolLabel = $classe->etablissement_id ? ('Etablissement #' . $classe->etablissement_id) : 'Mon Établissement';

                return [
                    'id' => $classe->id,
                    'name' => $classe->nom,
                    'school' => $schoolLabel,
                    'level' => $niveauLabel,
                    'student_count' => 0,
                    'max_students' => $classe->capacite,
                ];
            });

        // Champs auxiliaires utilisés par la vue
        $schools = Etablissement::query()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->orderByDesc('id')
            ->get(['id', 'nom'])
            ->map(fn ($e) => ['id' => $e->id, 'name' => $e->nom])
            ->values();

        // Niveaux : pour l'instant, on donne des valeurs par défaut si table non modélisée
        $levels = [
            ['id' => 1, 'name' => 'Primaire'],
            ['id' => 2, 'name' => 'Collège'],
            ['id' => 3, 'name' => 'Lycée'],
        ];

        return view('client.classe', [
            'classes' => $classes,
            'totalClasses' => $classes->count(),
            'totalLevels' => count($levels),
            'schoolName' => $schools->first()['name'] ?? 'Mon Établissement',
            'levels' => $levels,
            'schools' => $schools,
        ]);
    }
}


