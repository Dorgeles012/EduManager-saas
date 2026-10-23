<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AnneeAcademique;
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

class ComptabiliteController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $versements = Versement::with(['scolarite.eleve.classe', 'enregistrePar'])
            ->where('tenant_id', $user->tenant_id)
            ->whereHas('scolarite.eleve', fn ($q) => $q->where('tenant_id', $user->tenant_id)
                ->when($user->etablissement_id, fn ($schoolQuery) => $schoolQuery->where('etablissement_id', $user->etablissement_id)))
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

        $anneesAcademiques = AnneeAcademique::query()
            ->where('tenant_id', $user->tenant_id)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->orderByDesc('date_debut')
            ->get(['id', 'libelle', 'statut']);
        $activeYear = AnneeAcademique::query()
            ->where('tenant_id', $user->tenant_id)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->where('statut', 'active')
            ->orderByDesc('date_debut')
            ->first();
        $activeYearLabel = $activeYear?->libelle;

        $validVersements = $versements->where('statut', 'valide');
        $totalIncome = $validVersements->sum('montant');
        $registrationIncome = $validVersements->where('type', 'inscription')->sum('montant');
        $tuitionIncome = $validVersements->where('type', 'scolarite')->sum('montant');
        $totalExpense = $expenses->sum('montant');
        $openBalances = Scolarite::query()
            ->withSum(['versements as total_paye_valide' => fn ($q) => $q->where('statut', 'valide')], 'montant')
            ->where('tenant_id', $user->tenant_id)
            ->whereHas('eleve', fn ($q) => $q->where('tenant_id', $user->tenant_id)
                ->when($user->etablissement_id, fn ($schoolQuery) => $schoolQuery->where('etablissement_id', $user->etablissement_id)))
            ->get();
        $totalOutstanding = $openBalances->sum(fn ($item) => max((int) $item->montant_total - (int) $item->total_paye_valide, 0));
        if ($activeYear) {
            $activeFeesByLevel = FraisScolarite::query()
                ->where('tenant_id', $user->tenant_id)
                ->where('annee_academique_id', $activeYear->id)
                ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
                ->get()
                ->keyBy('niveau_id');
            $studentsWithoutCurrentLedger = Eleve::query()
                ->where('tenant_id', $user->tenant_id)
                ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
                ->whereDoesntHave('scolarites', fn ($q) => $q->where('tenant_id', $user->tenant_id)
                    ->where(fn ($yearQuery) => $yearQuery->where('annee_scolaire', $activeYear->libelle)->orWhereNull('annee_scolaire')))
                ->get(['niveau_id']);
            $totalOutstanding += $studentsWithoutCurrentLedger->sum(
                fn ($student) => (int) ($activeFeesByLevel->get($student->niveau_id)?->montant_total ?? 0)
            );
        }

        $paidByDebt = [];
        $remainingAfterByPayment = [];
        foreach ($validVersements->sortBy(fn ($payment) => ($payment->date_versement?->timestamp ?? $payment->created_at?->timestamp ?? 0) * 100000 + $payment->id) as $payment) {
            $paidByDebt[$payment->scolarite_id] = ($paidByDebt[$payment->scolarite_id] ?? 0) + (int) $payment->montant;
            $debtTotal = (int) ($payment->scolarite?->montant_total ?? 0);
            $remainingAfterByPayment[$payment->id] = max($debtTotal - $paidByDebt[$payment->scolarite_id], 0);
        }

        return view('client.comptabilite', [
            'payments' => $versements->map(fn ($versement) => [
                'student' => trim(($versement->scolarite?->eleve?->nom ?? '') . ' ' . ($versement->scolarite?->eleve?->prenom ?? '')) ?: 'N/A',
                'class' => $versement->scolarite?->eleve?->classe?->nom ?? 'N/A',
                'amount' => $versement->montant,
                'date' => $versement->date_versement?->format('d/m/Y') ?? $versement->created_at?->format('d/m/Y'),
                'method' => $versement->methode,
                'reference' => $versement->reference,
                'matricule' => $versement->scolarite?->eleve?->matricule,
                'type' => $versement->type,
                'status' => $versement->statut,
                'recorded_by' => trim(($versement->enregistrePar?->nom ?? '') . ' ' . ($versement->enregistrePar?->prenom ?? '')),
                'remaining' => $remainingAfterByPayment[$versement->id] ?? null,
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
            'registrationIncome' => $registrationIncome,
            'tuitionIncome' => $tuitionIncome,
            'totalOutstanding' => $totalOutstanding,
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
            'anneesAcademiques' => $anneesAcademiques,
            'paymentMethods' => PaymentProviderService::METHODS,
            'currentYear' => $activeYearLabel ?? (now()->year . '-' . now()->addYear()->year),
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
        $service = app(ScolariteService::class);
        $frais = $service->fraisPour($eleve);

        // Historique des paiements de l'élève
        $scolarite = Scolarite::where('tenant_id', $user->tenant_id)
            ->where('eleve_id', $eleve->id)
            ->latest()
            ->first();

        $scolarites = Scolarite::where('tenant_id', $user->tenant_id)->where('eleve_id', $eleve->id)->get();
        $versements = Versement::with(['scolarite', 'enregistrePar'])
            ->where('tenant_id', $user->tenant_id)
            ->whereIn('scolarite_id', $scolarites->pluck('id'))
            ->latest()
            ->get();
        $validVersements = $versements->where('statut', 'valide');
        $montantPaye = (int) $validVersements->sum('montant');
        $montantDu = $scolarites->isNotEmpty()
            ? (int) $scolarites->sum('montant_total')
            : (int) ($frais?->montant_total ?? 0);
        $reste = $scolarites->isNotEmpty()
            ? $scolarites->sum(fn ($item) => max((int) $item->montant_total - (int) $validVersements->where('scolarite_id', $item->id)->sum('montant'), 0))
            : $montantDu;
        $currentYearLabel = $frais?->anneeAcademique()->value('libelle');
        $hasCurrentLedger = $currentYearLabel
            ? $scolarites->contains(fn ($item) => $item->annee_scolaire === $currentYearLabel || $item->annee_scolaire === null)
            : $scolarites->isNotEmpty();
        if (! $hasCurrentLedger && $frais) {
            $montantDu += (int) $frais->montant_total;
            $reste += (int) $frais->montant_total;
        }

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
            'scolarite' => [
                'montant_total' => $montantDu,
                'montant_paye' => $montantPaye,
                'reste' => $reste,
                'statut' => $reste === 0 && $montantDu > 0 ? 'solde' : ($montantPaye > 0 ? 'partiel' : 'impaye'),
            ],
            'versements' => $versements->map(fn ($v) => [
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
     * Enregistre un paiement par matricule, basé sur les frais configurés.
     */
    public function storeScolarite(Request $request, NotificationService $notifications, PaymentProviderService $paymentProvider)
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

        $eleve = Eleve::with('niveau')
            ->where('tenant_id', $user->tenant_id)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->where('matricule', $validated['matricule'])
            ->firstOrFail();

        // Frais configurés pour le niveau de l'élève
        if (Versement::where('idempotency_key', $validated['idempotency_key'])->exists()) {
            return back()->with('success', 'Cette opération de paiement a déjà été enregistrée.');
        }

        $frais = app(ScolariteService::class)->fraisPour($eleve);

        if (! $frais) {
            return back()->with('error', 'Aucun frais configuré pour le niveau de cet élève. Configurez d\'abord les frais.');
        }

        $montantTotal = (int) $frais->montant_total;
        $versementMontant = (int) $validated['montant_versement'];
        $anneeScolaire = $frais->anneeAcademique()->value('libelle') ?? $validated['annee_scolaire'] ?? null;

        DB::transaction(function () use ($validated, $user, $eleve, $frais, $montantTotal, $versementMontant, $anneeScolaire, $notifications, $paymentProvider) {
            Eleve::whereKey($eleve->id)->lockForUpdate()->firstOrFail();
            if (Versement::where('idempotency_key', $validated['idempotency_key'])->exists()) {
                return;
            }
            $query = Scolarite::where('tenant_id', $user->tenant_id)->where('eleve_id', $eleve->id);
            $scolarite = $anneeScolaire
                ? (clone $query)->where('annee_scolaire', $anneeScolaire)->lockForUpdate()->first()
                : (clone $query)->whereNull('annee_scolaire')->lockForUpdate()->first();
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

            // Toujours baser le total sur les frais configurés (pas de saisie manuelle)
            $scolarite->montant_total = $montantTotal;
            $dejaPaye = (int) Versement::where('tenant_id', $user->tenant_id)
                ->where('scolarite_id', $scolarite->id)
                ->where('statut', 'valide')
                ->sum('montant');
            if ($validated['type'] === 'inscription') {
                $inscriptionDejaPayee = (int) Versement::where('tenant_id', $user->tenant_id)
                    ->where('scolarite_id', $scolarite->id)->where('statut', 'valide')->where('type', 'inscription')->sum('montant');
                $inscriptionRestante = max((int) $frais->inscription - $inscriptionDejaPayee, 0);
                if ($versementMontant > $inscriptionRestante) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'montant_versement' => 'Le paiement dépasse les frais d’inscription restants (' . number_format($inscriptionRestante, 0, ',', ' ') . ' FCFA).',
                    ]);
                }
            }
            $resteAvantPaiement = max($montantTotal - $dejaPaye, 0);
            if ($versementMontant > $resteAvantPaiement) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'montant_versement' => 'Le paiement dépasse le solde dû de ' . number_format($resteAvantPaiement, 0, ',', ' ') . ' FCFA.',
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

            $scolarite->montant_paye = (int) Versement::where('tenant_id', $user->tenant_id)
                ->where('scolarite_id', $scolarite->id)
                ->where('statut', 'valide')
                ->sum('montant');
            $scolarite->reste = max($montantTotal - $scolarite->montant_paye, 0);
            $scolarite->statut = $scolarite->reste === 0 ? 'paye' : 'partiel';
            $scolarite->save();

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
