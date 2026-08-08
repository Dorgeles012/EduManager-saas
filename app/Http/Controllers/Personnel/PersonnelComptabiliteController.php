<?php

namespace App\Http\Controllers\Personnel;

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
use App\Services\ScolariteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersonnelComptabiliteController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $versements = Versement::with('scolarite.eleve.classe')
            ->where('tenant_id', $user->tenant_id)
            ->when($user->etablissement_id, fn ($q) => $q->whereHas('scolarite.eleve', fn ($sq) => $sq->where('etablissement_id', $user->etablissement_id)))
            ->latest()
            ->get();

        $expenses = Depense::query()
            ->where('tenant_id', $user->tenant_id)
            ->latest('id_depense')
            ->get();

        $totalIncome = $versements->sum('montant');
        $totalExpense = $expenses->sum('montant');

        return view('personnel.comptabilite.index', [
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
            'paymentMethods' => PaymentProviderService::METHODS,
            'currentYear' => now()->year . '-' . now()->addYear()->year,
        ]);
    }

    /**
     * Recherche un élève par matricule (AJAX). Respecte tenant + établissement.
     */
    public function searchByMatricule(Request $request, ScolariteService $scolariteService)
    {
        $user = auth()->user();
        $matricule = trim((string) $request->input('matricule'));

        if ($matricule === '') {
            return response()->json(['error' => 'Veuillez saisir un matricule.'], 422);
        }

        $eleve = Eleve::with(['niveau', 'classe', 'etablissement'])
            ->where('tenant_id', $user->tenant_id)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->where('matricule', $matricule)
            ->first();

        if (! $eleve) {
            return response()->json(['error' => 'Aucun élève trouvé avec ce matricule.'], 404);
        }

        $situation = $scolariteService->situation($eleve);

        return response()->json([
            'eleve' => [
                'id' => $eleve->id,
                'matricule' => $eleve->matricule,
                'nom' => $eleve->nom,
                'prenom' => $eleve->prenom,
                'photo' => $eleve->photo_url,
                'classe' => $eleve->classe?->nom,
                'niveau' => $situation['niveau']?->nom ?? $eleve->classe?->niveau?->nom,
                'etablissement' => $eleve->etablissement?->nom,
            ],
            'frais' => $situation['frais'] ? [
                'inscription' => (int) $situation['frais']->inscription,
                'scolarite' => (int) $situation['frais']->scolarite,
                'autres_frais' => (int) $situation['frais']->autres_frais,
                'montant_total' => (int) $situation['frais']->montant_total,
            ] : null,
            'scolarite' => [
                'montant_total' => $situation['montant_total'],
                'montant_paye' => $situation['montant_paye'],
                'reste' => $situation['reste'],
                'statut' => $situation['statut'],
            ],
            'versements' => $situation['versements']->map(fn ($v) => [
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
    public function storeScolarite(Request $request, NotificationService $notifications, PaymentProviderService $paymentProvider, ScolariteService $scolariteService)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'matricule' => ['required', 'string', 'max:255'],
            'montant_versement' => ['nullable', 'integer', 'min:1'],
            'annee_scolaire' => ['nullable', 'string', 'max:100'],
            'date_versement' => ['nullable', 'date'],
            'methode' => ['nullable', 'string', 'max:50'],
        ]);

        $eleve = Eleve::with('niveau', 'classe')
            ->where('tenant_id', $user->tenant_id)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->where('matricule', $validated['matricule'])
            ->first();

        if (! $eleve) {
            return back()->with('error', 'Élève introuvable avec ce matricule.');
        }

        $frais = $scolariteService->fraisPour($eleve);

        if (! $frais) {
            return back()->with('error', 'Aucun frais configuré pour le niveau de cet élève. Le client doit d\'abord configurer les frais.');
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

