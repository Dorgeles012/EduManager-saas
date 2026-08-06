@extends('eleve.layouts.app')

@section('title', 'Modifier mon mot de passe')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="mb-6 text-center">
        <div class="w-16 h-16 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-4">
            <span class="material-symbols-outlined text-primary text-3xl">lock_reset</span>
        </div>
        <h2 class="font-headline-lg text-headline-lg text-primary">Sécurité du compte</h2>
        <p class="text-sm text-on-surface-variant mt-1">
            Pour des raisons de sécurité, vous devez modifier votre mot de passe avant de continuer.
        </p>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-lg border border-success-green/20 bg-success-green/10 px-4 py-3 text-success-green">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant p-6">
        <form method="POST" action="{{ route('eleve.password.update') }}" class="space-y-5">
            @csrf

            <div class="space-y-1">
                <label class="font-label-md text-label-md text-on-surface-variant">Mot de passe actuel</label>
                <input
                    type="password"
                    name="current_password"
                    required
                    autocomplete="current-password"
                    class="w-full bg-white border border-outline-variant rounded-lg px-4 py-2.5 text-body-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none"
                >
                <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
            </div>

            <div class="space-y-1">
                <label class="font-label-md text-label-md text-on-surface-variant">Nouveau mot de passe</label>
                <input
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    class="w-full bg-white border border-outline-variant rounded-lg px-4 py-2.5 text-body-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none"
                >
                <p class="text-xs text-text-muted">Minimum 8 caractères.</p>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="space-y-1">
                <label class="font-label-md text-label-md text-on-surface-variant">Confirmer le nouveau mot de passe</label>
                <input
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    class="w-full bg-white border border-outline-variant rounded-lg px-4 py-2.5 text-body-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none"
                >
            </div>

            <button type="submit" class="w-full bg-primary text-on-primary px-4 py-3 rounded-lg font-label-md hover:bg-primary/90 transition-colors flex items-center justify-center gap-2">
                <span class="material-symbols-outlined">check_circle</span>
                Modifier mon mot de passe
            </button>
        </form>
    </div>
</div>
@endsection
