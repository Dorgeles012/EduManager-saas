@extends('sadmin.layouts.app')

@section('content')
<div class="mb-6">
    <a href="{{ route('sadmin.parametres') }}" class="inline-flex items-center gap-2 text-on-surface-variant hover:text-primary transition-colors">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        <span class="text-[12px] font-medium">Retour aux paramètres</span>
    </a>
</div>

<div class="flex flex-col lg:flex-row gap-6">
    {{-- Profile Sidebar --}}
    <aside class="w-full lg:w-80 space-y-4">
        <div class="bg-surface-container-lowest rounded-2xl p-5 card-shadow border border-surface-subtle/50 flex flex-col items-center text-center">
            <div class="relative mb-4">
                <!-- Avatar (photo si dispo sinon icône) -->
                @php $profileUser = $user; @endphp
                <div class="h-24 w-24 rounded-full bg-primary/10 flex items-center justify-center border-4 border-primary/20 shadow-md overflow-hidden">
                    @if(!empty($profileUser?->image))
                        <img
                            src="{{ asset('storage/'.$profileUser->image) }}"
                            alt="Photo de profil"
                            class="w-full h-full object-cover"
                        >
                    @else
                        <span class="material-symbols-outlined text-primary text-5xl">account_circle</span>
                    @endif
                </div>
            </div>

            <!-- Affichage du nom et prénom -->
            <h3 class="font-headline-lg text-[18px] text-primary">{{ trim(($user?->nom ?? '').' '.($user?->prenom ?? '')) ?: 'Utilisateur' }}</h3>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-gradient-to-r from-primary/10 to-primary-container/10 text-primary mt-1">
                {{ $user?->role ?? 'USER' }}
            </span>

            <div class="w-full mt-6 space-y-1 text-left">
                <a class="flex items-center gap-3 px-3 py-2.5 bg-gradient-to-r from-primary/5 to-primary-container/5 text-primary font-semibold rounded-xl cursor-pointer" href="#">
                    <span class="material-symbols-outlined text-[18px]">account_circle</span>
                    <span class="text-[12px] font-medium">Mon profil</span>
                </a>
            </div>
        </div>
    </aside>

    {{-- Profile Form Content --}}
    <div class="flex-1 space-y-6">
        <section class="bg-surface-container-lowest rounded-2xl p-6 card-shadow border border-surface-subtle/50">
            <div class="flex items-center gap-3 mb-5 border-b border-outline-variant/30 pb-3">
                <div class="bg-gradient-to-br from-primary/10 to-primary-container/10 p-1.5 rounded-lg">
                    <span class="material-symbols-outlined text-primary text-[18px]">person</span>
                </div>
                <h3 class="font-headline-lg text-[16px] text-on-surface">Informations personnelles</h3>
            </div>

            <form method="POST" action="{{ route('sadmin.compte.update') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @csrf
                @method('PUT')

                {{-- Nom --}}
                <div class="space-y-1.5">
                    <label class="text-[11px] text-on-surface-variant font-medium">Nom</label>
                    <input
                        name="nom"
                        class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-[13px]"
                        type="text"
                        value="{{ old('nom', $user?->nom) }}"
                        required
                    >
                    @error('nom')
                        <div class="text-[12px] text-alert-red">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Prénom --}}
                <div class="space-y-1.5">
                    <label class="text-[11px] text-on-surface-variant font-medium">Prénom</label>
                    <input
                        name="prenom"
                        class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-[13px]"
                        type="text"
                        value="{{ old('prenom', $user?->prenom) }}"
                        required
                    >
                    @error('prenom')
                        <div class="text-[12px] text-alert-red">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="space-y-1.5">
                    <label class="text-[11px] text-on-surface-variant font-medium">Adresse email</label>
                    <input
                        name="email"
                        class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-[13px]"
                        type="email"
                        value="{{ old('email', $user?->email) }}"
                        required
                        autocomplete="email"
                    >
                    @error('email')
                        <div class="text-[12px] text-alert-red">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Téléphone --}}
                <div class="space-y-1.5">
                    <label class="text-[11px] text-on-surface-variant font-medium">Téléphone</label>
                    <input
                        name="telephone"
                        class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-[13px]"
                        type="tel"
                        value="{{ old('telephone', $user?->telephone) }}"
                        placeholder="+225 00 00 00 00 00"
                        autocomplete="tel"
                    >
                    @error('telephone')
                        <div class="text-[12px] text-alert-red">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Rôle --}}
                <div class="space-y-1.5">
                    <label class="text-[11px] text-on-surface-variant font-medium">Rôle</label>
                    <input
                        class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface-container-low text-on-surface-variant cursor-not-allowed text-[13px]"
                        disabled
                        type="text"
                        value="{{ $user?->role }}"
                    >
                </div>

                {{-- Statut --}}
                <div class="space-y-1.5">
                    <label class="text-[11px] text-on-surface-variant font-medium">Statut</label>
                    <input
                        class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface-container-low text-on-surface-variant cursor-not-allowed text-[13px]"
                        disabled
                        type="text"
                        value="{{ $user?->statut ?? 'Actif' }}"
                    >
                </div>

                {{-- Date d'inscription --}}
                <div class="space-y-1.5">
                    <label class="text-[11px] text-on-surface-variant font-medium">Date d'inscription</label>
                    <input
                        class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface-container-low text-on-surface-variant cursor-not-allowed text-[13px]"
                        disabled
                        type="text"
                        value="{{ $user?->created_at?->format('d/m/Y') ?? '-' }}"
                    >
                </div>

                {{-- Photo de profil --}}
                <div class="space-y-1.5 md:col-span-2">
                    <label class="text-[11px] text-on-surface-variant font-medium">Photo de profil</label>
                    <input
                        name="image"
                        class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-[13px]"
                        type="file"
                        accept="image/*"
                    >
                    @error('image')
                        <div class="text-[12px] text-alert-red">{{ $message }}</div>
                    @enderror
                    <p class="text-[12px] text-on-surface-variant mt-1">Laissez vide pour conserver l’ancienne photo.</p>
                </div>

                <div class="md:col-span-2 flex justify-end pt-3">
                    <button class="px-6 py-2 bg-gradient-to-r from-primary to-primary-container text-on-primary font-label-md text-[12px] rounded-lg" type="submit">
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </section>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Password visibility toggle
    const passwordToggles = document.querySelectorAll('.material-symbols-outlined');
    passwordToggles.forEach(toggle => {
        if (toggle.textContent === 'visibility') {
            toggle.addEventListener('click', function() {
                const input = this.previousElementSibling;
                if (input.type === 'password') {
                    input.type = 'text';
                    this.textContent = 'visibility_off';
                } else {
                    input.type = 'password';
                    this.textContent = 'visibility';
                }
            });
        }
    });
</script>
@endsection