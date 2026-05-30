<!DOCTYPE html>
<html class="light" lang="fr" style="">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>{{ $title ?? 'EduManager | Tableau de bord' }}</title>

    <!-- Polices -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=Lexend:wght@400;600;700&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">

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
            <div class="w-10 h-10 bg-primary-container rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-on-primary-container" style="font-variation-settings: 'FILL' 1;">school</span>
            </div>
            <div>
                <h1 class="font-headline-md text-headline-md font-bold text-primary">EduManager</h1>
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

            <a class="flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('sadmin.etablissement') || request()->routeIs('sadmin.etablissements.*') ? $activeClass : $inactiveClass }}" href="{{ route('sadmin.etablissement') }}">


                <span class="material-symbols-outlined">domain</span>
                <span class="font-label-md text-label-md">Établissements</span>
            </a>

            <a class="flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('sadmin.clients.*') || request()->routeIs('sadmin.clients.index') ? $activeClass : $inactiveClass }}" href="{{ route('sadmin.clients.index') }}">


                <span class="material-symbols-outlined">add_business</span>
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
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <button class="p-2 text-on-surface-variant hover:bg-surface-container-high rounded-full transition-all">
                        <span class="material-symbols-outlined">notifications</span>
                    </button>
                    <button class="p-2 text-on-surface-variant hover:bg-surface-container-high rounded-full transition-all"></button>

                    <div class="w-8 h-8 rounded-full bg-surface-container-highest overflow-hidden cursor-pointer border border-outline-variant">
                        <img class="w-full h-full object-cover" data-alt="Portrait professionnel" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAJ5sCn6ItxaWtj3lx_CPCmWWhg3X84zBOu5DRfsMBFPLHUzbEdl_LZeJivp1RUoCanz79520Nemj-sovggKyQ54x2gVOtvxe9OyPRowolySik3TMFtSg1Gg70CfTiixdMHFwKogFz1MdZUhCzw032S0C6L2_apuQszJtF6gb0PA30B0syA9m41oIEPSee4cbuQJWTytqHf3a_-Ew1TYaV3gPkrexbhE6ASsWSHzGyubS8gzjX9szBt2N7nKBA8UJ9C-1MQfHp-OAi9">
                    </div>
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

    @yield('scripts')
</body>
</html>

