@extends('parent.layouts.app')
@section('title', 'EduManager - Messagerie Parent')

@section('content')
<div class="mb-4">
    <h2 class="font-headline-md text-headline-md text-primary mb-0.5">Messagerie</h2>
    <p class="text-body-sm text-text-muted text-xs">Communiquez en direct avec les enseignants de vos enfants et le personnel de l'établissement.</p>
</div>

@include('partials.chat-interface', [
    'routePrefix' => 'parent',
    'conversations' => $conversations,
    'groups' => $groups,
    'contacts' => $contacts,
    'unreadTotal' => $unreadTotal
])
@endsection
