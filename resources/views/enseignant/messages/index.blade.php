@extends('enseignant.layouts.app')
@section('title', 'EduManager - Messagerie Enseignant')

@section('content')
<div class="mb-4">
    <h2 class="font-headline-md text-headline-md text-primary mb-0.5">Messagerie</h2>
    <p class="text-body-sm text-text-muted text-xs">Communiquez en direct avec vos élèves, leurs groupes de classe et les membres du personnel.</p>
</div>

@include('partials.chat-interface', [
    'routePrefix' => 'enseignant',
    'conversations' => $conversations,
    'groups' => $groups,
    'contacts' => $contacts,
    'unreadTotal' => $unreadTotal
])
@endsection
