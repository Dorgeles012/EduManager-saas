@extends('client.layouts.app')
@section('title', 'EduManager - Comptabilité')
@section('content')
<!-- Header Actions & Welcome -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-primary">Gestion financière </h2>
        <p class="text-body-md text-on-surface-variant">Gérez les finances de votre établissement avec précision et clarté.</p>
    </div>
    <div class="flex gap-3 flex-wrap">
        <button class="flex items-center gap-2 bg-success-green text-white px-5 py-2.5 rounded-lg font-label-md text-label-md hover:brightness-110 active:scale-95 transition-all" onclick="openModal('modalScolarite')">
            <span class="material-symbols-outlined text-[20px]">add_circle</span>
            Enregistrer un paiement
        </button>
        <button class="flex items-center gap-2 bg-primary text-white px-5 py-2.5 rounded-lg font-label-md text-label-md hover:brightness-110 active:scale-95 transition-all" onclick="openModal('modalFrais')">
            <span class="material-symbols-outlined text-[20px]">tune</span>
            Configurer les frais
        </button>
        <button class="flex items-center gap-2 bg-alert-red text-white px-5 py-2.5 rounded-lg font-label-md text-label-md hover:brightness-110 active:scale-95 transition-all" onclick="openModal('modalDepense')">
            <span class="material-symbols-outlined text-[20px]">payments</span>
            Enregistrer une dépense
        </button>
    </div>
</div>

<!-- Recherche par matricule -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden custom-shadow mb-8">
    <div class="px-6 py-5 border-b border-outline-variant flex items-center gap-3">
        <span class="material-symbols-outlined text-primary">search</span>
        <h4 class="font-headline-md text-headline-md">Rechercher un élève par matricule</h4>
    </div>
    <div class="p-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" id="matriculeSearch" placeholder="Saisissez le matricule de l'élève..." class="w-full rounded-lg border border-outline-variant focus:border-primary focus:ring-primary text-body-sm px-4 py-2.5">
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
                            <p id="resultEtab" class="text-sm text-on-surface-variant"></p>
                        </div>
                    </div>
                    <div id="fraisInfo" class="bg-surface-container-lowest rounded-lg p-4 border border-outline-variant">
                        <p class="text-label-sm text-on-surface-variant uppercase tracking-wider mb-2">Frais du niveau</p>
                        <div class="flex justify-between text-sm"><span>Inscription</span><span id="fraisInscription" class="font-semibold"></span></div>
                        <div class="flex justify-between text-sm"><span>Scolarité</span><span id="fraisScolarite" class="font-semibold"></span></div>
                        <div class="flex justify-between text-sm"><span>Autres frais</span><span id="fraisAutres" class="font-semibold"></span></div>
                        <div class="flex justify-between text-sm font-bold border-t border-outline-variant mt-2 pt-2"><span>Total</span><span id="fraisTotal" class="text-primary"></span></div>
                    </div>
                </div>

                <!-- Historique des paiements -->
                <div class="bg-surface-container-low rounded-xl p-5">
                    <h5 class="font-headline-md text-headline-md mb-3">Historique des paiements</h5>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="border-b border-outline-variant text-on-surface-variant">
                                <tr>
                                    <th class="px-3 py-2 text-left">Date</th>
                                    <th class="px-3 py-2 text-right">Montant</th>
                                    <th class="px-3 py-2 text-left">Méthode</th>
                                </tr>
                            </thead>
                            <tbody id="versementsList"></tbody>
                        </table>
                    </div>
                    <div id="scolariteRecap" class="mt-3 text-sm bg-surface-container-lowest rounded-lg p-3 border border-outline-variant">
                        <div class="flex justify-between"><span>Total payé</span><span id="scolaritePaye" class="font-semibold text-success-green"></span></div>
                        <div class="flex justify-between"><span>Reste à payer</span><span id="scolariteReste" class="font-semibold text-alert-red"></span></div>
                    </div>
                </div>
            </div>
        </div>

        <div id="eleveError" class="hidden mt-4 rounded-lg border border-alert-red/20 bg-alert-red/10 px-4 py-3 text-alert-red"></div>
    </div>
</div>

<!-- Dashboard Layout -->
<div class="space-y-8">
    <!-- Hero Stats Row -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Main Balance Card -->
        <div class="lg:col-span-8 bg-primary-container text-white p-8 rounded-xl relative overflow-hidden custom-shadow">
            <div class="relative z-10">
                <p class="font-label-sm text-label-sm uppercase tracking-widest opacity-80 mb-2">Solde Actuel</p>
                <h3 class="font-headline-xl text-headline-xl mb-6">{{ number_format($currentBalance ?? -12166200, 0, ',', ' ') }} FCFA</h3>
                <div class="grid grid-cols-2 gap-4 max-w-md">
                    <div class="bg-white/10 p-4 rounded-lg backdrop-blur-sm">
                        <p class="text-[10px] uppercase font-bold opacity-70 mb-1">Total Encaissé</p>
                        <p class="font-headline-md text-headline-md text-secondary-fixed">{{ number_format($totalIncome ?? 5000, 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div class="bg-white/10 p-4 rounded-lg backdrop-blur-sm">
                        <p class="text-[10px] uppercase font-bold opacity-70 mb-1">Total Dépensé</p>
                        <p class="font-headline-md text-headline-md text-error-container">{{ number_format($totalExpense ?? 12171200, 0, ',', ' ') }} FCFA</p>
                    </div>
                </div>
            </div>
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
        </div>

        <!-- Secondary Stats Column -->
        <div class="lg:col-span-4 flex flex-col gap-6">
            <div class="flex-1 bg-surface-container-lowest p-6 rounded-xl border border-outline-variant flex items-center justify-between group hover:border-success-green transition-all cursor-default">
                <div>
                    <p class="text-label-sm text-on-surface-variant font-medium">Total Scolarités</p>
                    <p class="font-headline-md text-headline-md text-success-green">{{ number_format($totalIncome ?? 5000, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-success-green/10 flex items-center justify-center text-success-green">
                    <span class="material-symbols-outlined">receipt_long</span>
                </div>
            </div>
            <div class="flex-1 bg-surface-container-lowest p-6 rounded-xl border border-outline-variant flex items-center justify-between group hover:border-alert-red transition-all cursor-default">
                <div>
                    <p class="text-label-sm text-on-surface-variant font-medium">Paiements effectués</p>
                    <p class="font-headline-md text-headline-md text-on-surface">{{ $paymentCount ?? 1 }} paiement(s)</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-surface-container/50 flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined">credit_score</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Data Tables -->
    <div class="grid grid-cols-1 gap-8">
        <!-- Scolarités Table Section -->
        <section class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden custom-shadow">
            <div class="px-6 py-5 border-b border-outline-variant flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <h4 class="font-headline-md text-headline-md">Scolarités</h4>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-surface-container-low">
                        <tr>
                            <th class="px-6 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant">Élève</th>
                            <th class="px-6 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant">Classe</th>
                            <th class="px-6 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant">Montant</th>
                            <th class="px-6 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant text-right">Date de versement</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant">
                        @forelse($payments ?? [
                            ['student' => 'Jean Dupont', 'class' => 'Terminal C1', 'amount' => 5000, 'date' => '15/10/2023']
                        ] as $payment)
                        <tr class="hover:bg-surface-container-low/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs">
                                        {{ substr($payment['student'], 0, 2) }}
                                    </div>
                                    <p class="font-body-md text-body-md text-on-surface">{{ $payment['student'] }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-body-sm text-body-sm text-on-surface-variant">{{ $payment['class'] }}</td>
                            <td class="px-6 py-4 font-body-md text-body-md font-semibold text-success-green">{{ number_format($payment['amount'], 0, ',', ' ') }} FCFA</td>
                            <td class="px-6 py-4 font-body-sm text-body-sm text-on-surface-variant text-right">{{ $payment['date'] }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-on-surface-variant">Aucune scolarité enregistrée</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-surface-container-low font-bold">
                        <tr>
                            <td class="px-6 py-4 text-on-surface" colspan="2">Total Général</td>
                            <td class="px-6 py-4 text-success-green text-headline-md" colspan="2">{{ number_format($totalIncome ?? 5000, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>

        <!-- Dépenses Table Section -->
        <section class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden custom-shadow">
            <div class="px-6 py-5 border-b border-outline-variant flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-alert-red">payments</span>
                    <h4 class="font-headline-md text-headline-md">Dépenses</h4>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-surface-container-low">
                        <tr>
                            <th class="px-6 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant w-16">#</th>
                            <th class="px-6 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant">Libellé</th>
                            <th class="px-6 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant">Montant</th>
                            <th class="px-6 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant text-right">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant">
                        @forelse($expenses ?? [
                            ['id' => '001', 'label' => 'Achat Fournitures Bureautiques - Q4', 'amount' => 12171200, 'date' => '12/10/2023']
                        ] as $expense)
                        <tr class="hover:bg-surface-container-low/30 transition-colors">
                            <td class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant">#{{ $expense['id'] }}</td>
                            <td class="px-6 py-4 font-body-md text-body-md text-on-surface">{{ $expense['label'] }}</td>
                            <td class="px-6 py-4 font-body-md text-body-md font-semibold text-alert-red">{{ number_format($expense['amount'], 0, ',', ' ') }} FCFA</td>
                            <td class="px-6 py-4 font-body-sm text-body-sm text-on-surface-variant text-right">{{ $expense['date'] }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-on-surface-variant">Aucune dépense enregistrée</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-surface-container-low font-bold">
                        <tr>
                            <td class="px-6 py-4 text-on-surface" colspan="2">Total Général</td>
                            <td class="px-6 py-4 text-alert-red text-headline-md" colspan="2">{{ number_format($totalExpense ?? 12171200, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>
    </div>
</div>

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
                <p class="text-sm text-on-surface mb-3">Paiement enregistré. Téléchargez ou imprimez le reçu.</p>
                <a href="{{ route('client.comptabilite.recu') }}" target="_blank" class="inline-flex items-center gap-2 bg-primary text-on-primary px-4 py-2 rounded-lg font-label-md">
                    <span class="material-symbols-outlined text-[18px]">print</span>
                    Imprimer le reçu
                </a>
            </div>
        @endif

        <form class="p-6 space-y-4" id="scolariteForm" action="{{ route('client.comptabilite.scolarite.store') }}" method="POST">
            @csrf
            <input type="hidden" name="montant_versement" id="amountVersementHidden">
            <input type="hidden" name="annee_scolaire" id="academicYearHidden" value="{{ $currentYear ?? '2024-2025' }}">
            <div>
                <label class="block text-label-sm text-on-surface-variant mb-1.5">Matricule de l'élève</label>
                <div class="flex gap-2">
                    <input class="w-full rounded-lg border-outline-variant focus:border-success-green focus:ring-success-green text-body-sm" id="paymentMatricule" name="matricule" placeholder="Saisissez le matricule" required>
                    <button type="button" class="px-4 py-2 bg-surface-container-high text-on-surface rounded-lg shrink-0" onclick="searchEleveForPayment()">Vérifier</button>
                </div>
                <p id="paymentEleveInfo" class="text-sm text-on-surface-variant mt-2"></p>
            </div>
            <div>
                <label class="block text-label-sm text-on-surface-variant mb-1.5">Montant à payer (FCFA)</label>
                <input class="w-full rounded-lg border-outline-variant focus:border-success-green focus:ring-success-green text-body-sm" id="amountInput" name="montant_versement" step="100" type="number" placeholder="Basé sur les frais configurés">
            </div>
            <div>
                <label class="block text-label-sm text-on-surface-variant mb-1.5">Moyen de paiement</label>
                <select class="w-full rounded-lg border-outline-variant focus:border-success-green focus:ring-success-green text-body-sm" id="paymentMethod" name="methode" required>
                    <option value="">Sélectionner un moyen</option>
                    @foreach($paymentMethods ?? [] as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-label-sm text-on-surface-variant mb-1.5">Date du versement</label>
                <input class="w-full rounded-lg border-outline-variant focus:border-success-green focus:ring-success-green text-body-sm" id="paymentDate" name="date_versement" type="date" value="{{ now()->toDateString() }}">
            </div>
            <div class="pt-4 flex gap-3">
                <button class="flex-1 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant font-label-md hover:bg-surface-container-low transition-colors" onclick="closeModal('modalScolarite')" type="button">Annuler</button>
                <button class="flex-1 px-4 py-2.5 rounded-lg bg-success-green text-white font-label-md hover:brightness-110 shadow-lg" type="submit">Valider l'encaissement</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Frais par niveau -->
<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="modalFrais">
    <div class="absolute inset-0 modal-overlay backdrop-blur-md bg-black/30" onclick="closeModal('modalFrais')"></div>
    <div class="bg-surface-container-lowest w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="modalFraisContent">
        <div class="p-6 border-b border-outline-variant flex justify-between items-center bg-primary text-white">
            <h3 class="font-headline-md text-headline-md">Configuration des frais de scolarité</h3>
            <button class="text-white/80 hover:text-white transition-colors" onclick="closeModal('modalFrais')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-surface-container-low">
                        <tr>
                            <th class="px-4 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant">Niveau</th>
                            <th class="px-4 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant">Inscription</th>
                            <th class="px-4 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant">Scolarité</th>
                            <th class="px-4 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant">Autres frais</th>
                            <th class="px-4 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant">Total</th>
                            <th class="px-4 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant">
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
                                    <button class="p-2 rounded-lg bg-surface-container-low hover:bg-surface-container-high transition-colors" onclick="openEditFrais({{ $level['id'] }}, {{ $frais?->inscription ?? 0 }}, {{ $frais?->scolarite ?? 0 }}, {{ $frais?->autres_frais ?? 0 }})" title="{{ $frais ? 'Modifier' : 'Configurer' }}">
                                        <span class="material-symbols-outlined text-[18px]">{{ $frais ? 'edit' : 'add' }}</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-6 text-center text-on-surface-variant">Aucun niveau configuré.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Formulaire édition frais -->
            <form method="POST" action="{{ route('client.comptabilite.frais.store') }}" class="mt-6 p-4 bg-surface-container-low rounded-xl space-y-4" id="fraisForm">
                @csrf
                <input type="hidden" name="niveau_id" id="fraisNiveauId">
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-label-sm text-on-surface-variant mb-1.5">Inscription (FCFA)</label>
                        <input class="w-full rounded-lg border-outline-variant text-body-sm" name="inscription" id="fraisInscriptionInput" type="number" min="0" value="0">
                    </div>
                    <div>
                        <label class="block text-label-sm text-on-surface-variant mb-1.5">Scolarité (FCFA)</label>
                        <input class="w-full rounded-lg border-outline-variant text-body-sm" name="scolarite" id="fraisScolariteInput" type="number" min="0" value="0">
                    </div>
                    <div>
                        <label class="block text-label-sm text-on-surface-variant mb-1.5">Autres frais (FCFA)</label>
                        <input class="w-full rounded-lg border-outline-variant text-body-sm" name="autres_frais" id="fraisAutresInput" type="number" min="0" value="0">
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 px-4 py-2.5 rounded-lg bg-primary text-white font-label-md hover:bg-primary/90">Enregistrer les frais</button>
                </div>
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
        <form class="p-6 space-y-4" id="depenseForm" action="{{ route('client.comptabilite.depense.store') }}" method="POST">
            @csrf
            <input type="hidden" name="libel_depense" id="expenseLabelHidden">
            <input type="hidden" name="montant" id="expenseAmountHidden">
            <input type="hidden" name="date_depense" id="expenseDateHidden">
            <div>
                <label class="block text-label-sm text-on-surface-variant mb-1.5">Libellé de la dépense</label>
                <textarea class="w-full rounded-lg border-outline-variant focus:border-alert-red focus:ring-alert-red text-body-sm" id="expenseLabel" placeholder="Décrivez la nature de la dépense..." rows="3"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-label-sm text-on-surface-variant mb-1.5">Montant (FCFA)</label>
                    <input class="w-full rounded-lg border-outline-variant focus:border-alert-red focus:ring-alert-red text-body-sm" id="expenseAmount" step="100" type="number">
                </div>
                <div>
                    <label class="block text-label-sm text-on-surface-variant mb-1.5">Date de l'opération</label>
                    <input class="w-full rounded-lg border-outline-variant focus:border-alert-red focus:ring-alert-red text-body-sm" id="expenseDate" type="date">
                </div>
            </div>
            <div class="pt-4 flex gap-3">
                <button class="flex-1 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant font-label-md hover:bg-surface-container-low transition-colors" onclick="closeModal('modalDepense')" type="button">Annuler</button>
                <button class="flex-1 px-4 py-2.5 rounded-lg bg-alert-red text-white font-label-md hover:brightness-110 shadow-lg" type="submit">Confirmer la dépense</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    body { font-family: 'Inter', sans-serif; background-color: #f9f9ff; color: #111c2d; }
    .font-headline { font-family: 'Lexend', sans-serif; }
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    .custom-shadow { box-shadow: 0 4px 12px rgba(55, 48, 163, 0.04); }
    
    /* Modal overlay animation */
    .modal-overlay {
        transition: backdrop-filter 0.3s ease;
    }
    
    /* Modal animation */
    #modalScolarite, #modalDepense {
        transition: opacity 0.3s ease;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const realDepenseForm = document.getElementById('depenseForm');
        if (realDepenseForm) {
            realDepenseForm.addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                document.getElementById('expenseLabelHidden').value = document.getElementById('expenseLabel').value;
                document.getElementById('expenseAmountHidden').value = document.getElementById('expenseAmount').value;
                document.getElementById('expenseDateHidden').value = document.getElementById('expenseDate').value;
                HTMLFormElement.prototype.submit.call(realDepenseForm);
            }, true);
        }
    });

    const searchRoute = @json(route('client.comptabilite.search'));

    // Recherche élève dans la section "Rechercher un élève par matricule"
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
            errorBox.textContent = 'Une erreur est survenue lors de la recherche.';
            errorBox.classList.remove('hidden');
        });
    }

    function renderResult(data) {
        const e = data.eleve || {};
        document.getElementById('resultNom').textContent = (e.nom || '') + ' ' + (e.prenom || '');
        document.getElementById('resultMatricule').textContent = 'Matricule : ' + (e.matricule || '—');
        document.getElementById('resultClasse').textContent = 'Classe : ' + (e.classe || '—');
        document.getElementById('resultNiveau').textContent = 'Niveau : ' + (e.niveau || '—');
        document.getElementById('resultEtab').textContent = 'Établissement : ' + (e.etablissement || '—');

        const photo = document.getElementById('resultPhoto');
        if (e.photo) {
            photo.src = e.photo;
            photo.classList.remove('hidden');
        } else {
            photo.classList.add('hidden');
        }

        const f = data.frais;
        if (f) {
            const fmt = n => Number(n || 0).toLocaleString('fr-FR') + ' FCFA';
            document.getElementById('fraisInscription').textContent = fmt(f.inscription);
            document.getElementById('fraisScolarite').textContent = fmt(f.scolarite);
            document.getElementById('fraisAutres').textContent = fmt(f.autres_frais);
            document.getElementById('fraisTotal').textContent = fmt(f.montant_total);
        } else {
            document.getElementById('fraisInscription').textContent = '—';
            document.getElementById('fraisScolarite').textContent = '—';
            document.getElementById('fraisAutres').textContent = '—';
            document.getElementById('fraisTotal').textContent = 'Aucun frais configuré';
        }

        const list = document.getElementById('versementsList');
        const versements = data.versements || [];
        if (versements.length) {
            list.innerHTML = versements.map(v => `<tr class="border-b border-outline-variant/50">
                <td class="px-3 py-2">${v.date}</td>
                <td class="px-3 py-2 text-right font-semibold">${Number(v.montant).toLocaleString('fr-FR')} FCFA</td>
                <td class="px-3 py-2">${v.methode || '—'}</td>
            </tr>`).join('');
        } else {
            list.innerHTML = '<tr><td colspan="3" class="px-3 py-4 text-center text-on-surface-variant">Aucun paiement enregistré.</td></tr>';
        }

        const s = data.scolarite;
        document.getElementById('scolaritePaye').textContent = s ? Number(s.montant_paye).toLocaleString('fr-FR') + ' FCFA' : '—';
        document.getElementById('scolariteReste').textContent = s ? Number(s.reste).toLocaleString('fr-FR') + ' FCFA' : '—';
    }

    // Vérifie le matricule dans le modal de paiement
    function searchEleveForPayment() {
        const matricule = document.getElementById('paymentMatricule').value.trim();
        const info = document.getElementById('paymentEleveInfo');
        if (!matricule) {
            info.textContent = 'Veuillez saisir un matricule.';
            info.classList.add('text-alert-red');
            return;
        }
        fetch(searchRoute + '?matricule=' + encodeURIComponent(matricule), {
            headers: { Accept: 'application/json' }
        })
        .then(response => response.json().then(data => ({ ok: response.ok, data })))
        .then(({ ok, data }) => {
            if (!ok) {
                info.textContent = (data.error || 'Élève introuvable.');
                info.className = 'text-sm text-alert-red mt-2';
                return;
            }
            const f = data.frais;
            info.textContent = (data.eleve.nom + ' ' + data.eleve.prenom) + (f ? ' — Total : ' + Number(f.montant_total).toLocaleString('fr-FR') + ' FCFA' : '');
            info.className = 'text-sm text-success-green mt-2';
        });
    }

    // Remplit le formulaire des frais pour modifier
    function openEditFrais(niveauId, inscription, scolarite, autres) {
        document.getElementById('fraisNiveauId').value = niveauId;
        document.getElementById('fraisInscriptionInput').value = inscription;
        document.getElementById('fraisScolariteInput').value = scolarite;
        document.getElementById('fraisAutresInput').value = autres;
    }

    // Modal Handling with animation
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        const contentId = modalId + 'Content';
        const content = document.getElementById(contentId);

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';

        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        const contentId = modalId + 'Content';
        const content = document.getElementById(contentId);

        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }, 300);
    }

    // Close modal on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const modals = ['modalScolarite', 'modalFrais', 'modalDepense'];
            modals.forEach(id => {
                const modal = document.getElementById(id);
                if (modal && modal.classList.contains('flex')) {
                    closeModal(id);
                }
            });
        }
    });

    const depenseForm = document.getElementById('depenseForm');
    if (depenseForm) {
        depenseForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const label = document.getElementById('expenseLabel').value;
            const amount = document.getElementById('expenseAmount').value;

            if (!label || !amount) {
                Swal.fire({
                    title: 'Champs manquants',
                    text: 'Veuillez remplir tous les champs obligatoires.',
                    icon: 'error',
                    confirmButtonColor: '#1f108e',
                    borderRadius: '12px'
                });
                return;
            }

            closeModal('modalDepense');
            Swal.fire({
                title: 'Dépense Enregistrée',
                text: `La dépense "${label.substring(0, 50)}" d'un montant de ${parseInt(amount).toLocaleString()} FCFA a été ajoutée à la comptabilité.`,
                icon: 'warning',
                confirmButtonColor: '#1f108e',
                timer: 3000,
                timerProgressBar: true,
                borderRadius: '12px'
            }).then(() => {
                depenseForm.reset();
            });
        });
    }
</script>
@endpush
