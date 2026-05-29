<?php

namespace App\Http\Controllers\Sadmin;

use App\Http\Controllers\Controller;
use App\Models\Etablissement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EtablissementCrudController extends Controller
{
    public function index(Request $request): View
    {
        $tenantId = auth()->user()?->tenant_id ?? 1;

        $query = Etablissement::query()
            ->where('tenant_id', $tenantId)
            ->orderByDesc('id');

        // Recherche
        $search = $request->string('q')->trim();
        if ($search->isNotEmpty()) {
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                    ->orWhere('acronyme', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtre (type)
        $type = $request->string('type')->trim();
        if ($type->isNotEmpty()) {
            $query->where('type_etablissement', $type->value());
        }

        // Filtre (statut)
        $statut = $request->string('statut')->trim();
        if ($statut->isNotEmpty() && in_array($statut->value(), ['active', 'inactive'], true)) {
            $query->where('statut', $statut->value());
        }

        // Pagination
        $etablissements = $query->paginate(10)->withQueryString();

        $types = ['primaire', 'college', 'lycee', 'universite', 'grande_ecole'];

        return view('sadmin.etablissements.index', [
            'etablissements' => $etablissements,
            'types' => $types,
            'filters' => [
                'q' => $request->input('q'),
                'type' => $request->input('type'),
                'statut' => $request->input('statut'),
            ],
        ]);
    }

    public function create(): View
    {
        return view('sadmin.etablissements.create');
    }

    public function store(Request $request)
    {
        $tenantId = auth()->user()?->tenant_id ?? 1;

        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'acronyme' => ['nullable', 'string', 'max:100'],
            'type_etablissement' => ['required', 'string', 'in:primaire,college,lycee,universite,grande_ecole'],
            'email' => ['nullable', 'email', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'adresse' => ['nullable', 'string'],
            'logo' => ['nullable', 'string'],
            'statut' => ['nullable', 'in:active,inactive'],
        ]);

        try {
            Etablissement::create([
                ...$validated,
                'tenant_id' => $tenantId,
                'statut' => $validated['statut'] ?? 'active',
            ]);

            return redirect()->route('sadmin.etablissement')
                ->with('success', 'Établissement ajouté avec succès.');
        } catch (\Throwable $e) {
            Log::error('EtablissementCrudController@store failed', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenantId,
                'payload' => $validated,
            ]);

            return redirect()->back()
                ->with('error', "Impossible d'ajouter l'établissement.")
                ->withInput();
        }
    }

    public function show(Etablissement $etablissement): View
    {
        $this->authorizeTenant($etablissement);

        return view('sadmin.etablissements.show', compact('etablissement'));
    }

    public function edit(Etablissement $etablissement): View
    {
        $this->authorizeTenant($etablissement);

        return view('sadmin.etablissements.edit', compact('etablissement'));
    }

    public function update(Request $request, Etablissement $etablissement)
    {
        $this->authorizeTenant($etablissement);

        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'acronyme' => ['nullable', 'string', 'max:100'],
            'type_etablissement' => ['required', 'string', 'in:primaire,college,lycee,universite,grande_ecole'],
            'email' => ['nullable', 'email', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'adresse' => ['nullable', 'string'],
            'logo' => ['nullable', 'string'],
            'statut' => ['nullable', 'in:active,inactive'],
        ]);

        try {
            $etablissement->update([
                ...$validated,
                'statut' => $validated['statut'] ?? $etablissement->statut,
            ]);

            // Recharge depuis MySQL (garanti côté vue)
            $etablissement = $etablissement->fresh();

            return redirect()
                ->route('sadmin.etablissement')
                ->with('success', 'Établissement modifié avec succès.');
        } catch (\Throwable $e) {
            Log::error('EtablissementCrudController@update failed', [
                'error' => $e->getMessage(),
                'etablissement_id' => $etablissement->id,
            ]);

            return redirect()->back()
                ->with('error', "Impossible de modifier l'établissement.")
                ->withInput();
        }
    }

    public function destroy(Etablissement $etablissement)
    {
        $this->authorizeTenant($etablissement);

        try {
            $etablissement->forceDelete();

            return redirect()->route('sadmin.etablissement')
                ->with('success', 'Établissement supprimé.');
        } catch (\Throwable $e) {
            Log::error('EtablissementCrudController@destroy failed', [
                'error' => $e->getMessage(),
                'etablissement_id' => $etablissement->id,
            ]);

            return redirect()->back()
                ->with('error', "Impossible de supprimer l'établissement.");
        }
    }

    private function authorizeTenant(Etablissement $etablissement): void
    {
        $tenantId = auth()->user()?->tenant_id ?? 1;

        if ((int) $etablissement->tenant_id !== (int) $tenantId) {
            abort(403, 'Accès interdit.');
        }
    }
}

