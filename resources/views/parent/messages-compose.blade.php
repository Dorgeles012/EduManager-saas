@extends('parent.layouts.app')

@section('title', 'Nouveau message')

@section('content')
<div class="mb-6 flex items-center justify-between gap-3 flex-wrap">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-primary">Nouveau message</h2>
        <p class="text-sm text-on-surface-variant">Envoyer un message à l'établissement</p>
    </div>
    <a class="inline-flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-primary rounded-lg" href="{{ route('parent.messages') }}">
        <span class="material-symbols-outlined text-lg">arrow_back</span> Retour
    </a>
</div>

<div class="max-w-2xl bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant p-6">
    <form method="POST" action="{{ route('parent.messages.store') }}" class="space-y-5">
        @csrf

        <div class="space-y-1">
            <label class="font-label-md text-label-md text-on-surface-variant">Destinataire</label>
            <select name="receiver_id" required class="w-full bg-white border border-outline-variant rounded-lg px-4 py-2.5 text-body-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none">
                <option value="">Sélectionnez un destinataire</option>
                @foreach($destinataires as $destinataire)
                    <option value="{{ $destinataire->id }}" {{ old('receiver_id') == $destinataire->id ? 'selected' : '' }}>
                        {{ $destinataire->prenom }} {{ $destinataire->nom }} ({{ ucfirst($destinataire->role) }})
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('receiver_id')" class="mt-2" />
        </div>

        <div class="space-y-1">
            <label class="font-label-md text-label-md text-on-surface-variant">Message</label>
            <textarea name="message" rows="6" required placeholder="Votre message..." class="w-full bg-white border border-outline-variant rounded-lg px-4 py-3 text-body-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none">{{ old('message') }}</textarea>
            <x-input-error :messages="$errors->get('message')" class="mt-2" />
        </div>

        <button type="submit" class="w-full bg-primary text-on-primary px-4 py-3 rounded-lg font-label-md hover:bg-primary/90 transition-colors flex items-center justify-center gap-2">
            <span class="material-symbols-outlined">send</span>
            Envoyer le message
        </button>
    </form>
</div>
@endsection
