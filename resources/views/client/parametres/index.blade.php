@extends('client.layouts.app')

@section('content')

@php
    $client = $client ?? auth()->user();
    $successMessage = session('success');
    $errorMessage = $errors->first();
@endphp

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Paramètres</h2>
    <p class="text-sm text-gray-500 mt-1">Gérez vos informations personnelles, votre sécurité et vos préférences.</p>
</div>

{{-- ============ COMPTE ============ --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
        <div class="w-11 h-11 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 flex-shrink-0 overflow-hidden border-2 border-indigo-100">
            @if(!empty($client?->image))
                <img src="{{ asset('storage/'.$client->image) }}" alt="Photo de profil" class="w-full h-full object-cover">
            @else
                <span class="material-symbols-outlined text-xl">account_circle</span>
            @endif
        </div>
        <div class="flex-1 min-w-0">
            <h3 class="text-sm font-semibold text-gray-900">Informations du compte</h3>
            <p class="text-[11px] text-gray-500">Gérez vos informations personnelles et votre identité publique</p>
        </div>
    </div>

    <div class="p-5">
        {{-- Récap rapide --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
            <div class="bg-gray-50/50 rounded-xl p-3">
                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Nom complet</p>
                <p class="text-xs font-semibold text-gray-900 truncate">{{ trim(($client?->nom ?? '').' '.($client?->prenom ?? '')) ?: '—' }}</p>
            </div>
            <div class="bg-gray-50/50 rounded-xl p-3">
                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Email</p>
                <p class="text-xs font-semibold text-gray-900 truncate">{{ $client?->email ?? '—' }}</p>
            </div>
            <div class="bg-gray-50/50 rounded-xl p-3">
                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Téléphone</p>
                <p class="text-xs font-semibold text-gray-900 truncate">{{ $client?->telephone ?? '—' }}</p>
            </div>
            <div class="bg-gray-50/50 rounded-xl p-3">
                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Rôle / Statut</p>
                <p class="text-xs font-semibold text-gray-900 truncate">{{ ucfirst($client?->role ?? 'client') }} · {{ ucfirst($client?->statut ?? '—') }}</p>
            </div>
        </div>

        {{-- Formulaire profil --}}
        <form method="POST" action="{{ route('client.parametres.update') }}" enctype="multipart/form-data" class="space-y-4" data-sweet-alert="loading">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Nom</label>
                    <input name="nom" type="text" value="{{ old('nom', $client?->nom) }}" required
                           class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                    @error('nom')<div class="text-rose-500 text-[10px] mt-1">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Prénom</label>
                    <input name="prenom" type="text" value="{{ old('prenom', $client?->prenom) }}" required
                           class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                    @error('prenom')<div class="text-rose-500 text-[10px] mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Adresse email</label>
                    <input name="email" type="email" value="{{ old('email', $client?->email) }}" required
                           class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                    @error('email')<div class="text-rose-500 text-[10px] mt-1">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Téléphone</label>
                    <input name="telephone" type="tel" value="{{ old('telephone', $client?->telephone) }}" placeholder="+225 00 00 00 00 00"
                           class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                    @error('telephone')<div class="text-rose-500 text-[10px] mt-1">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Photo de profil</label>
                    <input name="photo" type="file" accept="image/*"
                           class="w-full text-[11px] text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 file:cursor-pointer border border-gray-200 rounded-lg bg-gray-50">
                    @error('photo')<div class="text-rose-500 text-[10px] mt-1">{{ $message }}</div>@enderror
                    <p class="text-[10px] text-gray-400 mt-1">Laissez vide pour conserver l'ancienne photo.</p>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-gray-100">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-xs font-semibold transition shadow-sm">
                    <span class="material-symbols-outlined text-sm">save</span>
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============ SÉCURITÉ + PRÉFÉRENCES ============ --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    {{-- Sécurité --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                <span class="material-symbols-outlined text-base">security</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Sécurité & Authentification</h3>
                <p class="text-[11px] text-gray-500">Protégez votre compte</p>
            </div>
        </div>

        <div class="p-5 space-y-4">
            <div class="space-y-3">
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <div>
                        <p class="text-xs font-semibold text-gray-900">Authentification 2 facteurs</p>
                        <p class="text-[10px] text-gray-500">Sécurité renforcée</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only peer" checked>
                        <div class="w-10 h-5 bg-gray-200 rounded-full peer-checked:bg-indigo-600 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <div>
                        <p class="text-xs font-semibold text-gray-900">Notifications de connexion</p>
                        <p class="text-[10px] text-gray-500">Alertes email pour nouveaux appareils</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only peer" checked>
                        <div class="w-10 h-5 bg-gray-200 rounded-full peer-checked:bg-indigo-600 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"></div>
                    </label>
                </div>
            </div>

            <div class="pt-3 border-t border-gray-100">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-6 h-6 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600">
                        <span class="material-symbols-outlined text-sm">lock</span>
                    </div>
                    <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider">Changer le mot de passe</h4>
                </div>

                <form method="POST" action="{{ route('client.parametres.password') }}" class="space-y-3" data-sweet-alert="loading">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Mot de passe actuel</label>
                        <div class="relative">
                            <input name="current_password" type="password" placeholder="Mot de passe actuel"
                                   class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 pr-10 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-indigo-600 cursor-pointer toggle-password text-base transition">visibility</span>
                        </div>
                        @error('current_password')<div class="text-rose-500 text-[10px] mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Nouveau mot de passe</label>
                        <div class="relative">
                            <input name="password" type="password" placeholder="••••••••"
                                   class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 pr-10 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-indigo-600 cursor-pointer toggle-password text-base transition">visibility</span>
                        </div>
                        @error('password')<div class="text-rose-500 text-[10px] mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Confirmation</label>
                        <div class="relative">
                            <input name="password_confirmation" type="password" placeholder="Confirmer le mot de passe"
                                   class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 pr-10 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-indigo-600 cursor-pointer toggle-password text-base transition">visibility</span>
                        </div>
                        @error('password_confirmation')<div class="text-rose-500 text-[10px] mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-1.5 bg-white border border-indigo-200 text-indigo-600 hover:bg-indigo-50 px-4 py-2.5 rounded-lg text-xs font-semibold transition">
                            <span class="material-symbols-outlined text-sm">key</span>
                            Mettre à jour le mot de passe
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Préférences système --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                <span class="material-symbols-outlined text-base">tune</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Préférences Système</h3>
                <p class="text-[11px] text-gray-500">Interface et plateforme</p>
            </div>
        </div>

        <div class="p-5 space-y-3">
            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                <label class="text-xs font-semibold text-gray-900">Langue du système</label>
                <select class="bg-gray-50 border border-gray-200 rounded-lg text-xs py-1.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition font-semibold text-indigo-600 cursor-pointer">
                    <option>Français (FR)</option>
                </select>
            </div>

            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                <label class="text-xs font-semibold text-gray-900">Fuseau horaire</label>
                <span class="text-[11px] bg-gray-100 px-2.5 py-1 rounded-lg text-gray-600 font-semibold">Europe/Paris (GMT+1)</span>
            </div>

            <div class="flex items-center justify-between py-2">
                <div>
                    <p class="text-xs font-semibold text-gray-900">Mode sombre</p>
                    <p class="text-[10px] text-gray-500">Basculer l'apparence visuelle</p>
                </div>
                <button type="button" id="theme-toggle" class="w-10 h-5 bg-gray-200 rounded-full relative focus:outline-none transition-colors">
                    <div id="toggle-circle" class="w-4 h-4 bg-white rounded-full absolute left-0.5 top-0.5 transition-all duration-300 shadow-md"></div>
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const swalConfig = {
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold text-white mx-1',
            cancelButton: 'px-4 py-2 rounded-lg text-xs font-semibold text-white mx-1',
            title: 'text-base font-semibold',
            htmlContainer: 'text-xs text-gray-500'
        },
        buttonsStyling: false,
        reverseButtons: true
    };

    function showSweetAlert({ title, text = '', icon = 'success', confirmButtonText = 'Compris', confirmButtonColor = '#4f46e5', showConfirmButton = true } = {}) {
        if (window.Swal && typeof window.Swal.fire === 'function') {
            return window.Swal.fire({
                ...swalConfig,
                title,
                text,
                icon,
                confirmButtonText,
                showConfirmButton
            });
        }
        return window.alert(text ? `${title}\n${text}` : title);
    }

    window.showSweetAlert = showSweetAlert;

    const successMessage = @json($successMessage);
    const errorMessage = @json($errorMessage);

    if (successMessage) {
        showSweetAlert({ title: 'Succès', text: successMessage, icon: 'success', confirmButtonText: 'Compris' });
    }

    if (!successMessage && errorMessage) {
        showSweetAlert({ title: 'Oups', text: errorMessage, icon: 'error', confirmButtonText: 'Réessayer' });
    }

    document.querySelectorAll('.toggle-password').forEach(icon => {
        icon.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
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

    document.querySelectorAll('form[data-sweet-alert="loading"]').forEach(form => {
        form.addEventListener('submit', function (event) {
            if (this.dataset.confirmed === 'true') return;
            event.preventDefault();
            const isPasswordUpdate = this.action.includes('/password');
            const photoInput = this.querySelector('input[name="photo"]');
            const isPhotoUpdate = photoInput && photoInput.files.length > 0;
            const title = isPasswordUpdate
                ? 'Changer le mot de passe ?'
                : (isPhotoUpdate ? 'Modifier la photo et le profil ?' : 'Modifier les informations du profil ?');
            const text = isPasswordUpdate
                ? 'Votre nouveau mot de passe remplacera immédiatement l\'ancien.'
                : 'Les nouvelles informations seront enregistrées.';

            const submit = () => { this.dataset.confirmed = 'true'; this.requestSubmit(); };
            if (!window.Swal || typeof window.Swal.fire !== 'function') { submit(); return; }

            window.Swal.fire({
                ...swalConfig,
                title,
                text,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Oui, enregistrer',
                cancelButtonText: 'Annuler'
            }).then(result => { if (result.isConfirmed) submit(); });
        });

        form.addEventListener('submit', function () {
            if (this.dataset.confirmed !== 'true') return;
            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton && !submitButton.dataset.sweetAlertDisabled) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="inline-flex items-center gap-2"><span class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>En cours...</span>';
            }
            if (window.Swal && typeof window.Swal.fire === 'function' && this.dataset.sweetAlert === 'loading') {
                window.Swal.fire({
                    ...swalConfig,
                    title: 'Traitement en cours',
                    text: 'Veuillez patienter pendant la mise à jour.',
                    allowOutsideClick: false,
                    didOpen: () => window.Swal.showLoading(),
                    showConfirmButton: false
                });
            }
        });
    });

    // Toggle mode sombre
    document.getElementById('theme-toggle')?.addEventListener('click', function() {
        this.classList.toggle('bg-gray-200');
        this.classList.toggle('bg-indigo-600');
        document.getElementById('toggle-circle')?.classList.toggle('translate-x-5');
    });
</script>
@endsection