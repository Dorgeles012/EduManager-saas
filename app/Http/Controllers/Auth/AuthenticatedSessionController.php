<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Redirection automatique selon le rôle
        $user = $request->user();

        if ($user) {
            switch ($user->role) {
                case 'sadmin':
                case 'SADMIN':
                    return redirect()->route('sadmin.dashboard');

                case 'client':
                case 'CLIENT':
                    return redirect()->route('client.dashboard');

                case 'personnel':
                case 'PERSONNEL':
                    return redirect()->route('personnel.dashboard');

                case 'enseignant':
                case 'ENSEIGNANT':
                    return redirect()->route('enseignant.dashboard');

                case 'parent':
                case 'PARENT':
                    return redirect()->route('parent.dashboard');

                default:
                    Auth::guard('web')->logout();

                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('login')
                        ->withErrors(['email' => 'Rôle utilisateur non reconnu.']);
            }
        }

        return redirect()->route('login');

    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
