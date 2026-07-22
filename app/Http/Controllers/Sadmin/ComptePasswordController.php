<?php

namespace App\Http\Controllers\Sadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sadmin\ComptePasswordUpdateRequest;
use App\Services\RoleDashboardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class ComptePasswordController extends Controller
{
    public function update(ComptePasswordUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()
            ->route(app(RoleDashboardService::class)->routeNameFor($user))
            ->with('success', 'Mot de passe mis à jour avec succès.');
    }
}

