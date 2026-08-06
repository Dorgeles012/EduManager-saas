@extends(($pdfMode ?? false) ? 'client.layouts.pdf' : 'parent.layouts.app')

@section('title', 'Emploi du temps')

@section('content')
@php
    $isPdf = $pdfMode ?? false;
    $grid = $grid ?? [];
@endphp

<style>
    @page { margin: 8mm; size: A4 landscape; }
    body { font-family: Arial, Helvetica, sans-serif; color: #111; }
    .schedule-wrap { width: 100%; overflow-x: auto; }
    h2 { text-align: center; margin: 5px 0; }
    .meta { text-align: center; font-size: 12px; margin-bottom: 12px; }
    table.schedule { width: 100%; border-collapse: collapse; }
    table.schedule th, table.schedule td { border: 1px solid #111; padding: 8px; text-align: center; vertical-align: middle; }
    table.schedule thead th { background: #d9d9d9; font-weight: bold; text-transform: uppercase; }
    table.schedule tbody th { background: #f0f0f0; }
    .break-row th { background: #e5e5e5 !important; text-transform: uppercase; letter-spacing: .05em; }
    .no-print { margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center; }
    @media print { .no-print, nav, aside, header { display: none !important; } body, main { margin: 0 !important; background: #fff !important; } }
</style>

@if(! $isPdf)
<div class="no-print">
    <a href="{{ route('parent.enfant.emploi-temps', $eleve) }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-primary rounded-lg">
        <span class="material-symbols-outlined text-lg">arrow_back</span> Retour
    </a>
    <div class="flex gap-2">
        <a href="{{ route('parent.enfant.emploi-temps.pdf', $eleve) }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm text-warning-amber hover:bg-warning-amber/10 rounded-lg">
            <span class="material-symbols-outlined">download</span> Télécharger
        </a>
    </div>
</div>
@endif

<div class="schedule-wrap">
    <h2>EMPLOI DU TEMPS</h2>
    <div class="meta">
        {{ $school?->nom ?? '' }}
        @if($year) · {{ $year->libelle }} @endif
        · Classe : {{ $eleve->classe?->nom ?? '—' }}
        · Élève : {{ $eleve->nom }} {{ $eleve->prenom }}
    </div>

    @if($entries->isEmpty())
        <p style="text-align:center;padding:30px;">Aucun emploi du temps disponible.</p>
    @else
    <table class="schedule">
        <thead>
            <tr><th>Horaires</th>@foreach($days as $day)<th>{{ ucfirst($day) }}</th>@endforeach</tr>
        </thead>
        <tbody>
            @foreach($slots as $slot)
                @if(isset($slot['break']))
                    <tr class="break-row"><th colspan="6">{{ $slot['break'] }}</th></tr>
                @else
                    <tr>
                        <th>{{ str_replace(':', 'h', $slot['start']) }} - {{ str_replace(':', 'h', $slot['end']) }}</th>
                        @foreach($days as $day)
                            @php($entry = $grid[$day][$slot['key']] ?? null)
                            <td>
                                @if($entry)
                                    <strong>{{ $entry->matiere?->nom ?? '—' }}</strong><br>
                                    <span>{{ $entry->enseignant?->prenoms }} {{ $entry->enseignant?->nom }}</span>
                                    @if($entry->salle)<br><small>Salle : {{ $entry->salle }}</small>@endif
                                @else
                                    —
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
    @endif
</div>

@if($printMode ?? false)
<script>
    window.addEventListener('load', function () { window.print(); });
</script>
@endif
@endsection
