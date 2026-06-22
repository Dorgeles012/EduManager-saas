@extends('client.layouts.app')
@section('title', 'EduManager - Bulletins Scolaires')
@section('content')
<!-- Page Header -->
<div class="mb-8 flex justify-between items-end">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-primary mb-1">Bulletins Scolaires</h2>
        <p class="font-body-md text-body-md text-on-surface-variant">Consultez et gérez les bulletins des élèves</p>
    </div>
    <button class="bg-primary text-on-primary px-6 py-2.5 rounded-lg font-label-md text-label-md flex items-center gap-2 hover:opacity-90 active:scale-95 transition-all card-shadow" onclick="toggleModal(true)">
        <span class="material-symbols-outlined">add</span>
        Générer un bulletin
    </button>
</div>

<!-- Stats Grid -->
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

<!-- Filter Section -->
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
                <option value="s1">Semestre 1</option>
                <option value="s2">Semestre 2</option>
                <option value="an">Annuel</option>
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

<!-- Table Section -->
<div class="bg-surface-container-lowest rounded-xl card-shadow border border-outline-variant/30 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low border-b border-outline-variant">
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Élève</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Classe</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Période</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Moyenne Générale</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Appréciation</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px] text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportCards ?? [] as $report)
                <tr class="border-b border-outline-variant/50 hover:bg-surface-container-low transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary-fixed flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-[18px]">person</span>
                            </div>
                            <span class="font-body-md font-medium">{{ $report['student_name'] }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-on-surface-variant">{{ $report['class_name'] }}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-label-sm bg-secondary-container/20 text-on-secondary-container">
                            {{ $report['period'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-bold text-primary">{{ $report['average'] }}</span>
                        <span class="text-on-surface-variant">/20</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-label-sm {{ $report['appreciation_class'] }}">
                            {{ $report['appreciation'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button class="p-2 text-primary hover:bg-primary-fixed rounded-lg transition-colors view-action" onclick="openReportModal({{ json_encode($report) }})" title="Voir le bulletin">
                            <span class="material-symbols-outlined">visibility</span>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td class="px-6 py-20 text-center" colspan="6">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-20 h-20 bg-surface-container-low rounded-full flex items-center justify-center mb-4 text-outline">
                                <span class="material-symbols-outlined text-4xl">analytics</span>
                            </div>
                            <h3 class="font-headline-md text-on-surface mb-1">Aucune donnée disponible</h3>
                            <p class="text-on-surface-variant font-body-md">Aucune note n'a été saisie pour le moment pour cette période.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(($reportCards ?? collect())->isNotEmpty())
    <div class="px-6 py-4 border-t border-outline-variant bg-surface-container-low/30 flex items-center justify-between">
        <span class="text-label-sm text-text-muted">
            Affichage de {{ $reportCards->firstItem() ?? 1 }} à {{ $reportCards->lastItem() ?? count($reportCards ?? []) }} sur {{ $reportCards->total() ?? count($reportCards ?? []) }} bulletins
        </span>
        <div class="flex gap-2">
            {{ $reportCards->links() ?? '' }}
        </div>
    </div>
    @endif
</div>

<!-- Modal for Bulletin Visualization -->
<div class="fixed inset-0 z-[100] hidden items-center justify-center bg-on-surface/50 backdrop-blur-sm transition-opacity duration-300" id="viewBulletinModal">
    <div class="bg-surface-container-lowest w-full max-w-4xl rounded-2xl shadow-2xl overflow-hidden scale-95 transition-transform duration-300 mx-4">
        <!-- Modal Header -->
        <div class="px-8 py-6 border-b border-outline-variant flex justify-between items-center bg-primary-container text-on-primary">
            <div class="flex items-center gap-4">
                <span class="material-symbols-outlined text-3xl">description</span>
                <div>
                    <h2 class="font-headline-md text-headline-md">Bulletin Scolaire</h2>
                    <p class="text-label-sm opacity-90">Année Académique {{ $currentYear ?? '2024-2025' }}</p>
                </div>
            </div>
            <button class="p-2 hover:bg-white/10 rounded-full transition-colors" onclick="closeReportModal()">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <!-- Modal Content -->
        <div class="p-8">
            <div class="grid grid-cols-2 gap-8 mb-8">
                <div class="space-y-4">
                    <div class="flex gap-4 p-4 bg-surface-container-low rounded-xl">
                        <img alt="Student" class="w-16 h-16 rounded-lg object-cover" id="modalStudentAvatar" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBHes6u7iGHDQrihPpMOzFeLMrf7krtSXagoApIORaS-UUq0zlAB64YBLqfrBnuvJfPMFWjw5k1WHFo9jF_F1V4o3-4o4qJHVXIVOY3yk8y7-HExDugjYOedNrFaetF8sSvVuzcnzKqxWMSxsTIu2_tDHMCVNR54u0FSngEoRB22aX6SWv4Fnc3UKTsI0Zfh0eDGWdBweVQ-m6BjaB0Om6DR3uF-wsmk3YVZoDNMVpnurxhpJvYHp2OnoZabAaoPcP9C_TTjDbJeZhM">
                        <div>
                            <p class="text-label-sm text-outline uppercase font-bold">Élève</p>
                            <p class="font-headline-md text-on-surface" id="modalStudentName">Jean-Marc Bakary</p>
                            <p class="text-body-sm text-on-surface-variant" id="modalStudentMatricule">Matricule: #2024-0012</p>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 border border-outline-variant rounded-xl">
                        <p class="text-label-sm text-outline uppercase font-bold">Classe</p>
                        <p class="font-body-lg text-on-surface" id="modalClassName">3ème A - Sciences</p>
                    </div>
                    <div class="p-4 border border-outline-variant rounded-xl">
                        <p class="text-label-sm text-outline uppercase font-bold">Période</p>
                        <p class="font-body-lg text-on-surface" id="modalPeriod">1er Trimestre</p>
                    </div>
                </div>
            </div>
            <!-- Scores Summary -->
            <div class="flex items-center justify-between p-8 bg-surface rounded-2xl border-2 border-primary/10 mb-8">
                <div class="text-center px-8 border-r border-outline-variant">
                    <p class="text-label-sm text-outline uppercase mb-2">Moyenne Générale</p>
                    <div class="flex items-baseline justify-center gap-1">
                        <span class="text-5xl font-extrabold text-primary" id="modalAverage">16.45</span>
                        <span class="text-xl font-bold text-outline">/20</span>
                    </div>
                </div>
                <div class="flex-1 px-8">
                    <p class="text-label-sm text-outline uppercase mb-2 text-center">Appréciation Globale</p>
                    <div class="px-6 py-3 rounded-full text-center font-bold text-headline-md" id="modalAppreciation">
                        Excellent Travail
                    </div>
                </div>
                <div class="text-center px-8 border-l border-outline-variant">
                    <p class="text-label-sm text-outline uppercase mb-2">Rang</p>
                    <div class="flex items-baseline justify-center gap-1">
                        <span class="text-4xl font-extrabold text-on-surface" id="modalRank">02</span>
                        <span class="text-lg font-bold text-outline" id="modalTotalStudents">/ 35</span>
                    </div>
                </div>
            </div>
            <!-- Footer Actions -->
            <div class="flex justify-end gap-4 border-t border-outline-variant pt-6">
                <button class="px-6 py-2.5 border border-outline text-on-surface-variant rounded-lg font-label-md hover:bg-surface-container-low transition-all" onclick="downloadPDF()">
                    Télécharger PDF
                </button>
                <button class="px-8 py-2.5 bg-primary text-on-primary rounded-lg font-label-md flex items-center gap-2 hover:opacity-90 active:scale-95 transition-all" onclick="printReport()">
                    <span class="material-symbols-outlined">print</span>
                    Imprimer
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        vertical-align: middle;
    }
    body { background-color: #f9f9ff; }
    .card-shadow { box-shadow: 0 4px 12px rgba(55, 48, 163, 0.04); }
    
    /* Appreciation classes */
    .appreciation-excellent { background-color: #05966910; color: #059669; }
    .appreciation-good { background-color: #D9770610; color: #D97706; }
    .appreciation-average { background-color: #64748B10; color: #64748B; }
    .appreciation-poor { background-color: #E11D4810; color: #E11D48; }
</style>
@endpush

@push('scripts')
<script>
    let currentReport = null;

    function toggleModal(open) {
        if (open) {
            // Pour générer un nouveau bulletin
            Swal.fire({
                title: 'Générer un bulletin',
                text: 'Cette fonctionnalité permet de générer des bulletins pour les élèves.',
                icon: 'info',
                confirmButtonColor: '#1f108e',
                confirmButtonText: 'Continuer'
            });
        }
    }

    function openReportModal(report) {
        currentReport = report;
        
        // Remplir les données du modal
        document.getElementById('modalStudentName').textContent = report.student_name;
        document.getElementById('modalStudentMatricule').textContent = report.matricule || '#2024-0012';
        document.getElementById('modalClassName').textContent = report.class_name;
        document.getElementById('modalPeriod').textContent = report.period;
        document.getElementById('modalAverage').textContent = report.average;
        document.getElementById('modalRank').textContent = report.rank || '--';
        document.getElementById('modalTotalStudents').textContent = `/ ${report.total_students || 35}`;
        
        // Appréciation avec style
        const appreciationDiv = document.getElementById('modalAppreciation');
        appreciationDiv.textContent = report.appreciation;
        appreciationDiv.className = `px-6 py-3 rounded-full text-center font-bold text-headline-md ${report.appreciation_class || 'appreciation-excellent'}`;
        
        const modal = document.getElementById('viewBulletinModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.querySelector('div').classList.remove('scale-95');
        }, 10);
    }

    function closeReportModal() {
        const modal = document.getElementById('viewBulletinModal');
        modal.classList.add('opacity-0');
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            modal.classList.remove('opacity-0');
        }, 300);
    }

    function resetFilters() {
        document.getElementById('filterClass').value = '';
        document.getElementById('filterPeriod').value = 't1';
        
        Swal.fire({
            title: 'Filtres réinitialisés',
            text: 'Les filtres ont été réinitialisés avec succès.',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false,
            confirmButtonColor: '#1f108e'
        });
    }

    function downloadPDF() {
        if (currentReport) {
            Swal.fire({
                title: 'Téléchargement',
                text: `Téléchargement du bulletin de ${currentReport.student_name} en cours...`,
                icon: 'info',
                timer: 2000,
                showConfirmButton: false,
                confirmButtonColor: '#1f108e'
            });
        }
    }

    function printReport() {
        if (currentReport) {
            Swal.fire({
                title: 'Impression',
                text: `Préparation de l'impression du bulletin de ${currentReport.student_name}...`,
                icon: 'info',
                timer: 2000,
                showConfirmButton: false,
                confirmButtonColor: '#1f108e'
            });
        }
    }

    // Close modal on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const modal = document.getElementById('viewBulletinModal');
            if (modal && !modal.classList.contains('hidden')) {
                closeReportModal();
            }
        }
    });

    // Close modal on click outside
    window.onclick = function(event) {
        const modal = document.getElementById('viewBulletinModal');
        if (event.target === modal) {
            closeReportModal();
        }
    }
</script>
@endpush