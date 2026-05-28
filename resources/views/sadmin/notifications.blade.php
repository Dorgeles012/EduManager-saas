@extends('sadmin.layouts.app')

@section('content')
<div class="max-w-max-width mx-auto">
    <div class="mb-8">
        <h2 class="font-headline-lg text-headline-lg text-on-surface">Gestion des Notifications</h2>
        <p class="text-body-md text-text-muted mt-1">Créez, planifiez et envoyez des messages critiques à votre écosystème EduManager.</p>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Form Section -->
        <section class="lg:col-span-7 space-y-6">
            <div class="bg-surface-container-lowest rounded-xl p-6 card-shadow border border-slate-200">
                <div class="flex items-center gap-2 mb-6">
                    <span class="material-symbols-outlined text-primary">edit_note</span>
                    <h3 class="font-headline-md text-headline-md">Créer une notification</h3>
                </div>
                <form class="space-y-5" id="notificationForm" onsubmit="event.preventDefault();">
                    <div>
                        <label class="block font-label-md text-label-md text-on-surface-variant mb-2">Titre du message</label>
                        <input class="w-full bg-white border border-outline-variant rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-container focus:border-primary font-body-md" placeholder="Ex: Maintenance système prévue" type="text" id="titreMessage">
                    </div>
                    <div>
                        <label class="block font-label-md text-label-md text-on-surface-variant mb-2">Contenu du message</label>
                        <textarea class="w-full bg-white border border-outline-variant rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-container focus:border-primary font-body-md" placeholder="Saisissez le corps de votre notification ici..." rows="4" id="contenuMessage"></textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-label-md text-label-md text-on-surface-variant mb-2">Catégorie</label>
                            <select class="w-full bg-white border border-outline-variant rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-container focus:border-primary appearance-none cursor-pointer" id="categorieSelect">
                                <option value="Système">Système</option>
                                <option value="Sécurité">Sécurité</option>
                                <option value="Facturation">Facturation</option>
                                <option value="Nouveauté Client">Nouveauté Client</option>
                                <option value="Académique">Académique</option>
                                <option value="Événementiel">Événementiel</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-label-md text-label-md text-on-surface-variant mb-2">Niveau de priorité</label>
                            <div class="flex gap-2" id="priorityButtons">
                                <button type="button" class="priority-btn flex-1 py-2 text-label-sm border border-outline-variant rounded-lg hover:bg-surface-subtle flex items-center justify-center gap-1" data-priority="low">
                                    <div class="w-2 h-2 rounded-full bg-success-green"></div> Basse
                                </button>
                                <button type="button" class="priority-btn flex-1 py-2 text-label-sm border border-outline-variant rounded-lg hover:bg-surface-subtle flex items-center justify-center gap-1" data-priority="normal">
                                    <div class="w-2 h-2 rounded-full bg-warning-amber"></div> Normale
                                </button>
                                <button type="button" class="priority-btn flex-1 py-2 text-label-sm border border-outline-variant rounded-lg hover:bg-surface-subtle flex items-center justify-center gap-1" data-priority="urgent">
                                    <div class="w-2 h-2 rounded-full bg-alert-red"></div> Urgente
                                </button>
                            </div>
                            <input type="hidden" id="selectedPriority" value="normal">
                        </div>
                    </div>
                    
                    <!-- Audience Dropdown -->
                    <div>
                        <label class="block font-label-md text-label-md text-on-surface-variant mb-2">Audience cible</label>
                        <select class="w-full bg-white border border-outline-variant rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-container focus:border-primary font-body-md cursor-pointer appearance-none bg-no-repeat bg-right" style="background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2224%22 height=%2224%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%2364748B%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22%3E%3Cpolyline points=%226 9 12 15 18 9%22%3E%3C/polyline%3E%3C/svg%3E'); background-position: right 1rem center; padding-right: 2.5rem;" id="audienceSelect">
                            <option value="tous">📢 Tous les utilisateurs</option>
                            <option value="personnels">👥 Personnels (Administratifs & Éducatifs)</option>
                            <option value="enseignants">👨‍🏫 Enseignants uniquement</option>
                            <option value="eleves">🎓 Élèves uniquement</option>
                            <option value="parents">👪 Parents d'élèves</option>
                            <option value="administrateurs">🔐 Administrateurs seulement</option>
                            <option value="comptabilite">💰 Service Comptabilité</option>
                            <option value="direction">🏛️ Direction</option>
                        </select>
                        <p class="text-[11px] text-text-muted mt-1">* Sélectionnez le groupe qui recevra cette notification</p>
                    </div>

                    <!-- Date et heure de planification -->
                    <div class="pt-4 border-t border-surface-subtle">
                        <label class="block font-label-md text-label-md text-on-surface-variant mb-4">Planification (Optionnel)</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-label-sm text-text-muted mb-1">Date d'envoi</label>
                                <input type="date" id="scheduleDate" class="w-full bg-white border border-outline-variant rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-container focus:border-primary font-body-md cursor-pointer">
                            </div>
                            <div>
                                <label class="block text-label-sm text-text-muted mb-1">Heure d'envoi</label>
                                <input type="time" id="scheduleTime" class="w-full bg-white border border-outline-variant rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-container focus:border-primary font-body-md cursor-pointer">
                            </div>
                        </div>
                        <div class="mt-2 flex items-center gap-2">
                            <input type="checkbox" id="scheduleToggle" class="rounded text-primary focus:ring-primary h-4 w-4">
                            <label for="scheduleToggle" class="text-body-sm text-on-surface-variant">Planifier cette notification (sinon envoi immédiat)</label>
                        </div>
                    </div>

                    <!-- Canaux de diffusion -->
                    <div class="pt-4 border-t border-surface-subtle">
                        <label class="block font-label-md text-label-md text-on-surface-variant mb-4">Canaux de diffusion</label>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="channel-option flex flex-col items-center p-3 rounded-lg border border-primary bg-primary-fixed/30 text-primary cursor-pointer" data-channel="inapp">
                                <span class="material-symbols-outlined mb-1">chat</span>
                                <span class="text-[11px] font-bold uppercase">In-App</span>
                            </div>
                            <div class="channel-option flex flex-col items-center p-3 rounded-lg border border-outline-variant hover:bg-surface-subtle cursor-pointer text-on-surface-variant" data-channel="email">
                                <span class="material-symbols-outlined mb-1">mail</span>
                                <span class="text-[11px] font-bold uppercase">Email</span>
                            </div>
                            <div class="channel-option flex flex-col items-center p-3 rounded-lg border border-outline-variant hover:bg-surface-subtle cursor-pointer text-on-surface-variant" data-channel="push">
                                <span class="material-symbols-outlined mb-1">notifications_active</span>
                                <span class="text-[11px] font-bold uppercase">Push</span>
                            </div>
                        </div>
                        <input type="hidden" id="selectedChannel" value="inapp">
                    </div>

                    <!-- Boutons d'action -->
                    <div class="flex flex-wrap items-center justify-end gap-3 mt-8">
                        <button class="px-6 py-2.5 border border-outline-variant rounded-lg font-label-md text-label-md text-on-surface-variant hover:bg-surface-subtle" type="button" id="draftBtn">
                            Sauvegarder brouillon
                        </button>
                        <button class="px-6 py-2.5 border border-secondary text-secondary rounded-lg font-label-md text-label-md hover:bg-secondary-fixed/20 flex items-center gap-2" type="button" id="scheduleBtn">
                            <span class="material-symbols-outlined text-[20px]">calendar_today</span>
                            Planifier
                        </button>
                        <button class="px-8 py-2.5 bg-primary text-white rounded-lg font-label-md text-label-md flex items-center gap-2 hover:opacity-90 shadow-md" type="submit" id="sendBtn">
                            <span class="material-symbols-outlined text-[20px]">send</span>
                            Envoyer maintenant
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <!-- History / Preview Section -->
        <section class="lg:col-span-5 space-y-6">
            <!-- Preview Card -->
            <div class="bg-surface-container rounded-xl p-6 border border-outline-variant">
                <h4 class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider mb-4">Aperçu du rendu</h4>
                <div class="bg-white rounded-lg p-4 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1 h-full bg-primary" id="previewBar"></div>
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-[10px] font-bold text-primary uppercase px-2 py-0.5 bg-primary-fixed rounded" id="previewCategory">Notification Système</span>
                        <span class="text-[10px] text-text-muted" id="previewDate">À l'instant</span>
                    </div>
                    <p class="font-label-md text-label-md text-on-surface mb-1" id="previewTitle">Maintenance Hebdomadaire</p>
                    <p class="text-body-sm text-on-surface-variant line-clamp-2" id="previewContent">L'accès à la plateforme sera limité ce dimanche entre 2h et 4h du matin pour une mise à jour...</p>
                </div>
            </div>

            <!-- Stats Card -->
            <div class="bg-surface-container-lowest rounded-xl p-6 card-shadow border border-slate-100">
                <h4 class="font-headline-md text-headline-md mb-4">Derniers envois</h4>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 rounded-lg border border-surface-subtle">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-success-green/10 flex items-center justify-center text-success-green">
                                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                            </div>
                            <div>
                                <p class="font-label-md text-label-md text-on-surface">Update V2.4</p>
                                <p class="text-[11px] text-text-muted">14 Oct 2023 • 1,240 reçus</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-label-md text-label-md text-primary">94.2%</p>
                            <p class="text-[10px] text-text-muted uppercase">Ouverture</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-lg border border-surface-subtle">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-warning-amber/10 flex items-center justify-center text-warning-amber">
                                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">schedule</span>
                            </div>
                            <div>
                                <p class="font-label-md text-label-md text-on-surface">Facturation Q3</p>
                                <p class="text-[11px] text-text-muted">Demain 09:00 • 450 prévus</p>
                            </div>
                        </div>
                        <div class="px-3 py-1 bg-warning-amber/10 text-warning-amber text-[10px] font-bold rounded-full uppercase">
                            Planifié
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-lg border border-surface-subtle">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-alert-red/10 flex items-center justify-center text-alert-red">
                                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">error</span>
                            </div>
                            <div>
                                <p class="font-label-md text-label-md text-on-surface">Sécurité Critique</p>
                                <p class="text-[11px] text-text-muted">12 Oct 2023 • 1,240 reçus</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-label-md text-label-md text-primary">99.8%</p>
                            <p class="text-[10px] text-text-muted uppercase">Délivré</p>
                        </div>
                    </div>
                </div>
                <a href="{{ route('sadmin.notifications.historique') }}" class="w-full mt-4 text-center text-label-md text-primary hover:underline block">
                    Voir tout l'historique
                </a>
            </div>
        </section>
    </div>
</div>

<!-- Modal Calendrier -->
<div id="calendarModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="bg-gradient-to-r from-primary to-primary-container px-6 py-4 flex justify-between items-center">
            <h3 class="text-white font-headline-md text-headline-md">Choisir une date</h3>
            <button id="closeCalendarModal" class="text-white hover:opacity-80">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6">
            <input type="date" id="modalCalendarDate" class="w-full border border-outline-variant rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary focus:border-primary">
            <div class="mt-6 flex gap-3">
                <button id="cancelCalendarBtn" class="flex-1 px-4 py-2 border border-outline-variant rounded-lg hover:bg-surface-subtle">Annuler</button>
                <button id="confirmCalendarBtn" class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:opacity-90">Confirmer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Planification -->
<div id="scheduleConfirmModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="bg-gradient-to-r from-secondary to-secondary/80 px-6 py-4">
            <h3 class="text-white font-headline-md text-headline-md">Notification planifiée</h3>
        </div>
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <span class="material-symbols-outlined text-secondary text-3xl">event_available</span>
                <div>
                    <p class="text-body-sm text-text-muted">Votre notification sera envoyée le :</p>
                    <p class="font-headline-md text-headline-md text-on-surface" id="scheduleDisplayDate"></p>
                </div>
            </div>
            <button id="closeScheduleModal" class="w-full px-4 py-2 bg-primary text-white rounded-lg hover:opacity-90">Fermer</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Gestion des priorités
    let selectedPriority = 'normal';
    const priorityBtns = document.querySelectorAll('.priority-btn');
    const priorityInput = document.getElementById('selectedPriority');
    
    priorityBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Réinitialiser tous les boutons
            priorityBtns.forEach(b => {
                b.classList.remove('border-primary', 'bg-primary-fixed', 'text-primary');
                b.classList.add('border-outline-variant');
            });
            // Activer le bouton cliqué
            btn.classList.remove('border-outline-variant');
            btn.classList.add('border-primary', 'bg-primary-fixed', 'text-primary');
            selectedPriority = btn.getAttribute('data-priority');
            priorityInput.value = selectedPriority;
            
            // Changer la couleur de la barre d'aperçu
            const previewBar = document.getElementById('previewBar');
            if (selectedPriority === 'low') {
                previewBar.style.backgroundColor = '#059669';
            } else if (selectedPriority === 'normal') {
                previewBar.style.backgroundColor = '#D97706';
            } else {
                previewBar.style.backgroundColor = '#E11D48';
            }
        });
    });
    
    // Activer la priorité "Normale" par défaut
    const defaultPriorityBtn = document.querySelector('.priority-btn[data-priority="normal"]');
    if (defaultPriorityBtn) {
        defaultPriorityBtn.classList.add('border-primary', 'bg-primary-fixed', 'text-primary');
        defaultPriorityBtn.classList.remove('border-outline-variant');
    }
    
    // Gestion des canaux
    let selectedChannel = 'inapp';
    const channelOptions = document.querySelectorAll('.channel-option');
    const channelInput = document.getElementById('selectedChannel');
    
    channelOptions.forEach(channel => {
        channel.addEventListener('click', () => {
            channelOptions.forEach(c => {
                c.classList.remove('border-primary', 'bg-primary-fixed/30', 'text-primary');
                c.classList.add('border-outline-variant', 'text-on-surface-variant');
            });
            channel.classList.remove('border-outline-variant', 'text-on-surface-variant');
            channel.classList.add('border-primary', 'bg-primary-fixed/30', 'text-primary');
            selectedChannel = channel.getAttribute('data-channel');
            channelInput.value = selectedChannel;
        });
    });
    
    // Calendrier Modal
    const scheduleBtn = document.getElementById('scheduleBtn');
    const calendarModal = document.getElementById('calendarModal');
    const scheduleConfirmModal = document.getElementById('scheduleConfirmModal');
    const closeCalendarModal = document.getElementById('closeCalendarModal');
    const cancelCalendarBtn = document.getElementById('cancelCalendarBtn');
    const confirmCalendarBtn = document.getElementById('confirmCalendarBtn');
    const modalCalendarDate = document.getElementById('modalCalendarDate');
    const scheduleDisplayDate = document.getElementById('scheduleDisplayDate');
    const scheduleDateInput = document.getElementById('scheduleDate');
    const scheduleTimeInput = document.getElementById('scheduleTime');
    const scheduleToggle = document.getElementById('scheduleToggle');
    
    // Ouvrir le modal du calendrier
    if (scheduleBtn) {
        scheduleBtn.addEventListener('click', () => {
            const today = new Date().toISOString().split('T')[0];
            modalCalendarDate.value = today;
            calendarModal.style.display = 'flex';
        });
    }
    
    // Fermer le modal
    const closeModal = () => {
        calendarModal.style.display = 'none';
    };
    
    if (closeCalendarModal) closeCalendarModal.addEventListener('click', closeModal);
    if (cancelCalendarBtn) cancelCalendarBtn.addEventListener('click', closeModal);
    
    // Confirmer la date
    if (confirmCalendarBtn) {
        confirmCalendarBtn.addEventListener('click', () => {
            const selectedDate = modalCalendarDate.value;
            if (selectedDate) {
                scheduleDateInput.value = selectedDate;
                scheduleToggle.checked = true;
                calendarModal.style.display = 'none';
                
                // Afficher le modal de confirmation
                const formattedDate = new Date(selectedDate).toLocaleDateString('fr-FR', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                scheduleDisplayDate.textContent = formattedDate;
                scheduleConfirmModal.style.display = 'flex';
            }
        });
    }
    
    // Fermer le modal de confirmation
    const closeScheduleModal = document.getElementById('closeScheduleModal');
    if (closeScheduleModal) {
        closeScheduleModal.addEventListener('click', () => {
            scheduleConfirmModal.style.display = 'none';
        });
    }
    
    // Cliquer en dehors pour fermer
    window.addEventListener('click', (e) => {
        if (e.target === calendarModal) {
            calendarModal.style.display = 'none';
        }
        if (e.target === scheduleConfirmModal) {
            scheduleConfirmModal.style.display = 'none';
        }
    });
    
    // Désactiver/activer les champs de planification selon le toggle
    if (scheduleToggle) {
        scheduleToggle.addEventListener('change', () => {
            scheduleDateInput.disabled = !scheduleToggle.checked;
            scheduleTimeInput.disabled = !scheduleToggle.checked;
            if (!scheduleToggle.checked) {
                scheduleDateInput.value = '';
                scheduleTimeInput.value = '';
            }
        });
        // Initialiser
        scheduleDateInput.disabled = true;
        scheduleTimeInput.disabled = true;
    }
    
    // Aperçu en temps réel
    const titreMessage = document.getElementById('titreMessage');
    const contenuMessage = document.getElementById('contenuMessage');
    const categorieSelect = document.getElementById('categorieSelect');
    const previewTitle = document.getElementById('previewTitle');
    const previewContent = document.getElementById('previewContent');
    const previewCategory = document.getElementById('previewCategory');
    const previewDate = document.getElementById('previewDate');
    
    if (titreMessage) {
        titreMessage.addEventListener('input', () => {
            previewTitle.textContent = titreMessage.value || "Maintenance Hebdomadaire";
        });
    }
    
    if (contenuMessage) {
        contenuMessage.addEventListener('input', () => {
            const text = contenuMessage.value || "L'accès à la plateforme sera limité ce dimanche entre 2h et 4h du matin pour une mise à jour...";
            previewContent.textContent = text.length > 80 ? text.substring(0, 80) + '...' : text;
        });
    }
    
    if (categorieSelect) {
        categorieSelect.addEventListener('change', () => {
            previewCategory.textContent = `Notification ${categorieSelect.value}`;
        });
    }
    
    // Mise à jour de la date dans l'aperçu
    if (scheduleDateInput && scheduleTimeInput && previewDate) {
        const updatePreviewDate = () => {
            if (scheduleToggle.checked && scheduleDateInput.value) {
                const date = new Date(scheduleDateInput.value);
                const time = scheduleTimeInput.value || '00:00';
                previewDate.textContent = `Prévue le ${date.toLocaleDateString('fr-FR')} à ${time}`;
            } else {
                previewDate.textContent = 'Envoi immédiat';
            }
        };
        scheduleDateInput.addEventListener('change', updatePreviewDate);
        scheduleTimeInput.addEventListener('change', updatePreviewDate);
        scheduleToggle.addEventListener('change', updatePreviewDate);
    }
    
    // Sauvegarde brouillon
    const draftBtn = document.getElementById('draftBtn');
    if (draftBtn) {
        draftBtn.addEventListener('click', () => {
            alert('📝 Brouillon sauvegardé avec succès !');
        });
    }
    
    // Envoi immédiat
    const sendBtn = document.getElementById('sendBtn');
    if (sendBtn) {
        sendBtn.addEventListener('click', () => {
            if (scheduleToggle.checked && scheduleDateInput.value) {
                // C'est une notification planifiée
                alert(`✅ Notification planifiée pour le ${new Date(scheduleDateInput.value).toLocaleDateString('fr-FR')} à ${scheduleTimeInput.value || '00:00'}`);
            } else {
                // Envoi immédiat
                sendBtn.innerHTML = '<span class="material-symbols-outlined animate-spin">sync</span> Envoi en cours...';
                sendBtn.classList.add('opacity-75', 'pointer-events-none');
                
                setTimeout(() => {
                    sendBtn.innerHTML = '<span class="material-symbols-outlined">check</span> Envoyé !';
                    sendBtn.classList.replace('bg-primary', 'bg-success-green');
                    
                    setTimeout(() => {
                        sendBtn.innerHTML = '<span class="material-symbols-outlined text-[20px]">send</span> Envoyer maintenant';
                        sendBtn.classList.replace('bg-success-green', 'bg-primary');
                        sendBtn.classList.remove('opacity-75', 'pointer-events-none');
                    }, 2000);
                }, 1500);
            }
        });
    }
</script>
@endsection