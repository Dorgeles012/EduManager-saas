<?php

namespace App\Http\Controllers\Sadmin;

use App\Http\Controllers\Controller;
use App\Models\Etablissement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Support\Str;

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
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:4096'],
            'statut' => ['nullable', 'in:active,inactive'],
        ], [
            'type_etablissement.in' => "Le type d'établissement est invalide.",
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $extension = strtolower($file->getClientOriginalExtension() ?: 'png');
            $uniqueName = Str::uuid()->toString() . '.' . $extension;

            $stored = Storage::disk('public')->putFileAs('logos', $file, $uniqueName);
            $logoPath = $stored; // chemin relatif dans storage/app/public
        }

        $payload = [
            ...$validated,
            'logo' => $logoPath,
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

            // Cleanup si on a uploadé mais que l'insertion a échoué
            if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }

            return redirect()->route('sadmin.etablissement')
                ->with('error', "Impossible d'ajouter l'établissement. Veuillez réessayer.")
                ->withInput();
        }
    }
}


