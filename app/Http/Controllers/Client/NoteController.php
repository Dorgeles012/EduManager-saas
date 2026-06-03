<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

class NoteController extends Controller
{
    public function index()
    {
        return view('client.note');
    }
}

