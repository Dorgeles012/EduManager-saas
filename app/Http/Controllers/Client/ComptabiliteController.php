<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

class ComptabiliteController extends Controller
{
    public function index()
    {
        return view('client.comptabilite');
    }
}

