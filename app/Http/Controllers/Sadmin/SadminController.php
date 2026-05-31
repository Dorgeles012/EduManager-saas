<?php

namespace App\Http\Controllers\Sadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class SadminController extends Controller
{
    public function index(): View
    {
        $sadmins = User::query()
            ->where('role', 'SADMIN')
            ->latest('created_at')
            ->get();

        $stats = [
            'total' => $sadmins->count(),
        ];

        return view('sadmin.sadmin', compact('sadmins', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'telephone' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8'],
            'password_confirmation' => ['required', 'string', 'min:8', 'same:password'],
        ]);

        $user = new User();
        $user->tenant_id = Auth::user()?->tenant_id ?? 1;
        $user->nom = $validated['nom'];
        $user->prenom = $validated['prenom'];
        $user->email = $validated['email'];
        $user->telephone = $validated['telephone'];
        $user->password = Hash::make($validated['password']);
        $user->role = 'SADMIN';
        $user->statut = 'active';

        $user->save();

        return redirect()
            ->route('sadmin.index')
            ->with('success', 'Super Administrateur créé avec succès.');
    }

    public function edit(User $sadmin): View
    {
        abort_unless($sadmin->role === 'SADMIN', 403);

        return view('sadmin.sadmin', [
            'sadmins' => User::query()->where('role', 'SADMIN')->latest('created_at')->get(),
            'editUser' => $sadmin,
            'stats' => [
                'total' => User::query()->where('role', 'SADMIN')->count(),
            ],
        ]);
    }

    public function update(Request $request, User $sadmin): RedirectResponse
    {
        abort_unless($sadmin->role === 'SADMIN', 403);

        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($sadmin->id),
            ],
            'telephone' => ['required', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $sadmin->nom = $validated['nom'];
        $sadmin->prenom = $validated['prenom'];
        $sadmin->email = $validated['email'];
        $sadmin->telephone = $validated['telephone'];

        if (!empty($validated['password'])) {
            $sadmin->password = Hash::make($validated['password']);
        }

        $sadmin->save();

        return redirect()
            ->route('sadmin.index')
            ->with('success', 'Super Administrateur mis à jour avec succès.');
    }

    public function destroy(User $sadmin): RedirectResponse
    {
        abort_unless($sadmin->role === 'SADMIN', 403);

        $sadmin->delete();

        return redirect()
            ->route('sadmin.index')
            ->with('success', 'Super Administrateur supprimé avec succès.');
    }
}

