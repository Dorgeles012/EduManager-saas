@extends('sadmin.layouts.app')

@section('content')

@php
    $user = auth()->user();

    // photo: stockée en disk public sous le chemin relatif (ex: profile-images/xxx.jpg)
    $avatarUrl = $user?->image
        ? (Storage::disk('public')->exists($user->image) ? Storage::url($user->image) : null)
        : null;
@endphp

<!-- Section 1: Bento Grid Layout for Primary Settings -->
<div class="grid grid-cols-12 gap-6">
    <!-- Account Settings (Large Bento) -->
    <section class="col-span-12 lg:col-span-8 bg-surface-container-lowest rounded-2xl p-5 card-shadow border border-surface-subtle/50">
        <div class="flex items-center gap-3 mb-5">
            <!-- Avatar avec icône user -->
            <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center border-4 border-primary/20 shadow-md">
                <span class="material-symbols-outlined text-primary text-2xl">account_circle</span>
            </div>

            <div class="flex-1">
                <h3 class="font-headline-lg text-[18px] text-on-surface">Paramètres du Compte</h3>
                <p class="text-[12px] text-text-muted mt-0.5">Gérez vos informations personnelles et votre identité publique.</p>
            </div>

            <a href="{{ route('sadmin.compte') }}" class="px-3 py-1.5 text-primary hover:bg-primary/5 rounded-lg text-[12px] font-medium inline-flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">edit</span>
                Modifier
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-3">
                <div class="border-b border-outline-variant/30 pb-2">
                    <label class="text-[10px] text-text-muted uppercase tracking-wide">Nom complet</label>
                    <p class="text-[14px] font-semibold text-on-surface mt-1">{{ trim(($user?->nom ?? '').' '.($user?->prenom ?? '')) ?: '-' }}</p>
                </div>
                <div class="border-b border-outline-variant/30 pb-2">
                    <label class="text-[10px] text-text-muted uppercase tracking-wide">Email professionnel</label>
                    <p class="text-[14px] font-semibold text-on-surface mt-1">{{ $user?->email ?? '-' }}</p>
                </div>
                <div class="border-b border-outline-variant/30 pb-2">
                    <label class="text-[10px] text-text-muted uppercase tracking-wide">Téléphone</label>
                    <p class="text-[14px] font-semibold text-on-surface mt-1">{{ $user?->telephone ?? '-' }}</p>
                </div>
            </div>

            <div class="space-y-3">
                <div class="border-b border-outline-variant/30 pb-2">
                    <label class="text-[10px] text-text-muted uppercase tracking-wide">Rôle</label>
                    <p class="text-[14px] font-semibold text-on-surface mt-1">{{ $user?->role ?? '-' }}</p>
                </div>

                <div class="border-b border-outline-variant/30 pb-2">
                    <label class="text-[10px] text-text-muted uppercase tracking-wide">Statut</label>
                    <p class="text-[14px] font-semibold text-on-surface mt-1">{{ $user?->statut ?? '-' }}</p>
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
                <a href="{{ route('sadmin.passwordchange') }}" class="w-full mt-3 px-3 py-2 text-primary border border-primary/30 rounded-lg hover:bg-primary/5 font-medium text-[12px] inline-flex justify-center">
                        Changer le mot de passe
                    </a>
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
    // Note: sur cette page Paramètres, le modal ne doit pas changer le mot de passe.
    // On garde l'UI (ouverture/fermeture + validation locale), mais pas de soumission / pas de simulation.
    const changePasswordBtn = document.getElementById('changePasswordBtn');
    const passwordModal = document.getElementById('passwordModal');
    const closePasswordModal = document.getElementById('closePasswordModal');
    const cancelPasswordBtn = document.getElementById('cancelPasswordBtn');
    const passwordForm = document.getElementById('passwordForm');

    // Ouvrir le modal
    if (changePasswordBtn) {
        changePasswordBtn.addEventListener('click', () => {
            // Tailwind: initialement hidden -> on retire hidden et on force display flex
            passwordModal.classList.remove('hidden');
            passwordModal.classList.add('flex');
            passwordModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        });
    }


    // Fermer le modal
    const closeModal = () => {
        passwordModal.style.display = 'none';
        document.body.style.overflow = '';
        if (passwordForm) passwordForm.reset();
    };

    if (closePasswordModal) closePasswordModal.addEventListener('click', closeModal);
    if (cancelPasswordBtn) cancelPasswordBtn.addEventListener('click', closeModal);

    // Cliquer en dehors pour fermer
    window.addEventListener('click', (e) => {
        if (e.target === passwordModal) closeModal();
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

    // Activer la soumission (Laravel handle la mise à jour)
</script>
@endsection