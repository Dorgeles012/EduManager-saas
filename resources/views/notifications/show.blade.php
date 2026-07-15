@extends(auth()->user()?->role === 'sadmin' ? 'sadmin.layouts.app' : 'client.layouts.app')

@section('title', 'Notification')

@section('content')
    @php
        $notification = $recipient->notification;
        $icons = ['payment' => 'fa-credit-card', 'subscription' => 'fa-calendar-check', 'bulletin' => 'fa-file-lines', 'school_year' => 'fa-graduation-cap', 'update' => 'fa-arrows-rotate', 'staff' => 'fa-users', 'teacher' => 'fa-chalkboard-user', 'student' => 'fa-user-plus', 'establishment' => 'fa-school', 'security' => 'fa-lock'];
        $icon = $icons[strtolower((string) $notification->category)] ?? 'fa-bell';
        $sender = trim(($notification->sender?->prenom ?? '').' '.($notification->sender?->nom ?? '')) ?: 'EduManager';
    @endphp
    <div class="mx-auto max-w-3xl rounded-2xl border border-outline-variant bg-surface-container-lowest p-6 shadow-sm sm:p-8">
        <div class="flex items-start gap-4">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary-fixed text-primary"><i class="fa-solid {{ $icon }}"></i></span>
            <div class="min-w-0">
                <p class="text-sm text-text-muted">{{ $notification->sent_at?->translatedFormat('d F Y à H:i') }}</p>
                <h1 class="mt-1 text-2xl font-bold text-on-surface">{{ $notification->titre }}</h1>
                <p class="mt-2 text-sm text-text-muted">Envoyée par {{ $sender }}</p>
            </div>
        </div>
        <div class="mt-7 whitespace-pre-line leading-7 text-on-surface">{{ $notification->message }}</div>
        <a href="{{ url()->previous() }}" class="mt-8 inline-flex items-center gap-2 rounded-lg border border-outline-variant px-4 py-2 text-sm font-medium hover:bg-surface-container"><i class="fa-solid fa-arrow-left"></i> Retour</a>
    </div>
@endsection
