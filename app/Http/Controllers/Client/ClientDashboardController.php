<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

class ClientDashboardController extends Controller
{
    public function index()
    {
        return app('App\\Http\\Controllers\\Client\\DashboardController')->index();
    }
}

