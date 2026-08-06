@extends('eleve.layouts.app')

@section('title', 'Ma classe')

@section('content')
<div class="mb-6">
    <h2 class="font-headline-lg text-headline-lg text-primary">Ma classe</h2>
    <p class="text-sm text-on-surface-variant">
        Élèves de la classe {{ $eleve->classe?->nom ?? '—' }}
        @if($anneeActive) · {{ $anneeActive->libelle }} @endif
    </p>
</div>

<div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant overflow-hidden">
    <div class="p-6 border-b border-outline-variant flex items-center justify-between">
        <h4 class="font-headline-md text-headline-md flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">group</span> Liste des élèves
        </h4>
        <span class="text-sm text-on-surface-variant">{{ $classmates->count() }} élève(s)</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-surface-container-low border-b border-outline-variant">
                <tr>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Élève</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Matricule</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Sexe</th>
                </tr>
            </thead>
            <tbody>
                @forelse($classmates as $classmate)
                <tr class="border-b border-outline-variant/50 hover:bg-surface-container-low transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <img class="w-10 h-10 rounded-full object-cover border border-outline-variant" src="{{ $classmate->photo_url ?: 'https://ui-avatars.com/api/?background=1f108e&color=fff&name='.urlencode($classmate->nom.' '.$classmate->prenom) }}" alt="Photo">
                            <div>
                                <span class="font-body-md font-medium">{{ $classmate->nom }} {{ $classmate->prenom }}</span>
                                @if($classmate->id === $eleve->id)
                                    <span class="ml-2 px-2 py-0.5 rounded-full text-[11px] bg-primary-fixed text-primary">Moi</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $classmate->matricule }}</td>
                    <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $classmate->sexe ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="py-10 px-6 text-center text-on-surface-variant">Aucun élève dans cette classe.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
