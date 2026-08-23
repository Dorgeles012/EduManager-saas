<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\PersonnelStoreRequest;
use App\Http\Requests\Client\PersonnelUpdateRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PersonnelController extends Controller
{
    public function index()
    {
        $personnels = User::query()
            ->where('client_id', auth()->id())
            ->where('role', 'personnel')
            ->with(['etablissement:id,nom'])
            ->orderByDesc('id')
            ->get();

        return view('client.personnel', [
            'personnels' => $personnels,
        ]);
    }

    public function create()
    {
        return $this->index();
    }

    public function store(PersonnelStoreRequest $request)
    {
        $client = auth()->user();

        // Sécurité supplémentaire : vérifier l'unicité de l'email avant insertion
        $email = trim((string) $request->email);
        if (User::where('email', $email)->exists()) {
            throw ValidationException::withMessages([
                'email' => ['Cette adresse email est déjà utilisée par un autre compte utilisateur.'],
            ]);
        }

        User::create([
            'tenant_id' => $client->tenant_id ?? 1,
            'client_id' => $client->id,
            'etablissement_id' => $client->etablissement_id,
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $email,
            'telephone' => $request->telephone,
            'role' => 'personnel',
            'statut' => 'actif',
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('client.personnel.index')
            ->with('success', 'Personnel créé avec succès.');
    }

    protected function guardPersonnelOwnership(User $personnel): void
    {
        if ((int) $personnel->client_id !== (int) auth()->id()) {
            abort(403, 'Accès non autorisé.');
        }
    }

    public function edit(User $personnel)
    {
        $this->guardPersonnelOwnership($personnel);

        $personnels = User::query()
            ->where('client_id', auth()->id())
            ->where('role', 'personnel')
            ->with(['etablissement:id,nom'])
            ->orderByDesc('id')
            ->get();

        return view('client.personnel', [
            'personnels' => $personnels,
            'editPersonnel' => $personnel,
        ]);
    }

    public function update(PersonnelUpdateRequest $request, User $personnel)
    {
        $this->guardPersonnelOwnership($personnel);

        $email = trim((string) $request->email);
        if (User::where('email', $email)->where('id', '!=', $personnel->id)->exists()) {
            throw ValidationException::withMessages([
                'email' => ['Cette adresse email est déjà utilisée par un autre compte utilisateur.'],
            ]);
        }

        $personnel->update([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $email,
            'telephone' => $request->telephone,
            'password' => $request->filled('password') ? Hash::make($request->password) : $personnel->password,
        ]);

        return redirect()->route('client.personnel.index')
            ->with('success', 'Personnel modifié avec succès.');
    }

    public function destroy(User $personnel)
    {
        $this->guardPersonnelOwnership($personnel);

        $personnel->delete();

        return redirect()->route('client.personnel.index')
            ->with('success', 'Personnel supprimé avec succès.');
    }

    public function block(User $personnel)
    {
        $this->guardPersonnelOwnership($personnel);

        $personnel->update(['statut' => 'bloqué']);

        return redirect()->route('client.personnel.index')
            ->with('success', 'Personnel bloqué avec succès.');
    }

    public function unblock(User $personnel)
    {
        $this->guardPersonnelOwnership($personnel);

        $personnel->update(['statut' => 'actif']);

        return redirect()->route('client.personnel.index')
            ->with('success', 'Personnel débloqué avec succès.');
    }

    public function show(User $personnel)
    {
        $this->guardPersonnelOwnership($personnel);

        return $this->index();
    }
}
