<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Collection;

class AnneeController extends Controller
{
    public function index()
    {
        // Les vues existantes affichent des variables optionnelles.
        return view('client.annee', [
            'totalYears' => 1,
            'recentAdds' => 1,
            'archivedYears' => 0,
            'activeYear' => '2025-2026',
            'academicYears' => [
                ['id' => 1, 'label' => '2025-2026', 'status' => 'PLANIFIÉ'],
            ],
        ]);
    }

    public function create()
    {
        return view('client.annee');
    }

    public function store()
    {
        return redirect()->route('client.annee.index');
    }

    public function show($annee)
    {
        return view('client.annee');
    }

    public function edit($annee)
    {
        return view('client.annee');
    }

    public function update($request, $annee)
    {
        return redirect()->route('client.annee.index');
    }

    public function destroy($annee)
    {
        return redirect()->route('client.annee.index');
    }
}

