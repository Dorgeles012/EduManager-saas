@extends('client.layouts.app')


@section('content')
<!-- Header Section -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-primary">Gestion des Années Académiques</h2>
        <p class="font-body-md text-text-muted mt-1">Gérez les années scolaires et universitaires</p>
    </div>
    <button class="flex items-center gap-2 px-6 py-3 bg-primary-container text-on-primary rounded-lg font-label-md text-label-md hover:bg-opacity-90 transition-all shadow-sm active:scale-95" onclick="openModal('addModal')">
        <span class="material-symbols-outlined">add</span>
        Ajouter une année
    </button>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-gutter-desktop mb-10">
    <div class="bg-surface-container-lowest p-6 rounded-xl shadow-[4px_4px_12px_rgba(55,48,163,0.04)] border border-outline-variant/30 flex items-center gap-5">
        <div class="w-12 h-12 rounded-full bg-primary-fixed flex items-center justify-center">
            <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">calendar_month</span>
        </div>
        <div>
            <p class="font-label-sm text-text-muted uppercase tracking-wider">Années enregistrées</p>
            <h3 class="font-headline-md text-headline-md text-on-surface">{{ $totalYears ?? 1 }}</h3>
        </div>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-xl shadow-[4px_4px_12px_rgba(55,48,163,0.04)] border border-outline-variant/30 flex items-center gap-5">
        <div class="w-12 h-12 rounded-full bg-secondary-container/30 flex items-center justify-center">
            <span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1;">update</span>
        </div>
        <div>
            <p class="font-label-sm text-text-muted uppercase tracking-wider">Ajouts récents</p>
            <h3 class="font-headline-md text-headline-md text-on-surface">{{ $recentAdds ?? 1 }}</h3>
        </div>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-xl shadow-[4px_4px_12px_rgba(55,48,163,0.04)] border border-outline-variant/30 flex items-center gap-5 opacity-40">
        <div class="w-12 h-12 rounded-full bg-surface-subtle flex items-center justify-center">
            <span class="material-symbols-outlined text-outline">history</span>
        </div>
        <div>
            <p class="font-label-sm text-text-muted uppercase tracking-wider">Archives</p>
            <h3 class="font-headline-md text-headline-md text-on-surface">{{ $archivedYears ?? 0 }}</h3>
        </div>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-xl shadow-[4px_4px_12px_rgba(55,48,163,0.04)] border border-outline-variant/30 flex items-center gap-5">
        <div class="w-12 h-12 rounded-full bg-secondary-container/30 flex items-center justify-center">
            <span class="material-symbols-outlined text-secondary">check_circle</span>
        </div>
        <div>
            <p class="font-label-sm text-text-muted uppercase tracking-wider">Statut Actif</p>
            <h3 class="font-headline-md text-headline-md text-on-surface">{{ $activeYear ?? '2025-2026' }}</h3>
        </div>
    </div>
</div>

<!-- Main Content Card -->
<div class="bg-surface-container-lowest rounded-xl shadow-[4px_4px_12px_rgba(55,48,163,0.04)] border border-outline-variant/30 overflow-hidden">
    <div class="p-6 border-b border-surface-subtle flex justify-between items-center">
        <h4 class="font-headline-md text-headline-md text-on-surface">Liste des Années Académiques</h4>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-subtle/50">
                    <th class="px-6 py-4 font-label-sm text-label-sm text-text-muted uppercase tracking-wider">N°</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-text-muted uppercase tracking-wider">Libellé de l'année</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-text-muted uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-text-muted uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-subtle">
                @forelse($academicYears ?? [['id' => 1, 'label' => '2025-2026', 'status' => 'PLANIFIÉ']] as $year)
                <tr class="hover:bg-surface-subtle/20 transition-colors">
                    <td class="px-6 py-4 font-body-sm text-body-sm text-on-surface">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded bg-primary-fixed flex items-center justify-center">
                                <span class="material-symbols-outlined text-primary text-[18px]">calendar_today</span>
                            </div>
                            <span class="font-label-md text-label-md text-on-surface">{{ $year['label'] }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 bg-secondary-container/20 text-on-secondary-container text-[12px] font-bold rounded-full">{{ $year['status'] }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <button class="p-2 text-primary hover:bg-primary-fixed rounded-lg transition-all" onclick="openModal('detailModal', '{{ $year['label'] }}')" title="Voir les détails">
                                <span class="material-symbols-outlined">visibility</span>
                            </button>
                            <button class="p-2 text-alert-red hover:bg-error-container/30 rounded-lg transition-all" onclick="confirmDelete({{ $year['id'] }})" title="Supprimer">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-20 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-48 h-48 bg-surface-subtle rounded-full flex items-center justify-center mb-6">
                                <span class="material-symbols-outlined text-[80px] text-outline-variant">calendar_add_on</span>
                            </div>
                            <h5 class="font-headline-md text-headline-md text-on-surface">Aucune donnée trouvée</h5>
                            <p class="text-text-muted mt-2 max-w-sm">Commencez par ajouter votre première année académique pour structurer votre portail.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: Ajouter une année -->
<div class="fixed inset-0 bg-inverse-surface/40 backdrop-blur-sm z-[100] hidden items-center justify-center p-4" id="addModal">
    <div class="bg-surface-container-lowest w-full max-w-md rounded-2xl shadow-xl overflow-hidden">
        <div class="p-6 border-b border-surface-subtle flex justify-between items-center">
            <h3 class="font-headline-md text-headline-md text-primary">Nouvelle Année Académique</h3>
            <button class="text-outline hover:text-on-surface transition-colors" onclick="closeModal('addModal')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form class="p-6 space-y-4" id="addYearForm">
            <div>
                <label class="block font-label-sm text-label-sm text-on-surface-variant mb-2" for="yearLabel">Libellé de l'année académique</label>
                <input class="w-full px-4 py-3 bg-white border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary-container outline-none transition-all font-body-md" id="yearLabel" placeholder="Ex: 2026-2027" required type="text">
                <p class="text-label-sm text-text-muted mt-2">Utilisez le format AAAA-AAAA (ex: 2025-2026).</p>
            </div>
            <div class="pt-4 flex gap-3">
                <button class="flex-1 px-4 py-3 border border-outline-variant text-on-surface-variant rounded-lg font-label-md hover:bg-surface-subtle transition-all" onclick="closeModal('addModal')" type="button">
                    Annuler
                </button>
                <button class="flex-1 px-4 py-3 bg-primary-container text-on-primary rounded-lg font-label-md hover:shadow-lg transition-all active:scale-95" type="submit">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Détails -->
<div class="fixed inset-0 bg-inverse-surface/40 backdrop-blur-sm z-[100] hidden items-center justify-center p-4" id="detailModal">
    <div class="bg-surface-container-lowest w-full max-w-md rounded-2xl shadow-xl overflow-hidden">
        <div class="p-6 bg-primary-container text-on-primary flex justify-between items-center">
            <h3 class="font-headline-md text-headline-md">Détails de l'année</h3>
            <button class="text-on-primary/80 hover:text-on-primary transition-colors" onclick="closeModal('detailModal')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-8 space-y-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-primary-fixed flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary">label</span>
                </div>
                <div>
                    <p class="text-label-sm text-text-muted">Libellé</p>
                    <p class="font-headline-md text-headline-md text-on-surface" id="detailYearLabel">2025-2026</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-secondary-container/30 flex items-center justify-center">
                    <span class="material-symbols-outlined text-secondary">event_available</span>
                </div>
                <div>
                    <p class="text-label-sm text-text-muted">Date de création</p>
                    <p class="font-body-md text-body-md text-on-surface" id="currentDateDisplay"></p>
                </div>
            </div>
            <div class="p-4 bg-surface-subtle rounded-xl flex items-start gap-3">
                <span class="material-symbols-outlined text-primary text-[20px]">info</span>
                <p class="text-body-sm text-on-surface-variant italic">Cette année académique est actuellement configurée comme "Planifiée". Elle pourra être activée lors de la clôture de l'année en cours.</p>
            </div>
            <button class="w-full py-3 bg-surface-subtle text-on-surface font-label-md rounded-lg hover:bg-outline-variant/30 transition-all" onclick="closeModal('detailModal')">
                Fermer
            </button>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .modal-active {
        display: flex !important;
    }
</style>
@endpush

@push('scripts')
<script>
    // Modal Logic
    function openModal(id, yearLabel = null) {
        if (id === 'detailModal' && yearLabel) {
            document.getElementById('detailYearLabel').textContent = yearLabel;
        }
        document.getElementById(id).classList.add('modal-active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('modal-active');
        document.body.style.overflow = 'auto';
    }

    // Close modal on background click
    window.onclick = function(event) {
        if (event.target.classList.contains('fixed')) {
            event.target.classList.remove('modal-active');
            document.body.style.overflow = 'auto';
        }
    }

    // Add Form Logic
    const addYearForm = document.getElementById('addYearForm');
    if (addYearForm) {
        addYearForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const yearInput = document.getElementById('yearLabel').value;
            
            // Basic validation
            const regex = /^\d{4}-\d{4}$/;
            if (!regex.test(yearInput)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Format invalide',
                    text: 'Veuillez respecter le format AAAA-AAAA (ex: 2025-2026)',
                    confirmButtonColor: '#3730a3'
                });
                return;
            }

            Swal.fire({
                title: 'Confirmation',
                text: `Souhaitez-vous enregistrer l'année ${yearInput} ?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Enregistrer',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#3730a3',
                cancelButtonColor: '#64748B'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Succès !',
                        text: "L'année académique a été ajoutée avec succès.",
                        icon: 'success',
                        confirmButtonColor: '#3730a3'
                    });
                    closeModal('addModal');
                    addYearForm.reset();
                }
            });
        });
    }

    // Delete Logic
    function confirmDelete(id) {
        Swal.fire({
            title: 'Êtes-vous sûr ?',
            text: "Cette action est irréversible et pourrait affecter les inscriptions liées.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ba1a1a',
            cancelButtonColor: '#64748B',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Supprimé !',
                    text: "L'année académique a été supprimée.",
                    icon: 'success',
                    confirmButtonColor: '#3730a3'
                });
            }
        });
    }

    // Display today's date in details
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    const dateDisplay = document.getElementById('currentDateDisplay');
    if (dateDisplay) {
        dateDisplay.textContent = new Date().toLocaleDateString('fr-FR', options);
    }
</script>
@endpush