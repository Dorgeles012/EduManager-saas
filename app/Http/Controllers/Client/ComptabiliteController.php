<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Depense;
use App\Models\Eleve;
use App\Models\FraisScolarite;
use App\Models\Niveau;
use App\Models\Scolarite;
use App\Models\User;
use App\Models\Versement;
use App\Services\NotificationService;
use App\Services\PaymentProviderService;
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

        $levels = Niveau::where('tenant_id', $user->tenant_id)->orderBy('nom')->get(['id', 'nom']);
        $classes = \App\Models\Classe::where('tenant_id', $user->tenant_id)->orderBy('nom')->get(['id', 'nom']);
        $eleves = Eleve::where('tenant_id', $user->tenant_id)->orderBy('nom')->get(['id', 'nom', 'prenom', 'classe_id', 'niveau_id', 'matricule']);

        // Frais par niveau pour l'année académique courante
        $fraisParNiveau = FraisScolarite::with('niveau')
            ->where('tenant_id', $user->tenant_id)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->get();

        $totalIncome = $versements->sum('montant');
        $totalExpense = $expenses->sum('montant');

        return view('client.comptabilite', [
            'payments' => $versements->map(fn ($versement) => [
                'student' => trim(($versement->scolarite?->eleve?->nom ?? '') . ' ' . ($versement->scolarite?->eleve?->prenom ?? '')) ?: 'N/A',
                'class' => $versement->scolarite?->eleve?->classe?->nom ?? 'N/A',
                'amount' => $versement->montant,
                'date' => $versement->date_versement?->format('d/m/Y') ?? $versement->created_at?->format('d/m/Y'),
                'method' => $versement->methode,
                'reference' => $versement->reference,
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
                'matricule' => $eleve->matricule,
                'name' => trim($eleve->nom . ' ' . $eleve->prenom),
                'classe_id' => $eleve->classe_id,
                'niveau_id' => $eleve->niveau_id,
            ]),
            'fraisParNiveau' => $fraisParNiveau,
            'paymentMethods' => PaymentProviderService::METHODS,
            'currentYear' => now()->year . '-' . now()->addYear()->year,
        ]);
    }

    /**
     * Recherche un élève par matricule (AJAX).
     */
    public function searchByMatricule(Request $request)
    {
        $user = auth()->user();
        $matricule = trim((string) $request->input('matricule'));

        $eleve = Eleve::with(['niveau', 'classe', 'etablissement'])
            ->where('tenant_id', $user->tenant_id)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->where('matricule', $matricule)
            ->first();

        if (! $eleve) {
            return response()->json(['error' => 'Aucun élève trouvé avec ce matricule.'], 404);
        }

        // Frais correspondant au niveau de l'élève (année courante)
        $frais = FraisScolarite::where('tenant_id', $user->tenant_id)
            ->where('niveau_id', $eleve->niveau_id)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->latest()
            ->first();

        // Historique des paiements de l'élève
        $scolarite = Scolarite::where('tenant_id', $user->tenant_id)
            ->where('eleve_id', $eleve->id)
            ->latest()
            ->first();

        $versements = $scolarite
            ? Versement::with('scolarite')->where('scolarite_id', $scolarite->id)->latest()->get()
            : collect();

        return response()->json([
            'eleve' => [
                'id' => $eleve->id,
                'matricule' => $eleve->matricule,
                'nom' => $eleve->nom,
                'prenom' => $eleve->prenom,
                'photo' => $eleve->photo_url,
                'classe' => $eleve->classe?->nom,
                'niveau' => $eleve->niveau?->nom ?? $eleve->classe?->niveau?->nom,
                'etablissement' => $eleve->etablissement?->nom,
            ],
            'frais' => $frais ? [
                'inscription' => (int) $frais->inscription,
                'scolarite' => (int) $frais->scolarite,
                'autres_frais' => (int) $frais->autres_frais,
                'montant_total' => $frais->montant_total,
            ] : null,
            'scolarite' => $scolarite ? [
                'montant_total' => (int) $scolarite->montant_total,
                'montant_paye' => (int) $scolarite->montant_paye,
                'reste' => (int) $scolarite->reste,
                'statut' => $scolarite->statut,
            ] : null,
            'versements' => $versements->map(fn ($v) => [
                'reference' => $v->reference,
                'montant' => (int) $v->montant,
                'date' => $v->date_versement?->format('d/m/Y') ?? $v->created_at?->format('d/m/Y'),
                'methode' => $v->methode,
            ]),
        ]);
    }

    /**
     * Enregistre un paiement par matricule, basé sur les frais configurés.
     */
    public function storeScolarite(Request $request, NotificationService $notifications, PaymentProviderService $paymentProvider)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'matricule' => ['required', 'string', 'max:255'],
            'montant_versement' => ['nullable', 'integer', 'min:1'],
            'annee_scolaire' => ['nullable', 'string', 'max:100'],
            'date_versement' => ['nullable', 'date'],
            'methode' => ['nullable', 'string', 'max:50'],
        ]);

        $eleve = Eleve::with('niveau')
            ->where('tenant_id', $user->tenant_id)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->where('matricule', $validated['matricule'])
            ->firstOrFail();

        // Frais configurés pour le niveau de l'élève
        $frais = FraisScolarite::where('tenant_id', $user->tenant_id)
            ->where('niveau_id', $eleve->niveau_id)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->latest()
            ->first();

        if (! $frais) {
            return back()->with('error', 'Aucun frais configuré pour le niveau de cet élève. Configurez d\'abord les frais.');
        }

        $montantTotal = $frais->montant_total;
        $versementMontant = $validated['montant_versement'] ?? $montantTotal;

        DB::transaction(function () use ($validated, $user, $eleve, $frais, $montantTotal, $versementMontant, $notifications, $paymentProvider) {
            $scolarite = Scolarite::firstOrCreate(
                [
                    'tenant_id' => $user->tenant_id,
                    'eleve_id' => $eleve->id,
                    'annee_scolaire' => $validated['annee_scolaire'] ?? null,
                ],
                [
                    'montant_total' => $montantTotal,
                    'montant_paye' => 0,
                    'reste' => $montantTotal,
                    'statut' => 'impaye',
                ]
            );

            // Toujours baser le total sur les frais configurés (pas de saisie manuelle)
            $scolarite->montant_total = $montantTotal;
            $scolarite->montant_paye += $versementMontant;
            $scolarite->reste = max($scolarite->montant_total - $scolarite->montant_paye, 0);
            $scolarite->statut = $scolarite->reste === 0 ? 'paye' : 'partiel';
            $scolarite->save();

            $versement = $paymentProvider->charge([
                'tenant_id' => $user->tenant_id,
                'scolarite_id' => $scolarite->id,
                'montant' => $versementMontant,
                'date' => $validated['date_versement'] ?? now()->toDateString(),
                'methode' => $validated['methode'] ?? 'especes',
            ]);

            $recipients = User::query()->where('tenant_id', $user->tenant_id)->where('id', $eleve->parent_id)->get();
            $notifications->sendToUsers($user, $recipients, 'Paiement de scolarité enregistré', 'Un versement de '.number_format($versementMontant, 0, ',', ' ').' a été enregistré pour '.trim($eleve->nom.' '.$eleve->prenom).'.', 'payment');

            // Stocker la référence pour le reçu
            session()->flash('recu', [
                'reference' => $versement->reference,
                'eleve' => trim($eleve->nom.' '.$eleve->prenom),
                'matricule' => $eleve->matricule,
                'montant' => $versementMontant,
                'methode' => $versement->methode,
                'date' => $versement->date_versement?->format('d/m/Y'),
                'reste' => $scolarite->reste,
                'total' => $montantTotal,
            ]);
        });

        return back()->with('success', 'Paiement enregistré avec succès.')->with('showRecu', true);
    }

    /**
     * CRUD des frais de scolarité par niveau.
     */
    public function storeFrais(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'niveau_id' => ['required', 'exists:niveaux,id'],
            'inscription' => ['nullable', 'integer', 'min:0'],
            'scolarite' => ['nullable', 'integer', 'min:0'],
            'autres_frais' => ['nullable', 'integer', 'min:0'],
            'annee_academique_id' => ['nullable', 'exists:annee_academique,id'],
        ]);

        FraisScolarite::updateOrCreate(
            [
                'tenant_id' => $user->tenant_id,
                'etablissement_id' => $user->etablissement_id,
                'niveau_id' => $validated['niveau_id'],
                'annee_academique_id' => $validated['annee_academique_id'] ?? null,
            ],
            [
                'inscription' => $validated['inscription'] ?? 0,
                'scolarite' => $validated['scolarite'] ?? 0,
                'autres_frais' => $validated['autres_frais'] ?? 0,
            ]
        );

        return back()->with('success', 'Frais de scolarité enregistrés avec succès.');
    }

    public function updateFrais(Request $request, FraisScolarite $frais)
    {
        $this->authorizeFrais($frais);

        $validated = $request->validate([
            'inscription' => ['nullable', 'integer', 'min:0'],
            'scolarite' => ['nullable', 'integer', 'min:0'],
            'autres_frais' => ['nullable', 'integer', 'min:0'],
        ]);

        $frais->update([
            'inscription' => $validated['inscription'] ?? 0,
            'scolarite' => $validated['scolarite'] ?? 0,
            'autres_frais' => $validated['autres_frais'] ?? 0,
        ]);

        return back()->with('success', 'Frais de scolarité mis à jour.');
    }

    public function destroyFrais(FraisScolarite $frais)
    {
        $this->authorizeFrais($frais);
        $frais->delete();

        return back()->with('success', 'Frais de scolarité supprimé.');
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

private function authorizeFrais(FraisScolarite $frais): void
    {
        abort_unless($frais->tenant_id === auth()->user()->tenant_id, 403);
    }
}
