@extends('eleve.layouts.app')
@section('title', 'EduManager - Mes messages')

@push('styles')
<style>
    body.page-messages { overflow: hidden; }
    body.page-messages main {
        overflow: hidden;
        height: 100vh;
        display: flex;
        flex-direction: column;
    }
    body.page-messages .main-content-with-fixed-nav {
        flex: 1;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        padding-bottom: 0;
    }
</style>
@endpush

@section('content')
<div class="mb-2.5 flex-shrink-0">
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

@push('scripts')
<script>
    document.body.classList.add('page-messages');
</script>
@endpush
