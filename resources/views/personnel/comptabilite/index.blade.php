@extends('personnel.layouts.app')
@section('title', 'EduManager - Comptabilité')
@section('content')
<!-- En-tête simplifié -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-primary">Gestion financière</h2>
        <p class="text-body-md text-on-surface-variant">Gérez les finances de votre établissement avec précision et clarté.</p>
    </div>
    <div class="flex gap-2 flex-wrap">
        <button class="flex items-center gap-1.5 bg-success-green text-white px-3 py-1.5 rounded-lg font-label-sm text-label-sm hover:brightness-110 active:scale-95 transition-all" onclick="openModal('modalScolarite')">
            <span class="material-symbols-outlined text-[18px]">add_circle</span>
            Paiement
        </button>
        <button class="flex items-center gap-1.5 bg-alert-red text-white px-3 py-1.5 rounded-lg font-label-sm text-label-sm hover:brightness-110 active:scale-95 transition-all" onclick="openModal('modalDepense')">
            <span class="material-symbols-outlined text-[18px]">payments</span>
            Dépense
        </button>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant">
        <p class="text-label-sm text-on-surface-variant">Solde actuel</p>
        <p class="font-headline-md text-headline-md text-primary mt-1">{{ number_format($currentBalance ?? 0, 0, ',', ' ') }} FCFA</p>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant">
        <p class="text-label-sm text-on-surface-variant">Total encaissé</p>
        <p class="font-headline-md text-headline-md text-success-green mt-1">{{ number_format($totalIncome ?? 0, 0, ',', ' ') }} FCFA</p>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant">
        <p class="text-label-sm text-on-surface-variant">Total dépensé</p>
        <p class="font-headline-md text-headline-md text-alert-red mt-1">{{ number_format($totalExpense ?? 0, 0, ',', ' ') }} FCFA</p>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant">
        <p class="text-label-sm text-on-surface-variant">Paiements effectués</p>
        <p class="font-headline-md text-headline-md text-on-surface mt-1">{{ $paymentCount ?? 0 }}</p>
    </div>
</div>

<!-- Tableau Scolarités -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-outline-variant flex items-center justify-between">
        <h4 class="font-headline-md text-headline-md">Scolarités</h4>
        <span class="text-label-sm text-on-surface-variant">{{ count($payments ?? []) }} enregistrement(s)</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-surface-container-low">
                <tr>
                    <th class="px-6 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant">Élève</th>
                    <th class="px-6 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant">Classe</th>
                    <th class="px-6 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant text-right">Montant</th>
                    <th class="px-6 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant text-right">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse($payments ?? [] as $payment)
                <tr class="hover:bg-surface-container-low/30 transition-colors">
                    <td class="px-6 py-4 font-body-md text-body-md text-on-surface">{{ $payment['student'] }}</td>
                    <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $payment['class'] }}</td>
                    <td class="px-6 py-4 font-body-md font-semibold text-success-green text-right">{{ number_format($payment['amount'], 0, ',', ' ') }} FCFA</td>
                    <td class="px-6 py-4 text-body-sm text-on-surface-variant text-right">{{ $payment['date'] }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-8 text-center text-on-surface-variant">Aucune scolarité enregistrée</td></tr>
                @endforelse
            </tbody>
            <tfoot class="bg-surface-container-low font-bold border-t border-outline-variant">
                <tr>
                    <td class="px-6 py-4 text-on-surface" colspan="2">Total</td>
                    <td class="px-6 py-4 text-success-green text-headline-md text-right" colspan="2">{{ number_format($totalIncome ?? 0, 0, ',', ' ') }} FCFA</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Tableau Dépenses -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden">
    <div class="px-6 py-4 border-b border-outline-variant flex items-center justify-between">
        <h4 class="font-headline-md text-headline-md">Dépenses</h4>
        <span class="text-label-sm text-on-surface-variant">{{ count($expenses ?? []) }} enregistrement(s)</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-surface-container-low">
                <tr>
                    <th class="px-6 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant">Libellé</th>
                    <th class="px-6 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant text-right">Montant</th>
                    <th class="px-6 py-3 font-label-sm text-label-sm uppercase text-on-surface-variant text-right">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse($expenses ?? [] as $expense)
                <tr class="hover:bg-surface-container-low/30 transition-colors">
                    <td class="px-6 py-4 font-body-md text-body-md text-on-surface">{{ $expense['label'] }}</td>
                    <td class="px-6 py-4 font-body-md font-semibold text-alert-red text-right">{{ number_format($expense['amount'], 0, ',', ' ') }} FCFA</td>
                    <td class="px-6 py-4 text-body-sm text-on-surface-variant text-right">{{ $expense['date'] }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-6 py-8 text-center text-on-surface-variant">Aucune dépense enregistrée</td></tr>
                @endforelse
            </tbody>
            <tfoot class="bg-surface-container-low font-bold border-t border-outline-variant">
                <tr>
                    <td class="px-6 py-4 text-on-surface">Total</td>
                    <td class="px-6 py-4 text-alert-red text-headline-md text-right" colspan="2">{{ number_format($totalExpense ?? 0, 0, ',', ' ') }} FCFA</td>
                </tr>
            </tfoot>
        </table>
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
        <form class="p-6 space-y-4" action="{{ route('personnel.comptabilite.scolarite.store') }}" method="POST">
            @csrf
            <div>
                <label class="block text-label-sm text-on-surface-variant mb-1.5">Élève</label>
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
                    <label class="block text-label-sm text-on-surface-variant mb-1.5">Date</label>
                    <input class="w-full rounded-lg border-outline-variant focus:border-success-green focus:ring-success-green text-body-sm" name="date_versement" type="date" value="{{ now()->toDateString() }}">
                </div>
            </div>
            <div>
                <label class="block text-label-sm text-on-surface-variant mb-1.5">Année académique</label>
                <input class="w-full rounded-lg border-outline-variant text-body-sm bg-surface-container-low" readonly type="text" value="{{ $currentYear ?? '2024-2025' }}">
            </div>
            <div class="pt-4 flex gap-3">
                <button class="flex-1 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant font-label-md hover:bg-surface-container-low transition-colors" onclick="closeModal('modalScolarite')" type="button">Annuler</button>
                <button class="flex-1 px-4 py-2.5 rounded-lg bg-success-green text-white font-label-md hover:brightness-110 shadow-lg" type="submit">Valider</button>
            </div>
        </form>
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
        <form class="p-6 space-y-4" action="{{ route('personnel.comptabilite.depense.store') }}" method="POST">
            @csrf
            <div>
                <label class="block text-label-sm text-on-surface-variant mb-1.5">Libellé</label>
                <textarea class="w-full rounded-lg border-outline-variant focus:border-alert-red focus:ring-alert-red text-body-sm" name="libel_depense" placeholder="Décrivez la dépense..." rows="3" required></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-label-sm text-on-surface-variant mb-1.5">Montant (FCFA)</label>
                    <input class="w-full rounded-lg border-outline-variant focus:border-alert-red focus:ring-alert-red text-body-sm" name="montant" step="100" type="number" required>
                </div>
                <div>
                    <label class="block text-label-sm text-on-surface-variant mb-1.5">Date</label>
                    <input class="w-full rounded-lg border-outline-variant focus:border-alert-red focus:ring-alert-red text-body-sm" name="date_depense" type="date" value="{{ now()->toDateString() }}">
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
            ['modalScolarite', 'modalDepense'].forEach(id => {
                const modal = document.getElementById(id);
                if (modal && modal.classList.contains('flex')) closeModal(id);
            });
        }
    });
</script>
@endpush