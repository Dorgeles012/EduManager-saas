<!DOCTYPE html>
<html class="light" lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>@yield('title', 'EduManager - Espace Parent')</title>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&amp;family=Lexend:wght@600;700;800&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
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
                        "primary": "#1f108e",
                        "on-primary": "#ffffff",
                        "primary-fixed": "#e2dfff",
                        "primary-container": "#3730a3",
                        "on-primary-container": "#a9a7ff",
                        "secondary": "#006a61",
                        "on-secondary": "#ffffff",
                        "secondary-container": "#86f2e4",
                        "background": "#f9f9ff",
                        "on-background": "#111c2d",
                        "surface": "#f9f9ff",
                        "surface-container-lowest": "#ffffff",
                        "surface-container-low": "#f0f3ff",
                        "surface-container": "#e7eeff",
                        "surface-container-high": "#dee8ff",
                        "surface-container-highest": "#d8e3fb",
                        "surface-variant": "#d8e3fb",
                        "on-surface": "#111c2d",
                        "on-surface-variant": "#464553",
                        "outline": "#777584",
                        "outline-variant": "#c8c4d5",
                        "text-muted": "#64748B",
                        "success-green": "#059669",
                        "warning-amber": "#D97706",
                        "alert-red": "#E11D48",
                        "error": "#ba1a1a",
                        "error-container": "#ffdad6"
                    },
                    fontFamily: {
                        "headline-lg": ["Lexend"],
                        "headline-md": ["Lexend"],
                        "body-md": ["Inter"],
                        "body-sm": ["Inter"],
                        "label-md": ["Inter"]
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #F8FAFC; color: #111c2d; -webkit-font-smoothing: antialiased; }
        .ambient-shadow { box-shadow: 0 4px 12px 0 rgba(55, 48, 163, 0.04); }
        .custom-shadow { box-shadow: 0 4px 12px rgba(55, 48, 163, 0.04); }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .sidebar-nav { overflow-y: auto; scrollbar-width: thin; scrollbar-color: #c8c4d5 #f0f3ff; flex: 1; }
        .sidebar-nav::-webkit-scrollbar { width: 5px; }
        .sidebar-nav::-webkit-scrollbar-track { background: #f0f3ff; border-radius: 10px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: #c8c4d5; border-radius: 10px; }
        .navbar-fixed { position: fixed; top: 0; right: 0; left: 260px; z-index: 40; background-color: #f9f9ff; border-bottom: 1px solid #c8c4d5; height: 64px; }
        .main-content-with-fixed-nav { margin-top: 64px; }
        @media (max-width: 768px) { .navbar-fixed { left: 0; } }
        .sidebar-nav a.nav-active { color: #1f108e !important; font-weight: bold !important; border-right: 4px solid #1f108e !important; background-color: #e7eeff !important; }
        .sidebar-nav a.nav-active span.material-symbols-outlined { color: #1f108e !important; }
        .sidebar-nav a { transition: all 0.2s ease; border-right: 4px solid transparent; }
        .sidebar-nav a:hover { background-color: #f0f3ff; }
        .swal2-popup { font-size: 0.9375rem !important; }
    </style>
    @stack('styles')
</head>
<body class="font-body-md text-body-md overflow-x-hidden">
    <!-- SideNavBar -->
    <aside class="fixed left-0 top-0 h-screen w-[260px] bg-surface-container-lowest flex flex-col border-r border-outline-variant z-50">
        <div class="p-6 flex-shrink-0 flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-primary to-primary/80 rounded-xl flex items-center justify-center shadow-lg shadow-primary/20">
                <span class="material-symbols-outlined text-white text-2xl" style="font-variation-settings: 'FILL' 1;">family_restroom</span>
            </div>
            <div>
                <h1 class="font-headline-lg text-headline-md font-bold text-primary tracking-tight">EduManager</h1>
                <p class="text-xs text-text-muted">Espace Parent</p>
            </div>
        </div>

        <nav class="sidebar-nav mt-2">
            @php
                $currentRoute = request()->route()->getName();
            @endphp

            <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors {{ $currentRoute === 'parent.dashboard' ? 'nav-active' : '' }}" href="{{ route('parent.dashboard') }}">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="font-label-md text-label-md">Tableau de bord</span>
            </a>

            <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors {{ $currentRoute === 'parent.enfants' ? 'nav-active' : '' }}" href="{{ route('parent.enfants') }}">
                <span class="material-symbols-outlined">group</span>
                <span class="font-label-md text-label-md">Mes enfants</span>
            </a>

            <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors {{ str_starts_with($currentRoute, 'parent.enfant.scolarite') ? 'nav-active' : '' }}" href="{{ route('parent.enfants') }}">
                <span class="material-symbols-outlined">school</span>
                <span class="font-label-md text-label-md">Scolarité</span>
            </a>

            <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors {{ str_starts_with($currentRoute, 'parent.enfant.notes') ? 'nav-active' : '' }}" href="{{ route('parent.enfants') }}">
                <span class="material-symbols-outlined">fact_check</span>
                <span class="font-label-md text-label-md">Notes</span>
            </a>

            <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors {{ str_starts_with($currentRoute, 'parent.enfant.bulletins') ? 'nav-active' : '' }}" href="{{ route('parent.enfants') }}">
                <span class="material-symbols-outlined">description</span>
                <span class="font-label-md text-label-md">Bulletins</span>
            </a>

            <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors {{ str_starts_with($currentRoute, 'parent.enfant.emploi-temps') ? 'nav-active' : '' }}" href="{{ route('parent.enfants') }}">
                <span class="material-symbols-outlined">calendar_month</span>
                <span class="font-label-md text-label-md">Emploi du temps</span>
            </a>

            <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors" href="{{ route('parent.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <span class="material-symbols-outlined">logout</span>
                <span class="font-label-md text-label-md">Déconnexion</span>
            </a>

            <form id="logout-form" action="{{ route('parent.logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="ml-[260px] min-h-screen">
        <!-- TopNavBar -->
        <header class="navbar-fixed flex justify-between items-center px-gutter-desktop">
            @php($headerUser = auth()->user())
            <div class="text-sm text-text-muted hidden sm:block">
                Bienvenue, <span class="font-semibold text-on-surface">{{ $headerUser?->prenom ?? $headerUser?->nom ?? 'Parent' }}</span>
            </div>
            <div class="flex items-center gap-1.5 ml-auto">
                <div class="w-px h-5 bg-outline-variant"></div>
                <div class="relative">
                    <button id="profile-menu-button" type="button" class="flex items-center gap-1.5" aria-label="Menu du profil">
                        <img alt="Photo de profil" class="w-8 h-8 rounded-full border border-outline-variant object-cover" src="{{ $headerUser?->image ? asset('storage/'.$headerUser->image) : 'https://ui-avatars.com/api/?background=1f108e&color=fff&name='.urlencode($headerUser?->name ?? 'Parent') }}">
                        <span class="text-sm font-medium text-on-surface hidden sm:inline-block">{{ $headerUser?->name ?? 'Parent' }}</span>
                    </button>
                    <div id="profile-menu" class="hidden absolute right-0 top-11 z-50 w-44 rounded-xl border border-outline-variant bg-surface-container-lowest p-1 shadow-xl">
                        <button class="w-full rounded-lg px-3 py-2 text-left text-sm text-alert-red hover:bg-error-container" type="button" onclick="document.getElementById('logout-form').submit()">Déconnexion</button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="main-content-with-fixed-nav p-6">
            @if($errors->any())
                <div class="mb-4 rounded-lg border border-alert-red/20 bg-alert-red/10 px-4 py-3 text-alert-red">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script>
        (() => {
            const profileButton = document.getElementById('profile-menu-button');
            const profileMenu = document.getElementById('profile-menu');
            profileButton?.addEventListener('click', () => profileMenu?.classList.toggle('hidden'));
            document.addEventListener('click', event => {
                if (profileMenu && !profileMenu.contains(event.target) && !profileButton?.contains(event.target)) profileMenu.classList.add('hidden');
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>
