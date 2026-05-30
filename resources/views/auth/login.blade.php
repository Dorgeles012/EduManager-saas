@extends('sadmin.layouts.guest')

@section('content')
<main class="relative z-10 w-full max-w-[440px]">
    <!-- Logo Section -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-primary">EduManager</h1>
        <p class="text-sm text-on-surface-variant mt-1">Système de Gestion Centralisé</p>
    </div>

    <!-- Login Card -->
    <div class="bg-surface-container-lowest login-card-shadow rounded-xl border border-outline-variant p-8 md:p-10">
        <form method="POST" action="{{ route('login') }}" class="space-y-6" id="login-form">
            @csrf

            <!-- Email Field -->
            <div class="space-y-2">
                <label class="text-sm font-medium text-on-surface-variant">Adresse Email</label>
                
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <span class="material-symbols-outlined text-[20px]">mail</span>
                    </div>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="admin@educore.edu" class="block w-full pl-10 pr-4 py-3 border border-outline-variant rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>

                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password Field -->
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <label class="text-sm font-medium text-on-surface-variant">Mot de passe</label>
                    
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-primary hover:underline">Mot de passe oublié ?</a>
                    @endif
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

            <!-- Remember Me -->
            <div class="flex items-center">
                <input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-300 text-primary shadow-sm focus:ring-primary">
                <label for="remember_me" class="ml-2 text-sm text-gray-600">Se souvenir de moi</label>
            </div>

            <!-- Submit Button -->
            <button class="w-full bg-primary text-white py-4 rounded-lg shadow-sm hover:opacity-90 transition-all flex items-center justify-center gap-2" id="submit-btn" type="submit">
                <span id="btn-text">Se connecter</span>
                <span class="hidden" id="btn-spinner">
                    <span class="material-symbols-outlined animate-spin-custom">progress_activity</span>
                </span>
            </button>
        </form>
    </div>
</main>
@endsection

@section('scripts')
@include('auth.partials.sweet-alerts')

<script>
    (function() {
        'use strict';

        // DOM Elements
        const elements = {
            toggleBtn: document.getElementById('toggle-password'),
            passwordInput: document.getElementById('password'),
            eyeIcon: document.getElementById('eye-icon'),
            loginForm: document.getElementById('login-form'),
            submitBtn: document.getElementById('submit-btn'),
            btnText: document.getElementById('btn-text'),
            btnSpinner: document.getElementById('btn-spinner')
        };

        // Toggle Password Visibility
        function initPasswordToggle() {
            if (!elements.toggleBtn || !elements.passwordInput || !elements.eyeIcon) return;

            elements.toggleBtn.addEventListener('click', () => {
                const isPassword = elements.passwordInput.type === 'password';
                elements.passwordInput.type = isPassword ? 'text' : 'password';
                elements.eyeIcon.textContent = isPassword ? 'visibility_off' : 'visibility';
            });
        }

        // Form Submission Loading State
        function initFormLoading() {
            if (!elements.loginForm || !elements.submitBtn || !elements.btnText || !elements.btnSpinner) return;

            elements.loginForm.addEventListener('submit', () => {
                elements.submitBtn.disabled = true;
                elements.btnText.textContent = 'Authentification...';
                elements.btnSpinner.classList.remove('hidden');
            });
        }

        // Initialize all functions
        function init() {
            initPasswordToggle();
            initFormLoading();
        }

        // Run when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
</script>
@endsection