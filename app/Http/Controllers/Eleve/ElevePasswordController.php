<?php

namespace App\Http\Controllers\Eleve;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Gère le changement obligatoire de mot de passe à la première connexion.
 */
class ElevePasswordController extends Controller
{
    public function showChangeForm(): View
    {
        return view('eleve.change-password');
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = $request->user();

        if (! Hash::check($request->string('current_password'), $user->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        $user->password = Hash::make($request->string('password'));
        $user->must_change_password = false;
        $user->password_changed_at = now();
        $user->save();

        return redirect()
            ->route('eleve.dashboard')
            ->with('success', 'Votre mot de passe a été modifié avec succès.');
    }
}
