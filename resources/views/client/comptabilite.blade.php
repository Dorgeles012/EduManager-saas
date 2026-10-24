@extends('client.layouts.app')
@section('title', 'EduManager - Comptabilité')
@section('content')
<!-- Header Actions & Welcome -->
<div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Système de Comptabilité</h2>
        <p class="text-sm text-gray-500 mt-1">Gérez les finances de votre établissement avec précision et clarté.</p>
    </div>
    <div class="relative" id="actionsDropdownWrapper">
        <button type="button"
                id="actionsDropdownBtn"
                onclick="toggleActionsDropdown(event)"
                class="flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg font-label-md shadow-lg hover:brightness-110 active:scale-95 transition-all">
    
            <span class="material-symbols-outlined text-[20px] transition-transform duration-200" id="actionsDropdownIcon">add</span><span>Faire une action</span>
        </button>

        <!-- Menu déroulant -->
        <div id="actionsDropdownMenu"
             class="hidden absolute right-0 mt-2 w-48 bg-surface-container-lowest rounded-xl border border-outline-variant shadow-2xl overflow-hidden z-50">
            <button type="button"
                    class="w-full flex items-center gap-3 px-4 py-3 text-left font-label-md text-on-surface hover:bg-surface-container-low transition-colors"
                    onclick="openActionModal('modalScolarite')">
                <span class="material-symbols-outlined text-[20px] text-success-green">add_circle</span>
                Paiement
            </button>
            <button type="button"
                    class="w-full flex items-center gap-3 px-4 py-3 text-left font-label-md text-on-surface hover:bg-surface-container-low transition-colors"
                    onclick="openActionModal('modalDepense')">
                <span class="material-symbols-outlined text-[20px] text-alert-red">payments</span>
                Dépense
            </button>
            <button type="button"
                    class="w-full flex items-center gap-3 px-4 py-3 text-left font-label-md text-on-surface hover:bg-surface-container-low transition-colors"
                    onclick="openActionModal('modalFrais')">
                <span class="material-symbols-outlined text-[20px] text-primary">tune</span>
                Frais
            </button>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant"><p class="text-label-sm text-on-surface-variant">Solde actuel</p><p class="font-headline-md text-headline-md text-primary mt-1">{{ number_format($currentBalance ?? 0, 0, ',', ' ') }} FCFA</p></div>
    <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant"><p class="text-label-sm text-on-surface-variant">Total dépensé</p><p class="font-headline-md text-headline-md text-alert-red mt-1">{{ number_format($totalExpense ?? 0, 0, ',', ' ') }} FCFA</p></div>
    
</div>
<!-- Recherche par matricule -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-outline-variant flex items-center gap-3">
        <span class="material-symbols-outlined text-primary">search</span>
        <h4 class="font-headline-md text-headline-md">Rechercher un élève</h4>
    </div>
    <div class="p-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" id="matriculeSearch" placeholder="Saisissez le matricule..." class="w-full rounded-lg border border-outline-variant focus:border-primary focus:ring-primary text-body-sm px-4 py-2.5">
            </div>
            <button class="bg-primary text-on-primary px-6 py-2.5 rounded-lg font-label-md hover:bg-primary/90 transition-colors" onclick="searchEleve()">
                Rechercher
            </button>
        </div>

        <!-- Résultat de la recherche -->
        <div id="eleveResult" class="hidden mt-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Infos élève -->
                <div class="bg-surface-container-low rounded-xl p-5">
                    <div class="flex items-center gap-4 mb-4">
                        <img id="resultPhoto" src="" alt="Photo" class="w-16 h-16 rounded-full object-cover border border-outline-variant hidden">
                        <div>
                            <p id="resultNom" class="font-headline-md text-headline-md text-on-surface"></p>
                            <p id="resultMatricule" class="text-sm text-on-surface-variant"></p>
                            <p id="resultClasse" class="text-sm text-on-surface-variant"></p>
                            <p id="resultNiveau" class="text-sm text-on-surface-variant"></p>
                        </div>
                    </div>
                    <div class="bg-surface-container-lowest rounded-lg p-4 border border-outline-variant">
                        <p class="text-label-sm text-on-surface-variant uppercase tracking-wider mb-2">Frais du niveau</p>
                        <div class="flex justify-between text-sm"><span>Inscription</span><span id="fraisInscription" class="font-semibold"></span></div>
                        <div class="flex justify-between text-sm"><span>Scolarité</span><span id="fraisScolarite" class="font-semibold"></span></div>
                        <div class="flex justify-between text-sm"><span>Autres</span><span id="fraisAutres" class="font-semibold"></span></div>
                        <div class="flex justify-between text-sm font-bold border-t border-outline-variant mt-2 pt-2"><span>Total</span><span id="fraisTotal" class="text-primary"></span></div>
                    </div>
                </div>

                <!-- Historique des paiements -->
                <div class="bg-surface-container-low rounded-xl p-5">
                    <h5 class="font-headline-md text-headline-md mb-3">Historique</h5>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="border-b border-outline-variant text-on-surface-variant">
                                <tr>
                                    <th class="px-3 py-2 text-left">Date</th>
                                    <th class="px-3 py-2 text-left">Type</th>
                                    <th class="px-3 py-2 text-right">Montant</th>
                                    <th class="px-3 py-2 text-left">Référence</th>
                                    <th class="px-3 py-2 text-left">Statut</th>
                                    <th class="px-3 py-2 text-left">Enregistré par</th>
                                    <th class="px-3 py-2 text-left">Méthode</th>
                                </tr>
                            </thead>
                            <tbody id="versementsList"></tbody>
                        </table>
                    </div>
                    <div class="mt-3 text-sm bg-surface-container-lowest rounded-lg p-3 border border-outline-variant">
                        <div class="flex justify-between"><span>Total dû</span><span id="scolariteTotal" class="font-semibold"></span></div>
                        <div class="flex justify-between"><span>Total payé</span><span id="scolaritePaye" class="font-semibold text-success-green"></span></div>
                        <div class="flex justify-between"><span>Reste</span><span id="scolariteReste" class="font-semibold text-alert-red"></span></div>
                        <div class="flex justify-between"><span>Statut</span><span id="scolariteStatut" class="font-semibold"></span></div>
                    </div>
                </div>
            </div>
        </div>

        <div id="eleveError" class="hidden mt-4 rounded-lg border border-alert-red/20 bg-alert-red/10 px-4 py-3 text-alert-red"></div>
    </div>
</div>

<!-- Tables -->
<div class="space-y-8">
    <!-- Scolarités -->
    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden">
        <div class="px-6 py-4 border-b border-outline-variant">
            <h4 class="font-headline-md text-headline-md">Scolarités</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container-low">
                    <tr>
                        <th class="px-6 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant">Élève</th>
                        <th class="px-6 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant">Classe</th>
                        <th class="px-6 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant">Matricule</th>
                        <th class="px-6 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant">Type</th>
                        <th class="px-6 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant">Montant</th>
                        <th class="px-6 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant">Méthode</th>
                        <th class="px-6 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant">Référence</th>
                        <th class="px-6 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant">Statut</th>
                        <th class="px-6 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant">Enregistré par</th>
                        <th class="px-6 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant">Solde après paiement</th>
                        <th class="px-6 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant text-right">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse($payments ?? [] as $payment)
                    <tr class="hover:bg-surface-container-low/30 transition-colors">
                        <td class="px-6 py-4 font-body-md text-body-md text-on-surface">{{ $payment['student'] }}</td>
                        <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $payment['class'] }}</td>
                        <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $payment['matricule'] ?? '—' }}</td>
                        <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ ($payment['type'] ?? null) === 'inscription' ? 'Inscription' : (($payment['type'] ?? null) === 'scolarite' ? 'Scolarité' : 'Non classé') }}</td>
                        <td class="px-6 py-4 font-body-md font-semibold text-success-green">{{ number_format($payment['amount'], 0, ',', ' ') }} FCFA</td>
                        <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $payment['method'] ?? '—' }}</td>
                        <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $payment['reference'] ?? '—' }}</td>
                        <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $payment['status'] ?? 'À vérifier' }}</td>
                        <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $payment['recorded_by'] ?? '—' }}</td>
                        <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ isset($payment['remaining']) ? number_format($payment['remaining'], 0, ',', ' ') . ' FCFA' : '—' }}</td>
                        <td class="px-6 py-4 text-body-sm text-on-surface-variant text-right">{{ $payment['date'] }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="11" class="px-6 py-8 text-center text-on-surface-variant">Aucune scolarité</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-surface-container-low font-bold">
                    <tr>
                        <td class="px-6 py-4" colspan="2">Total</td>
                        <td class="px-6 py-4 text-success-green text-headline-md" colspan="9">{{ number_format($totalIncome ?? 0, 0, ',', ' ') }} FCFA</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </section>

    <!-- Dépenses -->
    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden">
        <div class="px-6 py-4 border-b border-outline-variant">
            <h4 class="font-headline-md text-headline-md">Dépenses</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container-low">
                    <tr>
                        <th class="px-6 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant">Libellé</th>
                        <th class="px-6 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant">Montant</th>
                        <th class="px-6 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant text-right">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse($expenses ?? [
                        ['label' => 'Achat Fournitures Bureautiques', 'amount' => 12171200, 'date' => '12/10/2023']
                    ] as $expense)
                    <tr class="hover:bg-surface-container-low/30 transition-colors">
                        <td class="px-6 py-4 font-body-md text-body-md text-on-surface">{{ $expense['label'] }}</td>
                        <td class="px-6 py-4 font-body-md font-semibold text-alert-red">{{ number_format($expense['amount'], 0, ',', ' ') }} FCFA</td>
                        <td class="px-6 py-4 text-body-sm text-on-surface-variant text-right">{{ $expense['date'] }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-6 py-8 text-center text-on-surface-variant">Aucune dépense</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-surface-container-low font-bold">
                    <tr>
                        <td class="px-6 py-4">Total</td>
                        <td class="px-6 py-4 text-alert-red text-headline-md" colspan="2">{{ number_format($totalExpense ?? 0, 0, ',', ' ') }} FCFA</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </section>
</div>

<!-- Modals (same as before, kept for functionality) -->
<!-- Modal Scolarité -->
<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="modalScolarite">
    <div class="absolute inset-0 modal-overlay backdrop-blur-md bg-black/30" onclick="closeModal('modalScolarite')"></div>
    <div class="bg-surface-container-lowest w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="modalScolariteContent">
        <div class="p-6 border-b border-outline-variant flex justify-between items-center bg-success-green text-white">
            <h3 class="font-headline-md text-headline-md">Enregistrer un paiement</h3>
            <button class="text-white/80 hover:text-white transition-colors" onclick="closeModal('modalScolarite')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        @if(session('showRecu'))
            <div class="p-6 border-b border-outline-variant bg-primary-fixed/20">
                <p class="text-sm text-on-surface mb-3">Paiement enregistré.</p>
                <a href="{{ route('client.comptabilite.recu') }}" target="_blank" class="inline-flex items-center gap-2 bg-primary text-on-primary px-4 py-2 rounded-lg font-label-md">
                    <span class="material-symbols-outlined text-[18px]">print</span>
                    Imprimer le reçu
                </a>
            </div>
        @endif
        <form class="p-6 space-y-4" action="{{ route('client.comptabilite.scolarite.store') }}" method="POST">
            @csrf
            <input type="hidden" name="idempotency_key" value="{{ (string) Illuminate\Support\Str::uuid() }}">
            <input type="hidden" name="annee_scolaire" value="{{ $currentYear ?? '2024-2025' }}">
            <div>
                <label class="block text-label-sm text-on-surface-variant mb-1.5">Matricule</label>
                <div class="flex gap-2">
                    <input class="w-full rounded-lg border-outline-variant focus:border-success-green focus:ring-success-green text-body-sm" id="paymentMatricule" name="matricule" placeholder="Saisissez le matricule" required>
                    <button type="button" class="px-4 py-2 bg-surface-container-high text-on-surface rounded-lg shrink-0" onclick="searchEleveForPayment()">Vérifier</button>
                </div>
                <p id="paymentEleveInfo" class="text-sm text-on-surface-variant mt-2"></p>
            </div>
            <div>
                <label class="block text-label-sm text-on-surface-variant mb-1.5">Catégorie de paiement</label>
                <select class="w-full rounded-lg border-outline-variant focus:border-success-green focus:ring-success-green text-body-sm" name="type" required>
                    <option value="inscription">Inscription</option>
                    <option value="scolarite">Frais de scolarité</option>
                </select>
            </div>
            <div>
                <label class="block text-label-sm text-on-surface-variant mb-1.5">Montant (FCFA)</label>
                <input class="w-full rounded-lg border-outline-variant focus:border-success-green focus:ring-success-green text-body-sm" id="amountInput" name="montant_versement" min="1" step="1" type="number" placeholder="Montant à payer" required>
            </div>
            <div>
                <label class="block text-label-sm text-on-surface-variant mb-1.5">Moyen de paiement</label>
                <select class="w-full rounded-lg border-outline-variant focus:border-success-green focus:ring-success-green text-body-sm" name="methode" required>
                    <option value="">Sélectionner</option>
                    @foreach($paymentMethods ?? [] as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-label-sm text-on-surface-variant mb-1.5">Date</label>
                <input class="w-full rounded-lg border-outline-variant focus:border-success-green focus:ring-success-green text-body-sm" name="date_versement" type="date" value="{{ now()->toDateString() }}">
            </div>
            <div class="pt-4 flex gap-3">
                <button class="flex-1 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant font-label-md hover:bg-surface-container-low transition-colors" onclick="closeModal('modalScolarite')" type="button">Annuler</button>
                <button class="flex-1 px-4 py-2.5 rounded-lg bg-success-green text-white font-label-md hover:brightness-110 shadow-lg" type="submit">Valider</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Frais -->
<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="modalFrais">
    <div class="absolute inset-0 modal-overlay backdrop-blur-md bg-black/30" onclick="closeModal('modalFrais')"></div>
    <div class="bg-surface-container-lowest w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="modalFraisContent">
        <div class="p-6 border-b border-outline-variant flex justify-between items-center bg-primary text-white">
            <h3 class="font-headline-md text-headline-md">Configurer les frais</h3>
            <button class="text-white/80 hover:text-white transition-colors" onclick="closeModal('modalFrais')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
<div class="p-6">
            <div class="mb-4">
                <label class="block text-label-sm text-on-surface-variant mb-1.5">Année académique</label>
                <select class="w-full rounded-lg border-outline-variant text-body-sm" id="fraisAnneeId" onchange="filterFraisByAnnee(this.value)">
                    @foreach($anneesAcademiques ?? [] as $annee)
                        <option value="{{ $annee->id }}" @if(($annee->statut ?? '') === 'active') selected @endif>{{ $annee->libelle }}</option>
                    @endforeach
                </select>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-surface-container-low">
                        <tr>
                            <th class="px-4 py-3 font-label-sm uppercase text-on-surface-variant">Niveau</th>
                            <th class="px-4 py-3 font-label-sm uppercase text-on-surface-variant">Inscription</th>
                            <th class="px-4 py-3 font-label-sm uppercase text-on-surface-variant">Scolarité</th>
                            <th class="px-4 py-3 font-label-sm uppercase text-on-surface-variant">Autres</th>
                            <th class="px-4 py-3 font-label-sm uppercase text-on-surface-variant">Total</th>
                            <th class="px-4 py-3 font-label-sm uppercase text-on-surface-variant text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant" id="fraisTableBody">
                        @forelse($levels ?? [] as $level)
                            @php
                                $frais = $fraisParNiveau->firstWhere('niveau_id', $level['id']);
                            @endphp
                            <tr>
                                <td class="px-4 py-3 font-label-md">{{ $level['name'] }}</td>
                                <td class="px-4 py-3">{{ $frais ? number_format((int) $frais->inscription, 0, ',', ' ') : '—' }}</td>
                                <td class="px-4 py-3">{{ $frais ? number_format((int) $frais->scolarite, 0, ',', ' ') : '—' }}</td>
                                <td class="px-4 py-3">{{ $frais ? number_format((int) $frais->autres_frais, 0, ',', ' ') : '—' }}</td>
                                <td class="px-4 py-3 font-bold text-primary">{{ $frais ? number_format((int) $frais->montant_total, 0, ',', ' ') : '—' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <button class="p-2 rounded-lg bg-surface-container-low hover:bg-surface-container-high transition-colors" onclick="openEditFrais({{ $level['id'] }}, {{ $frais?->inscription ?? 0 }}, {{ $frais?->scolarite ?? 0 }}, {{ $frais?->autres_frais ?? 0 }})">
                                        <span class="material-symbols-outlined text-[18px]">{{ $frais ? 'edit' : 'add' }}</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-6 text-center text-on-surface-variant">Aucun niveau</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <form method="POST" action="{{ route('client.comptabilite.frais.store') }}" class="mt-6 p-4 bg-surface-container-low rounded-xl space-y-4">
                @csrf
                <input type="hidden" name="niveau_id" id="fraisNiveauId">
                <input type="hidden" name="annee_academique_id" id="fraisAnneeIdHidden">
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-label-sm text-on-surface-variant mb-1.5">Inscription</label>
                        <input class="w-full rounded-lg border-outline-variant text-body-sm" name="inscription" id="fraisInscriptionInput" type="number" min="0" value="0">
                    </div>
                    <div>
                        <label class="block text-label-sm text-on-surface-variant mb-1.5">Scolarité</label>
                        <input class="w-full rounded-lg border-outline-variant text-body-sm" name="scolarite" id="fraisScolariteInput" type="number" min="0" value="0">
                    </div>
                    <div>
                        <label class="block text-label-sm text-on-surface-variant mb-1.5">Autres</label>
                        <input class="w-full rounded-lg border-outline-variant text-body-sm" name="autres_frais" id="fraisAutresInput" type="number" min="0" value="0">
                    </div>
                </div>
                <button type="submit" class="w-full px-4 py-2.5 rounded-lg bg-primary text-white font-label-md hover:bg-primary/90">Enregistrer</button>
            </form>
        </div>
    </div>
</div>

<!-- Modal Dépense -->
<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="modalDepense">
    <div class="absolute inset-0 modal-overlay backdrop-blur-md bg-black/30" onclick="closeModal('modalDepense')"></div>
    <div class="bg-surface-container-lowest w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="modalDepenseContent">
        <div class="p-6 border-b border-outline-variant flex justify-between items-center bg-alert-red text-white">
            <h3 class="font-headline-md text-headline-md">Enregistrer une dépense</h3>
            <button class="text-white/80 hover:text-white transition-colors" onclick="closeModal('modalDepense')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form class="p-6 space-y-4" action="{{ route('client.comptabilite.depense.store') }}" method="POST">
            @csrf
            <div>
                <label class="block text-label-sm text-on-surface-variant mb-1.5">Libellé</label>
                <textarea class="w-full rounded-lg border-outline-variant focus:border-alert-red focus:ring-alert-red text-body-sm" id="expenseLabel" name="libel_depense" placeholder="Décrivez la dépense..." rows="3" required></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-label-sm text-on-surface-variant mb-1.5">Montant (FCFA)</label>
                    <input class="w-full rounded-lg border-outline-variant focus:border-alert-red focus:ring-alert-red text-body-sm" id="expenseAmount" name="montant" step="100" type="number" required>
                </div>
                <div>
                    <label class="block text-label-sm text-on-surface-variant mb-1.5">Date</label>
                    <input class="w-full rounded-lg border-outline-variant focus:border-alert-red focus:ring-alert-red text-body-sm" id="expenseDate" name="date_depense" type="date" required>
                </div>
            </div>
            <div class="pt-4 flex gap-3">
                <button class="flex-1 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant font-label-md hover:bg-surface-container-low transition-colors" onclick="closeModal('modalDepense')" type="button">Annuler</button>
                <button class="flex-1 px-4 py-2.5 rounded-lg bg-alert-red text-white font-label-md hover:brightness-110 shadow-lg" type="submit">Confirmer</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    body { font-family: 'Inter', sans-serif; background-color: #f9f9ff; color: #111c2d; }
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    .modal-overlay { transition: backdrop-filter 0.3s ease; }
</style>
@endpush

@push('scripts')
<script>
    const searchRoute = @json(route('client.comptabilite.search'));

    // ============================================================
    // MENU DÉROULANT DES ACTIONS (Faire une action +, Paiement, Dépense, Frais)
    // ============================================================
    function toggleActionsDropdown(event) {
        event.stopPropagation();
        const menu = document.getElementById('actionsDropdownMenu');
        const icon = document.getElementById('actionsDropdownIcon');
        if (!menu) return;
        const isHidden = menu.classList.contains('hidden');
        menu.classList.toggle('hidden');
        if (icon) icon.style.transform = isHidden ? 'rotate(45deg)' : 'rotate(0deg)';
    }

    function openActionModal(modalId) {
        closeActionsDropdown();
        openModal(modalId);
    }

    function closeActionsDropdown() {
        const menu = document.getElementById('actionsDropdownMenu');
        const icon = document.getElementById('actionsDropdownIcon');
        if (menu) menu.classList.add('hidden');
        if (icon) icon.style.transform = 'rotate(0deg)';
    }

    // Ferme le menu si on clique ailleurs
    document.addEventListener('click', (e) => {
        const wrapper = document.getElementById('actionsDropdownWrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            closeActionsDropdown();
        }
    });

    // Ferme aussi avec la touche Échap
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeActionsDropdown();
    });

    function searchEleve() {
        const matricule = document.getElementById('matriculeSearch').value.trim();
        const errorBox = document.getElementById('eleveError');
        const resultBox = document.getElementById('eleveResult');
        errorBox.classList.add('hidden');
        resultBox.classList.add('hidden');

        if (!matricule) {
            errorBox.textContent = 'Veuillez saisir un matricule.';
            errorBox.classList.remove('hidden');
            return;
        }

        fetch(searchRoute + '?matricule=' + encodeURIComponent(matricule), {
            headers: { Accept: 'application/json' }
        })
        .then(response => response.json().then(data => ({ ok: response.ok, data })))
        .then(({ ok, data }) => {
            if (!ok) {
                errorBox.textContent = data.error || 'Aucun élève trouvé.';
                errorBox.classList.remove('hidden');
                return;
            }
            renderResult(data);
            resultBox.classList.remove('hidden');
        })
        .catch(() => {
            errorBox.textContent = 'Une erreur est survenue.';
            errorBox.classList.remove('hidden');
        });
    }

    function renderResult(data) {
        const e = data.eleve || {};
        document.getElementById('resultNom').textContent = (e.nom || '') + ' ' + (e.prenom || '');
        document.getElementById('resultMatricule').textContent = 'Matricule : ' + (e.matricule || '—');
        document.getElementById('resultClasse').textContent = 'Classe : ' + (e.classe || '—');
        document.getElementById('resultNiveau').textContent = 'Niveau : ' + (e.niveau || '—');

        const f = data.frais;
        if (f) {
            const fmt = n => Number(n || 0).toLocaleString('fr-FR') + ' FCFA';
            document.getElementById('fraisInscription').textContent = fmt(f.inscription);
            document.getElementById('fraisScolarite').textContent = fmt(f.scolarite);
            document.getElementById('fraisAutres').textContent = fmt(f.autres_frais);
            document.getElementById('fraisTotal').textContent = fmt(f.montant_total);
        } else {
            ['fraisInscription', 'fraisScolarite', 'fraisAutres'].forEach(id => document.getElementById(id).textContent = '—');
            document.getElementById('fraisTotal').textContent = 'Aucun frais';
        }

        const list = document.getElementById('versementsList');
        const versements = data.versements || [];
        list.innerHTML = versements.length ? versements.map(v => `<tr>
            <td class="px-3 py-2">${v.date}</td>
            <td class="px-3 py-2">${v.type === 'inscription' ? 'Inscription' : (v.type === 'scolarite' ? 'Scolarité' : 'Non classé')}</td>
            <td class="px-3 py-2 text-right font-semibold">${Number(v.montant).toLocaleString('fr-FR')} FCFA</td>
            <td class="px-3 py-2">${v.reference || '—'}</td>
            <td class="px-3 py-2">${v.statut || 'À vérifier'}</td>
            <td class="px-3 py-2">${v.enregistre_par || '—'}</td>
            <td class="px-3 py-2">${v.methode || '—'}</td>
        </tr>`).join('') : '<tr><td colspan="3" class="px-3 py-4 text-center text-on-surface-variant">Aucun paiement</td></tr>';

        const s = data.scolarite;
        document.getElementById('scolariteStatut').textContent = s?.statut === 'solde' ? 'Soldé' : (s?.statut || 'Impayé');
        document.getElementById('scolariteTotal').textContent = Number(s?.montant_total || 0).toLocaleString('fr-FR') + ' FCFA';
        document.getElementById('scolaritePaye').textContent = s ? Number(s.montant_paye).toLocaleString('fr-FR') + ' FCFA' : '—';
        document.getElementById('scolariteReste').textContent = s ? Number(s.reste).toLocaleString('fr-FR') + ' FCFA' : '—';
    }

    function searchEleveForPayment() {
        const matricule = document.getElementById('paymentMatricule').value.trim();
        const info = document.getElementById('paymentEleveInfo');
        if (!matricule) {
            info.textContent = 'Veuillez saisir un matricule.';
            info.className = 'text-sm text-alert-red mt-2';
            return;
        }
        fetch(searchRoute + '?matricule=' + encodeURIComponent(matricule), {
            headers: { Accept: 'application/json' }
        })
        .then(response => response.json().then(data => ({ ok: response.ok, data })))
        .then(({ ok, data }) => {
            if (!ok) {
                info.textContent = data.error || 'Élève introuvable.';
                info.className = 'text-sm text-alert-red mt-2';
                return;
            }
            const f = data.frais;
            info.textContent = (data.eleve.nom + ' ' + data.eleve.prenom) + (f ? ' — Total : ' + Number(f.montant_total).toLocaleString('fr-FR') + ' FCFA' : '');
            info.className = 'text-sm text-success-green mt-2';
        });
    }

    function openEditFrais(niveauId, inscription, scolarite, autres) {
        document.getElementById('fraisNiveauId').value = niveauId;
        document.getElementById('fraisInscriptionInput').value = inscription;
        document.getElementById('fraisScolariteInput').value = scolarite;
        document.getElementById('fraisAutresInput').value = autres;
        const annee = document.getElementById('fraisAnneeId');
        if (annee) {
            document.getElementById('fraisAnneeIdHidden').value = annee.value;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const annee = document.getElementById('fraisAnneeId');
        if (annee) {
            document.getElementById('fraisAnneeIdHidden').value = annee.value;
            filterFraisByAnnee(annee.value);
            annee.addEventListener('change', () => filterFraisByAnnee(annee.value));
        }
    });

function openModal(modalId) {
        const modal = document.getElementById(modalId);
        const content = document.getElementById(modalId + 'Content');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    // Frais par niveau et par année (JSON généré côté serveur)
    @php
        $fraisJsonList = ($fraisParNiveau ?? collect())->map(fn ($f) => [
            'niveau_id' => $f->niveau_id,
            'annee_academique_id' => $f->annee_academique_id,
            'inscription' => (int) $f->inscription,
            'scolarite' => (int) $f->scolarite,
            'autres_frais' => (int) $f->autres_frais,
            'montant_total' => (int) $f->montant_total,
        ])->values();
        $levelsJsonList = ($levels ?? collect())->map(fn ($l) => ['id' => $l['id'], 'name' => $l['name']])->values();
    @endphp
    const fraisData = @json($fraisJsonList);
    const levelsData = @json($levelsJsonList);

    function filterFraisByAnnee(anneeId) {
        document.getElementById('fraisAnneeIdHidden').value = anneeId;
        const tbody = document.getElementById('fraisTableBody');
        const selected = String(anneeId);
        const fmt = n => n ? Number(n).toLocaleString('fr-FR') : '—';
        tbody.innerHTML = levelsData.map(level => {
            const f = fraisData.find(item => String(item.annee_academique_id) === selected && item.niveau_id === level.id);
            return `<tr>
                <td class="px-4 py-3 font-label-md">${level.name}</td>
                <td class="px-4 py-3">${f ? fmt(f.inscription) : '—'}</td>
                <td class="px-4 py-3">${f ? fmt(f.scolarite) : '—'}</td>
                <td class="px-4 py-3">${f ? fmt(f.autres_frais) : '—'}</td>
                <td class="px-4 py-3 font-bold text-primary">${f ? fmt(f.montant_total) : '—'}</td>
                <td class="px-4 py-3 text-right">
                    <button class="p-2 rounded-lg bg-surface-container-low hover:bg-surface-container-high transition-colors" onclick="openEditFrais(${level.id}, ${f ? f.inscription : 0}, ${f ? f.scolarite : 0}, ${f ? f.autres_frais : 0})">
                        <span class="material-symbols-outlined text-[18px]">${f ? 'edit' : 'add'}</span>
                    </button>
                </td>
            </tr>`;
        }).join('');
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        const content = document.getElementById(modalId + 'Content');
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }, 300);
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            ['modalScolarite', 'modalFrais', 'modalDepense'].forEach(id => {
                const modal = document.getElementById(id);
                if (modal && modal.classList.contains('flex')) closeModal(id);
            });
        }
    });
</script>
@endpush