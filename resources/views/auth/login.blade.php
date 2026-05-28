<!DOCTYPE html>
<html class="light" lang="fr">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>EduManager Pro | Connexion SADMIN</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Lexend:wght@600;700;800&display=swap" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#1f108e",
                        secondary: "#006a61",
                        surface: "#f9f9ff",
                        "surface-container-lowest": "#ffffff",
                        "outline-variant": "#c8c4d5",
                        "on-surface": "#111c2d",
                        "on-surface-variant": "#464553",
                        "primary-container": "#3730a3",
                        "on-primary-container": "#a9a7ff",
                        error: "#ba1a1a",
                        "error-container": "#ffdad6",
                        "on-error-container": "#93000a",
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0,
                'wght' 400,
                'GRAD' 0,
                'opsz' 24;
        }

        .bg-pattern {
            background-color: #f9f9ff;
            background-image:
                radial-gradient(#3730a3 0.5px, transparent 0.5px),
                radial-gradient(#3730a3 0.5px, #f9f9ff 0.5px);

            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
            opacity: 0.05;
        }

        .login-card-shadow {
            box-shadow:
                0 4px 12px rgba(55, 48, 163, 0.04),
                0 8px 24px rgba(55, 48, 163, 0.08);
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin-custom {
            animation: spin 1s linear infinite;
        }
    </style>
</head>

<body class="bg-surface min-h-screen flex items-center justify-center relative overflow-hidden p-4">

    <!-- Background -->
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="bg-pattern absolute inset-0"></div>

        <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary/5 rounded-full blur-3xl"></div>

        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-secondary/5 rounded-full blur-3xl"></div>
    </div>

    <!-- Main -->
    <main class="relative z-10 w-full max-w-[440px]">

        <!-- Logo -->
        <div class="text-center mb-8">

            <h1 class="text-3xl font-bold text-primary">
                EduManager Admin
            </h1>

            <p class="text-sm text-on-surface-variant mt-1">
                Système de Gestion Centralisé
            </p>
        </div>

        <!-- Card -->
        <div class="bg-surface-container-lowest login-card-shadow rounded-xl border border-outline-variant p-8 md:p-10">

            <!-- Session Status -->
            <x-auth-session-status
                class="mb-4"
                :status="session('status')" />

            <!-- Errors -->
            @if ($errors->any())
                <div class="mb-6 flex items-center gap-3 p-4 bg-error-container text-on-error-container rounded-lg border border-error/20">

                    <span class="material-symbols-outlined text-[20px]">
                        report
                    </span>

                    <p class="text-sm">
                        {{ $errors->first() }}
                    </p>

                </div>
            @endif

            <!-- Form -->
            <form
                method="POST"
                action="{{ route('login') }}"
                class="space-y-6"
                id="login-form">

                @csrf

                <!-- Email -->
                <div class="space-y-2">

                    <label class="text-sm font-medium text-on-surface-variant">
                        Adresse Email
                    </label>

                    <div class="relative">

                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">

                            <span class="material-symbols-outlined text-[20px]">
                                mail
                            </span>

                        </div>

                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="admin@educore.edu"
                            class="block w-full pl-10 pr-4 py-3 border border-outline-variant rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">

                    </div>

                    <x-input-error :messages="$errors->get('email')" class="mt-2" />

                </div>

                <!-- Password -->
                <div class="space-y-2">

                    <div class="flex justify-between items-center">

                        <label class="text-sm font-medium text-on-surface-variant">
                            Mot de passe
                        </label>

                        @if (Route::has('password.request'))
                            <a
                                href="{{ route('password.request') }}"
                                class="text-sm text-primary hover:underline">

                                Mot de passe oublié ?

                            </a>
                        @endif

                    </div>

                    <div class="relative">

                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">

                            <span class="material-symbols-outlined text-[20px]">
                                lock
                            </span>

                        </div>

                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="block w-full pl-10 pr-12 py-3 border border-outline-variant rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">

                        <button
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-primary"
                            id="toggle-password"
                            type="button">

                            <span
                                class="material-symbols-outlined text-[20px]"
                                id="eye-icon">

                                visibility

                            </span>

                        </button>

                    </div>

                    <x-input-error :messages="$errors->get('password')" class="mt-2" />

                </div>

                <!-- Remember -->
                <div class="flex items-center">

                    <input
                        id="remember_me"
                        type="checkbox"
                        name="remember"
                        class="rounded border-gray-300 text-primary shadow-sm focus:ring-primary">

                    <label for="remember_me" class="ml-2 text-sm text-gray-600">
                        Se souvenir de moi
                    </label>

                </div>

                <!-- Submit -->
                <button
                    class="w-full bg-primary text-white py-4 rounded-lg shadow-sm hover:opacity-90 transition-all flex items-center justify-center gap-2"
                    id="submit-btn"
                    type="submit">

                    <span id="btn-text">
                        Se connecter
                    </span>

                    <span class="hidden" id="btn-spinner">

                        <span class="material-symbols-outlined animate-spin-custom">
                            progress_activity
                        </span>

                    </span>

                </button>

            </form>

        </div>

    </main>

    <script>
        // Password Toggle
        const toggleBtn = document.getElementById('toggle-password');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');

        toggleBtn.addEventListener('click', () => {

            const isPassword =
                passwordInput.getAttribute('type') === 'password';

            passwordInput.setAttribute(
                'type',
                isPassword ? 'text' : 'password'
            );

            eyeIcon.textContent =
                isPassword ? 'visibility_off' : 'visibility';
        });

        // Loading Animation
        const loginForm = document.getElementById('login-form');

        const submitBtn = document.getElementById('submit-btn');

        const btnText = document.getElementById('btn-text');

        const btnSpinner = document.getElementById('btn-spinner');

        loginForm.addEventListener('submit', () => {

            submitBtn.disabled = true;

            btnText.textContent = 'Authentification...';

            btnSpinner.classList.remove('hidden');
        });
    </script>

</body>

</html>