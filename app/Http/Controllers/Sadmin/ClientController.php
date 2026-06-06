<?php

namespace App\Http\Controllers\Sadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sadmin\StoreClientRequest;
use App\Http\Requests\Sadmin\UpdateClientRequest;
use App\Models\Etablissement;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()->whereRaw('LOWER(role) = ?', ['client']);

        $q = trim((string) $request->get('q', ''));
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('nom', 'like', "%{$q}%")
                    ->orWhere('prenom', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('telephone', 'like', "%{$q}%")
                    ->orWhereHas('etablissement', function ($etablissementQuery) use ($q) {
                        $etablissementQuery->where('nom', 'like', "%{$q}%");
                    });
            });
        }

        $status = $request->get('status', 'all');
        if (in_array($status, ['actif', 'bloqué'], true)) {
            // users.statut (DB: actif/bloqué)
            $query->where('statut', $status);
        }

        // Filtre strict sur statut DB (actif/bloqué)
        // Création : toujours 'actif' (bloqué jamais)


        $clients = $query->with('etablissement')->latest()->paginate(10)->withQueryString();

        return view('sadmin.clients.index', compact('clients', 'q', 'status'));
    }

    public function create(): View
    {
        $etablissements = $this->availableEtablissements();

        return view('sadmin.clients.create', compact('etablissements'));
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['photo'] = $this->handlePhotoUpload($request);
        $validated['password'] = Hash::make($validated['password']);
        // Multi-tenant: clients doivent appartenir à un tenant
        $validated['tenant_id'] = auth()->user()?->tenant_id ?? 1;

        // Règles métier imposées
        $password = $validated['password'];

        User::create([
            'tenant_id' => auth()->user()?->tenant_id ?? 1,
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'] ?? null,
            'telephone' => $validated['telephone'] ?? null,
            'email' => $validated['email'],
            'password' => $password,
            'image' => $validated['photo'] ?? null,
            'etablissement_id' => $validated['etablissement_id'] ?? null,
            'adresse' => $validated['adresse'] ?? null,
            'ville' => $validated['ville'] ?? null,
            'role' => 'client',
            // `statut` est forcé par défaut côté modèle (actif)
        ]);


        return redirect()->route('sadmin.clients.index')->with('success', 'Client ajouté avec succès.');
    }

    public function show(User $client): View
    {
        return view('sadmin.clients.show', compact('client'));
    }

    public function edit(User $client): View
    {
        $etablissements = $this->availableEtablissements();


        return view('sadmin.clients.edit', compact('client', 'etablissements'));
    }

    public function update(UpdateClientRequest $request, User $client): RedirectResponse
    {
        $validated = $request->validated();

        $photoPath = $this->handlePhotoUpload($request);
        if ($photoPath !== null) {
            $validated['image'] = $photoPath;
            unset($validated['photo']);
        } else {
            unset($validated['photo']);
        }

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Ne jamais forcer 'blocked' via un champ de formulaire optionnel non sécurisé.
        // La gestion blocage se fait via les actions block/unblock.
        if (array_key_exists('status', $validated)) {
            // On ignore volontairement l'éventuel champ `status` côté formulaire,
            // le blocage/déblocage se fait via les actions block/unblock.
            unset($validated['status']);
        }


        // Map anciens champs CRUD -> colonnes users
        if (array_key_exists('photo', $validated)) {
            $validated['image'] = $validated['photo'];
            unset($validated['photo']);
        }

        $client->update($validated);

        return redirect()->route('sadmin.clients.index')->with('success', 'Client mis à jour avec succès.');
    }

    public function destroy(User $client): RedirectResponse
    {
        $client->delete();

        return redirect()->route('sadmin.clients.index')->with('success', 'Client supprimé avec succès.');
    }

    public function block(User $client): RedirectResponse
    {
        if ($client->statut !== 'bloqué') {
            $client->block();
        }


        return redirect()->route('sadmin.clients.index')->with('success', 'Client bloqué.');
    }

    public function unblock(User $client): RedirectResponse
    {
        if ($client->statut !== 'actif') {
            $client->unblock();
        }


        return redirect()->route('sadmin.clients.index')->with('success', 'Client débloqué.');
    }

    private function handlePhotoUpload(Request $request): ?string
    {
        if (! $request->hasFile('photo')) {
            return null;
        }

        return $request->file('photo')->storeAs(
            'clients',
            Str::uuid()->toString() . '.' . $request->file('photo')->getClientOriginalExtension(),
            'public'
        );
    }

    private function availableEtablissements()
    {
        $tenantId = auth()->user()?->tenant_id ?? 1;

        return Etablissement::query()
            ->where('tenant_id', $tenantId)
            ->orderBy('nom')
            ->get(['id', 'nom']);
    }
}
