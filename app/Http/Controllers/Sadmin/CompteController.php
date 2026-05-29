<?php

namespace App\Http\Controllers\Sadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sadmin\CompteUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CompteController extends Controller
{
    /**
     * Display the account form.
     */
    public function index(): View
    {
        $user = Auth::user();

        return view('sadmin.compte', compact('user'));
    }

    /**
     * Update the authenticated user's account.
     */
    public function update(CompteUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Update basic information
        $user->nom = $validated['nom'] ?? $user->nom;
        $user->prenom = $validated['prenom'] ?? $user->prenom;
        $user->email = $validated['email'] ?? $user->email;
        $user->telephone = $validated['telephone'] ?? $user->telephone;



        // Handle profile image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $dir = 'profile-images';
            $filename = sprintf(
                '%s_%s.%s',
                $user->id,
                Str::random(20),
                $file->getClientOriginalExtension() ?: 'jpg'
            );

            $path = $file->storeAs($dir, $filename, 'public');

            // store only relative path (public disk)
            $user->image = $path;
        }

        // Password update (optional)
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        // If email changed: force re-verify
        if ($user->isDirty('email') && $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Ensure fresh values are visible immediately (no stale cache)
        $user->refresh();

        return redirect()
            ->route('sadmin.compte')
            ->with('success', 'Vos informations ont été mises à jour avec succès.');
    }
}


