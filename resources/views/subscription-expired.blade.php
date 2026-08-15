@php
    /** @var \App\Models\Subscription|null $subscription */
    $role = strtolower(trim((string) ($user?->role ?? '')));
    $roleLabel = match ($role) {
        'client'     => 'votre espace Client',
        'personnel'  => "votre espace Personnel",
        'enseignant' => 'votre espace Enseignant',
        'parent'     => 'votre espace Parent',
        'eleve'      => 'votre espace Élève',
        default      => 'votre espace',
    };

    $dateFin          = $subscription?->date_fin;
    $dateFinGrace     = $subscription?->dateFinGrace();
    $inGrace          = $subscription?->isWithinGracePeriod() ?? false;
    $graceExpired     = $subscription?->isGraceExpired() ?? false;
    $graceDaysLeft    = $subscription?->remainingGraceDays();
    $isPending        = in_array($subscription?->abonnement_status, ['en_attente', 'paye'], true);

    $statusLabel = match (strtolower((string) ($subscription?->abonnement_status ?? ''))) {
        'actif'      => 'Actif',
        'paye'       => 'En attente de validation',
        'en_attente' => 'En attente de paiement',
        'expire'     => 'Expiré',
        default      => ucfirst((string) ($subscription?->abonnement_status ?? 'Inactif')),
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
        .card { background: #ffffff; border-radius: 20px; box-shadow: 0 20px 50px rgba(55, 48, 163, 0.12); max-width: 540px; width: 100%; overflow: hidden; }
        .icon-badge-red    { width: 88px; height: 88px; border-radius: 9999px; display: flex; align-items: center; justify-content: center; margin: 0 auto; background: #fee2e2; }
        .icon-badge-amber  { width: 88px; height: 88px; border-radius: 9999px; display: flex; align-items: center; justify-content: center; margin: 0 auto; background: #fef3c7; }
        .icon-badge-blue   { width: 88px; height: 88px; border-radius: 9999px; display: flex; align-items: center; justify-content: center; margin: 0 auto; background: #dbeafe; }
        .btn-primary { display: inline-flex; align-items: center; justify-content: center; border-radius: 9999px; background: #1f108e; padding: 0.75rem 1.5rem; font-size: 0.875rem; font-weight: 600; color: white; transition: background 0.2s; text-decoration: none; }
        .btn-primary:hover { background: #2d1fad; }
        .btn-secondary { display: inline-flex; align-items: center; gap: 0.5rem; border-radius: 0.5rem; border: 1px solid #d1d5db; background: white; padding: 0.625rem 1.25rem; font-size: 0.875rem; font-weight: 600; color: #374151; transition: background 0.2s; }
        .btn-secondary:hover { background: #f9fafb; }
    </style>
</head>
<body>
    <div class="card">
        <div class="p-8">

            {{-- ═══════════════════════════════════════════════════════
                 CAS 1 : ABONNEMENT EN ATTENTE DE VALIDATION
                 (ne devrait normalement pas atterrir ici, mais sécurité)
            ═══════════════════════════════════════════════════════ --}}
            @if($isPending)
                <div class="icon-badge-blue">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h1 class="text-center mt-6 text-2xl font-bold text-gray-800" style="font-family: 'Lexend', sans-serif;">
                    Paiement en attente de validation
                </h1>
                <p class="text-center mt-3 text-gray-600 leading-relaxed">
                    Votre paiement a bien été enregistré. L'administrateur doit valider votre abonnement pour activer toutes les fonctionnalités.
                </p>
                <div class="mt-6 text-center">
                    @if($role === 'client')
                        <a href="{{ route('client.abonnement.index') }}" class="btn-primary">Voir mon abonnement</a>
                    @endif
                </div>

            {{-- ═══════════════════════════════════════════════════════
                 CAS 2 : EN PÉRIODE DE GRÂCE (encore du temps restant)
            ═══════════════════════════════════════════════════════ --}}
            @elseif($inGrace)
                <div class="icon-badge-amber">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <h1 class="text-center mt-6 text-2xl font-bold text-gray-800" style="font-family: 'Lexend', sans-serif;">
                    Abonnement expiré — Période de grâce
                </h1>
                <p class="text-center mt-3 text-gray-600 leading-relaxed">
                    Votre abonnement EduManager a expiré le <strong>{{ $dateFin?->format('d/m/Y') ?? '—' }}</strong>.
                    Vous bénéficiez d'une période de grâce de <strong>{{ \App\Models\Subscription::GRACE_DAYS }} jours</strong>.
                </p>
                @if($graceDaysLeft !== null && $graceDaysLeft > 0)
                    <p class="text-center mt-2 text-amber-700 font-semibold text-sm">
                        ⏰ Il vous reste <strong>{{ $graceDaysLeft }} jour{{ $graceDaysLeft > 1 ? 's' : '' }}</strong> avant le blocage de votre compte.
                    </p>
                @else
                    <p class="text-center mt-2 text-red-600 font-semibold text-sm">
                        ⚠️ C'est le dernier jour de votre période de grâce. Renouvelez maintenant !
                    </p>
                @endif
                <div class="mt-6 text-center">
                    @if($role === 'client')
                        <a href="{{ route('client.abonnement.index') }}" class="btn-primary">Renouveler maintenant</a>
                    @endif
                </div>

            {{-- ═══════════════════════════════════════════════════════
                 CAS 3 : GRÂCE EXPIRÉE — ACCÈS BLOQUÉ
            ═══════════════════════════════════════════════════════ --}}
            @else
                <div class="icon-badge-red">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </div>
                <h1 class="text-center mt-6 text-2xl font-bold text-red-700" style="font-family: 'Lexend', sans-serif;">
                    Accès bloqué
                </h1>
                <p class="text-center mt-3 text-gray-700 leading-relaxed font-medium">
                    Votre abonnement a expiré. Votre période de grâce de {{ \App\Models\Subscription::GRACE_DAYS }} jours est terminée.
                </p>
                <p class="text-center mt-2 text-gray-500 text-sm leading-relaxed">
                    L'accès à <strong>{{ $roleLabel }}</strong> est actuellement bloqué.
                    Veuillez contacter l'administrateur pour renouveler votre abonnement.
                </p>
                <div class="mt-6 flex flex-wrap justify-center gap-3">
                    @if($role === 'client')
                        <a href="{{ route('client.abonnement.index') }}" class="btn-primary">
                            Renouveler mon abonnement
                        </a>
                    @endif
                </div>
            @endif

            {{-- Informations abonnement --}}
            @if($subscription)
            <div class="mt-8 rounded-xl border border-gray-200 bg-gray-50 p-5 space-y-3">
                @if($dateFin)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Date d'expiration</span>
                    <span class="text-sm font-medium text-gray-800">{{ $dateFin->format('d/m/Y') }}</span>
                </div>
                @endif
                @if($dateFinGrace && ! $isPending)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Fin de la période de grâce</span>
                    <span class="text-sm font-medium text-gray-800">{{ $dateFinGrace->format('d/m/Y') }}</span>
                </div>
                @endif
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Statut</span>
                    <span class="inline-flex items-center gap-1.5 text-sm font-semibold
                        {{ $inGrace ? 'text-amber-600' : ($isPending ? 'text-blue-600' : 'text-red-600') }}">
                        <span class="h-2 w-2 rounded-full
                            {{ $inGrace ? 'bg-amber-500' : ($isPending ? 'bg-blue-500' : 'bg-red-500') }}"></span>
                        {{ $statusLabel }}
                    </span>
                </div>
                @if($role === 'client')
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Compte</span>
                    <span class="text-sm font-medium text-gray-800">{{ $user?->nom }} {{ $user?->prenom }}</span>
                </div>
                @endif
            </div>
            @endif

            {{-- Déconnexion --}}
            <div class="mt-6 text-center">
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Se déconnecter
                    </button>
                </form>
            </div>

            <p class="text-center mt-6 text-xs text-gray-400">
                © {{ date('Y') }} EduManager — Tous droits réservés.
            </p>
        </div>
    </div>
</body>
</html>