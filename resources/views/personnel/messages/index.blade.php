@extends('personnel.layouts.app')
@section('title', 'EduManager - Messagerie')

@section('content')
<div class="mb-4">
    <h2 class="font-headline-md text-headline-md text-primary mb-0.5">Messagerie</h2>
    <p class="text-body-sm text-text-muted text-xs">Communiquez en direct avec les enseignants et les parents d'élèves de l'établissement.</p>
</div>

@include('partials.chat-interface', [
    'routePrefix' => 'personnel',
    'conversations' => $conversations,
    'groups' => $groups,
    'contacts' => $contacts,
    'unreadTotal' => $unreadTotal
])
@endsection
