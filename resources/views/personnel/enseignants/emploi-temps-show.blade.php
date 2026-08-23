@extends('personnel.layouts.app')
@section('title', 'Emploi du temps - ' . $enseignant->nom)

@section('content')
<div class="mb-6 flex items-center justify-between gap-3 flex-wrap">
    <div class="flex items-center gap-3">
        <a href="{{ route('personnel.enseignants.index') }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white border border-outline-variant hover:bg-surface-container transition-all text-on-surface-variant flex-shrink-0">
            <span class="material-symbols-outlined text-xl">arrow_back</span>
        </a>
        <div>
            <h2 class="font-headline-md text-headline-md text-primary">Emploi du temps enseignant</h2>
            <p class="text-xs text-text-muted">{{ $enseignant->nom }} {{ $enseignant->prenoms }} ({{ $enseignant->matricule ?? '—' }})</p>
        </div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('personnel.enseignants.emploi-temps.edit', $enseignant->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 rounded-lg text-xs font-medium transition-all shadow-sm">
            <span class="material-symbols-outlined text-base">edit</span>
            Modifier
        </a>
        <a href="{{ route('personnel.enseignants.emploi-temps.teacher.pdf', $enseignant->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 rounded-lg text-xs font-medium transition-all shadow-sm">
            <span class="material-symbols-outlined text-base">download</span>
            Télécharger PDF
        </a>
        <a href="{{ route('personnel.enseignants.emploi-temps.teacher.print', $enseignant->id) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary text-white hover:opacity-90 rounded-lg text-xs font-medium transition-all shadow-sm">
            <span class="material-symbols-outlined text-base">print</span>
            Imprimer
        </a>
    </div>
</div>

<div class="glass-card rounded-xl border border-outline-variant/50 p-5 mb-6 shadow-sm">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-xs border-b border-surface-subtle pb-4 mb-4">
        <div><span class="text-text-muted block">Enseignant :</span> <strong class="text-sm text-on-surface">{{ $enseignant->nom }} {{ $enseignant->prenoms }}</strong></div>
        <div><span class="text-text-muted block">Matricule :</span> <strong class="text-sm text-on-surface font-mono">{{ $enseignant->matricule ?? '—' }}</strong></div>
        <div><span class="text-text-muted block">Matière(s) :</span> <strong class="text-sm text-primary">{{ $enseignant->matieres->pluck('nom')->join(', ') }}</strong></div>
        <div><span class="text-text-muted block">Classes :</span> <strong class="text-sm text-on-surface">{{ $enseignant->classes->pluck('nom')->join(', ') }}</strong></div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse border border-outline-variant min-w-[750px]">
            <thead>
                <tr class="bg-primary text-white text-center">
                    <th class="p-2.5 border border-primary/30 w-32">Horaires</th>
                    @foreach($days as $day)
                        <th class="p-2.5 border border-primary/30">{{ ucfirst($day) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($slots as $slot)
                    @if(isset($slot['break']))
                        <tr class="bg-surface-container-high font-semibold text-center text-text-muted">
                            <td colspan="{{ count($days) + 1 }}" class="py-1.5 border border-outline-variant">
                                ☕ {{ $slot['break'] }}
                            </td>
                        </tr>
                    @else
                        @php($slotKey = $slot['key'])
                        <tr>
                            <td class="p-2 border border-outline-variant bg-surface-container-low text-center font-mono font-semibold text-xs whitespace-nowrap">
                                {{ $slot['start'] }} - {{ $slot['end'] }}
                            </td>
                            @foreach($days as $day)
                                @php($cell = $grid[$day][$slotKey] ?? null)
                                <td class="p-2 border border-outline-variant h-16 align-top {{ $cell ? 'bg-primary/5' : 'bg-white' }}">
                                    @if($cell)
                                        <div class="flex flex-col justify-between h-full">
                                            <span class="font-bold text-primary text-xs">{{ $cell['classe'] }}</span>
                                            <span class="font-medium text-on-surface text-[11px]">{{ $cell['matiere'] }}</span>
                                            @if(!empty($cell['salle']))
                                                <span class="text-[10px] text-text-muted">({{ $cell['salle'] }})</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-text-muted/30 text-center block">—</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
