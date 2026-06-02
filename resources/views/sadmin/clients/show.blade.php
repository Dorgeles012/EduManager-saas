@extends('sadmin.layouts.app')

@section('content')
<div class="flex flex-col gap-6 max-w-5xl mx-auto w-full">
    <!-- En-tête avec bouton retour -->
    <div class="flex items-center gap-3 mb-2">
        <a href="{{ route('sadmin.clients.index') }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-surface-container-lowest hover:bg-surface-container-high transition-colors shadow-sm">
            <span class="material-symbols-outlined text-[18px] text-on-surface-variant hover:text-primary">arrow_back</span>
        </a>
        <h2 class="font-headline-md text-[18px] text-on-surface">Détails du client</h2>
    </div>

    <!-- Carte principale avec icônes en arrière-plan -->
    <div class="bg-surface-container-lowest rounded-2xl card-shadow border border-outline-variant overflow-hidden relative">
        <!-- Icônes client en arrière-plan -->
        <div class="absolute -right-4 -top-4 opacity-5 pointer-events-none">
            <span class="material-symbols-outlined text-[200px]" style="font-variation-settings: 'FILL' 1;">person</span>
        </div>
        <div class="absolute -left-4 -bottom-4 opacity-5 pointer-events-none rotate-12">
            <span class="material-symbols-outlined text-[180px]" style="font-variation-settings: 'FILL' 1;">badge</span>
        </div>

        <!-- Bannière de statut -->
        <div class="px-5 py-2.5 bg-gradient-to-r from-primary/5 to-primary-container/5 border-b border-outline-variant/30 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[16px]">info</span>
                <span class="text-label-sm text-[11px] text-text-muted">Informations générales</span>
            </div>
                    <div class="flex items-center gap-2">
                        @php
                            $isActive = strtolower((string) $client->statut) === 'active';
                        @endphp
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="absolute inline-flex h-full w-full rounded-full {{ $isActive ? 'bg-success-green' : 'bg-red-500' }} opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 {{ $isActive ? 'bg-success-green' : 'bg-red-500' }}"></span>
                        </span>
                        <span class="text-label-sm text-[11px] font-semibold {{ $isActive ? 'text-success-green' : 'text-red-500' }}">
                            {{ $isActive ? 'Actif' : 'Bloqué' }}
                </span>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="p-5 md:p-6">
            <!-- En-tête avec avatar et nom -->
            <div class="flex flex-col md:flex-row md:items-start gap-5 pb-5 border-b border-outline-variant/30">
                <div class="relative">
                    <div class="w-20 h-20 bg-gradient-to-br from-primary/10 to-primary-container/10 rounded-2xl flex items-center justify-center shadow-md relative z-10 overflow-hidden">
                        @if($client->image)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($client->image) }}" alt="Photo client" class="w-full h-full object-cover">
                        @else
                            <span class="text-2xl font-bold bg-gradient-to-r from-primary to-primary-container bg-clip-text text-transparent">
                                {{ strtoupper(substr($client->prenom ?? $client->nom, 0, 1)) }}{{ strtoupper(substr($client->nom ?? '', 0, 1)) }}
                            </span>
                        @endif
                    </div>
                    <div class="absolute -right-2 -bottom-2 opacity-20">
                        <span class="material-symbols-outlined text-2xl">person</span>
                    </div>
                </div>
                <div class="flex-1 relative">
                    <h3 class="font-headline-xl text-[22px] text-on-surface mb-2 flex items-center gap-2">
                        {{ $client->nom }} {{ $client->prenom }}
                        <span class="material-symbols-outlined text-primary text-2xl opacity-60" style="font-variation-settings: 'FILL' 1;">verified</span>
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-primary-fixed/30 text-primary rounded-full text-[10px]">
                            <span class="material-symbols-outlined text-[12px]">badge</span>
                            ID: {{ $client->id }}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-surface-container-high text-on-surface-variant rounded-full text-[10px]">
                            <span class="material-symbols-outlined text-[12px]">calendar_today</span>
                            Créé le {{ $client->created_at ? $client->created_at->format('d/m/Y') : '—' }}
                        </span>
                        @if($client->updated_at && $client->updated_at != $client->created_at)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-surface-container-high text-on-surface-variant rounded-full text-[10px]">
                                <span class="material-symbols-outlined text-[12px]">update</span>
                                Mis à jour le {{ $client->updated_at->format('d/m/Y') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Grille des informations détaillées -->
            <div class="pt-5">
                <h4 class="font-headline-md text-[16px] text-on-surface mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[18px]">contact_mail</span>
                    Informations de contact
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Email -->
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-surface-container-low/30 hover:bg-surface-container-low transition-all duration-200">
                        <div class="p-1.5 bg-primary-fixed/20 rounded-lg">
                            <span class="material-symbols-outlined text-primary text-[18px]">mail</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-label-sm text-[10px] text-text-muted uppercase tracking-wider">Email</p>
                            <p class="font-body-md text-[13px] text-on-surface break-all">{{ $client->email ?? '—' }}</p>
                            @if($client->email)
                                <a href="mailto:{{ $client->email }}" class="text-label-sm text-[10px] text-primary hover:underline inline-flex items-center gap-1 mt-1">
                                    <span class="material-symbols-outlined text-[12px]">send</span>
                                    Envoyer un email
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Téléphone -->
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-surface-container-low/30 hover:bg-surface-container-low transition-all duration-200">
                        <div class="p-1.5 bg-primary-fixed/20 rounded-lg">
                            <span class="material-symbols-outlined text-primary text-[18px]">call</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-label-sm text-[10px] text-text-muted uppercase tracking-wider">Téléphone</p>
                            <p class="font-body-md text-[13px] text-on-surface">{{ $client->telephone ?? '—' }}</p>
                            @if($client->telephone)
                                <a href="tel:{{ $client->telephone }}" class="text-label-sm text-[10px] text-primary hover:underline inline-flex items-center gap-1 mt-1">
                                    <span class="material-symbols-outlined text-[12px]">phone_in_talk</span>
                                    Appeler
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Adresse (pleine largeur) -->
                    @if($client->adresse)
                        <div class="md:col-span-2">
                            <div class="flex items-start gap-3 p-3 rounded-xl bg-surface-container-low/30 hover:bg-surface-container-low transition-all duration-200">
                                <div class="p-1.5 bg-primary-fixed/20 rounded-lg">
                                    <span class="material-symbols-outlined text-primary text-[18px]">location_on</span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-label-sm text-[10px] text-text-muted uppercase tracking-wider">Adresse</p>
                                    <p class="font-body-md text-[13px] text-on-surface whitespace-pre-line">{{ $client->adresse }}</p>
                                    <a href="https://maps.google.com/?q={{ urlencode($client->adresse) }}" target="_blank" class="text-label-sm text-[10px] text-primary hover:underline inline-flex items-center gap-1 mt-1">
                                        <span class="material-symbols-outlined text-[12px]">map</span>
                                        Voir sur Google Maps
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Ville -->
                    @if($client->ville)
                        <div class="flex items-start gap-3 p-3 rounded-xl bg-surface-container-low/30 hover:bg-surface-container-low transition-all duration-200">
                            <div class="p-1.5 bg-primary-fixed/20 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-[18px]">location_city</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-label-sm text-[10px] text-text-muted uppercase tracking-wider">Ville</p>
                                <p class="font-body-md text-[13px] text-on-surface">{{ $client->ville }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Établissement -->
                    <div class="md:col-span-2">
                        <div class="flex items-start gap-3 p-3 rounded-xl bg-surface-container-low/30 hover:bg-surface-container-low transition-all duration-200">
                            <div class="p-1.5 bg-primary-fixed/20 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-[18px]">school</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-label-sm text-[10px] text-text-muted uppercase tracking-wider">Établissement</p>
                                <p class="font-body-md text-[13px] text-on-surface">{{ $client->etablissement?->nom ?? 'Aucun établissement associé' }}</p>
                                @if($client->etablissement)
                                    <div class="text-label-sm text-[10px] text-text-muted inline-flex items-center gap-1 mt-1">
                                        <span class="material-symbols-outlined text-[12px]">business</span>
                                        ID: {{ $client->etablissement->id }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmAction(event, form, action, message) {
        event.preventDefault();
        if (confirm(`⚠️ Êtes-vous sûr de vouloir ${action} ce client ?\n\n${message}`)) {
            form.submit();
        }
        return false;
    }
</script>
@endsection