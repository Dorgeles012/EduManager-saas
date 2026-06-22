<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Depense;
use App\Models\Eleve;
use App\Models\Scolarite;
use App\Models\Versement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComptabiliteController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $versements = Versement::with('scolarite.eleve.classe')
            ->where('tenant_id', $user->tenant_id)
            ->latest()
            ->get();

        $expenses = Depense::query()
            ->where('tenant_id', $user->tenant_id)
            ->latest('id_depense')
            ->get();

        $levels = \App\Models\Niveau::where('tenant_id', $user->tenant_id)->orderBy('nom')->get(['id', 'nom']);
        $classes = \App\Models\Classe::where('tenant_id', $user->tenant_id)->orderBy('nom')->get(['id', 'nom']);
        $eleves = Eleve::where('tenant_id', $user->tenant_id)->orderBy('nom')->get(['id', 'nom', 'prenom', 'classe_id', 'niveau_id']);

        $totalIncome = $versements->sum('montant');
        $totalExpense = $expenses->sum('montant');

        return view('client.comptabilite', [
            'payments' => $versements->map(fn ($versement) => [
                'student' => trim(($versement->scolarite?->eleve?->nom ?? '') . ' ' . ($versement->scolarite?->eleve?->prenom ?? '')) ?: 'N/A',
                'class' => $versement->scolarite?->eleve?->classe?->nom ?? 'N/A',
                'amount' => $versement->montant,
                'date' => $versement->date_versement?->format('d/m/Y') ?? $versement->created_at?->format('d/m/Y'),
            ]),
            'expenses' => $expenses->map(fn ($depense) => [
                'id' => $depense->id_depense,
                'label' => $depense->libel_depense,
                'amount' => $depense->montant,
                'category' => $depense->categorie,
                'date' => $depense->date_depense?->format('d/m/Y') ?? $depense->created_at?->format('d/m/Y'),
            ]),
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'currentBalance' => $totalIncome - $totalExpense,
            'paymentCount' => $versements->count(),
            'levels' => $levels->map(fn ($level) => ['id' => $level->id, 'name' => $level->nom]),
            'classes' => $classes->map(fn ($classe) => ['id' => $classe->id, 'name' => $classe->nom]),
            'eleves' => $eleves->map(fn ($eleve) => [
                'id' => $eleve->id,
                'name' => trim($eleve->nom . ' ' . $eleve->prenom),
                'classe_id' => $eleve->classe_id,
                'niveau_id' => $eleve->niveau_id,
            ]),
            'currentYear' => now()->year . '-' . now()->addYear()->year,
        ]);
    }

    public function storeScolarite(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'eleve_id' => ['required', 'exists:eleves,id'],
            'montant' => ['required', 'integer', 'min:1'],
            'montant_versement' => ['nullable', 'integer', 'min:1'],
            'annee_scolaire' => ['nullable', 'string', 'max:100'],
            'date_versement' => ['nullable', 'date'],
        ]);

        $eleve = Eleve::where('tenant_id', $user->tenant_id)->findOrFail($validated['eleve_id']);
        $versementMontant = $validated['montant_versement'] ?? $validated['montant'];

        DB::transaction(function () use ($validated, $user, $eleve, $versementMontant) {
            $scolarite = Scolarite::firstOrCreate(
                [
                    'tenant_id' => $user->tenant_id,
                    'eleve_id' => $eleve->id,
                    'annee_scolaire' => $validated['annee_scolaire'] ?? null,
                ],
                [
                    'montant_total' => $validated['montant'],
                    'montant_paye' => 0,
                    'reste' => $validated['montant'],
                    'statut' => 'impaye',
                ]
            );

            $scolarite->montant_total = max($scolarite->montant_total, (int) $validated['montant']);
            $scolarite->montant_paye += $versementMontant;
            $scolarite->reste = max($scolarite->montant_total - $scolarite->montant_paye, 0);
            $scolarite->statut = $scolarite->reste === 0 ? 'paye' : 'partiel';
            $scolarite->save();

            Versement::create([
                'tenant_id' => $user->tenant_id,
                'scolarite_id' => $scolarite->id,
                'montant' => $versementMontant,
                'date_versement' => $validated['date_versement'] ?? now()->toDateString(),
                'methode' => 'Espèces',
            ]);
        });

        return back()->with('success', 'Paiement enregistré avec succès.');
    }

    public function storeDepense(Request $request)
    {
        $validated = $this->validateDepense($request);

        Depense::create($validated + ['tenant_id' => auth()->user()->tenant_id]);

        return back()->with('success', 'Dépense enregistrée avec succès.');
    }

    public function updateDepense(Request $request, Depense $depense)
    {
        $this->authorizeDepense($depense);
        $depense->update($this->validateDepense($request));

        return back()->with('success', 'Dépense mise à jour avec succès.');
    }

    public function destroyDepense(Depense $depense)
    {
        $this->authorizeDepense($depense);
        $depense->delete();

        return back()->with('success', 'Dépense supprimée avec succès.');
    }

    private function validateDepense(Request $request): array
    {
        return $request->validate([
            'libel_depense' => ['required', 'string', 'max:255'],
            'montant' => ['required', 'integer', 'min:1'],
            'categorie' => ['nullable', 'string', 'max:255'],
            'date_depense' => ['nullable', 'date'],
        ]);
    }

    private function authorizeDepense(Depense $depense): void
    {
        abort_unless($depense->tenant_id === auth()->user()->tenant_id, 403);
    }
}
