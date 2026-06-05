<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class AbonnementController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type');

        $planQuery = Plan::query()->where('statut', 'active');

        if ($type) {
            // Filtre par type via la colonne plans.subscription_type_id
            $planQuery->where('subscription_type_id', $type);
        }

        // “Premier plan actif” pour conserver le design actuel (1 seule carte)
        // On tri avec `id` (stable) pour garantir que le “premier” est déterministe.
        $featuredPlan = $planQuery->orderBy('id')->first();

        return view('client.abonnements', [
            'featuredPlan' => $featuredPlan,
        ]);
    }


    // CRUD minimal : pour éviter 404 si la sidebar/les futures actions utilisent route resource.
    public function create(Request $request)
    {
        return $this->index($request);
    }

    public function store()
    {
        return redirect()->route('client.abonnement.index');
    }

    public function show($abonnement)
    {
        return redirect()->route('client.abonnement.index');
    }

    public function edit($abonnement)
    {
        return redirect()->route('client.abonnement.index');
    }

    public function update($request, $abonnement)
    {
        return redirect()->route('client.abonnement.index');
    }

    public function destroy($abonnement)
    {
        return redirect()->route('client.abonnement.index');
    }
}


