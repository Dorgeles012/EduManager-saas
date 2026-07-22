<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\RoleDashboardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Throwable;

class ResetPasswordController extends Controller
{
    public function create(Request $request): View
    {
        // Blade reçoit $request pour récupérer token/email
        return view('auth.reset-password', ['request' => $request]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $tokenRaw = (string) $request->input('token');
        $email = (string) $request->input('email');

        $tokenHash = hash('sha256', $tokenRaw);

        $row = \DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$row) {
            return back()->withErrors(['token' => 'Token invalide ou expiré.']);
        }

        $createdAt = $row->created_at ? \Carbon\Carbon::parse($row->created_at) : null;
        $expireMinutes = (int) env('AUTH_PASSWORD_RESET_EXPIRE', 60);

        $isExpired = $createdAt ? $createdAt->copy()->addMinutes($expireMinutes)->isPast() : true;

        if ($isExpired || !hash_equals((string) $row->token, (string) $tokenHash)) {
            return back()->withErrors(['token' => 'Token invalide ou expiré.']);
        }

        $user = User::query()->where('email', $email)->first();
        if (!$user) {
            // Suppression token quand même
            \DB::table('password_reset_tokens')->where('email', $email)->delete();
            return back()->withErrors(['email' => 'Email introuvable.']);
        }

        try {
            $user->forceFill([
                'password' => Hash::make($request->input('password')),
                'remember_token' => Str::random(60),
            ])->save();
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors(['password' => 'Impossible de mettre à jour le mot de passe.']);
        }

        // Suppression token après utilisation
        \DB::table('password_reset_tokens')->where('email', $email)->delete();

        Auth::login($user);

        $routeName = app(RoleDashboardService::class)->routeNameFor($user);
        if (! $routeName) {
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'Rôle utilisateur non autorisé.']);
        }

        return redirect()->route($routeName)->with('status', 'Mot de passe mis à jour avec succès. Vous êtes connecté(e).');
    }
}

