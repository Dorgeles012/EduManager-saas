<!-- View Modal -->
<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="modal-view">
    <div class="absolute inset-0 modal-overlay backdrop-blur-md bg-black/30" onclick="closeModal('modal-view')"></div>
    <div class="relative glass-card w-full max-w-4xl h-[90vh] rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0 flex flex-col" id="modal-view-content">
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
        <div class="flex-1 overflow-y-auto p-5 md:p-6" id="modal-view-scroll">
            <div class="flex flex-col md:flex-row md:items-start gap-6 pb-5 border-b border-outline-variant/30">
                <div class="relative">
                    <div class="w-28 h-28 rounded-full bg-gradient-to-br from-primary/10 to-primary-container/10 flex items-center justify-center shadow-md relative z-10 overflow-hidden" id="viewStudentPhotoContainer">
                        <span class="material-symbols-outlined text-5xl text-primary/40">account_circle</span>
                    </div>
                </div>
                <div class="flex-1 relative">
                    <h3 class="font-headline-xl text-[28px] text-on-surface mb-2 flex items-center gap-2" id="viewStudentFullName">-</h3>
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
            <div class="pt-5">
                <h4 class="font-headline-md text-[20px] text-on-surface mb-4">Informations scolaires</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="flex items-start gap-4 p-4 rounded-xl bg-surface-container-low/30">
                        <div class="p-2 bg-primary-fixed/20 rounded-lg"><span class="material-symbols-outlined text-primary">class</span></div>
                        <div><p class="text-label-sm text-[12px] text-text-muted uppercase">Classe</p><p class="font-body-md text-[16px] text-on-surface" id="viewStudentClasse">-</p></div>
                    </div>
                    <div class="flex items-start gap-4 p-4 rounded-xl bg-surface-container-low/30">
                        <div class="p-2 bg-primary-fixed/20 rounded-lg"><span class="material-symbols-outlined text-primary">account_tree</span></div>
                        <div><p class="text-label-sm text-[12px] text-text-muted uppercase">Niveau</p><p class="font-body-md text-[16px] text-on-surface" id="viewStudentNiveau">-</p></div>
                    </div>
                    <div class="flex items-start gap-4 p-4 rounded-xl bg-surface-container-low/30">
                        <div class="p-2 bg-primary-fixed/20 rounded-lg"><span class="material-symbols-outlined text-primary">category</span></div>
                        <div><p class="text-label-sm text-[12px] text-text-muted uppercase">Série</p><p class="font-body-md text-[16px]" id="viewStudentSerie">—</p></div>
                    </div>
                    <div class="flex items-start gap-4 p-4 rounded-xl bg-surface-container-low/30">
                        <div class="p-2 bg-primary-fixed/20 rounded-lg"><span class="material-symbols-outlined text-primary">wc</span></div>
                        <div><p class="text-label-sm text-[12px] text-text-muted uppercase">Sexe</p><p class="font-body-md text-[16px] text-on-surface" id="viewStudentSexeDetail">-</p></div>
                    </div>
                    <div class="md:col-span-2">
                        <div class="flex items-start gap-4 p-4 rounded-xl bg-surface-container-low/30">
                            <div class="p-2 bg-primary-fixed/20 rounded-lg"><span class="material-symbols-outlined text-primary">location_on</span></div>
                            <div><p class="text-label-sm text-[12px] text-text-muted uppercase">Lieu de naissance</p><p class="font-body-md text-[16px] text-on-surface" id="viewStudentBirthplace">-</p></div>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <div class="flex items-start gap-4 p-4 rounded-xl bg-surface-container-low/30">
                            <div class="p-2 bg-primary-fixed/20 rounded-lg"><span class="material-symbols-outlined text-primary">flag</span></div>
                            <div><p class="text-label-sm text-[12px] text-text-muted uppercase">Nationalité</p><p class="font-body-md text-[16px] text-on-surface" id="viewStudentNationalite">-</p></div>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 p-4 rounded-xl bg-surface-container-low/30">
                        <div class="p-2 bg-primary-fixed/20 rounded-lg"><span class="material-symbols-outlined text-primary">bed</span></div>
                        <div><p class="text-label-sm text-[12px] text-text-muted uppercase">Interne</p><p class="font-body-md text-[16px] text-on-surface" id="viewStudentInterne">-</p></div>
                    </div>
                    <div class="flex items-start gap-4 p-4 rounded-xl bg-surface-container-low/30">
                        <div class="p-2 bg-primary-fixed/20 rounded-lg"><span class="material-symbols-outlined text-primary">assignment_turned_in</span></div>
                        <div><p class="text-label-sm text-[12px] text-text-muted uppercase">Affecté</p><p class="font-body-md text-[16px] text-on-surface" id="viewStudentAffecte">-</p></div>
                    </div>
                    <div class="md:col-span-2">
                        <div class="flex items-start gap-4 p-4 rounded-xl bg-surface-container-low/30">
                            <div class="p-2 bg-primary-fixed/20 rounded-lg"><span class="material-symbols-outlined text-primary">family_restroom</span></div>
                            <div><p class="text-label-sm text-[12px] text-text-muted uppercase">Parents / Tuteurs</p>
                                <div class="mt-1 space-y-1">
                                    <p class="font-body-md text-[16px]">Nom : <span id="viewStudentParentLastname">-</span></p>
                                    <p class="font-body-md text-[16px]">Prénom : <span id="viewStudentParentFirstname">-</span></p>
                                    <p class="font-body-md text-[16px]">Téléphone : <span id="viewStudentParentPhone">-</span></p>
                                    <p class="font-body-md text-[16px]">Email : <span id="viewStudentParentEmail">-</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="px-5 md:px-6 py-4 border-t border-outline-variant/30 flex justify-end bg-white/95 rounded-b-2xl">
            <button class="px-4 py-2 bg-primary text-white rounded-lg font-label-md text-[14px] hover:bg-primary/90 transition-all" onclick="closeModal('modal-view')">Fermer</button>
        </div>
    </div>
</div>

<!-- Add Student Modal -->
<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="modal-standard">
    <div class="absolute inset-0 modal-overlay backdrop-blur-md bg-black/30" onclick="closeModal('modal-standard')"></div>
    <div class="relative glass-card w-full max-w-4xl max-h-[90vh] overflow-y-auto rounded-xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0" id="modal-standard-content">
        <div class="p-6 border-b border-outline-variant flex justify-between items-center bg-primary text-white sticky top-0 shadow-lg z-20">
            <h3 class="font-headline-md text-headline-md">AJOUT D'UN ÉLÈVE</h3>
            <button class="hover:bg-white/20 rounded-full p-1 transition-colors" onclick="closeModal('modal-standard')" type="button">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <form class="p-8" id="form-standard" action="{{ route('personnel.eleves.store') }}" method="POST" enctype="multipart/form-data">
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
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="sexe" value="Féminin" class="w-4 h-4 text-primary focus:ring-primary focus:ring-2 border-outline-variant">
                                    <span class="text-body-sm text-on-surface">Féminin</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Date de naissance <span class="text-alert-red">*</span></label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-primary focus:border-primary" id="birthdate-std" required type="date">
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Lieu de naissance</label>
                            <input class="w-full rounded-lg border-outline-variant focus:ring-primary focus:border-primary" id="stdBirthPlace" type="text">
                        </div>
                        <div>
                            <label class="block text-label-sm text-on-surface mb-1.5">Nationalité <span class="text-alert-red">*</span></label>
                            <select class="w-full rounded-lg border-outline-variant bg-white px-3 py-2 text-sm" id="stdNationalite" name="nationalite">
                                <option value="">Sélectionner une nationalité</option>
                                @foreach(config('nationalities', ['Ivoirienne', 'Française', 'Autre']) as $nationalite)
                                    <option value="{{ $nationalite }}">{{ $nationalite }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-label-sm text-on-surface mb-1.5">Interne <span class="text-alert-red">*</span></label>
                                <select class="w-full rounded-lg border-outline-variant focus:ring-primary focus:border-primary" id="stdInterne" name="interne" required>
                                    <option value="1">Oui</option>
                                    <option value="0" selected>Non</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-label-sm text-on-surface mb-1.5">Affecté <span class="text-alert-red">*</span></label>
                                <select class="w-full rounded-lg border-outline-variant focus:ring-primary focus:border-primary" id="stdAffecte" name="affecte" required>
                                    <option value="1">Oui</option>
                                    <option value="0" selected>Non</option>
                                </select>
                            </div>
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
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                <button class="px-6 py-2 text-on-surface-variant hover:bg-surface-subtle rounded-lg font-label-md transition-all" onclick="closeModal('modal-standard')" type="button">Annuler</button>
                <button class="px-8 py-2 bg-primary text-white rounded-lg font-label-md hover:bg-primary/90 transition-all active:scale-95 shadow-md" type="submit">Enregistrer l'élève</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Student Modal -->
<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="modal-edit">
    <div class="absolute inset-0 modal-overlay backdrop-blur-md bg-black/30" onclick="closeModal('modal-edit')"></div>
    <div class="relative glass-card w-full max-w-4xl max-h-[90vh] overflow-y-auto rounded-xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0" id="modal-edit-content">
        <div class="p-6 border-b border-outline-variant flex justify-between items-center bg-warning-amber text-white sticky top-0 shadow-lg z-20">
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
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="edit_sexe" value="Féminin" class="w-4 h-4 text-warning-amber focus:ring-warning-amber focus:ring-2 border-outline-variant">
                                    <span class="text-body-sm text-on-surface">Féminin</span>
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
                            <select class="w-full rounded-lg border-outline-variant bg-white px-3 py-2 text-sm" id="editNationalite" name="nationalite">
                                <option value="">Sélectionner une nationalité</option>
                                @foreach(config('nationalities', ['Ivoirienne', 'Française', 'Autre']) as $nationalite)
                                    <option value="{{ $nationalite }}">{{ $nationalite }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-label-sm text-on-surface mb-1.5">Interne <span class="text-alert-red">*</span></label>
                                <select class="w-full rounded-lg border-outline-variant focus:ring-warning-amber focus:border-warning-amber" id="editInterne" name="interne" required>
                                    <option value="1">Oui</option>
                                    <option value="0">Non</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-label-sm text-on-surface mb-1.5">Affecté <span class="text-alert-red">*</span></label>
                                <select class="w-full rounded-lg border-outline-variant focus:ring-warning-amber focus:border-warning-amber" id="editAffecte" name="affecte" required>
                                    <option value="1">Oui</option>
                                    <option value="0">Non</option>
                                </select>
                            </div>
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
                            <label class="block text-label-sm text-on-surface mb-1.5">Photo</label>
                            <input class="w-full text-body-sm text-text-muted file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-label-md file:bg-warning-amber/20 file:text-warning-amber hover:file:bg-warning-amber/30" id="editPhoto" name="photo" type="file" accept="image/*" onchange="previewPhoto(this, 'photo-preview-edit')">
                            <div class="w-20 h-20 mt-2 rounded-full bg-surface-container border-2 border-dashed border-outline-variant flex items-center justify-center overflow-hidden" id="photo-preview-edit">
                                <span class="material-symbols-outlined text-3xl text-text-muted">photo_camera</span>
                            </div>
                        </div>
                    </div>
                </div>
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
    .animate-ping { animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite; }
    @keyframes ping { 75%, 100% { transform: scale(2); opacity: 0; } }
    #modal-view-scroll::-webkit-scrollbar { width: 6px; }
    #modal-view-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    #modal-view-scroll { scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent; }
</style>
