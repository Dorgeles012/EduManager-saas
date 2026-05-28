<?php

namespace App\Http\Controllers\Sadmin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class EtablissementController extends Controller
{
    public function index(): View
    {
        return view('sadmin.etablissement');
    }
}

