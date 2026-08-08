<?php

namespace App\Http\Controllers\Eleve;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class EleveParametreController extends EleveController
{
    /**
     * Affiche la page des paramètres de l'élève.
     */
    public function index(): View
    {
        $eleve = $this->currentEleve();
        $eleve->load(['classe.niveau', 'niveau', 'serie', 'etablissement']);

        return view('eleve.parametres', [
            'eleve' => $eleve,
        ]);
    }

    /**
     * Modifie le mot de passe depuis les paramètres.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        $user->password = Hash::make($validated['password']);
        $user->must_change_password = false;
        $user->password_changed_at = now();
        $user->save();

        return back()->with('success', 'Votre mot de passe a été modifié avec succès.');
    }
}
