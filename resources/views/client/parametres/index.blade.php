@extends('client.layouts.app')

@section('content')

@php
    $client = $client ?? auth()->user();
    $successMessage = session('success');
    $errorMessage = $errors->first();
@endphp

<!-- Section 1: Bento Grid Layout for Primary Settings -->
<div class="grid grid-cols-12 gap-6">
    <!-- Account Settings (Large Bento) -->
    <section class="col-span-12 lg:col-span-8 bg-surface-container-lowest rounded-2xl p-5 card-shadow border border-surface-subtle/50">
        <div class="flex items-center gap-3 mb-5">
            <!-- Avatar avec icône user -->
            <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center border-4 border-primary/20 shadow-md overflow-hidden">
                @if(!empty($client?->image))
                    <img src="{{ asset('storage/'.$client->image) }}" alt="Photo de profil" class="w-full h-full object-cover">
                @else
                    <span class="material-symbols-outlined text-primary text-2xl">account_circle</span>
                @endif
            </div>

            <div class="flex-1">
                <h3 class="font-headline-lg text-[18px] text-on-surface">Paramètres du Compte</h3>
                <p class="text-[12px] text-text-muted mt-0.5">Gérez vos informations personnelles et votre identité publique.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-3">
                <div class="border-b border-outline-variant/30 pb-2">
                    <label class="text-[10px] text-text-muted uppercase tracking-wide">Nom complet</label>
                    <p class="text-[14px] font-semibold text-on-surface mt-1">{{ trim(($client?->nom ?? '').' '.($client?->prenom ?? '')) ?: '-' }}</p>
                </div>
                <div class="border-b border-outline-variant/30 pb-2">
                    <label class="text-[10px] text-text-muted uppercase tracking-wide">Email professionnel</label>
                    <p class="text-[14px] font-semibold text-on-surface mt-1">{{ $client?->email ?? '-' }}</p>
                </div>
                <div class="border-b border-outline-variant/30 pb-2">
                    <label class="text-[10px] text-text-muted uppercase tracking-wide">Téléphone</label>
                    <p class="text-[14px] font-semibold text-on-surface mt-1">{{ $client?->telephone ?? '-' }}</p>
                </div>
            </div>

            <div class="space-y-3">
                <div class="border-b border-outline-variant/30 pb-2">
                    <label class="text-[10px] text-text-muted uppercase tracking-wide">Rôle</label>
                    <p class="text-[14px] font-semibold text-on-surface mt-1">{{ $client?->role ?? 'client' }}</p>
                </div>

                <div class="border-b border-outline-variant/30 pb-2">
                    <label class="text-[10px] text-text-muted uppercase tracking-wide">Statut</label>
                    <p class="text-[14px] font-semibold text-on-surface mt-1">{{ $client?->statut ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <form method="POST" action="{{ route('client.parametres.update') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-5" data-sweet-alert="loading">
                @csrf
                @method('PUT')

                <div class="space-y-1.5">
                    <label class="text-[11px] text-on-surface-variant font-medium">Nom</label>
                    <input
                        name="nom"
                        class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-[13px]"
                        type="text"
                        value="{{ old('nom', $client?->nom) }}"
                        required
                    >
                    @error('nom')
                        <div class="text-[12px] text-alert-red">{{ $message }}</div>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="text-[11px] text-on-surface-variant font-medium">Prénom</label>
                    <input
                        name="prenom"
                        class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-[13px]"
                        type="text"
                        value="{{ old('prenom', $client?->prenom) }}"
                        required
                    >
                    @error('prenom')
                        <div class="text-[12px] text-alert-red">{{ $message }}</div>
                    @enderror
                </div>

                <div class="space-y-1.5 md:col-span-2">
                    <label class="text-[11px] text-on-surface-variant font-medium">Adresse email</label>
                    <input
                        name="email"
                        class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-[13px]"
                        type="email"
                        value="{{ old('email', $client?->email) }}"
                        required
                    >
                    @error('email')
                        <div class="text-[12px] text-alert-red">{{ $message }}</div>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="text-[11px] text-on-surface-variant font-medium">Téléphone</label>
                    <input
                        name="telephone"
                        class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-[13px]"
                        type="tel"
                        value="{{ old('telephone', $client?->telephone) }}"
                        placeholder="+225 00 00 00 00 00"
                    >
                    @error('telephone')
                        <div class="text-[12px] text-alert-red">{{ $message }}</div>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="text-[11px] text-on-surface-variant font-medium">Photo</label>
                    <input
                        name="photo"
                        class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-[13px]"
                        type="file"
                        accept="image/*"
                    >
                    @error('photo')
                        <div class="text-[12px] text-alert-red">{{ $message }}</div>
                    @enderror
                    <p class="text-[12px] text-on-surface-variant mt-1">Laissez vide pour conserver l’ancienne photo.</p>
                </div>

                <div class="md:col-span-2 flex justify-end pt-3">
                    <button class="px-6 py-2 bg-gradient-to-r from-primary to-primary-container text-on-primary font-label-md text-[12px] rounded-lg" type="submit">
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
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

                    <div class="mt-4">
                        <h4 class="font-headline-lg text-[14px] mb-3">Changer le mot de passe</h4>
                        <form method="POST" action="{{ route('client.parametres.password') }}" class="space-y-5" data-sweet-alert="loading">
                            @csrf
                            @method('PUT')

                            <div class="space-y-1.5">
                                <label class="text-[11px] text-on-surface-variant font-medium">Mot de passe actuel</label>
                                <div class="relative">
                                    <input
                                        name="current_password"
                                        type="password"
                                        class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-[13px] pr-10"
                                        placeholder="Entrez votre mot de passe actuel"
                                    >
                                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant cursor-pointer hover:text-primary toggle-password text-[18px]">visibility</span>
                                </div>
                                @error('current_password')
                                    <div class="text-[12px] text-alert-red">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-[11px] text-on-surface-variant font-medium">Nouveau mot de passe</label>
                                <div class="relative">
                                    <input
                                        name="password"
                                        type="password"
                                        class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-[13px] pr-10"
                                        placeholder="••••••••"
                                    >
                                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant cursor-pointer hover:text-primary toggle-password text-[18px]">visibility</span>
                                </div>
                                @error('password')
                                    <div class="text-[12px] text-alert-red">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-[11px] text-on-surface-variant font-medium">Confirmation du mot de passe</label>
                                <div class="relative">
                                    <input
                                        name="password_confirmation"
                                        type="password"
                                        class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-[13px] pr-10"
                                        placeholder="Confirmez votre nouveau mot de passe"
                                    >
                                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant cursor-pointer hover:text-primary toggle-password text-[18px]">visibility</span>
                                </div>
                                @error('password_confirmation')
                                    <div class="text-[12px] text-alert-red">{{ $message }}</div>
                                @enderror
                            </div>

                            <button class="w-full mt-2 px-3 py-2 text-primary border border-primary/30 rounded-lg hover:bg-primary/5 font-medium text-[12px] inline-flex justify-center" type="submit">
                                Mettre à jour le mot de passe
                            </button>
                        </form>
                    </div>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function showSweetAlert({ title, text = '', icon = 'success', confirmButtonText = 'Compris', confirmButtonColor = '#1f108e', showConfirmButton = true } = {}) {
        if (window.Swal && typeof window.Swal.fire === 'function') {
            return window.Swal.fire({
                title,
                text,
                icon,
                confirmButtonText,
                confirmButtonColor,
                showConfirmButton
            });
        }

        return window.alert(text ? `${title}\n${text}` : title);
    }

    window.showSweetAlert = showSweetAlert;

    const successMessage = @json($successMessage);
    const errorMessage = @json($errorMessage);

    if (successMessage) {
        showSweetAlert({
            title: 'Succès',
            text: successMessage,
            icon: 'success',
            confirmButtonText: 'Compris',
            confirmButtonColor: '#1f108e'
        });
    }

    if (!successMessage && errorMessage) {
        showSweetAlert({
            title: 'Oups',
            text: errorMessage,
            icon: 'error',
            confirmButtonText: 'Réessayer',
            confirmButtonColor: '#E11D48'
        });
    }

    document.querySelectorAll('.toggle-password').forEach(icon => {
        icon.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
            if (!input) return;

            if (input.type === 'password') {
                input.type = 'text';
                this.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                this.textContent = 'visibility';
            }
        });
    });

    document.querySelectorAll('form[data-sweet-alert="loading"]').forEach(form => {
        form.addEventListener('submit', function (event) {
            if (this.dataset.confirmed === 'true') return;

            event.preventDefault();
            const isPasswordUpdate = this.action.includes('/password');
            const photoInput = this.querySelector('input[name="photo"]');
            const isPhotoUpdate = photoInput && photoInput.files.length > 0;
            const title = isPasswordUpdate ? 'Changer le mot de passe ?' : (isPhotoUpdate ? 'Modifier la photo et le profil ?' : 'Modifier les informations du profil ?');
            const text = isPasswordUpdate ? 'Votre nouveau mot de passe remplacera immédiatement l’ancien.' : 'Les nouvelles informations seront enregistrées.';
            const submit = () => {
                this.dataset.confirmed = 'true';
                this.requestSubmit();
            };

            if (!window.Swal || typeof window.Swal.fire !== 'function') {
                submit();
                return;
            }

            window.Swal.fire({
                title,
                text,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Oui, enregistrer',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#1f108e',
                cancelButtonColor: '#64748B'
            }).then(result => {
                if (result.isConfirmed) submit();
            });
        });

        form.addEventListener('submit', function () {
            if (this.dataset.confirmed !== 'true') return;
            const submitButton = this.querySelector('button[type="submit"]');

            if (submitButton && !submitButton.dataset.sweetAlertDisabled) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="inline-flex items-center gap-2"><span class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>En cours...</span>';
            }

            if (window.Swal && typeof window.Swal.fire === 'function' && this.dataset.sweetAlert === 'loading') {
                window.Swal.fire({
                    title: 'Traitement en cours',
                    text: 'Veuillez patienter pendant la mise à jour.',
                    allowOutsideClick: false,
                    didOpen: () => window.Swal.showLoading(),
                    showConfirmButton: false
                });
            }
        });
    });
</script>
@endsection

