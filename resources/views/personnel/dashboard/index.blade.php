@extends('personnel.layouts.app')

@section('content')

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Tableau de bord</h2>
    <p class="text-sm text-gray-500 mt-1">Bienvenue, voici la performance de votre établissement.</p>
</div>

@php
    $stats = [
        ['label' => 'Revenu total',         'value' => number_format($counts['revenu_total'] ?? 0, 0, ',', ' '), 'suffix' => 'FCFA', 'icon' => 'payments',      'color' => 'primary'],
        ['label' => 'Étudiants',            'value' => $counts['eleves'] ?? 0,                                  'suffix' => null,   'icon' => 'group',         'color' => 'blue'],
        ['label' => 'Établissements',       'value' => $counts['etablissements'] ?? 0,                          'suffix' => null,   'icon' => 'domain',        'color' => 'emerald'],
        ['label' => 'Factures en attente',  'value' => $counts['factures_impayees'] ?? 0,                       'suffix' => null,   'icon' => 'receipt_long',  'color' => 'rose'],
        ['label' => 'Enseignants',          'value' => $counts['enseignants'] ?? 0,                             'suffix' => null,   'icon' => 'school',        'color' => 'violet'],
        ['label' => 'Matières',             'value' => $counts['matieres'] ?? 0,                                'suffix' => null,   'icon' => 'menu_book',     'color' => 'teal'],
        ['label' => 'Niveaux',              'value' => $counts['niveaux'] ?? 0,                                 'suffix' => null,   'icon' => 'layers',        'color' => 'blue'],
        ['label' => 'Classes',              'value' => $counts['classes'] ?? 0,                                 'suffix' => null,   'icon' => 'meeting_room',  'color' => 'emerald'],
    ];

    $colorMap = [
        'primary' => ['bg' => 'bg-primary-fixed', 'text' => 'text-primary'],
        'blue'    => ['bg' => 'bg-blue-50',       'text' => 'text-blue-600'],
        'emerald' => ['bg' => 'bg-emerald-50',    'text' => 'text-emerald-600'],
        'rose'    => ['bg' => 'bg-rose-50',       'text' => 'text-rose-600'],
        'violet'  => ['bg' => 'bg-violet-50',     'text' => 'text-violet-600'],
        'teal'    => ['bg' => 'bg-teal-50',       'text' => 'text-teal-600'],
    ];
@endphp

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
    @foreach($stats as $stat)
        @php($colors = $colorMap[$stat['color']] ?? $colorMap['primary'])
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 rounded-xl {{ $colors['bg'] }} flex items-center justify-center {{ $colors['text'] }}">
                    <span class="material-symbols-outlined text-lg">{{ $stat['icon'] }}</span>
                </div>
                <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider truncate max-w-[90px] text-right">{{ $stat['label'] }}</span>
            </div>
            <h3 class="text-xl sm:text-2xl font-bold text-gray-900 truncate leading-tight">
                {{ $stat['value'] }}
                @if($stat['suffix'])
                    <span class="text-xs font-semibold text-gray-400 ml-0.5">{{ $stat['suffix'] }}</span>
                @endif
            </h3>
            <p class="text-xs text-gray-500 mt-0.5">{{ $stat['label'] }}</p>
        </div>
    @endforeach
</div>

@endsection