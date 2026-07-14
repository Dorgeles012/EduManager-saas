@extends('sadmin.layouts.app')

@section('content')
<div class="max-w-max-width mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
    <section class="lg:col-span-7 bg-surface-container-lowest rounded-xl p-6 card-shadow border border-slate-200">
        <div class="flex items-center gap-2 mb-6"><span class="material-symbols-outlined text-primary">edit_note</span><h2 class="font-headline-md">Créer une notification</h2></div>
        @if($errors->any())<div class="mb-4 rounded-lg bg-alert-red/10 p-3 text-sm text-alert-red">{{ $errors->first() }}</div>@endif
        <form class="space-y-5" method="POST" action="{{ route('sadmin.notifications.store') }}">
            @csrf
            <div><label class="block text-sm mb-2">Titre du message</label><input class="w-full rounded-lg border-outline-variant" name="titre" value="{{ old('titre') }}" required></div>
            <div><label class="block text-sm mb-2">Contenu du message</label><textarea class="w-full rounded-lg border-outline-variant" name="message" rows="5" required>{{ old('message') }}</textarea></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div><label class="block text-sm mb-2">Catégorie</label><input class="w-full rounded-lg border-outline-variant" name="category" value="{{ old('category', 'Système') }}"></div><div><label class="block text-sm mb-2">Priorité</label><select class="w-full rounded-lg border-outline-variant" name="priority"><option value="low">Basse</option><option value="normal" selected>Normale</option><option value="urgent">Urgente</option></select></div></div>
            <div><label class="block text-sm mb-2">Audience cible</label><select class="w-full rounded-lg border-outline-variant" id="audience" name="audience"><option value="all">Tous les utilisateurs</option><option value="clients">Tous les clients</option><option value="parents">Tous les parents</option><option value="personnel">Tout le personnel</option><option value="enseignants">Tous les enseignants</option><option value="users">Utilisateurs sélectionnés</option></select></div>
            <div id="users-field" class="hidden"><label class="block text-sm mb-2">Destinataires</label><select class="w-full rounded-lg border-outline-variant" name="recipient_ids[]" multiple size="7">@foreach($users as $user)<option value="{{ $user->id }}" @selected(in_array($user->id, old('recipient_ids', [])))>{{ trim($user->nom.' '.$user->prenom) }} — {{ strtolower($user->role) }} — {{ $user->email }}</option>@endforeach</select><p class="mt-1 text-xs text-text-muted">Maintenez Ctrl (ou Cmd) pour sélectionner plusieurs personnes.</p></div>
            <button class="px-8 py-2.5 bg-primary text-white rounded-lg font-medium flex items-center gap-2" type="submit"><span class="material-symbols-outlined">send</span>Envoyer maintenant</button>
        </form>
    </section>
    <section class="lg:col-span-5 bg-surface-container-lowest rounded-xl p-6 card-shadow border border-slate-200"><div class="flex justify-between items-center mb-4"><h3 class="font-headline-md">Derniers envois</h3><a class="text-primary text-sm" href="{{ route('sadmin.notifications.historique') }}">Voir l'historique</a></div><div class="space-y-3">@forelse($notifications as $notification)<div class="rounded-lg border border-surface-subtle p-3"><p class="font-medium">{{ $notification->titre }}</p><p class="text-xs text-text-muted mt-1">{{ $notification->recipients_count }} destinataire(s) · {{ $notification->read_count }} lu(s)</p></div>@empty<p class="text-sm text-text-muted">Aucun envoi pour le moment.</p>@endforelse</div></section>
</div>
@endsection

@push('scripts')
<script>
    const audience = document.getElementById('audience');
    const usersField = document.getElementById('users-field');
    const refreshUsersField = () => usersField.classList.toggle('hidden', audience.value !== 'users');
    audience.addEventListener('change', refreshUsersField); refreshUsersField();
</script>
@endpush
