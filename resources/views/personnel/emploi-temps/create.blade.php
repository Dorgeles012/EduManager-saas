@extends('personnel.layouts.app')

@section('title', $existing ? 'Modifier l\'emploi du temps' : 'Créer un emploi du temps')

@section('styles')
<style>
    .schedule-table th, .schedule-table td { border: 1px solid #dbe2ea; padding: 8px; vertical-align: top; }
    .schedule-table thead th { background: #173b78; color: #fff; text-align: center; padding: 12px; }
    .schedule-table tbody > tr > th { background: #f4f7fb; width: 130px; text-align: center; }
    .schedule-table td { width: 18%; height: 90px; }
    .cell-empty { background: #f8fafc; }
    .custom-shadow { box-shadow: 0 4px 12px rgba(55, 48, 163, 0.04); }
</style>
@endsection

@section('content')
<div class="mb-6 flex items-center justify-between gap-3 flex-wrap">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-primary">{{ $existing ? 'Modifier l\'emploi du temps' : 'Créer un emploi du temps' }}</h2>
        <p class="text-sm text-on-surface-variant">
            Classe : <strong>{{ $classe->nom }}</strong>
            @if($classe->niveau) · {{ $classe->niveau->nom }} @endif
            @if($anneeId) · {{ $annees->firstWhere('id', $anneeId)?->libelle ?? '' }} @endif
        </p>
    </div>
    <a class="inline-flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-primary rounded-lg" href="{{ route('personnel.emploi-temps.index') }}">
        <span class="material-symbols-outlined text-lg">arrow_back</span> Retour
    </a>
</div>

@if(! $existing)
<!-- Sélection de la classe -->
<div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant p-6 mb-6">
    <h4 class="font-headline-md text-headline-md mb-4">Sélection de la classe</h4>
    <form method="GET" action="{{ route('personnel.emploi-temps.edit') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="space-y-1">
            <label class="font-label-md text-label-md text-on-surface-variant">Classe</label>
            <select name="classe_id" required class="w-full bg-white border border-outline-variant rounded-lg px-4 py-2.5 text-body-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none">
                <option value="">— Choisir —</option>
                @foreach(\App\Models\Classe::where('tenant_id', auth()->user()->tenant_id)->with('niveau')->orderBy('nom')->get() as $c)
                <option value="{{ $c->id }}" {{ $c->id == $classe->id ? 'selected' : '' }}>{{ $c->nom }} ({{ $c->niveau?->nom ?? '—' }})</option>
                @endforeach
            </select>
        </div>
        <div class="space-y-1">
            <label class="font-label-md text-label-md text-on-surface-variant">Année académique</label>
            <select name="annee_academique_id" class="w-full bg-white border border-outline-variant rounded-lg px-4 py-2.5 text-body-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none">
                <option value="">Aucune (par défaut)</option>
                @foreach($annees as $year)
                <option value="{{ $year->id }}" {{ $anneeId == $year->id ? 'selected' : '' }}>{{ $year->libelle }}</option>
                @endforeach
            </select>
        </div>
        <div class="space-y-1 flex items-end">
            <button type="submit" class="w-full bg-primary text-on-primary px-4 py-2.5 rounded-lg font-label-md hover:bg-primary/90 transition-colors">Charger</button>
        </div>
    </form>
</div>
@endif

<!-- Grille de l'emploi du temps -->
<div class="bg-surface-container-lowest rounded-xl custom-shadow border border-outline-variant p-6">
    <div class="flex items-center justify-between mb-4">
        <h4 class="font-headline-md text-headline-md">Grille de l'emploi du temps</h4>
        <span class="text-sm text-on-surface-variant">{{ $totalSeances }} séance(s) programmée(s)</span>
    </div>

    <form method="POST" action="{{ route('personnel.emploi-temps.store') }}" id="schedule-form">
        @csrf
        <input type="hidden" name="classe_id" value="{{ $classe->id }}">
        <input type="hidden" name="annee_academique_id" value="{{ $anneeId }}">
        <input type="hidden" name="serie_id" value="{{ $serieId }}">

        <div class="overflow-x-auto">
            <table class="schedule-table w-full min-w-[960px] border-collapse">
                <thead>
                    <tr>
                        <th>Horaires</th>
                        @foreach($days as $day)
                        <th>{{ ucfirst($day) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($slots as $slot)
                        @if(isset($slot['break']))
                            <tr>
                                <th colspan="6" style="background:#d1d5db;color:#4b5563;text-align:center;padding:8px;text-transform:uppercase;letter-spacing:.05em;">{{ $slot['break'] }}</th>
                            </tr>
                        @else
                            <tr>
                                <th>{{ str_replace(':', 'h', $slot['start']) }} - {{ str_replace(':', 'h', $slot['end']) }}</th>
                                @foreach($days as $day)
                                    @php
                                        $cellKey = $day.'|'.$slot['key'];
                                        $entry = $grid[$day][$slot['key']] ?? null;
                                    @endphp
                                    <td class="{{ $entry ? '' : 'cell-empty' }}">
                                        <div class="cell-content" data-cell-key="{{ $cellKey }}">
                                            @if($entry)
                                                <div class="mb-2">
                                                    <span class="text-sm font-bold text-primary block">{{ $entry->matiere?->nom }}</span>
                                                    <span class="text-xs text-on-surface-variant block">{{ $entry->enseignant?->prenoms }} {{ $entry->enseignant?->nom }}</span>
                                                    @if($entry->salle)<span class="text-xs text-text-muted block">Salle : {{ $entry->salle }}</span>@endif
                                                </div>
                                            @endif
                                            <div class="flex flex-col gap-1">
                                                <select name="cells[{{ $cellKey }}][matiere_id]" class="cell-matiere w-full text-xs border border-outline-variant rounded px-2 py-1.5 outline-none">
                                                    <option value="">— Matière —</option>
                                                    @foreach($matieres as $matiere)
                                                    <option value="{{ $matiere->id }}" {{ $entry && $entry->matiere_id == $matiere->id ? 'selected' : '' }}>{{ $matiere->nom }}</option>
                                                    @endforeach
                                                </select>
                                                <select name="cells[{{ $cellKey }}][enseignant_id]" class="cell-enseignant w-full text-xs border border-outline-variant rounded px-2 py-1.5 outline-none">
                                                    <option value="">— Enseignant —</option>
                                                    @foreach($enseignants as $ens)
                                                    <option value="{{ $ens->id }}" {{ $entry && $entry->enseignant_id == $ens->id ? 'selected' : '' }}>{{ $ens->prenoms }} {{ $ens->nom }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="text" name="cells[{{ $cellKey }}][salle]" value="{{ $entry->salle ?? '' }}" placeholder="Salle" class="w-full text-xs border border-outline-variant rounded px-2 py-1.5 outline-none">
                                            </div>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
<div class="mt-6 flex items-center justify-between">
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-on-primary rounded-lg font-label-md hover:bg-primary/90 transition-colors">
                <span class="material-symbols-outlined">save</span>
                {{ $existing ? 'Enregistrer les modifications' : 'Créer l\'emploi du temps' }}
            </button>
        </div>
    </form>

    @if($existing)
    <div class="mt-6 flex items-center justify-between border-t border-outline-variant pt-6">
        <p class="text-sm text-on-surface-variant">Emploi du temps déjà créé pour cette classe.</p>
        <form method="POST" action="{{ route('personnel.emploi-temps.destroy', ['classe_id' => $classe->id, 'annee_academique_id' => $anneeId]) }}" onsubmit="return confirm('Supprimer cet emploi du temps ?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-alert-red text-white rounded-lg font-label-md hover:bg-red-600 transition-colors">
                <span class="material-symbols-outlined">delete</span> Supprimer
            </button>
        </form>
    </div>
    @endif
</div>
@endsection
