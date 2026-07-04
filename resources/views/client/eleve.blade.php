@extends('client.layouts.app')
@section('title', 'EduManager - Eleves')
@section('content')
<!-- Page Header -->
<div class="flex justify-between items-start mb-10">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-primary mb-1">Gestion des Élèves</h2>
        <p class="text-body-md text-text-muted">Gérez l'ensemble des élèves inscrits dans votre établissement</p>
    </div>
    <div class="flex gap-4">
        <button class="flex items-center gap-2 px-6 py-2.5 bg-primary text-white rounded-lg font-label-md text-label-md hover:opacity-90 active:scale-95 transition-all card-shadow" onclick="openModal('modal-standard')" type="button">
            <span class="material-symbols-outlined text-[18px]">person_add</span>
            Nouvel élève
        </button>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
    <div class="glass-card p-6 rounded-xl flex items-center gap-5 shadow-[4px_4px_12px_rgba(55,48,163,0.04)]">
        <div class="w-14 h-14 rounded-full bg-primary-fixed flex items-center justify-center text-primary">
            <span class="material-symbols-outlined text-3xl">school</span>
        </div>
        <div>
            <h3 class="text-label-md text-text-muted">Élèves inscrits</h3>
            <p class="text-headline-xl font-headline-xl text-on-surface">{{ $totalStudents ?? 0 }}</p>
        </div>
    </div>
    <div class="glass-card p-6 rounded-xl flex items-center gap-5 shadow-[4px_4px_12px_rgba(55,48,163,0.04)]">
        <div class="w-14 h-14 rounded-full bg-secondary-container flex items-center justify-center text-secondary">
            <span class="material-symbols-outlined text-3xl">meeting_room</span>
        </div>
        <div>
            <h3 class="text-label-md text-text-muted">Classes actives</h3>
            <p class="text-headline-xl font-headline-xl text-on-surface">{{ $activeClasses ?? 0 }}</p>
        </div>
    </div>
</div>

<!-- Data Table Section -->
<div class="glass-card rounded-xl overflow-hidden shadow-[0_4px_12px_rgba(55,48,163,0.04)]">
    <div class="px-6 py-4 border-b border-surface-subtle bg-surface-container-low flex justify-between items-center">
        <h4 class="font-headline-md text-headline-md text-primary">Liste des élèves</h4>
    </div>

    <form method="GET" action="{{ route('client.eleve') }}" class="px-6 py-4 border-b border-surface-subtle grid grid-cols-1 md:grid-cols-5 gap-3 bg-white">
        <input type="text" name="search" value="{{ request('search') }}" class="rounded-lg border-outline-variant text-sm" placeholder="Rechercher nom ou matricule">
        <select name="niveau_id" class="rounded-lg border-outline-variant text-sm">
            <option value="">Tous les niveaux</option>
            @foreach($levels ?? [] as $level)
                <option value="{{ $level['id'] }}" @selected((string) request('niveau_id') === (string) $level['id'])>{{ $level['name'] }}</option>
            @endforeach
        </select>
        <select name="id_serie" class="rounded-lg border-outline-variant text-sm">
            <option value="">Toutes les séries</option>
            @foreach($series ?? [] as $serie)
                <option value="{{ $serie->id }}" @selected((string) request('id_serie') === (string) $serie->id)>{{ $serie->nom_serie }}</option>
            @endforeach
        </select>
        <select name="classe_id" class="rounded-lg border-outline-variant text-sm">
            <option value="">Toutes les classes</option>
            @foreach($classes ?? [] as $classe)
                <option value="{{ $classe['id'] }}" @selected((string) request('classe_id') === (string) $classe['id'])>{{ $classe['name'] }}</option>
            @endforeach
        </select>
        <button class="bg-primary text-white rounded-lg px-4 py-2 font-label-md text-sm" type="submit">Filtrer</button>
    </form>

    @if(($students ?? collect())->isEmpty())
    <div class="min-h-[200px] flex flex-col items-center justify-center text-center p-9">
        <div class="w-24 h-24 bg-surface-container rounded-full flex items-center justify-center mb-5">
            <span class="material-symbols-outlined text-primary text-5xl">school</span>
        </div>
        <h3 class="font-headline-md text-headline-md text-on-surface mb-2">Aucun élève enregistré pour le moment</h3>
    </div>
    @else
    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left text-[14px] border-separate border-spacing-y-2">
            <thead class="bg-surface-container-low text-[13px] uppercase tracking-wider text-text-muted">
                <tr>
                    <th class="px-3 py-4 font-semibold">#</th>
                    <th class="px-4 py-4 font-semibold min-w-[200px]">Nom &amp; Prénoms</th>
                    <th class="px-3 py-4 font-semibold">Matricule</th>
                    <th class="px-3 py-4 font-semibold">Sexe</th>
                    <th class="px-3 py-4 font-semibold">Classe</th>
                    <th class="px-3 py-4 font-semibold">Niveau</th>
                    <th class="px-3 py-4 font-semibold min-w-[100px]">Série</th>
                    <th class="px-3 py-4 font-semibold min-w-[110px]">Date de naissance</th>
                    <th class="px-3 py-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-subtle">
                @foreach($students as $student)
                <tr class="hover:bg-surface-container-low transition-colors rounded-lg shadow-sm bg-white">
                    <td class="px-3 py-4 text-[14px] align-middle">{{ $loop->iteration }}</td>
                    
                    <td class="px-4 py-4 min-w-[200px] align-middle">
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-[14px] whitespace-nowrap">{{ $student['lastname'] }} {{ $student['firstname'] }}</span>
                        </div>
                    </td>
                    <td class="px-3 py-4 text-[14px] text-on-surface-variant align-middle">{{ $student['matricule'] ?? 'N/A' }}</td>
                    
                    <!-- Colonne Sexe -->
                    <td class="px-3 py-4 align-middle">
                        @php
                            $sexe = strtolower(trim($student['sexe'] ?? ''));
                            $badgeClass = '';
                            $displaySexe = 'N/A';
                            
                            if (in_array($sexe, ['m', 'masculin', 'male', 'homme', 'h'])) {
                                $badgeClass = 'bg-blue-100 text-blue-700';
                                $displaySexe = 'Masculin';
                            } elseif (in_array($sexe, ['f', 'féminin', 'feminin', 'female', 'femme', 'f'])) {
                                $badgeClass = 'bg-pink-100 text-pink-700';
                                $displaySexe = 'Féminin';
                            } else {
                                $badgeClass = 'bg-gray-100 text-gray-700';
                                $displaySexe = 'N/A';
                            }
                        @endphp
                        <span class="px-3 py-1.5 rounded-full text-[12px] font-medium {{ $badgeClass }}">
                            {{ $displaySexe }}
                        </span>
                    </td>
                    
                    <td class="px-3 py-4 text-[14px] text-on-surface-variant align-middle">{{ $student['classe'] ?? $student['class'] ?? 'N/A' }}</td>
                    <td class="px-3 py-4 align-middle">
                        <span class="px-3 py-1.5 rounded-full text-[12px] font-medium bg-secondary-container/20 text-on-secondary-container">
                            {{ $student['level'] }}
                        </span>
                    </td>
                    <td class="px-3 py-4 text-on-surface-variant align-middle min-w-[100px]">{{ $student['serie'] ?? '—' }}</td>
                    <td class="px-3 py-4 text-[14px] text-on-surface-variant align-middle min-w-[110px]">{{ $student['birthdate'] }}</td>
                    <td class="px-3 py-4 text-right align-middle">
                        <div class="flex justify-end items-center gap-1">
                            <button class="p-1.5 text-primary hover:bg-primary-fixed rounded-lg transition-colors" onclick="viewStudent({{ json_encode($student) }})" title="Voir" type="button">
                                <span class="material-symbols-outlined text-[20px]">visibility</span>
                            </button>
                            <button class="p-1.5 text-warning-amber hover:bg-warning-amber/10 rounded-lg transition-colors" onclick="editStudent({{ json_encode($student) }})" title="Modifier" type="button">
                                <span class="material-symbols-outlined text-[20px]">edit</span>
                            </button>
                            <form action="{{ route('client.eleve.destroy', $student['id']) }}" method="POST" class="inline delete-student-form">
                                @csrf
                                @method('DELETE')
                                <button class="p-1.5 text-alert-red hover:bg-error-container/20 rounded-lg transition-colors delete-student-btn" data-name="{{ $student['firstname'] }} {{ $student['lastname'] }}" title="Supprimer" type="button">
                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-surface-subtle bg-surface-container-low/30 flex items-center justify-between">
        <span class="text-[13px] text-text-muted">
            Affichage de {{ $students->firstItem() ?? 0 }} à {{ $students->lastItem() ?? 0 }} sur {{ $students->total() ?? 0 }} élèves
        </span>
        <div class="flex gap-2 text-sm">
            {{ $students->links() ?? '' }}
        </div>
    </div>
    @endif
</div>

<!-- MODALS (inchangées) -->
<!-- Modal: Standard Addition -->
<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="modal-standard">
    <div class="absolute inset-0 modal-overlay backdrop-blur-md bg-black/30" onclick="closeModal('modal-standard')"></div>
    <div class="relative glass-card w-full max-w-4xl max-h-[90vh] overflow-y-auto rounded-xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0" id="modal-standard-content">
        <div class="p-6 border-b border-outline-variant flex justify-between items-center bg-primary text-white sticky top-0">
            <h3 class="font-headline-md text-headline-md">AJOUT D'UN ÉLÈVE</h3>
            <button class="hover:bg-white/20 rounded-full p-1 transition-colors" onclick="closeModal('modal-standard')" type="button">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <form class="p-8" id="form-standard" action="{{ route('client.eleve.store') }}" method="POST" enctype="multipart/form-data">
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
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Section Élève -->
                <div class="space-y-6">
                    <h4 class="font-label-md text-primary flex items-center gap-2 border-b border-primary-fixed pb-2">
                        <span class="material-symbols-outlined text-[20px]">person</span>
                        INFORMATIONS DE L'ÉLÈVE
                    </h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Nom <span class="text-alert-red">*</span></label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-primary focus:border-primary" id="stdLastname" required type="text">
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Prénom <span class="text-alert-red">*</span></label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-primary focus:border-primary" id="stdFirstname" required type="text">
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Matricule <span class="text-alert-red">*</span></label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-primary focus:border-primary" id="stdMatricule" required type="text">
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Sexe <span class="text-alert-red">*</span></label>
                            <div class="flex gap-6 mt-1">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="sexe" value="Masculin" class="w-4 h-4 text-primary focus:ring-primary focus:ring-2 border-outline-variant" checked>
                                    <span class="text-body-sm text-on-surface">Masculin</span>
                                    <span class="material-symbols-outlined text-[20px] text-primary/60">male</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="sexe" value="Féminin" class="w-4 h-4 text-primary focus:ring-primary focus:ring-2 border-outline-variant">
                                    <span class="text-body-sm text-on-surface">Féminin</span>
                                    <span class="material-symbols-outlined text-[20px] text-primary/60">female</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Date de naissance <span class="text-alert-red">*</span></label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-primary focus:border-primary" id="birthdate-std" required type="date">
                            <p class="hidden text-[11px] text-alert-red mt-1 flex items-center gap-1" id="age-warning-std">
                                <span class="material-symbols-outlined text-[14px]">warning</span>
                                L'âge minimum requis est de 5 ans.
                            </p>
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Lieu de naissance</label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-primary focus:border-primary" id="stdBirthPlace" type="text">
                        </div>

                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Nationalité</label>
                            <select class="w-full rounded-lg border-outline-variant focus:ring-primary focus:border-primary" id="nationalite" name="nationalite">
                                <option value="">Sélectionner une nationalité</option>
                                @foreach(config('nationalities') as $nationalite)
                                    <option value="{{ $nationalite }}">{{ $nationalite }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-label-sm text-on-surface mb-1.5">Niveau</label>
                                <select class="w-full rounded-lg border-outline-variant focus:ring-primary focus:border-primary" id="stdLevel">
                                    <option value="">Sélectionner un niveau</option>
                                    @foreach($levels ?? [] as $level)
                                    <option value="{{ $level['id'] }}">{{ $level['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-label-sm text-on-surface mb-1.5">Classe <span class="text-alert-red">*</span></label>
                                <select class="w-full rounded-lg border-outline-variant focus:ring-primary focus:border-primary" id="stdClasse" required disabled>
                                    <option value="">Sélectionner d'abord un niveau</option>
                                </select>
                            </div>
                        </div>
                        <div id="stdSerieWrapper">
                            <label class="block text-label-sm text-on-surface mb-1.5">Série</label>
                            <select class="w-full rounded-lg border-outline-variant focus:ring-primary focus:border-primary" id="stdSerie" name="id_serie"><option value="">Aucune série</option></select>
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Photo de l'élève</label>
                            <div class="flex items-center gap-4">
                                <div class="w-20 h-20 rounded-full bg-surface-container border-2 border-dashed border-outline-variant flex items-center justify-center overflow-hidden" id="photo-preview-std">
                                    <span class="material-symbols-outlined text-3xl text-text-muted">photo_camera</span>
                                </div>
                                <div class="flex-1">
                                    <input class="w-full text-body-sm text-text-muted file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-label-md file:bg-primary-fixed file:text-primary hover:file:bg-primary-fixed/80" id="stdPhoto" name="photo" type="file" accept="image/*" onchange="previewPhoto(this, 'photo-preview-std')">
                                    <p class="text-[11px] text-text-muted mt-1">Formats acceptés: JPG, PNG, GIF (max 2MB)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Section Parent -->
                <div class="space-y-6">
                    <h4 class="font-label-md text-primary flex items-center gap-2 border-b border-primary-fixed pb-2">
                        <span class="material-symbols-outlined text-[20px]">family_restroom</span>
                        INFORMATIONS DU PARENT
                    </h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Nom du Parent <span class="text-alert-red">*</span></label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-primary focus:border-primary" id="parentLastname" required type="text">
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Prénom du Parent <span class="text-alert-red">*</span></label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-primary focus:border-primary" id="parentFirstname" required type="text">
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Type de Parent</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 text-label-sm cursor-pointer">
                                    <input class="text-primary focus:ring-primary" name="parent_type" type="radio" value="pere"> Père
                                </label>
                                <label class="flex items-center gap-2 text-label-sm cursor-pointer">
                                    <input class="text-primary focus:ring-primary" name="parent_type" type="radio" value="mere"> Mère
                                </label>
                                <label class="flex items-center gap-2 text-label-sm cursor-pointer">
                                    <input checked class="text-primary focus:ring-primary" name="parent_type" type="radio" value="tuteur"> Tuteur
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Téléphone <span class="text-alert-red">*</span></label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-primary focus:border-primary" id="parentPhone" required type="tel">
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Email</label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-primary focus:border-primary" id="parentEmail" type="email">
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-10 flex justify-end gap-4 pt-6 border-t border-outline-variant">
                <button class="px-6 py-2 text-on-surface-variant hover:bg-surface-subtle rounded-lg font-label-md transition-all" onclick="closeModal('modal-standard')" type="button">
                    Annuler
                </button>
                <button class="px-8 py-2 bg-primary text-white rounded-lg font-label-md hover:bg-primary/90 transition-all active:scale-95 shadow-md" type="submit">
                    Enregistrer l'élève
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: View (Details) - PHOTO RONDE ET SANS BADGE SEXE -->
<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="modal-view">
    <div class="absolute inset-0 modal-overlay backdrop-blur-md bg-black/30" onclick="closeModal('modal-view')"></div>
    <div class="relative glass-card w-full max-w-4xl h-[90vh] rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0 flex flex-col" id="modal-view-content">
        <!-- Icônes en arrière-plan -->
        <div class="absolute -right-4 -top-4 opacity-5 pointer-events-none">
            <span class="material-symbols-outlined text-[200px]" style="font-variation-settings: 'FILL' 1;">school</span>
        </div>
        <div class="absolute -left-4 -bottom-4 opacity-5 pointer-events-none rotate-12">
            <span class="material-symbols-outlined text-[180px]" style="font-variation-settings: 'FILL' 1;">badge</span>
        </div>

        <!-- Bannière de statut - Fixe -->
        <div class="px-5 py-2.5 bg-gradient-to-r from-primary/5 to-primary-container/5 border-b border-outline-variant/30 flex justify-between items-center flex-shrink-0 bg-white/95 backdrop-blur-sm z-10 rounded-t-2xl">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[16px]">info</span>
                <span class="text-label-sm text-[11px] text-text-muted">Informations générales</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="absolute inline-flex h-full w-full rounded-full bg-success-green opacity-75 animate-ping"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-success-green"></span>
                </span>
                <span class="text-label-sm text-[11px] font-semibold text-success-green">Actif</span>
            </div>
        </div>

        <!-- Contenu principal - Scrollable -->
        <div class="flex-1 overflow-y-auto p-5 md:p-6" id="modal-view-scroll">
            <!-- En-tête avec avatar et nom - PHOTO RONDE -->
            <div class="flex flex-col md:flex-row md:items-start gap-6 pb-5 border-b border-outline-variant/30">
                <div class="relative">
                    <!-- Photo rendue RONDE avec rounded-full -->
                    <div class="w-28 h-28 rounded-full bg-gradient-to-br from-primary/10 to-primary-container/10 flex items-center justify-center shadow-md relative z-10 overflow-hidden" id="viewStudentPhotoContainer">
                        <span class="material-symbols-outlined text-5xl text-primary/40">account_circle</span>
                    </div>
                    <div class="absolute -right-2 -bottom-2 opacity-20">
                        <span class="material-symbols-outlined text-2xl">person</span>
                    </div>
                </div>
                <div class="flex-1 relative">
                    <!-- Nom en plus grand -->
                    <h3 class="font-headline-xl text-[28px] text-on-surface mb-2 flex items-center gap-2" id="viewStudentFullName">
                        -
                        <span class="material-symbols-outlined text-primary text-2xl opacity-60" style="font-variation-settings: 'FILL' 1;">verified</span>
                    </h3>
                    <!-- Badges sans celui du sexe -->
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-primary-fixed/30 text-primary rounded-full text-[12px]">
                            <span class="material-symbols-outlined text-[14px]">badge</span>
                            Matricule: <span id="viewStudentMatricule">-</span>
                        </span>
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-surface-container-high text-on-surface-variant rounded-full text-[12px]">
                            <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                            Né(e) le <span id="viewStudentBirthdate">-</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Grille des informations détaillées - TEXTES AGRANDIS -->
            <div class="pt-5">
                <h4 class="font-headline-md text-[20px] text-on-surface mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[22px]">contact_mail</span>
                    Informations scolaires
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Classe -->
                    <div class="flex items-start gap-4 p-4 rounded-xl bg-surface-container-low/30 hover:bg-surface-container-low transition-all duration-200">
                        <div class="p-2 bg-primary-fixed/20 rounded-lg">
                            <span class="material-symbols-outlined text-primary text-[22px]">class</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-label-sm text-[12px] text-text-muted uppercase tracking-wider">Classe</p>
                            <p class="font-body-md text-[16px] text-on-surface" id="viewStudentClasse">-</p>
                        </div>
                    </div>

                    <!-- Niveau -->
                    <div class="flex items-start gap-4 p-4 rounded-xl bg-surface-container-low/30 hover:bg-surface-container-low transition-all duration-200">
                        <div class="p-2 bg-primary-fixed/20 rounded-lg">
                            <span class="material-symbols-outlined text-primary text-[22px]">account_tree</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-label-sm text-[12px] text-text-muted uppercase tracking-wider">Niveau</p>
                            <p class="font-body-md text-[16px] text-on-surface" id="viewStudentNiveau">-</p>
                        </div>
                    </div>

                    <!-- Série -->
                    <div class="flex items-start gap-4 p-4 rounded-xl bg-surface-container-low/30">
                        <div class="p-2 bg-primary-fixed/20 rounded-lg"><span class="material-symbols-outlined text-primary">category</span></div>
                        <div><p class="text-label-sm text-[12px] text-text-muted uppercase">Série</p><p class="font-body-md text-[16px]" id="viewStudentSerie">—</p></div>
                    </div>

                    <!-- Sexe -->
                    <div class="flex items-start gap-4 p-4 rounded-xl bg-surface-container-low/30 hover:bg-surface-container-low transition-all duration-200">
                        <div class="p-2 bg-primary-fixed/20 rounded-lg">
                            <span class="material-symbols-outlined text-primary text-[22px]">wc</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-label-sm text-[12px] text-text-muted uppercase tracking-wider">Sexe</p>
                            <p class="font-body-md text-[16px] text-on-surface" id="viewStudentSexeDetail">-</p>
                        </div>
                    </div>

                    <!-- Lieu de naissance (pleine largeur) -->
                    <div class="md:col-span-2">
                        <div class="flex items-start gap-4 p-4 rounded-xl bg-surface-container-low/30 hover:bg-surface-container-low transition-all duration-200">
                            <div class="p-2 bg-primary-fixed/20 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-[22px]">location_on</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-label-sm text-[12px] text-text-muted uppercase tracking-wider">Lieu de naissance</p>
                                <p class="font-body-md text-[16px] text-on-surface" id="viewStudentBirthplace">-</p>
                            </div>
                        </div>
                    </div>

                    <!-- Nationalité -->
                    <div class="md:col-span-2">
                        <div class="flex items-start gap-4 p-4 rounded-xl bg-surface-container-low/30 hover:bg-surface-container-low transition-all duration-200">
                            <div class="p-2 bg-primary-fixed/20 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-[22px]">flag</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-label-sm text-[12px] text-text-muted uppercase tracking-wider">Nationalité</p>
                                <p class="font-body-md text-[16px] text-on-surface" id="viewStudentNationalite">-</p>
                            </div>
                        </div>
                    </div>

                    <!-- Parents (pleine largeur) -->
                    <div class="md:col-span-2">
                        <div class="flex items-start gap-4 p-4 rounded-xl bg-surface-container-low/30 hover:bg-surface-container-low transition-all duration-200">
                            <div class="p-2 bg-primary-fixed/20 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-[22px]">family_restroom</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-label-sm text-[12px] text-text-muted uppercase tracking-wider">Parents / Tuteurs</p>
                                <div class="mt-1 space-y-1">
                                    <p class="font-body-md text-[16px] text-on-surface">Nom : <span id="viewStudentParentLastname">-</span></p>
                                    <p class="font-body-md text-[16px] text-on-surface">Prénom : <span id="viewStudentParentFirstname">-</span></p>
                                    <p class="font-body-md text-[16px] text-on-surface">Téléphone : <span id="viewStudentParentPhone">-</span></p>
                                    <p class="font-body-md text-[16px] text-on-surface">Email : <span id="viewStudentParentEmail">-</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pied de page - Fixe -->
        <div class="mt-auto pt-4 pb-4 px-5 md:px-6 border-t border-outline-variant/30 flex flex-wrap items-center justify-between gap-3 flex-shrink-0 bg-white/95 rounded-b-2xl">
            <div class="flex items-center gap-4 text-text-muted">
                <span class="text-label-sm text-[12px] flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">event_note</span>
                    Créé: <span id="viewStudentCreatedAt" class="text-on-surface font-medium">-</span>
                </span>
                <span class="text-label-sm text-[12px] flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">update</span>
                    Modifié: <span id="viewStudentUpdatedAt" class="text-on-surface font-medium">-</span>
                </span>
            </div>
            <div class="flex gap-2">
                <button class="px-4 py-2 bg-warning-amber text-white rounded-lg font-label-md text-[14px] hover:bg-warning-amber/90 transition-all flex items-center gap-2" onclick="window.print()" type="button">
                    <span class="material-symbols-outlined text-[18px]">print</span>
                    Imprimer
                </button>
                <button class="px-4 py-2 bg-primary text-white rounded-lg font-label-md text-[14px] hover:bg-primary/90 transition-all flex items-center gap-2" onclick="closeModal('modal-view')" type="button">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Edit -->
<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="modal-edit">
    <div class="absolute inset-0 modal-overlay backdrop-blur-md bg-black/30" onclick="closeModal('modal-edit')"></div>
    <div class="relative glass-card w-full max-w-4xl max-h-[90vh] overflow-y-auto rounded-xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0" id="modal-edit-content">
        <div class="p-6 border-b border-outline-variant flex justify-between items-center bg-warning-amber text-white sticky top-0">
            <h3 class="font-headline-md text-headline-md">MODIFIER L'ÉLÈVE</h3>
            <button class="hover:bg-white/20 rounded-full p-1 transition-colors" onclick="closeModal('modal-edit')" type="button">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <form class="p-8" id="form-edit" action="" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" value="PUT" />
            <input type="hidden" name="editEleveId" id="editEleveId" value="" />

            <input type="hidden" name="type_eleve" value="nouveau" />
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Section Élève -->
                <div class="space-y-6">
                    <h4 class="font-label-md text-warning-amber flex items-center gap-2 border-b border-warning-amber/30 pb-2">
                        <span class="material-symbols-outlined text-[20px]">person</span>
                        INFORMATIONS DE L'ÉLÈVE
                    </h4>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Nom <span class="text-alert-red">*</span></label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-warning-amber focus:border-warning-amber" id="editLastname" required type="text">
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Prénom <span class="text-alert-red">*</span></label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-warning-amber focus:border-warning-amber" id="editFirstname" required type="text">
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Matricule <span class="text-alert-red">*</span></label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-warning-amber focus:border-warning-amber" id="editMatricule" required type="text">
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Sexe <span class="text-alert-red">*</span></label>
                            <div class="flex gap-6 mt-1">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="edit_sexe" value="Masculin" class="w-4 h-4 text-warning-amber focus:ring-warning-amber focus:ring-2 border-outline-variant">
                                    <span class="text-body-sm text-on-surface">Masculin</span>
                                    <span class="material-symbols-outlined text-[20px] text-warning-amber/60">male</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="edit_sexe" value="Féminin" class="w-4 h-4 text-warning-amber focus:ring-warning-amber focus:ring-2 border-outline-variant">
                                    <span class="text-body-sm text-on-surface">Féminin</span>
                                    <span class="material-symbols-outlined text-[20px] text-warning-amber/60">female</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Date de naissance <span class="text-alert-red">*</span></label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-warning-amber focus:border-warning-amber" id="editBirthdate" required type="date">
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Lieu de naissance</label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-warning-amber focus:border-warning-amber" id="editBirthPlace" type="text">
                        </div>

                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Nationalité</label>
                            <select class="w-full rounded-lg border-outline-variant focus:ring-warning-amber focus:border-warning-amber" id="editNationalite" name="nationalite">
                                <option value="">Sélectionner une nationalité</option>
                                @foreach(config('nationalities') as $nationalite)
                                    <option value="{{ $nationalite }}">{{ $nationalite }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-label-sm text-on-surface mb-1.5">Niveau</label>
                                <select class="w-full rounded-lg border-outline-variant focus:ring-warning-amber focus:border-warning-amber" id="editLevel">
                                    <option value="">Sélectionner un niveau</option>
                                    @foreach($levels ?? [] as $level)
                                        <option value="{{ $level['id'] }}">{{ $level['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-label-sm text-on-surface mb-1.5">Classe <span class="text-alert-red">*</span></label>
                                <select class="w-full rounded-lg border-outline-variant focus:ring-warning-amber focus:border-warning-amber" id="editClasse" required disabled>
                                    <option value="">Sélectionner d'abord un niveau</option>
                                </select>
                            </div>
                        </div>
                        <div id="editSerieWrapper">
                            <label class="block text-label-sm text-on-surface mb-1.5">Série</label>
                            <select class="w-full rounded-lg border-outline-variant focus:ring-warning-amber focus:border-warning-amber" id="editSerie" name="id_serie"><option value="">Aucune série</option></select>
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-3 text-center">Photo de l'élève</label>
                            <div class="flex flex-col items-center justify-center gap-3 text-center">
                                <div class="w-24 h-24 mx-auto shrink-0 rounded-full bg-surface-container border-2 border-dashed border-outline-variant flex items-center justify-center overflow-hidden" id="photo-preview-edit">
                                    <span class="material-symbols-outlined text-3xl text-text-muted">photo_camera</span>
                                </div>
                                <div class="w-full max-w-sm mx-auto">
                                    <input class="w-full text-body-sm text-text-muted file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-label-md file:bg-warning-amber/20 file:text-warning-amber hover:file:bg-warning-amber/30" id="editPhoto" name="photo" type="file" accept="image/*" onchange="previewPhoto(this, 'photo-preview-edit')">
                                    <p class="text-[11px] text-text-muted mt-1">Formats acceptés: JPG, PNG, GIF (max 2MB)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Parent -->
                <div class="space-y-6">
                    <h4 class="font-label-md text-warning-amber flex items-center gap-2 border-b border-warning-amber/30 pb-2">
                        <span class="material-symbols-outlined text-[20px]">family_restroom</span>
                        INFORMATIONS DU PARENT
                    </h4>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Nom du Parent <span class="text-alert-red">*</span></label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-warning-amber focus:border-warning-amber" id="editParentLastname" required type="text">
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Prénom du Parent <span class="text-alert-red">*</span></label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-warning-amber focus:border-warning-amber" id="editParentFirstname" required type="text">
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Téléphone <span class="text-alert-red">*</span></label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-warning-amber focus:border-warning-amber" id="editParentPhone" required type="tel">
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Email</label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-warning-amber focus:border-warning-amber" id="editParentEmail" type="email">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-10 flex justify-end gap-4 pt-6 border-t border-outline-variant">
                <button class="px-6 py-2 text-on-surface-variant hover:bg-surface-subtle rounded-lg font-label-md transition-all" onclick="closeModal('modal-edit')" type="button">Annuler</button>
                <button class="px-8 py-2 bg-warning-amber text-white rounded-lg font-label-md hover:opacity-90 transition-all active:scale-95 shadow-md" type="submit">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>

<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    .glass-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(226, 232, 240, 1);
    }
    .modal-overlay {
        transition: backdrop-filter 0.3s ease;
    }

    #modal-standard, #modal-view, #modal-edit {
        transition: opacity 0.3s ease;
    }

    @keyframes ping {
        75%, 100% {
            transform: scale(2);
            opacity: 0;
        }
    }
    .animate-ping {
        animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
    }

    #modal-view-scroll::-webkit-scrollbar {
        width: 6px;
    }

    #modal-view-scroll::-webkit-scrollbar-track {
        background: transparent;
        border-radius: 10px;
    }

    #modal-view-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
        transition: background 0.2s ease;
    }

    #modal-view-scroll::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    #modal-view-scroll {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 transparent;
    }

    /* Custom Scrollbar pour le tableau */
    .custom-scrollbar {
        overflow-x: auto;
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 transparent;
        padding-bottom: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar {
        height: 8px;
        width: 8px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
        margin: 0 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
        transition: background 0.2s ease;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Uniformiser l'alignement vertical de toutes les cellules */
    tbody tr td {
        vertical-align: middle !important;
    }

    /* Assurer une hauteur minimale pour toutes les lignes */
    tbody tr {
        height: 60px;
    }

    /* Pour les badges et autres éléments inline */
    tbody tr td .flex,
    tbody tr td .inline-flex {
        align-items: center;
    }

    /* Style pour espacer les lignes du tableau */
    .border-separate {
        border-collapse: separate;
    }

    .border-spacing-y-2 {
        border-spacing: 0 8px;
    }
    
    tbody tr {
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    tbody tr:first-child td:first-child {
        border-top-left-radius: 8px;
    }

    tbody tr:first-child td:last-child {
        border-top-right-radius: 8px;
    }

    tbody tr:last-child td:first-child {
        border-bottom-left-radius: 8px;
    }
    
    tbody tr:last-child td:last-child {
        border-bottom-right-radius: 8px;
    }
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
            preview.innerHTML = `<span class="material-symbols-outlined text-3xl text-text-muted">photo_camera</span>`;
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
            placeholder: 'Rechercher une nationalité',
            render: {
                option: function(data, escape) {
                    return '<div class="flex items-center justify-between gap-3">' +
                        '<span>' + escape(data.text) + '</span>' +
                        '</div>';
                },
                item: function(data, escape) {
                    return '<div>' + escape(data.text) + '</div>';
                }
            }
        });

        if (!window.tomSelectInstances) {
            window.tomSelectInstances = {};
        }
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

                // Nationalité: select classique (Tom Select ou autre) -> valeur stockée dans le <select name="nationalite">
                // Le form envoie directement via name="nationalite".

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

                // Nationalité: select classique (Tom Select ou autre) -> valeur stockée dans le <select name="nationalite">

                HTMLFormElement.prototype.submit.call(editForm);
            }, true);
        }

        document.querySelectorAll('.delete-student-btn').forEach((button) => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                Swal.fire({
                    title: 'Êtes-vous sûr ?',
                    text: `L'élève "${this.dataset.name}" sera définitivement supprimé.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ba1a1a',
                    cancelButtonColor: '#64748B',
                    confirmButtonText: 'Oui, supprimer',
                    cancelButtonText: 'Annuler',
                    borderRadius: '12px'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.closest('form').submit();
                    }
                });
            }, true);
        });

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Succès',
                text: @json(session('success')),
                toast: false,
                position: 'center',
                showConfirmButton: false,
                confirmButtonText: 'Fermer',
                timer: 2500,
                timerProgressBar: false,
                customClass: {
                    popup: 'rounded-2xl'
                }
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: @json(session('error')),
                toast: false,
                position: 'center',
                showConfirmButton: false,
                confirmButtonText: 'Fermer',
                timer: 3000,
                timerProgressBar: false,
                customClass: {
                    popup: 'rounded-2xl'
                }
            });
        @endif
    });

    function openModal(id) {
        const modal = document.getElementById(id);
        const contentId = id + '-content';
        const content = document.getElementById(contentId);
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        const contentId = id + '-content';
        const content = document.getElementById(contentId);
        
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }, 300);
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
        const nationalite = student.nationalite ?? '-';
        document.getElementById('viewStudentNationalite').textContent = nationalite;

        
        const sexeRaw = (student.sexe ?? '').toString().trim();
        const sexeLower = sexeRaw.toLowerCase();
        let sexeDisplay = 'Non renseigné';

        const masculinValues = ['m', 'masculin', 'male', 'homme', 'h'];
        const femininValues = ['f', 'féminin', 'feminin', 'female', 'femme', 'f'];

        if (masculinValues.includes(sexeLower)) {
            sexeDisplay = 'Masculin';
        } else if (femininValues.includes(sexeLower)) {
            sexeDisplay = 'Féminin';
        } else if (sexeRaw === 'Masculin' || sexeRaw === 'MASCULIN') {
            sexeDisplay = 'Masculin';
        } else if (sexeRaw === 'Féminin' || sexeRaw === 'FEMININ' || sexeRaw === 'FÉMININ') {
            sexeDisplay = 'Féminin';
        }

        document.getElementById('viewStudentSexeDetail').textContent = sexeDisplay;

        const parentLastname = student.parent_lastname ?? null;
        const parentFirstname = student.parent_firstname ?? null;
        const parentPhone = student.parent_phone ?? null;
        const parentEmail = student.parent_email ?? null;

        const hasSeparatedParents = parentLastname || parentFirstname || parentPhone || parentEmail;

        document.getElementById('viewStudentParentLastname').textContent = parentLastname ?? '-';
        document.getElementById('viewStudentParentFirstname').textContent = parentFirstname ?? '-';
        document.getElementById('viewStudentParentPhone').textContent = parentPhone ?? '-';
        document.getElementById('viewStudentParentEmail').textContent = parentEmail ?? '-';

        if (!hasSeparatedParents) {
            const parentName = student.parent_name ?? 'N/A';
            document.getElementById('viewStudentParentLastname').textContent = parentName;
            document.getElementById('viewStudentParentFirstname').textContent = '-';
            document.getElementById('viewStudentParentPhone').textContent = '-';
            document.getElementById('viewStudentParentEmail').textContent = '-';
        }

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
                photoContainer.innerHTML = `<div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary/20 to-primary-container/20 rounded-full"><span class="text-3xl font-bold text-primary">${initials}</span></div>`;
            }, { once: true });
            photoContainer.replaceChildren(image);
        } else {
            photoContainer.innerHTML = `
                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary/20 to-primary-container/20 rounded-full">
                    <span class="text-3xl font-bold text-primary">${initials}</span>
                </div>
            `;
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
        const femininValues = ['f', 'féminin', 'feminin', 'female', 'femme', 'f'];

        if (masculinValues.includes(sexeLower) || sexe === 'Masculin') {
            document.querySelector('input[name="edit_sexe"][value="Masculin"]').checked = true;
        } else if (femininValues.includes(sexeLower) || sexe === 'Féminin' || sexe === 'FEMININ') {
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

        // Nationalité pré-sélectionnée à l'édition
        const nationaliteSelect = document.getElementById('editNationalite');
        if (nationaliteSelect) {
            nationaliteSelect.value = student.nationalite ?? '';
            if (window.tomSelectInstances?.editNationalite) {
                window.tomSelectInstances.editNationalite.setValue(student.nationalite ?? '', true);
            }
        }

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
            image.className = 'block w-full h-full object-cover object-center rounded-full';
            image.addEventListener('error', () => {
                photoPreviewEdit.innerHTML = `<span class="text-2xl font-bold text-primary">${editInitials}</span>`;
            }, { once: true });
            photoPreviewEdit.replaceChildren(image);
        } else {
            photoPreviewEdit.innerHTML = `<span class="text-2xl font-bold text-primary">${editInitials}</span>`;
        }

        openModal('modal-edit');
    }

    function viewStudent(student) {
        openViewModal(student);
    }

    function editStudent(student) {
        openEditModal(student);
    }

    function validateAge(inputDate, warningId) {
        if (!inputDate) return true;
        
        const birth = new Date(inputDate);
        const today = new Date();
        let age = today.getFullYear() - birth.getFullYear();
        const m = today.getMonth() - birth.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
            age--;
        }
        
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
                const modals = ['modal-standard', 'modal-view', 'modal-edit'];
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
