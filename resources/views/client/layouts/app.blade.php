<!DOCTYPE html>
<html class="light" lang="fr">
<head>
    @php
        $teachers = $teachers ?? collect();
    @endphp
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>@yield('title', 'EduManager - Tableau de bord')</title>
    
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
                    borderRadius: {
                        DEFAULT: "0.25rem",
                        lg: "0.5rem",
                        xl: "12px",
                        full: "9999px"
                    },
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
                        "headline-lg": ["28px", { "lineHeight": "36px", "letterSpacing": "-0.01em", "fontWeight": "600" }],
                        "headline-md": ["20px", { "lineHeight": "28px", "fontWeight": "600" }],
                        "body-md": ["16px", { "lineHeight": "24px", "fontWeight": "400" }],
                        "body-sm": ["14px", { "lineHeight": "20px", "fontWeight": "400" }],
                        "label-md": ["14px", { "lineHeight": "16px", "letterSpacing": "0.01em", "fontWeight": "600" }],
                        "headline-xl": ["36px", { "lineHeight": "44px", "letterSpacing": "-0.02em", "fontWeight": "700" }],
                        "label-sm": ["12px", { "lineHeight": "16px", "fontWeight": "500" }],
                        "body-lg": ["18px", { "lineHeight": "28px", "fontWeight": "400" }]
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
        }
        
        .ambient-shadow { 
            box-shadow: 0 4px 12px 0 rgba(55, 48, 163, 0.04); 
        }
        
        .glass-card { 
            background: rgba(255, 255, 255, 0.8); 
            backdrop-filter: blur(10px); 
            border: 1px solid rgba(226, 232, 240, 1); 
        }
        
        .material-symbols-outlined { 
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; 
        }
        
        /* Sidebar scrollbar styles */
        .sidebar-nav {
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #c8c4d5 #f0f3ff;
            flex: 1;
        }
        
        .sidebar-nav::-webkit-scrollbar {
            width: 5px;
        }
        
        .sidebar-nav::-webkit-scrollbar-track {
            background: #f0f3ff;
            border-radius: 10px;
        }
        
        .sidebar-nav::-webkit-scrollbar-thumb {
            background: #c8c4d5;
            border-radius: 10px;
        }
        
        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: #a09eb0;
        }
        
        /* Fixed navbar */
        .navbar-fixed {
            position: fixed;
            top: 0;
            right: 0;
            left: 260px;
            z-index: 40;
            background-color: #f9f9ff;
            border-bottom: 1px solid #c8c4d5;
            height: 64px;
        }
        
        /* Main content adjustment */
        .main-content-with-fixed-nav {
            margin-top: 64px;
        }
        
        @media (max-width: 768px) {
            .navbar-fixed {
                left: 0;
            }
        }
        
        /* Active link style - CORRIGÉ */
        .sidebar-nav a.nav-active {
            color: #1f108e !important;
            font-weight: bold !important;
            border-right: 4px solid #1f108e !important;
            background-color: #e7eeff !important;
        }
        
        .sidebar-nav a.nav-active span.material-symbols-outlined {
            color: #1f108e !important;
        }
        
        /* Hover style for nav links */
        .sidebar-nav a {
            transition: all 0.2s ease;
            border-right: 4px solid transparent;
        }
        
        .sidebar-nav a:hover {
            background-color: #f0f3ff;
        }

        /* SweetAlert2 - Tailles de police optimisées */
        .swal2-popup {
            font-size: 0.9375rem !important;
        }

        .swal2-title {
            font-size: 1.25rem !important;
        }

        .swal2-html-container {
            font-size: 0.875rem !important;
        }

        .swal2-confirm,
        .swal2-cancel {
            font-size: 0.8125rem !important;
        }
    </style>
    
    @stack('styles')
</head>
<body class="font-body-md text-body-md overflow-x-hidden">
    <!-- SideNavBar -->
    <aside class="fixed left-0 top-0 h-screen w-[260px] bg-surface-container-lowest flex flex-col border-r border-outline-variant z-50">
        <div class="p-6 flex-shrink-0 flex items-center gap-3">
    <div class="w-10 h-10 bg-gradient-to-br from-primary to-primary/80 rounded-xl flex items-center justify-center shadow-lg shadow-primary/20">
        <span class="material-symbols-outlined text-white text-2xl" style="font-variation-settings: 'FILL' 1;">school</span>
    </div>
    <div>
        <h1 class="font-headline-lg text-headline-md font-bold text-primary tracking-tight">EduManager</h1>
    </div>
</div>
        
        <!-- Scrollable navigation -->
        <nav class="sidebar-nav mt-2">
            @php
                $currentRoute = request()->route()->getName();
            @endphp
            
            <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors {{ $currentRoute === 'client.dashboard' ? 'nav-active' : '' }}" href="{{ route('client.dashboard') }}">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="font-label-md text-label-md">Dashboard</span>
            </a>

            <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors {{ str_starts_with($currentRoute, 'client.abonnement') ? 'nav-active' : '' }}" href="{{ route('client.abonnement.index') }}">
                <span class="material-symbols-outlined">subscriptions</span>
                <span class="font-label-md text-label-md">Abonnements</span>
            </a>

            <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors {{ str_starts_with($currentRoute, 'client.annee') ? 'nav-active' : '' }}" href="{{ route('client.annee.index') }}">
                <span class="material-symbols-outlined">calendar_today</span>
                <span class="font-label-md text-label-md">Année académique</span>
            </a>

            <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors {{ str_starts_with($currentRoute, 'client.personnel') ? 'nav-active' : '' }}" href="{{ route('client.personnel.index') }}">
                <span class="material-symbols-outlined">badge</span>
                <span class="font-label-md text-label-md">Personnel</span>
            </a>

            <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors {{ request()->routeIs('client.series.*') ? 'nav-active' : '' }}" href="{{ route('client.series.index') }}">
                <span class="material-symbols-outlined">category</span>
                <span class="font-label-md text-label-md">Séries</span>
            </a>

            <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors {{ $currentRoute === 'client.niveaux' || str_starts_with($currentRoute, 'client.niveaux') ? 'nav-active' : '' }}" href="{{ route('client.niveaux') }}">
                <span class="material-symbols-outlined">leaderboard</span>
                <span class="font-label-md text-label-md">Niveau</span>
            </a>
            
            <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors {{ $currentRoute === 'client.classe' || str_starts_with($currentRoute, 'client.classe') ? 'nav-active' : '' }}" href="{{ route('client.classe') }}">
                <span class="material-symbols-outlined">meeting_room</span>
                <span class="font-label-md text-label-md">Classe</span>
            </a>
            
            <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors {{ $currentRoute === 'client.eleve' || str_starts_with($currentRoute, 'client.eleve') ? 'nav-active' : '' }}" href="{{ route('client.eleve') }}">
                <span class="material-symbols-outlined">group</span>
                <span class="font-label-md text-label-md">Eleves</span>
            </a>
            
            <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors {{ $currentRoute === 'client.matiere' || str_starts_with($currentRoute, 'client.matiere') ? 'nav-active' : '' }}" href="{{ route('client.matiere') }}">
                <span class="material-symbols-outlined">menu_book</span>
                <span class="font-label-md text-label-md">Matiere(s)</span>
            </a>
            
            <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors {{ $currentRoute === 'client.enseignant' || str_starts_with($currentRoute, 'client.enseignant') ? 'nav-active' : '' }}" href="{{ route('client.enseignant') }}">
                <span class="material-symbols-outlined">school</span>
                <span class="font-label-md text-label-md">Enseignants</span>
            </a>
            
            <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors {{ $currentRoute === 'client.note' || str_starts_with($currentRoute, 'client.note') ? 'nav-active' : '' }}" href="{{ route('client.note') }}">
                <span class="material-symbols-outlined">grade</span>
                <span class="font-label-md text-label-md">Note(s)</span>
            </a>
            
            <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors {{ $currentRoute === 'client.bulletin.index' || str_starts_with($currentRoute, 'client.bulletin.index') ? 'nav-active' : '' }}" href="{{ route('client.bulletin.index') }}">
                <span class="material-symbols-outlined">description</span>
                <span class="font-label-md text-label-md">Bulletin</span>
            </a>
            
            <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors {{ $currentRoute === 'client.comptabilite' || str_starts_with($currentRoute, 'client.comptabilite') ? 'nav-active' : '' }}" href="{{ route('client.comptabilite') }}">
                <span class="material-symbols-outlined">payments</span>
                <span class="font-label-md text-label-md">Comptabilite</span>
            </a>
            
            <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors {{ str_starts_with($currentRoute, 'client.parametres') ? 'nav-active' : '' }}" href="{{ route('client.parametres.index') }}">
                <span class="material-symbols-outlined">settings</span>
                <span class="font-label-md text-label-md">Paramètres</span>
            </a>
            
            <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <span class="material-symbols-outlined">logout</span>
                <span class="font-label-md text-label-md">Déconnexion</span>
            </a>
            
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </nav>
    </aside>


    <!-- Main Content -->
    <main class="ml-[260px] min-h-screen">
        <!-- Fixed TopNavBar -->
        <header class="navbar-fixed flex justify-between items-center px-gutter-desktop">
            <div class="flex items-center gap-6 w-1/2">
                <div class="relative w-full max-w-md">
                    @yield('search')
                </div>
            </div>
            
            @php($headerUser = auth()->user())
            <div class="flex items-center gap-1.5">
                <div class="relative">
                    <button id="notification-button" type="button" class="p-1.5 text-on-surface-variant hover:text-primary transition-colors relative" aria-label="Notifications" aria-expanded="false">
                    <span class="material-symbols-outlined text-xl">notifications</span>
                    <span id="notification-badge" class="hidden absolute -top-1 -right-1 min-w-4 h-4 px-1 bg-alert-red text-white text-[10px] leading-4 rounded-full border border-surface"></span>
                    </button>
                    <div id="notification-dropdown" class="hidden absolute right-0 top-11 z-50 w-[min(24rem,calc(100vw-2rem))] rounded-xl border border-outline-variant bg-surface-container-lowest shadow-xl overflow-hidden">
                        <div class="px-4 py-3 border-b border-surface-container flex items-center justify-between"><span class="font-semibold text-on-surface">Notifications</span><span id="notification-count" class="text-xs text-text-muted"></span></div>
                        <div id="notification-list" class="max-h-96 overflow-y-auto"><p class="px-4 py-6 text-sm text-text-muted text-center">Aucune notification.</p></div>
                    </div>
                </div>

                <div class="w-px h-5 bg-outline-variant mx-0"></div>

                <div class="relative">
                    <button id="profile-menu-button" type="button" class="flex items-center gap-1.5" aria-label="Menu du profil" aria-expanded="false">
                        <img alt="Photo de profil" class="w-8 h-8 rounded-full border border-outline-variant object-cover" src="{{ $headerUser?->image ? asset('storage/'.$headerUser->image) : 'https://ui-avatars.com/api/?background=1f108e&color=fff&name='.urlencode($headerUser?->name ?? 'Client') }}">
                        <span class="text-sm font-medium text-on-surface hidden sm:inline-block">{{ $headerUser?->name ?? 'Client' }}</span>
                    </button>
                    <div id="profile-menu" class="hidden absolute right-0 top-11 z-50 w-44 rounded-xl border border-outline-variant bg-surface-container-lowest p-1 shadow-xl">
                        <a class="block rounded-lg px-3 py-2 text-sm text-on-surface hover:bg-surface-container" href="{{ route('client.parametres.index') }}">Mon profil</a>
                        <a class="block rounded-lg px-3 py-2 text-sm text-on-surface hover:bg-surface-container" href="{{ route('client.parametres.index') }}">Paramètres</a>
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
            const button = document.getElementById('notification-button');
            const dropdown = document.getElementById('notification-dropdown');
            const list = document.getElementById('notification-list');
            const badge = document.getElementById('notification-badge');
            const count = document.getElementById('notification-count');
            const profileButton = document.getElementById('profile-menu-button');
            const profileMenu = document.getElementById('profile-menu');
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            const escapeHtml = value => String(value ?? '').replace(/[&<>'"]/g, char => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' }[char]));

            const render = payload => {
                const unread = payload.unread_count || 0;
                badge.textContent = unread > 99 ? '99+' : unread;
                badge.classList.toggle('hidden', unread === 0);
                count.textContent = unread ? `${unread} non lue${unread > 1 ? 's' : ''}` : 'À jour';
                const icons = { payment: 'fa-credit-card', subscription: 'fa-calendar-check', bulletin: 'fa-file-lines', school_year: 'fa-graduation-cap', update: 'fa-arrows-rotate', staff: 'fa-users', teacher: 'fa-chalkboard-user', student: 'fa-user-plus', establishment: 'fa-school', security: 'fa-lock' };
                list.innerHTML = payload.notifications.length ? payload.notifications.map(item => `<div class="flex border-b border-surface-container hover:bg-surface-container-low transition-colors ${item.read ? '' : 'bg-primary-fixed/30 font-semibold'}"><a href="${escapeHtml(item.url)}" class="notification-item flex min-w-0 flex-1 gap-3 px-4 py-3 text-left"><span class="mt-1 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-fixed text-primary"><i class="fa-solid ${icons[item.category] || 'fa-bell'}"></i></span><span class="min-w-0"><span class="block truncate text-sm text-on-surface">${escapeHtml(item.title)}</span><span class="mt-1 block truncate text-xs font-normal text-on-surface-variant">${escapeHtml(item.preview)}</span><span class="mt-1 block text-[11px] font-normal text-text-muted">${escapeHtml(item.date)}${item.read ? '' : ' · Non lu'}</span></span></a><button type="button" class="notification-delete px-3 text-text-muted hover:text-alert-red" data-id="${item.id}" title="Supprimer"><i class="fa-solid fa-trash"></i></button></div>`).join('') : '<p class="px-4 py-6 text-sm text-text-muted text-center">Aucune notification.</p>';
            };
            const load = () => fetch(@json(route('notifications.index')), { headers: { Accept: 'application/json' }, credentials: 'same-origin' }).then(response => response.ok ? response.json() : null).then(payload => payload && render(payload)).catch(() => {});
            button?.addEventListener('click', () => { dropdown.classList.toggle('hidden'); button.setAttribute('aria-expanded', String(!dropdown.classList.contains('hidden'))); load(); });
            profileButton?.addEventListener('click', () => profileMenu.classList.toggle('hidden'));
            list?.addEventListener('click', event => {
                const deleteButton = event.target.closest('.notification-delete');
                if (!deleteButton) return;
                Swal.fire({ title: 'Supprimer cette notification ?', text: 'Cette action est irréversible.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Oui, supprimer', cancelButtonText: 'Annuler', confirmButtonColor: '#dc2626' }).then(result => {
                    if (!result.isConfirmed) return;
                    fetch(`/notifications/${deleteButton.dataset.id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' }, credentials: 'same-origin' })
                        .then(response => { if (!response.ok) throw new Error(); return response.json(); })
                        .then(() => { Swal.fire({ icon: 'success', title: 'Notification supprimée avec succès.', timer: 1800, showConfirmButton: false }); load(); })
                        .catch(() => Swal.fire({ icon: 'error', title: 'Une erreur est survenue lors de la suppression de la notification.' }));
                });
            });
            document.addEventListener('click', event => { if (!dropdown?.contains(event.target) && !button?.contains(event.target)) dropdown?.classList.add('hidden'); if (!profileMenu?.contains(event.target) && !profileButton?.contains(event.target)) profileMenu?.classList.add('hidden'); });
            load();
            window.setInterval(load, 20000);
        })();
    </script>
    @stack('scripts')
</body>
</html>