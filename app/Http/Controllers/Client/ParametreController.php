<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ParametrePasswordUpdateRequest;
use App\Http\Requests\Client\ParametrePhotoUpdateRequest;
use App\Http\Requests\Client\ParametreUpdateRequest;
use App\Models\User;
use App\Services\RoleDashboardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ParametreController extends Controller
{
    public function index(): View
    {
        return view('client.parametres.index', [
            'client' => $this->client(),
        ]);
    }

    public function update(ParametreUpdateRequest $request): RedirectResponse
    {
        $client = $this->client();
        $validated = $request->validated();

        $client->nom = $validated['nom'];
        $client->prenom = $validated['prenom'];
        $client->email = $validated['email'];
        $client->telephone = $validated['telephone'] ?? null;

        if ($request->hasFile('photo')) {
            $this->storePhoto($client, $request->file('photo'));
        }

        $client->save();

        return redirect()
            ->route('client.parametres.index')
            ->with('success', 'Vos informations ont été mises à jour avec succès.');
    }

    public function updatePassword(ParametrePasswordUpdateRequest $request): RedirectResponse
    {
        $client = $this->client();
        $client->password = Hash::make($request->validated('password'));
        $client->save();

        return redirect()
            ->route(app(RoleDashboardService::class)->routeNameFor($client))
            ->with('success', 'Mot de passe mis à jour avec succès.');
    }

    public function updatePhoto(ParametrePhotoUpdateRequest $request): RedirectResponse
    {
        $client = $this->client();
        $this->storePhoto($client, $request->file('photo'));
        $client->save();

        return redirect()
            ->route('client.parametres.index')
            ->with('success', 'Photo de profil mise à jour avec succès.');
    }

    private function client(): User
    {
        /** @var User $client */
        $client = request()->user();

        return $client;
    }

    private function storePhoto(User $client, UploadedFile $photo): void
    {
        if ($client->image && Storage::disk('public')->exists($client->image)) {
            Storage::disk('public')->delete($client->image);
        }

        $filename = sprintf(
            '%s_%s.%s',
            $client->id,
            Str::random(20),
            $photo->extension() ?: 'jpg',
        );

        $client->image = $photo->storeAs('profile-images', $filename, 'public');
    }
}
