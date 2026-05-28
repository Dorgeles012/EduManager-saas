@extends('sadmin.layouts.app')

@section('content')

<!-- Section 1: Bento Grid Layout for Primary Settings -->
<div class="grid grid-cols-12 gap-6">
    <!-- Account Settings (Large Bento) -->
    <section class="col-span-12 lg:col-span-8 bg-surface-container-lowest rounded-2xl p-5 card-shadow border border-surface-subtle/50">
        <a href="{{ route('sadmin.compte') }}" class="block" aria-label="Ouvrir la page Compte">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="bg-gradient-to-br from-primary/10 to-primary-container/10 p-2 rounded-xl">
                        <span class="material-symbols-outlined text-primary text-xl">person_outline</span>
                    </div>
                    <div>
                        <h3 class="font-headline-lg text-[18px] text-on-surface">Paramètres du Compte</h3>
                        <p class="text-[12px] text-text-muted mt-0.5">Gérez vos informations personnelles et votre identité publique.</p>
                    </div>
                </div>
                <div>
                    <button type="button" class="px-3 py-1.5 text-primary hover:bg-primary/5 rounded-lg text-[12px] font-medium">Modifier</button>
                </div>
            </div>
        </a>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-3">
                <div class="border-b border-outline-variant/30 pb-2">
                    <label class="text-[10px] text-text-muted uppercase tracking-wide">Nom complet</label>
                    <p class="text-[14px] font-semibold text-on-surface mt-1">Dr. Sarah Elisabeth Martin</p>
                </div>
                <div class="border-b border-outline-variant/30 pb-2">
                    <label class="text-[10px] text-text-muted uppercase tracking-wide">Email professionnel</label>
                    <p class="text-[14px] font-semibold text-on-surface mt-1">sarah.martin@academie.edu</p>
                </div>
                <div class="border-b border-outline-variant/30 pb-2">
                    <label class="text-[10px] text-text-muted uppercase tracking-wide">Téléphone</label>
                    <p class="text-[14px] font-semibold text-on-surface mt-1">+33 (0)1 23 45 67 89</p>
                </div>
            </div>
            <div class="space-y-3">
                <div class="border-b border-outline-variant/30 pb-2">
                    <label class="text-[10px] text-text-muted uppercase tracking-wide">Dernière connexion</label>
                    <p class="text-[14px] font-semibold text-success-green mt-1">Aujourd'hui, 09:42</p>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Section 2: Settings Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
    <!-- Security Card -->
    <div class="bg-surface-container-lowest rounded-2xl p-5 card-shadow border border-surface-subtle/50">
        <div class="flex items-start gap-4">
            <div class="p-2 bg-gradient-to-br from-secondary-container to-secondary/20 rounded-xl text-on-secondary-container">
                <span class="material-symbols-outlined text-xl">security</span>
            </div>
            <div class="flex-1">
                <h3 class="font-headline-lg text-[16px] mb-1">Sécurité & Authentification</h3>
                <p class="text-[12px] text-text-muted mb-4">Gérez vos préférences de sécurité et méthodes d'authentification.</p>
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-1.5 border-b border-outline-variant/20">
                        <div>
                            <span class="text-[13px] font-medium">Authentification 2 facteurs</span>
                            <p class="text-[10px] text-text-muted">Sécurité renforcée</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-10 h-5 bg-outline-variant/50 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between py-1.5 border-b border-outline-variant/20">
                        <div>
                            <span class="text-[13px] font-medium">Notifications de connexion</span>
                            <p class="text-[10px] text-text-muted">Alertes email pour nouveaux appareils</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-10 h-5 bg-outline-variant/50 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                        </label>
                    </div>
                    <button id="changePasswordBtn" class="w-full mt-3 px-3 py-2 text-primary border border-primary/30 rounded-lg hover:bg-primary/5 font-medium text-[12px]">
                        Changer le mot de passe
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- System Preferences Card -->
    <div class="bg-surface-container-lowest rounded-2xl p-5 card-shadow border border-surface-subtle/50">
        <div class="flex items-start gap-4">
            <div class="p-2 bg-gradient-to-br from-primary-fixed to-primary/20 rounded-xl text-primary">
                <span class="material-symbols-outlined text-xl">tune</span>
            </div>
            <div class="flex-1">
                <h3 class="font-headline-lg text-[16px] mb-1">Préférences Système</h3>
                <p class="text-[12px] text-text-muted mb-4">Configurez les paramètres globaux de l'interface et de la plateforme.</p>
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-1.5">
                        <label class="text-[13px] font-medium">Langue du système</label>
                        <select class="text-[12px] bg-surface-container-high border border-outline-variant/50 rounded-lg px-2 py-1.5 focus:border-primary focus:ring-2 focus:ring-primary/20 cursor-pointer font-medium text-primary">
                            <option>Français (FR)</option>
                        </select>
                    </div>
                    <div class="flex items-center justify-between py-1.5">
                        <label class="text-[13px] font-medium">Fuseau horaire</label>
                        <span class="text-[12px] bg-surface-container-high px-2 py-1.5 rounded-lg text-text-muted">Europe/Paris (GMT+1)</span>
                    </div>
                    <div class="flex items-center justify-between py-1.5">
                        <div>
                            <span class="text-[13px] font-medium">Mode Sombre</span>
                            <p class="text-[10px] text-text-muted">Basculer l'apparence visuelle</p>
                        </div>
                        <button class="w-10 h-5 bg-outline-variant/50 rounded-full relative focus:outline-none shadow-inner" id="theme-toggle">
                            <div class="w-4 h-4 bg-white rounded-full absolute left-0.5 top-0.5 transition-all duration-300 translate-x-0 shadow-md" id="toggle-circle"></div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Changement de mot de passe -->
<div id="passwordModal" class="fixed inset-0 z-50 hidden items-center justify-center" style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden">
        <!-- En-tête du modal avec couleur bleue -->
        <div class="bg-gradient-to-r from-primary to-primary-container px-5 py-3 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-white text-xl">lock</span>
                <h3 class="text-white font-headline-md text-[16px]">Changement de mot de passe</h3>
            </div>
            <button id="closePasswordModal" class="text-white hover:opacity-80">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
        
        <!-- Corps du modal -->
        <div class="p-5">
            <div class="mb-3 p-2 bg-primary/10 rounded-lg border border-primary/20">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[18px]">info</span>
                    <p class="text-[11px] text-on-surface-variant">Pour votre sécurité, choisissez un mot de passe robuste d'au moins 8 caractères avec des lettres, chiffres et symboles.</p>
                </div>
            </div>
            
            <form id="passwordForm" class="space-y-4">
                <div class="space-y-1.5">
                    <label class="text-[11px] text-on-surface-variant font-medium">Mot de passe actuel</label>
                    <div class="relative">
                        <input type="password" id="currentPassword" class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-[13px] pr-8" placeholder="Entrez votre mot de passe actuel">
                        <span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-on-surface-variant cursor-pointer hover:text-primary toggle-password text-[18px]">visibility</span>
                    </div>
                </div>
                
                <div class="space-y-1.5">
                    <label class="text-[11px] text-on-surface-variant font-medium">Nouveau mot de passe</label>
                    <div class="relative">
                        <input type="password" id="newPassword" class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-[13px] pr-8" placeholder="••••••••">
                        <span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-on-surface-variant cursor-pointer hover:text-primary toggle-password text-[18px]">visibility</span>
                    </div>
                    <div class="flex gap-2 mt-1.5">
                        <div class="flex-1 h-1 bg-surface-container-high rounded-full overflow-hidden">
                            <div id="passwordStrength" class="h-full w-0 bg-alert-red transition-all duration-300"></div>
                        </div>
                    </div>
                    <p id="strengthText" class="text-[10px] text-text-muted">Force du mot de passe</p>
                </div>
                
                <div class="space-y-1.5">
                    <label class="text-[11px] text-on-surface-variant font-medium">Confirmer le nouveau mot de passe</label>
                    <div class="relative">
                        <input type="password" id="confirmPassword" class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-[13px] pr-8" placeholder="Confirmez votre nouveau mot de passe">
                        <span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-on-surface-variant cursor-pointer hover:text-primary toggle-password text-[18px]">visibility</span>
                    </div>
                </div>
                
                <!-- Boutons d'action -->
                <div class="flex justify-end gap-3 pt-3 border-t border-outline-variant/30">
                    <button type="button" id="cancelPasswordBtn" class="px-4 py-1.5 border border-outline-variant rounded-lg text-on-surface-variant hover:bg-surface-subtle font-medium text-[12px]">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-1.5 bg-gradient-to-r from-primary to-primary-container text-white rounded-lg hover:opacity-90 font-medium shadow-md text-[12px]">
                        Mettre à jour le mot de passe
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Theme Toggle Simulation
    const themeToggle = document.getElementById('theme-toggle');
    const toggleCircle = document.getElementById('toggle-circle');
    let isDarkMode = false;

    if (themeToggle && toggleCircle) {
        themeToggle.addEventListener('click', () => {
            isDarkMode = !isDarkMode;
            if (isDarkMode) {
                themeToggle.classList.replace('bg-outline-variant/50', 'bg-primary');
                toggleCircle.style.transform = 'translateX(22px)';
                document.documentElement.classList.add('dark');
            } else {
                themeToggle.classList.replace('bg-primary', 'bg-outline-variant/50');
                toggleCircle.style.transform = 'translateX(0)';
                document.documentElement.classList.remove('dark');
            }
        });
    }

    // Modal Password Change
    const changePasswordBtn = document.getElementById('changePasswordBtn');
    const passwordModal = document.getElementById('passwordModal');
    const closePasswordModal = document.getElementById('closePasswordModal');
    const cancelPasswordBtn = document.getElementById('cancelPasswordBtn');
    const passwordForm = document.getElementById('passwordForm');
    
    // Ouvrir le modal
    if (changePasswordBtn) {
        changePasswordBtn.addEventListener('click', () => {
            passwordModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        });
    }
    
    // Fermer le modal
    const closeModal = () => {
        passwordModal.style.display = 'none';
        document.body.style.overflow = '';
        // Réinitialiser le formulaire
        if (passwordForm) {
            passwordForm.reset();
            const strengthBar = document.getElementById('passwordStrength');
            const strengthText = document.getElementById('strengthText');
            if (strengthBar) strengthBar.style.width = '0%';
            if (strengthText) strengthText.textContent = 'Force du mot de passe';
            if (strengthText) strengthText.className = 'text-[10px] text-text-muted';
        }
    };
    
    if (closePasswordModal) closePasswordModal.addEventListener('click', closeModal);
    if (cancelPasswordBtn) cancelPasswordBtn.addEventListener('click', closeModal);
    
    // Cliquer en dehors pour fermer
    window.addEventListener('click', (e) => {
        if (e.target === passwordModal) {
            closeModal();
        }
    });
    
    // Toggle password visibility
    const togglePasswordBtns = document.querySelectorAll('.toggle-password');
    togglePasswordBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.previousElementSibling;
            if (input.type === 'password') {
                input.type = 'text';
                btn.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                btn.textContent = 'visibility';
            }
        });
    });
    
    // Vérification de la force du mot de passe
    const newPasswordInput = document.getElementById('newPassword');
    const strengthBar = document.getElementById('passwordStrength');
    const strengthText = document.getElementById('strengthText');
    
    if (newPasswordInput) {
        newPasswordInput.addEventListener('input', () => {
            const password = newPasswordInput.value;
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            const width = (strength / 4) * 100;
            strengthBar.style.width = width + '%';
            
            if (strength === 0) {
                strengthBar.className = 'h-full w-0 bg-alert-red transition-all duration-300';
                strengthText.textContent = 'Entrez un mot de passe';
                strengthText.className = 'text-[10px] text-text-muted';
            } else if (strength <= 2) {
                strengthBar.className = 'h-full bg-alert-red transition-all duration-300';
                strengthText.textContent = 'Mot de passe faible';
                strengthText.className = 'text-[10px] text-alert-red';
            } else if (strength === 3) {
                strengthBar.className = 'h-full bg-warning-amber transition-all duration-300';
                strengthText.textContent = 'Mot de passe moyen';
                strengthText.className = 'text-[10px] text-warning-amber';
            } else {
                strengthBar.className = 'h-full bg-success-green transition-all duration-300';
                strengthText.textContent = 'Mot de passe fort';
                strengthText.className = 'text-[10px] text-success-green';
            }
        });
    }
    
    // Soumission du formulaire
    if (passwordForm) {
        passwordForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const currentPassword = document.getElementById('currentPassword')?.value;
            const newPassword = document.getElementById('newPassword')?.value;
            const confirmPassword = document.getElementById('confirmPassword')?.value;
            
            if (!currentPassword) {
                alert('Veuillez entrer votre mot de passe actuel');
                return;
            }
            
            if (newPassword !== confirmPassword) {
                alert('Les mots de passe ne correspondent pas');
                return;
            }
            
            if (newPassword.length < 8) {
                alert('Le mot de passe doit contenir au moins 8 caractères');
                return;
            }
            
            // Simulation de mise à jour
            const submitBtn = passwordForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = 'Mise à jour en cours...';
            submitBtn.disabled = true;
            
            setTimeout(() => {
                alert('✅ Mot de passe mis à jour avec succès !');
                closeModal();
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 1500);
        });
    }
</script>
@endsection