<!DOCTYPE html>
<html class="light" lang="fr" style="">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>{{ $title ?? 'EduManager | Connexion' }}</title>

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

    @yield('content')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @yield('scripts')
</body>
</html>

