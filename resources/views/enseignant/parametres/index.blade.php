@extends('enseignant.layouts.app')

@section('content')

@php
    $client = $client ?? auth()->user();
    $successMessage = session('success');
    $errorMessage = $errors->first();
@endphp

<div class="grid grid-cols-12 gap-6">
    <section class="col-span-12 lg:col-span-8 bg-surface-container-lowest rounded-2xl p-5 card-shadow border border-surface-subtle/50">
        <div class="flex items-center gap-3 mb-5">
            <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center border-4 border-primary/20 shadow-md overflow-hidden">
                @if(!empty($client?->image))
                    <img src="{{ asset('storage/'.$client->image) }}" alt="Photo de profil" class="w-full h-full object-cover">
                @else
                    <span class="material-symbols-outlined text-primary text-2xl">account_circle</span>
                @endif
            </div>
            <div class="flex-1">
                <h3 class="font-headline-lg text-[18px] text-on-surface">Paramètres du Compte</h3>
                <p class="text-[12px] text-text-muted mt-0.5">Gérez vos informations personnelles.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-3">
                <div class="border-b border-outline-variant/30 pb-2">
                    <label class="text-[10px] text-text-muted uppercase tracking-wide">Nom complet</label>
                    <p class="text-[14px] font-semibold text-on-surface mt-1">{{ trim(($client?->nom ?? '').' '.($client?->prenom ?? '')) ?: '-' }}</p>
                </div>
                <div class="border-b border-outline-variant/30 pb-2">
                    <label class="text-[10px] text-text-muted uppercase tracking-wide">Email professionnel</label>
                    <p class="text-[14px] font-semibold text-on-surface mt-1">{{ $client?->email ?? '-' }}</p>
                </div>
                <div class="border-b border-outline-variant/30 pb-2">
                    <label class="text-[10px] text-text-muted uppercase tracking-wide">Téléphone</label>
                    <p class="text-[14px] font-semibold text-on-surface mt-1">{{ $client?->telephone ?? '-' }}</p>
                </div>
            </div>
            <div class="space-y-3">
                <div class="border-b border-outline-variant/30 pb-2">
                    <label class="text-[10px] text-text-muted uppercase tracking-wide">Rôle</label>
                    <p class="text-[14px] font-semibold text-on-surface mt-1">{{ $client?->role ?? 'enseignant' }}</p>
                </div>
                <div class="border-b border-outline-variant/30 pb-2">
                    <label class="text-[10px] text-text-muted uppercase tracking-wide">Statut</label>
                    <p class="text-[14px] font-semibold text-on-surface mt-1">{{ $client?->statut ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <form method="POST" action="{{ route('enseignant.parametres.update') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @csrf
                @method('PUT')

                <div class="space-y-1.5">
                    <label class="text-[11px] text-on-surface-variant font-medium">Nom</label>
                    <input name="nom" class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-[13px]" type="text" value="{{ old('nom', $client?->nom) }}" required>
                    @error('nom')<div class="text-[12px] text-alert-red">{{ $message }}</div>@enderror
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] text-on-surface-variant font-medium">Prénom</label>
                    <input name="prenom" class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-[13px]" type="text" value="{{ old('prenom', $client?->prenom) }}" required>
                    @error('prenom')<div class="text-[12px] text-alert-red">{{ $message }}</div>@enderror
                </div>
                <div class="space-y-1.5 md:col-span-2">
                    <label class="text-[11px] text-on-surface-variant font-medium">Adresse email</label>
                    <input name="email" class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-[13px]" type="email" value="{{ old('email', $client?->email) }}" required>
                    @error('email')<div class="text-[12px] text-alert-red">{{ $message }}</div>@enderror
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] text-on-surface-variant font-medium">Téléphone</label>
                    <input name="telephone" class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-[13px]" type="tel" value="{{ old('telephone', $client?->telephone) }}" placeholder="+225 00 00 00 00 00">
                    @error('telephone')<div class="text-[12px] text-alert-red">{{ $message }}</div>@enderror
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] text-on-surface-variant font-medium">Photo</label>
                    <input name="photo" class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-[13px]" type="file" accept="image/*">
                    @error('photo')<div class="text-[12px] text-alert-red">{{ $message }}</div>@enderror
                    <p class="text-[12px] text-on-surface-variant mt-1">Laissez vide pour conserver l'ancienne photo.</p>
                </div>
                <div class="md:col-span-2 flex justify-end pt-3">
                    <button class="px-6 py-2 bg-gradient-to-r from-primary to-primary-container text-on-primary font-label-md text-[12px] rounded-lg" type="submit">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </section>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
    <div class="bg-surface-container-lowest rounded-2xl p-5 card-shadow border border-surface-subtle/50">
        <div class="flex items-start gap-4">
            <div class="p-2 bg-gradient-to-br from-secondary-container to-secondary/20 rounded-xl text-on-secondary-container">
                <span class="material-symbols-outlined text-xl">security</span>
            </div>
            <div class="flex-1">
                <h3 class="font-headline-lg text-[16px] mb-1">Sécurité & Authentification</h3>
                <p class="text-[12px] text-text-muted mb-4">Gérez vos préférences de sécurité.</p>
                <div class="mt-4">
                    <h4 class="font-headline-lg text-[14px] mb-3">Changer le mot de passe</h4>
                    <form method="POST" action="{{ route('enseignant.parametres.password') }}" class="space-y-5">
                        @csrf
                        @method('PUT')
                        <div class="space-y-1.5">
                            <label class="text-[11px] text-on-surface-variant font-medium">Mot de passe actuel</label>
                            <input name="current_password" type="password" class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-[13px]" placeholder="Entrez votre mot de passe actuel">
                            @error('current_password')<div class="text-[12px] text-alert-red">{{ $message }}</div>@enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] text-on-surface-variant font-medium">Nouveau mot de passe</label>
                            <input name="password" type="password" class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-[13px]" placeholder="••••••••">
                            @error('password')<div class="text-[12px] text-alert-red">{{ $message }}</div>@enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] text-on-surface-variant font-medium">Confirmation du mot de passe</label>
                            <input name="password_confirmation" type="password" class="w-full px-3 py-2 rounded-xl border border-outline-variant/50 bg-surface focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-[13px]" placeholder="Confirmez votre nouveau mot de passe">
                        </div>
                        <button class="w-full mt-2 px-3 py-2 text-primary border border-primary/30 rounded-lg hover:bg-primary/5 font-medium text-[12px] inline-flex justify-center" type="submit">Mettre à jour le mot de passe</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-surface-container-lowest rounded-2xl p-5 card-shadow border border-surface-subtle/50">
        <div class="flex items-start gap-4">
            <div class="p-2 bg-gradient-to-br from-primary-fixed to-primary/20 rounded-xl text-primary">
                <span class="material-symbols-outlined text-xl">tune</span>
            </div>
            <div class="flex-1">
                <h3 class="font-headline-lg text-[16px] mb-1">Préférences Système</h3>
                <p class="text-[12px] text-text-muted mb-4">Configurez les paramètres globaux de l'interface.</p>
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-1.5">
                        <label class="text-[13px] font-medium">Langue du système</label>
                        <select class="text-[12px] bg-surface-container-high border border-outline-variant/50 rounded-lg px-2 py-1.5 focus:border-primary focus:ring-2 focus:ring-primary/20 cursor-pointer font-medium text-primary">
                            <option>Français (FR)</option>
                        </select>
                    </div>
                    <div class="flex items-center justify-between py-1.5">
                        <label class="text-[13px] font-medium">Fuseau horaire</label>
                        <span class="text-[12px] bg-surface-container-high px-2 py-1.5 rounded-lg text-text-muted">Europe/Paris (GMT+1)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if($successMessage)
        Swal.fire({ title: 'Succès', text: @json($successMessage), icon: 'success', confirmButtonColor: '#1f108e' });
    @endif
    @if(!$successMessage && $errorMessage)
        Swal.fire({ title: 'Oups', text: @json($errorMessage), icon: 'error', confirmButtonColor: '#E11D48' });
    @endif
</script>
@endsection

