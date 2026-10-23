<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\{AnneeAcademique, Bulletin, Classe, Eleve, EmploiTemps, Enseignant, Etablissement, Matiere, Niveau, Series, User};
use App\Models\Versement;
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

        $subscriptionStatus = app(\App\Services\SubscriptionStatusService::class);
        if (! $subscriptionStatus->hasSubscription($user)) {
            return redirect()->route('client.abonnement.index');
        }

        $schoolId = $user->etablissement_id;
        $forSchool = static fn ($query) => $query->when($schoolId, fn ($q) => $q->where('etablissement_id', $schoolId));

        return view('client.dashboard', [
            'counts' => [
                'eleves' => $forSchool(Eleve::where('tenant_id', $tenantId))->count(),
                'enseignants' => $forSchool(Enseignant::where('tenant_id', $tenantId))->count(),
                'personnels' => $forSchool(User::where('tenant_id', $tenantId)->whereRaw('LOWER(role) = ?', ['personnel']))->count(),
                'parents' => $forSchool(User::where('tenant_id', $tenantId)->whereRaw('LOWER(role) = ?', ['parent']))->count(),
                'clients' => User::where('tenant_id', $tenantId)->whereRaw('LOWER(role) = ?', ['client'])->count(),
                'sadmins' => User::where('tenant_id', $tenantId)->whereRaw('LOWER(role) = ?', ['sadmin'])->count(),
                'classes' => $forSchool(Classe::where('tenant_id', $tenantId))->count(),
                'matieres' => Matiere::withoutGlobalScopes()->where('tenant_id', $tenantId)->count(),
                'filieres' => $forSchool(DB::table('filieres')->where('tenant_id', $tenantId))->count(),
                'series' => Series::where('tenant_id', $tenantId)->count(),
                'niveaux' => $forSchool(Niveau::where('tenant_id', $tenantId))->count(),
                'etablissements' => Etablissement::where('tenant_id', $tenantId)->count(),
                'emplois_du_temps' => $forSchool(EmploiTemps::where('tenant_id', $tenantId))->count(),
                'bulletins' => $forSchool(Bulletin::where('tenant_id', $tenantId))->count(),
                'notifications' => DB::table('notifications')->where(fn ($q) => $q->where('tenant_id', $tenantId)->orWhereNull('tenant_id'))->count(),
                'depenses' => DB::table('depense')->where('tenant_id', $tenantId)->count(),
                'revenu_total' => Versement::query()->where('tenant_id', $tenantId)->where('statut', 'valide')
                    ->whereHas('scolarite.eleve', fn ($q) => $q->where('tenant_id', $tenantId)->when($schoolId, fn ($schoolQuery) => $schoolQuery->where('etablissement_id', $schoolId)))
                    ->sum('montant'),
                'paiements_valides' => Versement::query()->where('tenant_id', $tenantId)->where('statut', 'valide')
                    ->whereHas('scolarite.eleve', fn ($q) => $q->where('tenant_id', $tenantId)->when($schoolId, fn ($schoolQuery) => $schoolQuery->where('etablissement_id', $schoolId)))
                    ->count(),
                'factures_impayees' => DB::table('scolarites')
                    ->join('eleves', 'scolarites.eleve_id', '=', 'eleves.id')
                    ->where('scolarites.tenant_id', $tenantId)
                    ->where('scolarites.reste', '>', 0)
                    ->when($schoolId, fn ($q) => $q->where('eleves.etablissement_id', $schoolId))
                    ->count(),
            ],
            'activeYear' => $forSchool(AnneeAcademique::where('tenant_id', $tenantId)->where('statut', 'active'))->orderByDesc('date_debut')->first(),
        ]);
    }
}
