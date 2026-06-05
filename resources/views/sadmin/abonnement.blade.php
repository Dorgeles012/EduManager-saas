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
        <button class="bg-primary-container text-white px-6 py-3 rounded-lg flex items-center gap-2 hover:opacity-90 shadow-md font-label-md text-label-md" onclick="openModal('modal-add')">
            <span class="material-symbols-outlined" data-icon="add_circle">add_circle</span>
            Nouveau Plan
        </button>
    </div>
</div>

<!-- Dashboard-style Content Area -->
<div class="grid grid-cols-12 gap-6">
    <!-- Summary Stats -->
    <div class="col-span-12 md:col-span-4 bg-surface-container-lowest p-6 rounded-xl card-shadow border border-outline-variant flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-primary-fixed flex items-center justify-center text-primary">
            <span class="material-symbols-outlined text-[32px]" data-icon="inventory_2">inventory_2</span>
        </div>
        <div>
            <p class="text-label-sm text-outline uppercase tracking-wider">Total Plans</p>
            <p class="font-headline-md text-headline-md text-on-surface">{{ $plansCount ?? ($plans->count() ?? 0) }} Plans</p>
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
                    {{ \Carbon\Carbon::parse($lastUpdatedAt)->translatedFormat('d/m/Y, H:i') }}
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
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-subtle">
                    <tr>
                        <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Libellé du plan</th>
                        <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider text-right">Prix (FCFA)</th>
                        <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-subtle">
                    @forelse($plans ?? [] as $plan)
                        @php
                            $featuresArray = [];
                            $description = $plan->description ?? '';

                            if ($description !== '') {
                                $decoded = json_decode($description, true);

                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                    $decodedFeatures = $decoded['features'] ?? $decoded['data'] ?? $decoded;
                                    $featuresArray = is_array($decodedFeatures) ? $decodedFeatures : [];
                                } else {
                                    $featuresArray = preg_split("/\r?\n/", $description) ?: [];
                                }

                                $featuresArray = array_values(array_filter(array_map(
                                    fn($feature) => is_string($feature) ? trim($feature) : '',
                                    $featuresArray
                                )));
                            }
                        @endphp
                        <tr class="hover:bg-surface-dim transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-label-md text-label-md text-on-surface">{{ $plan->nom }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-primary-fixed text-primary rounded-full font-label-sm text-label-sm">{{ $plan->statut }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-body-md text-body-md font-semibold">{{ number_format((int) $plan->prix, 0, ',', ' ') }} FCFA</span>
                            </td>
                            <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $plan->created_at?->format('d/m/Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center gap-2">
                                    <button 
                                        onclick="openEditModal(this)"
                                        data-id="{{ $plan->id }}"
                                        data-name="{{ $plan->nom }}"
                                        data-type="{{ $plan->subscriptionType?->type ?? $plan->type }}"
                                        data-price="{{ $plan->prix }}"
                                        data-features='@json($featuresArray)'
                                        data-status="{{ $plan->statut }}"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-200 text-gray-900"
                                    >
                                        <span class="material-symbols-outlined text-[18px]" data-icon="edit">edit</span>
                                    </button>
                                    
                                    <form method="POST" action="{{ route('plans.destroy', $plan->id) }}" class="m-0" onsubmit="return confirmDeleteSweet(event, this)">
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
                            <td colspan="5" class="px-6 py-10 text-center text-body-sm text-on-surface-variant">
                                Aucun plan d'abonnement trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Nouveau Plan avec features -->
<div class="fixed inset-0 z-[100] hidden" id="modal-add">
    <div class="absolute inset-0 bg-on-background/50 backdrop-blur-sm" onclick="closeModal('modal-add')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl bg-surface-container-lowest rounded-xl shadow-2xl transform transition-all duration-300 modal-content scale-95 opacity-0" style="max-height: 90vh; display: flex; flex-direction: column;">
        <div class="p-8 pb-0">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-headline-md text-headline-md text-primary">Créer un Nouveau Plan</h3>
                <button class="p-2 hover:bg-surface-subtle rounded-full text-outline" onclick="closeModal('modal-add')">
                    <span class="material-symbols-outlined" data-icon="close">close</span>
                </button>
            </div>
        </div>
        
        <div class="overflow-y-auto px-8 pb-8" style="flex: 1;">
            <form class="space-y-6" method="POST" action="{{ route('plans.store') }}" id="addForm">
                @csrf

                <div class="space-y-2">
                    <label class="font-label-md text-label-md text-on-surface-variant block">Nom du Plan</label>
                    <input
                        class="w-full px-4 py-2.5 rounded-lg border border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary-fixed-dim outline-none"
                        placeholder="ex: Plan Gold Illimité"
                        type="text"
                        name="nom"
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
                        <label class="font-label-md text-label-md text-on-surface-variant block">Prix (FCFA)</label>
                        <input
                            class="w-full px-4 py-2.5 rounded-lg border border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary-fixed-dim outline-none"
                            placeholder="0"
                            type="number"
                            name="prix"
                            min="0"
                            required
                        >
                    </div>
                </div>

                <!-- Section Features avec check_circle -->
                <div class="space-y-3">
                    <label class="font-label-md text-label-md text-on-surface-variant block">Fonctionnalités incluses</label>
                    <div class="border border-outline-variant rounded-lg p-4 bg-surface-container-low">
                        <div class="space-y-3" id="addFeaturesContainer">
                            <!-- Les features seront ajoutées dynamiquement -->
                            <div class="feature-item flex items-center gap-3 group">
                                <button type="button" class="feature-toggle w-6 h-6 rounded-full border-2 border-primary bg-primary/10 flex items-center justify-center hover:border-primary transition-colors" data-checked="true">
                                    <span class="material-symbols-outlined text-primary text-sm">check_circle</span>
                                </button>
                                <input type="text" name="features[]" class="flex-1 px-3 py-2 rounded-lg border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary-fixed-dim outline-none" placeholder=" Ajouter des avantages" value="">
                                <button type="button" class="remove-feature text-error opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </div>
                        </div>
                        <button type="button" onclick="addFeatureField('addFeaturesContainer')" class="mt-3 text-primary hover:bg-primary/10 px-3 py-1.5 rounded-lg font-label-sm text-label-sm flex items-center gap-1 transition-colors">
                            <span class="material-symbols-outlined text-sm">add_circle</span>
                            Ajouter une fonctionnalité
                        </button>
                    </div>
                    <input type="hidden" name="features_json" id="addFeaturesJson">
                </div>

                <input type="hidden" name="statut" value="active">

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
</div>

<!-- Modal: Modifier Plan avec features -->
<div class="fixed inset-0 z-[100] hidden" id="modal-edit">
    <div class="absolute inset-0 bg-on-background/50 backdrop-blur-sm" onclick="closeModal('modal-edit')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl bg-surface-container-lowest rounded-xl shadow-2xl transform transition-all duration-300 modal-content scale-95 opacity-0" style="max-height: 90vh; display: flex; flex-direction: column;">
        <div class="p-8 pb-0">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-headline-md text-headline-md text-primary">Modifier le Plan</h3>
                <button class="p-2 hover:bg-surface-subtle rounded-full text-outline" onclick="closeModal('modal-edit')">
                    <span class="material-symbols-outlined" data-icon="close">close</span>
                </button>
            </div>
        </div>
        
        <div class="overflow-y-auto px-8 pb-8" style="flex: 1;">
            <form class="space-y-6" method="POST" id="editForm" action="">
                @csrf
                @method('PUT')
                
                <div class="space-y-2">
                    <label class="font-label-md text-label-md text-on-surface-variant block">Nom du Plan</label>
                    <input
                        id="edit_name"
                        class="w-full px-4 py-2.5 rounded-lg border border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary-fixed-dim outline-none"
                        placeholder="ex: Plan Gold Illimité"
                        type="text"
                        name="nom"
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
                        <label class="font-label-md text-label-md text-on-surface-variant block">Prix (FCFA)</label>
                        <input
                            id="edit_price"
                            class="w-full px-4 py-2.5 rounded-lg border border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary-fixed-dim outline-none"
                            placeholder="0"
                            type="number"
                            name="prix"
                            min="0"
                            required
                        >
                    </div>
                </div>

                <!-- Section Features avec check_circle -->
                <div class="space-y-3">
                    <label class="font-label-md text-label-md text-on-surface-variant block">Fonctionnalités incluses</label>
                    <div class="border border-outline-variant rounded-lg p-4 bg-surface-container-low">
                        <div class="space-y-3" id="editFeaturesContainer">
                            <!-- Les features seront chargées dynamiquement -->
                        </div>
                        <button type="button" onclick="addFeatureField('editFeaturesContainer')" class="mt-3 text-primary hover:bg-primary/10 px-3 py-1.5 rounded-lg font-label-sm text-label-sm flex items-center gap-1 transition-colors">
                            <span class="material-symbols-outlined text-sm">add_circle</span>
                            Ajouter une fonctionnalité
                        </button>
                    </div>
                    <input type="hidden" name="features_json" id="editFeaturesJson">
                </div>

                <div class="space-y-2">
                    <label class="font-label-md text-label-md text-on-surface-variant block">Statut</label>
                    <select
                        id="edit_status"
                        class="w-full px-4 py-2.5 rounded-lg border border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary-fixed-dim outline-none"
                        name="statut"
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

    function addFeatureField(containerId) {
        const container = document.getElementById(containerId);
        const newFeatureDiv = document.createElement('div');
        newFeatureDiv.className = 'feature-item flex items-center gap-3 group';
        newFeatureDiv.innerHTML = `
            <button type="button" class="feature-toggle w-6 h-6 rounded-full border-2 border-outline bg-white flex items-center justify-center hover:border-primary transition-colors" data-checked="true">
                <span class="material-symbols-outlined text-primary text-sm">check_circle</span>
            </button>
            <input type="text" name="features[]" class="flex-1 px-3 py-2 rounded-lg border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary-fixed-dim outline-none" placeholder="Ex: Nouvelle fonctionnalité" value="">
            <button type="button" onclick="this.closest('.feature-item').remove()" class="remove-feature text-error opacity-0 group-hover:opacity-100 transition-opacity">
                <span class="material-symbols-outlined text-[18px]">delete</span>
            </button>
        `;
        container.appendChild(newFeatureDiv);
        attachToggleListener(newFeatureDiv.querySelector('.feature-toggle'));
    }

    function attachToggleListener(button) {
        if (!button) return;
        button.addEventListener('click', function() {
            const isChecked = this.getAttribute('data-checked') === 'true';
            const checkIcon = this.querySelector('.material-symbols-outlined');
            if (!checkIcon) return;

            if (!isChecked) {
                this.setAttribute('data-checked', 'true');
                checkIcon.classList.remove('hidden');
                this.classList.add('border-primary', 'bg-primary/10');
            } else {
                this.setAttribute('data-checked', 'false');
                checkIcon.classList.add('hidden');
                this.classList.remove('border-primary', 'bg-primary/10');
            }
        });
    }

    function initAllToggles(containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;
        container.querySelectorAll('.feature-toggle').forEach(toggle => attachToggleListener(toggle));
    }

    function getFeaturesArray(containerId) {
        const container = document.getElementById(containerId);
        const features = [];
        if (!container) return features;

        container.querySelectorAll('.feature-item').forEach(item => {
            const toggle = item.querySelector('.feature-toggle');
            const isChecked = toggle?.getAttribute('data-checked') === 'true';
            const input = item.querySelector('input[name="features[]"]');
            const featureText = input?.value.trim();

            if (featureText && isChecked) features.push(featureText);
        });

        return features;
    }

    function setupFormSubmit(formId, containerId, jsonFieldId) {
        const form = document.getElementById(formId);
        if (!form) return;

        form.addEventListener('submit', function() {
            const features = getFeaturesArray(containerId);
            const container = document.getElementById(containerId);

            if (container) {
                container.querySelectorAll('.feature-item').forEach(item => {
                    const toggle = item.querySelector('.feature-toggle');
                    const input = item.querySelector('input[name="features[]"]');
                    const isChecked = toggle?.getAttribute('data-checked') === 'true';
                    const featureText = input?.value.trim();

                    if (input) {
                        input.disabled = !isChecked || !featureText;
                    }
                });
            }

            if (jsonFieldId) {
                const hidden = document.getElementById(jsonFieldId);
                if (hidden) hidden.value = JSON.stringify(features);
            }
        });
    }

    function normalizeFeaturesData(featuresData) {
        if (typeof featuresData === 'string') {
            const raw = featuresData.trim();
            if (!raw) return [];

            try {
                featuresData = JSON.parse(raw);
            } catch (e) {
                return raw
                    .split(/\r?\n/)
                    .map(feature => feature.trim())
                    .filter(Boolean);
            }
        }

        if (featuresData && typeof featuresData === 'object' && !Array.isArray(featuresData)) {
            if (Array.isArray(featuresData.features)) featuresData = featuresData.features;
            else if (Array.isArray(featuresData.data)) featuresData = featuresData.data;
            else featuresData = Object.values(featuresData);
        }

        if (!Array.isArray(featuresData)) return [];

        return featuresData
            .map(feature => {
                if (typeof feature === 'string') return feature;
                if (feature && typeof feature === 'object') {
                    return feature.text || feature.name || feature.feature || '';
                }
                return String(feature ?? '');
            })
            .map(feature => feature.trim())
            .filter(Boolean);
    }

    function loadFeatures(containerId, featuresData) {
        const container = document.getElementById(containerId);
        if (!container) return;

        container.innerHTML = '';
        featuresData = normalizeFeaturesData(featuresData);

        if (Array.isArray(featuresData) && featuresData.length > 0) {
            featuresData.forEach(feature => {
                const featureText = String(feature ?? '').trim();
                if (!featureText) return;

                const featureDiv = document.createElement('div');
                featureDiv.className = 'feature-item flex items-center gap-3 group';
                featureDiv.innerHTML = `
                    <button type="button" class="feature-toggle w-6 h-6 rounded-full border-2 border-primary bg-primary/10 flex items-center justify-center hover:border-primary transition-colors" data-checked="true">
                        <span class="material-symbols-outlined text-primary text-sm">check_circle</span>
                    </button>
                    <input type="text" name="features[]" class="flex-1 px-3 py-2 rounded-lg border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary-fixed-dim outline-none" placeholder="Ex: Fonctionnalité" value="${escapeHtml(featureText)}">
                    <button type="button" onclick="this.closest('.feature-item').remove()" class="remove-feature text-error opacity-0 group-hover:opacity-100 transition-opacity">
                        <span class="material-symbols-outlined text-[18px]">delete</span>
                    </button>
                `;
                container.appendChild(featureDiv);
                attachToggleListener(featureDiv.querySelector('.feature-toggle'));
            });
        }

        if (container.children.length === 0) addFeatureField(containerId);
    }

    function escapeHtml(text) {
        if (text === null || text === undefined) return '';
        const div = document.createElement('div');
        div.textContent = String(text);
        return div.innerHTML;
    }

    function openEditModal(button) {
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        const type = button.getAttribute('data-type');
        const price = button.getAttribute('data-price');
        const status = button.getAttribute('data-status');

        const features = normalizeFeaturesData(button.getAttribute('data-features'));

        const nameInput = document.getElementById('edit_name');
        const priceInput = document.getElementById('edit_price');
        const statusSelect = document.getElementById('edit_status');
        const typeSelect = document.getElementById('edit_type');

        if (nameInput) nameInput.value = name || '';
        if (priceInput) priceInput.value = price || 0;
        if (statusSelect) statusSelect.value = status || 'active';

        if (typeSelect && type) {
            for (let i = 0; i < typeSelect.options.length; i++) {
                if (typeSelect.options[i].value === type) {
                    typeSelect.selectedIndex = i;
                    break;
                }
            }
        }

        loadFeatures('editFeaturesContainer', features);

        const form = document.getElementById('editForm');
        if (form && id) form.action = `/plans/${id}`;

        openModal('modal-edit');
    }

    function confirmDeleteSweet(event, form) {
        event.preventDefault();
        Swal.fire({
            title: 'Confirmation',
            text: 'Êtes-vous sûr de vouloir supprimer ce plan ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
        return false;
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('click', function(event) {
            const removeButton = event.target.closest('.remove-feature');
            if (removeButton) {
                removeButton.closest('.feature-item')?.remove();
            }
        });

        setTimeout(() => {
            initAllToggles('addFeaturesContainer');
            initAllToggles('editFeaturesContainer');

            const addContainer = document.getElementById('addFeaturesContainer');
            if (addContainer && addContainer.children.length === 0) addFeatureField('addFeaturesContainer');

            setupFormSubmit('addForm', 'addFeaturesContainer', 'addFeaturesJson');
            setupFormSubmit('editForm', 'editFeaturesContainer', 'editFeaturesJson');
        }, 100);
    });
</script>
@endsection
