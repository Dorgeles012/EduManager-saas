@extends('enseignant.layouts.app')
@section('title', 'EduManager - Bulletins Scolaires')
@section('content')
<div class="mb-8 flex justify-between items-end">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-primary mb-1">Bulletins Scolaires</h2>
        <p class="font-body-md text-body-md text-on-surface-variant">Consultez et téléchargez les bulletins de vos classes</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-surface-container-lowest p-6 rounded-xl card-shadow border border-outline-variant/30 flex items-center gap-5">
        <div class="w-14 h-14 rounded-xl bg-primary-fixed flex items-center justify-center text-primary">
            <span class="material-symbols-outlined text-3xl">school</span>
        </div>
        <div>
            <p class="text-on-surface-variant font-label-sm uppercase tracking-wide">Élèves</p>
            <p class="text-3xl font-headline-md text-on-surface">{{ $totalStudents ?? 0 }}</p>
        </div>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-xl card-shadow border border-outline-variant/30 flex items-center gap-5">
        <div class="w-14 h-14 rounded-xl bg-secondary-fixed flex items-center justify-center text-on-secondary-container">
            <span class="material-symbols-outlined text-3xl">co_present</span>
        </div>
        <div>
            <p class="text-on-surface-variant font-label-sm uppercase tracking-wide">Mes Classes</p>
            <p class="text-3xl font-headline-md text-on-surface">{{ $totalClasses ?? 0 }}</p>
        </div>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-xl card-shadow border border-outline-variant/30 flex items-center gap-5">
        <div class="w-14 h-14 rounded-xl bg-surface-container-highest flex items-center justify-center text-primary">
            <span class="material-symbols-outlined text-3xl">calendar_month</span>
        </div>
        <div>
            <p class="text-on-surface-variant font-label-sm uppercase tracking-wide">Périodes</p>
            <p class="text-3xl font-headline-md text-on-surface">{{ $totalPeriods ?? 6 }}</p>
        </div>
    </div>
</div>

<div class="bg-surface-container-lowest p-6 rounded-xl card-shadow border border-outline-variant/30 mb-8">
    <div class="flex flex-wrap items-center gap-4">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-label-sm text-on-surface-variant mb-2">Classe</label>
            <select class="w-full bg-surface-container-low border-outline-variant rounded-lg font-body-sm focus:ring-primary focus:border-primary" id="filterClass">
                <option value="">Mes classes</option>
                @foreach($classes ?? [] as $class)
                <option value="{{ $class['id'] }}">{{ $class['name'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[200px]">
            <label class="block text-label-sm text-on-surface-variant mb-2">Période</label>
            <select class="w-full bg-surface-container-low border-outline-variant rounded-lg font-body-sm focus:ring-primary focus:border-primary" id="filterPeriod">
                <option value="t1">1er Trimestre</option>
                <option value="t2">2ème Trimestre</option>
                <option value="t3">3ème Trimestre</option>
            </select>
        </div>
        <div class="flex items-end h-full pt-6">
            <button class="bg-surface-variant text-on-surface px-6 py-2.5 rounded-lg font-label-md flex items-center gap-2 hover:bg-outline-variant/30 transition-all" onclick="resetFilters()">
                <span class="material-symbols-outlined">restart_alt</span>
                Réinitialiser
            </button>
        </div>
    </div>
</div>

<div class="bg-surface-container-lowest rounded-xl card-shadow border border-outline-variant/30 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-[13px]">
            <thead>
                <tr class="bg-surface-container-low border-b border-outline-variant">
                    <th class="px-4 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider text-[11px]">Élève</th>
                    <th class="px-4 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider text-[11px]">Classe</th>
                    <th class="px-4 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider text-[11px]">Période</th>
                    <th class="px-4 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider text-[11px]">Moyenne</th>
                    <th class="px-4 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider text-[11px]">Mention</th>
                    <th class="px-4 py-3 font-label-sm text-on-surface-variant uppercase tracking-wider text-[11px] text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportCards ?? [] as $report)
                <tr class="border-b border-outline-variant/50 hover:bg-surface-container-low transition-colors">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-full bg-primary-fixed flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-[15px]">person</span>
                            </div>
                            <span class="font-body-md font-medium">{{ $report['student_name'] ?? 'N/A' }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-on-surface-variant">{{ $report['class_name'] ?? 'N/A' }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-label-sm bg-secondary-container/20 text-on-secondary-container">{{ $report['period'] ?? 'N/A' }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="font-bold text-primary">{{ $report['average'] ?? '0' }}</span>
                        <span class="text-on-surface-variant">/20</span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-label-sm {{ $report['mention_class'] ?? 'mention-average' }}">{{ $report['mention'] ?? 'N/A' }}</span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a class="inline-block p-1.5 text-primary hover:bg-primary-fixed rounded-lg transition-colors" href="{{ route('enseignant.bulletin.show', $report['id']) }}" title="Voir">
                            <span class="material-symbols-outlined text-[17px]">visibility</span>
                        </a>
                        <a class="inline-block p-1.5 text-primary hover:bg-primary-fixed rounded-lg transition-colors" href="{{ route('enseignant.bulletin.download-pdf', $report['id']) }}" title="Télécharger PDF">
                            <span class="material-symbols-outlined text-[17px]">download</span>
                        </a>
                        <a class="inline-block p-1.5 text-primary hover:bg-primary-fixed rounded-lg transition-colors" href="{{ route('enseignant.bulletin.print', $report['id']) }}" target="_blank" title="Imprimer">
                            <span class="material-symbols-outlined text-[17px]">print</span>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <span class="material-symbols-outlined text-4xl text-on-surface-variant">description</span>
                            <p class="font-body-md text-on-surface-variant">Aucun bulletin trouvé.</p>
                            <p class="font-body-sm text-text-muted">Aucun bulletin n'est disponible pour le moment.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($reportCards, 'links'))
    <div class="px-4 py-3 border-t border-outline-variant/50">
        {{ $reportCards->links() }}
    </div>
    @endif
</div>

<style>
    .card-shadow { box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
    .mention-excellent { background: #d4edda; color: #155724; }
    .mention-tres-bien { background: #cce5ff; color: #004085; }
    .mention-bien { background: #d6d8db; color: #383d41; }
    .mention-asser-bien { background: #fff3cd; color: #856404; }
    .mention-passable { background: #f8d7da; color: #721c24; }
    .mention-average { background: #e2e3e5; color: #383d41; }
</style>
@endsection

