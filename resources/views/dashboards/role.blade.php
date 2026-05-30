<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | EduManager</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    <main class="mx-auto flex min-h-screen max-w-5xl flex-col px-6 py-8">
        <header class="flex items-center justify-between border-b border-slate-200 pb-5">
            <div>
                <p class="text-sm font-semibold text-indigo-700">EduManager</p>
                <h1 class="mt-1 text-2xl font-bold">{{ $title }}</h1>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                    Déconnexion
                </button>
            </form>
        </header>

        <section class="flex flex-1 items-center">
            <div>
                <h2 class="text-xl font-semibold">Bienvenue, {{ auth()->user()?->prenom ?? auth()->user()?->nom ?? 'utilisateur' }}</h2>
                <p class="mt-2 max-w-xl text-slate-600">
                    Votre session est active et vous avez été redirigé vers l'espace correspondant à votre rôle.
                </p>
            </div>
        </section>
    </main>
</body>
</html>
