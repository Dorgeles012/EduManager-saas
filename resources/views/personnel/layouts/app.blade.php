<!DOCTYPE html>
<html class="light" lang="fr" style="">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/edumanager-logo.svg') }}">
    <title>{{ $title ?? 'EduManager | Tableau de bord Personnel' }}</title>

    <!-- Polices -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=Lexend:wght@400;600;700&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
    <!-- Font Awesome pour les icônes supplémentaires -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "surface-container-low": "#f0f3ff",
                        "secondary": "#006a61",
                        "success-green": "#059669",
                        "primary-fixed-dim": "#c3c0ff",
                        "surface-container": "#e7eeff",
                        "surface-container-lowest": "#ffffff",
                        "surface-dim": "#cfdaf2",
                        "on-tertiary-fixed": "#191c1e",
                        "surface-bright": "#f9f9ff",
                        "on-secondary": "#ffffff",
                        "outline-variant": "#c8c4d5",
                        "inverse-on-surface": "#ecf1ff",
                        "on-primary-fixed": "#0f0069",
                        "alert-red": "#E11D48",
                        "on-secondary-container": "#006f66",
                        "on-surface-variant": "#464553",
                        "tertiary-fixed": "#e0e3e5",
                        "background": "#f9f9ff",
                        "surface-subtle": "#F1F5F9",
                        "secondary-fixed-dim": "#6bd8cb",
                        "primary-container": "#3730a3",
                        "on-primary-fixed-variant": "#3b35a7",
                        "secondary-container": "#86f2e4",
                        "tertiary-container": "#404345",
                        "on-error": "#ffffff",
                        "on-tertiary-container": "#adb0b2",
                        "error-container": "#ffdad6",
                        "surface-container-highest": "#d8e3fb",
                        "tertiary": "#2a2d2f",
                        "on-primary-container": "#a9a7ff",
                        "inverse-primary": "#c3c0ff",
                        "on-primary": "#ffffff",
                        "secondary-fixed": "#89f5e7",
                        "tertiary-fixed-dim": "#c4c7c9",
                        "error": "#ba1a1a",
                        "primary-fixed": "#e2dfff",
                        "on-secondary-fixed": "#00201d",
                        "on-tertiary": "#ffffff",
                        "surface-variant": "#d8e3fb",
                        "primary": "#1f108e",
                        "surface-tint": "#544fc0",
                        "text-muted": "#64748B",
                        "surface": "#f9f9ff",
                        "on-tertiary-fixed-variant": "#444749",
                        "inverse-surface": "#263143",
                        "outline": "#777584",
                        "on-background": "#111c2d",
                        "warning-amber": "#D97706",
                        "on-secondary-fixed-variant": "#005049",
                        "surface-container-high": "#dee8ff",
                        "on-surface": "#111c2d",
                        "on-error-container": "#93000a"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "base-unit": "4px",
                        "max-width": "1440px",
                        "margin-mobile": "16px",
                        "margin-desktop": "32px",
                        "gutter-mobile": "16px",
                        "gutter-desktop": "24px"
                    },
                    "fontFamily": {
                        "body-sm": ["Inter"],
                        "body-md": ["Inter"],
                        "body-lg": ["Inter"],
                        "headline-lg-mobile": ["Lexend"],
                        "headline-md": ["Lexend"],
                        "headline-lg": ["Lexend"],
                        "label-md": ["Inter"],
                        "headline-xl": ["Lexend"],
                        "label-sm": ["Inter"]
                    },
                    "fontSize": {
                        "body-sm": ["14px", {"lineHeight": "20px", "fontWeight": "400"}],
                        "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                        "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
                        "headline-lg-mobile": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                        "headline-md": ["20px", {"lineHeight": "28px", "fontWeight": "600"}],
                        "headline-lg": ["28px", {"lineHeight": "36px", "letterSpacing": "-0.01em", "fontWeight": "600"}],
                        "label-md": ["14px", {"lineHeight": "16px", "letterSpacing": "0.01em", "fontWeight": "600"}],
                        "headline-xl": ["36px", {"lineHeight": "44px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                        "label-sm": ["12px", {"lineHeight": "16px", "fontWeight": "500"}]
                    }
                },
            },
        }
    </script>

    <style>
        /* Styles de base */
        .sidebar-shadow { box-shadow: 4px 0 12px rgba(55,48,163,0.04); }
        .card-shadow { box-shadow: 0 4px 12px rgba(55,48,163,0.04); }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .chart-bar { transition: height 1s ease-in-out; }

        /* ============================================
           STYLES DE LA SIDEBAR AVEC SURBRILLANCE
           ============================================ */

        .nav-active {
            background: linear-gradient(to right, rgba(31, 16, 142, 0.08), rgba(31, 16, 142, 0.03));
            border-right: 4px solid #1f108e;
            color: #1f108e !important;
            border-radius: 0 12px 12px 0;
            margin-right: 8px;
            transition: all 0.3s ease;
        }

        .nav-active .material-symbols-outlined {
            color: #1f108e;
            font-variation-settings: 'FILL' 1, 'wght' 500;
        }

        .nav-active span:not(.material-symbols-outlined) {
            color: #1f108e !important;
            font-weight: 600;
        }

        .sidebar-nav a {
            position: relative;
            transition: all 0.25s ease;
            border-radius: 0 12px 12px 0;
            margin-right: 4px;
        }

        .sidebar-nav a:hover {
            background-color: rgba(31, 16, 142, 0.06);
            transform: translateX(4px);
        }

        .sidebar-nav a:active {
            transform: scale(0.97);
        }

        /* Scrollbar de la sidebar */
        .sidebar-nav {
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #c8c4d5 #f0f3ff;
            flex: 1;
        }
        .sidebar-nav::-webkit-scrollbar { width: 5px; }
        .sidebar-nav::-webkit-scrollbar-track { background: #f0f3ff; border-radius: 10px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: #c8c4d5; border-radius: 10px; }
        .sidebar-nav::-webkit-scrollbar-thumb:hover { background: #a09eb0; }
    </style>
    @include('partials.compact-styles')
</head>
<body class="bg-background text-on-surface font-body-md">

<!-- Barre latérale de navigation -->
<aside class="fixed left-0 top-0 h-screen w-[260px] bg-surface-container-lowest flex flex-col border-r border-outline-variant z-50">
    <div class="p-6 flex-shrink-0 flex items-center gap-3">
        <img src="{{ asset('images/edumanager-logo.svg') }}" alt="EduManager" class="w-10 h-10 object-contain">
        <div>
            <h1 class="font-headline-lg text-headline-md font-bold text-primary tracking-tight">EduManager</h1>
            <p class="text-xs text-gray-400 tracking-wider font-semibold">Espace personnel</p>
        </div>
    </div>

    <nav class="sidebar-nav mt-2">
        @php
            $currentRoute = request()->route()->getName();
        @endphp

        <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors {{ $currentRoute === 'personnel.dashboard' ? 'nav-active' : '' }}" href="{{ route('personnel.dashboard') }}">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="font-label-md text-label-md">Dashboard</span>
        </a>

        <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors {{ str_starts_with($currentRoute, 'personnel.annee-academique') ? 'nav-active' : '' }}" href="{{ route('personnel.annee-academique.index') }}">
            <span class="material-symbols-outlined">calendar_today</span>
            <span class="font-label-md text-label-md">Année académique</span>
        </a>

        <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors {{ request()->routeIs('personnel.series.*') ? 'nav-active' : '' }}" href="{{ route('personnel.series.index') }}">
            <span class="material-symbols-outlined">category</span>
            <span class="font-label-md text-label-md">Séries</span>
        </a>

        <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors {{ request()->routeIs('personnel.niveaux.*') ? 'nav-active' : '' }}" href="{{ route('personnel.niveaux.index') }}">
            <span class="material-symbols-outlined">leaderboard</span>
            <span class="font-label-md text-label-md">Niveau</span>
        </a>

        <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors {{ $currentRoute === 'personnel.classes.index' || str_starts_with($currentRoute, 'personnel.classes') ? 'nav-active' : '' }}" href="{{ route('personnel.classes.index') }}">
            <span class="material-symbols-outlined">meeting_room</span>
            <span class="font-label-md text-label-md">Classe</span>
        </a>

        <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors {{ $currentRoute === 'personnel.eleves.index' || str_starts_with($currentRoute, 'personnel.eleves') ? 'nav-active' : '' }}" href="{{ route('personnel.eleves.index') }}">
            <span class="material-symbols-outlined">group</span>
            <span class="font-label-md text-label-md">Elèves</span>
        </a>

        <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors {{ str_starts_with($currentRoute, 'personnel.enseignants') ? 'nav-active' : '' }}" href="{{ route('personnel.enseignants.index') }}">
            <span class="material-symbols-outlined">school</span>
            <span class="font-label-md text-label-md">Enseignants</span>
        </a>

        <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors {{ $currentRoute === 'personnel.matieres.index' || str_starts_with($currentRoute, 'personnel.matieres') ? 'nav-active' : '' }}" href="{{ route('personnel.matieres.index') }}">
            <span class="material-symbols-outlined">menu_book</span>
            <span class="font-label-md text-label-md">Matiere(s)</span>
        </a>

        <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors {{ str_starts_with($currentRoute, 'personnel.emploi-temps') && !str_starts_with($currentRoute, 'personnel.enseignants') ? 'nav-active' : '' }}" href="{{ route('personnel.emploi-temps.index') }}">
            <span class="material-symbols-outlined">calendar_month</span>
            <span class="font-label-md text-label-md">Emplois du temps</span>
        </a>

        <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors {{ str_starts_with($currentRoute, 'personnel.notes') ? 'nav-active' : '' }}" href="{{ route('personnel.notes.index') }}">
            <span class="material-symbols-outlined">rule</span>
            <span class="font-label-md text-label-md">Validation des Notes</span>
        </a>

        <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors {{ $currentRoute === 'personnel.bulletin.index' || str_starts_with($currentRoute, 'personnel.bulletin') ? 'nav-active' : '' }}" href="{{ route('personnel.bulletin.index') }}">
            <span class="material-symbols-outlined">description</span>
            <span class="font-label-md text-label-md">Bulletin</span>
        </a>

        <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors {{ $currentRoute === 'personnel.comptabilite.index' || str_starts_with($currentRoute, 'personnel.comptabilite') ? 'nav-active' : '' }}" href="{{ route('personnel.comptabilite.index') }}">
            <span class="material-symbols-outlined">payments</span>
            <span class="font-label-md text-label-md">Comptabilité</span>
        </a>

        <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors {{ str_starts_with($currentRoute, 'personnel.messages') ? 'nav-active' : '' }}" href="{{ route('personnel.messages.index') }}">
            <span class="material-symbols-outlined">chat</span>
            <span class="font-label-md text-label-md">Messages</span>
        </a>

        <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors {{ str_starts_with($currentRoute, 'personnel.parametres') ? 'nav-active' : '' }}" href="{{ route('personnel.parametres.index') }}">
            <span class="material-symbols-outlined">settings</span>
            <span class="font-label-md text-label-md">Paramètres</span>
        </a>

        <a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container transition-colors" href="{{ route('personnel.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <span class="material-symbols-outlined">logout</span>
            <span class="font-label-md text-label-md">Déconnexion</span>
        </a>

        <form id="logout-form" action="{{ route('personnel.logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </nav>
</aside>

<!-- Zone de contenu principale -->
<main class="ml-[260px] min-h-screen">
    <!-- Barre d'application supérieure -->
    <header class="h-16 bg-surface border-b border-outline-variant flex justify-between items-center px-8 fixed top-0 right-0 left-[260px] z-40">
        <div class="flex-1 max-w-xl">
            <div class="relative group">@yield('search')</div>
        </div>
        <div class="flex items-center gap-4 h-full">
            <!-- Bouton Notifications -->
            <div class="relative">
                <button id="notification-button" type="button" class="w-10 h-10 flex items-center justify-center text-on-surface-variant hover:bg-surface-container-high rounded-full transition-all relative" aria-label="Notifications">
                    <span class="material-symbols-outlined">notifications</span>
                    <span id="notification-badge" class="hidden absolute top-0 right-0 min-w-4 h-4 px-1 bg-alert-red text-white text-[10px] leading-4 rounded-full"></span>
                </button>
                <div id="notification-dropdown" class="hidden absolute right-0 top-12 z-50 w-96 overflow-hidden rounded-xl border border-outline-variant bg-surface-container-lowest shadow-xl">
                    <div class="flex justify-between border-b border-surface-container px-4 py-3">
                        <span class="font-semibold">Notifications</span>
                        <span id="notification-count" class="text-xs text-text-muted"></span>
                    </div>
                    <div id="notification-list" class="max-h-96 overflow-y-auto"></div>
                </div>
            </div>

            <!-- Séparateur vertical -->
            <div class="w-px h-6 bg-outline-variant"></div>

            <!-- Photo de profil avec icône utilisateur -->
            <div class="flex items-center gap-3 cursor-pointer hover:opacity-80 transition-opacity group">
                @php $authUser = Auth::user(); @endphp
                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center border border-primary/20 transition-all group-hover:bg-primary/20 overflow-hidden">
                    @if(!empty($authUser?->image))
                        <img
                            src="{{ asset('storage/'.$authUser->image) }}"
                            alt="Photo de profil"
                            class="w-full h-full object-cover"
                        >
                    @else
                        <span class="material-symbols-outlined text-primary">account_circle</span>
                    @endif
                </div>
                <span class="text-label-md text-on-surface font-medium hidden md:inline">{{ Auth::user()->prenom ?? 'Personnel' }}</span>
            </div>
        </div>
    </header>

    <!-- Zone principale -->
    <div class="p-8 mt-16 max-w-[1440px] mx-auto">
        {{-- Affichage des erreurs de validation --}}
        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-error/30 bg-error-container/30 px-4 py-3 text-error">
                <p class="font-label-md mb-2">Veuillez corriger les erreurs suivantes :</p>
                <ul class="list-disc pl-5 font-body-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @include('partials.subscription-grace-warning')

        @yield('content')
    </div>

    {{-- SweetAlert2 (via CDN) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // DELETE: confirmation SweetAlert2
        window.confirmDeleteSweet = function(event, form){
            event.preventDefault();
            Swal.fire({
                title: 'Supprimer ?',
                text: 'Cette action est irréversible.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
            return false;
        }

        // Success messages (CRUD)
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
                confirmButtonText: 'OK'
            });
        @endif
    </script>
</main>

<script>
    (() => {
        const button = document.getElementById('notification-button'), dropdown = document.getElementById('notification-dropdown'), list = document.getElementById('notification-list'), badge = document.getElementById('notification-badge'), count = document.getElementById('notification-count');
        const escapeHtml = value => String(value ?? '').replace(/[&<>'"]/g, char => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' }[char]));
        const icons = { payment: 'fa-credit-card', subscription: 'fa-calendar-check', bulletin: 'fa-file-lines', school_year: 'fa-graduation-cap', update: 'fa-arrows-rotate', staff: 'fa-users', teacher: 'fa-chalkboard-user', student: 'fa-user-plus', establishment: 'fa-school', security: 'fa-lock' };
        const render = payload => { const unread = payload.unread_count || 0; badge.textContent = unread > 99 ? '99+' : unread; badge.classList.toggle('hidden', unread === 0); count.textContent = unread ? `${unread} non lue${unread > 1 ? 's' : ''}` : 'À jour'; list.innerHTML = payload.notifications.length ? payload.notifications.map(item => `<div class="flex border-b border-surface-container hover:bg-surface-container-low ${item.read ? '' : 'bg-primary-fixed/30 font-semibold'}"><a href="${escapeHtml(item.url)}" class="flex min-w-0 flex-1 gap-3 px-4 py-3"><span class="mt-1 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-fixed text-primary"><i class="fa-solid ${icons[item.category] || 'fa-bell'}"></i></span><span class="min-w-0"><span class="block truncate text-sm">${escapeHtml(item.title)}</span><span class="mt-1 block truncate text-xs font-normal text-text-muted">${escapeHtml(item.preview)}</span><span class="mt-1 block text-[11px] font-normal text-text-muted">${escapeHtml(item.date)}${item.read ? '' : ' · Non lu'}</span></span></a><button type="button" class="notification-delete px-3 text-text-muted hover:text-alert-red" data-id="${item.id}" title="Supprimer"><i class="fa-solid fa-trash"></i></button></div>`).join('') : '<p class="px-4 py-6 text-center text-sm text-text-muted">Aucune notification.</p>'; };
        const load = () => fetch(@json(route('notifications.index')), { headers: { Accept: 'application/json' }, credentials: 'same-origin' }).then(r => r.ok ? r.json() : null).then(p => p && render(p)).catch(() => {});
        button?.addEventListener('click', () => { dropdown.classList.toggle('hidden'); load(); });
        list?.addEventListener('click', event => { const deleteButton = event.target.closest('.notification-delete'); if (!deleteButton) return; Swal.fire({ title: 'Supprimer cette notification ?', text: 'Cette action est irréversible.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Oui, supprimer', cancelButtonText: 'Annuler', confirmButtonColor: '#dc2626' }).then(result => { if (!result.isConfirmed) return; fetch(`/notifications/${deleteButton.dataset.id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, Accept: 'application/json' }, credentials: 'same-origin' }).then(r => { if (!r.ok) throw new Error(); return r.json(); }).then(() => { Swal.fire({ icon: 'success', title: 'Notification supprimée avec succès.', timer: 1800, showConfirmButton: false }); load(); }).catch(() => Swal.fire({ icon: 'error', title: 'Une erreur est survenue lors de la suppression de la notification.' })); }); });
        document.addEventListener('click', e => { if (!dropdown?.contains(e.target) && !button?.contains(e.target)) dropdown?.classList.add('hidden'); });
        load();
        window.setInterval(load, 20000);
    })();
</script>
@yield('scripts')
</body>
</html>