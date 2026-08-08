<?php

namespace App\Http\Controllers\Eleve;

use App\Http\Controllers\Controller;
use App\Models\Eleve;
use App\Models\User;
use App\Services\RoleDashboardService;
use App\Services\SubscriptionStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Authentification spécifique pour les élèves.
 *
 * Identifiant = matricule
 * Mot de passe par défaut = 12345678
 */
class EleveAuthController extends Controller
{
    public function create(): View
    {
        return view('auth.eleve-login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'matricule' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $eleve = Eleve::query()
            ->where('matricule', $credentials['matricule'])
            ->first();

        // Aucun élève avec ce matricule
        if (! $eleve) {
            throw ValidationException::withMessages([
                'matricule' => 'Matricule introuvable.',
            ]);
        }

        // Récupérer le compte utilisateur (rôle élève) lié
        $user = User::query()
            ->where('tenant_id', $eleve->tenant_id)
            ->where('eleve_id', $eleve->id)
            ->whereRaw('LOWER(role) = ?', ['eleve'])
            ->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'matricule' => 'Aucun compte élève associé à ce matricule.',
            ]);
        }

        if (in_array(strtolower((string) ($user->statut ?? '')), ['bloqué', 'bloque', 'blocked'], true)) {
            throw ValidationException::withMessages([
                'matricule' => 'Votre compte est bloqué, veuillez contacter l’administration.',
            ]);
        }

        if (! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'Mot de passe incorrect.',
            ]);
        }

Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        // Vérification immédiate de l'abonnement du tenant après authentification.
        $subscriptionStatus = app(SubscriptionStatusService::class);
        $subscription = $subscriptionStatus->subscriptionForUser($user);

        if (! $subscriptionStatus->isExempt($user)) {
            if (! $subscription && strtolower(trim((string) $user->role)) === 'client') {
                return redirect()->route('client.abonnement.index');
            }

            if (! $subscriptionStatus->isActiveForUser($user)) {
                return redirect()->route('subscription.expired');
            }
        }

        return redirect()->route('eleve.dashboard');
    }
}
