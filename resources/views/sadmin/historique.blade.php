@extends('sadmin.layouts.app')

@section('content')
<div class="max-w-max-width mx-auto">
    <div class="flex items-center justify-between mb-6"><div><h2 class="font-headline-lg text-primary">Historique des notifications</h2><p class="text-sm text-text-muted">Suivi des envois et de leur lecture.</p></div><a class="px-4 py-2 rounded-lg bg-primary text-white text-sm" href="{{ route('sadmin.notifications') }}">Nouvelle notification</a></div>
    <div class="bg-surface-container-lowest rounded-xl card-shadow overflow-x-auto">
        <table class="w-full text-left"><thead class="bg-surface-bright text-xs text-text-muted"><tr><th class="px-4 py-3">Date</th><th class="px-4 py-3">Sujet</th><th class="px-4 py-3">Audience</th><th class="px-4 py-3">Destinataires</th><th class="px-4 py-3">Statut</th></tr></thead><tbody class="divide-y divide-surface-container">
            @forelse($notifications as $notification)
                <tr><td class="px-4 py-3 text-sm">{{ $notification->sent_at?->format('d/m/Y H:i') }}</td><td class="px-4 py-3"><p class="font-medium">{{ $notification->titre }}</p><p class="text-xs text-text-muted">{{ $notification->category }}</p></td><td class="px-4 py-3 text-sm">{{ $notification->audience }}</td><td class="px-4 py-3 text-sm">{{ $notification->recipients_count }}</td><td class="px-4 py-3 text-sm"><span class="text-success-green">Envoyée</span><br><span class="text-text-muted">{{ $notification->read_count }} lue(s) · {{ $notification->recipients_count - $notification->read_count }} non lue(s)</span></td></tr>
            @empty
                <tr><td colspan="5" class="px-4 py-10 text-center text-text-muted">Aucune notification envoyée.</td></tr>
            @endforelse
        </tbody></table>
    </div>
    <div class="mt-4">{{ $notifications->links() }}</div>
</div>
@endsection
