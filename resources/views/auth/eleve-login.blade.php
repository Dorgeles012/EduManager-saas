@extends('sadmin.layouts.guest')

@section('content')
<main class="relative z-10 w-full max-w-[440px]">
    <!-- Logo Section -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-primary">EduManager</h1>
        <p class="text-sm text-on-surface-variant mt-1">Espace Élève</p>
    </div>

    <!-- Login Card -->
    <div class="bg-surface-container-lowest login-card-shadow rounded-xl border border-outline-variant p-8 md:p-10">
        <form method="POST" action="{{ route('eleve.login') }}" class="space-y-6" id="login-form">
            @csrf

            <!-- Matricule Field -->
            <div class="space-y-2">
                <label class="text-sm font-medium text-on-surface-variant">Matricule</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <span class="material-symbols-outlined text-[20px]">badge</span>
                    </div>
                    <input id="matricule" type="text" name="matricule" value="{{ old('matricule') }}" required autofocus autocomplete="username" placeholder="Votre matricule" class="block w-full pl-10 pr-4 py-3 border border-outline-variant rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>
                <x-input-error :messages="$errors->get('matricule')" class="mt-2" />
            </div>

            <!-- Password Field -->
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <label class="text-sm font-medium text-on-surface-variant">Mot de passe</label>
                    <a href="{{ route('login') }}" class="text-sm text-primary hover:underline">Connexion admin</a>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <span class="material-symbols-outlined text-[20px]">lock</span>
                    </div>
                    <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" class="block w-full pl-10 pr-12 py-3 border border-outline-variant rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <button class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-primary" id="toggle-password" type="button">
                        <span class="material-symbols-outlined text-[20px]" id="eye-icon">visibility</span>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Submit Button -->
            <button class="w-full bg-primary text-white py-4 rounded-lg shadow-sm hover:opacity-90 transition-all flex items-center justify-center gap-2" type="submit">
                <span class="material-symbols-outlined text-[20px]">school</span>
                Se connecter
            </button>
        </form>
    </div>
</main>
@endsection

@section('scripts')
<script>
    (function() {
        'use strict';
        const toggleBtn = document.getElementById('toggle-password');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');

        if (toggleBtn && passwordInput && eyeIcon) {
            toggleBtn.addEventListener('click', () => {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                eyeIcon.textContent = isPassword ? 'visibility_off' : 'visibility';
            });
        }
    })();
</script>
@endsection
