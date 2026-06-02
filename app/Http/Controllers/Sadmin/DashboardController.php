<?php

namespace App\Http\Controllers\Sadmin;

use App\Http\Controllers\Controller;
use App\Models\Etablissement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $tenantId = auth()->user()?->tenant_id ?? 1;

        // Revenu total = somme des payments (montant)
        $revenueTotal = DB::table('payments')
            ->where('tenant_id', $tenantId)
            ->sum('montant');


        $clientsCount = User::query()
            ->where('tenant_id', $tenantId)
            ->whereRaw('LOWER(role) = ?', ['client'])
            ->count();

        $etablissements = Etablissement::query()
            ->where('tenant_id', $tenantId)
            ->orderBy('nom')
            ->get(['id', 'nom']);

        $etablissementsCount = $etablissements->count();

        $sadminCount = User::query()
            ->where('tenant_id', $tenantId)
            ->where('role', 'SADMIN')
            ->count();

        return view('sadmin.dashboard', [
            'revenueTotal' => (int) $revenueTotal,
            'clientsCount' => $clientsCount,
            'etablissements' => $etablissements,
            'etablissementsCount' => $etablissementsCount,
            'sadminCount' => $sadminCount,
        ]);
    }
}

