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
        <button class="px-6 py-2.5 bg-primary text-on-primary rounded-lg font-label-md text-label-md flex items-center gap-2 shadow-sm" onclick="openModal('modal-add')">
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
            <p class="font-headline-md text-headline-md text-on-surface">12 Plans Actifs</p>
        </div>
    </div>

    <div class="col-span-12 md:col-span-4 bg-surface-container-lowest p-6 rounded-xl card-shadow border border-outline-variant flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-surface-container-high flex items-center justify-center text-primary">
            <span class="material-symbols-outlined text-[32px]" data-icon="calendar_today">calendar_today</span>
        </div>
        <div>
            <p class="text-label-sm text-outline uppercase tracking-wider">Dernière Mise à Jour</p>
            <p class="font-headline-md text-headline-md text-on-surface">Aujourd'hui, 10:45</p>
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
                    <!-- Row 1 -->
                    <tr class="hover:bg-surface-container">
                        <td class="px-6 py-4">
                            <span class="font-label-md text-label-md text-on-surface">Plan Standard École</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-primary-fixed text-primary rounded-full font-label-sm text-label-sm">Annuel</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="font-body-md text-body-md font-semibold">150,000</span>
                        </td>
                        <td class="px-6 py-4 text-body-sm text-on-surface-variant">12/05/2024 - 09:15</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-surface-container-high text-[10px] flex items-center justify-center font-bold text-primary">JD</div>
                                <span class="text-body-sm">Jean Dupont</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center gap-2">
                                <button class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-primary-fixed text-primary">
                                    <span class="material-symbols-outlined text-[18px]" data-icon="edit">edit</span>
                                </button>
                                <button class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-error-container text-error">
                                    <span class="material-symbols-outlined text-[18px]" data-icon="delete">delete</span>
                                </button>
                            </div>
                         </td>
                    </tr>
                    <!-- Row 2 -->
                    <tr class="hover:bg-surface-container">
                        <td class="px-6 py-4">
                            <span class="font-label-md text-label-md text-on-surface">Premium Multi-Campus</span>
                         </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-secondary-fixed text-on-secondary-fixed-variant rounded-full font-label-sm text-label-sm">Mensuel</span>
                         </td>
                        <td class="px-6 py-4 text-right">
                            <span class="font-body-md text-body-md font-semibold">25,000</span>
                         </td>
                        <td class="px-6 py-4 text-body-sm text-on-surface-variant">15/05/2024 - 14:30</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-surface-container-high text-[10px] flex items-center justify-center font-bold text-primary">MS</div>
                                <span class="text-body-sm">Marie Sarr</span>
                            </div>
                         </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center gap-2">
                                <button class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-primary-fixed text-primary">
                                    <span class="material-symbols-outlined text-[18px]" data-icon="edit">edit</span>
                                </button>
                                <button class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-error-container text-error">
                                    <span class="material-symbols-outlined text-[18px]" data-icon="delete">delete</span>
                                </button>
                            </div>
                         </td>
                    </tr>
                    <!-- Row 3 -->
                    <tr class="hover:bg-surface-container">
                        <td class="px-6 py-4">
                            <span class="font-label-md text-label-md text-on-surface">Basique Starter</span>
                         </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-surface-container-high text-on-surface-variant rounded-full font-label-sm text-label-sm">Trimestriel</span>
                         </td>
                        <td class="px-6 py-4 text-right">
                            <span class="font-body-md text-body-md font-semibold">45,000</span>
                         </td>
                        <td class="px-6 py-4 text-body-sm text-on-surface-variant">18/05/2024 - 11:00</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-surface-container-high text-[10px] flex items-center justify-center font-bold text-primary">AA</div>
                                <span class="text-body-sm">Alioune Ardo</span>
                            </div>
                         </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center gap-2">
                                <button class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-primary-fixed text-primary">
                                    <span class="material-symbols-outlined text-[18px]" data-icon="edit">edit</span>
                                </button>
                                <button class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-error-container text-error">
                                    <span class="material-symbols-outlined text-[18px]" data-icon="delete">delete</span>
                                </button>
                            </div>
                         </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 bg-surface-subtle flex justify-between items-center border-t border-outline-variant">
            <p class="text-body-sm text-on-surface-variant">Affichage de 1 à 10 sur 12 résultats</p>
            <div class="flex gap-2">
                <button class="px-3 py-1 border border-outline-variant rounded hover:bg-surface-container-high">Précédent</button>
                <button class="px-3 py-1 bg-primary text-on-primary rounded">1</button>
                <button class="px-3 py-1 border border-outline-variant rounded hover:bg-surface-container-high">2</button>
                <button class="px-3 py-1 border border-outline-variant rounded hover:bg-surface-container-high">Suivant</button>
            </div>
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
        <form class="space-y-6">
            <div class="space-y-2">
                <label class="font-label-md text-label-md text-on-surface-variant block">Libellé du Plan</label>
                <input class="w-full px-4 py-2.5 rounded-lg border border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary-fixed-dim outline-none" placeholder="ex: Plan Gold Illimité" type="text">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="font-label-md text-label-md text-on-surface-variant block">Type d'abonnement</label>
                    <select class="w-full px-4 py-2.5 rounded-lg border border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary-fixed-dim outline-none">
                        <option>Mensuel</option>
                        <option>Trimestriel</option>
                        <option>Annuel</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="font-label-md text-label-md text-on-surface-variant block">Montant (FCFA)</label>
                    <input class="w-full px-4 py-2.5 rounded-lg border border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary-fixed-dim outline-none" placeholder="0.00" type="number">
                </div>
            </div>
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
            <div class="flex gap-2">
                <input class="flex-1 px-4 py-2 rounded-lg border border-outline-variant outline-none" placeholder="Nouveau type..." type="text">
                <button class="bg-secondary p-2 text-on-secondary rounded-lg"><span class="material-symbols-outlined" data-icon="add">add</span></button>
            </div>
            <div class="space-y-2 border-t border-surface-subtle pt-4 max-h-60 overflow-y-auto">
                <div class="flex justify-between items-center p-3 bg-surface-subtle rounded-lg">
                    <span class="font-label-md">Mensuel</span>
                    <button class="text-error"><span class="material-symbols-outlined text-[18px]" data-icon="delete">delete</span></button>
                </div>
                <div class="flex justify-between items-center p-3 bg-surface-subtle rounded-lg">
                    <span class="font-label-md">Trimestriel</span>
                    <button class="text-error"><span class="material-symbols-outlined text-[18px]" data-icon="delete">delete</span></button>
                </div>
                <div class="flex justify-between items-center p-3 bg-surface-subtle rounded-lg">
                    <span class="font-label-md">Annuel</span>
                    <button class="text-error"><span class="material-symbols-outlined text-[18px]" data-icon="delete">delete</span></button>
                </div>
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
</script>
@endsection