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

        $versements = Versement::with(['scolarite.eleve.classe', 'enregistrePar'])
            ->where('tenant_id', $user->tenant_id)
            ->when($user->etablissement_id, fn ($q) => $q->whereHas('scolarite.eleve', fn ($sq) => $sq->where('etablissement_id', $user->etablissement_id)))
            ->latest()
            ->get();

        $expenses = Depense::query()
            ->where('tenant_id', $user->tenant_id)
            ->latest('id_depense')
            ->get();

        $validVersements = $versements->where('statut', 'valide');
        $totalIncome = $validVersements->sum('montant');
        $totalExpense = $expenses->sum('montant');

        return view('personnel.comptabilite.index', [
            'payments' => $versements->map(fn ($versement) => [
                'student' => trim(($versement->scolarite?->eleve?->nom ?? '') . ' ' . ($versement->scolarite?->eleve?->prenom ?? '')) ?: 'N/A',
                'class' => $versement->scolarite?->eleve?->classe?->nom ?? 'N/A',
                'amount' => $versement->montant,
                'date' => $versement->date_versement?->format('d/m/Y') ?? $versement->created_at?->format('d/m/Y'),
                'matricule' => $versement->scolarite?->eleve?->matricule,
                'method' => $versement->methode,
                'reference' => $versement->reference,
                'type' => $versement->type,
                'status' => $versement->statut,
                'recorded_by' => trim(($versement->enregistrePar?->nom ?? '') . ' ' . ($versement->enregistrePar?->prenom ?? '')),
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
            'paymentCount' => $validVersements->count(),
            'registrationIncome' => $validVersements->where('type', 'inscription')->sum('montant'),
            'tuitionIncome' => $validVersements->where('type', 'scolarite')->sum('montant'),
            'paymentMethods' => PaymentProviderService::METHODS,
            'currentYear' => now()->year . '-' . now()->addYear()->year,
        ]);
    }

    /**
     * Recherche un Ã©lÃ¨ve par matricule (AJAX). Respecte tenant + Ã©tablissement.
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
            return response()->json(['error' => 'Aucun Ã©lÃ¨ve trouvÃ© avec ce matricule.'], 404);
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
                'type' => $v->type,
                'statut' => $v->statut,
                'enregistre_par' => trim(($v->enregistrePar?->nom ?? '') . ' ' . ($v->enregistrePar?->prenom ?? '')),
            ]),
        ]);
    }

    /**
     * Enregistre un paiement par matricule, basÃ© sur les frais configurÃ©s.
     */
    public function storeScolarite(Request $request, NotificationService $notifications, PaymentProviderService $paymentProvider, ScolariteService $scolariteService)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'matricule' => ['required', 'string', 'max:255'],
            'montant_versement' => ['required', 'integer', 'min:1'],
            'annee_scolaire' => ['nullable', 'string', 'max:100'],
            'date_versement' => ['nullable', 'date'],
            'methode' => ['required', 'string', 'in:' . implode(',', array_keys(PaymentProviderService::METHODS))],
            'type' => ['required', 'in:inscription,scolarite'],
            'idempotency_key' => ['required', 'uuid'],
        ]);

        $eleve = Eleve::with('niveau', 'classe')
            ->where('tenant_id', $user->tenant_id)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->where('matricule', $validated['matricule'])
            ->first();

        if (! $eleve) {
            return back()->with('error', 'Ã‰lÃ¨ve introuvable avec ce matricule.');
        }

        $frais = $scolariteService->fraisPour($eleve);

        if (! $frais) {
            return back()->with('error', 'Aucun frais configurÃ© pour le niveau de cet Ã©lÃ¨ve. Le client doit d\'abord configurer les frais.');
        }

        if (Versement::where('idempotency_key', $validated['idempotency_key'])->exists()) {
            return back()->with('success', 'Cette operation de paiement a deja ete enregistree.');
        }

        $montantTotal = (int) $frais->montant_total;
        $versementMontant = (int) $validated['montant_versement'];
        $anneeScolaire = $frais->anneeAcademique()->value('libelle') ?? $validated['annee_scolaire'] ?? null;

        DB::transaction(function () use ($validated, $user, $eleve, $frais, $montantTotal, $versementMontant, $anneeScolaire, $notifications, $paymentProvider) {
            Eleve::whereKey($eleve->id)->lockForUpdate()->firstOrFail();
            if (Versement::where('idempotency_key', $validated['idempotency_key'])->exists()) {
                return;
            }
            $baseQuery = Scolarite::where('tenant_id', $user->tenant_id)->where('eleve_id', $eleve->id);
            $scolarite = $anneeScolaire
                ? (clone $baseQuery)->where('annee_scolaire', $anneeScolaire)->lockForUpdate()->first()
                : (clone $baseQuery)->whereNull('annee_scolaire')->lockForUpdate()->first();
            if (! $scolarite) {
                $scolarite = Scolarite::create([
                    'tenant_id' => $user->tenant_id,
                    'eleve_id' => $eleve->id,
                    'annee_scolaire' => $anneeScolaire,
                    'montant_total' => $montantTotal,
                    'montant_paye' => 0,
                    'reste' => $montantTotal,
                    'statut' => 'impaye',
                ]);
            }

            $scolarite->montant_total = $montantTotal;
            $alreadyPaid = (int) Versement::where('tenant_id', $user->tenant_id)->where('scolarite_id', $scolarite->id)->where('statut', 'valide')->sum('montant');
            if ($validated['type'] === 'inscription') {
                $registrationPaid = (int) Versement::where('tenant_id', $user->tenant_id)->where('scolarite_id', $scolarite->id)->where('statut', 'valide')->where('type', 'inscription')->sum('montant');
                $registrationRemaining = max((int) $frais->inscription - $registrationPaid, 0);
                if ($versementMontant > $registrationRemaining) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'montant_versement' => 'Le paiement dÃ©passe les frais dâ€™inscription restants (' . number_format($registrationRemaining, 0, ',', ' ') . ' FCFA).',
                    ]);
                }
            }
            $remaining = max($montantTotal - $alreadyPaid, 0);
            if ($versementMontant > $remaining) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'montant_versement' => 'Le paiement depasse le solde du de ' . number_format($remaining, 0, ',', ' ') . ' FCFA.',
                ]);
            }

            $versement = $paymentProvider->charge([
                'tenant_id' => $user->tenant_id,
                'scolarite_id' => $scolarite->id,
                'montant' => $versementMontant,
                'date' => $validated['date_versement'] ?? now()->toDateString(),
                'methode' => $validated['methode'],
                'type' => $validated['type'],
                'enregistre_par' => $user->id,
                'idempotency_key' => $validated['idempotency_key'],
            ]);

            $scolarite->montant_paye = (int) Versement::where('tenant_id', $user->tenant_id)->where('scolarite_id', $scolarite->id)->where('statut', 'valide')->sum('montant');
            $scolarite->reste = max($montantTotal - $scolarite->montant_paye, 0);
            $scolarite->statut = $scolarite->reste === 0 ? 'paye' : 'partiel';
            $scolarite->save();

            $recipients = User::query()->where('tenant_id', $user->tenant_id)->where('id', $eleve->parent_id)->get();
            $notifications->sendToUsers($user, $recipients, 'Paiement de scolaritÃ© enregistrÃ©', 'Un versement de '.number_format($versementMontant, 0, ',', ' ').' a Ã©tÃ© enregistrÃ© pour '.trim($eleve->nom.' '.$eleve->prenom).'.', 'payment');

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

        return back()->with('success', 'Paiement enregistrÃ© avec succÃ¨s.');
    }

    public function storeDepense(Request $request)
    {
        $validated = $this->validateDepense($request);

        Depense::create($validated + ['tenant_id' => auth()->user()->tenant_id]);

        return back()->with('success', 'DÃ©pense enregistrÃ©e avec succÃ¨s.');
    }

    public function updateDepense(Request $request, Depense $depense)
    {
        $this->authorizeDepense($depense);
        $depense->update($this->validateDepense($request));

        return back()->with('success', 'DÃ©pense mise Ã  jour avec succÃ¨s.');
    }

    public function destroyDepense(Depense $depense)
    {
        $this->authorizeDepense($depense);
        $depense->delete();

        return back()->with('success', 'DÃ©pense supprimÃ©e avec succÃ¨s.');
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
