<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Models\{AnneeAcademique, Bulletin, Classe, Eleve, Etablissement, Matiere, Niveau, Series, User};
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PersonnelDashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $tenantId = $user?->tenant_id;

        if (empty($tenantId)) {
            return view('personnel.dashboard.index', ['counts' => [], 'activeYear' => null]);
        }

        $schoolId = $user->etablissement_id;
        $forSchool = static fn ($query) => $query->when($schoolId, fn ($q) => $q->where('etablissement_id', $schoolId));

        return view('personnel.dashboard.index', [
            'counts' => [
                'eleves' => $forSchool(Eleve::where('tenant_id', $tenantId))->count(),
                'enseignants' => $forSchool(User::where('tenant_id', $tenantId)->whereRaw('LOWER(role) = ?', ['enseignant']))->count(),
                'personnels' => $forSchool(User::where('tenant_id', $tenantId)->whereRaw('LOWER(role) = ?', ['personnel']))->count(),
                'parents' => $forSchool(User::where('tenant_id', $tenantId)->whereRaw('LOWER(role) = ?', ['parent']))->count(),
                'classes' => $forSchool(Classe::where('tenant_id', $tenantId))->count(),
                'matieres' => Matiere::withoutGlobalScopes()->where('tenant_id', $tenantId)->count(),
                'series' => Series::where('tenant_id', $tenantId)->count(),
                'niveaux' => $forSchool(Niveau::where('tenant_id', $tenantId))->count(),
                'etablissements' => Etablissement::where('tenant_id', $tenantId)->count(),
                'bulletins' => $forSchool(Bulletin::where('tenant_id', $tenantId))->count(),
                'notifications' => DB::table('notifications')->where(fn ($q) => $q->where('tenant_id', $tenantId)->orWhereNull('tenant_id'))->count(),
                'depenses' => DB::table('depense')->where('tenant_id', $tenantId)->count(),
                'revenu_total' => DB::table('scolarites')->where('tenant_id', $tenantId)->sum('montant_paye') ?: 0,
                'factures_impayees' => DB::table('scolarites')->where('tenant_id', $tenantId)->where('reste', '>', 0)->count(),
            ],
            'activeYear' => $forSchool(AnneeAcademique::where('tenant_id', $tenantId)->where('statut', 'active'))->orderByDesc('date_debut')->first(),
        ]);
    }
}

