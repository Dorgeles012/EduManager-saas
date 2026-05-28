@extends('sadmin.layouts.app')

@section('content')
<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-primary">Gestion des Clients</h2>
        <p class="text-body-sm text-text-muted">Administrez vos clients et suivez leurs dossiers académiques.</p>
    </div>
    <button class="flex items-center gap-1.5 px-4 py-2 bg-primary-container text-on-primary font-label-sm rounded-lg shadow-md hover:shadow-lg" onclick="openModal('addClientModal')">
        <span class="material-symbols-outlined text-[18px]">person_add</span>
        AJOUTER UN CLIENT
    </button>
</div>

<!-- Dashboard Stats Summary -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-surface-container-lowest p-5 rounded-xl card-shadow border border-outline-variant/30">
        <p class="text-label-sm text-text-muted mb-1">Total Clients</p>
        <h3 class="font-headline-md text-[28px] text-primary">1,284</h3>
    </div>
    <div class="bg-surface-container-lowest p-5 rounded-xl card-shadow border border-outline-variant/30">
        <p class="text-label-sm text-text-muted mb-1">Nouveaux (Ce mois)</p>
        <h3 class="font-headline-md text-[28px] text-success-green">+42</h3>
    </div>
    <div class="bg-surface-container-lowest p-5 rounded-xl card-shadow border border-outline-variant/30">
        <p class="text-label-sm text-text-muted mb-1">Actifs</p>
        <h3 class="font-headline-md text-[28px] text-primary-container">1,150</h3>
    </div>
</div>

<!-- Data Table Card -->
<div class="bg-surface-container-lowest rounded-xl card-shadow border border-outline-variant/30 overflow-hidden">
    <div class="px-6 py-4 border-b border-surface-subtle flex items-center justify-between">
        <h4 class="font-headline-md text-[18px]">Liste des Clients</h4>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-subtle">
                    <th class="px-4 py-3 font-label-sm text-text-muted uppercase tracking-wider text-[11px]">N°</th>
                    <th class="px-4 py-3 font-label-sm text-text-muted uppercase tracking-wider text-[11px]">Nom</th>
                    <th class="px-4 py-3 font-label-sm text-text-muted uppercase tracking-wider text-[11px]">Prénoms</th>
                    <th class="px-4 py-3 font-label-sm text-text-muted uppercase tracking-wider text-[11px]">Téléphone</th>
                    <th class="px-4 py-3 font-label-sm text-text-muted uppercase tracking-wider text-[11px]">Email</th>
                    <th class="px-4 py-3 font-label-sm text-text-muted uppercase tracking-wider text-[11px]">Sexe</th>
                    <th class="px-4 py-3 font-label-sm text-text-muted uppercase tracking-wider text-[11px] text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-subtle">
                <!-- Row 1 -->
                <tr class="hover:bg-background transition-colors">
                    <td class="px-4 py-3 text-body-sm text-[13px] text-on-surface">1</td>
                    <td class="px-4 py-3 font-label-md text-[13px] text-on-surface">KOUADIO</td>
                    <td class="px-4 py-3 text-body-sm text-[13px] text-on-surface">Koffi Marc</td>
                    <td class="px-4 py-3 text-body-sm text-[13px] text-on-surface">+225 07 08 09 10 11</td>
                    <td class="px-4 py-3 text-body-sm text-[13px] text-on-surface-variant">k.marc@email.com</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 bg-surface-container-high text-primary-container text-[10px] font-bold rounded-full">HOMME</span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <button class="px-3 py-1 bg-surface-subtle text-primary border border-primary/20 hover:bg-primary-fixed rounded-lg font-label-sm text-[12px] transition-all" onclick="openModal('detailsModal')">Détails</button>
                    </td>
                </tr>
                <!-- Row 2 -->
                <tr class="bg-surface-container-low/30 hover:bg-background transition-colors">
                    <td class="px-4 py-3 text-body-sm text-[13px] text-on-surface">2</td>
                    <td class="px-4 py-3 font-label-md text-[13px] text-on-surface">TOURE</td>
                    <td class="px-4 py-3 text-body-sm text-[13px] text-on-surface">Awa Sarah</td>
                    <td class="px-4 py-3 text-body-sm text-[13px] text-on-surface">+225 01 02 03 04 05</td>
                    <td class="px-4 py-3 text-body-sm text-[13px] text-on-surface-variant">t.awa@email.com</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 bg-secondary-container/30 text-secondary text-[10px] font-bold rounded-full">FEMME</span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <button class="px-3 py-1 bg-surface-subtle text-primary border border-primary/20 hover:bg-primary-fixed rounded-lg font-label-sm text-[12px] transition-all" onclick="openModal('detailsModal')">Détails</button>
                    </td>
                </tr>
                <!-- Row 3 -->
                <tr class="hover:bg-background transition-colors">
                    <td class="px-4 py-3 text-body-sm text-[13px] text-on-surface">3</td>
                    <td class="px-4 py-3 font-label-md text-[13px] text-on-surface">YOBOUÉ</td>
                    <td class="px-4 py-3 text-body-sm text-[13px] text-on-surface">Jean-Luc</td>
                    <td class="px-4 py-3 text-body-sm text-[13px] text-on-surface">+225 05 55 66 77 88</td>
                    <td class="px-4 py-3 text-body-sm text-[13px] text-on-surface-variant">yoboue.jl@email.com</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 bg-surface-container-high text-primary-container text-[10px] font-bold rounded-full">HOMME</span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <button class="px-3 py-1 bg-surface-subtle text-primary border border-primary/20 hover:bg-primary-fixed rounded-lg font-label-sm text-[12px] transition-all" onclick="openModal('detailsModal')">Détails</button>
                    </td>
                </tr>
                <!-- Row 4 -->
                <tr class="bg-surface-container-low/30 hover:bg-background transition-colors">
                    <td class="px-4 py-3 text-body-sm text-[13px] text-on-surface">4</td>
                    <td class="px-4 py-3 font-label-md text-[13px] text-on-surface">BAMBA</td>
                    <td class="px-4 py-3 text-body-sm text-[13px] text-on-surface">Moussa Ibrahim</td>
                    <td class="px-4 py-3 text-body-sm text-[13px] text-on-surface">+225 09 88 77 66 55</td>
                    <td class="px-4 py-3 text-body-sm text-[13px] text-on-surface-variant">b.moussa@email.com</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 bg-surface-container-high text-primary-container text-[10px] font-bold rounded-full">HOMME</span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <button class="px-3 py-1 bg-surface-subtle text-primary border border-primary/20 hover:bg-primary-fixed rounded-lg font-label-sm text-[12px] transition-all" onclick="openModal('detailsModal')">Détails</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-surface-subtle flex items-center justify-between">
        <p class="text-label-sm text-[11px] text-text-muted">Affichage de 1 à 4 sur 1,284 clients</p>
        <div class="flex items-center gap-1">
            <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-outline-variant text-on-surface hover:bg-surface-subtle disabled:opacity-50" disabled>
                <span class="material-symbols-outlined text-[18px]">chevron_left</span>
            </button>
            <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-primary text-on-primary font-bold text-[13px]">1</button>
            <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-outline-variant text-on-surface hover:bg-surface-subtle text-[13px]">2</button>
            <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-outline-variant text-on-surface hover:bg-surface-subtle text-[13px]">3</button>
            <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-outline-variant text-on-surface hover:bg-surface-subtle">
                <span class="material-symbols-outlined text-[18px]">chevron_right</span>
            </button>
        </div>
    </div>
</div>

<!-- Modal: Ajouter Client -->
<div class="fixed inset-0 z-[100] items-center justify-center p-4 hidden" id="addClientModal">
    <div class="absolute inset-0 bg-inverse-surface/60 backdrop-blur-sm" onclick="closeModal('addClientModal')"></div>
    <div class="relative bg-surface-container-lowest w-full max-w-xl rounded-2xl shadow-2xl transform transition-all duration-300 overflow-hidden scale-95 opacity-0" id="addClientModalContent">
        <div class="px-6 py-4 border-b border-surface-subtle flex items-center justify-between bg-primary-container text-on-primary">
            <h3 class="font-headline-md text-[18px]">Inscrire un client</h3>
            <button class="p-1.5 hover:bg-white/10 rounded-full" onclick="closeModal('addClientModal')">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <form class="p-6 space-y-5">
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="font-label-md text-[12px] text-on-surface">Nom</label>
                    <input class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container focus:border-primary-container outline-none text-[13px]" placeholder="Ex: KOUADIO" type="text">
                </div>
                <div class="space-y-1">
                    <label class="font-label-md text-[12px] text-on-surface">Prénoms</label>
                    <input class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container focus:border-primary-container outline-none text-[13px]" placeholder="Ex: Marc Koffi" type="text">
                </div>
            </div>
            <div class="space-y-1">
                <label class="font-label-md text-[12px] text-on-surface">Téléphone</label>
                <input class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container focus:border-primary-container outline-none text-[13px]" placeholder="+225 00 00 00 00 00" type="tel">
            </div>
            <div class="space-y-1">
                <label class="font-label-md text-[12px] text-on-surface">Email</label>
                <input class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container focus:border-primary-container outline-none text-[13px]" placeholder="client@exemple.com" type="email">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="font-label-md text-[12px] text-on-surface">Sexe</label>
                    <select class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container focus:border-primary-container outline-none text-[13px] appearance-none">
                        <option value="">Sélectionner</option>
                        <option value="H">Homme</option>
                        <option value="F">Femme</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="font-label-md text-[12px] text-on-surface">Mot de passe</label>
                    <input class="w-full px-3 py-2 bg-white border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container focus:border-primary-container outline-none text-[13px]" placeholder="••••••••" type="password">
                </div>
            </div>
            <div class="pt-3 flex gap-3">
                <button class="flex-1 py-2 border border-outline-variant text-on-surface-variant font-label-md text-[13px] rounded-lg hover:bg-surface-subtle" onclick="closeModal('addClientModal')" type="button">Annuler</button>
                <button class="flex-1 py-2 bg-primary text-on-primary font-label-md text-[13px] rounded-lg shadow-md hover:shadow-lg" type="submit">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Détails Client -->
<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="detailsModal">
    <div class="absolute inset-0 bg-inverse-surface/60 backdrop-blur-sm" onclick="closeModal('detailsModal')"></div>
    <div class="relative bg-surface-container-lowest w-full max-w-lg rounded-2xl shadow-2xl transform transition-all scale-95 opacity-0 duration-300" id="detailsModalContent">
        <div class="p-6 text-center">
            <div class="w-20 h-20 bg-primary-fixed rounded-full flex items-center justify-center mx-auto mb-3 border-4 border-white shadow-lg">
                <span class="material-symbols-outlined text-3xl text-primary">person</span>
            </div>
            <h3 class="font-headline-md text-[18px] text-primary mb-1">KOUADIO Koffi Marc</h3>
            <p class="text-body-sm text-[12px] text-text-muted mb-6">Inscrit le 12 Octobre 2023</p>
            <div class="grid grid-cols-1 gap-3 text-left">
                <div class="p-3 bg-surface-container-low rounded-lg flex items-center gap-3">
                    <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center shadow-sm">
                        <span class="material-symbols-outlined text-[18px] text-primary-container">mail</span>
                    </div>
                    <div>
                        <p class="text-[10px] text-text-muted uppercase font-bold">Email</p>
                        <p class="text-body-sm text-[12px] text-on-surface">k.marc@email.com</p>
                    </div>
                </div>
                <div class="p-3 bg-surface-container-low rounded-lg flex items-center gap-3">
                    <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center shadow-sm">
                        <span class="material-symbols-outlined text-[18px] text-primary-container">call</span>
                    </div>
                    <div>
                        <p class="text-[10px] text-text-muted uppercase font-bold">Téléphone</p>
                        <p class="text-body-sm text-[12px] text-on-surface">+225 07 08 09 10 11</p>
                    </div>
                </div>
                <div class="p-3 bg-surface-container-low rounded-lg flex items-center gap-3">
                    <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center shadow-sm">
                        <span class="material-symbols-outlined text-[18px] text-primary-container">wc</span>
                    </div>
                    <div>
                        <p class="text-[10px] text-text-muted uppercase font-bold">Sexe</p>
                        <p class="text-body-sm text-[12px] text-on-surface">Homme</p>
                    </div>
                </div>
            </div>
            <div class="mt-8 flex gap-3">
                <button class="flex-1 py-2 border border-outline-variant text-on-surface font-label-md text-[13px] rounded-lg hover:bg-surface-subtle flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-[16px] text-primary">edit</span>
                    Modifier
                </button>
                <button class="flex-1 py-2 bg-alert-red text-on-primary font-label-md text-[13px] rounded-lg shadow-md flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">delete</span>
                    Supprimer
                </button>
            </div>
            <button class="mt-4 text-text-muted font-label-sm text-[11px] hover:text-primary underline" onclick="closeModal('detailsModal')">Fermer la fenêtre</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        const content = document.getElementById(id + 'Content');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        const content = document.getElementById(id + 'Content');
        
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }
</script>
@endsection