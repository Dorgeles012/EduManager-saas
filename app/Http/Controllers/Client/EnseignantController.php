<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Matiere;
use App\Models\User;
use Illuminate\Support\Facades\View;

class EnseignantController extends Controller
{
    public function index()
    {
        $client = auth()->user();

        $teachers = User::query()
            ->where('role', 'enseignant')
            ->when(!empty($client?->tenant_id), function ($q) use ($client) {
                $q->where('tenant_id', $client->tenant_id);
            })
            ->orderByDesc('id')
            ->paginate(10);

        // Matières pour le select (utilisé par la vue)
        $subjects = Matiere::query()
            ->when(!empty($client?->tenant_id), function ($q) use ($client) {
                $q->where('tenant_id', $client->tenant_id);
            })
            ->orderBy('nom')
            ->pluck('nom')
            ->toArray();

        return view('client.enseignant', [
            'teachers' => $teachers,
            'subjects' => $subjects,
        ]);
    }
}



