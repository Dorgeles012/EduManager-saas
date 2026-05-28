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
        <button class="bg-primary-container text-white px-6 py-3 rounded-lg flex items-center gap-2 hover:opacity-90 shadow-md font-label-md text-label-md" onclick="document.getElementById('modal-add').classList.remove('hidden')">
            <span class="material-symbols-outlined">add_business</span>
            Ajouter un établissement
        </button>
    </div>

    <!-- Stats Overview (Bento Style) -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-surface-container-lowest p-6 rounded-xl card-shadow border border-outline-variant flex flex-col">
            <span class="text-text-muted font-label-sm text-label-sm uppercase tracking-wider">Total Établissements</span>
            <span class="text-headline-xl font-headline-xl text-primary mt-2">12</span>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-xl card-shadow border border-outline-variant flex flex-col">
            <span class="text-text-muted font-label-sm text-label-sm uppercase tracking-wider">Primaire</span>
            <span class="text-headline-xl font-headline-xl text-primary mt-2">4</span>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-xl card-shadow border border-outline-variant flex flex-col">
            <span class="text-text-muted font-label-sm text-label-sm uppercase tracking-wider">Collèges / Lycées</span>
            <span class="text-headline-xl font-headline-xl text-warning-amber mt-2">15</span>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-xl card-shadow border border-outline-variant flex flex-col">
            <span class="text-text-muted font-label-sm text-label-sm uppercase tracking-wider">Univ. / Grandes Écoles</span>
            <span class="text-headline-xl font-headline-xl text-secondary mt-2">16</span>
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
                        <th class="px-6 py-4 font-label-sm text-label-sm text-text-muted uppercase">Anagramme</th>
                        <th class="px-6 py-4 font-label-sm text-label-sm text-text-muted uppercase">Localisation</th>
                        <th class="px-6 py-4 font-label-sm text-label-sm text-text-muted uppercase">Email</th>
                        <th class="px-6 py-4 font-label-sm text-label-sm text-text-muted uppercase">Statut</th>
                        <th class="px-6 py-4 font-label-sm text-label-sm text-text-muted uppercase text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-subtle">
                    <tr class="hover:bg-surface-container-low">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-primary-fixed text-primary font-bold flex items-center justify-center rounded-lg">LI</div>
                                <div>
                                    <p class="font-label-md text-label-md text-on-surface">Lycée International</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-body-sm text-body-sm">LI</td>
                        <td class="px-6 py-4 font-body-sm text-body-sm">Paris, France</td>
                        <td class="px-6 py-4 font-body-sm text-body-sm">contact@lycee-int.fr</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-success-green/10 text-success-green rounded-full text-label-sm font-label-sm">À jour</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button class="p-2 text-on-surface-variant hover:text-primary hover:bg-primary-fixed rounded-lg" onclick="document.getElementById('modal-details').classList.remove('hidden')" title="Détails">
                                    <span class="material-symbols-outlined">visibility</span>
                                </button>
                                <button class="p-2 text-on-surface-variant hover:text-primary hover:bg-primary-fixed rounded-lg" title="Modifier">
                                    <span class="material-symbols-outlined">edit_square</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr class="bg-surface-container-lowest/50 hover:bg-surface-container-low">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-secondary-fixed text-secondary font-bold flex items-center justify-center rounded-lg">EP</div>
                                <div>
                                    <p class="font-label-md text-label-md text-on-surface">École Poly-Tech</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-body-sm text-body-sm">EP</td>
                        <td class="px-6 py-4 font-body-sm text-body-sm">Lyon, France</td>
                        <td class="px-6 py-4 font-body-sm text-body-sm">admin@polytech-lyon.edu</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-success-green/10 text-success-green rounded-full text-label-sm font-label-sm">À jour</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button class="p-2 text-on-surface-variant hover:text-primary hover:bg-primary-fixed rounded-lg">
                                    <span class="material-symbols-outlined">visibility</span>
                                </button>
                                <button class="p-2 text-on-surface-variant hover:text-primary hover:bg-primary-fixed rounded-lg">
                                    <span class="material-symbols-outlined">edit_square</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr class="hover:bg-surface-container-low">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-surface-container text-outline font-bold flex items-center justify-center rounded-lg">PC</div>
                                <div>
                                    <p class="font-label-md text-label-md text-on-surface">Primaire du Centre</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-body-sm text-body-sm">PC</td>
                        <td class="px-6 py-4 font-body-sm text-body-sm">Bordeaux, France</td>
                        <td class="px-6 py-4 font-body-sm text-body-sm">bordeaux.centre@ecole.fr</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-warning-amber/10 text-warning-amber rounded-full text-label-sm font-label-sm">En révision</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button class="p-2 text-on-surface-variant hover:text-primary hover:bg-primary-fixed rounded-lg">
                                    <span class="material-symbols-outlined">visibility</span>
                                </button>
                                <button class="p-2 text-on-surface-variant hover:text-primary hover:bg-primary-fixed rounded-lg">
                                    <span class="material-symbols-outlined">edit_square</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-surface-subtle flex items-center justify-between border-t border-surface-subtle">
            <p class="text-body-sm text-label-sm text-text-muted">Affichage de 3 sur 12 établissements</p>
            <div class="flex gap-1">
                <button class="w-8 h-8 flex items-center justify-center rounded border border-outline-variant bg-white text-text-muted">1</button>
                <button class="w-8 h-8 flex items-center justify-center rounded border border-transparent hover:bg-surface-container text-text-muted">2</button>
                <button class="w-8 h-8 flex items-center justify-center rounded border border-transparent hover:bg-surface-container text-text-muted">3</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajouter un établissement -->
<div class="fixed inset-0 z-[60] flex items-center justify-center bg-on-surface/40 backdrop-blur-sm hidden" id="modal-add">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden">
        <div class="px-8 py-6 border-b border-surface-subtle flex justify-between items-center bg-primary-container text-white">
            <h3 class="font-headline-md text-headline-md">Nouvel Établissement</h3>
            <button class="hover:bg-white/10 rounded-full p-1" onclick="document.getElementById('modal-add').classList.add('hidden')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-8 grid grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="font-label-md text-label-md text-on-surface">Nom de l'établissement</label>
                <input class="w-full px-4 py-3 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none font-body-sm text-body-sm" placeholder="Ex: Lycée International" type="text">
            </div>
            <div class="space-y-2">
                <label class="font-label-md text-label-md text-on-surface">Anagramme</label>
                <input class="w-full px-4 py-3 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none font-body-sm text-body-sm" placeholder="Ex: LI" type="text">
            </div>
            <div class="space-y-2">
                <label class="font-label-md text-label-md text-on-surface">N° Établissement</label>
                <input class="w-full px-4 py-3 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none font-body-sm text-body-sm" placeholder="+33 1 23 45 67 89" type="tel">
            </div>
            <div class="space-y-2">
                <label class="font-label-md text-label-md text-on-surface">Type d'institution</label>
                <select class="w-full px-4 py-3 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none font-body-sm text-body-sm appearance-none bg-white">
                    <option>Primaire</option>
                    <option>Collège</option>
                    <option>Lycée</option>
                    <option>Grande École</option>
                    <option>Université</option>
                </select>
            </div>
            <div class="col-span-2 space-y-2">
                <label class="font-label-md text-label-md text-on-surface">Localisation</label>
                <input class="w-full px-4 py-3 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none font-body-sm text-body-sm" placeholder="Adresse complète" type="text">
            </div>
            <div class="col-span-2 space-y-2">
                <label class="font-label-md text-label-md text-on-surface">Email de contact</label>
                <input class="w-full px-4 py-3 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none font-body-sm text-body-sm" placeholder="contact@etablissement.fr" type="email">
            </div>
        </div>
        <div class="px-8 py-6 bg-surface-subtle flex justify-end gap-3">
            <button class="px-6 py-2 rounded-lg border border-outline-variant text-on-surface-variant font-label-md text-label-md hover:bg-white" onclick="document.getElementById('modal-add').classList.add('hidden')">Annuler</button>
            <button class="px-6 py-2 rounded-lg bg-primary-container text-white font-label-md text-label-md hover:opacity-90">Enregistrer</button>
        </div>
    </div>
</div>

<!-- Modal Détails Établissement -->
<div class="fixed inset-0 z-[60] flex items-center justify-center bg-on-surface/40 backdrop-blur-sm hidden" id="modal-details">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-xl overflow-hidden">
        <div class="p-8">
            <div class="flex justify-between items-start mb-8">
                <div class="flex gap-4 items-center">
                    <div class="w-16 h-16 bg-primary-fixed text-primary font-bold text-2xl flex items-center justify-center rounded-xl">LI</div>
                    <div>
                        <h3 class="font-headline-md text-headline-md text-on-surface">Lycée International</h3>
                        <p class="text-success-green font-label-sm text-label-sm flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">verified</span>
                            Établissement certifié
                        </p>
                    </div>
                </div>
                <button class="text-outline hover:text-on-surface p-1" onclick="document.getElementById('modal-details').classList.add('hidden')">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="grid grid-cols-2 gap-y-6 gap-x-4 border-y border-surface-subtle py-6">
                <div>
                    <p class="text-text-muted font-label-sm text-label-sm uppercase mb-1">Responsable</p>
                    <p class="font-body-md text-body-md">M. Jean Dupont</p>
                </div>
                <div>
                    <p class="text-text-muted font-label-sm text-label-sm uppercase mb-1">Code Système</p>
                    <p class="font-body-md text-body-md">SYS-LI-7501</p>
                </div>
                <div>
                    <p class="text-text-muted font-label-sm text-label-sm uppercase mb-1">Téléphone</p>
                    <p class="font-body-md text-body-md">+33 1 45 67 89 00</p>
                </div>
                <div>
                    <p class="text-text-muted font-label-sm text-label-sm uppercase mb-1">Dernière Connexion</p>
                    <p class="font-body-md text-body-md">Il y a 2 heures</p>
                </div>
            </div>
            <div class="mt-8 flex gap-4">
                <button class="flex-1 py-3 rounded-lg border border-primary-container text-primary-container font-label-md text-label-md hover:bg-primary-fixed flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">edit</span>
                    Modifier les accès
                </button>
                <button class="flex-1 py-3 rounded-lg bg-alert-red text-white font-label-md text-label-md hover:opacity-90 flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">block</span>
                    Bloquer l'établissement
                </button>
            </div>
        </div>
    </div>
</div>
@endsection