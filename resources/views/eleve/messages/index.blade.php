@extends('eleve.layouts.app')
@section('title', 'EduManager - Mes messages')

@section('content')
<div class="mb-4">
    <h2 class="font-headline-md text-headline-md text-primary mb-0.5">Mes messages</h2>
    <p class="text-body-sm text-text-muted text-xs">Échangez avec vos enseignants et participez au groupe de discussion de votre classe.</p>
</div>

@include('partials.chat-interface', [
    'routePrefix' => 'eleve',
    'conversations' => $conversations,
    'groups' => $groups,
    'contacts' => $contacts,
    'unreadTotal' => $unreadTotal
])
@endsection
