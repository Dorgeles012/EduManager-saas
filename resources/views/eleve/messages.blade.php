@extends('eleve.layouts.app')

@section('title', 'Mes messages')

@section('content')
<div class="mb-6 flex items-center justify-between gap-3 flex-wrap">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-primary">Mes messages</h2>
        <p class="text-sm text-on-surface-variant">Vos échanges avec l'établissement</p>
    </div>
    <a class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-on-primary text-sm font-medium hover:opacity-90" href="{{ route('eleve.messages.create') }}">
        <span class="material-symbols-outlined">add_comment</span> Nouveau message
    </a>
</div>

<div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant overflow-hidden">
    <div class="p-6 border-b border-outline-variant flex items-center justify-between">
        <h4 class="font-headline-md text-headline-md flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">chat</span> Conversations
            @if($unreadCount > 0)
                <span class="px-2 py-0.5 rounded-full bg-alert-red/10 text-alert-red text-xs font-bold">{{ $unreadCount }} non lu(s)</span>
            @endif
        </h4>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-surface-container-low border-b border-outline-variant">
                <tr>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">De / À</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Message</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Date</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Statut</th>
                    <th class="px-6 py-4 text-right"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($messages as $message)
                    @php
                        $isIncoming = $message->receiver_id === auth()->id();
                        $other = $isIncoming ? $message->sender : $message->receiver;
                        $name = trim((string) ($other->prenom ?? '').' '.($other->nom ?? ''));
                        $isUnread = $isIncoming && ! $message->is_read;
                    @endphp
                    <tr class="border-b border-outline-variant/50 hover:bg-surface-container-low transition-colors {{ $isUnread ? 'bg-primary-fixed/10' : '' }}">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                                    <span class="material-symbols-outlined">person</span>
                                </div>
                                <div>
                                    <span class="font-body-md font-medium">{{ $name ?: '—' }}</span>
                                    @if($isIncoming)
                                        <p class="text-xs text-text-muted">{{ $message->sender?->role ?? '' }}</p>
                                    @else
                                        <p class="text-xs text-text-muted">Vous</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-body-sm text-on-surface-variant max-w-md">
                            <span class="line-clamp-2">{{ $message->message }}</span>
                        </td>
                        <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $message->created_at?->translatedFormat('d M Y, H:i') ?? '—' }}</td>
                        <td class="px-6 py-4">
                            @if($isUnread)
                                <span class="px-3 py-1 rounded-full text-label-sm bg-alert-red/10 text-alert-red">Non lu</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-label-sm bg-success-green/10 text-success-green">Lu</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($isUnread)
                                <form method="POST" action="{{ route('eleve.messages.read', $message->id) }}">
                                    @csrf
                                    <button type="submit" class="p-2 text-primary hover:bg-primary-fixed rounded-lg transition-colors" title="Marquer comme lu">
                                        <span class="material-symbols-outlined">done</span>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-14 px-6 text-center">
                            <div class="flex flex-col items-center max-w-xs mx-auto">
                                <div class="w-20 h-20 bg-surface-container rounded-full flex items-center justify-center mb-6">
                                    <span class="material-symbols-outlined text-5xl text-outline-variant">chat</span>
                                </div>
                                <h5 class="font-headline-md text-headline-md text-on-surface mb-2">Aucun message</h5>
                                <p class="text-sm text-text-muted text-center">Vous n'avez aucun message pour le moment.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $messages->links() }}
</div>
@endsection
