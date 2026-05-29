@extends('sadmin.layouts.app')

@section('content')
<div class="mb-6">
    <a href="{{ route('sadmin.parametres') }}" class="inline-flex items-center gap-2 text-on-surface-variant hover:text-primary transition-colors">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        <span class="text-[12px] font-medium">Retour aux paramètres</span>
    </a>
</div>

<div class="flex flex-col lg:flex-row gap-6">
    <aside class="w-full lg:w-80 space-y-4">
        <div class="bg-surface-container-lowest rounded-2xl p-5 card-shadow border border-surface-subtle/50">
            <div class="flex items-start gap-4">
                <div class="p-2 bg-gradient-to-br from-secondary-container to-secondary/20 rounded-xl text-on-secondary-container">
                    <span class="material-symbols-outlined text-xl">lock</span>
                </div>
                <div class="flex-1">
                    <h3 class="font-headline-lg text-[16px] mb-1">Sécurité</h3>
                    <p class="text-[12px] text-text-muted mb-4">Mettez à jour votre mot de passe.</p>
                </div>
            </div>
        </div>
    </aside>

    <div class="flex-1 space-y-6">
        <section class="bg-surface-container-lowest rounded-2xl p-6 card-shadow border border-surface-subtle/50">
            <div class="flex items-center gap-3 mb-5 border-b border-outline-variant/30 pb-3">
                <div class="bg-gradient-to-br from-primary/10 to-primary-container/10 p-1.5 rounded-lg">
                    <span class="material-symbols-outlined text-primary text-[18px]">key</span>
                </div>
                <h3 class="font-headline-lg text-[16px] text-on-surface">Changer le mot de passe</h3>
            </div>

            <form id="passwordForm" method="POST" action="{{ route('sadmin.parametres.password.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="space-y-1.5">
                    <label class="text-[11px] text-on-surface-variant font-medium">Mot de passe actuel</label>
                    <div class="relative">
                        <input
                            name="current_password"
                            type="password"
                            class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-[13px] pr-10"
                            placeholder="Entrez votre mot de passe actuel"
                        >
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant cursor-pointer hover:text-primary toggle-password text-[18px]">
                            visibility
                        </span>
                    </div>
                    @error('current_password')
                        <div class="text-[12px] text-alert-red">{{ $message }}</div>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="text-[11px] text-on-surface-variant font-medium">Nouveau mot de passe</label>
                    <div class="relative">
                        <input
                            name="password"
                            type="password"
                            class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-[13px] pr-10"
                            placeholder="••••••••"
                        >
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant cursor-pointer hover:text-primary toggle-password text-[18px]">
                            visibility
                        </span>
                    </div>
                    @error('password')
                        <div class="text-[12px] text-alert-red">{{ $message }}</div>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="text-[11px] text-on-surface-variant font-medium">Confirmation du mot de passe</label>
                    <div class="relative">
                        <input
                            name="password_confirmation"
                            type="password"
                            class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-[13px] pr-10"
                            placeholder="Confirmez votre nouveau mot de passe"
                        >
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant cursor-pointer hover:text-primary toggle-password text-[18px]">
                            visibility
                        </span>
                    </div>
                    @error('password_confirmation')
                        <div class="text-[12px] text-alert-red">{{ $message }}</div>
                    @enderror
                </div>

                <div class="md:col-span-2 flex justify-end pt-3">
                    <button class="px-6 py-2 bg-gradient-to-r from-primary to-primary-container text-on-primary font-label-md text-[12px] rounded-lg" type="submit">
                        Mettre à jour le mot de passe
                    </button>
                </div>
            </form>
        </section>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(icon => {
        icon.addEventListener('click', function () {
            const input = this.previousElementSibling;
            if (!input) return;

            if (input.type === 'password') {
                input.type = 'text';
                this.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                this.textContent = 'visibility';
            }
        });
    });
</script>
@endsection

