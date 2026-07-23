@extends('personnel.layouts.app')
@section('title', 'EduManager - Bulletins Scolaires')
@section('content')
<div class="mb-8 flex justify-between items-end">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-primary mb-1">Bulletins Scolaires</h2>
        <p class="font-body-md text-body-md text-on-surface-variant">Consultez et gérez les bulletins des élèves</p>
    </div>
    <a href="{{ route('personnel.bulletin.create') }}"
       class="bg-primary text-on-primary px-6 py-2.5 rounded-lg font-label-md text-label-md flex items-center gap-2 hover:opacity-90 active:scale-95 transition-all card-shadow">
        <span class="material-symbols-outlined">add</span>
        Générer un bulletin
    </a>
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
            <p class="text-on-surface-variant font-label-sm uppercase tracking-wide">Classes</p>
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
                <option value="">Toutes les classes</option>
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
                        <a class="inline-block p-1.5 text-primary hover:bg-primary-fixed rounded-lg transition-colors" href="{{ route('personnel.bulletin.show', $report['id']) }}" title="Voir">
                            <span class="material-symbols-outlined text-[17px]">visibility</span>
                        </a>
                        <a class="inline-block p-1.5 text-primary hover:bg-primary-fixed rounded-lg transition-colors" href="{{ route('personnel.bulletin.edit', $report['id']) }}" title="Modifier">
                            <span class="material-symbols-outlined text-[17px]">edit</span>
                        </a>
                        <form class="inline bulletin-delete-form" method="POST" action="{{ route('personnel.bulletin.destroy', $report['id']) }}">
                            @csrf @method('DELETE')
                            <button class="p-1.5 text-error hover:bg-error-container rounded-lg" title="Supprimer">
                                <span class="material-symbols-outlined text-[17px]">delete</span>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td class="px-4 py-12 text-center" colspan="6">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-16 h-16 bg-surface-container-low rounded-full flex items-center justify-center mb-3 text-outline">
                                <span class="material-symbols-outlined text-4xl">analytics</span>
                            </div>
                            <h3 class="font-headline-md text-on-surface mb-1">Aucune donnée disponible</h3>
                            <p class="text-on-surface-variant font-body-md">Aucun bulletin n'a été créé pour le moment.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(($reportCards ?? collect())->isNotEmpty() && method_exists($reportCards, 'links'))
    <div class="px-6 py-4 border-t border-outline-variant bg-surface-container-low/30 flex items-center justify-between">
        <span class="text-label-sm text-text-muted">Affichage de {{ $reportCards->firstItem() ?? 1 }} à {{ $reportCards->lastItem() ?? count($reportCards ?? []) }} sur {{ $reportCards->total() ?? count($reportCards ?? []) }} bulletins</span>
        <div class="flex gap-2">{{ $reportCards->links() }}</div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; vertical-align: middle; }
    body { background-color: #f9f9ff; }
    .card-shadow { box-shadow: 0 4px 12px rgba(55, 48, 163, 0.04); }
    .mention-excellent { background-color: #05966910; color: #059669; }
    .mention-good { background-color: #D9770610; color: #D97706; }
    .mention-average { background-color: #64748B10; color: #64748B; }
    .mention-poor { background-color: #E11D4810; color: #E11D48; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.bulletin-delete-form').forEach(form => {
        form.addEventListener('submit', function (event) {
            if (this.dataset.confirmed === 'true') return;
            event.preventDefault();
            const submit = () => { this.dataset.confirmed = 'true'; this.requestSubmit(); };
            Swal.fire({
                title: 'Supprimer ce bulletin ?',
                text: 'Cette action est irréversible.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#E11D48',
                cancelButtonColor: '#64748B'
            }).then(result => { if (result.isConfirmed) submit(); });
        });
    });

    function resetFilters() {
        document.getElementById('filterClass').value = '';
        document.getElementById('filterPeriod').value = 't1';
        Swal.fire({ title: 'Filtres réinitialisés', text: 'Les filtres ont été réinitialisés.', icon: 'success', timer: 1500, showConfirmButton: false });
    }
</script>
@endpush
