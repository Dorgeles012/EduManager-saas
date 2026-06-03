<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

class PersonnelController extends Controller
{
    public function index()
    {
        return view('client.personnel');
    }

    public function create()
    {
        return view('client.personnel');
    }

    public function store()
    {
        return redirect()->route('client.personnel.index');
    }

    public function show($personnel)
    {
        return view('client.personnel');
    }

    public function edit($personnel)
    {
        return view('client.personnel');
    }

    public function update($request, $personnel)
    {
        return redirect()->route('client.personnel.index');
    }

    public function destroy($personnel)
    {
        return redirect()->route('client.personnel.index');
    }
}

