<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ParentProfilController extends Controller
{
    /**
     * Affiche le profil du parent connecté.
     */
    public function index(): View
    {
        $parent = auth()->user();

        return view('parent.profil', [
            'parent' => $parent,
        ]);
    }

    /**
     * Met à jour les informations personnelles du parent.
     */
    public function update(Request $request): RedirectResponse
    {
        $parent = $request->user();

        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['nullable', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'ville' => ['nullable', 'string', 'max:255'],
        ]);

        $parent->update($validated);

        return back()->with('success', 'Vos informations ont été mises à jour.');
    }

    /**
     * Met à jour le mot de passe du parent.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $parent = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if (! Hash::check($validated['current_password'], $parent->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        $parent->password = Hash::make($validated['password']);
        $parent->save();

        return back()->with('success', 'Votre mot de passe a été modifié avec succès.');
    }

    /**
     * Met à jour la photo de profil du parent.
     */
    public function updatePhoto(Request $request): RedirectResponse
    {
        $parent = $request->user();

        $validated = $request->validate([
            'image' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
        ]);

        if ($parent->image) {
            $oldPath = ltrim($parent->image, '/');
            if (str_starts_with($oldPath, 'storage/')) {
                $oldPath = substr($oldPath, strlen('storage/'));
            }
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $path = $request->file('image')->store('profils', 'public');
        $parent->image = $path;
        $parent->save();

        return back()->with('success', 'Votre photo de profil a été mise à jour.');
    }
}
