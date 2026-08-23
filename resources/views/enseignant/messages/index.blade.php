@extends('enseignant.layouts.app')
@section('title', 'EduManager - Messagerie Enseignant')

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

@push('scripts')
<script>
    document.body.classList.add('page-messages');
</script>
@endpush
