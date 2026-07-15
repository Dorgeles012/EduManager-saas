<!DOCTYPE html>
<html class="light" lang="fr" style="">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>{{ $title ?? 'EduManager | Tableau de bord' }}</title>

    <!-- Polices -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=Lexend:wght@400;600;700&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
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
        .sidebar-shadow { box-shadow: 4px 0 12px rgba(55,48,163,0.04); }
        .card-shadow { box-shadow: 0 4px 12px rgba(55,48,163,0.04); }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .chart-bar { transition: height 1s ease-in-out; }
    </style>
</head>
<body class="bg-background text-on-surface font-body-md">

    <!-- Barre latérale de navigation -->
    <aside class="fixed left-0 top-0 h-full w-[260px] bg-surface-container-lowest sidebar-shadow flex flex-col py-6 px-4 z-50">
        <div class="mb-10 px-2 flex items-center gap-3">
          <div class="w-10 h-10 bg-gradient-to-br from-primary to-primary/80 rounded-xl flex items-center justify-center shadow-lg shadow-primary/20">
        <span class="material-symbols-outlined text-white text-2xl" style="font-variation-settings: 'FILL' 1;">school</span>
    </div>
    <div>
        <h1 class="font-headline-lg text-headline-md font-bold text-primary tracking-tight">EduManager</h1>
    </div>
        </div>

        <nav class="flex-1 space-y-1">
            @php
                $activeClass = 'text-primary font-bold bg-primary-fixed rounded-lg transition-transform active:scale-95';
                $inactiveClass = 'text-on-surface-variant hover:bg-surface-subtle rounded-lg transition-all duration-200';
                $route = request()->route();
                $routeName = $route?->getName();
            @endphp

            <a class="flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('sadmin.dashboard') ? $activeClass : $inactiveClass }}" href="{{ route('sadmin.dashboard') }}">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">dashboard</span>
                <span class="font-label-md text-label-md">Accueil</span>
            </a>


            @if(Auth::check() && Auth::user()->role === 'SADMIN')
                <a class="flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('sadmin.index') ? $activeClass : $inactiveClass }}" href="{{ route('sadmin.index') }}">
                    <span class="material-symbols-outlined">admin_panel_settings</span>
                    <span class="font-label-md text-label-md">Super Administrateur</span>
                </a>
            @endif

            <a class="flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('sadmin.etablissement') || request()->routeIs('sadmin.etablissements.*') ? $activeClass : $inactiveClass }}" href="{{ route('sadmin.etablissement') }}">
                <span class="material-symbols-outlined">domain</span>
                <span class="font-label-md text-label-md">Établissements</span>
            </a>

            <a class="flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('sadmin.clients.*') || request()->routeIs('sadmin.clients.index') ? $activeClass : $inactiveClass }}" href="{{ route('sadmin.clients.index') }}">
                <span class="material-symbols-outlined">person_add</span>
                <span class="font-label-md text-label-md">Clients</span>
            </a>

            <a class="flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('sadmin.abonnement') ? $activeClass : $inactiveClass }}" href="{{ route('sadmin.abonnement') }}">
                <span class="material-symbols-outlined">card_membership</span>
                <span class="font-label-md text-label-md">Abonnements</span>
            </a>

            <a class="flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('sadmin.notifications') ? $activeClass : $inactiveClass }}" href="{{ route('sadmin.notifications') }}">
                <span class="material-symbols-outlined">notifications</span>
                <span class="font-label-md text-label-md">Notifications</span>
            </a>

        </nav>

        <div class="pt-6 border-t border-outline-variant mt-auto space-y-1">
            <a class="flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('sadmin.parametres') ? $activeClass : $inactiveClass }}" href="{{ route('sadmin.parametres') }}">
                <span class="material-symbols-outlined">settings</span>
                <span class="font-label-md text-label-md">Paramètres</span>
            </a>

            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 text-on-surface-variant hover:bg-surface-subtle rounded-lg bg-transparent">
                    <span class="material-symbols-outlined">logout</span>
                    <span class="font-label-md text-label-md">Deconnexion</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Zone de contenu principale -->
    <main class="ml-[260px] min-h-screen">
        <!-- Barre d'application supérieure -->
        <header class="h-16 bg-surface border-b border-outline-variant flex justify-between items-center px-8 fixed top-0 right-0 left-[260px] z-40">
            <div class="flex-1 max-w-xl">
                <div class="relative group"></div>
            </div>
            <div class="flex items-center gap-4 h-full">
                <!-- Bouton Notifications -->
                <div class="relative">
                <button id="notification-button" type="button" class="w-10 h-10 flex items-center justify-center text-on-surface-variant hover:bg-surface-container-high rounded-full transition-all relative" aria-label="Notifications">
                    <span class="material-symbols-outlined">notifications</span>
                    <span id="notification-badge" class="hidden absolute top-0 right-0 min-w-4 h-4 px-1 bg-alert-red text-white text-[10px] leading-4 rounded-full"></span>
                </button>
                <div id="notification-dropdown" class="hidden absolute right-0 top-12 z-50 w-96 overflow-hidden rounded-xl border border-outline-variant bg-surface-container-lowest shadow-xl"><div class="flex justify-between border-b border-surface-container px-4 py-3"><span class="font-semibold">Notifications</span><span id="notification-count" class="text-xs text-text-muted"></span></div><div id="notification-list" class="max-h-96 overflow-y-auto"></div></div>
                </div>
                
                <!-- Séparateur vertical -->
                <div class="w-px h-6 bg-outline-variant"></div>
                
                <!-- Photo de profil avec icône utilisateur -->
                <div class="flex items-center gap-3 cursor-pointer hover:opacity-80 transition-opacity group">
                    <!-- Avatar (photo si dispo sinon icône) -->
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
                    <!-- Nom de l'utilisateur -->
                    <span class="text-label-md text-on-surface font-medium hidden md:inline">{{ Auth::user()->prenom ?? 'Admin' }}</span>
                </div>
            </div>
        </header>

        <!-- Zone principale -->
        <div class="p-8 mt-16 max-w-[1440px] mx-auto">
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
            button?.addEventListener('click', () => { dropdown.classList.toggle('hidden'); load(); }); list?.addEventListener('click', event => { const deleteButton = event.target.closest('.notification-delete'); if (!deleteButton) return; Swal.fire({ title: 'Supprimer cette notification ?', text: 'Cette action est irréversible.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Oui, supprimer', cancelButtonText: 'Annuler', confirmButtonColor: '#dc2626' }).then(result => { if (!result.isConfirmed) return; fetch(`/notifications/${deleteButton.dataset.id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, Accept: 'application/json' }, credentials: 'same-origin' }).then(r => { if (!r.ok) throw new Error(); return r.json(); }).then(() => { Swal.fire({ icon: 'success', title: 'Notification supprimée avec succès.', timer: 1800, showConfirmButton: false }); load(); }).catch(() => Swal.fire({ icon: 'error', title: 'Une erreur est survenue lors de la suppression de la notification.' })); }); }); document.addEventListener('click', e => { if (!dropdown?.contains(e.target) && !button?.contains(e.target)) dropdown?.classList.add('hidden'); }); load(); window.setInterval(load, 20000);
        })();
    </script>
    @yield('scripts')
</body>
</html>
