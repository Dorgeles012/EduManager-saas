@php
    /** @var \App\Models\Subscription|null $subscription */
    $role = strtolower(trim((string) ($user?->role ?? '')));
    $roleLabel = match ($role) {
        'client' => 'votre espace Client',
        'personnel' => "votre espace Personnel",
        'enseignant' => 'votre espace Enseignant',
        'parent' => 'votre espace Parent',
        'eleve' => 'votre espace Élève',
        default => 'votre espace',
    };

    $dateFin = $subscription?->date_fin;
    $dateFinGrace = $subscription?->dateFinGrace();
    $expired = $subscription?->isExpired() ?? false;
    $inGrace = $subscription?->isWithinGracePeriod() ?? false;
    $graceDaysRemaining = $subscription?->remainingGraceDays();
    $statusLabel = $expired ? 'Expiré' : ($subscription?->abonnement_status ?? 'Inactif');
    $statusLabel = match (strtolower((string) $statusLabel)) {
        'actif' => 'Actif',
        'paye' => 'En attente de validation',
        'en_attente' => 'En attente de validation',
        'expire' => 'Expiré',
        default => ucfirst((string) ($subscription?->abonnement_status ?? 'Inactif')),
    };
@endphp

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Abonnement expiré - EduManager</title>
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Lexend:wght@600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #f9f9ff 0%, #e7eeff 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1rem; }
        .card { background: #ffffff; border-radius: 20px; box-shadow: 0 20px 50px rgba(55, 48, 163, 0.12); max-width: 520px; width: 100%; overflow: hidden; }
        .icon-badge { width: 88px; height: 88px; border-radius: 9999px; display: flex; align-items: center; justify-content: center; margin: 0 auto; background: #fee2e2; }
    </style>
</head>
<body>
    <div class="card">
        <div class="p-8">
            <div class="icon-badge">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m0-6v4m-7 7h14a2 2 0 002-2V9.828a2 2 0 00-1.172-1.828l-7-3.1a2 2 0 00-1.656 0l-7 3.1A2 2 0 003 9.828V19a2 2 0 002-2zm8-3a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>

            <h1 class="text-center mt-6 text-2xl font-bold text-gray-800" style="font-family: 'Lexend', sans-serif;">
                Abonnement expiré
            </h1>

            <p class="text-center mt-3 text-gray-600 leading-relaxed">
                Votre abonnement EduManager n'est plus actif.
                L'accès à <strong>{{ $roleLabel }}</strong> est actuellement bloqué.
            </p>

            <p class="text-center mt-2 text-gray-500 text-sm">
                @if($inGrace)
                    Votre période de grâce est active.
                    Veuillez renouveler votre abonnement avant la fin de la période de grâce pour conserver l'accès.
                @else
                    Votre période de grâce de {{ \App\Models\Subscription::GRACE_DAYS }} jours est terminée.
                    Pour continuer à utiliser EduManager, veuillez renouveler votre abonnement.
                @endif
            </p>

            <div class="mt-6 text-center">
                <a href="{{ route('client.abonnement.index') }}" class="inline-flex items-center justify-center rounded-full bg-primary px-5 py-3 text-sm font-semibold text-white hover:bg-primary-container transition">Renouveler mon abonnement</a>
            </div>

            <div class="mt-8 rounded-xl border border-gray-200 bg-gray-50 p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Statut</span>
                    <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-red-600">
                        <span class="h-2 w-2 rounded-full bg-red-500"></span>
                        {{ $statusLabel }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Date d'expiration</span>
                    <span class="text-sm font-medium text-gray-800">
                        {{ $dateFin?->format('d/m/Y') ?? '—' }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Fin de la période de grâce</span>
                    <span class="text-sm font-medium text-gray-800">
                        {{ $dateFinGrace?->format('d/m/Y') ?? '—' }}
                    </span>
                </div>
                @if($role === 'client')
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Compte</span>
                        <span class="text-sm font-medium text-gray-800">{{ $user?->name ?? 'Client' }}</span>
                    </div>
                @endif
            </div>

            <div class="mt-6 text-center">
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                        Se déconnecter
                    </button>
                </form>
            </div>

            <p class="text-center mt-6 text-xs text-gray-400">
                © {{ date('Y') }} EduManager - Tous droits réservés.
            </p>
        </div>
    </div>
</body>
</html>

