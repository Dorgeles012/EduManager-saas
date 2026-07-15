<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AnneeAcademique;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnneeController extends Controller
{
    public function destroy(AnneeAcademique $annee)
    {
        $user = auth()->user();
        $tenantId = $user?->tenant_id;
        $etablissementId = $user?->etablissement_id;

        abort_unless(
            $annee->tenant_id === $tenantId && $annee->etablissement_id === $etablissementId,
            403
        );

        $annee->delete();

        return redirect()
            ->route('client.annee.index')
            ->with('success', 'Année académique supprimée avec succès.');
    }

    public function index()
    {
        $user = auth()->user();
        $tenantId = $user?->tenant_id;
        $etablissementId = $user?->etablissement_id;

        $academicYears = AnneeAcademique::query()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->when($etablissementId, fn ($q) => $q->where('etablissement_id', $etablissementId))
            ->orderByDesc('id')
            ->get();

        $activeYear = $academicYears->firstWhere('statut', 'active');

        return view('client.annee', [
            'totalYears' => $academicYears->count(),
            'academicYears' => $academicYears,
            'activeYear' => $activeYear?->libelle,
        ]);
    }

    public function create()
    {
        // Toujours envoyer les variables utilisées par la vue (stats + liste)
        return view('client.annee', [
            'mode' => 'create',
            'academicYears' => collect(),
            'totalYears' => 0,
            'activeYear' => '—',
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $tenantId = $user?->tenant_id;
        $etablissementId = $user?->etablissement_id;

        $validated = $request->validate([
            'libelle' => [
                'required',
                'string',
                'regex:/^\\d{4}-\\d{4}$/',
                'unique:annee_academique,libelle,NULL,id,tenant_id,' . $tenantId . ',etablissement_id,' . $etablissementId,
            ],
            'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'statut' => ['required', 'in:active,inactive'],
        ], [
            'libelle.regex' => "Le libellé doit respecter le format AAAA-AAAA (ex: 2025-2026).",
        ]);

        DB::transaction(function () use ($validated, $tenantId, $etablissementId) {
            if (($validated['statut'] ?? null) === 'active') {
                AnneeAcademique::query()
                    ->where('tenant_id', $tenantId)
                    ->where('etablissement_id', $etablissementId)
                    ->where('statut', 'active')
                    ->update(['statut' => 'inactive']);
            }

            AnneeAcademique::create([
                'tenant_id' => $tenantId,
                'etablissement_id' => $etablissementId,
                'libelle' => $validated['libelle'],
                'date_debut' => $validated['date_debut'] ?? null,
                'date_fin' => $validated['date_fin'] ?? null,
                'statut' => $validated['statut'],
            ]);
        });

        return redirect()
            ->route('client.annee.index')
            ->with('success', "Année académique créée avec succès.");
    }

    public function edit(AnneeAcademique $annee)
    {
        $user = auth()->user();
        $tenantId = $user?->tenant_id;
        $etablissementId = $user?->etablissement_id;

        abort_unless(
            $annee->tenant_id === $tenantId && $annee->etablissement_id === $etablissementId,
            403
        );

        // Toujours envoyer les variables utilisées par la vue (stats + liste)
        $academicYears = AnneeAcademique::query()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->when($etablissementId, fn ($q) => $q->where('etablissement_id', $etablissementId))
            ->orderByDesc('id')
            ->get();

        $activeYear = $academicYears->firstWhere('statut', 'active');

        return view('client.annee', [
            'mode' => 'edit',
            'annee' => $annee,
            'academicYears' => $academicYears,
            'totalYears' => $academicYears->count(),
            'activeYear' => $activeYear?->libelle ?? '—',
        ]);
    }

    public function update(Request $request, AnneeAcademique $annee, NotificationService $notifications)
    {
        $user = auth()->user();
        $tenantId = $user?->tenant_id;
        $etablissementId = $user?->etablissement_id;

        abort_unless(
            $annee->tenant_id === $tenantId && $annee->etablissement_id === $etablissementId,
            403
        );

        $validated = $request->validate([
            'libelle' => [
                'required',
                'string',
                'regex:/^\\d{4}-\\d{4}$/',
                'unique:annee_academique,libelle,' . $annee->id . ',id,tenant_id,' . $tenantId . ',etablissement_id,' . $etablissementId,
            ],
            'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'statut' => ['required', 'in:active,inactive'],
        ], [
            'libelle.regex' => "Le libellé doit respecter le format AAAA-AAAA (ex: 2025-2026).",
        ]);

        DB::transaction(function () use ($validated, $tenantId, $etablissementId, $annee, $user, $notifications) {
            $isActivating = ($validated['statut'] ?? null) === 'active' && $annee->statut !== 'active';
            $isEnding = ($validated['statut'] ?? null) === 'inactive' && $annee->statut === 'active';

            if ($isActivating) {
                AnneeAcademique::query()
                    ->where('tenant_id', $tenantId)
                    ->where('etablissement_id', $etablissementId)
                    ->where('statut', 'active')
                    ->where('id', '!=', $annee->id)
                    ->update(['statut' => 'inactive']);
            }

            $annee->update([
                'libelle' => $validated['libelle'],
                'date_debut' => $validated['date_debut'] ?? null,
                'date_fin' => $validated['date_fin'] ?? null,
                'statut' => $validated['statut'],
            ]);

            if ($isEnding) {
                $notifications->sendToAudience($user, 'all', 'Année scolaire terminée', "L'année scolaire {$annee->libelle} est désormais terminée.", 'school_year');
            }
        });

        return redirect()
            ->route('client.annee.index')
            ->with('success', "Année académique mise à jour avec succès.");
    }
}


