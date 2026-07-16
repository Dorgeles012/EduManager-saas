@extends('client.layouts.app')
@section('title', 'EduManager - Enseignants')
@section('content')
<div class="flex justify-between items-end mb-10">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-primary">Gestion des Enseignants</h2>
        <p class="text-body-md text-on-surface-variant">Gérez les enseignants de votre établissement avec précision et clarté.</p>
    </div>
    <button class="inline-flex items-center px-5 py-2.5 bg-primary text-white font-label-md text-label-md rounded-lg hover:bg-primary/90 active:scale-95 transition-all shadow-md" onclick="openModal('add-teacher-modal')">
        <span class="material-symbols-outlined mr-2">add</span>
        Ajouter un enseignant
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <div class="bg-surface-container-lowest rounded-xl p-6 custom-shadow border border-outline-variant/30 flex items-center space-x-5">
        <div class="w-14 h-14 rounded-lg bg-gradient-to-br from-primary to-primary-container flex items-center justify-center shadow-lg">
            <span class="material-symbols-outlined text-white text-3xl">school</span>
        </div>
        <div>
            <p class="text-label-sm text-on-surface-variant uppercase tracking-wider">Enseignants</p>
            <p class="font-headline-md text-headline-md text-on-surface" id="totalTeachers">{{ $totalTeachers ?? 1 }}</p>
        </div>
    </div>
    <div class="bg-surface-container-lowest rounded-xl p-6 custom-shadow border border-outline-variant/30 flex items-center space-x-5">
        <div class="w-14 h-14 rounded-lg bg-orange-500 flex items-center justify-center shadow-lg">
            <span class="material-symbols-outlined text-white text-3xl">book</span>
        </div>
        <div>
            <p class="text-label-sm text-on-surface-variant uppercase tracking-wider">Matières</p>
            <p class="font-headline-md text-headline-md text-on-surface">{{ $totalSubjects ?? 3 }}</p>
        </div>
    </div>
</div>

<div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant/30 overflow-hidden">
    <div class="px-6 py-6 border-b border-surface-subtle flex flex-col md:flex-row md:items-center justify-between gap-4">
        <h4 class="font-headline-md text-headline-md flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">people</span>
            Liste des enseignants
        </h4>
        <div class="relative w-full md:w-80">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-xl">search</span>
            <input type="text" id="searchTeacher" placeholder="Rechercher un enseignant par nom, email ou matière..." class="w-full pl-10 pr-4 py-2.5 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-surface-container-lowest">
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm" id="teachersTable">
            <thead class="bg-surface-container-low">
                <tr>
                    <th class="px-4 py-3 font-label-sm text-label-sm text-on-surface-variant uppercase text-xs">N°</th>
                    <th class="px-4 py-3 font-label-sm text-label-sm text-on-surface-variant uppercase text-xs">Nom &amp; Prénoms</th>
                    <th class="px-4 py-3 font-label-sm text-label-sm text-on-surface-variant uppercase text-xs">Email</th>
                    <th class="px-4 py-3 font-label-sm text-label-sm text-on-surface-variant uppercase text-xs">Téléphone</th>
                    <th class="px-4 py-3 font-label-sm text-label-sm text-on-surface-variant uppercase text-xs">Matière</th>
                    <th class="px-4 py-3 font-label-sm text-label-sm text-on-surface-variant uppercase text-xs">Statut</th>
                    <th class="px-4 py-3 font-label-sm text-label-sm text-on-surface-variant uppercase text-xs text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-subtle" id="teachersTableBody">
                @forelse($teachers ?? [] as $teacher)
                <tr class="hover:bg-surface-subtle transition-colors teacher-row text-sm" id="teacher-row-{{ $teacher['id'] }}" data-name="{{ strtolower($teacher['firstname'] . ' ' . $teacher['lastname']) }}" data-email="{{ strtolower($teacher['email']) }}" data-subject="{{ strtolower($teacher['subject']) }}" data-position="{{ strtolower($teacher['subject']) }}">
                    <td class="px-4 py-3 text-on-surface-variant text-sm">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3 font-semibold text-on-surface text-sm">{{ ucfirst($teacher['firstname']) }} {{ ucfirst($teacher['lastname']) }}</td>
                    <td class="px-4 py-3 text-on-surface-variant text-sm">{{ $teacher['email'] }}</td>
                    <td class="px-4 py-3 text-on-surface-variant text-sm">{{ $teacher['phone'] }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2.5 py-1 bg-primary/10 text-primary text-xs font-bold rounded-full">{{ $teacher['subject'] }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2.5 py-1 bg-success-green/10 text-success-green text-xs font-bold rounded-full">
                            {{ ucfirst($teacher['status']) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end space-x-1.5 relative">
                            <button type="button" class="btn-plus" title="Gérer l'emploi du temps" onclick="handleEmploiTempsPlus({{ $teacher['id'] }}, this)">
                                <span class="material-symbols-outlined">add</span>
                            </button>
                            
                            <button class="w-8 h-8 flex items-center justify-center text-warning-amber hover:bg-warning-amber/10 rounded-full transition-all" onclick="openEditModal({{ json_encode($teacher) }})" title="Modifier">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </button>
                            <button class="w-8 h-8 flex items-center justify-center text-alert-red hover:bg-alert-red/10 rounded-full transition-all delete-teacher-btn" data-id="{{ $teacher['id'] }}" data-name="{{ $teacher['firstname'] }} {{ $teacher['lastname'] }}" type="button" title="Supprimer">
                                <span class="material-symbols-outlined text-base">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="emptyRow">
                    <td colspan="7" class="px-4 py-12 text-center text-on-surface-variant text-sm">
                        <div class="flex flex-col items-center gap-4">
                            <span class="material-symbols-outlined text-5xl text-outline">school</span>
                            <p>Aucun enseignant trouvé</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-surface-subtle flex items-center justify-between">
        <span class="text-sm text-on-surface-variant" id="paginationInfo">
            @php
                $isPaginator = isset($teachers) && method_exists($teachers, 'links');
            @endphp
            @if($isPaginator)
                Affichage de {{ $teachers->firstItem() }} à {{ $teachers->lastItem() }} sur {{ $teachers->total() }} enseignants
            @else
                Affichage de {{ count($teachers ?? []) }} enseignant(s)
            @endif
        </span>
        <div class="flex space-x-2">
            @if($isPaginator)
                {{ $teachers->links() }}
            @endif
        </div>
    </div>
</div>

<!-- MODAL AJOUT -->
<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="add-teacher-modal">
    <div class="absolute inset-0 modal-overlay backdrop-blur-md bg-black/30" onclick="closeModal('add-teacher-modal')"></div>
    <div class="bg-white w-full max-w-4xl max-h-[90vh] rounded-2xl custom-shadow overflow-hidden transform transition-all duration-300 scale-95 opacity-0 flex flex-col" id="add-modal-content">
        <div class="px-8 py-5 border-b border-surface-subtle flex items-center justify-between bg-gradient-to-r from-primary to-primary/90 text-white flex-shrink-0">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-2xl">person_add</span>
                <h3 class="font-headline-md text-headline-md">Ajouter un enseignant</h3>
            </div>
            <button class="w-10 h-10 flex items-center justify-center hover:bg-white/20 rounded-full transition-all" onclick="closeModal('add-teacher-modal')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        
        <div class="flex-1 overflow-y-auto p-8 bg-gray-50/50">
            <!-- Message info -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl flex items-start">
                <span class="material-symbols-outlined text-blue-600 mr-3 mt-0.5">info</span>
                <div>
                    <p class="text-sm text-blue-800 font-medium">Mot de passe par défaut : <span class="font-mono bg-blue-100 px-2 py-0.5 rounded">12345678</span></p>
                    <p class="text-xs text-blue-600/80 mt-0.5">L'enseignant pourra modifier ce mot de passe lors de sa première connexion.</p>
                </div>
            </div>

            <form class="space-y-6" id="addTeacherForm">
                @csrf
                <!-- Section 1: Identité -->
                <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                    <h4 class="font-semibold text-gray-700 text-sm uppercase tracking-wider mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-base">badge</span>
                        Identité
                    </h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-1.5">Nom <span class="text-red-500">*</span></label>
                            <input class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-gray-50/50" id="addLastName" placeholder="Entrez le nom" required type="text">
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-1.5">Prénoms <span class="text-red-500">*</span></label>
                            <input class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-gray-50/50" id="addFirstName" placeholder="Entrez les prénoms" required type="text">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-1.5">Matricule <span class="text-red-500">*</span></label>
                            <input id="addMatricule" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-gray-50/50" placeholder="Ex: ENS-2024-001">
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-1.5">Sexe <span class="text-red-500">*</span></label>
                            <div class="flex gap-6 pt-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="addSexe" value="Masculin" required class="w-4 h-4 text-primary focus:ring-primary">
                                    <span class="text-sm text-gray-700">👨 Masculin</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="addSexe" value="Féminin" class="w-4 h-4 text-primary focus:ring-primary">
                                    <span class="text-sm text-gray-700">👩 Féminin</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Contact -->
                <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                    <h4 class="font-semibold text-gray-700 text-sm uppercase tracking-wider mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-base">contact_mail</span>
                        Contact
                    </h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                            <input class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-gray-50/50" id="addEmail" placeholder="exemple@mail.com" required type="email">
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-1.5">Téléphone <span class="text-red-500">*</span></label>
                            <input class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-gray-50/50" id="addPhone" placeholder="01 02 03 04 05" required type="tel">
                        </div>
                    </div>
                </div>

                <!-- Section 3: Enseignement -->
                <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                    <h4 class="font-semibold text-gray-700 text-sm uppercase tracking-wider mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-base">teaching</span>
                        Enseignement
                    </h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-1.5">Matières enseignées <span class="text-red-500">*</span></label>
                            <select multiple class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-gray-50/50" id="addSubjects" required>
                                @foreach($subjects ?? ['Anglais', 'Mathématiques', 'Physique chimie'] as $subject)
                                <option value="{{ is_array($subject) ? $subject['id'] : $subject }}">{{ is_array($subject) ? $subject['name'] : $subject }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 mt-1">Maintenez Ctrl/Cmd pour sélectionner plusieurs matières</p>
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-1.5">Années d'enseignement <span class="text-red-500">*</span></label>
                            <input id="addTeachingYears" type="number" min="0" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-gray-50/50" placeholder="0">
                        </div>
                    </div>
                </div>

                <!-- Section 4: Affectations -->
                <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                    <h4 class="font-semibold text-gray-700 text-sm uppercase tracking-wider mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-base">assignment</span>
                        Affectations
                    </h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-1.5">Classes affectées <span class="text-red-500">*</span></label>
                            <select id="addClasses" multiple required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-gray-50/50">
                                @foreach($classes as $classe)
                                <option value="{{ $classe->id }}">{{ $classe->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-1.5">Séries affectées</label>
                            <select id="addSeries" multiple class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-gray-50/50">
                                @foreach($series as $serie)
                                <option value="{{ $serie->id }}">{{ $serie->nom_serie }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 5: Photo -->
                <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                    <h4 class="font-semibold text-gray-700 text-sm uppercase tracking-wider mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-base">photo_camera</span>
                        Photo de profil
                    </h4>
                    <div class="flex items-center gap-6">
                        <div class="w-24 h-24 rounded-full bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden" id="addPhotoPreview">
                            <span class="material-symbols-outlined text-gray-400 text-4xl">person</span>
                        </div>
                        <div>
                            <label class="cursor-pointer inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium text-sm rounded-lg transition-all">
                                <span class="material-symbols-outlined mr-2 text-base">upload</span>
                                Choisir une photo
                                <input id="addPhoto" type="file" accept="image/png,image/jpeg,image/webp" class="hidden" onchange="previewImage(this, 'addPhotoPreview')">
                            </label>
                            <p class="text-xs text-gray-400 mt-1">PNG, JPG, WEBP • Max 2MB</p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="px-8 py-5 border-t border-surface-subtle bg-white flex space-x-3 justify-end flex-shrink-0">
            <button class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium text-sm rounded-lg hover:bg-gray-50 transition-all" onclick="closeModal('add-teacher-modal')" type="button">Annuler</button>
            <button class="px-8 py-2.5 bg-primary text-white font-medium text-sm rounded-lg hover:bg-primary/90 shadow-md active:scale-95 transition-all flex items-center gap-2" id="addTeacherBtn" type="button">
                <span class="material-symbols-outlined text-base">save</span>
                Enregistrer
            </button>
        </div>
    </div>
</div>

<!-- MODAL MODIFICATION -->
<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="edit-teacher-modal">
    <div class="absolute inset-0 modal-overlay backdrop-blur-md bg-black/30" onclick="closeModal('edit-teacher-modal')"></div>
    <div class="bg-white w-full max-w-4xl max-h-[90vh] rounded-2xl custom-shadow overflow-hidden transform transition-all duration-300 scale-95 opacity-0 flex flex-col" id="edit-modal-content">
        <div class="px-8 py-5 border-b border-surface-subtle flex items-center justify-between bg-gradient-to-r from-primary to-primary/90 text-white flex-shrink-0">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-2xl">edit_note</span>
                <h3 class="font-headline-md text-headline-md">Modifier l'enseignant</h3>
            </div>
            <button class="w-10 h-10 flex items-center justify-center hover:bg-white/20 rounded-full transition-all" onclick="closeModal('edit-teacher-modal')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        
        <div class="flex-1 overflow-y-auto p-8 bg-gray-50/50">
            <form class="space-y-6" id="editTeacherForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editTeacherId">
                
                <!-- Section 1: Identité -->
                <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                    <h4 class="font-semibold text-gray-700 text-sm uppercase tracking-wider mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-base">badge</span>
                        Identité
                    </h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-1.5">Nom <span class="text-red-500">*</span></label>
                            <input class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-gray-50/50" id="editLastName" required type="text">
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-1.5">Prénoms <span class="text-red-500">*</span></label>
                            <input class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-gray-50/50" id="editFirstName" required type="text">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-1.5">Matricule <span class="text-red-500">*</span></label>
                            <input id="editMatricule" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-gray-50/50">
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-1.5">Sexe <span class="text-red-500">*</span></label>
                            <div class="flex gap-6 pt-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="editSexe" value="Masculin" required class="w-4 h-4 text-primary focus:ring-primary">
                                    <span class="text-sm text-gray-700">👨 Masculin</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="editSexe" value="Féminin" class="w-4 h-4 text-primary focus:ring-primary">
                                    <span class="text-sm text-gray-700">👩 Féminin</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Contact -->
                <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                    <h4 class="font-semibold text-gray-700 text-sm uppercase tracking-wider mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-base">contact_mail</span>
                        Contact
                    </h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                            <input class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-gray-50/50" id="editEmail" required type="email">
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-1.5">Téléphone <span class="text-red-500">*</span></label>
                            <input class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-gray-50/50" id="editPhone" required type="tel">
                        </div>
                    </div>
                </div>

                <!-- Section 3: Enseignement -->
                <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                    <h4 class="font-semibold text-gray-700 text-sm uppercase tracking-wider mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-base">teaching</span>
                        Enseignement
                    </h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-1.5">Matières enseignées <span class="text-red-500">*</span></label>
                            <select multiple class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-gray-50/50" id="editSubjects" required>
                                @foreach($subjects ?? ['Anglais', 'Mathématiques', 'Physique chimie'] as $subject)
                                <option value="{{ is_array($subject) ? $subject['id'] : $subject }}">{{ is_array($subject) ? $subject['name'] : $subject }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 mt-1">Maintenez Ctrl/Cmd pour sélectionner plusieurs matières</p>
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-1.5">Années d'enseignement <span class="text-red-500">*</span></label>
                            <input id="editTeachingYears" type="number" min="0" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-gray-50/50">
                        </div>
                    </div>
                </div>

                <!-- Section 4: Affectations -->
                <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                    <h4 class="font-semibold text-gray-700 text-sm uppercase tracking-wider mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-base">assignment</span>
                        Affectations
                    </h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-1.5">Classes affectées <span class="text-red-500">*</span></label>
                            <select id="editClasses" multiple required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-gray-50/50">
                                @foreach($classes as $classe)
                                <option value="{{ $classe->id }}">{{ $classe->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-1.5">Séries affectées</label>
                            <select id="editSeries" multiple class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-gray-50/50">
                                @foreach($series as $serie)
                                <option value="{{ $serie->id }}">{{ $serie->nom_serie }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 5: Photo -->
                <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                    <h4 class="font-semibold text-gray-700 text-sm uppercase tracking-wider mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-base">photo_camera</span>
                        Photo de profil
                    </h4>
                    <div class="flex items-center gap-6">
                        <div class="w-24 h-24 rounded-full bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden" id="editPhotoPreview">
                            <span class="material-symbols-outlined text-gray-400 text-4xl">person</span>
                        </div>
                        <div>
                            <label class="cursor-pointer inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium text-sm rounded-lg transition-all">
                                <span class="material-symbols-outlined mr-2 text-base">upload</span>
                                Changer la photo
                                <input id="editPhoto" type="file" accept="image/png,image/jpeg,image/webp" class="hidden" onchange="previewImage(this, 'editPhotoPreview')">
                            </label>
                            <p class="text-xs text-gray-400 mt-1">PNG, JPG, WEBP • Max 2MB</p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="px-8 py-5 border-t border-surface-subtle bg-white flex space-x-3 justify-end flex-shrink-0">
            <button class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium text-sm rounded-lg hover:bg-gray-50 transition-all" onclick="closeModal('edit-teacher-modal')" type="button">Annuler</button>
            <button class="px-8 py-2.5 bg-primary text-white font-medium text-sm rounded-lg hover:bg-primary/90 shadow-md active:scale-95 transition-all flex items-center gap-2" id="editTeacherBtn" type="button">
                <span class="material-symbols-outlined text-base">save</span>
                Mettre à jour
            </button>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    .custom-shadow {
        box-shadow: 0 4px 12px rgba(55, 48, 163, 0.04);
    }
    .overflow-y-auto::-webkit-scrollbar {
        width: 6px;
    }
    .overflow-y-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #c7c7c7;
        border-radius: 10px;
    }
    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: #a0a0a0;
    }
    .modal-overlay {
        transition: backdrop-filter 0.3s ease;
    }
    #add-teacher-modal, #edit-teacher-modal {
        transition: opacity 0.3s ease;
    }
    .btn-plus {
        background-color: #2563EB !important;
        color: white !important;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
        border: none;
        cursor: pointer;
        padding: 0;
    }
    .btn-plus:hover {
        background-color: #1d4ed8 !important;
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
    }
    .btn-plus:active {
        transform: scale(0.95);
    }
    .btn-plus .material-symbols-outlined {
        font-size: 18px !important;
        font-weight: bold;
    }
    .btn-plus:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none !important;
    }
    
    .action-popup {
        position: fixed;
        z-index: 9999;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        padding: 8px 0;
        min-width: 220px;
        display: none;
        border: 1px solid #e5e7eb;
        animation: popupFadeIn 0.15s ease-out;
    }
    
    @keyframes popupFadeIn {
        from { opacity: 0; transform: translateY(8px) scale(0.96); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    
    .action-popup .popup-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 10px 18px;
        background: white;
        transition: background 0.15s;
        width: 100%;
        text-align: left;
        border: none;
        font-size: 14px;
        font-weight: 500;
        color: #1f2937;
        cursor: pointer;
    }
    
    .action-popup .popup-item:hover {
        background: #f3f4f6;
    }
    
    .action-popup .popup-item .material-symbols-outlined {
        font-size: 20px !important;
    }
    
    .action-popup-arrow {
        position: absolute;
        bottom: -8px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 0;
        border-left: 8px solid transparent;
        border-right: 8px solid transparent;
        border-top: 8px solid white;
        filter: drop-shadow(0 2px 2px rgba(0,0,0,0.05));
    }
    
    /* Tom Select personnalisé */
    .ts-wrapper .ts-control {
        border-color: #d1d5db !important;
        border-radius: 0.5rem !important;
        padding: 0.625rem 1rem !important;
        background-color: #f9fafb !important;
        min-height: 44px !important;
    }
    .ts-wrapper .ts-control:focus {
        border-color: #4f46e5 !important;
        box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2) !important;
    }
    .ts-wrapper .ts-dropdown {
        border-radius: 0.5rem !important;
        border-color: #d1d5db !important;
        margin-top: 4px !important;
    }
    .ts-wrapper .ts-dropdown .option.active {
        background-color: #4f46e5 !important;
        color: white !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Initialisation TomSelect
    ['addSubjects', 'editSubjects'].forEach(id => { 
        const element = document.getElementById(id); 
        if (element) {
            new TomSelect(element, { 
                maxItems: 3, 
                create: false, 
                placeholder: 'Choisir jusqu\'à 3 matières',
                plugins: ['remove_button'],
                sortField: 'text'
            });
        }
    });
    
    ['addClasses', 'editClasses', 'addSeries', 'editSeries'].forEach(id => { 
        const element = document.getElementById(id); 
        if (element) {
            new TomSelect(element, { 
                create: false,
                plugins: ['remove_button'],
                sortField: 'text'
            });
        }
    });

    let rowCounter = document.querySelectorAll('.teacher-row').length;

    // Fonction de prévisualisation d'image
    window.previewImage = function(input, previewId) {
        const preview = document.getElementById(previewId);
        if (!preview) return;
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" alt="Photo" class="w-full h-full object-cover">`;
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.innerHTML = `<span class="material-symbols-outlined text-gray-400 text-4xl">person</span>`;
        }
    };

    window.handleEmploiTempsPlus = function(enseignantId, btnEl) {
        if (btnEl.disabled) return;
        btnEl.disabled = true;

        let existingPopup = document.getElementById('action-popup-' + enseignantId);
        if (existingPopup) {
            existingPopup.remove();
            btnEl.disabled = false;
            return;
        }

        document.querySelectorAll('.action-popup').forEach(el => el.remove());

        fetch(`{{ url('/client/emploi-temps/exists') }}/${enseignantId}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            const exists = !!data.exists;
            let itemsHtml = '';
            
            if (exists) {
                itemsHtml = `
                    <p class="px-4 py-3 text-sm text-on-surface-variant border-b border-gray-100">✅ Emploi du temps existant</p>
                    <button disabled class="popup-item opacity-50 cursor-not-allowed bg-gray-50">
                        <span class="material-symbols-outlined text-green-500">add_circle</span>
                        Créer un emploi du temps
                    </button>
                    <button onclick="window.location.href='{{ url('/client/emploi-temps/edit') }}/${enseignantId}'" class="popup-item">
                        <span class="material-symbols-outlined text-pink-500">edit</span>
                        Modifier l'emploi du temps
                    </button>
                    <button onclick="window.location.href='{{ url('/client/emploi-temps/show') }}/${enseignantId}'" class="popup-item">
                        <span class="material-symbols-outlined text-blue-500">visibility</span>
                        Voir l'emploi du temps
                    </button>
                `;
            } else {
                itemsHtml = `
                    <button onclick="window.location.href='{{ url('/client/emploi-temps/create') }}/${enseignantId}'" class="popup-item">
                        <span class="material-symbols-outlined text-green-500">add_circle</span>
                        Créer un emploi du temps
                    </button>
                    <button disabled class="popup-item opacity-50 cursor-not-allowed bg-gray-50">
                        <span class="material-symbols-outlined text-pink-500">edit</span>
                        Modifier l'emploi du temps
                    </button>
                `;
            }

            const popup = document.createElement('div');
            popup.id = 'action-popup-' + enseignantId;
            popup.className = 'action-popup';
            popup.innerHTML = `
                <div class="flex flex-col">
                    ${itemsHtml}
                </div>
                <div class="action-popup-arrow"></div>
            `;

            document.body.appendChild(popup);

            popup.style.display = 'block';

            const rect = btnEl.getBoundingClientRect();
            const popupWidth = 240;
            const left = rect.left + rect.width / 2 - popupWidth / 2;
            const popupHeight = popup.offsetHeight || 120;
            const top = rect.top - popupHeight - 10;

            popup.style.left = Math.max(10, left) + 'px';
            popup.style.top = Math.max(10, top) + 'px';

            setTimeout(() => {
                document.addEventListener('click', function closePopup(e) {
                    if (!popup.contains(e.target) && e.target !== btnEl && !btnEl.contains(e.target)) {
                        popup.remove();
                        document.removeEventListener('click', closePopup);
                        btnEl.disabled = false;
                    }
                }, { once: true });
            }, 100);
        })
        .catch(() => {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Impossible de vérifier l\'emploi du temps.',
                confirmButtonText: 'OK',
                timer: 3000
            });
        })
        .finally(() => {
            setTimeout(() => {
                if (btnEl.disabled && !document.getElementById('action-popup-' + enseignantId)) {
                    btnEl.disabled = false;
                }
            }, 500);
        });
    };

    document.getElementById('addTeacherBtn').addEventListener('click', function() {
        const lastName = document.getElementById('addLastName').value.trim();
        const firstName = document.getElementById('addFirstName').value.trim();
        const email = document.getElementById('addEmail').value.trim();
        const phone = document.getElementById('addPhone').value.trim();
        const subjects = Array.from(document.getElementById('addSubjects').selectedOptions).map(option => option.value);
        const matricule = document.getElementById('addMatricule').value.trim();
        const teachingYears = document.getElementById('addTeachingYears').value;
        const sexe = document.querySelector('input[name="addSexe"]:checked')?.value;

        if (!lastName || !firstName || !email || !phone || !matricule || !teachingYears || !sexe || !subjects.length) {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Veuillez remplir tous les champs obligatoires',
                showConfirmButton: false,
                timer: 2000,
                position: 'center'
            });
            return;
        }

        const formData = new FormData();
        formData.append('nom', lastName);
        formData.append('prenoms', firstName);
        formData.append('email', email);
        formData.append('telephone', phone);
        formData.append('matricule', matricule); 
        formData.append('nombre_annees_enseignement', teachingYears); 
        formData.append('sexe', sexe);
        subjects.forEach(subject => formData.append('matiere_ids[]', subject));
        Array.from(document.getElementById('addClasses').selectedOptions).forEach(option => formData.append('classe_ids[]', option.value));
        Array.from(document.getElementById('addSeries').selectedOptions).forEach(option => formData.append('serie_ids[]', option.value));
        if (document.getElementById('addPhoto').files[0]) formData.append('photo', document.getElementById('addPhoto').files[0]);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}');

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-base">progress_activity</span> Enregistrement...';

        fetch('{{ route("client.enseignant.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                addTeacherRow(data.teacher);
                updateTotalTeachers(1);
                Swal.fire({
                    icon: 'success',
                    title: 'Succès !',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 2000,
                    borderRadius: '12px',
                    position: 'center'
                });
                closeModal('add-teacher-modal');
                document.getElementById('addTeacherForm').reset();
                // Réinitialiser la prévisualisation
                document.getElementById('addPhotoPreview').innerHTML = `<span class="material-symbols-outlined text-gray-400 text-4xl">person</span>`;
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: data.message || 'Une erreur est survenue',
                    showConfirmButton: false,
                    timer: 2000,
                    borderRadius: '12px',
                    position: 'center'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Une erreur est survenue lors de l\'ajout',
                showConfirmButton: false,
                timer: 2000,
                borderRadius: '12px',
                position: 'center'
            });
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<span class="material-symbols-outlined text-base">save</span> Enregistrer';
        });
    });

    document.getElementById('editTeacherBtn').addEventListener('click', function() {
        const id = document.getElementById('editTeacherId').value;
        const lastName = document.getElementById('editLastName').value.trim();
        const firstName = document.getElementById('editFirstName').value.trim();
        const email = document.getElementById('editEmail').value.trim();
        const phone = document.getElementById('editPhone').value.trim();
        const subjects = Array.from(document.getElementById('editSubjects').selectedOptions).map(option => option.value);
        const matricule = document.getElementById('editMatricule').value.trim();
        const teachingYears = document.getElementById('editTeachingYears').value;
        const sexe = document.querySelector('input[name="editSexe"]:checked')?.value;

        if (!lastName || !firstName || !email || !phone || !matricule || !teachingYears || !sexe || !subjects.length) {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Veuillez remplir tous les champs obligatoires',
                showConfirmButton: false,
                timer: 2000,
                borderRadius: '12px',
                position: 'center'
            });
            return;
        }

        const formData = new FormData();
        formData.append('nom', lastName);
        formData.append('prenoms', firstName);
        formData.append('email', email);
        formData.append('telephone', phone);
        formData.append('matricule', matricule); 
        formData.append('nombre_annees_enseignement', teachingYears); 
        formData.append('sexe', sexe);
        subjects.forEach(subject => formData.append('matiere_ids[]', subject));
        Array.from(document.getElementById('editClasses').selectedOptions).forEach(option => formData.append('classe_ids[]', option.value));
        Array.from(document.getElementById('editSeries').selectedOptions).forEach(option => formData.append('serie_ids[]', option.value));
        if (document.getElementById('editPhoto').files[0]) formData.append('photo', document.getElementById('editPhoto').files[0]);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}');
        formData.append('_method', 'PUT');

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-base">progress_activity</span> Modification...';

        fetch(`{{ url("/client/enseignant") }}/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateTeacherRow(data.teacher);
                Swal.fire({
                    icon: 'success',
                    title: 'Succès !',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 2000,
                    borderRadius: '12px',
                    position: 'center'
                });
                closeModal('edit-teacher-modal');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: data.message || 'Une erreur est survenue',
                    showConfirmButton: false,
                    timer: 2000,
                    borderRadius: '12px',
                    position: 'center'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Une erreur est survenue lors de la modification',
                showConfirmButton: false,
                timer: 2000,
                borderRadius: '12px',
                position: 'center'
            });
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<span class="material-symbols-outlined text-base">save</span> Mettre à jour';
        });
    });

    document.querySelectorAll('.delete-teacher-btn').forEach((button) => {
        button.addEventListener('click', function() {
            handleDeleteClick(this);
        });
    });

    function addTeacherRow(teacher) {
        const tbody = document.getElementById('teachersTableBody');
        const emptyRow = document.getElementById('emptyRow');
        if (emptyRow) emptyRow.remove();

        const rowCount = tbody.querySelectorAll('.teacher-row').length + 1;
        
        const row = document.createElement('tr');
        row.className = 'hover:bg-surface-subtle transition-colors teacher-row text-sm';
        row.id = `teacher-row-${teacher.id}`;
        row.setAttribute('data-name', `${teacher.firstname} ${teacher.lastname}`.toLowerCase());
        row.setAttribute('data-email', teacher.email.toLowerCase());
        row.setAttribute('data-subject', teacher.subject.toLowerCase());
        row.setAttribute('data-position', teacher.subject.toLowerCase());

        row.innerHTML = `
            <td class="px-4 py-3 text-on-surface-variant text-sm">${rowCount}</td>
            <td class="px-4 py-3 font-semibold text-on-surface text-sm">${teacher.firstname} ${teacher.lastname}</td>
            <td class="px-4 py-3 text-on-surface-variant text-sm">${teacher.email}</td>
            <td class="px-4 py-3 text-on-surface-variant text-sm">${teacher.phone}</td>
            <td class="px-4 py-3">
                <span class="px-2.5 py-1 bg-primary/10 text-primary text-xs font-bold rounded-full">${teacher.subject}</span>
            </td>
            <td class="px-4 py-3">
                <span class="px-2.5 py-1 bg-success-green/10 text-success-green text-xs font-bold rounded-full">${teacher.status}</span>
            </td>
            <td class="px-4 py-3 text-right">
                <div class="flex items-center justify-end space-x-1.5 relative">
                    <button type="button" class="btn-plus" title="Gérer l'emploi du temps" onclick="handleEmploiTempsPlus(${teacher.id}, this)">
                        <span class="material-symbols-outlined">add</span>
                    </button>
                    <button class="w-8 h-8 flex items-center justify-center text-warning-amber hover:bg-warning-amber/10 rounded-full transition-all" onclick='openEditModal(${JSON.stringify(teacher).replace(/'/g, "&#39;")})' title="Modifier">
                        <span class="material-symbols-outlined text-base">edit</span>
                    </button>
                    <button class="w-8 h-8 flex items-center justify-center text-alert-red hover:bg-alert-red/10 rounded-full transition-all delete-teacher-btn" data-id="${teacher.id}" data-name="${teacher.firstname} ${teacher.lastname}" type="button" title="Supprimer">
                        <span class="material-symbols-outlined text-base">delete</span>
                    </button>
                </div>
            </td>
        `;

        tbody.appendChild(row);

        row.querySelector('.delete-teacher-btn').addEventListener('click', function() {
            handleDeleteClick(this);
        });

        renumberRows();
    }

    function updateTeacherRow(teacher) {
        const row = document.getElementById(`teacher-row-${teacher.id}`);
        if (row) {
            row.setAttribute('data-name', `${teacher.firstname} ${teacher.lastname}`.toLowerCase());
            row.setAttribute('data-email', teacher.email.toLowerCase());
            row.setAttribute('data-subject', teacher.subject.toLowerCase());
            row.setAttribute('data-position', teacher.subject.toLowerCase());

            const cells = row.querySelectorAll('td');
            if (cells.length >= 6) {
                cells[0].textContent = row.rowIndex;
                cells[1].textContent = `${teacher.firstname} ${teacher.lastname}`;
                cells[2].textContent = teacher.email;
                cells[3].textContent = teacher.phone;
                cells[4].innerHTML = `<span class="px-2.5 py-1 bg-primary/10 text-primary text-xs font-bold rounded-full">${teacher.subject}</span>`;
                cells[5].innerHTML = `<span class="px-2.5 py-1 bg-success-green/10 text-success-green text-xs font-bold rounded-full">${teacher.status}</span>`;
            }
        }
    }

    function updateTotalTeachers(change) {
        const totalSpan = document.getElementById('totalTeachers');
        if (totalSpan) {
            let current = parseInt(totalSpan.textContent) || 0;
            totalSpan.textContent = current + change;
        }
    }

    function renumberRows() {
        const rows = document.querySelectorAll('.teacher-row');
        rows.forEach((row, index) => {
            const cells = row.querySelectorAll('td');
            if (cells.length > 0) {
                cells[0].textContent = index + 1;
            }
        });
    }

    function checkEmptyTable() {
        const tbody = document.getElementById('teachersTableBody');
        const rows = tbody.querySelectorAll('.teacher-row');
        if (rows.length === 0) {
            const oldEmpty = document.getElementById('emptyRow');
            if (oldEmpty) oldEmpty.remove();
            
            const emptyRow = document.createElement('tr');
            emptyRow.id = 'emptyRow';
            emptyRow.innerHTML = `
                <td colspan="7" class="px-4 py-12 text-center text-on-surface-variant text-sm">
                    <div class="flex flex-col items-center gap-4">
                        <span class="material-symbols-outlined text-5xl text-outline">school</span>
                        <p>Aucun enseignant trouvé</p>
                    </div>
                </td>
            `;
            tbody.appendChild(emptyRow);
        }
    }

    function handleDeleteClick(button) {
        const teacherId = button.dataset.id;
        const teacherName = button.dataset.name;

        Swal.fire({
            title: 'Confirmer la suppression',
            text: `Êtes-vous sûr de vouloir supprimer l'enseignant "${teacherName}" ?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler',
            reverseButtons: true,
            borderRadius: '12px'
        }).then((result) => {
            if (result.isConfirmed) {
                const btn = button;
                btn.disabled = true;

                fetch(`{{ url("/client/enseignant") }}/${teacherId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const row = document.getElementById(`teacher-row-${teacherId}`);
                        if (row) {
                            row.remove();
                        }
                        updateTotalTeachers(-1);
                        checkEmptyTable();
                        renumberRows();
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Supprimé !',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 2000,
                            borderRadius: '12px',
                            position: 'center'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: data.message || 'Une erreur est survenue',
                            showConfirmButton: false,
                            timer: 2000,
                            borderRadius: '12px',
                            position: 'center'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Une erreur est survenue lors de la suppression',
                        showConfirmButton: false,
                        timer: 2000,
                        borderRadius: '12px',
                        position: 'center'
                    });
                })
                .finally(() => {
                    btn.disabled = false;
                });
            }
        });
    }

    window.openModal = function(modalId) {
        const modal = document.getElementById(modalId);
        const contentId = modalId === 'add-teacher-modal' ? 'add-modal-content' : 'edit-modal-content';
        const content = document.getElementById(contentId);

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';

        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    };

    window.closeModal = function(modalId) {
        const modal = document.getElementById(modalId);
        const contentId = modalId === 'add-teacher-modal' ? 'add-modal-content' : 'edit-modal-content';
        const content = document.getElementById(contentId);

        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }, 300);
    };

    window.openEditModal = function(teacher) {
        document.getElementById('editTeacherId').value = teacher.id;
        document.getElementById('editLastName').value = teacher.lastname || '';
        document.getElementById('editFirstName').value = teacher.firstname || '';
        document.getElementById('editEmail').value = teacher.email || '';
        document.getElementById('editPhone').value = teacher.phone || '';
        document.getElementById('editMatricule').value = teacher.matricule || '';
        document.getElementById('editTeachingYears').value = teacher.teaching_years ?? '';
        
        document.querySelectorAll('input[name="editSexe"]').forEach(input => {
            input.checked = input.value === teacher.sexe;
        });
        
        // Si une photo existe, l'afficher en prévisualisation
        if (teacher.photo) {
            const preview = document.getElementById('editPhotoPreview');
            if (preview) {
                preview.innerHTML = `<img src="${teacher.photo}" alt="Photo" class="w-full h-full object-cover">`;
            }
        } else {
            document.getElementById('editPhotoPreview').innerHTML = `<span class="material-symbols-outlined text-gray-400 text-4xl">person</span>`;
        }

        const subjectSelect = document.getElementById('editSubjects');
        if (subjectSelect) { 
            const values = (teacher.subject_ids || []).map(String); 
            if (subjectSelect.tomselect) {
                subjectSelect.tomselect.setValue(values, true);
            } else {
                Array.from(subjectSelect.options).forEach(option => option.selected = values.includes(option.value));
            }
        }
        
        const classSelect = document.getElementById('editClasses');
        if (classSelect) { 
            const values = (teacher.class_ids || []).map(String); 
            if (classSelect.tomselect) {
                classSelect.tomselect.setValue(values, true);
            } else {
                Array.from(classSelect.options).forEach(option => option.selected = values.includes(option.value));
            }
        }
        
        const serieSelect = document.getElementById('editSeries');
        if (serieSelect) { 
            const values = (teacher.serie_ids || []).map(String); 
            if (serieSelect.tomselect) {
                serieSelect.tomselect.setValue(values, true);
            } else {
                Array.from(serieSelect.options).forEach(option => option.selected = values.includes(option.value));
            }
        }

        openModal('edit-teacher-modal');
    };

    const searchInput = document.getElementById('searchTeacher');
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('.teacher-row');
            let visibleCount = 0;

            rows.forEach(row => {
                const name = row.getAttribute('data-name') || '';
                const email = row.getAttribute('data-email') || '';
                const subject = row.getAttribute('data-subject') || '';
                const position = row.getAttribute('data-position') || '';

                if (name.includes(searchTerm) || email.includes(searchTerm) || subject.includes(searchTerm) || position.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            const paginationSpan = document.getElementById('paginationInfo');
            if (paginationSpan && !paginationSpan.innerHTML.includes('Précédent')) {
                paginationSpan.textContent = visibleCount === 1
                    ? `Affichage de 1 sur ${visibleCount} enseignant`
                    : `Affichage de ${visibleCount} sur ${visibleCount} enseignants`;
            }
        });
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const modals = ['add-teacher-modal', 'edit-teacher-modal'];
            modals.forEach(id => {
                const modal = document.getElementById(id);
                if (modal && modal.classList.contains('flex')) {
                    closeModal(id);
                }
            });
        }
    });
});
</script>
@endpush