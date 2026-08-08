<?php

namespace App\Http\Controllers\Eleve;

use App\Services\ScolariteService;
use Illuminate\View\View;

class EleveScolariteController extends EleveController
{
    public function index(ScolariteService $scolariteService): View
    {
        $eleve = $this->currentEleve();

        $situation = $scolariteService->situation($eleve);

        return view('eleve.scolarite', [
            'eleve' => $eleve,
            'situation' => $situation,
        ]);
    }
}
