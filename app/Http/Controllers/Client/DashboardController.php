<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\{AnneeAcademique, Bulletin, Classe, Eleve, EmploiTemps, Etablissement, Matiere, Series, User};
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $tenantId = $user?->tenant_id;

        if (empty($tenantId)) {
            return view('client.dashboard', ['counts' => [], 'activeYear' => null]);
        }

        $schoolId = $user->etablissement_id;
        $forSchool = static fn ($query) => $query->when($schoolId, fn ($q) => $q->where('etablissement_id', $schoolId));

        return view('client.dashboard', [
            'counts' => [
                'eleves' => $forSchool(Eleve::where('tenant_id', $tenantId))->count(),
                'enseignants' => $forSchool(User::where('tenant_id', $tenantId)->whereRaw('LOWER(role) = ?', ['enseignant']))->count(),
                'personnels' => $forSchool(User::where('tenant_id', $tenantId)->whereRaw('LOWER(role) = ?', ['personnel']))->count(),
                'parents' => $forSchool(User::where('tenant_id', $tenantId)->whereRaw('LOWER(role) = ?', ['parent']))->count(),
                'clients' => User::where('tenant_id', $tenantId)->whereRaw('LOWER(role) = ?', ['client'])->count(),
                'sadmins' => User::where('tenant_id', $tenantId)->whereRaw('LOWER(role) = ?', ['sadmin'])->count(),
                'classes' => $forSchool(Classe::where('tenant_id', $tenantId))->count(),
                'matieres' => Matiere::withoutGlobalScopes()->where('tenant_id', $tenantId)->count(),
                'filieres' => $forSchool(DB::table('filieres')->where('tenant_id', $tenantId))->count(),
                'series' => Series::where('tenant_id', $tenantId)->count(),
                'etablissements' => Etablissement::where('tenant_id', $tenantId)->count(),
                'emplois_du_temps' => $forSchool(EmploiTemps::where('tenant_id', $tenantId))->count(),
                'bulletins' => $forSchool(Bulletin::where('tenant_id', $tenantId))->count(),
                'notifications' => DB::table('notifications')->where(fn ($q) => $q->where('tenant_id', $tenantId)->orWhereNull('tenant_id'))->count(),
                'depenses' => DB::table('depense')->where('tenant_id', $tenantId)->count(),
            ],
            'activeYear' => $forSchool(AnneeAcademique::where('tenant_id', $tenantId)->where('statut', 'active'))->orderByDesc('date_debut')->first(),
        ]);
    }
}
