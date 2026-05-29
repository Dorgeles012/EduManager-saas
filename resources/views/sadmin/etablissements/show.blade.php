@extends('sadmin.layouts.app')

@section('content')
<div class="flex flex-col gap-6 max-w-5xl mx-auto w-full">
    <!-- En-tête avec bouton retour -->
    <div class="flex items-center gap-3 mb-2">
        <a href="{{ route('sadmin.etablissement') }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-surface-container-lowest hover:bg-surface-container-high transition-colors shadow-sm">
            <span class="material-symbols-outlined text-[18px] text-on-surface-variant hover:text-primary">arrow_back</span>
        </a>
        <h2 class="font-headline-md text-[18px] text-on-surface">Détails de l'établissement</h2>
    </div>

    <!-- Carte principale avec icône d'école en arrière-plan -->
    <div class="bg-surface-container-lowest rounded-2xl card-shadow border border-outline-variant overflow-hidden relative">
        <!-- Icône d'école en arrière-plan -->
        <div class="absolute -right-4 -top-4 opacity-5 pointer-events-none">
            <span class="material-symbols-outlined text-[200px]" style="font-variation-settings: 'FILL' 1;">school</span>
        </div>
        <div class="absolute -left-4 -bottom-4 opacity-5 pointer-events-none rotate-12">
            <span class="material-symbols-outlined text-[180px]" style="font-variation-settings: 'FILL' 1;">castle</span>
        </div>

        <!-- Bannière de statut -->
        <div class="px-5 py-2.5 bg-gradient-to-r from-primary/5 to-primary-container/5 border-b border-outline-variant/30 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[16px]">info</span>
                <span class="text-label-sm text-[11px] text-text-muted">Informations générales</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="absolute inline-flex h-full w-full rounded-full {{ $etablissement->statut === 'active' ? 'bg-success-green' : 'bg-warning-amber' }} opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 {{ $etablissement->statut === 'active' ? 'bg-success-green' : 'bg-warning-amber' }}"></span>
                </span>
                <span class="text-label-sm text-[11px] font-semibold {{ $etablissement->statut === 'active' ? 'text-success-green' : 'text-warning-amber' }}">
                    {{ ucfirst($etablissement->statut ?? 'Actif') }}
                </span>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="p-5 md:p-6">
            <!-- En-tête avec logo/acronyme et nom -->
            <div class="flex flex-col md:flex-row md:items-start gap-5 pb-5 border-b border-outline-variant/30">
                <div class="relative">
                    <div class="w-20 h-20 bg-gradient-to-br from-primary/10 to-primary-container/10 rounded-2xl flex items-center justify-center shadow-md relative z-10">
                        <span class="text-2xl font-bold bg-gradient-to-r from-primary to-primary-container bg-clip-text text-transparent">
                            {{ strtoupper($etablissement->acronyme ?? substr($etablissement->nom, 0, 2)) }}
                        </span>
                    </div>
                    <div class="absolute -right-2 -bottom-2 opacity-20">
                        <span class="material-symbols-outlined text-2xl">school</span>
                    </div>
                </div>
                <div class="flex-1 relative">
                    <h3 class="font-headline-xl text-[22px] text-on-surface mb-2 flex items-center gap-2">
                        {{ $etablissement->nom }}
                        <span class="material-symbols-outlined text-primary text-2xl opacity-60" style="font-variation-settings: 'FILL' 1;">school</span>
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-primary-fixed/30 text-primary rounded-full text-[10px]">
                            <span class="material-symbols-outlined text-[12px]">category</span>
                            {{ str_replace('_', ' ', $etablissement->type_etablissement) }}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-surface-container-high text-on-surface-variant rounded-full text-[10px]">
                            <span class="material-symbols-outlined text-[12px]">badge</span>
                            ID: {{ $etablissement->id }}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-surface-container-high text-on-surface-variant rounded-full text-[10px]">
                            <span class="material-symbols-outlined text-[12px]">calendar_today</span>
                            Créé le {{ $etablissement->created_at ? $etablissement->created_at->format('d/m/Y') : '—' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Grille des informations détaillées -->
            <div class="pt-5">
                <h4 class="font-headline-md text-[16px] text-on-surface mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[18px]">info</span>
                    Informations de contact
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Email -->
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-surface-container-low/30 hover:bg-surface-container-low">
                        <div class="p-1.5 bg-primary-fixed/20 rounded-lg">
                            <span class="material-symbols-outlined text-primary text-[18px]">mail</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-label-sm text-[10px] text-text-muted uppercase tracking-wider">Email</p>
                            <p class="font-body-md text-[13px] text-on-surface break-all">{{ $etablissement->email ?? '—' }}</p>
                            @if($etablissement->email)
                                <a href="mailto:{{ $etablissement->email }}" class="text-label-sm text-[10px] text-primary hover:underline inline-flex items-center gap-1 mt-1">
                                    <span class="material-symbols-outlined text-[12px]">send</span>
                                    Envoyer un email
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Téléphone -->
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-surface-container-low/30 hover:bg-surface-container-low">
                        <div class="p-1.5 bg-primary-fixed/20 rounded-lg">
                            <span class="material-symbols-outlined text-primary text-[18px]">call</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-label-sm text-[10px] text-text-muted uppercase tracking-wider">Téléphone</p>
                            <p class="font-body-md text-[13px] text-on-surface">{{ $etablissement->telephone ?? '—' }}</p>
                            @if($etablissement->telephone)
                                <a href="tel:{{ $etablissement->telephone }}" class="text-label-sm text-[10px] text-primary hover:underline inline-flex items-center gap-1 mt-1">
                                    <span class="material-symbols-outlined text-[12px]">phone_in_talk</span>
                                    Appeler
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Adresse (pleine largeur) -->
                    <div class="md:col-span-2">
                        <div class="flex items-start gap-3 p-3 rounded-xl bg-surface-container-low/30 hover:bg-surface-container-low">
                            <div class="p-1.5 bg-primary-fixed/20 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-[18px]">location_on</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-label-sm text-[10px] text-text-muted uppercase tracking-wider">Adresse</p>
                                <p class="font-body-md text-[13px] text-on-surface">{{ $etablissement->adresse ?? '—' }}</p>
                                @if($etablissement->adresse)
                                    <a href="https://maps.google.com/?q={{ urlencode($etablissement->adresse) }}" target="_blank" class="text-label-sm text-[10px] text-primary hover:underline inline-flex items-center gap-1 mt-1">
                                        <span class="material-symbols-outlined text-[12px]">map</span>
                                        Voir sur Google Maps
                                    </a>
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
    function confirmDelete() {
        return confirm('⚠️ ATTENTION : Cette action est irréversible.\n\nÊtes-vous absolument sûr de vouloir supprimer cet établissement ?\n\nToutes les données associées (utilisateurs, abonnements, factures, classes, etc.) seront également supprimées.');
    }

    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        const content = modal.querySelector('.bg-white, #modal-edit-content');
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        const content = modal.querySelector('.bg-white, #modal-edit-content');
        content.classList.add('scale-95', 'opacity-0');
        content.classList.remove('scale-100', 'opacity-100');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
</script>
@endsection