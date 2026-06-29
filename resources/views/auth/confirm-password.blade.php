@extends('sadmin.layouts.guest')

@section('content')
<main class="relative z-10 w-full max-w-[440px]">
    <div class="bg-surface-container-lowest login-card-shadow rounded-xl border border-outline-variant p-8">
        <h1 class="text-2xl font-bold text-primary mb-2">Confirmer votre mot de passe</h1>
        <p class="text-sm text-on-surface-variant mb-6">Cette zone est sécurisée. Confirmez votre mot de passe pour continuer.</p>
        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
            @csrf
            <div>
                <label for="password" class="block text-sm font-medium mb-2">Mot de passe</label>
                <input id="password" type="password" name="password" required autofocus autocomplete="current-password"
                       class="block w-full px-4 py-3 border border-outline-variant rounded-lg focus:ring-primary focus:border-primary">
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <button type="submit" class="w-full bg-primary text-on-primary px-6 py-3 rounded-lg font-semibold">Confirmer</button>
        </form>
    </div>
</main>
@endsection
