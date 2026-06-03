<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

class AbonnementController extends Controller
{
    public function index()
    {
        return view('client.abonnements');
    }

    // CRUD minimal : pour éviter 404 si la sidebar/les futures actions utilisent route resource.
    public function create()
    {
        return view('client.abonnements');
    }

    public function store()
    {
        return redirect()->route('client.abonnement.index');
    }

    public function show($abonnement)
    {
        return view('client.abonnements');
    }

    public function edit($abonnement)
    {
        return view('client.abonnements');
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

