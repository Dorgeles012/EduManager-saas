<!DOCTYPE html>
<html class="light" lang="fr">
<head>
    @php
        $teachers = $teachers ?? collect();
    @endphp
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/edumanager-logo.svg') }}">
    <title>@yield('title', 'EduManager  Tableau de bord')</title>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Lexend:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary-fixed-dim": "#c3c0ff",
                        "on-background": "#111c2d",
                        "inverse-on-surface": "#ecf1ff",
                        "error-container": "#ffdad6",
                        "tertiary": "#2a2d2f",
                        "on-primary": "#ffffff",
                        "inverse-surface": "#263143",
                        "background": "#f9f9ff",
                        "secondary-fixed-dim": "#6bd8cb",
                        "primary-fixed": "#e2dfff",
                        "surface-subtle": "#F1F5F9",
                        "surface-container-high": "#dee8ff",
                        "on-tertiary-fixed": "#191c1e",
                        "tertiary-container": "#404345",
                        "surface-container": "#e7eeff",
                        "primary": "#1f108e",
                        "outline": "#777584",
                        "surface-container-lowest": "#ffffff",
                        "on-tertiary-container": "#adb0b2",
                        "surface-variant": "#d8e3fb",
                        "tertiary-fixed-dim": "#c4c7c9",
                        "primary-container": "#3730a3",
                        "surface": "#f9f9ff",
                        "surface-container-highest": "#d8e3fb",
                        "surface-tint": "#544fc0",
                        "outline-variant": "#c8c4d5",
                        "on-error": "#ffffff",
                        "surface-container-low": "#f0f3ff",
                        "warning-amber": "#D97706",
                        "on-secondary-fixed-variant": "#005049",
                        "surface-dim": "#cfdaf2",
                        "success-green": "#059669",
                        "on-secondary-fixed": "#00201d",
                        "secondary": "#006a61",
                        "on-primary-container": "#a9a7ff",
                        "text-muted": "#64748B",
                        "secondary-fixed": "#89f5e7",
                        "tertiary-fixed": "#e0e3e5",
                        "secondary-container": "#86f2e4",
                        "surface-bright": "#f9f9ff",
                        "inverse-primary": "#c3c0ff",
                        "on-tertiary-fixed-variant": "#444749",
                        "on-primary-fixed": "#0f0069",
                        "on-error-container": "#93000a",
                        "error": "#ba1a1a",
                        "on-surface": "#111c2d",
                        "on-primary-fixed-variant": "#3b35a7",
                        "alert-red": "#E11D48",
                        "on-secondary-container": "#006f66",
                        "on-secondary": "#ffffff",
                        "on-surface-variant": "#464553",
                        "on-tertiary": "#ffffff"
                    },
                    borderRadius: { DEFAULT: "0.25rem", lg: "0.5rem", xl: "12px", full: "9999px" },
                    spacing: {
                        "gutter-desktop": "24px",
                        "max-width": "1440px",
                        "margin-mobile": "16px",
                        "gutter-mobile": "16px",
                        "margin-desktop": "32px",
                        "base-unit": "4px"
                    },
                    fontFamily: {
                        "headline-lg": ["Lexend"],
                        "headline-md": ["Lexend"],
                        "body-md": ["Inter"],
                        "body-sm": ["Inter"],
                        "label-md": ["Inter"],
                        "headline-xl": ["Lexend"],
                        "label-sm": ["Inter"],
                        "body-lg": ["Inter"]
                    },
                    fontSize: {
                        "headline-lg": ["28px", { lineHeight: "36px", letterSpacing: "-0.01em", fontWeight: "600" }],
                        "headline-md": ["20px", { lineHeight: "28px", fontWeight: "600" }],
                        "body-md": ["16px", { lineHeight: "24px", fontWeight: "400" }],
                        "body-sm": ["14px", { lineHeight: "20px", fontWeight: "400" }],
                        "label-md": ["14px", { lineHeight: "16px", letterSpacing: "0.01em", fontWeight: "600" }],
                        "headline-xl": ["36px", { lineHeight: "44px", letterSpacing: "-0.02em", fontWeight: "700" }],
                        "label-sm": ["12px", { lineHeight: "16px", fontWeight: "500" }],
                        "body-lg": ["18px", { lineHeight: "28px", fontWeight: "400" }]
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #F8FAFC;
            color: #111c2d;
            -webkit-font-smoothing: antialiased;
            font-family: 'Inter', sans-serif;
            font-size: 16px;
        }

        .ambient-shadow { box-shadow: 0 4px 12px 0 rgba(55, 48, 163, 0.04); }
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(226, 232, 240, 1);
        }

        /* ============ ICÔNES MATERIAL — plus grandes ============ */
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            font-size: 30px;
            line-height: 1;
            vertical-align: middle;
        }

        /* Icônes de la sidebar alignées sur celles du portail sadmin */
        .sidebar-nav .material-symbols-outlined {
            font-size: 24px;
        }

        /* Sidebar scrollbar */
        .sidebar-nav {
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #c8c4d5 transparent;
            flex: 1;
        }
        .sidebar-nav::-webkit-scrollbar { width: 5px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: #c8c4d5; border-radius: 10px; }
        .sidebar-nav::-webkit-scrollbar-thumb:hover { background: #a09eb0; }

        /* Fixed navbar */
        .navbar-fixed {
            position: fixed;
            top: 0;
            right: 0;
            left: 250px; 
            z-index: 40;
            background-color: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            height: 76px;
        }
        .main-content-with-fixed-nav { margin-top: 76px; }

        @media (max-width: 768px) {
            .navbar-fixed { left: 0; }
        }

        /* ============ SIDEBAR STYLES ============ */
        .sidebar-nav a.nav-active {
            background: linear-gradient(to right, rgba(31, 16, 142, 0.08), rgba(31, 16, 142, 0.03)) !important;
            border-right: 4px solid #1f108e !important;
            color: #1f108e !important;
            font-weight: 600 !important;
            border-radius: 0 12px 12px 0 !important;
            margin-right: 8px !important;
            transition: all 0.3s ease !important;
        }
        .sidebar-nav a.nav-active span.material-symbols-outlined {
            color: #1f108e !important;
            font-variation-settings: 'FILL' 1, 'wght' 500 !important;
        }
        .sidebar-nav a.nav-active span:not(.material-symbols-outlined) {
            color: #1f108e !important;
            font-weight: 600 !important;
        }
        .sidebar-nav a.nav-active span.material-symbols-outlined.ml-auto {
            color: #1f108e !important;
            font-variation-settings: 'FILL' 1, 'wght' 400 !important;
        }

        .sidebar-nav a {
            position: relative;
            padding-left: 24px !important;
            padding-right: 24px !important;
            color: #464553;
            transition: all 0.25s ease;
            border-radius: 0 12px 12px 0;
            margin-right: 4px;
        }
        .sidebar-nav a:hover:not(.pointer-events-none) {
            background-color: rgba(31, 16, 142, 0.06);
            transform: translateX(4px);
        }
        .sidebar-nav a:active { transform: scale(0.97); }
        .sidebar-nav a span:not(.material-symbols-outlined) {
            font-size: 14px !important;
            line-height: 16px !important;
            font-weight: 600 !important;
        }
        .sidebar-nav a.pointer-events-none {
            opacity: 0.5 !important;
            cursor: not-allowed !important;
        }
        .sidebar-nav a.pointer-events-none:hover {
            transform: none !important;
            background-color: transparent !important;
        }
        /* ============ END SIDEBAR STYLES ============ */

        /* SweetAlert */
        .swal2-popup { font-size: 1.0625rem !important; border-radius: 16px !important; }
        .swal2-title { font-size: 1.4rem !important; }
        .swal2-html-container { font-size: 1rem !important; }
        .swal2-confirm,
        .swal2-cancel,
        .edu-swal-confirm,
        .edu-swal-cancel {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 92px !important;
            min-height: 38px !important;
            border: 0 !important;
            border-radius: 0.5rem !important;
            padding: 0.55rem 1rem !important;
            font-size: 0.9375rem !important;
            font-weight: 700 !important;
            line-height: 1.2 !important;
            color: #ffffff !important;
            text-decoration: none !important;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.12) !important;
            transition: transform 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease !important;
        }
        .swal2-confirm:hover,
        .swal2-cancel:hover,
        .edu-swal-confirm:hover,
        .edu-swal-cancel:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.16) !important;
        }
        .swal2-confirm:focus-visible,
        .swal2-cancel:focus-visible,
        .edu-swal-confirm:focus-visible,
        .edu-swal-cancel:focus-visible {
            outline: 3px solid rgba(13, 110, 253, 0.28) !important;
            outline-offset: 2px !important;
        }
        .swal2-confirm,
        .edu-swal-confirm,
        .edu-swal-confirm-primary { background-color: #0d6efd !important; }
        .edu-swal-confirm-success { background-color: #198754 !important; }
        .edu-swal-confirm-danger { background-color: #dc3545 !important; }
        .edu-swal-confirm-warning { background-color: #d97706 !important; }
        .swal2-cancel,
        .edu-swal-cancel { background-color: #6c757d !important; }
        .swal2-actions { gap: 0.65rem !important; flex-wrap: wrap !important; }
        @media (max-width: 480px) {
            .swal2-actions { width: 100% !important; }
            .swal2-confirm,
            .swal2-cancel,
            .edu-swal-confirm,
            .edu-swal-cancel { flex: 1 1 100% !important; }
        }
    </style>

    <script>
        (() => {
            if (!window.Swal || window.Swal.__eduClientThemeApplied) return;

            const originalFire = window.Swal.fire.bind(window.Swal);
            const addClass = (current, value) => [current, value].filter(Boolean).join(' ').trim();
            const actionClassFor = (options) => {
                const text = [
                    options.confirmButtonText,
                    options.title,
                    options.icon
                ].filter(Boolean).join(' ').toLowerCase();

                if (/(supprimer|retirer|delete|destruct)/i.test(text)) return 'edu-swal-confirm-danger';
                if (/(bloquer|désactiver|desactiver|suspendre)/i.test(text)) return 'edu-swal-confirm-warning';
                if (/(enregistrer|valider|débloquer|debloquer|ajouter|mettre à jour|mis à jour|créé|cree)/i.test(text)) return 'edu-swal-confirm-success';
                return 'edu-swal-confirm-primary';
            };

            const normalizeOptions = (args) => {
                if (!args.length || typeof args[0] !== 'object' || args[0] === null || Array.isArray(args[0])) {
                    return args;
                }

                const options = { ...args[0] };
                const customClass = typeof options.customClass === 'object' && options.customClass !== null
                    ? { ...options.customClass }
                    : { popup: typeof options.customClass === 'string' ? options.customClass : '' };

                customClass.popup = addClass(customClass.popup, 'rounded-2xl');
                customClass.confirmButton = addClass(addClass(customClass.confirmButton, 'edu-swal-confirm'), actionClassFor(options));
                customClass.cancelButton = addClass(customClass.cancelButton, 'edu-swal-cancel');
                customClass.title = addClass(customClass.title, 'text-base font-semibold');
                customClass.htmlContainer = addClass(customClass.htmlContainer, 'text-sm text-gray-500');

                options.customClass = customClass;
                options.buttonsStyling = false;
                options.reverseButtons = options.reverseButtons ?? true;
                options.confirmButtonColor = options.confirmButtonColor ?? '#0d6efd';
                options.cancelButtonColor = options.cancelButtonColor ?? '#6c757d';

                return [options, ...args.slice(1)];
            };

            window.Swal.fire = (...args) => originalFire(...normalizeOptions(args));
            window.Swal.__eduClientThemeApplied = true;
        })();
    </script>

    @include('partials.compact-styles')
    <style>
        /* Client typography: overrides the shared compact stylesheet only in this portal. */
        body {
            font-size: 17px !important;
            line-height: 1.55 !important;
        }

        .text-4xl { font-size: 1.875rem !important; line-height: 2.35rem !important; }
        .text-3xl { font-size: 1.625rem !important; line-height: 2.1rem !important; }
        .text-2xl { font-size: 1.375rem !important; line-height: 1.85rem !important; }
        .text-xl  { font-size: 1.1875rem !important; line-height: 1.65rem !important; }
        .text-lg  { font-size: 1.0625rem !important; line-height: 1.5rem !important; }
        .text-base{ font-size: 1rem !important; line-height: 1.45rem !important; }
        .text-sm  { font-size: .9375rem !important; line-height: 1.35rem !important; }
        .text-xs  { font-size: .8125rem !important; line-height: 1.2rem !important; }

        .text-\[10px\] { font-size: .75rem !important; line-height: 1.05rem !important; }
        .text-\[11px\] { font-size: .78125rem !important; line-height: 1.1rem !important; }
        .text-\[12px\] { font-size: .8125rem !important; line-height: 1.2rem !important; }
        .text-\[13px\] { font-size: .875rem !important; line-height: 1.25rem !important; }
        .text-\[14px\] { font-size: .9375rem !important; line-height: 1.35rem !important; }
        .text-\[15px\] { font-size: 1rem !important; line-height: 1.45rem !important; }
        .text-\[16px\] { font-size: 1.0625rem !important; line-height: 1.5rem !important; }
        .text-\[18px\] { font-size: 1.125rem !important; line-height: 1.6rem !important; }
        .text-\[20px\] { font-size: 1.25rem !important; line-height: 1.75rem !important; }
        .text-\[24px\] { font-size: 1.5rem !important; line-height: 2rem !important; }

        .text-headline-xl { font-size: 2rem !important; line-height: 2.5rem !important; }
        .text-headline-lg { font-size: 1.625rem !important; line-height: 2.1rem !important; }
        .text-headline-md { font-size: 1.25rem !important; line-height: 1.75rem !important; }
        .text-headline-lg-mobile { font-size: 1.375rem !important; line-height: 1.85rem !important; }
        .text-body-lg { font-size: 1.0625rem !important; line-height: 1.55rem !important; }
        .text-body-md { font-size: 1rem !important; line-height: 1.45rem !important; }
        .text-body-sm { font-size: .9375rem !important; line-height: 1.35rem !important; }
        .text-label-md { font-size: .875rem !important; line-height: 1.2rem !important; }
        .text-label-sm { font-size: .8125rem !important; line-height: 1.1rem !important; }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        input[type="tel"],
        input[type="date"],
        input[type="time"],
        input[type="search"],
        input[type="url"],
        select,
        textarea {
            font-size: .9375rem !important;
            line-height: 1.35rem !important;
        }

        button,
        .btn {
            font-size: .875rem !important;
            line-height: 1.25rem !important;
        }

        table {
            font-size: .9375rem !important;
        }
        table th {
            font-size: .875rem !important;
        }
        table td {
            font-size: .9375rem !important;
        }

        .swal2-popup {
            font-size: 1.125rem !important;
        }
        .swal2-title {
            font-size: 1.625rem !important;
            line-height: 2rem !important;
        }
        .swal2-html-container {
            font-size: 1.0625rem !important;
            line-height: 1.55rem !important;
        }
        .swal2-confirm,
        .swal2-cancel,
        .edu-swal-confirm,
        .edu-swal-cancel {
            min-height: 44px !important;
            padding: 0.7rem 1.15rem !important;
            font-size: 1rem !important;
            line-height: 1.35rem !important;
        }
    </style>
    @stack('styles')
</head>
<body class="font-body-md text-body-md overflow-x-hidden">

{{-- ============ SIDEBAR ============ --}}
<aside class="fixed left-0 top-0 h-screen w-[260px] bg-surface-container-lowest flex flex-col border-r border-outline-variant z-50">
     <div class="p-6 flex-shrink-0 flex items-center gap-3">
            <img src="{{ asset('images/edumanager-logo.svg') }}" alt="EduManager" class="w-10 h-10 object-contain">
            <div>
                <h1 class="font-headline-lg text-headline-md font-bold text-primary tracking-tight">EduManager</h1>
                <p class="text-xs text-gray-400 tracking-wider font-semibold">Espace client</p>
            </div>
        </div>

    <nav class="sidebar-nav py-3">
        @php
            $currentRoute = request()->route()->getName();
            $subscriptionActive = app(\App\Services\SubscriptionStatusService::class)
                ->isActiveForUser(auth()->user());
            $isLocked = ! $subscriptionActive;
        @endphp

        <a class="flex items-center gap-3 px-4 py-3 {{ $isLocked ? 'text-gray-400 opacity-50 cursor-not-allowed pointer-events-none' : 'text-gray-600 hover:bg-gray-50' }} transition-colors {{ $currentRoute === 'client.dashboard' ? 'nav-active' : '' }}" href="{{ route('client.dashboard') }}">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="text-lg font-semibold">Dashboard</span>
        </a>

        <a class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 transition-colors {{ str_starts_with($currentRoute, 'client.abonnement') ? 'nav-active' : '' }}" href="{{ route('client.abonnement.index') }}">
            <span class="material-symbols-outlined">subscriptions</span>
            <span class="text-lg font-semibold">Abonnements</span>
        </a>

        <a class="flex items-center gap-3 px-4 py-3 {{ $isLocked ? 'text-gray-400 opacity-50 cursor-not-allowed pointer-events-none' : 'text-gray-600 hover:bg-gray-50' }} transition-colors {{ str_starts_with($currentRoute, 'client.annee') ? 'nav-active' : '' }}" href="{{ route('client.annee.index') }}">
            <span class="material-symbols-outlined">calendar_today</span>
            <span class="text-lg font-semibold">Année académique</span>
            @if($isLocked)<span class="material-symbols-outlined ml-auto">lock</span>@endif
        </a>

        <a class="flex items-center gap-3 px-4 py-3 {{ $isLocked ? 'text-gray-400 opacity-50 cursor-not-allowed pointer-events-none' : 'text-gray-600 hover:bg-gray-50' }} transition-colors {{ str_starts_with($currentRoute, 'client.personnel') ? 'nav-active' : '' }}" href="{{ route('client.personnel.index') }}">
            <span class="material-symbols-outlined">badge</span>
            <span class="text-lg font-semibold">Personnel</span>
            @if($isLocked)<span class="material-symbols-outlined ml-auto">lock</span>@endif
        </a>

        <a class="flex items-center gap-3 px-4 py-3 {{ $isLocked ? 'text-gray-400 opacity-50 cursor-not-allowed pointer-events-none' : 'text-gray-600 hover:bg-gray-50' }} transition-colors {{ request()->routeIs('client.series.*') ? 'nav-active' : '' }}" href="{{ route('client.series.index') }}">
            <span class="material-symbols-outlined">category</span>
            <span class="text-lg font-semibold">Séries</span>
            @if($isLocked)<span class="material-symbols-outlined ml-auto">lock</span>@endif
        </a>

        <a class="flex items-center gap-3 px-4 py-3 {{ $isLocked ? 'text-gray-400 opacity-50 cursor-not-allowed pointer-events-none' : 'text-gray-600 hover:bg-gray-50' }} transition-colors {{ $currentRoute === 'client.niveaux' || str_starts_with($currentRoute, 'client.niveaux') ? 'nav-active' : '' }}" href="{{ route('client.niveaux') }}">
            <span class="material-symbols-outlined">leaderboard</span>
            <span class="text-lg font-semibold">Niveau</span>
            @if($isLocked)<span class="material-symbols-outlined ml-auto">lock</span>@endif
        </a>

        <a class="flex items-center gap-3 px-4 py-3 {{ $isLocked ? 'text-gray-400 opacity-50 cursor-not-allowed pointer-events-none' : 'text-gray-600 hover:bg-gray-50' }} transition-colors {{ $currentRoute === 'client.classe' || str_starts_with($currentRoute, 'client.classe') ? 'nav-active' : '' }}" href="{{ route('client.classe') }}">
            <span class="material-symbols-outlined">meeting_room</span>
            <span class="text-lg font-semibold">Classe</span>
            @if($isLocked)<span class="material-symbols-outlined ml-auto">lock</span>@endif
        </a>

        <a class="flex items-center gap-3 px-4 py-3 {{ $isLocked ? 'text-gray-400 opacity-50 cursor-not-allowed pointer-events-none' : 'text-gray-600 hover:bg-gray-50' }} transition-colors {{ $currentRoute === 'client.eleve' || str_starts_with($currentRoute, 'client.eleve') ? 'nav-active' : '' }}" href="{{ route('client.eleve') }}">
            <span class="material-symbols-outlined">group</span>
            <span class="text-lg font-semibold">Élèves</span>
            @if($isLocked)<span class="material-symbols-outlined ml-auto">lock</span>@endif
        </a>

        <a class="flex items-center gap-3 px-4 py-3 {{ $isLocked ? 'text-gray-400 opacity-50 cursor-not-allowed pointer-events-none' : 'text-gray-600 hover:bg-gray-50' }} transition-colors {{ $currentRoute === 'client.matiere' || str_starts_with($currentRoute, 'client.matiere') ? 'nav-active' : '' }}" href="{{ route('client.matiere') }}">
            <span class="material-symbols-outlined">menu_book</span>
            <span class="text-lg font-semibold">Matières</span>
            @if($isLocked)<span class="material-symbols-outlined ml-auto">lock</span>@endif
        </a>

        <a class="flex items-center gap-3 px-4 py-3 {{ $isLocked ? 'text-gray-400 opacity-50 cursor-not-allowed pointer-events-none' : 'text-gray-600 hover:bg-gray-50' }} transition-colors {{ $currentRoute === 'client.enseignant' || str_starts_with($currentRoute, 'client.enseignant') ? 'nav-active' : '' }}" href="{{ route('client.enseignant') }}">
            <span class="material-symbols-outlined">school</span>
            <span class="text-lg font-semibold">Enseignants</span>
            @if($isLocked)<span class="material-symbols-outlined ml-auto">lock</span>@endif
        </a>

        <a class="flex items-center gap-3 px-4 py-3 {{ $isLocked ? 'text-gray-400 opacity-50 cursor-not-allowed pointer-events-none' : 'text-gray-600 hover:bg-gray-50' }} transition-colors {{ $currentRoute === 'client.note' || str_starts_with($currentRoute, 'client.notes') ? 'nav-active' : '' }}" href="{{ route('client.notes.index') }}">
            <span class="material-symbols-outlined">rule</span>
            <span class="text-lg font-semibold">Validation Notes</span>
            @if($isLocked)<span class="material-symbols-outlined ml-auto">lock</span>@endif
        </a>

        <a class="flex items-center gap-3 px-4 py-3 {{ $isLocked ? 'text-gray-400 opacity-50 cursor-not-allowed pointer-events-none' : 'text-gray-600 hover:bg-gray-50' }} transition-colors {{ $currentRoute === 'client.bulletin.index' || str_starts_with($currentRoute, 'client.bulletin.index') ? 'nav-active' : '' }}" href="{{ route('client.bulletin.index') }}">
            <span class="material-symbols-outlined">description</span>
            <span class="text-lg font-semibold">Bulletins</span>
            @if($isLocked)<span class="material-symbols-outlined ml-auto">lock</span>@endif
        </a>

        <a class="flex items-center gap-3 px-4 py-3 {{ $isLocked ? 'text-gray-400 opacity-50 cursor-not-allowed pointer-events-none' : 'text-gray-600 hover:bg-gray-50' }} transition-colors {{ $currentRoute === 'client.comptabilite' || str_starts_with($currentRoute, 'client.comptabilite') ? 'nav-active' : '' }}" href="{{ route('client.comptabilite') }}">
            <span class="material-symbols-outlined">payments</span>
            <span class="text-lg font-semibold">Comptabilité</span>
            @if($isLocked)<span class="material-symbols-outlined ml-auto">lock</span>@endif
        </a>

    </nav>
</aside>

{{-- ============ MAIN ============ --}}
<main class="ml-[250px] min-h-screen">
    <header class="navbar-fixed flex justify-between items-center px-6">
        <div class="flex items-center gap-4 w-1/2">
            <div class="relative w-full max-w-md">
                @yield('search')
            </div>
        </div>

        @php($headerUser = auth()->user())
        <div class="flex items-center gap-2">

            {{-- Notifications --}}
            <div class="relative">
                <button id="notification-button" type="button"
                        class="w-12 h-12 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-500 hover:text-indigo-600 transition-colors relative"
                        aria-label="Notifications" aria-expanded="false">
                    <span class="material-symbols-outlined">notifications</span>
                    <span id="notification-badge" class="hidden absolute top-1 right-1 min-w-[22px] h-[22px] px-1 bg-rose-500 text-white text-xs leading-[22px] text-center rounded-full border-2 border-white font-bold"></span>
                </button>
                <div id="notification-dropdown" class="hidden absolute right-0 top-14 z-50 w-[min(26rem,calc(100vw-2rem))] rounded-2xl border border-gray-100 bg-white shadow-xl overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                        <span class="text-base font-bold text-gray-900">Notifications</span>
                        <div class="flex items-center gap-2">
                            <span id="notification-count" class="text-sm text-gray-400 font-semibold"></span>
                            <button id="notification-delete-all" type="button" class="rounded-lg p-2 text-gray-400 hover:bg-rose-50 hover:text-rose-600 disabled:cursor-not-allowed disabled:opacity-40" title="Supprimer toutes les notifications" aria-label="Supprimer toutes les notifications" disabled>
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div id="notification-list" class="max-h-96 overflow-y-auto">
                        <p class="px-5 py-6 text-base text-gray-400 text-center">Aucune notification.</p>
                    </div>
                </div>
            </div>

            <div class="w-px h-6 bg-gray-200 mx-1"></div>

            {{-- Profil --}}
            <div class="relative">
                <button id="profile-menu-button" type="button"
                        class="flex items-center gap-2.5 px-2 py-1.5 rounded-lg hover:bg-gray-100 transition-colors"
                        aria-label="Menu du profil" aria-expanded="false">
                    <img alt="Photo de profil"
                         class="w-11 h-11 rounded-full border border-gray-200 object-cover"
                         src="{{ $headerUser?->image ? asset('storage/'.$headerUser->image) : 'https://ui-avatars.com/api/?background=4f46e5&color=fff&name='.urlencode($headerUser?->name ?? 'Client') }}">
                    <span class="text-base font-semibold text-gray-700 hidden sm:inline-block">{{ $headerUser?->name ?? 'Client' }}</span>
                    <span class="material-symbols-outlined text-gray-400 hidden sm:inline-block">expand_more</span>
                </button>
                <div id="profile-menu" class="hidden absolute right-0 top-14 z-50 w-64 rounded-2xl border border-gray-100 bg-white p-2 shadow-xl">
                    <a class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-base font-semibold text-gray-700 hover:bg-gray-50 transition" href="{{ route('client.parametres.index') }}">
                        <span class="material-symbols-outlined text-gray-400">settings</span>
                        Paramètres
                    </a>
                    <div class="border-t border-gray-100 my-1"></div>
                    <button class="w-full flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-base font-semibold text-rose-600 hover:bg-rose-50 transition text-left" type="button" onclick="document.getElementById('logout-form').submit()">
                        <span class="material-symbols-outlined">logout</span>
                        Déconnexion
                    </button>
                </div>
            </div>
        </div>
    </header>

    <div class="main-content-with-fixed-nav p-6">
        @if($errors->any())
            <div class="mb-4 rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-rose-700 text-base">
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @include('partials.subscription-grace-warning')

        @yield('content')
    </div>
</main>

{{-- Formulaire de déconnexion caché --}}
<form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
    @csrf
</form>

<script>
(() => {
    const button = document.getElementById('notification-button');
    const dropdown = document.getElementById('notification-dropdown');
    const list = document.getElementById('notification-list');
    const badge = document.getElementById('notification-badge');
    const count = document.getElementById('notification-count');
    const deleteAllButton = document.getElementById('notification-delete-all');
    const profileButton = document.getElementById('profile-menu-button');
    const profileMenu = document.getElementById('profile-menu');
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const escapeHtml = value => String(value ?? '').replace(/[&<>'"]/g, char => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' }[char]));

    const render = payload => {
        const unread = payload.unread_count || 0;
        badge.textContent = unread > 99 ? '99+' : unread;
        badge.classList.toggle('hidden', unread === 0);
        count.textContent = unread ? `${unread} non lue${unread > 1 ? 's' : ''}` : 'À jour';
        deleteAllButton.disabled = payload.notifications.length === 0;
        const icons = {
            payment: 'fa-credit-card',
            subscription: 'fa-calendar-check',
            bulletin: 'fa-file-lines',
            school_year: 'fa-graduation-cap',
            update: 'fa-arrows-rotate',
            staff: 'fa-users',
            teacher: 'fa-chalkboard-user',
            student: 'fa-user-plus',
            establishment: 'fa-school',
            security: 'fa-lock'
        };
        list.innerHTML = payload.notifications.length
            ? payload.notifications.map(item => `
                <div class="flex border-b border-gray-100 hover:bg-gray-50 transition-colors ${item.read ? '' : 'bg-indigo-50/50'}">
                    <a href="${escapeHtml(item.url)}" class="notification-item flex min-w-0 flex-1 gap-3 px-5 py-3.5 text-left">
                        <span class="mt-0.5 flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                            <i class="fa-solid ${icons[item.category] || 'fa-bell'} text-lg"></i>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-base font-semibold text-gray-900">${escapeHtml(item.title)}</span>
                            <span class="mt-0.5 block truncate text-sm text-gray-500">${escapeHtml(item.preview)}</span>
                            <span class="mt-1 block text-xs text-gray-400">${escapeHtml(item.date)}${item.read ? '' : ' · <span class="text-indigo-600 font-semibold">Non lu</span>'}</span>
                        </span>
                    </a>
                    <button type="button" class="notification-delete px-3 text-gray-400 hover:text-rose-600 transition" data-id="${item.id}" title="Supprimer">
                        <i class="fa-solid fa-trash text-lg"></i>
                    </button>
                </div>
            `).join('')
            : '<p class="px-5 py-6 text-base text-gray-400 text-center">Aucune notification.</p>';
    };

    const load = () => fetch(@json(route('notifications.index')), {
        headers: { Accept: 'application/json' },
        credentials: 'same-origin'
    })
    .then(response => response.ok ? response.json() : null)
    .then(payload => payload && render(payload))
    .catch(() => {});

    button?.addEventListener('click', () => {
        dropdown.classList.toggle('hidden');
        button.setAttribute('aria-expanded', String(!dropdown.classList.contains('hidden')));
        load();
    });
    deleteAllButton?.addEventListener('click', () => {
        Swal.fire({
            title: 'Supprimer toutes les notifications ?',
            text: 'Cette action supprimera toutes vos notifications et ne peut pas être annulée.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Tout supprimer',
            cancelButtonText: 'Annuler',
            confirmButtonColor: '#dc2626'
        }).then(result => {
            if (!result.isConfirmed) return;
            fetch(@json(route('notifications.destroy-all')), {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
                credentials: 'same-origin'
            })
            .then(response => { if (!response.ok) throw new Error(); return response.json(); })
            .then(() => {
                load();
                Swal.fire({ icon: 'success', title: 'Toutes les notifications ont été supprimées.', timer: 1800, showConfirmButton: false });
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Erreur', text: 'Impossible de supprimer les notifications.' }));
        });
    });
    profileButton?.addEventListener('click', () => profileMenu.classList.toggle('hidden'));

    list?.addEventListener('click', event => {
        const deleteButton = event.target.closest('.notification-delete');
        if (!deleteButton) return;
        Swal.fire({
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'px-4 py-2 rounded-lg text-sm font-semibold text-white mx-1',
                cancelButton: 'px-4 py-2 rounded-lg text-sm font-semibold text-white mx-1',
                title: 'text-base font-semibold',
                htmlContainer: 'text-sm text-gray-500'
            },
            buttonsStyling: false,
            reverseButtons: true,
            title: 'Supprimer cette notification ?',
            text: 'Cette action est irréversible.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler',
            iconColor: '#e11d48'
        }).then(result => {
            if (!result.isConfirmed) return;
            fetch(`/notifications/${deleteButton.dataset.id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
                credentials: 'same-origin'
            })
            .then(response => { if (!response.ok) throw new Error(); return response.json(); })
            .then(() => {
                Swal.fire({ icon: 'success', title: 'Notification supprimée', timer: 1800, showConfirmButton: false, customClass: { popup: 'rounded-2xl' } });
                load();
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Erreur', text: 'Impossible de supprimer la notification.', customClass: { popup: 'rounded-2xl' } }));
        });
    });

    document.addEventListener('click', event => {
        if (!dropdown?.contains(event.target) && !button?.contains(event.target)) dropdown?.classList.add('hidden');
        if (!profileMenu?.contains(event.target) && !profileButton?.contains(event.target)) profileMenu?.classList.add('hidden');
    });

    load();
    window.setInterval(load, 20000);
})();

// Global SweetAlert confirmation for deletion across Client portal
window.confirmDeleteSweet = function(event, form, title = 'Supprimer ?', text = 'Cette action est irréversible.') {
    event.preventDefault();
    Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e11d48',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Oui, supprimer',
        cancelButtonText: 'Annuler',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
};

// Global session alerts
@if (session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Succès',
        text: @json(session('success')),
        timer: 3000,
        showConfirmButton: false
    });
@endif

@if (session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Erreur',
        text: @json(session('error')),
        confirmButtonText: 'OK',
        confirmButtonColor: '#4f46e5'
    });
@endif
</script>

@yield('scripts')
@stack('scripts')
</body>
</html>
