@extends('client.layouts.app')
@section('title', 'EduManager - Bulletins Scolaires')
@section('content')

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Bulletins Scolaires</h2>
        <p class="text-sm text-gray-500 mt-1">Consultez et gérez les bulletins des élèves</p>
    </div>
    <a href="{{ route('client.bulletin.create') }}"
       class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-xs font-semibold transition shadow-sm">
        <span class="material-symbols-outlined text-sm">add</span>
        Générer un bulletin
    </a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mb-6">
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                <span class="material-symbols-outlined text-lg">school</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Élèves</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $totalStudents ?? 0 }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Inscrits</p>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                <span class="material-symbols-outlined text-lg">co_present</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Classes</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $totalClasses ?? 0 }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Actives</p>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-violet-50 flex items-center justify-center text-violet-600">
                <span class="material-symbols-outlined text-lg">calendar_month</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Périodes</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $totalPeriods ?? 6 }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Par an</p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div>
            <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Classe</label>
            <select class="w-full bg-gray-50 border border-gray-200 rounded-lg text-sm py-2 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition" id="filterClass">
                <option value="">Toutes les classes</option>
                @foreach($classes ?? [] as $class)
                <option value="{{ $class['id'] }}">{{ $class['name'] }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Période</label>
            <select class="w-full bg-gray-50 border border-gray-200 rounded-lg text-sm py-2 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition" id="filterPeriod">
                <option value="t1">1er Trimestre</option>
                <option value="t2">2ème Trimestre</option>
                <option value="t3">3ème Trimestre</option>
            </select>
        </div>
        <div class="flex items-end">
            <button onclick="resetFilters()" class="w-full h-[42px] bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium flex items-center justify-center gap-1.5 transition">
                <span class="material-symbols-outlined text-base">restart_alt</span>
                Réinitialiser
            </button>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

    <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                <span class="material-symbols-outlined text-base">description</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Liste des bulletins</h3>
                <p class="text-[11px] text-gray-500">
                    <span id="visibleCount">{{ count($reportCards ?? []) }}</span> bulletin(s)
                    <span id="filteredLabel" class="hidden text-indigo-600">· filtré(s)</span>
                </p>
            </div>
        </div>

        <div class="relative w-full sm:w-72">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg pointer-events-none">search</span>
            <input type="text"
                   id="searchInput"
                   placeholder="Rechercher un élève..."
                   autocomplete="off"
                   class="w-full bg-gray-50 border border-gray-200 rounded-lg text-sm py-2 pl-10 pr-9 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
            <button type="button"
                    id="clearSearch"
                    class="hidden absolute right-2 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full hover:bg-gray-200 flex items-center justify-center text-gray-400 transition"
                    title="Effacer">
                <span class="material-symbols-outlined text-base">close</span>
            </button>
        </div>
    </div>

    <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left" id="bulletinTable">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Élève</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Classe</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Période</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Moyenne</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Mention</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($reportCards ?? [] as $report)
                <tr class="bulletin-row hover:bg-gray-50/50 transition-colors" data-name="{{ strtolower($report['student_name'] ?? '') }}">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold text-gray-900">{{ $report['student_name'] ?? 'N/A' }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-700 font-medium">{{ $report['class_name'] ?? 'N/A' }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-100 text-[10px] font-bold text-gray-700 uppercase">
                            {{ $report['period'] ?? 'N/A' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-xs font-bold {{ ($report['average'] ?? 0) >= 10 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $report['average'] ?? '0' }}</span>
                        <span class="text-[10px] text-gray-400">/20</span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold {{ $report['mention_class'] ?? 'bg-gray-100 text-gray-600' }}">
                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                            {{ $report['mention'] ?? 'N/A' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="{{ route('client.bulletin.show', $report['id']) }}"
                               class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center transition"
                               title="Voir">
                                <span class="material-symbols-outlined text-base">visibility</span>
                            </a>
                            <a href="{{ route('client.bulletin.edit', $report['id']) }}"
                               class="w-8 h-8 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-600 flex items-center justify-center transition"
                               title="Modifier">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </a>
                            <form class="inline bulletin-delete-form" method="POST" action="{{ route('client.bulletin.destroy', $report['id']) }}">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition"
                                        title="Supprimer">
                                    <span class="material-symbols-outlined text-base">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="empty-state-row">
                    <td colspan="6" class="py-16 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center mb-3">
                                <span class="material-symbols-outlined text-2xl text-gray-300">analytics</span>
                            </div>
                            <p class="text-sm font-semibold text-gray-700">Aucune donnée disponible</p>
                            <p class="text-xs text-gray-400 mt-1">Aucune note n'a été saisie pour le moment.</p>
                        </div>
                    </td>
                </tr>
                @endforelse

                <tr id="noResultRow" class="hidden">
                    <td colspan="6" class="py-16 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center mb-3">
                                <span class="material-symbols-outlined text-2xl text-indigo-400">search_off</span>
                            </div>
                            <p class="text-sm font-semibold text-gray-700">Aucun élève trouvé</p>
                            <p class="text-xs text-gray-400 mt-1">Aucun nom ne correspond à votre recherche.</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="md:hidden divide-y divide-gray-100" id="bulletinMobileList">
        @forelse($reportCards ?? [] as $report)
        <div class="bulletin-row p-4 space-y-3" data-name="{{ strtolower($report['student_name'] ?? '') }}">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-sm font-bold flex-shrink-0">
                        {{ strtoupper(substr($report['student_name'] ?? 'E', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $report['student_name'] ?? 'N/A' }}</p>
                        <p class="text-[11px] text-gray-500 truncate">{{ $report['class_name'] ?? 'N/A' }}</p>
                    </div>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-100 text-[10px] font-bold text-gray-700 uppercase flex-shrink-0">
                    {{ $report['period'] ?? 'N/A' }}
                </span>
            </div>

            <div class="grid grid-cols-2 gap-2 py-2">
                <div>
                    <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Moyenne</p>
                    <p class="text-sm font-bold mt-0.5 {{ ($report['average'] ?? 0) >= 10 ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $report['average'] ?? '0' }}<span class="text-[10px] text-gray-400">/20</span>
                    </p>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Mention</p>
                    <p class="text-[11px] font-semibold mt-0.5 text-gray-700">{{ $report['mention'] ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <a href="{{ route('client.bulletin.show', $report['id']) }}"
                   class="flex-1 h-9 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold flex items-center justify-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-sm">visibility</span>
                    Voir
                </a>
                <a href="{{ route('client.bulletin.edit', $report['id']) }}"
                   class="flex-1 h-9 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-600 text-xs font-semibold flex items-center justify-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-sm">edit</span>
                    Modifier
                </a>
                <form class="bulletin-delete-form" method="POST" action="{{ route('client.bulletin.destroy', $report['id']) }}">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="w-9 h-9 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition">
                        <span class="material-symbols-outlined text-base">delete</span>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="py-16 text-center empty-state-row">
            <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-2xl text-gray-300">analytics</span>
            </div>
            <p class="text-sm font-semibold text-gray-700">Aucune donnée disponible</p>
            <p class="text-xs text-gray-400 mt-1">Aucune note n'a été saisie pour le moment.</p>
        </div>
        @endforelse

        <div id="noResultMobile" class="hidden py-16 text-center">
            <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-2xl text-indigo-400">search_off</span>
            </div>
            <p class="text-sm font-semibold text-gray-700">Aucun élève trouvé</p>
            <p class="text-xs text-gray-400 mt-1">Aucun nom ne correspond à votre recherche.</p>
        </div>
    </div>

    @if(($reportCards ?? collect())->isNotEmpty() && method_exists($reportCards, 'links'))
    <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between">
        <span class="text-[11px] text-gray-500">
            {{ $reportCards->firstItem() ?? 1 }} - {{ $reportCards->lastItem() ?? count($reportCards ?? []) }} sur {{ $reportCards->total() ?? count($reportCards ?? []) }}
        </span>
        <div class="text-xs">
            {{ $reportCards->links() ?? '' }}
        </div>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const swalConfig = {
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold text-white mx-1',
            cancelButton: 'px-4 py-2 rounded-lg text-xs font-semibold text-white mx-1',
            title: 'text-base font-semibold',
            htmlContainer: 'text-xs text-gray-500'
        },
        buttonsStyling: false,
        reverseButtons: true
    };

    const searchInput = document.getElementById('searchInput');
    const clearSearchBtn = document.getElementById('clearSearch');
    const noResultRow = document.getElementById('noResultRow');
    const noResultMobile = document.getElementById('noResultMobile');
    const visibleCount = document.getElementById('visibleCount');
    const filteredLabel = document.getElementById('filteredLabel');

    const totalCount = document.querySelectorAll('#bulletinTable .bulletin-row').length;

    const toggleClearButton = () => {
        if (searchInput.value.trim().length > 0) {
            clearSearchBtn.classList.remove('hidden');
        } else {
            clearSearchBtn.classList.add('hidden');
        }
    };

    const filterRows = () => {
        const query = searchInput.value.trim().toLowerCase();
        let visible = 0;

        document.querySelectorAll('#bulletinTable .bulletin-row').forEach(row => {
            const name = row.dataset.name || '';
            if (name.includes(query)) visible++;
        });

        document.querySelectorAll('.bulletin-row').forEach(row => {
            const name = row.dataset.name || '';
            row.classList.toggle('hidden', !name.includes(query));
        });

        const isEmpty = visible === 0 && query.length > 0;
        noResultRow?.classList.toggle('hidden', !isEmpty);
        noResultMobile?.classList.toggle('hidden', !isEmpty);

        if (query.length > 0) {
            document.querySelectorAll('.empty-state-row').forEach(el => el.classList.add('hidden'));
        } else if (totalCount > 0) {
            document.querySelectorAll('.empty-state-row').forEach(el => el.classList.remove('hidden'));
        }

        if (visibleCount) {
            visibleCount.textContent = query.length > 0 ? visible : totalCount;
        }
        if (filteredLabel) {
            filteredLabel.classList.toggle('hidden', query.length === 0);
        }
    };

    searchInput?.addEventListener('input', () => {
        toggleClearButton();
        filterRows();
    });

    clearSearchBtn?.addEventListener('click', () => {
        searchInput.value = '';
        toggleClearButton();
        filterRows();
        searchInput.focus();
    });

    searchInput?.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            searchInput.value = '';
            toggleClearButton();
            filterRows();
        }
    });

    function resetFilters() {
        document.getElementById('filterClass').value = '';
        document.getElementById('filterPeriod').value = 't1';
        document.getElementById('searchInput').value = '';
        toggleClearButton();
        filterRows();

        Swal.fire({
            ...swalConfig,
            title: 'Filtres réinitialisés',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    }

    document.querySelectorAll('.bulletin-delete-form').forEach(form => {
        form.addEventListener('submit', function (event) {
            if (this.dataset.confirmed === 'true') return;

            event.preventDefault();
            const destroy = () => {
                this.dataset.confirmed = 'true';
                this.requestSubmit();
            };

            if (!window.Swal) {
                destroy();
                return;
            }

            Swal.fire({
                ...swalConfig,
                title: 'Supprimer ce bulletin ?',
                text: 'Cette action est irréversible.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
                iconColor: '#e11d48'
            }).then(result => {
                if (result.isConfirmed) destroy();
            });
        });
    });
</script>
@endpush