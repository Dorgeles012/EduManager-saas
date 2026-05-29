<?php

namespace App\Http\Controllers\Sadmin;

use App\Http\Controllers\Controller;
use App\Models\Etablissement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class EtablissementController extends Controller
{
    public function index(): View
    {
        $tenantId = auth()->user()?->tenant_id ?? 1;

        $etablissements = Etablissement::query()
            ->where('tenant_id', $tenantId)
            ->orderByDesc('id')
            ->get();

        return view('sadmin.etablissement', compact('etablissements'));
    }

    public function create(): View
    {
        return view('sadmin.etablissement');
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'acronyme' => ['nullable', 'string', 'max:100'],
            'type_etablissement' => ['required', 'string', 'in:primaire,college,lycee,universite,grande_ecole'],
            'email' => ['nullable', 'email', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'adresse' => ['nullable', 'string'],
            'logo' => ['nullable', 'string'],
            'statut' => ['nullable', 'in:active,inactive'],
        ], [
            'type_etablissement.in' => "Le type d'établissement est invalide.",
        ]);

        $payload = [
            ...$validated,
            'tenant_id' => $user?->tenant_id ?? 1,
            'statut' => $validated['statut'] ?? 'active',
        ];

        try {
            Etablissement::create($payload);

            return redirect()->route('sadmin.etablissement')
                ->with('success', "L'établissement a été ajouté avec succès.");
        } catch (\Throwable $e) {
            Log::error('EtablissementController@store failed', [
                'error' => $e->getMessage(),
                'payload' => $payload,
                'tenant_id' => $payload['tenant_id'] ?? null,
                'user_id' => $user?->id,
            ]);

            return redirect()->route('sadmin.etablissement')
                ->with('error', "Impossible d'ajouter l'établissement. Veuillez réessayer.")
                ->withInput();
        }
    }
}

