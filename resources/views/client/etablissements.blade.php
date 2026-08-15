@extends('client.layouts.app')

@section('title', 'EduManager - Mes etablissements')

@section('content')
<div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-primary mb-2">Mes etablissements</h2>
        <p class="font-body-lg text-body-lg text-on-surface-variant">Creez vos ecoles selon la limite de votre abonnement et choisissez l'etablissement actif.</p>
    </div>
    <div class="rounded-lg border border-outline-variant bg-white px-4 py-3 text-body-sm">
        <span class="font-semibold">Utilisation :</span>
        {{ $usedSchools }} / {{ $plan?->is_unlimited ? 'Illimite' : ($plan?->max_schools ?? 0) }}
    </div>
</div>

@if(session('success'))
    <div class="mb-6 rounded-lg border border-success-green/30 bg-success-green/10 px-4 py-3 text-success-green">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="mb-6 rounded-lg border border-alert-red/30 bg-alert-red/10 px-4 py-3 text-alert-red">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <section class="lg:col-span-2 bg-white rounded-xl border border-outline-variant ambient-shadow overflow-hidden">
        <div class="px-6 py-5 border-b border-outline-variant">
            <h3 class="font-headline-md text-headline-md text-on-surface">Ecoles du compte</h3>
        </div>
        <div class="divide-y divide-outline-variant">
            @forelse($schools as $school)
                <div class="px-6 py-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="font-label-md text-on-surface">{{ $school->nom }}</p>
                        <p class="text-body-sm text-on-surface-variant">{{ str_replace('_', ' ', $school->type_etablissement) }} · {{ $school->email ?? 'Email non renseigne' }}</p>
                    </div>
                    @if((int) auth()->user()->etablissement_id === (int) $school->id)
                        <span class="inline-flex items-center gap-2 rounded-full bg-success-green/10 px-3 py-1 text-success-green text-label-sm">
                            <span class="material-symbols-outlined text-[16px]">check_circle</span>
                            Actif
                        </span>
                    @else
                        <form method="POST" action="{{ route('client.etablissements.switch') }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="etablissement_id" value="{{ $school->id }}">
                            <button class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-container" type="submit">Selectionner</button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="px-6 py-10 text-center text-on-surface-variant">Aucun etablissement cree pour le moment.</div>
            @endforelse
        </div>
    </section>

    <section class="bg-white rounded-xl border border-outline-variant ambient-shadow p-6">
        <h3 class="font-headline-md text-headline-md text-on-surface mb-5">Nouvel etablissement</h3>
        @if($plan && ($plan->is_unlimited || ($remainingSchools ?? 0) > 0))
            <form method="POST" action="{{ route('client.etablissements.store') }}" class="space-y-4">
                @csrf
                <input name="nom" class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary" placeholder="Nom de l'ecole" required>
                <input name="acronyme" class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary" placeholder="Acronyme">
                <select name="type_etablissement" class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary" required>
                    <option value="">Type d'etablissement</option>
                    <option value="primaire">Primaire</option>
                    <option value="college">College</option>
                    <option value="lycee">Lycee</option>
                    <option value="universite">Universite</option>
                    <option value="grande_ecole">Grande ecole</option>
                </select>
                <input name="email" type="email" class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary" placeholder="Email">
                <input name="telephone" class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary" placeholder="Telephone">
                <textarea name="adresse" class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary" rows="3" placeholder="Adresse"></textarea>
                <button class="w-full rounded-lg bg-primary px-4 py-3 font-semibold text-white hover:bg-primary-container" type="submit">Creer un etablissement</button>
            </form>
        @else
            <div class="rounded-lg border border-warning-amber/30 bg-warning-amber/10 p-4 text-sm text-on-surface">
                <p class="font-semibold mb-2">Vous avez atteint la limite de votre abonnement.</p>
                <p>Votre abonnement autorise {{ $plan?->schoolsLimitLabel() ?? 0 }} etablissement(s). Vous utilisez actuellement {{ $usedSchools }} etablissement(s).</p>
                <p class="mt-2">Veuillez changer d'abonnement pour ajouter une autre ecole.</p>
            </div>
        @endif
    </section>
</div>
@endsection
