@extends('parent.layouts.app')

@section('title', 'Mes notifications')

@section('content')
<div class="mb-6 flex items-center justify-between gap-3 flex-wrap">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-primary">Mes notifications</h2>
        <p class="text-sm text-on-surface-variant">Retrouvez ici toutes les notifications qui vous sont destinées</p>
    </div>
    @if($unreadCount > 0)
        <form method="POST" action="{{ route('parent.notifications.read-all') }}">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-on-primary text-sm font-medium hover:opacity-90">
                <span class="material-symbols-outlined">mark_all_read</span> Tout marquer comme lu
            </button>
        </form>
    @endif
</div>

<div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant overflow-hidden">
    <div class="p-6 border-b border-outline-variant flex items-center justify-between">
        <h4 class="font-headline-md text-headline-md flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">notifications</span> Notifications
            @if($unreadCount > 0)
                <span class="px-2 py-0.5 rounded-full bg-alert-red/10 text-alert-red text-xs font-bold">{{ $unreadCount }} non lue(s)</span>
            @endif
        </h4>
    </div>
    <div class="divide-y divide-outline-variant/50">
        @forelse($notifications as $recipient)
            @php
                $notification = $recipient->notification;
                $isUnread = $recipient->read_at === null;
                $icons = ['payment' => 'payments', 'bulletin' => 'description', 'note' => 'fact_check', 'absence' => 'event_busy', 'message' => 'chat', 'system' => 'info'];
                $icon = $icons[strtolower((string) $notification->category)] ?? 'notifications';
            @endphp
            <div class="p-5 flex items-start gap-4 {{ $isUnread ? 'bg-primary-fixed/10' : '' }}">
                <div class="w-11 h-11 shrink-0 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined">{{ $icon }}</span>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-3">
                        <h5 class="font-headline-md text-headline-md {{ $isUnread ? 'font-bold' : '' }}">{{ $notification->titre }}</h5>
                        <span class="text-xs text-text-muted shrink-0">{{ $notification->sent_at?->translatedFormat('d M Y, H:i') ?? '—' }}</span>
                    </div>
                    <p class="text-sm text-on-surface-variant mt-1 line-clamp-2">{{ $notification->message }}</p>
                    <div class="flex items-center gap-3 mt-3">
                        @if($isUnread)
                            <form method="POST" action="{{ route('parent.notifications.read', $recipient) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1 text-sm text-primary hover:underline">
                                    <span class="material-symbols-outlined text-base">done_all</span> Marquer comme lu
                                </button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('parent.notifications.destroy', $recipient) }}" onsubmit="return confirm('Supprimer cette notification ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1 text-sm text-alert-red hover:underline">
                                <span class="material-symbols-outlined text-base">delete</span> Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="py-14 px-6 text-center">
                <div class="w-20 h-20 bg-surface-container rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="material-symbols-outlined text-5xl text-outline-variant">notifications_off</span>
                </div>
                <h5 class="font-headline-md text-headline-md text-on-surface mb-2">Aucune notification</h5>
                <p class="text-sm text-text-muted text-center">Vous n'avez aucune notification pour le moment.</p>
            </div>
        @endforelse
    </div>
</div>

<div class="mt-6">
    {{ $notifications->links() }}
</div>
@endsection
