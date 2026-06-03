<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

class NiveauxController extends Controller
{
    public function index()
    {
        // Le template utilise potentiellement plusieurs variables.
        // On fournit des valeurs par défaut pour éviter "Undefined variable".
        return view('client.niveaux', [
            'levels' => [
                ['id' => 1, 'name' => 'CM2 B', 'school' => 'saint françois xavier', 'date' => '26-05-2026', 'icon' => 'auto_stories'],
                ['id' => 2, 'name' => 'Tle D', 'school' => 'saint françois xavier', 'date' => '23-05-2026', 'icon' => 'workspace_premium'],
            ],
            'schools' => [
                ['id' => 1, 'name' => 'saint françois xavier'],
            ],
            // Certains templates existants peuvent encore référencer $classes.
            // On fournit une valeur par défaut pour éviter "Undefined variable $classes".
            'classes' => collect(),
            'totalLevels' => 2,
        ]);

    }
}


