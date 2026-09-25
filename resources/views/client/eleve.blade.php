@extends('client.layouts.app')
@section('title', 'EduManager - Eleves')
@section('content')

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Gestion des Élèves</h2>
        <p class="text-sm text-gray-500 mt-1">Gérez l'ensemble des élèves inscrits dans votre établissement</p>
    </div>
    <button onclick="openModal('modal-standard')" type="button" class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-xs font-semibold transition shadow-sm">
        <span class="material-symbols-outlined text-sm">person_add</span>
        Nouvel élève
    </button>
</div>

<div class="grid grid-cols-2 gap-3 sm:gap-4 mb-6">
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                <span class="material-symbols-outlined text-lg">school</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Inscrits</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $totalStudents ?? 0 }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Élèves au total</p>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                <span class="material-symbols-outlined text-lg">meeting_room</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Classes</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $activeClasses ?? 0 }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Classes actives</p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
            <span class="material-symbols-outlined text-base">list_alt</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-900">Liste des élèves</h3>
            <p class="text-[11px] text-gray-500">{{ $students->total() ?? 0 }} élève(s) enregistré(s)</p>
        </div>
    </div>

    <form method="GET" action="{{ route('client.eleve') }}" class="px-5 py-4 border-b border-gray-100 bg-gray-50/30">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom ou matricule"
                   class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
            <select name="niveau_id" class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                <option value="">Tous les niveaux</option>
                @foreach($levels ?? [] as $level)
                <option value="{{ $level['id'] }}" @selected((string) request('niveau_id') === (string) $level['id'])>{{ $level['name'] }}</option>
                @endforeach
            </select>
            <select name="id_serie" class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                <option value="">Toutes les séries</option>
                @foreach($series ?? [] as $serie)
                <option value="{{ $serie->id }}" @selected((string) request('id_serie') === (string) $serie->id)>{{ $serie->nom_serie }}</option>
                @endforeach
            </select>
            <select name="classe_id" class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                <option value="">Toutes les classes</option>
                @foreach($classes ?? [] as $classe)
                <option value="{{ $classe['id'] }}" @selected((string) request('classe_id') === (string) $classe['id'])>{{ $classe['name'] }}</option>
                @endforeach
            </select>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold transition shadow-sm inline-flex items-center justify-center gap-1.5 py-2.5">
                <span class="material-symbols-outlined text-sm">filter_alt</span>
                Filtrer
            </button>
        </div>
    </form>

    @if(($students ?? collect())->isEmpty())
        <div class="py-16 text-center">
            <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-2xl text-gray-300">school</span>
            </div>
            <p class="text-sm font-semibold text-gray-700">Aucun élève enregistré</p>
            <p class="text-xs text-gray-400 mt-1">Commencez par ajouter votre premier élève.</p>
        </div>
    @else
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/50">
                        <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">N°</th>
                        <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Nom & Prénoms</th>
                        <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Matricule</th>
                        <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Sexe</th>
                        <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Classe</th>
                        <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Niveau</th>
                        <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Série</th>
                        <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Naissance</th>
                        <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($students as $student)
                    @php
                        $sexe = strtolower(trim($student['sexe'] ?? ''));
                        if (in_array($sexe, ['m', 'masculin', 'male', 'homme', 'h'])) { $badge = 'bg-blue-50 text-blue-700'; $label = 'Masculin'; }
                        elseif (in_array($sexe, ['f', 'féminin', 'feminin', 'female', 'femme'])) { $badge = 'bg-pink-50 text-pink-700'; $label = 'Féminin'; }
                        else { $badge = 'bg-gray-100 text-gray-600'; $label = 'N/A'; }
                    @endphp
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-4 py-3 text-xs text-gray-500 font-semibold">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-semibold text-gray-900 whitespace-nowrap">{{ $student['lastname'] }} {{ $student['firstname'] }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $student['matricule'] ?? 'N/A' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $badge }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                {{ $label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-700">{{ $student['classe'] ?? $student['class'] ?? 'N/A' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-100 text-[10px] font-bold text-gray-700 uppercase">{{ $student['level'] }}</span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $student['serie'] ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $student['birthdate'] }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1.5">
                                <button onclick='viewStudent(@json($student))'
                                        class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center transition"
                                        title="Voir">
                                    <span class="material-symbols-outlined text-base">visibility</span>
                                </button>
                                <button onclick='editStudent(@json($student))'
                                        class="w-8 h-8 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600 flex items-center justify-center transition"
                                        title="Modifier">
                                    <span class="material-symbols-outlined text-base">edit</span>
                                </button>
                                <form action="{{ route('client.eleve.destroy', $student['id']) }}" method="POST" class="inline delete-student-form">
                                    @csrf @method('DELETE')
                                    <button type="button"
                                            data-name="{{ $student['firstname'] }} {{ $student['lastname'] }}"
                                            class="delete-student-btn w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition"
                                            title="Supprimer">
                                        <span class="material-symbols-outlined text-base">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="md:hidden divide-y divide-gray-100">
            @foreach($students as $student)
            @php
                $sexe = strtolower(trim($student['sexe'] ?? ''));
                if (in_array($sexe, ['m', 'masculin', 'male', 'homme', 'h'])) { $badge = 'bg-blue-50 text-blue-700'; $label = 'M'; }
                elseif (in_array($sexe, ['f', 'féminin', 'feminin', 'female', 'femme'])) { $badge = 'bg-pink-50 text-pink-700'; $label = 'F'; }
                else { $badge = 'bg-gray-100 text-gray-600'; $label = '?'; }
            @endphp
            <div class="p-4 space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-sm font-bold flex-shrink-0">
                            {{ strtoupper(substr($student['lastname'] ?? 'E', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $student['lastname'] }} {{ $student['firstname'] }}</p>
                            <p class="text-[11px] text-gray-500 truncate">{{ $student['matricule'] ?? 'N/A' }} — {{ $student['classe'] ?? $student['class'] ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $badge }} flex-shrink-0">{{ $label }}</span>
                </div>
                <div class="flex items-center gap-2 pt-1">
                    <button onclick='viewStudent(@json($student))'
                            class="flex-1 h-9 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold flex items-center justify-center gap-1.5 transition">
                        <span class="material-symbols-outlined text-sm">visibility</span>Voir
                    </button>
                    <button onclick='editStudent(@json($student))'
                            class="flex-1 h-9 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600 text-xs font-semibold flex items-center justify-center gap-1.5 transition">
                        <span class="material-symbols-outlined text-sm">edit</span>Modifier
                    </button>
                    <form action="{{ route('client.eleve.destroy', $student['id']) }}" method="POST" class="delete-student-form">
                        @csrf @method('DELETE')
                        <button type="button"
                                data-name="{{ $student['firstname'] }} {{ $student['lastname'] }}"
                                class="delete-student-btn w-9 h-9 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition">
                            <span class="material-symbols-outlined text-base">delete</span>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

        <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <span class="text-[11px] text-gray-500">
                {{ $students->firstItem() ?? 0 }} - {{ $students->lastItem() ?? 0 }} sur {{ $students->total() ?? 0 }}
            </span>
            <div class="text-xs">{{ $students->links() ?? '' }}</div>
        </div>
    @endif
</div>

{{-- MODAL AJOUTER --}}
<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="modal-standard">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('modal-standard')"></div>
    <div class="bg-white w-full max-w-4xl max-h-[92vh] rounded-2xl shadow-2xl overflow-hidden flex flex-col transform transition-all duration-300 scale-95 opacity-0 relative z-10" id="modal-standard-content">

        <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center flex-shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <span class="material-symbols-outlined text-base">person_add</span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Ajouter un élève</h3>
                    <p class="text-[11px] text-gray-500">Enregistrez un nouvel élève et son parent</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modal-standard')" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center transition">
                <span class="material-symbols-outlined text-gray-500">close</span>
            </button>
        </div>

        <form class="flex-1 overflow-y-auto p-5" id="form-standard" action="{{ route('client.eleve.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="type_eleve" value="nouveau">
            <input type="hidden" name="nom" id="stdLastnameHidden">
            <input type="hidden" name="prenom" id="stdFirstnameHidden">
            <input type="hidden" name="matricule" id="stdMatriculeHidden">
            <input type="hidden" name="sexe" id="stdSexeHidden">
            <input type="hidden" name="date_naissance" id="stdBirthdateHidden">
            <input type="hidden" name="lieu_naissance" id="stdBirthPlaceHidden">
            <input type="hidden" name="classe_id" id="stdClasseHidden">
            <input type="hidden" name="niveau_id" id="stdLevelHidden">
            <input type="hidden" name="parent_nom" id="parentLastnameHidden">
            <input type="hidden" name="parent_prenom" id="parentFirstnameHidden">
            <input type="hidden" name="parent_telephone" id="parentPhoneHidden">
            <input type="hidden" name="parent_email" id="parentEmailHidden">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                <div class="bg-gray-50/50 rounded-2xl border border-gray-100 p-4 space-y-3.5">
                    <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                        <div class="w-6 h-6 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600">
                            <span class="material-symbols-outlined text-sm">person</span>
                        </div>
                        <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider">Informations élève</h4>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Nom <span class="text-rose-500">*</span></label>
                            <input id="stdLastname" type="text" required
                                   class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Prénom <span class="text-rose-500">*</span></label>
                            <input id="stdFirstname" type="text" required
                                   class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Matricule <span class="text-rose-500">*</span></label>
                        <input id="stdMatricule" type="text" required
                               class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Sexe <span class="text-rose-500">*</span></label>
                        <div class="flex gap-2">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="sexe" value="Masculin" class="peer sr-only" checked>
                                <div class="flex items-center justify-center gap-1.5 py-2 rounded-lg border border-gray-200 bg-white text-xs font-semibold text-gray-600 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 transition">
                                    <span class="material-symbols-outlined text-sm">male</span>
                                    Masculin
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="sexe" value="Féminin" class="peer sr-only">
                                <div class="flex items-center justify-center gap-1.5 py-2 rounded-lg border border-gray-200 bg-white text-xs font-semibold text-gray-600 peer-checked:border-pink-500 peer-checked:bg-pink-50 peer-checked:text-pink-700 transition">
                                    <span class="material-symbols-outlined text-sm">female</span>
                                    Féminin
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Date de naissance <span class="text-rose-500">*</span></label>
                            <input id="birthdate-std" type="date" required
                                   class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                            <p class="hidden text-[10px] text-rose-600 mt-1 flex items-center gap-1" id="age-warning-std">
                                <span class="material-symbols-outlined text-[12px]">warning</span>
                                Âge minimum : 5 ans.
                            </p>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Lieu de naissance</label>
                            <input id="stdBirthPlace" type="text"
                                   class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Nationalité <span class="text-rose-500">*</span></label>
                        <select id="nationalite" name="nationalite" placeholder="Sélectionner une nationalité"
                                class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition ts-wrapper-custom">
                            <option value="">Sélectionner une nationalité</option>
                            @foreach(config('nationalities') as $nationalite)
                            <option value="{{ $nationalite }}">{{ $nationalite }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Interne <span class="text-rose-500">*</span></label>
                            <select id="stdInterne" name="interne" required
                                    class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                                <option value="1" @selected(old('interne') === '1')>Oui</option>
                                <option value="0" @selected(old('interne', '0') === '0')>Non</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Affecté <span class="text-rose-500">*</span></label>
                            <select id="stdAffecte" name="affecte" required
                                    class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                                <option value="1" @selected(old('affecte') === '1')>Oui</option>
                                <option value="0" @selected(old('affecte', '0') === '0')>Non</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Niveau</label>
                            <select id="stdLevel"
                                    class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                                <option value="">Sélectionner un niveau</option>
                                @foreach($levels ?? [] as $level)
                                <option value="{{ $level['id'] }}">{{ $level['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Classe <span class="text-rose-500">*</span></label>
                            <select id="stdClasse" required disabled
                                    class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition disabled:bg-gray-100 disabled:text-gray-400">
                                <option value="">Choisir un niveau d'abord</option>
                            </select>
                        </div>
                    </div>

                    <div id="stdSerieWrapper">
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Série</label>
                        <select id="stdSerie" name="id_serie"
                                class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                            <option value="">Aucune série</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Photo de l'élève</label>
                        <div class="flex items-center gap-3">
                            <div id="photo-preview-std" class="w-16 h-16 rounded-2xl bg-gray-100 border border-dashed border-gray-300 flex items-center justify-center overflow-hidden flex-shrink-0">
                                <span class="material-symbols-outlined text-2xl text-gray-400">photo_camera</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <input id="stdPhoto" name="photo" type="file" accept="image/*" onchange="previewPhoto(this, 'photo-preview-std')"
                                       class="w-full text-[11px] text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 file:cursor-pointer">
                                <p class="text-[10px] text-gray-400 mt-1">JPG, PNG, GIF — max 2 MB</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50/50 rounded-2xl border border-gray-100 p-4 space-y-3.5">
                    <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                        <div class="w-6 h-6 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">
                            <span class="material-symbols-outlined text-sm">family_restroom</span>
                        </div>
                        <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider">Informations parent</h4>
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Nom du parent <span class="text-rose-500">*</span></label>
                        <input id="parentLastname" type="text" required
                               class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Prénom du parent <span class="text-rose-500">*</span></label>
                        <input id="parentFirstname" type="text" required
                               class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Type de parent</label>
                        <div class="flex gap-2">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="parent_type" value="pere" class="peer sr-only">
                                <div class="text-center py-2 rounded-lg border border-gray-200 bg-white text-xs font-semibold text-gray-600 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 transition">Père</div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="parent_type" value="mere" class="peer sr-only">
                                <div class="text-center py-2 rounded-lg border border-gray-200 bg-white text-xs font-semibold text-gray-600 peer-checked:border-pink-500 peer-checked:bg-pink-50 peer-checked:text-pink-700 transition">Mère</div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="parent_type" value="tuteur" class="peer sr-only" checked>
                                <div class="text-center py-2 rounded-lg border border-gray-200 bg-white text-xs font-semibold text-gray-600 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 transition">Tuteur</div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Téléphone <span class="text-rose-500">*</span></label>
                        <input id="parentPhone" type="tel" required placeholder="00 00 00 00"
                               class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Email</label>
                        <input id="parentEmail" type="email" placeholder="exemple@mail.com"
                               class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                    </div>
                </div>
            </div>

            <div class="mt-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-2 pt-4 border-t border-gray-100">
                <button type="button" onclick="closeModal('modal-standard')"
                        class="w-full sm:w-auto px-5 py-2.5 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    Annuler
                </button>
                <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-xs font-semibold transition shadow-sm">
                    <span class="material-symbols-outlined text-sm">save</span>
                    Enregistrer l'élève
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL VOIR --}}
<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="modal-view">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('modal-view')"></div>
    <div class="bg-white w-full max-w-3xl max-h-[92vh] rounded-2xl shadow-2xl overflow-hidden flex flex-col transform transition-all duration-300 scale-95 opacity-0 relative z-10" id="modal-view-content">

        <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center flex-shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <span class="material-symbols-outlined text-base">badge</span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Fiche élève</h3>
                    <p class="text-[11px] text-gray-500">Détails et informations personnelles</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Actif
                </span>
                <button type="button" onclick="closeModal('modal-view')" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center transition">
                    <span class="material-symbols-outlined text-gray-500">close</span>
                </button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-5">

            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 pb-5 mb-5 border-b border-gray-100">
                <div id="viewStudentPhotoContainer" class="w-20 h-20 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-2xl font-bold flex-shrink-0 overflow-hidden">
                    <span class="material-symbols-outlined text-4xl text-indigo-300">account_circle</span>
                </div>
                <div class="flex-1 text-center sm:text-left">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center justify-center sm:justify-start gap-1.5" id="viewStudentFullName">
                        —
                        <span class="material-symbols-outlined text-indigo-500 text-base">verified</span>
                    </h3>
                    <div class="flex flex-wrap gap-2 justify-center sm:justify-start mt-2">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-[10px] font-semibold">
                            <span class="material-symbols-outlined text-[12px]">badge</span>
                            <span id="viewStudentMatricule">-</span>
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-100 text-gray-700 rounded-lg text-[10px] font-semibold">
                            <span class="material-symbols-outlined text-[12px]">calendar_today</span>
                            <span id="viewStudentBirthdate">-</span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="mb-5">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-6 h-6 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600">
                        <span class="material-symbols-outlined text-sm">school</span>
                    </div>
                    <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider">Informations scolaires</h4>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="p-3 rounded-xl bg-gray-50/50 border border-gray-100 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 flex-shrink-0">
                            <span class="material-symbols-outlined text-base">class</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Classe</p>
                            <p class="text-xs font-semibold text-gray-900 mt-0.5 truncate" id="viewStudentClasse">-</p>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-gray-50/50 border border-gray-100 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 flex-shrink-0">
                            <span class="material-symbols-outlined text-base">account_tree</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Niveau</p>
                            <p class="text-xs font-semibold text-gray-900 mt-0.5 truncate" id="viewStudentNiveau">-</p>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-gray-50/50 border border-gray-100 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 flex-shrink-0">
                            <span class="material-symbols-outlined text-base">category</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Série</p>
                            <p class="text-xs font-semibold text-gray-900 mt-0.5 truncate" id="viewStudentSerie">—</p>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-gray-50/50 border border-gray-100 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 flex-shrink-0">
                            <span class="material-symbols-outlined text-base">wc</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Sexe</p>
                            <p class="text-xs font-semibold text-gray-900 mt-0.5 truncate" id="viewStudentSexeDetail">-</p>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-gray-50/50 border border-gray-100 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 flex-shrink-0">
                            <span class="material-symbols-outlined text-base">location_on</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Lieu de naissance</p>
                            <p class="text-xs font-semibold text-gray-900 mt-0.5 truncate" id="viewStudentBirthplace">-</p>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-gray-50/50 border border-gray-100 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 flex-shrink-0">
                            <span class="material-symbols-outlined text-base">flag</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Nationalité</p>
                            <p class="text-xs font-semibold text-gray-900 mt-0.5 truncate" id="viewStudentNationalite">-</p>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-gray-50/50 border border-gray-100 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 flex-shrink-0">
                            <span class="material-symbols-outlined text-base">bed</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Interne</p>
                            <p class="text-xs font-semibold text-gray-900 mt-0.5 truncate" id="viewStudentInterne">-</p>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-gray-50/50 border border-gray-100 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 flex-shrink-0">
                            <span class="material-symbols-outlined text-base">assignment_turned_in</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Affecté</p>
                            <p class="text-xs font-semibold text-gray-900 mt-0.5 truncate" id="viewStudentAffecte">-</p>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-6 h-6 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">
                        <span class="material-symbols-outlined text-sm">family_restroom</span>
                    </div>
                    <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider">Parents / Tuteurs</h4>
                </div>

                <div class="p-4 rounded-xl bg-gray-50/50 border border-gray-100">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Nom</p>
                            <p class="text-xs font-semibold text-gray-900 mt-0.5" id="viewStudentParentLastname">-</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Prénom</p>
                            <p class="text-xs font-semibold text-gray-900 mt-0.5" id="viewStudentParentFirstname">-</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Téléphone</p>
                            <p class="text-xs font-semibold text-gray-900 mt-0.5" id="viewStudentParentPhone">-</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Email</p>
                            <p class="text-xs font-semibold text-gray-900 mt-0.5 truncate" id="viewStudentParentEmail">-</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-center justify-between gap-2 flex-shrink-0">
            <div class="flex items-center gap-3 text-[10px] text-gray-500">
                <span class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">event_note</span>
                    Créé : <span id="viewStudentCreatedAt" class="text-gray-700 font-medium">-</span>
                </span>
                <span class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">update</span>
                    Modifié : <span id="viewStudentUpdatedAt" class="text-gray-700 font-medium">-</span>
                </span>
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="button" onclick="window.print()"
                        class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 bg-white border border-gray-200 hover:bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-xs font-semibold transition">
                    <span class="material-symbols-outlined text-sm">print</span>
                    Imprimer
                </button>
                <button type="button" onclick="closeModal('modal-view')"
                        class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-xs font-semibold transition shadow-sm">
                    <span class="material-symbols-outlined text-sm">check</span>
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL MODIFIER --}}
<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="modal-edit">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('modal-edit')"></div>
    <div class="bg-white w-full max-w-4xl max-h-[92vh] rounded-2xl shadow-2xl overflow-hidden flex flex-col transform transition-all duration-300 scale-95 opacity-0 relative z-10" id="modal-edit-content">

        <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center flex-shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                    <span class="material-symbols-outlined text-base">edit</span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Modifier l'élève</h3>
                    <p class="text-[11px] text-gray-500">Mettre à jour les informations</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modal-edit')" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center transition">
                <span class="material-symbols-outlined text-gray-500">close</span>
            </button>
        </div>

        <form class="flex-1 overflow-y-auto p-5" id="form-edit" action="" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="editEleveId" id="editEleveId" value="">
            <input type="hidden" name="type_eleve" value="nouveau">
            <input type="hidden" name="nom" id="editLastnameHidden">
            <input type="hidden" name="prenom" id="editFirstnameHidden">
            <input type="hidden" name="matricule" id="editMatriculeHidden">
            <input type="hidden" name="sexe" id="editSexeHidden">
            <input type="hidden" name="date_naissance" id="editBirthdateHidden">
            <input type="hidden" name="lieu_naissance" id="editBirthPlaceHidden">
            <input type="hidden" name="classe_id" id="editClasseHidden">
            <input type="hidden" name="niveau_id" id="editLevelHidden">
            <input type="hidden" name="parent_nom" id="editParentLastnameHidden">
            <input type="hidden" name="parent_prenom" id="editParentFirstnameHidden">
            <input type="hidden" name="parent_telephone" id="editParentPhoneHidden">
            <input type="hidden" name="parent_email" id="editParentEmailHidden">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                <div class="bg-gray-50/50 rounded-2xl border border-gray-100 p-4 space-y-3.5">
                    <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                        <div class="w-6 h-6 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600">
                            <span class="material-symbols-outlined text-sm">person</span>
                        </div>
                        <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider">Informations élève</h4>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Nom <span class="text-rose-500">*</span></label>
                            <input id="editLastname" type="text" required
                                   class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Prénom <span class="text-rose-500">*</span></label>
                            <input id="editFirstname" type="text" required
                                   class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Matricule <span class="text-rose-500">*</span></label>
                        <input id="editMatricule" type="text" required
                               class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Sexe <span class="text-rose-500">*</span></label>
                        <div class="flex gap-2">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="edit_sexe" value="Masculin" class="peer sr-only">
                                <div class="flex items-center justify-center gap-1.5 py-2 rounded-lg border border-gray-200 bg-white text-xs font-semibold text-gray-600 peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:text-amber-700 transition">
                                    <span class="material-symbols-outlined text-sm">male</span>
                                    Masculin
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="edit_sexe" value="Féminin" class="peer sr-only">
                                <div class="flex items-center justify-center gap-1.5 py-2 rounded-lg border border-gray-200 bg-white text-xs font-semibold text-gray-600 peer-checked:border-pink-500 peer-checked:bg-pink-50 peer-checked:text-pink-700 transition">
                                    <span class="material-symbols-outlined text-sm">female</span>
                                    Féminin
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Date de naissance <span class="text-rose-500">*</span></label>
                            <input id="editBirthdate" type="date" required
                                   class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Lieu de naissance</label>
                            <input id="editBirthPlace" type="text"
                                   class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Nationalité</label>
                        <select id="editNationalite" name="nationalite" placeholder="Sélectionner une nationalité"
                                class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition ts-wrapper-custom">
                            <option value="">Sélectionner une nationalité</option>
                            @foreach(config('nationalities') as $nationalite)
                            <option value="{{ $nationalite }}">{{ $nationalite }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Interne <span class="text-rose-500">*</span></label>
                            <select id="editInterne" name="interne" required
                                    class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition">
                                <option value="1">Oui</option>
                                <option value="0">Non</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Affecté <span class="text-rose-500">*</span></label>
                            <select id="editAffecte" name="affecte" required
                                    class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition">
                                <option value="1">Oui</option>
                                <option value="0">Non</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Niveau</label>
                            <select id="editLevel"
                                    class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition">
                                <option value="">Sélectionner un niveau</option>
                                @foreach($levels ?? [] as $level)
                                <option value="{{ $level['id'] }}">{{ $level['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Classe <span class="text-rose-500">*</span></label>
                            <select id="editClasse" required disabled
                                    class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition disabled:bg-gray-100 disabled:text-gray-400">
                                <option value="">Choisir un niveau d'abord</option>
                            </select>
                        </div>
                    </div>

                    <div id="editSerieWrapper">
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Série</label>
                        <select id="editSerie" name="id_serie"
                                class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition">
                            <option value="">Aucune série</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Photo de l'élève</label>
                        <div class="flex items-center gap-3">
                            <div id="photo-preview-edit" class="w-16 h-16 rounded-2xl bg-gray-100 border border-dashed border-gray-300 flex items-center justify-center overflow-hidden flex-shrink-0 text-xs font-bold text-gray-500">
                                <span class="material-symbols-outlined text-2xl text-gray-400">photo_camera</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <input id="editPhoto" name="photo" type="file" accept="image/*" onchange="previewPhoto(this, 'photo-preview-edit')"
                                       class="w-full text-[11px] text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-amber-50 file:text-amber-600 hover:file:bg-amber-100 file:cursor-pointer">
                                <p class="text-[10px] text-gray-400 mt-1">JPG, PNG, GIF — max 2 MB</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50/50 rounded-2xl border border-gray-100 p-4 space-y-3.5">
                    <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                        <div class="w-6 h-6 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">
                            <span class="material-symbols-outlined text-sm">family_restroom</span>
                        </div>
                        <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider">Informations parent</h4>
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Nom du parent <span class="text-rose-500">*</span></label>
                        <input id="editParentLastname" type="text" required
                               class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Prénom du parent <span class="text-rose-500">*</span></label>
                        <input id="editParentFirstname" type="text" required
                               class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Téléphone <span class="text-rose-500">*</span></label>
                        <input id="editParentPhone" type="tel" required
                               class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Email</label>
                        <input id="editParentEmail" type="email"
                               class="w-full bg-white border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition">
                    </div>
                </div>
            </div>

            <div class="mt-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-2 pt-4 border-t border-gray-100">
                <button type="button" onclick="closeModal('modal-edit')"
                        class="w-full sm:w-auto px-5 py-2.5 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    Annuler
                </button>
                <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 bg-amber-600 hover:bg-amber-700 text-white px-5 py-2.5 rounded-lg text-xs font-semibold transition shadow-sm">
                    <span class="material-symbols-outlined text-sm">save</span>
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    .ts-wrapper-custom .ts-control { border: none !important; box-shadow: none !important; padding: 0 !important; background: transparent !important; min-height: auto !important; height: auto !important; color: #64748B !important; display: block !important; width: 100% !important; }
    .ts-wrapper-custom .ts-control > * { padding: 8px 12px !important; font-size: 0.75rem !important; line-height: normal !important; }
    .ts-wrapper-custom .ts-control.has-items > * { color: #1e293b !important; }
    .ts-wrapper-custom .ts-control .item { margin: 0 !important; }
    .ts-wrapper-custom .ts-dropdown { border: 1px solid #cbd5e1 !important; border-radius: 8px !important; margin-top: 4px !important; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important; z-index: 50 !important; }
    .ts-wrapper-custom .ts-dropdown .option.active { background-color: #f1f5f9 !important; color: #1e293b !important; }
    .ts-wrapper-custom .ts-dropdown .option { padding: 10px 12px !important; }
    .ts-wrapper-custom .ts-control::after { display: none !important; }
</style>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@php
    $availableClassesForJs = collect($classes ?? [])->values();
    $availableSeriesForJs = ($series ?? collect())->map(function ($serie) {
        return [
            'id' => $serie->id,
            'class_ids' => $serie->classes->pluck('id')->values(),
            'nom_serie' => $serie->nom_serie,
        ];
    })->values();
@endphp
<script>
    const availableClasses = @json($availableClassesForJs);
    const availableSeries = @json($availableSeriesForJs);

    function populateClasses(levelSelectId, classSelectId, seriesSelectId, seriesWrapperId, selectedClassId = '', selectedSeriesId = '') {
        const levelId = document.getElementById(levelSelectId)?.value;
        const select = document.getElementById(classSelectId);
        if (!select) return;
        const matching = availableClasses.filter(classe => String(classe.level_id) === String(levelId));
        select.innerHTML = '<option value="">Sélectionner une classe</option>' + matching.map(classe =>
            `<option value="${classe.id}">${classe.name}</option>`
        ).join('');
        select.disabled = !levelId || matching.length === 0;
        select.value = matching.some(classe => String(classe.id) === String(selectedClassId)) ? String(selectedClassId) : '';
        populateSeries(classSelectId, seriesSelectId, seriesWrapperId, selectedSeriesId);
    }

    function populateSeries(classSelectId, seriesSelectId, wrapperId, selectedId = '') {
        const classId = document.getElementById(classSelectId)?.value;
        const select = document.getElementById(seriesSelectId);
        const wrapper = document.getElementById(wrapperId);
        if (!select || !wrapper) return;
        const matching = availableSeries.filter(serie => (serie.class_ids ?? []).map(String).includes(String(classId)));
        select.innerHTML = `<option value="">${matching.length ? 'Sélectionner une série' : 'Aucune série pour cette classe'}</option>` + matching.map(serie =>
            `<option value="${serie.id}">${serie.nom_serie}</option>`
        ).join('');
        wrapper.classList.remove('hidden');
        select.disabled = !classId || matching.length === 0;
        select.value = matching.some(serie => String(serie.id) === String(selectedId)) ? String(selectedId) : '';
    }

    function previewPhoto(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" alt="Photo" class="w-full h-full object-cover object-center">`;
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.innerHTML = `<span class="material-symbols-outlined text-2xl text-gray-400">photo_camera</span>`;
        }
    }

    function initNationalitySelect(selectId) {
        const select = document.getElementById(selectId);
        if (!select) return;
        if (window.tomSelectInstances && window.tomSelectInstances[selectId]) {
            window.tomSelectInstances[selectId].destroy();
        }
        const instance = new TomSelect(select, {
            create: false,
            sortField: { field: 'text', direction: 'asc' },
            searchField: ['text'],
            placeholder: 'Sélectionner une nationalité',
            render: {
                option: function(data, escape) {
                    return '<div class="flex items-center justify-between gap-3"><span>' + escape(data.text) + '</span></div>';
                },
                item: function(data, escape) {
                    return '<div>' + escape(data.text) + '</div>';
                }
            }
        });
        if (!window.tomSelectInstances) window.tomSelectInstances = {};
        window.tomSelectInstances[selectId] = instance;
    }

    document.addEventListener('DOMContentLoaded', function() {
        initNationalitySelect('nationalite');
        initNationalitySelect('editNationalite');

        document.getElementById('stdLevel')?.addEventListener('change', () => populateClasses('stdLevel', 'stdClasse', 'stdSerie', 'stdSerieWrapper'));
        document.getElementById('editLevel')?.addEventListener('change', () => populateClasses('editLevel', 'editClasse', 'editSerie', 'editSerieWrapper'));
        document.getElementById('stdClasse')?.addEventListener('change', () => populateSeries('stdClasse', 'stdSerie', 'stdSerieWrapper'));
        document.getElementById('editClasse')?.addEventListener('change', () => populateSeries('editClasse', 'editSerie', 'editSerieWrapper'));

        const oldLevelId = @json(old('niveau_id'));
        const oldClassId = @json(old('classe_id'));
        if (oldLevelId) {
            document.getElementById('stdLevel').value = oldLevelId;
            populateClasses('stdLevel', 'stdClasse', 'stdSerie', 'stdSerieWrapper', oldClassId, @json(old('id_serie')));
        }

        const standardForm = document.getElementById('form-standard');
        if (standardForm) {
            standardForm.addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                if (!validateAge($('#birthdate-std').val(), 'age-warning-std')) return;
                const selectedSexe = document.querySelector('input[name="sexe"]:checked');
                const sexeValue = selectedSexe ? selectedSexe.value : '';
                document.getElementById('stdLastnameHidden').value = document.getElementById('stdLastname').value;
                document.getElementById('stdFirstnameHidden').value = document.getElementById('stdFirstname').value;
                document.getElementById('stdMatriculeHidden').value = document.getElementById('stdMatricule').value;
                document.getElementById('stdSexeHidden').value = sexeValue;
                document.getElementById('stdBirthdateHidden').value = document.getElementById('birthdate-std').value;
                document.getElementById('stdBirthPlaceHidden').value = document.getElementById('stdBirthPlace').value;
                document.getElementById('stdClasseHidden').value = document.getElementById('stdClasse').value;
                document.getElementById('stdLevelHidden').value = document.getElementById('stdLevel').value;
                document.getElementById('parentLastnameHidden').value = document.getElementById('parentLastname').value;
                document.getElementById('parentFirstnameHidden').value = document.getElementById('parentFirstname').value;
                document.getElementById('parentPhoneHidden').value = document.getElementById('parentPhone').value;
                document.getElementById('parentEmailHidden').value = document.getElementById('parentEmail').value;
                HTMLFormElement.prototype.submit.call(standardForm);
            }, true);
        }

        const editForm = document.getElementById('form-edit');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                if (!validateAge($('#editBirthdate').val(), 'age-warning-std')) return;
                const selectedSexe = document.querySelector('input[name="edit_sexe"]:checked');
                const sexeValue = selectedSexe ? selectedSexe.value : '';
                document.getElementById('editLastnameHidden').value = document.getElementById('editLastname').value;
                document.getElementById('editFirstnameHidden').value = document.getElementById('editFirstname').value;
                document.getElementById('editMatriculeHidden').value = document.getElementById('editMatricule').value;
                document.getElementById('editSexeHidden').value = sexeValue;
                document.getElementById('editBirthdateHidden').value = document.getElementById('editBirthdate').value;
                document.getElementById('editBirthPlaceHidden').value = document.getElementById('editBirthPlace').value;
                document.getElementById('editClasseHidden').value = document.getElementById('editClasse').value;
                document.getElementById('editLevelHidden').value = document.getElementById('editLevel').value;
                document.getElementById('editParentLastnameHidden').value = document.getElementById('editParentLastname').value;
                document.getElementById('editParentFirstnameHidden').value = document.getElementById('editParentFirstname').value;
                document.getElementById('editParentPhoneHidden').value = document.getElementById('editParentPhone').value;
                document.getElementById('editParentEmailHidden').value = document.getElementById('editParentEmail').value;
                HTMLFormElement.prototype.submit.call(editForm);
            }, true);
        }

        document.querySelectorAll('.delete-student-btn').forEach((button) => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                const form = this.closest('form');
                Swal.fire({
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold text-white mx-1',
                        cancelButton: 'px-4 py-2 rounded-lg text-xs font-semibold text-white mx-1',
                        title: 'text-base font-semibold',
                        htmlContainer: 'text-xs text-gray-500'
                    },
                    buttonsStyling: false,
                    reverseButtons: true,
                    title: 'Supprimer cet élève ?',
                    html: `L'élève <strong class="text-rose-600">"${this.dataset.name}"</strong> sera définitivement supprimé.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Oui, supprimer',
                    cancelButtonText: 'Annuler',
                    iconColor: '#e11d48'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            }, true);
        });

        @if(session('success'))
            Swal.fire({
                customClass: { popup: 'rounded-2xl', title: 'text-base font-semibold', htmlContainer: 'text-xs text-gray-500' },
                buttonsStyling: false,
                icon: 'success', title: 'Succès', text: @json(session('success')),
                timer: 2500, showConfirmButton: false, position: 'center'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                customClass: { popup: 'rounded-2xl', confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold text-white', title: 'text-base font-semibold', htmlContainer: 'text-xs text-gray-500' },
                buttonsStyling: false,
                icon: 'error', title: 'Erreur', text: @json(session('error')),
                confirmButtonText: 'OK', position: 'center'
            });
        @endif
    });

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
        content.classList.remove('scale-100', 'opacity-100'); content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => { modal.classList.remove('flex'); modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
    }

    function openViewModal(student) {
        const fullName = `${student.firstname ?? ''} ${student.lastname ?? ''}`.trim();
        document.getElementById('viewStudentFullName').textContent = fullName || '-';
        document.getElementById('viewStudentMatricule').textContent = student.matricule ?? 'N/A';
        document.getElementById('viewStudentBirthdate').textContent = student.birthdate ?? 'N/A';
        document.getElementById('viewStudentBirthplace').textContent = student.birthplace ?? 'N/A';
        document.getElementById('viewStudentClasse').textContent = student.classe ?? student.class ?? 'N/A';
        document.getElementById('viewStudentNiveau').textContent = student.level ?? 'N/A';
        document.getElementById('viewStudentSerie').textContent = student.serie ?? '—';
        document.getElementById('viewStudentNationalite').textContent = student.nationalite ?? '-';
        document.getElementById('viewStudentInterne').textContent = student.interne ? 'Oui' : 'Non';
        document.getElementById('viewStudentAffecte').textContent = student.affecte ? 'Oui' : 'Non';

        const sexeRaw = (student.sexe ?? '').toString().trim();
        const sexeLower = sexeRaw.toLowerCase();
        let sexeDisplay = 'Non renseigné';
        const masculinValues = ['m', 'masculin', 'male', 'homme', 'h'];
        const femininValues = ['f', 'féminin', 'feminin', 'female', 'femme'];
        if (masculinValues.includes(sexeLower)) sexeDisplay = 'Masculin';
        else if (femininValues.includes(sexeLower)) sexeDisplay = 'Féminin';
        document.getElementById('viewStudentSexeDetail').textContent = sexeDisplay;

        const parentLastname = student.parent_lastname ?? null;
        const parentFirstname = student.parent_firstname ?? null;
        const parentPhone = student.parent_phone ?? null;
        const parentEmail = student.parent_email ?? null;
        document.getElementById('viewStudentParentLastname').textContent = parentLastname ?? '-';
        document.getElementById('viewStudentParentFirstname').textContent = parentFirstname ?? '-';
        document.getElementById('viewStudentParentPhone').textContent = parentPhone ?? '-';
        document.getElementById('viewStudentParentEmail').textContent = parentEmail ?? '-';
        document.getElementById('viewStudentCreatedAt').textContent = student.created_at ?? 'N/A';
        document.getElementById('viewStudentUpdatedAt').textContent = student.updated_at ?? 'N/A';

        const photoContainer = document.getElementById('viewStudentPhotoContainer');
        const photoUrl = student.photo_url ?? student.photo ?? null;
        const initials = `${(student.firstname ?? '')[0] ?? ''}${(student.lastname ?? '')[0] ?? ''}`.toUpperCase() || '?';
        if (photoUrl) {
            const image = document.createElement('img');
            image.src = photoUrl;
            image.alt = `Photo de ${fullName || 'l’élève'}`;
            image.className = 'w-full h-full object-cover object-center';
            image.addEventListener('error', () => {
                photoContainer.innerHTML = `<span class="text-3xl font-bold text-indigo-600">${initials}</span>`;
            }, { once: true });
            photoContainer.replaceChildren(image);
        } else {
            photoContainer.innerHTML = `<span class="text-3xl font-bold text-indigo-600">${initials}</span>`;
        }
        openModal('modal-view');
    }

    function openEditModal(student) {
        document.getElementById('editEleveId').value = student.id;
        document.getElementById('editLastname').value = student.lastname ?? '';
        document.getElementById('editFirstname').value = student.firstname ?? '';
        document.getElementById('editMatricule').value = student.matricule ?? '';
        document.getElementById('editBirthdate').value = student.birthdate_raw ?? '';
        document.getElementById('editBirthPlace').value = student.birthplace ?? '';
        document.getElementById('editLevel').value = student.level_id ?? '';
        populateClasses('editLevel', 'editClasse', 'editSerie', 'editSerieWrapper', student.class_id ?? '', student.serie_id ?? '');
        document.getElementById('editParentLastname').value = student.parent_lastname ?? '';
        document.getElementById('editParentFirstname').value = student.parent_firstname ?? '';
        document.getElementById('editParentPhone').value = student.parent_phone ?? '';
        document.getElementById('editParentEmail').value = student.parent_email ?? '';

        const sexe = student.sexe ?? '';
        const sexeLower = sexe.toLowerCase();
        const masculinValues = ['m', 'masculin', 'male', 'homme', 'h'];
        const femininValues = ['f', 'féminin', 'feminin', 'female', 'femme'];
        if (masculinValues.includes(sexeLower) || sexe === 'Masculin') {
            document.querySelector('input[name="edit_sexe"][value="Masculin"]').checked = true;
        } else if (femininValues.includes(sexeLower) || sexe === 'Féminin') {
            document.querySelector('input[name="edit_sexe"][value="Féminin"]').checked = true;
        }
        document.getElementById('editLastnameHidden').value = student.lastname ?? '';
        document.getElementById('editFirstnameHidden').value = student.firstname ?? '';
        document.getElementById('editMatriculeHidden').value = student.matricule ?? '';
        document.getElementById('editSexeHidden').value = sexe;
        document.getElementById('editBirthdateHidden').value = student.birthdate_raw ?? '';
        document.getElementById('editBirthPlaceHidden').value = student.birthplace ?? '';
        document.getElementById('editClasseHidden').value = student.class_id ?? '';
        document.getElementById('editLevelHidden').value = student.level_id ?? '';

        const nationaliteSelect = document.getElementById('editNationalite');
        if (nationaliteSelect) {
            nationaliteSelect.value = student.nationalite ?? '';
            if (window.tomSelectInstances?.editNationalite) {
                window.tomSelectInstances.editNationalite.setValue(student.nationalite ?? '', true);
            }
        }
        document.getElementById('editInterne').value = student.interne ? '1' : '0';
        document.getElementById('editAffecte').value = student.affecte ? '1' : '0';
        document.getElementById('editParentLastnameHidden').value = student.parent_lastname ?? '';
        document.getElementById('editParentFirstnameHidden').value = student.parent_firstname ?? '';
        document.getElementById('editParentPhoneHidden').value = student.parent_phone ?? '';
        document.getElementById('editParentEmailHidden').value = student.parent_email ?? '';
        document.getElementById('form-edit').action = `/client/eleve/${student.id}`;

        const photoPreviewEdit = document.getElementById('photo-preview-edit');
        const photoInputEdit = document.getElementById('editPhoto');
        photoInputEdit.value = '';
        const photoUrlEdit = student.photo_url ?? student.photo ?? student.photo_path ?? null;
        const editInitials = `${(student.firstname ?? '')[0] ?? ''}${(student.lastname ?? '')[0] ?? ''}`.toUpperCase() || '?';
        if (photoUrlEdit) {
            const image = document.createElement('img');
            image.src = photoUrlEdit;
            image.alt = `Photo de ${student.firstname ?? ''} ${student.lastname ?? ''}`.trim();
            image.className = 'block w-full h-full object-cover object-center';
            image.addEventListener('error', () => {
                photoPreviewEdit.innerHTML = `<span class="text-xl font-bold text-amber-600">${editInitials}</span>`;
            }, { once: true });
            photoPreviewEdit.replaceChildren(image);
        } else {
            photoPreviewEdit.innerHTML = `<span class="text-xl font-bold text-amber-600">${editInitials}</span>`;
        }
        openModal('modal-edit');
    }

    function viewStudent(student) { openViewModal(student); }
    function editStudent(student) { openEditModal(student); }

    function validateAge(inputDate, warningId) {
        if (!inputDate) return true;
        const birth = new Date(inputDate);
        const today = new Date();
        let age = today.getFullYear() - birth.getFullYear();
        const m = today.getMonth() - birth.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
        if (age < 5) {
            $(`#${warningId}`).removeClass('hidden');
            return false;
        } else {
            $(`#${warningId}`).addClass('hidden');
            return true;
        }
    }

    $(document).ready(function() {
        $('#birthdate-std').on('change', function() {
            validateAge($(this).val(), 'age-warning-std');
        });
        $(document).on('keydown', function(e) {
            if (e.key === "Escape") {
                ['modal-standard', 'modal-view', 'modal-edit'].forEach(id => {
                    const modal = document.getElementById(id);
                    if (modal && modal.classList.contains('flex')) closeModal(id);
                });
            }
        });
    });
</script>
@endpush