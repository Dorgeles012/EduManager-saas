@extends('client.layouts.app')
@section('title', 'EduManager - Personnel')
@section('content')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let personnelsData = @json($personnels);
</script>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Gestion du Personnel</h2>
        <p class="text-sm text-gray-500 mt-1">Gérez les utilisateurs de votre établissement</p>
    </div>
    <button onclick="openModal('add-user-modal')" class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-xs font-semibold transition shadow-sm">
        <span class="material-symbols-outlined text-sm">person_add</span>
        Ajouter un utilisateur
    </button>
</div>

@php
    $totalEmployes = $personnels->count();
    $actifs = $personnels->where('statut', 'actif')->count();
    $bloques = $personnels->where('statut', 'bloqué')->count();
@endphp

<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mb-6">
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                <span class="material-symbols-outlined text-lg">group</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Total</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $totalEmployes }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Employés</p>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                <span class="material-symbols-outlined text-lg">how_to_reg</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Actifs</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold text-emerald-600">{{ $actifs }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">En service</p>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow col-span-2 lg:col-span-1">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600">
                <span class="material-symbols-outlined text-lg">block</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Bloqués</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold text-rose-600">{{ $bloques }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Accès suspendu</p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                <span class="material-symbols-outlined text-base">badge</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Liste du personnel</h3>
                <p class="text-[11px] text-gray-500">{{ $totalEmployes }} membre(s) enregistré(s)</p>
            </div>
        </div>
        <div class="relative w-full sm:w-72">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base pointer-events-none">search</span>
            <input id="searchEmployee" type="text" placeholder="Rechercher..."
                   class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 pl-10 pr-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
        </div>
    </div>

    <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left" id="employeesTable">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Employé</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Contact</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Établissement</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Rôle</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($personnels as $p)
                <tr class="employee-row hover:bg-gray-50/50 transition-colors"
                    data-name="{{ strtolower(($p->nom ?? '').' '.($p->prenom ?? '')) }}"
                    data-email="{{ strtolower($p->email ?? '') }}"
                    data-position="{{ strtolower($p->role ?? '') }}">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-gray-900 truncate">{{ $p->nom }} {{ $p->prenom }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <p class="text-xs text-gray-700 truncate">{{ $p->email }}</p>
                        <p class="text-[10px] text-gray-400">{{ $p->telephone ?? '—' }}</p>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-700">{{ optional($p->etablissement)->nom ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-100 text-[10px] font-bold text-gray-700 uppercase">Personnel</span>
                    </td>
                    <td class="px-4 py-3">
                        @if (($p->statut ?? '') === 'actif')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Actif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                Bloqué
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-1.5">
                            <button onclick="openEditModal({{ $p->id }})"
                                    class="w-8 h-8 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-600 flex items-center justify-center transition"
                                    title="Modifier">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </button>

                            @if (($p->statut ?? '') === 'actif')
                                <button onclick="confirmBlock({{ $p->id }}, '{{ addslashes($p->nom . ' ' . $p->prenom) }}')"
                                        class="w-8 h-8 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600 flex items-center justify-center transition"
                                        title="Bloquer">
                                    <span class="material-symbols-outlined text-base">block</span>
                                </button>
                            @else
                                <button onclick="confirmUnblock({{ $p->id }}, '{{ addslashes($p->nom . ' ' . $p->prenom) }}')"
                                        class="w-8 h-8 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600 flex items-center justify-center transition"
                                        title="Débloquer">
                                    <span class="material-symbols-outlined text-base">lock_open</span>
                                </button>
                            @endif

                            <button onclick="confirmDelete({{ $p->id }}, '{{ addslashes($p->nom . ' ' . $p->prenom) }}')"
                                    class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition"
                                    title="Supprimer">
                                <span class="material-symbols-outlined text-base">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-16 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center mb-3">
                                <span class="material-symbols-outlined text-2xl text-gray-300">badge</span>
                            </div>
                            <p class="text-sm font-semibold text-gray-700">Aucun personnel</p>
                            <p class="text-xs text-gray-400 mt-1">Ajoutez votre premier utilisateur.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="md:hidden divide-y divide-gray-100">
        @forelse ($personnels as $p)
        <div class="employee-row p-4 space-y-3"
             data-name="{{ strtolower(($p->nom ?? '').' '.($p->prenom ?? '')) }}"
             data-email="{{ strtolower($p->email ?? '') }}"
             data-position="{{ strtolower($p->role ?? '') }}">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-xs font-bold flex-shrink-0">
                        {{ strtoupper(substr($p->nom ?? 'P', 0, 1)) }}{{ strtoupper(substr($p->prenom ?? '', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $p->nom }} {{ $p->prenom }}</p>
                        <p class="text-[11px] text-gray-500 truncate">{{ $p->email }}</p>
                    </div>
                </div>
                @if (($p->statut ?? '') === 'actif')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 flex-shrink-0">Actif</span>
                @else
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 flex-shrink-0">Bloqué</span>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-2 py-1 text-[11px]">
                <div>
                    <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Téléphone</p>
                    <p class="text-xs text-gray-700 mt-0.5">{{ $p->telephone ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Établissement</p>
                    <p class="text-xs text-gray-700 mt-0.5 truncate">{{ optional($p->etablissement)->nom ?? '—' }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <button onclick="openEditModal({{ $p->id }})"
                        class="flex-1 h-9 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold flex items-center justify-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-sm">edit</span>Modifier
                </button>
                @if (($p->statut ?? '') === 'actif')
                    <button onclick="confirmBlock({{ $p->id }}, '{{ addslashes($p->nom . ' ' . $p->prenom) }}')"
                            class="flex-1 h-9 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600 text-xs font-semibold flex items-center justify-center gap-1.5 transition">
                        <span class="material-symbols-outlined text-sm">block</span>Bloquer
                    </button>
                @else
                    <button onclick="confirmUnblock({{ $p->id }}, '{{ addslashes($p->nom . ' ' . $p->prenom) }}')"
                            class="flex-1 h-9 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600 text-xs font-semibold flex items-center justify-center gap-1.5 transition">
                        <span class="material-symbols-outlined text-sm">lock_open</span>Débloquer
                    </button>
                @endif
                <button onclick="confirmDelete({{ $p->id }}, '{{ addslashes($p->nom . ' ' . $p->prenom) }}')"
                        class="w-9 h-9 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition">
                    <span class="material-symbols-outlined text-base">delete</span>
                </button>
            </div>
        </div>
        @empty
        <div class="py-16 text-center">
            <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-2xl text-gray-300">badge</span>
            </div>
            <p class="text-sm font-semibold text-gray-700">Aucun personnel</p>
            <p class="text-xs text-gray-400 mt-1">Ajoutez votre premier utilisateur.</p>
        </div>
        @endforelse
    </div>

    <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between">
        <span id="paginationInfo" class="text-[11px] text-gray-500">
            Affichage de {{ $totalEmployes }} sur {{ $totalEmployes }} employé(s)
        </span>
        <div class="flex items-center gap-1.5 text-xs">
            <button class="px-3 py-1 bg-indigo-600 text-white rounded-lg text-[11px] font-semibold">1</button>
        </div>
    </div>
</div>

<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="add-user-modal">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('add-user-modal')"></div>
    <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden flex flex-col transform transition-all duration-300 scale-95 opacity-0 relative z-10 max-h-[90vh]" id="add-user-modal-content">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center flex-shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <span class="material-symbols-outlined text-base">person_add</span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Ajouter un utilisateur</h3>
                    <p class="text-[11px] text-gray-500">Créez un nouveau compte personnel</p>
                </div>
            </div>
            <button onclick="closeModal('add-user-modal')" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center transition">
                <span class="material-symbols-outlined text-gray-500">close</span>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-5">
            <form id="addUserForm" method="POST" action="{{ route('client.personnel.store') }}">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Nom <span class="text-rose-500">*</span></label>
                        <input type="text" name="nom" value="{{ old('nom') }}" required placeholder="Entrez le nom"
                               class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Prénom <span class="text-rose-500">*</span></label>
                        <input type="text" name="prenom" value="{{ old('prenom') }}" required placeholder="Entrez le prénom"
                               class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Téléphone <span class="text-rose-500">*</span></label>
                        <input type="tel" name="telephone" value="{{ old('telephone') }}" required placeholder="00 00 00 00"
                               class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Email <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="exemple@mail.com"
                               class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Établissement</label>
                        <input type="text" value="{{ auth()->user()->etablissement->nom ?? '—' }}" disabled
                               class="w-full bg-gray-100 border border-gray-200 rounded-lg text-xs py-2.5 px-3 text-gray-500">
                        <input type="hidden" name="etablissement_id" value="{{ auth()->user()->etablissement_id }}">
                        <p class="text-[10px] text-gray-400 mt-1">Défini automatiquement</p>
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Mot de passe <span class="text-rose-500">*</span></label>
                        <input type="password" name="password" required placeholder="••••••••"
                               class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Confirmer <span class="text-rose-500">*</span></label>
                        <input type="password" name="password_confirmation" required placeholder="••••••••"
                               class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                    </div>
                </div>
                <input type="hidden" name="role" value="personnel">

                <div class="flex justify-end gap-2 pt-5">
                    <button type="button" onclick="closeModal('add-user-modal')"
                            class="px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition">
                        Annuler
                    </button>
                    <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-xs font-semibold transition shadow-sm">
                        Créer l'utilisateur
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="edit-user-modal">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('edit-user-modal')"></div>
    <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden flex flex-col transform transition-all duration-300 scale-95 opacity-0 relative z-10 max-h-[90vh]" id="edit-user-modal-content">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center flex-shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                    <span class="material-symbols-outlined text-base">edit</span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Modifier l'utilisateur</h3>
                    <p class="text-[11px] text-gray-500">Mise à jour des informations</p>
                </div>
            </div>
            <button onclick="closeModal('edit-user-modal')" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center transition">
                <span class="material-symbols-outlined text-gray-500">close</span>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-5">
            <form id="editForm" method="POST" action="">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Nom</label>
                        <input id="edit_nom" name="nom" type="text"
                               class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Prénom</label>
                        <input id="edit_prenom" name="prenom" type="text"
                               class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Téléphone</label>
                        <input id="edit_telephone" name="telephone" type="tel"
                               class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Email</label>
                        <input id="edit_email" name="email" type="email"
                               class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Établissement</label>
                        <input type="text" id="edit_etablissement_text" disabled
                               class="w-full bg-gray-100 border border-gray-200 rounded-lg text-xs py-2.5 px-3 text-gray-500">
                        <input type="hidden" id="edit_etablissement_id" name="etablissement_id">
                        <p class="text-[10px] text-gray-400 mt-1">Non modifiable</p>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Rôle</label>
                        <input type="text" disabled value="Personnel"
                               class="w-full bg-gray-100 border border-gray-200 rounded-lg text-xs py-2.5 px-3 text-gray-500">
                        <input type="hidden" name="role" value="personnel">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-5">
                    <button type="button" onclick="closeModal('edit-user-modal')"
                            class="px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition">
                        Annuler
                    </button>
                    <button type="submit"
                            class="bg-amber-600 hover:bg-amber-700 text-white px-5 py-2 rounded-lg text-xs font-semibold transition shadow-sm">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const swalConfig = {
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold text-white mx-1',
            cancelButton: 'px-4 py-2 rounded-lg text-xs font-semibold text-white mx-1',
            title: 'text-base font-semibold',
            htmlContainer: 'text-xs text-gray-500'
        },
        buttonsStyling: false,
        reverseButtons: true
    };

    @if(session('success'))
        Swal.fire({ ...swalConfig, icon: 'success', title: 'Succès', text: @json(session('success')), timer: 2500, showConfirmButton: false });
    @endif
    @if($errors->any())
        Swal.fire({
            ...swalConfig,
            icon: 'error',
            title: 'Erreur de validation',
            html: `<ul class="text-left text-xs list-disc pl-5 space-y-0.5">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>`,
            confirmButtonText: 'Corriger'
        });
    @endif
    @if(session('error'))
        Swal.fire({ ...swalConfig, icon: 'error', title: 'Erreur', text: @json(session('error')), confirmButtonText: 'OK' });
    @endif

    function openModal(id) {
        const modal = document.getElementById(id);
        const content = document.getElementById(id + '-content');
        modal.classList.remove('hidden'); modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        setTimeout(() => { content.classList.remove('scale-95', 'opacity-0'); content.classList.add('scale-100', 'opacity-100'); }, 10);
    }
    function closeModal(id) {
        const modal = document.getElementById(id);
        const content = document.getElementById(id + '-content');
        if (content) {
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
        }
        setTimeout(() => {
            modal.classList.remove('flex'); modal.classList.add('hidden');
            document.body.style.overflow = '';
        }, 300);
    }

    document.getElementById('searchEmployee')?.addEventListener('keyup', function() {
        const term = this.value.toLowerCase();
        let visible = 0;
        document.querySelectorAll('.employee-row').forEach(row => {
            const match = (row.dataset.name || '').includes(term)
                       || (row.dataset.email || '').includes(term)
                       || (row.dataset.position || '').includes(term);
            row.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        const info = document.getElementById('paginationInfo');
        if (info) info.textContent = `Affichage de ${visible} sur ${visible} employé(s)`;
    });

    function openEditModal(id) {
        const p = personnelsData.find(x => x.id === id);
        if (!p) return;
        document.getElementById('edit_nom').value = p.nom ?? '';
        document.getElementById('edit_prenom').value = p.prenom ?? '';
        document.getElementById('edit_telephone').value = p.telephone ?? '';
        document.getElementById('edit_email').value = p.email ?? '';
        const etabNom = p.etablissement?.nom ?? (p.etablissement_id ? 'Établissement #' + p.etablissement_id : '—');
        document.getElementById('edit_etablissement_text').value = etabNom;
        document.getElementById('edit_etablissement_id').value = p.etablissement_id ?? '';
        document.getElementById('editForm').action = "{{ url('client/personnel') }}/" + id;
        openModal('edit-user-modal');
    }

    function submitForm(method, url) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="${method}">`;
        document.body.appendChild(form);
        form.submit();
    }

    function confirmDelete(id, name) {
        Swal.fire({
            ...swalConfig,
            title: 'Supprimer cet utilisateur ?',
            html: `<strong class="text-rose-600">${name}</strong> sera définitivement supprimé.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler',
            iconColor: '#e11d48'
        }).then(r => { if (r.isConfirmed) submitForm('DELETE', `{{ url('client/personnel') }}/${id}`); });
    }

    function confirmBlock(id, name) {
        Swal.fire({
            ...swalConfig,
            title: 'Bloquer cet utilisateur ?',
            html: `<strong class="text-amber-600">${name}</strong> n'aura plus accès à la plateforme.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Oui, bloquer',
            cancelButtonText: 'Annuler',
            iconColor: '#d97706'
        }).then(r => { if (r.isConfirmed) submitForm('PATCH', `{{ url('client/personnel') }}/${id}/block`); });
    }

    function confirmUnblock(id, name) {
        Swal.fire({
            ...swalConfig,
            title: 'Débloquer cet utilisateur ?',
            html: `<strong class="text-emerald-600">${name}</strong> pourra à nouveau accéder à la plateforme.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Oui, débloquer',
            cancelButtonText: 'Annuler',
            iconColor: '#059669'
        }).then(r => { if (r.isConfirmed) submitForm('PATCH', `{{ url('client/personnel') }}/${id}/unblock`); });
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') ['add-user-modal', 'edit-user-modal'].forEach(id => {
            const m = document.getElementById(id);
            if (m && m.classList.contains('flex')) closeModal(id);
        });
    });
</script>
@endpush