@extends('client.layouts.app')

@section('title', 'Comptabilité - EduAdmin Pro')

@section('content')
<!-- Header Actions & Welcome -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <p class="font-body-md text-body-md text-on-surface-variant">Gestion financière du portail client</p>
    </div>
    <div class="flex gap-3">
        <button class="flex items-center gap-2 bg-success-green text-white px-5 py-2.5 rounded-lg font-label-md text-label-md hover:brightness-110 active:scale-95 transition-all" onclick="toggleModal('modalScolarite')">
            <span class="material-symbols-outlined text-[20px]">add_circle</span>
            Enregistrer une scolarité
        </button>
        <button class="flex items-center gap-2 bg-alert-red text-white px-5 py-2.5 rounded-lg font-label-md text-label-md hover:brightness-110 active:scale-95 transition-all" onclick="toggleModal('modalDepense')">
            <span class="material-symbols-outlined text-[20px]">payments</span>
            Enregistrer une dépense
        </button>
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
<div class="fixed inset-0 z-[60] hidden items-center justify-center bg-inverse-surface/40 backdrop-blur-sm p-4" id="modalScolarite">
    <div class="bg-surface-container-lowest w-full max-w-lg rounded-2xl shadow-2xl animate-in fade-in zoom-in duration-300 overflow-hidden">
        <div class="p-6 border-b border-outline-variant flex justify-between items-center bg-success-green/5">
            <h3 class="font-headline-md text-headline-md text-primary">Enregistrer une scolarité</h3>
            <button class="text-on-surface-variant hover:text-primary transition-colors" onclick="toggleModal('modalScolarite')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form class="p-6 space-y-4" id="scolariteForm">
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-1">
                    <label class="block text-label-sm text-on-surface-variant mb-1.5">Niveau</label>
                    <select class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary-container text-body-sm" id="levelSelect">
                        @foreach($levels ?? ['Primaire', 'Secondaire', 'Lycée'] as $level)
                        <option>{{ $level }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-1">
                    <label class="block text-label-sm text-on-surface-variant mb-1.5">Classe</label>
                    <input class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary-container text-body-sm" id="className" placeholder="Ex: Terminal C" type="text">
                </div>
            </div>
            <div>
                <label class="block text-label-sm text-on-surface-variant mb-1.5">Nom de l'élève</label>
                <input class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary-container text-body-sm" id="studentName" placeholder="Entrez le nom complet" type="text">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-label-sm text-on-surface-variant mb-1.5">Montant (FCFA)</label>
                    <input class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary-container text-body-sm" id="amountInput" step="100" type="number">
                </div>
                <div>
                    <label class="block text-label-sm text-on-surface-variant mb-1.5">Année Académique</label>
                    <input class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary-container text-body-sm bg-surface-container-low" readonly type="text" value="{{ $currentYear ?? '2024-2025' }}">
                </div>
            </div>
            <div class="pt-4 flex gap-3">
                <button class="flex-1 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant font-label-md hover:bg-surface-container-low transition-colors" onclick="toggleModal('modalScolarite')" type="button">Annuler</button>
                <button class="flex-1 px-4 py-2.5 rounded-lg bg-success-green text-white font-label-md hover:brightness-110 shadow-lg shadow-success-green/20" type="submit">Valider l'encaissement</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Dépense -->
<div class="fixed inset-0 z-[60] hidden items-center justify-center bg-inverse-surface/40 backdrop-blur-sm p-4" id="modalDepense">
    <div class="bg-surface-container-lowest w-full max-w-lg rounded-2xl shadow-2xl animate-in fade-in zoom-in duration-300 overflow-hidden">
        <div class="p-6 border-b border-outline-variant flex justify-between items-center bg-alert-red/5">
            <h3 class="font-headline-md text-headline-md text-primary">Enregistrer une dépense</h3>
            <button class="text-on-surface-variant hover:text-primary transition-colors" onclick="toggleModal('modalDepense')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form class="p-6 space-y-4" id="depenseForm">
            <div>
                <label class="block text-label-sm text-on-surface-variant mb-1.5">Libellé de la dépense</label>
                <textarea class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary-container text-body-sm" id="expenseLabel" placeholder="Décrivez la nature de la dépense..." rows="3"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-label-sm text-on-surface-variant mb-1.5">Montant (FCFA)</label>
                    <input class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary-container text-body-sm" id="expenseAmount" step="100" type="number">
                </div>
                <div>
                    <label class="block text-label-sm text-on-surface-variant mb-1.5">Date de l'opération</label>
                    <input class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary-container text-body-sm" id="expenseDate" type="date">
                </div>
            </div>
            <div class="pt-4 flex gap-3">
                <button class="flex-1 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant font-label-md hover:bg-surface-container-low transition-colors" onclick="toggleModal('modalDepense')" type="button">Annuler</button>
                <button class="flex-1 px-4 py-2.5 rounded-lg bg-alert-red text-white font-label-md hover:brightness-110 shadow-lg shadow-alert-red/20" type="submit">Confirmer la dépense</button>
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
    .modal-active { display: flex !important; }
</style>
@endpush

@push('scripts')
<script>
    // Modal Handling
    function toggleModal(id) {
        const modal = document.getElementById(id);
        modal.classList.toggle('hidden');
        modal.classList.toggle('modal-active');
        if (!modal.classList.contains('hidden')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = 'auto';
        }
    }

    // Close modal on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const activeModals = document.querySelectorAll('.modal-active');
            activeModals.forEach(modal => {
                modal.classList.add('hidden');
                modal.classList.remove('modal-active');
                document.body.style.overflow = 'auto';
            });
        }
    });

    // Form Submission Logic with SweetAlert2
    const scolariteForm = document.getElementById('scolariteForm');
    if (scolariteForm) {
        scolariteForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const studentName = document.getElementById('studentName').value;
            const amount = document.getElementById('amountInput').value;
            
            if (!studentName || !amount) {
                Swal.fire({
                    title: 'Champs manquants',
                    text: 'Veuillez remplir tous les champs obligatoires.',
                    icon: 'error',
                    confirmButtonColor: '#1f108e'
                });
                return;
            }
            
            toggleModal('modalScolarite');
            Swal.fire({
                title: 'Enregistré !',
                text: `La scolarité de ${studentName} d'un montant de ${parseInt(amount).toLocaleString()} FCFA a été encaissée avec succès.`,
                icon: 'success',
                confirmButtonColor: '#1f108e',
                timer: 3000,
                timerProgressBar: true
            }).then(() => {
                scolariteForm.reset();
            });
        });
    }

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
                    confirmButtonColor: '#1f108e'
                });
                return;
            }
            
            toggleModal('modalDepense');
            Swal.fire({
                title: 'Dépense Enregistrée',
                text: `La dépense "${label.substring(0, 50)}" d'un montant de ${parseInt(amount).toLocaleString()} FCFA a été ajoutée à la comptabilité.`,
                icon: 'warning',
                confirmButtonColor: '#1f108e',
                timer: 3000,
                timerProgressBar: true
            }).then(() => {
                depenseForm.reset();
            });
        });
    }
</script>
@endpush