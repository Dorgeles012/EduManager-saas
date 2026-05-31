<?php

namespace App\Http\Controllers\Sadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sadmin\StoreClientRequest;
use App\Http\Requests\Sadmin\UpdateClientRequest;
use App\Models\Client;
use App\Models\Etablissement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $query = Client::query()->with('etablissement');

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
            $query->where('status', $status);
        }

        $clients = $query->latest()->paginate(10)->withQueryString();

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
        $validated['status'] = $validated['status'] ?? 'actif';

        // Multi-tenant: clients doivent appartenir à un tenant
        $validated['tenant_id'] = auth()->user()?->tenant_id ?? 1;

        Client::create($validated);

        return redirect()->route('sadmin.clients.index')->with('success', 'Client ajouté avec succès.');
    }

    public function show(Client $client): View
    {
        $client->load('etablissement');

        return view('sadmin.clients.show', compact('client'));
    }

    public function edit(Client $client): View
    {
        $client->load('etablissement');
        $etablissements = $this->availableEtablissements();

        return view('sadmin.clients.edit', compact('client', 'etablissements'));
    }

    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        $validated = $request->validated();

        $photoPath = $this->handlePhotoUpload($request);
        if ($photoPath !== null) {
            $validated['photo'] = $photoPath;
        } else {
            unset($validated['photo']);
        }

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['status'] = $validated['status'] ?? $client->status;

        $client->update($validated);

        return redirect()->route('sadmin.clients.index')->with('success', 'Client mis à jour avec succès.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        $client->delete();

        return redirect()->route('sadmin.clients.index')->with('success', 'Client supprimé avec succès.');
    }

    public function block(Client $client): RedirectResponse
    {
        if ($client->status !== 'bloqué') {
            $client->update(['status' => 'bloqué']);
        }

        return redirect()->route('sadmin.clients.index')->with('success', 'Client bloqué.');
    }

    public function unblock(Client $client): RedirectResponse
    {
        if ($client->status !== 'actif') {
            $client->update(['status' => 'actif']);
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
