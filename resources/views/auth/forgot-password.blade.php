@extends('sadmin.layouts.guest')
@section('content')
<div class="w-full max-w-[440px]">
    <!-- Logo & Title Area -->
    <div class="text-center mb-10">
        <h1 class="text-3xl font-bold text-primary">EduManager </h1>
         <p class="text-[15px] text-on-surface-variant mt-1">Système de Gestion Centralisé</p>
    </div>

    <!-- Main Auth Card -->
     <div class="bg-surface-container-lowest login-card-shadow rounded-xl border border-outline-variant p-8 md:p-10">
        <div class="mb-7">
            <h2 class="font-headline-md text-[20px] text-on-surface mb-2 text-center">Mot de passe oublié</h2>
            <p class="font-body-md text-[14px] text-on-surface-variant">Entrez votre adresse email pour recevoir un lien de réinitialisation de mot de passe.</p>
        </div>

        <!-- Message de succès -->
        @if (session('status'))
            <div class="mb-6 p-4 bg-secondary-container/20 border border-secondary rounded-lg flex items-start gap-3">
                <span class="material-symbols-outlined text-secondary text-[20px]">check_circle</span>
                <div>
                    <p class="font-label-md text-[14px] text-on-secondary-container">Email envoyé avec succès !</p>
                    <p class="font-body-sm text-[13px] text-on-secondary-container/80 mt-1">{{ session('status') }}</p>
                </div>
            </div>
        @endif

        <!-- Message d'erreur -->
        @if ($errors->any())
            <div class="mb-6 p-4 bg-error-container/20 border border-error rounded-lg flex items-start gap-3">
                <span class="material-symbols-outlined text-error text-[20px]">error</span>
                <div>
                    <p class="font-label-md text-[14px] text-error">Une erreur est survenue</p>
                    @foreach ($errors->all() as $error)
                        <p class="font-body-sm text-[13px] text-error/80 mt-1">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('password.otp.send') }}" class="space-y-6" id="resetForm">


            @csrf

            <!-- Email Input -->
            <div class="space-y-2">
                <label class="font-label-md text-[14px] text-on-surface-variant block" for="email">
                    Adresse Email
                </label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-outline group-focus-within:text-primary transition-colors">
                        <span class="material-symbols-outlined text-[20px]">mail</span>
                    </div>
                    <input 
                        class="w-full pl-11 pr-4 py-3 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-md text-[15px] text-on-surface placeholder:text-text-muted focus:outline-none focus:ring-2 focus:ring-primary-container/20 focus:border-primary transition-all @error('email') border-error @enderror" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}"
                        placeholder="nom@ecole.com" 
                        required 
                        type="email"
                        autofocus
                    >
                </div>
                @error('email')
                    <p class="text-[13px] text-error flex items-center gap-1 mt-1">
                        <span class="material-symbols-outlined text-[16px]">error</span>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-primary-container text-white font-label-md text-[15px] py-4 px-6 rounded-lg flex items-center justify-center gap-2 hover:bg-primary transition-all shadow-md shadow-primary-container/10">
                <span>Envoyer le code de réinitialisation</span>
                <span class="material-symbols-outlined text-[20px]">send</span>
            </button>
        </form>

        <!-- Back Link -->
        <div class="mt-8 pt-6 border-t border-outline-variant text-center">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 font-label-md text-[14px] text-primary hover:underline transition-all group">
                <span class="material-symbols-outlined text-[18px] transition-transform group-hover:-translate-x-1">arrow_back</span>
                <span>Retour à la connexion</span>
            </a>
        </div>
    </div>

</div>
@endsection

@section('scripts')
@include('auth.partials.sweet-alerts')
<script>
    // Animation de soumission (optionnelle - améliore l'UX)
    const resetForm = document.getElementById('resetForm');
    if (resetForm) {
        resetForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span> <span>Envoi en cours...</span>';
                
                // Réactiver après 5 secondes si la page ne s'est pas rechargée (en cas d'erreur)
                setTimeout(() => {
                    if (submitBtn.disabled) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<span>Envoyer le lien de réinitialisation</span><span class="material-symbols-outlined text-[20px]">send</span>';
                    }
                }, 5000);
            }
        });
    }
</script>
@endsection
