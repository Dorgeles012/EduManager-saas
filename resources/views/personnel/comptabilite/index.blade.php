@extends('personnel.layouts.app')
@section('title', 'EduManager - Comptabilité')
@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <p class="font-body-md text-body-md text-on-surface-variant">Gestion financière</p>
    </div>
    <div class="flex gap-3">
        <button class="flex items-center gap-2 bg-success-green text-white px-5 py-2.5 rounded-lg font-label-md text-label-md hover:brightness-110 active:scale-95 transition-all" onclick="openModal('modalScolarite')">
            <span class="material-symbols-outlined text-[20px]">add_circle</span>
            Enregistrer une scolarité
        </button>
        <button class="flex items-center gap-2 bg-alert-red text-white px-5 py-2.5 rounded-lg font-label-md text-label-md hover:brightness-110 active:scale-95 transition-all" onclick="openModal('modalDepense')">
            <span class="material-symbols-outlined text-[20px]">payments</span>
            Enregistrer une dépense
        </button>
    </div>
</div>

<div class="space-y-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-8 bg-primary-container text-white p-8 rounded-xl relative overflow-hidden custom-shadow">
            <div class="relative z-10">
                <p class="font-label-sm text-label-sm uppercase tracking-widest opacity-80 mb-2">Solde Actuel</p>
                <h3 class="font-headline-xl text-headline-xl mb-6">{{ number_format($currentBalance ?? 0, 0, ',', ' ') }} FCFA</h3>
                <div class="grid grid-cols-2 gap-4 max-w-md">
                    <div class="bg-white/10 p-4 rounded-lg backdrop-blur-sm">
                        <p class="text-[10px] uppercase font-bold opacity-70 mb-1">Total Encaissé</p>
                        <p class="font-headline-md text-headline-md text-secondary-fixed">{{ number_format($totalIncome ?? 0, 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div class="bg-white/10 p-4 rounded-lg backdrop-blur-sm">
                        <p class="text-[10px] uppercase font-bold opacity-70 mb-1">Total Dépensé</p>
                        <p class="font-headline-md text-headline-md text-error-container">{{ number_format($totalExpense ?? 0, 0, ',', ' ') }} FCFA</p>
                    </div>
                </div>
            </div>
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
        </div>
        <div class="lg:col-span-4 flex flex-col gap-6">
            <div class="flex-1 bg-surface-container-lowest p-6 rounded-xl border border-outline-variant flex items-center justify-between group hover:border-success-green transition-all cursor-default">
                <div>
                    <p class="text-label-sm text-on-surface-variant font-medium">Total Scolarités</p>
                    <p class="font-headline-md text-headline-md text-success-green">{{ number_format($totalIncome ?? 0, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-success-green/10 flex items-center justify-center text-success-green">
                    <span class="material-symbols-outlined">receipt_long</span>
                </div>
            </div>
            <div class="flex-1 bg-surface-container-lowest p-6 rounded-xl border border-outline-variant flex items-center justify-between group hover:border-alert-red transition-all cursor-default">
                <div>
                    <p class="text-label-sm text-on-surface-variant font-medium">Paiements effectués</p>
                    <p class="font-headline-md text-headline-md text-on-surface">{{ $paymentCount ?? 0 }} paiement(s)</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-surface-container/50 flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined">credit_score</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8">
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
                        @forelse($payments ?? [] as $payment)
                        <tr class="hover:bg-surface-container-low/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs">{{ substr($payment['student'], 0, 2) }}</div>
                                    <p class="font-body-md text-body-md text-on-surface">{{ $payment['student'] }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-body-sm text-body-sm text-on-surface-variant">{{ $payment['class'] }}</td>
                            <td class="px-6 py-4 font-body-md text-body-md font-semibold text-success-green">{{ number_format($payment['amount'], 0, ',', ' ') }} FCFA</td>
                            <td class="px-6 py-4 font-body-sm text-body-sm text-on-surface-variant text-right">{{ $payment['date'] }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-on-surface-variant">Aucune scolarité enregistrée</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-surface-container-low font-bold">
                        <tr>
                            <td class="px-6 py-4 text-on-surface" colspan="2">Total Général</td>
                            <td class="px-6 py-4 text-success-green text-headline-md" colspan="2">{{ number_format($totalIncome ?? 0, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>

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
                        @forelse($expenses ?? [] as $expense)
                        <tr class="hover:bg-surface-container-low/30 transition-colors">
                            <td class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant">#{{ $expense['id'] }}</td>
                            <td class="px-6 py-4 font-body-md text-body-md text-on-surface">{{ $expense['label'] }}</td>
                            <td class="px-6 py-4 font-body-md text-body-md font-semibold text-alert-red">{{ number_format($expense['amount'], 0, ',', ' ') }} FCFA</td>
                            <td class="px-6 py-4 font-body-sm text-body-sm text-on-surface-variant text-right">{{ $expense['date'] }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-on-surface-variant">Aucune dépense enregistrée</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-surface-container-low font-bold">
                        <tr>
                            <td class="px-6 py-4 text-on-surface" colspan="2">Total Général</td>
                            <td class="px-6 py-4 text-alert-red text-headline-md" colspan="2">{{ number_format($totalExpense ?? 0, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>
    </div>
</div>

<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="modalScolarite">
    <div class="absolute inset-0 modal-overlay backdrop-blur-md bg-black/30" onclick="closeModal('modalScolarite')"></div>
    <div class="bg-surface-container-lowest w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="modalScolariteContent">
        <div class="p-6 border-b border-outline-variant flex justify-between items-center bg-success-green text-white">
            <h3 class="font-headline-md text-headline-md">Enregistrer une scolarité</h3>
            <button class="text-white/80 hover:text-white transition-colors" onclick="closeModal('modalScolarite')"><span class="material-symbols-outlined">close</span></button>
        </div>
        <form class="p-6 space-y-4" action="{{ route('personnel.comptabilite.scolarite.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-1">
                    <label class="block text-label-sm text-on-surface-variant mb-1.5">Niveau</label>
                    <select class="w-full rounded-lg border-outline-variant focus:border-success-green focus:ring-success-green text-body-sm" id="levelSelect">
                        @foreach($levels ?? [] as $level)
                        <option value="{{ $level['id'] }}">{{ $level['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-1">
                    <label class="block text-label-sm text-on-surface-variant mb-1.5">Classe</label>
                    <input class="w-full rounded-lg border-outline-variant focus:border-success-green focus:ring-success-green text-body-sm" placeholder="Ex: Terminal C" type="text">
                </div>
            </div>
            <div>
                <label class="block text-label-sm text-on-surface-variant mb-1.5">Nom de l'élève</label>
                <select class="w-full rounded-lg border-outline-variant focus:border-success-green focus:ring-success-green text-body-sm" name="eleve_id" required>
                    <option value="">Sélectionner un élève</option>
                    @foreach($eleves ?? [] as $eleve)
                        <option value="{{ $eleve['id'] }}">{{ $eleve['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-label-sm text-on-surface-variant mb-1.5">Montant (FCFA)</label>
                    <input class="w-full rounded-lg border-outline-variant focus:border-success-green focus:ring-success-green text-body-sm" name="montant" step="100" type="number" required>
                </div>
                <div>
                    <label class="block text-label-sm text-on-surface-variant mb-1.5">Année Académique</label>
                    <input class="w-full rounded-lg border-outline-variant focus:border-success-green focus:ring-success-green text-body-sm bg-surface-container-low" readonly type="text" value="{{ $currentYear ?? '2024-2025' }}">
                </div>
            </div>
            <div class="pt-4 flex gap-3">
                <button class="flex-1 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant font-label-md hover:bg-surface-container-low transition-colors" onclick="closeModal('modalScolarite')" type="button">Annuler</button>
                <button class="flex-1 px-4 py-2.5 rounded-lg bg-success-green text-white font-label-md hover:brightness-110 shadow-lg" type="submit">Valider l'encaissement</button>
            </div>
        </form>
    </div>
</div>

<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="modalDepense">
    <div class="absolute inset-0 modal-overlay backdrop-blur-md bg-black/30" onclick="closeModal('modalDepense')"></div>
    <div class="bg-surface-container-lowest w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="modalDepenseContent">
        <div class="p-6 border-b border-outline-variant flex justify-between items-center bg-alert-red text-white">
            <h3 class="font-headline-md text-headline-md">Enregistrer une dépense</h3>
            <button class="text-white/80 hover:text-white transition-colors" onclick="closeModal('modalDepense')"><span class="material-symbols-outlined">close</span></button>
        </div>
        <form class="p-6 space-y-4" action="{{ route('personnel.comptabilite.depense.store') }}" method="POST">
            @csrf
            <div>
                <label class="block text-label-sm text-on-surface-variant mb-1.5">Libellé de la dépense</label>
                <textarea class="w-full rounded-lg border-outline-variant focus:border-alert-red focus:ring-alert-red text-body-sm" name="libel_depense" placeholder="Décrivez la nature de la dépense..." rows="3" required></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-label-sm text-on-surface-variant mb-1.5">Montant (FCFA)</label>
                    <input class="w-full rounded-lg border-outline-variant focus:border-alert-red focus:ring-alert-red text-body-sm" name="montant" step="100" type="number" required>
                </div>
                <div>
                    <label class="block text-label-sm text-on-surface-variant mb-1.5">Date de l'opération</label>
                    <input class="w-full rounded-lg border-outline-variant focus:border-alert-red focus:ring-alert-red text-body-sm" name="date_depense" type="date">
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
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    .custom-shadow { box-shadow: 0 4px 12px rgba(55, 48, 163, 0.04); }
    .modal-overlay { transition: backdrop-filter 0.3s ease; }
    #modalScolarite, #modalDepense { transition: opacity 0.3s ease; }
</style>
@endpush

@push('scripts')
<script>
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        const contentId = modalId + 'Content';
        const content = document.getElementById(contentId);
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        setTimeout(() => { content.classList.remove('scale-95', 'opacity-0'); content.classList.add('scale-100', 'opacity-100'); }, 10);
    }
    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        const contentId = modalId + 'Content';
        const content = document.getElementById(contentId);
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => { modal.classList.remove('flex'); modal.classList.add('hidden'); document.body.style.overflow = 'auto'; }, 300);
    }
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            ['modalScolarite', 'modalDepense'].forEach(id => {
                const modal = document.getElementById(id);
                if (modal && modal.classList.contains('flex')) closeModal(id);
            });
        }
    });
</script>
@endpush
