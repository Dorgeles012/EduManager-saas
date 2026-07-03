<?php

namespace App\Http\Controllers\Sadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sadmin\EtablissementRequest;
use App\Models\Etablissement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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

    public function store(EtablissementRequest $request)
    {
        $tenantId = auth()->user()?->tenant_id ?? 1;

        $validated = $request->validated();
        unset($validated['logo']);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $extension = strtolower($file->getClientOriginalExtension() ?: 'png');
            $uniqueName = Str::uuid()->toString() . '.' . $extension;

            $logoPath = Storage::disk('public')->putFileAs('logos', $file, $uniqueName);
        }

        try {
            Etablissement::create([
                ...$validated,
                'logo' => $logoPath,
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

            if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }

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

    public function update(EtablissementRequest $request, Etablissement $etablissement)
    {
        $this->authorizeTenant($etablissement);

        $validated = $request->validated();
        unset($validated['logo']);

        $newLogoPath = null;
        $oldLogoPath = $etablissement->logo;
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $extension = strtolower($file->getClientOriginalExtension() ?: 'png');
            $uniqueName = Str::uuid()->toString() . '.' . $extension;

            $newLogoPath = Storage::disk('public')->putFileAs('logos', $file, $uniqueName);
        }

        try {
            $etablissement->update([
                ...$validated,
                'logo' => $newLogoPath ?? $etablissement->logo,
                'statut' => $validated['statut'] ?? $etablissement->statut,
            ]);

            $etablissement = $etablissement->fresh();

            if ($newLogoPath && $oldLogoPath && Storage::disk('public')->exists($oldLogoPath)) {
                Storage::disk('public')->delete($oldLogoPath);
            }

            return redirect()
                ->route('sadmin.etablissement')
                ->with('success', 'Établissement modifié avec succès.');
        } catch (\Throwable $e) {
            // Cleanup du nouveau logo si update échoue
            if ($newLogoPath && Storage::disk('public')->exists($newLogoPath)) {
                Storage::disk('public')->delete($newLogoPath);
            }

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
            if (! empty($etablissement->logo) && Storage::disk('public')->exists($etablissement->logo)) {
                Storage::disk('public')->delete($etablissement->logo);
            }

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


