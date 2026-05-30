@extends('sadmin.layouts.guest')

@section('content')
<div class="w-full max-w-[400px] flex flex-col items-center">
    <!-- Branding -->
    <div class="mb-6 text-center">
        <h1 class="text-3xl font-bold text-primary">EduManager </h1>
        <p class="text-sm text-on-surface-variant mt-1">Système de Gestion Centralisé</p>
    </div>

    <!-- Card Container -->
    <div class="w-full bg-surface-container-lowest rounded-xl shadow-[0_4px_12px_rgba(55,48,163,0.04)] border border-outline-variant p-6 md:p-8">
        <div class="mb-6 text-center">
            <h2 class="font-headline-lg text-[20px] text-on-surface mb-1">Réinitialiser le mot de passe</h2>
            <p class="font-body-sm text-[12px] text-on-surface-variant">
                Veuillez choisir un mot de passe fort pour sécuriser votre compte.
            </p>
        </div>

        <!-- Message d'erreur -->
        @if ($errors->any())
            <div class="mb-5 p-3 bg-error-container/20 border border-error rounded-lg flex items-start gap-2">
                <span class="material-symbols-outlined text-error text-[18px]">error</span>
                <div class="flex-1">
                    <p class="font-label-md text-[12px] text-error mb-1">Erreur de validation</p>
                    @foreach ($errors->all() as $error)
                        <p class="font-body-sm text-[11px] text-error/80">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Message de succès -->
        @if (session('success'))
            <div class="mb-5 p-3 bg-secondary-container/20 border border-secondary rounded-lg flex items-start gap-2">
                <span class="material-symbols-outlined text-secondary text-[18px]">check_circle</span>
                <div>
                    <p class="font-label-md text-[12px] text-on-secondary-container">Mot de passe mis à jour !</p>
                    <p class="font-body-sm text-[11px] text-on-secondary-container/80 mt-1">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('password.otp.update', ['email' => $email ?? $request->input('email') ?? session('password_reset_email') ?? '']) }}" class="space-y-5" id="resetForm">


            @csrf
            <input type="hidden" name="token" value="{{ $token ?? '' }}">
            <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

            <!-- Nouveau mot de passe -->
            <div class="space-y-1.5">
                <label class="font-label-md text-[12px] text-on-surface" for="new-password">
                    Nouveau mot de passe
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="material-symbols-outlined text-on-surface-variant text-[18px]">lock</span>
                    </div>
                    <input 
                        class="block w-full pl-10 pr-10 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-sm text-[13px] text-on-surface placeholder:text-outline focus:ring-2 focus:ring-primary-container focus:border-primary-container transition-all outline-none @error('password') border-error @enderror" 
                        id="new-password" 
                        name="password"
                        oninput="checkStrength(this.value)" 
                        placeholder="••••••••" 
                        type="password"
                        required
                    >
                    <button class="absolute inset-y-0 right-0 pr-3 flex items-center text-on-surface-variant hover:text-primary transition-colors" onclick="toggleVisibility('new-password')" type="button">
                        <span class="material-symbols-outlined text-[18px]" id="eye-icon-new">visibility</span>
                    </button>
                </div>

                <!-- Indicateur de force -->
                <div class="pt-1">
                    <div class="flex gap-1 mb-1">
                        <div class="strength-meter flex-1 bg-surface-variant rounded-full" id="meter-1"></div>
                        <div class="strength-meter flex-1 bg-surface-variant rounded-full" id="meter-2"></div>
                        <div class="strength-meter flex-1 bg-surface-variant rounded-full" id="meter-3"></div>
                    </div>
                    <p class="font-label-sm text-[10px] text-outline" id="strength-text">
                        Entrez un mot de passe
                    </p>
                </div>

                @error('password')
                    <p class="text-[11px] text-error flex items-center gap-1 mt-1">
                        <span class="material-symbols-outlined text-[14px]">error</span>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Confirmation du mot de passe -->
            <div class="space-y-1.5">
                <label class="font-label-md text-[12px] text-on-surface" for="confirm-password">
                    Confirmer le mot de passe
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="material-symbols-outlined text-on-surface-variant text-[18px]">lock_reset</span>
                    </div>
                    <input 
                        class="block w-full pl-10 pr-10 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-sm text-[13px] text-on-surface placeholder:text-outline focus:ring-2 focus:ring-primary-container focus:border-primary-container transition-all outline-none" 
                        id="confirm-password" 
                        name="password_confirmation"
                        placeholder="••••••••" 
                        type="password"
                        required
                    >
                    <button class="absolute inset-y-0 right-0 pr-3 flex items-center text-on-surface-variant hover:text-primary transition-colors" onclick="toggleVisibility('confirm-password')" type="button">
                        <span class="material-symbols-outlined text-[18px]" id="eye-icon-confirm">visibility</span>
                    </button>
                </div>
            </div>

            <!-- Bouton de soumission -->
            <button class="w-full py-2.5 px-4 bg-primary-container hover:bg-primary text-white font-label-md text-[13px] rounded-lg shadow-md transition-all duration-200 focus:ring-4 focus:ring-primary-container/20" type="submit">
                Mettre à jour le mot de passe
            </button>
        </form>

        <!-- Lien de retour -->
        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 font-label-md text-[12px] text-primary hover:text-on-primary-fixed-variant transition-colors group">
                <span class="material-symbols-outlined text-[16px] group-hover:-translate-x-1 transition-transform">arrow_back</span>
                Retour à la connexion
            </a>
        </div>
    </div>
</div>

<style>
    .strength-meter {
        height: 3px;
        transition: all 0.3s ease;
    }
</style>
@endsection

@section('scripts')
@include('auth.partials.sweet-alerts')
<script>
    function toggleVisibility(inputId) {
        const input = document.getElementById(inputId);
        const iconId = inputId === 'new-password' ? 'eye-icon-new' : 'eye-icon-confirm';
        const icon = document.getElementById(iconId);
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.textContent = 'visibility_off';
        } else {
            input.type = 'password';
            icon.textContent = 'visibility';
        }
    }

    function checkStrength(password) {
        const m1 = document.getElementById('meter-1');
        const m2 = document.getElementById('meter-2');
        const m3 = document.getElementById('meter-3');
        const text = document.getElementById('strength-text');
        
        if (password.length === 0) {
            [m1, m2, m3].forEach(m => m.className = 'strength-meter flex-1 bg-surface-variant rounded-full');
            text.textContent = 'Entrez un mot de passe';
            text.className = 'font-label-sm text-[10px] text-outline';
            return;
        }

        let score = 0;
        
        // Critères de force
        if (password.length >= 8) score++;
        if (/[A-Z]/.test(password) && /[0-9]/.test(password)) score++;
        if (/[^A-Za-z0-9]/.test(password)) score++;

        if (score === 1 || password.length <= 5) {
            m1.className = 'strength-meter flex-1 bg-alert-red rounded-full';
            m2.className = 'strength-meter flex-1 bg-surface-variant rounded-full';
            m3.className = 'strength-meter flex-1 bg-surface-variant rounded-full';
            text.textContent = 'Faible';
            text.className = 'font-label-sm text-[10px] text-alert-red';
        } else if (score === 2) {
            m1.className = 'strength-meter flex-1 bg-warning-amber rounded-full';
            m2.className = 'strength-meter flex-1 bg-warning-amber rounded-full';
            m3.className = 'strength-meter flex-1 bg-surface-variant rounded-full';
            text.textContent = 'Moyen';
            text.className = 'font-label-sm text-[10px] text-warning-amber';
        } else if (score >= 3) {
            m1.className = 'strength-meter flex-1 bg-success-green rounded-full';
            m2.className = 'strength-meter flex-1 bg-success-green rounded-full';
            m3.className = 'strength-meter flex-1 bg-success-green rounded-full';
            text.textContent = 'Fort';
            text.className = 'font-label-sm text-[10px] text-success-green';
        }
    }

    // Validation côté client avant soumission
    document.getElementById('resetForm').addEventListener('submit', function(e) {
        const password = document.getElementById('new-password').value;
        const confirm = document.getElementById('confirm-password').value;
        
        if (password !== confirm) {
            e.preventDefault();
            showAuthAlert({
                title: 'Erreur',
                text: 'Les mots de passe ne correspondent pas.',
                icon: 'error',
            });
            return false;
        }
        
        if (password.length < 8) {
            e.preventDefault();
            showAuthAlert({
                title: 'Erreur',
                text: 'Le mot de passe doit contenir au moins 8 caractères.',
                icon: 'error',
            });
            return false;
        }
        
        // Animation du bouton
        const btn = e.target.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[16px]">progress_activity</span> Chargement...';
        
        // Le formulaire sera soumis normalement après cette animation
        setTimeout(() => {}, 100);
    });
</script>
@endsection
