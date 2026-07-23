<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\RoleDashboardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PersonnelParametreController extends Controller
{
    public function index(): View
    {
        return view('personnel.parametres.index', [
            'client' => $this->user(),
        ]);
    }

    public function update(\App\Http\Requests\Client\ParametreUpdateRequest $request): RedirectResponse
    {
        $user = $this->user();
        $validated = $request->validated();

        $user->nom = $validated['nom'];
        $user->prenom = $validated['prenom'];
        $user->email = $validated['email'];
        $user->telephone = $validated['telephone'] ?? null;

        if ($request->hasFile('photo')) {
            $this->storePhoto($user, $request->file('photo'));
        }

        $user->save();

        return redirect()
            ->route('personnel.parametres.index')
            ->with('success', 'Vos informations ont été mises à jour avec succès.');
    }

    public function updatePassword(\App\Http\Requests\Client\ParametrePasswordUpdateRequest $request): RedirectResponse
    {
        $user = $this->user();
        $user->password = Hash::make($request->validated('password'));
        $user->save();

        return redirect()
            ->route(app(RoleDashboardService::class)->routeNameFor($user))
            ->with('success', 'Mot de passe mis à jour avec succès.');
    }

    public function updatePhoto(\App\Http\Requests\Client\ParametrePhotoUpdateRequest $request): RedirectResponse
    {
        $user = $this->user();
        $this->storePhoto($user, $request->file('photo'));
        $user->save();

        return redirect()
            ->route('personnel.parametres.index')
            ->with('success', 'Photo de profil mise à jour avec succès.');
    }

    private function user(): User
    {
        /** @var User $user */
        $user = request()->user();
        return $user;
    }

    private function storePhoto(User $user, UploadedFile $photo): void
    {
        if ($user->image && Storage::disk('public')->exists($user->image)) {
            Storage::disk('public')->delete($user->image);
        }

        $filename = sprintf(
            '%s_%s.%s',
            $user->id,
            Str::random(20),
            $photo->extension() ?: 'jpg',
        );

        $user->image = $photo->storeAs('profile-images', $filename, 'public');
    }
}

