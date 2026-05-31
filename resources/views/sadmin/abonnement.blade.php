@extends('sadmin.layouts.app')

@section('content')

<!-- Page Header -->
<div class="flex justify-between items-end mb-8">
    <div>
        <nav class="flex items-center gap-2 text-label-sm text-outline mb-2"></nav>
        <h2 class="font-headline-lg text-headline-lg text-primary">Gestion des Plans d'Abonnement</h2>
    </div>
    <div class="flex gap-4">
        <button class="px-6 py-2.5 border border-secondary text-secondary rounded-lg font-label-md text-label-md flex items-center gap-2 hover:bg-secondary hover:text-on-secondary" onclick="openModal('modal-types')">
            <span class="material-symbols-outlined" data-icon="category">category</span>
            Gérer les Types
        </button>
        <button class="bg-primary-container text-white px-6 py-3 rounded-lg flex items-center gap-2 hover:opacity-90 shadow-md font-label-md text-label-md"  onclick="openModal('modal-add')">
            <span class="material-symbols-outlined" data-icon="add_circle">add_circle</span>
            Nouveau Plan
        </button>
    </div>
</div>

<!-- Dashboard-style Content Area -->
<div class="grid grid-cols-12 gap-6">
    <!-- Summary Stats (Bento Grid Style) -->
    <div class="col-span-12 md:col-span-4 bg-surface-container-lowest p-6 rounded-xl card-shadow border border-outline-variant flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-primary-fixed flex items-center justify-center text-primary">
            <span class="material-symbols-outlined text-[32px]" data-icon="inventory_2">inventory_2</span>
        </div>
        <div>
            <p class="text-label-sm text-outline uppercase tracking-wider">Total Plans</p>
            <p class="font-headline-md text-headline-md text-on-surface">{{ $activeCount ?? 0 }} Plans Actifs</p>

        </div>
    </div>

    <div class="col-span-12 md:col-span-4 bg-surface-container-lowest p-6 rounded-xl card-shadow border border-outline-variant flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-surface-container-high flex items-center justify-center text-primary">
            <span class="material-symbols-outlined text-[32px]" data-icon="calendar_today">calendar_today</span>
        </div>
        <div>
            <p class="text-label-sm text-outline uppercase tracking-wider">Dernière Mise à Jour</p>
            <p class="font-headline-md text-headline-md text-on-surface">
                @if(!empty($lastUpdatedAt))
                    {{ 
                        \Carbon\Carbon::parse($lastUpdatedAt)->translatedFormat('d/m/Y, H:i')
                    }}
                @else
                    —
                @endif
            </p>
        </div>

    </div>

    <!-- Main Table Card -->
    <div class="col-span-12 bg-surface-container-lowest rounded-xl card-shadow border border-outline-variant overflow-hidden">
        <div class="px-6 py-5 border-b border-surface-subtle flex justify-between items-center">
            <h3 class="font-headline-md text-headline-md text-on-surface">Liste des Plans</h3>
            <div class="flex gap-2">
                <button class="p-2 hover:bg-surface-subtle rounded-lg text-outline"></button>
                <button class="p-2 hover:bg-surface-subtle rounded-lg text-outline"></button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-subtle">
                    <tr>
                        <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Libellé du plan</th>
                        <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Type</th>
                        <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider text-right">Montant (FCFA)</th>
                        <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Date et Heure</th>
                        <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Créé par</th>
                        <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-subtle">
                    @forelse($subscriptions ?? [] as $sub)
                        <tr class="hover:bg-surface-container">
                            <td class="px-6 py-4">
                                <span class="font-label-md text-label-md text-on-surface">{{ $sub->name }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-primary-fixed text-primary rounded-full font-label-sm text-label-sm">{{ $sub->type }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-body-md text-body-md font-semibold">{{ number_format((int) $sub->price, 0, ',', ' ') }} FCFA</span>
                            </td>
                            <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $sub->created_at?->format('d/m/Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-surface-container-high text-[10px] flex items-center justify-center font-bold text-primary">{{ strtoupper(mb_substr($sub->name, 0, 2)) }}</div>
                                    <span class="text-body-sm">{{ $sub->created_by ?? '—' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center gap-2">
                                    <button 
                                        onclick="openEditModal(this)"
                                        data-id="{{ $sub->id }}"
                                        data-name="{{ $sub->name }}"
                                        data-type="{{ $sub->type }}"
                                        data-price="{{ $sub->price }}"
                                        data-duration="{{ $sub->duration }}"
                                        data-status="{{ $sub->status }}"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-primary-fixed text-primary">
                                        <span class="material-symbols-outlined text-[18px]" data-icon="edit">edit</span>
                                    </button>

                                    <form method="POST" action="{{ route('subscriptions.destroy', $sub->id) }}" class="m-0" onsubmit="return confirmDeleteSweet(event, this)">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-error-container text-error">
                                            <span class="material-symbols-outlined text-[18px]" data-icon="delete">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-body-sm text-on-surface-variant">
                                Aucun abonnement actif.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Nouveau Plan -->
<div class="fixed inset-0 z-[100] hidden" id="modal-add">
    <div class="absolute inset-0 bg-on-background/50 backdrop-blur-sm" onclick="closeModal('modal-add')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg bg-surface-container-lowest rounded-xl shadow-2xl p-8 transform transition-all duration-300 modal-content scale-95 opacity-0">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-headline-md text-headline-md text-primary">Créer un Nouveau Plan</h3>
            <button class="p-2 hover:bg-surface-subtle rounded-full text-outline" onclick="closeModal('modal-add')">
                <span class="material-symbols-outlined" data-icon="close">close</span>
            </button>
        </div>
        <form class="space-y-6" method="POST" action="{{ route('subscriptions.store') }}">
            @csrf
            <div class="space-y-2">
                <label class="font-label-md text-label-md text-on-surface-variant block">Libellé du Plan</label>
                <input
                    class="w-full px-4 py-2.5 rounded-lg border border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary-fixed-dim outline-none"
                    placeholder="ex: Plan Gold Illimité"
                    type="text"
                    name="name"
                    required
                >
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="font-label-md text-label-md text-on-surface-variant block">Type d'abonnement</label>
                    <select
                        class="w-full px-4 py-2.5 rounded-lg border border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary-fixed-dim outline-none"
                        name="type"
                        required
                    >
                        @foreach(($subscriptionTypes ?? []) as $t)
                            <option value="{{ $t->type }}">{{ $t->type }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="font-label-md text-label-md text-on-surface-variant block">Montant (FCFA)</label>
                    <input
                        class="w-full px-4 py-2.5 rounded-lg border border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary-fixed-dim outline-none"
                        placeholder="0.00"
                        type="number"
                        name="price"
                        min="0"
                        required
                    >
                </div>
            </div>

            <input type="hidden" name="duration" id="subscription-duration" required>
            <input type="hidden" name="status" value="active">

            <div class="pt-4 flex gap-4">
                <button class="flex-1 px-6 py-2.5 bg-surface-container-high text-on-surface-variant rounded-lg font-label-md text-label-md hover:bg-surface-dim" onclick="closeModal('modal-add')" type="button">
                    Annuler
                </button>
                <button class="flex-1 px-6 py-2.5 bg-primary text-on-primary rounded-lg font-label-md text-label-md shadow-md" type="submit">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Modifier Plan (avec données pré-remplies) -->
<div class="fixed inset-0 z-[100] hidden" id="modal-edit">
    <div class="absolute inset-0 bg-on-background/50 backdrop-blur-sm" onclick="closeModal('modal-edit')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg bg-surface-container-lowest rounded-xl shadow-2xl p-8 transform transition-all duration-300 modal-content scale-95 opacity-0">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-headline-md text-headline-md text-primary">Modifier le Plan</h3>
            <button class="p-2 hover:bg-surface-subtle rounded-full text-outline" onclick="closeModal('modal-edit')">
                <span class="material-symbols-outlined" data-icon="close">close</span>
            </button>
        </div>
        <form class="space-y-6" method="POST" id="editForm" action="">
            @csrf
            @method('PUT')
            <div class="space-y-2">
                <label class="font-label-md text-label-md text-on-surface-variant block">Libellé du Plan</label>
                <input
                    id="edit_name"
                    class="w-full px-4 py-2.5 rounded-lg border border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary-fixed-dim outline-none"
                    placeholder="ex: Plan Gold Illimité"
                    type="text"
                    name="name"
                    required
                >
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="font-label-md text-label-md text-on-surface-variant block">Type d'abonnement</label>
                    <select
                        id="edit_type"
                        class="w-full px-4 py-2.5 rounded-lg border border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary-fixed-dim outline-none"
                        name="type"
                        required
                    >
                        @foreach(($subscriptionTypes ?? []) as $t)
                            <option value="{{ $t->type }}">{{ $t->type }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="font-label-md text-label-md text-on-surface-variant block">Montant (FCFA)</label>
                    <input
                        id="edit_price"
                        class="w-full px-4 py-2.5 rounded-lg border border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary-fixed-dim outline-none"
                        placeholder="0.00"
                        type="number"
                        name="price"
                        min="0"
                        required
                    >
                </div>
            </div>

            <div class="space-y-2">
                <label class="font-label-md text-label-md text-on-surface-variant block">Durée (mois)</label>
                <input
                    id="edit_duration"
                    class="w-full px-4 py-2.5 rounded-lg border border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary-fixed-dim outline-none"
                    type="number"
                    name="duration"
                    min="1"
                    required
                >
            </div>

            <div class="space-y-2">
                <label class="font-label-md text-label-md text-on-surface-variant block">Statut</label>
                <select
                    id="edit_status"
                    class="w-full px-4 py-2.5 rounded-lg border border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary-fixed-dim outline-none"
                    name="status"
                    required
                >
                    <option value="active">Actif</option>
                    <option value="inactive">Inactif</option>
                </select>
            </div>

            <div class="pt-4 flex gap-4">
                <button class="flex-1 px-6 py-2.5 bg-surface-container-high text-on-surface-variant rounded-lg font-label-md text-label-md hover:bg-surface-dim" onclick="closeModal('modal-edit')" type="button">
                    Annuler
                </button>
                <button class="flex-1 px-6 py-2.5 bg-primary text-on-primary rounded-lg font-label-md text-label-md shadow-md" type="submit">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Gérer les Types -->
<div class="fixed inset-0 z-[100] hidden" id="modal-types">
    <div class="absolute inset-0 bg-on-background/50 backdrop-blur-sm" onclick="closeModal('modal-types')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-surface-container-lowest rounded-xl shadow-2xl p-8 transform transition-all duration-300 modal-content scale-95 opacity-0">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-headline-md text-headline-md text-primary">Types d'Abonnement</h3>
            <button class="p-2 hover:bg-surface-subtle rounded-full text-outline" onclick="closeModal('modal-types')">
                <span class="material-symbols-outlined" data-icon="close">close</span>
            </button>
        </div>
        <div class="space-y-4">
            <form class="flex gap-2" method="POST" action="{{ route('subscription-types.store') }}">
                @csrf
                <input
                    class="flex-1 px-4 py-2 rounded-lg border border-outline-variant outline-none"
                    placeholder="Nouveau type... (ex: Mensuel)"
                    type="text"
                    name="type"
                    required
                >
                <button class="bg-secondary p-2 text-on-secondary rounded-lg" type="submit">
                    <span class="material-symbols-outlined" data-icon="add">add</span>
                </button>
            </form>

            <div class="space-y-2 border-t border-surface-subtle pt-4 max-h-60 overflow-y-auto">
                @forelse($subscriptionTypes ?? [] as $t)
                    <div class="flex justify-between items-center p-3 bg-surface-subtle rounded-lg">
                        <span class="font-label-md">{{ $t->type }}</span>

                        <form
                            method="POST"
                            action="{{ route('subscription-types.destroy', $t->id) }}"
                            onsubmit="return confirmDeleteSweet(event, this)"
                            class="m-0"
                        >
                            @csrf
                            @method('DELETE')
                            <button class="text-error" type="submit" aria-label="Supprimer">
                                <span class="material-symbols-outlined text-[18px]" data-icon="delete">delete</span>
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="py-6 text-center text-body-sm text-on-surface-variant">
                        Aucun type d'abonnement.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        const content = modal.querySelector('.modal-content');
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        const content = modal.querySelector('.modal-content');
        content.classList.add('scale-95', 'opacity-0');
        content.classList.remove('scale-100', 'opacity-100');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // Fonction pour ouvrir le modal de modification avec les données déjà dans les cases
    function openEditModal(button) {
        // Récupérer les données depuis les attributs data-*
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        const type = button.getAttribute('data-type');
        const price = button.getAttribute('data-price');
        const duration = button.getAttribute('data-duration');
        const status = button.getAttribute('data-status');
        
        // Remplir le formulaire avec les données
        document.getElementById('edit_name').value = name || '';
        document.getElementById('edit_type').value = type || '';
        document.getElementById('edit_price').value = price || 0;
        document.getElementById('edit_duration').value = duration || 1;
        document.getElementById('edit_status').value = status || 'active';
        
        // Mettre à jour l'action du formulaire
        const form = document.getElementById('editForm');
        form.action = `/subscriptions/${id}`;
        
        // Ouvrir le modal
        openModal('modal-edit');
    }

    // Déduction de duration (mois) depuis le type
    (function () {
        const typeSelect = document.querySelector('#modal-add select[name="type"]');
        const durationInput = document.getElementById('subscription-duration');

        if (!typeSelect || !durationInput) return;

        const map = {
            'Mensuel': 1,
            'Trimestriel': 3,
            'Annuel': 12,
            'mensuel': 1,
            'trimestriel': 3,
            'annuel': 12,
        };

        function computeDuration() {
            const t = (typeSelect.value || '').trim();
            const d = map[t] ?? map[t.toLowerCase()] ?? null;
            if (d) durationInput.value = d;
        }

        typeSelect.addEventListener('change', computeDuration);
        computeDuration();
    })();
</script>
@endsection