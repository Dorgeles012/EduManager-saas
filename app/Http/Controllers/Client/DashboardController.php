<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Etablissement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $tenantId = auth()->user()?->tenant_id;

        // Par défaut, on empêche l’accès à des données hors tenant.
        // Si tenant_id est null, on renvoie simplement 0/vides.
        if (empty($tenantId)) {
            return view('client.dashboard', [
                'revenueTotal' => 0,
                'clientsCount' => 0,
                'etablissements' => collect(),
                'etablissementsCount' => 0,
                'sadminCount' => 0,
            ]);
        }

        $revenueTotal = DB::table('payments')
            ->where('tenant_id', $tenantId)
            ->sum('montant');

        // Un client ne doit voir que ses propres données.
        // Ici, on limite à son tenant.
        $clientsCount = User::query()
            ->where('tenant_id', $tenantId)
            ->whereRaw('LOWER(role) = ?', ['client'])
            ->count();

        $etablissements = Etablissement::query()
            ->where('tenant_id', $tenantId)
            ->orderBy('nom')
            ->get(['id', 'nom']);

        $etablissementsCount = $etablissements->count();

        // Les clients ne doivent pas vraiment “voir” des sadmin d’un autre tenant.
        // On garde la stat mais filtrée tenant.
        $sadminCount = User::query()
            ->where('tenant_id', $tenantId)
            ->whereRaw('LOWER(role) = ?', ['sadmin'])
            ->count();

        return view('client.dashboard', [
            'revenueTotal' => (int) $revenueTotal,
            'clientsCount' => $clientsCount,
            'etablissements' => $etablissements,
            'etablissementsCount' => $etablissementsCount,
            'sadminCount' => $sadminCount,
        ]);
    }
}

