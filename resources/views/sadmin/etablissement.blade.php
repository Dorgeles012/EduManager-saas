@extends('sadmin.layouts.app')

@section('content')
<!-- Content Canvas -->
<div class="flex flex-col gap-8 max-w-max-width mx-auto w-full">
    <!-- Page Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="font-headline-lg text-headline-lg text-primary">Gestion de mes Établissements</h2>
            <p class="font-body-md text-body-md text-text-muted mt-1">Supervisez et gérez vos structures éducatives depuis un centre de contrôle unique.</p>
        </div>
        <button class="bg-primary-container text-white px-6 py-3 rounded-lg flex items-center gap-2 hover:opacity-90 shadow-md font-label-md text-label-md" onclick="openModal('modal-add')">
            <span class="material-symbols-outlined">add_business</span>
            Ajouter un établissement
        </button>
    </div>

    <!-- Stats Overview (Bento Style) -->
    @php
        $total = $etablissements?->count() ?? 0;
        $primaire = $etablissements?->where('type_etablissement', 'primaire')->count() ?? 0;
        $collegeLycee = ($etablissements?->whereIn('type_etablissement', ['college', 'lycee'])->count()) ?? 0;
        $univGrandeEcole = ($etablissements?->whereIn('type_etablissement', ['universite', 'grande_ecole'])->count()) ?? 0;
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-surface-container-lowest p-6 rounded-xl card-shadow border border-outline-variant flex flex-col">
            <span class="text-text-muted font-label-sm text-label-sm uppercase tracking-wider">Total Établissements</span>
            <span class="text-headline-xl font-headline-xl text-primary mt-2">{{ $total }}</span>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-xl card-shadow border border-outline-variant flex flex-col">
            <span class="text-text-muted font-label-sm text-label-sm uppercase tracking-wider">Primaire</span>
            <span class="text-headline-xl font-headline-xl text-primary mt-2">{{ $primaire }}</span>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-xl card-shadow border border-outline-variant flex flex-col">
            <span class="text-text-muted font-label-sm text-label-sm uppercase tracking-wider">Collèges / Lycées</span>
            <span class="text-headline-xl font-headline-xl text-warning-amber mt-2">{{ $collegeLycee }}</span>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-xl card-shadow border border-outline-variant flex flex-col">
            <span class="text-text-muted font-label-sm text-label-sm uppercase tracking-wider">Univ. / Grandes Écoles</span>
            <span class="text-headline-xl font-headline-xl text-secondary mt-2">{{ $univGrandeEcole }}</span>
        </div>
    </div>


    <!-- Main Table Section -->
    <div class="bg-surface-container-lowest rounded-xl card-shadow border border-outline-variant overflow-hidden">
        <div class="px-6 py-4 border-b border-surface-subtle flex items-center justify-between bg-white">
            <h3 class="font-headline-md text-headline-md text-on-surface">Liste des établissements</h3>
            <div class="flex gap-2">
                <div class="relative flex items-center">
                    <span class="material-symbols-outlined absolute left-3 text-on-surface-variant">search</span>
                    <input type="text" placeholder="Rechercher un établissement..." class="pl-10 pr-4 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-body-sm font-body-sm w-64 focus:ring-2 focus:ring-primary outline-none hover:border-primary">
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-subtle">
                        <th class="px-6 py-4 font-label-sm text-label-sm text-text-muted uppercase">Établissement</th>
                        <th class="px-6 py-4 font-label-sm text-label-sm text-text-muted uppercase">Type</th>
                        <th class="px-6 py-4 font-label-sm text-label-sm text-text-muted uppercase">Localisation</th>
                        <th class="px-6 py-4 font-label-sm text-label-sm text-text-muted uppercase">Email</th>
                        <th class="px-6 py-4 font-label-sm text-label-sm text-text-muted uppercase">Statut</th>
                        <th class="px-6 py-4 font-label-sm text-label-sm text-text-muted uppercase text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-subtle">
                    @forelse($etablissements ?? collect() as $index => $etablissement)
                        <tr class="hover:bg-surface-container-low">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div>
                                        <p class="font-label-md text-label-md text-on-surface">{{ $etablissement->nom }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-body-sm text-body-sm">{{ str_replace('_', ' ', $etablissement->type_etablissement) }}</td>
                            <td class="px-6 py-4 font-body-sm text-body-sm">{{ $etablissement->adresse }}</td>
                            <td class="px-6 py-4 font-body-sm text-body-sm">{{ $etablissement->email }}</td>
                            <td class="px-6 py-4">
                                @if(($etablissement->statut ?? 'active') === 'active')
                                    <span class="px-3 py-1 bg-success-green/10 text-success-green rounded-full text-label-sm font-label-sm">À jour</span>
                                @else
                                    <span class="px-3 py-1 bg-warning-amber/10 text-warning-amber rounded-full text-label-sm font-label-sm">En révision</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <!-- Icône Voir / Détails -->
                                    <a href="{{ route('sadmin.etablissements.show', $etablissement->id) }}" class="p-2 rounded-lg bg-surface-subtle hover:bg-primary/10 hover:text-primary transition-all" title="Voir les détails">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                    
                                    <!-- Icône Modifier -->
                                    <a href="{{ route('sadmin.etablissements.edit', $etablissement->id) }}" class="p-2 rounded-lg bg-surface-subtle hover:bg-warning-amber/10 hover:text-warning-amber transition-all" title="Modifier">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    
                                    <!-- Icône Supprimer -->
                                    <form method="POST" action="{{ route('sadmin.etablissements.destroy', $etablissement->id) }}" class="p-0 m-0" onsubmit="return confirmDeleteSweet(event, this);">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-lg bg-surface-subtle hover:bg-alert-red/10 hover:text-alert-red transition-all" title="Supprimer">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-text-muted">Aucun établissement trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-surface-subtle flex items-center justify-between border-t border-surface-subtle">
            <p class="text-body-sm text-label-sm text-text-muted">Affichage de {{ $etablissements?->count() ?? 0 }} sur {{ $total }} établissements</p>
            <div class="flex gap-1">
                <button class="w-8 h-8 flex items-center justify-center rounded border border-outline-variant bg-white text-text-muted">1</button>
                <button class="w-8 h-8 flex items-center justify-center rounded border border-transparent hover:bg-surface-container text-text-muted">2</button>
                <button class="w-8 h-8 flex items-center justify-center rounded border border-transparent hover:bg-surface-container text-text-muted">3</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajouter un établissement avec animation -->
<div class="fixed inset-0 z-[60] hidden items-center justify-center p-4 transition-all duration-300" id="modal-add">
    <div class="absolute inset-0 bg-on-surface/40 backdrop-blur-sm transition-opacity duration-300" id="modal-add-backdrop" style="opacity: 0;" onclick="closeModal('modal-add')"></div>
    
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden transform transition-all duration-300" id="modal-add-content" style="opacity: 0; transform: scale(0.95) translateY(-20px);">
        <div class="px-8 py-6 border-b border-surface-subtle flex justify-between items-center bg-primary-container text-white">
            <h3 class="font-headline-md text-headline-md">Nouvel Établissement</h3>
            <button class="hover:bg-white/10 rounded-full p-1 transition-all" onclick="closeModal('modal-add')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form method="POST" action="{{ route('sadmin.etablissements.store') }}">
            @csrf
            <div class="p-8 grid grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="font-label-md text-label-md text-on-surface">Nom de l'établissement</label>
                    <input name="nom" value="{{ old('nom') }}" class="w-full px-4 py-3 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none font-body-sm text-body-sm" placeholder="Ex: Lycée International" type="text">
                    @error('nom')
                        <p class="text-alert-red text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="font-label-md text-label-md text-on-surface">Anagramme</label>
                    <input name="acronyme" value="{{ old('acronyme') }}" class="w-full px-4 py-3 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none font-body-sm text-body-sm" placeholder="Ex: LI" type="text">
                    @error('acronyme')
                        <p class="text-alert-red text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="font-label-md text-label-md text-on-surface">Téléphone</label>
                    <input name="telephone" value="{{ old('telephone') }}" class="w-full px-4 py-3 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none font-body-sm text-body-sm" placeholder="+225 00 00 00 00 00" type="tel">
                    @error('telephone')
                        <p class="text-alert-red text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="font-label-md text-label-md text-on-surface">Type d'institution</label>
                    <select name="type_etablissement" class="w-full px-4 py-3 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none font-body-sm text-body-sm appearance-none bg-white">
                        @php
                            $currentType = old('type_etablissement');
                        @endphp
                        <option value="primaire" {{ $currentType === 'primaire' ? 'selected' : '' }}>Primaire</option>
                        <option value="college" {{ $currentType === 'college' ? 'selected' : '' }}>Collège</option>
                        <option value="lycee" {{ $currentType === 'lycee' ? 'selected' : '' }}>Lycée</option>
                        <option value="grande_ecole" {{ $currentType === 'grande_ecole' ? 'selected' : '' }}>Grande École</option>
                        <option value="universite" {{ $currentType === 'universite' ? 'selected' : '' }}>Université</option>
                    </select>
                    @error('type_etablissement')
                        <p class="text-alert-red text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-2 space-y-2">
                    <label class="font-label-md text-label-md text-on-surface">Localisation</label>
                    <input name="adresse" value="{{ old('adresse') }}" class="w-full px-4 py-3 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none font-body-sm text-body-sm" placeholder="Adresse complète" type="text">
                    @error('adresse')
                        <p class="text-alert-red text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-2 space-y-2">
                    <label class="font-label-md text-label-md text-on-surface">Email de contact</label>
                    <input name="email" value="{{ old('email') }}" class="w-full px-4 py-3 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none font-body-sm text-body-sm" placeholder="contact@etablissement.com" type="email">
                    @error('email')
                        <p class="text-alert-red text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="px-8 py-6 bg-surface-subtle flex justify-end gap-3">
                <button type="button" class="px-6 py-2 rounded-lg border border-outline-variant text-on-surface-variant font-label-md text-label-md hover:bg-white transition-all" onclick="closeModal('modal-add')">Annuler</button>
                <button type="submit" class="px-6 py-2 rounded-lg bg-primary-container text-white font-label-md text-label-md hover:opacity-90 transition-all">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        const backdrop = document.getElementById(modalId + '-backdrop');
        const content = document.getElementById(modalId + '-content');
        
        if (modal) {
            // Afficher le modal
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
            
            // Déclencher l'animation après un court délai
            setTimeout(() => {
                if (backdrop) backdrop.style.opacity = '1';
                if (content) {
                    content.style.opacity = '1';
                    content.style.transform = 'scale(1) translateY(0)';
                }
            }, 10);
        }
    }
    
    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        const backdrop = document.getElementById(modalId + '-backdrop');
        const content = document.getElementById(modalId + '-content');
        
        if (modal) {
            // Lancer l'animation de fermeture
            if (backdrop) backdrop.style.opacity = '0';
            if (content) {
                content.style.opacity = '0';
                content.style.transform = 'scale(0.95) translateY(-20px)';
            }
            
            // Attendre la fin de l'animation pour cacher
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }, 300);
        }
    }
    
    // Fermer le modal avec la touche Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('modal-add');
            if (modal && !modal.classList.contains('hidden')) {
                closeModal('modal-add');
            }
        }
    });
    
    // Fermer le modal en cliquant sur le backdrop
    document.addEventListener('click', function(e) {
        const backdrop = document.getElementById('modal-add-backdrop');
        if (backdrop && e.target === backdrop) {
            closeModal('modal-add');
        }
    });
</script>
@endsection