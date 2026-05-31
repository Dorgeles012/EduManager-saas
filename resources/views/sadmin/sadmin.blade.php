@extends('sadmin.layouts.app')

@section('content')
<div class="container-fluid px-0">
    <!-- DASHBOARD HEADER -->
    <div class="flex justify-between items-end mb-6">
        <div>
            <h2 class="font-headline-lg text-headline-lg text-primary">Gestion des Super Administrateurs</h2>
            <p class="text-text-muted mt-1 text-sm">Consultez, ajoutez et gérez les accès de haut niveau au portail.</p>
        </div>
        @if(Auth::user() && Auth::user()->role === 'SADMIN')

       <button type="button" class="bg-primary-container text-white px-6 py-3 rounded-lg flex items-center gap-2 hover:opacity-90 shadow-md font-label-md text-label-md"  onclick="toggleModal('addSadminModal')">
          <span class="material-symbols-outlined text-base">add_circle</span> Ajouter un SAdmin
     </button>

        @endif
    </div>

    <!-- STATS ROW -->
    <div class="grid grid-cols-12 gap-4 mb-6">
        <div class="col-span-12 md:col-span-4 bg-white p-4 rounded-lg ambient-shadow border border-slate-100">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-label-sm text-text-muted uppercase tracking-wider text-xs">Total SAdmins</p>
                    <h2 class="font-headline-xl text-2xl mt-1">{{ $stats['total'] ?? ($sadmins ?? collect())->total() ?? ($sadmins ?? collect())->count() }}</h2>
                </div>
                <div class="p-2 bg-primary/10 rounded-lg">
                    <span class="material-symbols-outlined text-primary text-base">shield_person</span>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1.5 text-success-green text-sm">
                <span class="material-symbols-outlined text-sm">trending_up</span>
                <span>+{{ $newThisMonth ?? 0 }} ce mois-ci</span>
            </div>
        </div>
        <div class="col-span-12 md:col-span-4 bg-white p-4 rounded-lg ambient-shadow border border-slate-100">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-label-sm text-text-muted uppercase tracking-wider text-xs">Actifs</p>
                    <h2 class="font-headline-xl text-2xl mt-1">
                        @php
                            $sadminsCollection = $sadmins ?? collect();
                            if(method_exists($sadminsCollection, 'items')) {
                                $activeCount = collect($sadminsCollection->items())->where('statut', 'active')->count();
                            } else {
                                $activeCount = $sadminsCollection->where('statut', 'active')->count();
                            }
                        @endphp
                        {{ $activeCount }}
                    </h2>
                </div>
                <div class="p-2 bg-success-green/10 rounded-lg">
                    <span class="material-symbols-outlined text-success-green text-base">verified</span>
                </div>
            </div>
        </div>
        
    </div>

    <!-- DATA TABLE CARD -->
    <div class="bg-white rounded-lg ambient-shadow border border-slate-100 overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex justify-between items-center">
            <h3 class="font-headline-md text-base">Liste des Administrateurs</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead class="bg-surface-container-low">
                    <tr>
                        <th class="px-4 py-3 font-label-sm text-text-muted uppercase text-xs">Nom</th>
                        <th class="px-4 py-3 font-label-sm text-text-muted uppercase text-xs">Prénom</th>
                        <th class="px-4 py-3 font-label-sm text-text-muted uppercase text-xs">Email</th>
                        <th class="px-4 py-3 font-label-sm text-text-muted uppercase text-xs">Téléphone</th>
                        <th class="px-4 py-3 font-label-sm text-text-muted uppercase text-xs">Statut</th>
                        <th class="px-4 py-3 font-label-sm text-text-muted uppercase text-xs">Date création</th>
                        <th class="px-4 py-3 font-label-sm text-text-muted uppercase text-xs text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @php
                        $sadminsList = ($sadmins ?? collect());
                        if(method_exists($sadminsList, 'items')) {
                            $sortedItems = collect($sadminsList->items())->sortByDesc('id');
                            $sadminsList->setCollection($sortedItems);
                        } else {
                            $sadminsList = $sadminsList->sortByDesc('id');
                        }
                    @endphp
                    
                    @forelse($sadminsList as $sadmin)
                    <tr class="hover:bg-surface-container-low/30 transition-colors">
                        <td class="px-4 py-3 text-sm font-semibold text-on-surface">{{ $sadmin->nom }}</td>
                        <td class="px-4 py-3 text-sm">{{ $sadmin->prenom }}</td>
                        <td class="px-4 py-3 text-sm text-text-muted">{{ $sadmin->email }}</td>
                        <td class="px-4 py-3 text-sm">{{ $sadmin->telephone }}</td>
                        <td class="px-4 py-3">
                            @php
                                $statut = $sadmin->statut ?? 'active';
                            @endphp
                            @if($statut === 'active')
                                <span class="bg-teal-50 text-success-green px-2 py-0.5 rounded-full text-xs font-semibold">Actif</span>
                            @else
                                <span class="bg-amber-50 text-warning-amber px-2 py-0.5 rounded-full text-xs font-semibold">Inactif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-text-muted">{{ optional($sadmin->created_at)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button type="button" class="p-2 rounded-lg bg-primary/10 text-primary hover:bg-primary/20 transition-all" onclick="openEditModal(this)"
                                    data-id="{{ $sadmin->id }}"
                                    data-nom="{{ $sadmin->nom }}"
                                    data-prenom="{{ $sadmin->prenom }}"
                                    data-email="{{ $sadmin->email }}"
                                    data-telephone="{{ $sadmin->telephone }}"
                                    title="Modifier">
                                    <span class="material-symbols-outlined text-sm">edit</span>
                                </button>

                                <form method="POST" action="{{ route('sadmin.destroy', $sadmin) }}" class="m-0" onsubmit="return confirm('Supprimer ce SAdmin ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg bg-alert-red/10 text-alert-red hover:bg-alert-red/20 transition-all" title="Supprimer">
                                        <span class="material-symbols-outlined text-sm">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-text-muted">
                            <span class="material-symbols-outlined text-2xl mb-1 opacity-50">admin_panel_settings</span>
                            <p class="text-base">Aucun Super Administrateur trouvé.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(($sadmins ?? collect())->count() > 0 && method_exists($sadmins, 'links'))
        <div class="px-4 py-3 bg-slate-50 flex justify-between items-center text-sm text-text-muted border-t border-slate-100">
            <span>
                Affichage de {{ $sadmins->firstItem() }} à {{ $sadmins->lastItem() }} 
                sur {{ $sadmins->total() }} administrateurs
            </span>
            <div class="flex gap-1">
                @if($sadmins->onFirstPage())
                    <span class="w-7 h-7 flex items-center justify-center rounded border border-slate-200 bg-slate-100 text-slate-400">
                        <span class="material-symbols-outlined text-sm">chevron_left</span>
                    </span>
                @else
                    <a href="{{ $sadmins->previousPageUrl() }}" class="w-7 h-7 flex items-center justify-center rounded border border-slate-200 hover:bg-white transition-colors">
                        <span class="material-symbols-outlined text-sm">chevron_left</span>
                    </a>
                @endif

                @foreach($sadmins->getUrlRange(1, $sadmins->lastPage()) as $page => $url)
                    @if($page == $sadmins->currentPage())
                        <span class="w-7 h-7 flex items-center justify-center rounded bg-primary text-white font-bold text-sm">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="w-7 h-7 flex items-center justify-center rounded border border-slate-200 hover:bg-white transition-colors text-sm">{{ $page }}</a>
                    @endif
                @endforeach

                @if($sadmins->hasMorePages())
                    <a href="{{ $sadmins->nextPageUrl() }}" class="w-7 h-7 flex items-center justify-center rounded border border-slate-200 hover:bg-white transition-colors">
                        <span class="material-symbols-outlined text-sm">chevron_right</span>
                    </a>
                @else
                    <span class="w-7 h-7 flex items-center justify-center rounded border border-slate-200 bg-slate-100 text-slate-400">
                        <span class="material-symbols-outlined text-sm">chevron_right</span>
                    </span>
                @endif
            </div>
        </div>
        @elseif(($sadmins ?? collect())->count() > 0)
        <div class="px-4 py-3 bg-slate-50 flex justify-between items-center text-sm text-text-muted border-t border-slate-100">
            <span>Affichage de {{ ($sadmins ?? collect())->count() }} administrateurs</span>
        </div>
        @endif
    </div>
</div>

<!-- MODAL: AJOUTER SADMIN avec animation -->
<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4 transition-all duration-300" id="addSadminModal">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300" id="addModalBackdrop" style="opacity: 0;" onclick="toggleModal('addSadminModal')"></div>
    
    <div class="relative bg-white w-full max-w-2xl max-h-[90vh] rounded-2xl shadow-2xl overflow-hidden flex flex-col transform transition-all duration-300" id="addModalContent" style="opacity: 0; transform: scale(0.95) translateY(-20px);">
        
        <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-surface-container-low flex-shrink-0">
            <h3 class="font-headline-md text-base text-primary flex items-center gap-2">
                <span class="material-symbols-outlined text-base">person_add</span>
                Inscrire un SAdmin
            </h3>
            <button type="button" class="p-2 hover:bg-slate-200 rounded-full transition-colors" onclick="toggleModal('addSadminModal')">
                <span class="material-symbols-outlined text-base">close</span>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto custom-scrollbar">
            <form method="POST" action="{{ route('sadmin.store') }}" id="addSadminForm">
                @csrf
                <div class="p-8">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="col-span-1">
                            <label class="block text-label-sm text-on-surface-variant mb-2 text-sm">Nom</label>
                            <input type="text" name="nom" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-base @error('nom') border-alert-red @enderror" value="{{ old('nom') }}" required>
                            @error('nom')<div class="text-alert-red text-xs mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-span-1">
                            <label class="block text-label-sm text-on-surface-variant mb-2 text-sm">Prénom</label>
                            <input type="text" name="prenom" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-base @error('prenom') border-alert-red @enderror" value="{{ old('prenom') }}" required>
                            @error('prenom')<div class="text-alert-red text-xs mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-span-2">
                            <label class="block text-label-sm text-on-surface-variant mb-2 text-sm">Email</label>
                            <input type="email" name="email" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-base @error('email') border-alert-red @enderror" value="{{ old('email') }}" required>
                            @error('email')<div class="text-alert-red text-xs mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-span-2">
                            <label class="block text-label-sm text-on-surface-variant mb-2 text-sm">Téléphone</label>
                            <input type="text" name="telephone" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-base @error('telephone') border-alert-red @enderror" value="{{ old('telephone') }}" required>
                            @error('telephone')<div class="text-alert-red text-xs mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-span-1">
                            <label class="block text-label-sm text-on-surface-variant mb-2 text-sm">Mot de passe</label>
                            <input type="password" name="password" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-base @error('password') border-alert-red @enderror" required>
                            @error('password')<div class="text-alert-red text-xs mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-span-1">
                            <label class="block text-label-sm text-on-surface-variant mb-2 text-sm">Confirmation</label>
                            <input type="password" name="password_confirmation" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-base" required>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="px-8 py-6 border-t border-slate-100 flex gap-4 justify-end flex-shrink-0 bg-white">
            <button type="button" class="flex-1 py-3 px-6 rounded-lg border border-slate-200 text-on-surface-variant font-label-md text-base hover:bg-slate-50 transition-all" onclick="toggleModal('addSadminModal')">Annuler</button>
            <button type="submit" form="addSadminForm" class="flex-2 py-3 px-12 rounded-lg bg-primary text-on-primary font-label-md text-base hover:opacity-90 transition-all shadow-lg shadow-primary/20">Enregistrer le SAdmin</button>
        </div>
    </div>
</div>

<!-- MODAL: MODIFIER SADMIN -->
<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="editSadminModal">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="toggleModal('editSadminModal')"></div>
    
    <div class="relative bg-white w-full max-w-2xl max-h-[90vh] rounded-2xl shadow-2xl overflow-hidden flex flex-col">
        
        <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-surface-container-low flex-shrink-0">
            <h3 class="font-headline-md text-base text-primary flex items-center gap-2">
                <span class="material-symbols-outlined text-base">edit</span>
                Modifier le SAdmin
            </h3>
            <button type="button" class="p-2 hover:bg-slate-200 rounded-full transition-colors" onclick="toggleModal('editSadminModal')">
                <span class="material-symbols-outlined text-base">close</span>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto custom-scrollbar">
            <form method="POST" id="editSadminForm">
                @csrf
                @method('PUT')
                <div class="p-8">
                    <input type="hidden" name="sadmin_id" id="editSadminId">

                    <div class="grid grid-cols-2 gap-6">
                        <div class="col-span-1">
                            <label class="block text-label-sm text-on-surface-variant mb-2 text-sm">Nom</label>
                            <input type="text" name="nom" id="editNom" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-base" required>
                        </div>
                        <div class="col-span-1">
                            <label class="block text-label-sm text-on-surface-variant mb-2 text-sm">Prénom</label>
                            <input type="text" name="prenom" id="editPrenom" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-base" required>
                        </div>

                        <div class="col-span-2">
                            <label class="block text-label-sm text-on-surface-variant mb-2 text-sm">Email</label>
                            <input type="email" name="email" id="editEmail" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-base" required>
                        </div>

                        <div class="col-span-2">
                            <label class="block text-label-sm text-on-surface-variant mb-2 text-sm">Téléphone</label>
                            <input type="text" name="telephone" id="editTelephone" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-base" required>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="px-8 py-6 border-t border-slate-100 flex gap-4 justify-end flex-shrink-0 bg-white">
            <button type="button" class="flex-1 py-3 px-6 rounded-lg border border-slate-200 text-on-surface-variant font-label-md text-base hover:bg-slate-50 transition-all" onclick="toggleModal('editSadminModal')">Annuler</button>
            <button type="submit" form="editSadminForm" class="flex-2 py-3 px-12 rounded-lg bg-primary text-on-primary font-label-md text-base hover:opacity-90 transition-all shadow-lg shadow-primary/20">Mettre à jour</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        
        if (modalId === 'addSadminModal') {
            const backdrop = document.getElementById('addModalBackdrop');
            const content = document.getElementById('addModalContent');
            
            if (modal.classList.contains('hidden')) {
                // Ouvrir le modal avec animation
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
            } else {
                // Fermer le modal avec animation inverse
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
        } else {
            // Pour editSadminModal (sans animation)
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            } else {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }
    }

    function openEditModal(button) {
        const id = button.getAttribute('data-id');
        const nom = button.getAttribute('data-nom');
        const prenom = button.getAttribute('data-prenom');
        const email = button.getAttribute('data-email');
        const telephone = button.getAttribute('data-telephone');

        document.getElementById('editSadminId').value = id;
        document.getElementById('editNom').value = nom;
        document.getElementById('editPrenom').value = prenom;
        document.getElementById('editEmail').value = email;
        document.getElementById('editTelephone').value = telephone;

        const form = document.getElementById('editSadminForm');
        form.action = '{{ url("/") }}' + '/sadmin/sadmin/' + id;

        toggleModal('editSadminModal');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modals = ['addSadminModal', 'editSadminModal'];
            modals.forEach(id => {
                const modal = document.getElementById(id);
                if (modal && !modal.classList.contains('hidden')) {
                    toggleModal(id);
                }
            });
        }
    });
</script>
@endsection