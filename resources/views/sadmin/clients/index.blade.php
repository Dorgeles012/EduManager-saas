@extends('sadmin.layouts.app')

@section('content')
<div class="mb-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-primary">Gestion des Clients</h2>
        <p class="text-body-sm text-text-muted">Administrez vos clients, bloquez/débloquez et gérez leurs comptes.</p>
    </div>

    <a href="{{ route('sadmin.clients.create') }}" class="bg-primary-container text-white px-6 py-3 rounded-lg flex items-center gap-2 hover:opacity-90 shadow-md font-label-md text-label-md" >
        <span class="material-symbols-outlined text-[18px]">person_add</span>
        Ajouter un client
    </a>
</div>


<div class="bg-surface-container-lowest rounded-xl card-shadow border border-outline-variant/30 overflow-hidden mt-6">
    <div class="px-6 py-4 border-b border-surface-subtle flex flex-col md:flex-row gap-3 md:items-center md:justify-between">
        <h4 class="font-headline-md text-[18px]">Liste des Clients</h4>

        <form method="GET" action="{{ route('sadmin.clients.index') }}" class="flex flex-col md:flex-row md:items-center gap-3">
            <input
                name="q"
                value="{{ $q }}"
                class="w-[260px] px-3 py-2 bg-white border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container focus:border-primary-container outline-none text-[13px]"
                type="search"
                placeholder="Rechercher (nom, prénom, email, téléphone, établissement)"
            >

            <select
                name="status"
                class="px-3 py-2 bg-white border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container focus:border-primary-container outline-none text-[13px]"
            >
                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Tous</option>
                <option value="actif" {{ $status === 'actif' ? 'selected' : '' }}>Actifs</option>
                <option value="bloqué" {{ $status === 'bloqué' ? 'selected' : '' }}>Bloqués</option>
            </select>

            <button class="px-4 py-2 bg-primary text-on-primary font-label-md text-[13px] rounded-lg shadow-md hover:shadow-lg" type="submit">
                Rechercher
            </button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
            <tr class="bg-surface-subtle">
                <th class="px-4 py-3 font-label-sm text-text-muted uppercase tracking-wider text-[11px]">Client</th>
                <th class="px-4 py-3 font-label-sm text-text-muted uppercase tracking-wider text-[11px]">Établissement</th>
                <th class="px-4 py-3 font-label-sm text-text-muted uppercase tracking-wider text-[11px]">Téléphone</th>
                <th class="px-4 py-3 font-label-sm text-text-muted uppercase tracking-wider text-[11px]">Email</th>
                <th class="px-4 py-3 font-label-sm text-text-muted uppercase tracking-wider text-[11px]">Statut</th>
                <th class="px-4 py-3 font-label-sm text-text-muted uppercase tracking-wider text-[11px]">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-surface-subtle">
            @forelse($clients as $client)
                <tr class="hover:bg-background transition-colors">

                    <td class="px-4 py-3">
                        <div class="font-label-md text-[13px] text-on-surface">{{ $client->nom }} {{ $client->prenom }}</div>
                    </td>

                    <td class="px-4 py-3 text-body-sm text-[13px] text-on-surface">{{ $client->etablissement?->nom ?? '-' }}</td>
                    <td class="px-4 py-3 text-body-sm text-[13px] text-on-surface">{{ $client->telephone ?? '-' }}</td>
                    <td class="px-4 py-3 text-body-sm text-[13px] text-on-surface-variant">{{ $client->email }}</td>

                    <td class="px-4 py-3">
                        @if(($client->statut ?? null) === 'actif')
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-500/15 border border-green-500/30 text-green-800 text-[12px]">Actif</span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-red-500/15 border border-red-500/30 text-red-800 text-[12px]">Bloqué</span>
                        @endif
                    </td>

                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('sadmin.clients.show', $client) }}" class="p-2 rounded-lg bg-surface-subtle hover:bg-surface-container-high transition-colors" title="Voir">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </a>

                            <a href="{{ route('sadmin.clients.edit', $client) }}" class="p-2 rounded-lg bg-surface-subtle hover:bg-surface-container-high transition-colors" title="Modifier">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </a>

                            <form method="POST" action="{{ route('sadmin.clients.destroy', $client) }}" onsubmit="return confirmDeleteSweet(event, this)" class="m-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 rounded-lg bg-surface-subtle hover:bg-surface-container-high transition-colors" title="Supprimer">
                                    <span class="material-symbols-outlined text-[18px]" style="color:#d33">delete</span>
                                </button>
                            </form>

                            @if(($client->statut ?? null) === 'actif')
                                <form method="POST" action="{{ route('sadmin.clients.block', $client) }}" class="m-0">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="p-2 rounded-lg bg-surface-subtle hover:bg-surface-container-high transition-colors" title="Bloquer">
                                        <span class="material-symbols-outlined text-[18px]" style="color:#d97706">block</span>
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('sadmin.clients.unblock', $client) }}" class="m-0">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="p-2 rounded-lg bg-surface-subtle hover:bg-surface-container-high transition-colors" title="Débloquer">
                                        <span class="material-symbols-outlined text-[18px]" style="color:#059669">check_circle</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-text-muted">Aucun client trouvé.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-surface-subtle">
        {{ $clients->links() }}
    </div>
</div>
@endsection