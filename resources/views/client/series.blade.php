@extends('client.layouts.app')
@section('title', 'EduManager - Séries')
@section('content')
<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-on-surface">
            <span class="text-primary">Gestion des Matières</span>
        </h2>
        <p class="font-body-md text-body-md text-text-muted mt-1">Créez et gérez les séries du secondaire</p>
    </div>
    <button class="flex items-center gap-2 px-6 py-2.5 bg-primary text-on-primary px-6 py-2.5 rounded-lg font-label-md text-label-md flex items-center gap-2 hover:opacity-90 active:scale-95 transition-all card-shadow" onclick="openModal('addModal')">
        <span class="material-symbols-outlined">add</span>
        Ajouter une série
    </button>
</div>

<!-- Bento Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-gutter-desktop mb-10">
    <div class="bg-surface-container-lowest p-6 rounded-xl shadow-[4px_4px_12px_rgba(55,48,163,0.04)] border border-outline-variant/30 flex items-center gap-5">
        <div class="w-14 h-14 rounded-full bg-primary-container/10 flex items-center justify-center text-primary">
            <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">school</span>
        </div>
        <div>
            <p class="text-text-muted text-sm font-medium">Séries disponibles</p>
            <p class="text-3xl font-headline-md text-primary">{{ $totalSeries ?? 0 }}</p>
        </div>
    </div>

    

    <div class="bg-surface-container-lowest p-6 rounded-xl shadow-[4px_4px_12px_rgba(55,48,163,0.04)] border border-outline-variant/30 flex items-center gap-5">
        <div class="w-14 h-14 rounded-full bg-warning-amber/10 flex items-center justify-center text-warning-amber">
            <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">assignment</span>
        </div>
        <div>
            <p class="text-text-muted text-sm font-medium">Assoc. matières</p>
            <p class="text-3xl font-headline-md text-warning-amber">—</p>
        </div>
    </div>
</div>

<!-- Data Table Container -->
<div class="bg-surface-container-lowest rounded-xl shadow-[0_4px_24px_rgba(55,48,163,0.04)] border border-outline-variant overflow-hidden">
    <div class="px-6 py-5 border-b border-surface-subtle flex justify-between items-center bg-white">
        <h3 class="font-headline-md text-headline-md text-on-surface">
            <span class="text-primary">Liste des Séries</span>
        </h3>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left zebra-table">
            <thead>
                <tr class="bg-surface-subtle/50 text-slate-600">
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">N°</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Nom de la série</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px]">Classe</th>
                    <th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider text-[12px] text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/20">
                @forelse($series ?? [] as $s)
                <tr class="hover:bg-primary/5 transition-colors group">
                    <td class="px-6 py-4 text-on-surface-variant">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded bg-primary/10 flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-lg">filter_alt</span>
                            </div>
                            <span class="font-medium text-on-surface">{{ $s['nom_serie'] }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-on-surface-variant">{{ $s['classe'] }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <button class="p-2 text-primary hover:bg-primary/10 rounded-lg transition-all" onclick="editSeries({{ $s['id'] }}, @js($s['nom_serie']), @js($s['id_classes']))" title="Modifier">
                                <span class="material-symbols-outlined">edit</span>
                            </button>
                            <form action="{{ route('client.series.destroy', $s['id']) }}" method="POST" class="inline delete-series-form">
                                @csrf
                                @method('DELETE')
                                <button class="p-2 text-alert-red hover:bg-alert-red/10 rounded-lg transition-all delete-series-btn" data-name="{{ $s['nom_serie'] }}" type="button" title="Supprimer">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-on-surface-variant">Aucune série enregistrée</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Add Modal -->
<div class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4" id="addModal">
    <div class="absolute inset-0 modal-backdrop backdrop-blur-md bg-black/30" onclick="closeModal('addModal')"></div>
    <div class="bg-white w-full max-w-md rounded-xl shadow-2xl relative z-10 overflow-hidden transform transition-all scale-95 opacity-0 duration-300" id="addModalContent">
        <div class="p-6 border-b border-outline-variant/30 flex justify-between items-center bg-primary text-on-primary">
            <h3 class="font-headline-md text-headline-md">Ajouter une série</h3>
            <button class="hover:bg-white/20 p-1 rounded-full transition-colors" onclick="closeModal('addModal')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form class="p-6 space-y-5" id="addSeriesForm" action="{{ route('client.series.store') }}" method="POST">
            @csrf
            <div class="space-y-2">
                <label class="font-label-md text-on-surface">Classes</label>
                <select name="id_classes[]" multiple required class="w-full px-4 py-2.5 rounded-lg border border-outline-variant" size="5">
                    @foreach($classes as $classe)
                        <option value="{{ $classe->id }}" @selected(collect(old('id_classes'))->contains($classe->id))>{{ $classe->nom }}</option>
                    @endforeach
                </select>
                <p class="text-[11px] text-text-muted">Maintenez Ctrl/Cmd pour sélectionner plusieurs classes.</p>
            </div>

            <div class="space-y-2">
                <label class="font-label-md text-on-surface">Nom de la série</label>
                <input class="w-full px-4 py-2.5 rounded-lg border border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all" name="nom_serie" placeholder="Ex: Série A1" required type="text">
                <p class="text-[11px] text-text-muted">Exemples: A1, A2, B, C, D, F2, F3, G1, G2</p>
            </div>

            <div class="pt-4 flex gap-3">
                <button class="flex-1 px-4 py-2.5 border border-outline-variant rounded-lg font-label-md text-on-surface-variant hover:bg-surface-container-low transition-colors" onclick="closeModal('addModal')" type="button">Annuler</button>
                <button class="flex-1 px-4 py-2.5 bg-primary text-on-primary rounded-lg font-label-md hover:bg-primary-container transition-colors" type="submit">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4" id="editModal">
    <div class="absolute inset-0 modal-backdrop backdrop-blur-md bg-black/30" onclick="closeModal('editModal')"></div>
    <div class="bg-white w-full max-w-md rounded-xl shadow-2xl relative z-10 overflow-hidden transform transition-all scale-95 opacity-0 duration-300" id="editModalContent">
        <div class="p-6 border-b border-outline-variant/30 flex justify-between items-center bg-primary text-on-primary">
            <h3 class="font-headline-md text-headline-md">Modifier la série</h3>
            <button class="hover:bg-white/20 p-1 rounded-full transition-colors" onclick="closeModal('editModal')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form class="p-6 space-y-5" id="editSeriesForm" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-2">
                <label class="font-label-md text-on-surface">Classes</label>
                <select id="editSerieClasses" name="id_classes[]" multiple required class="w-full px-4 py-2.5 rounded-lg border border-outline-variant" size="5">
                    @foreach($classes as $classe)<option value="{{ $classe->id }}">{{ $classe->nom }}</option>@endforeach
                </select>
            </div>

            <div class="space-y-2">
                <label class="font-label-md text-on-surface">Nom de la série</label>
                <input class="w-full px-4 py-2.5 rounded-lg border border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all" id="editNomSerie" name="nom_serie" required type="text">
            </div>

            <div class="pt-4 flex gap-3">
                <button class="flex-1 px-4 py-2.5 border border-outline-variant rounded-lg font-label-md text-on-surface-variant hover:bg-surface-container-low transition-colors" onclick="closeModal('editModal')" type="button">Annuler</button>
                <button class="flex-1 px-4 py-2.5 bg-primary text-on-primary rounded-lg font-label-md hover:bg-primary-container transition-colors" type="submit">Appliquer</button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Animation styles for modals */
    #addModal, #editModal {
        transition: opacity 0.3s ease;
    }

    .modal-backdrop {
        transition: backdrop-filter 0.3s ease;
    }

    /* SweetAlert custom styles - légèrement augmenté */
    .swal2-popup {
        font-size: 0.95rem !important;
        padding: 1.5rem !important;
    }
    
    .swal2-title {
        font-size: 1.3rem !important;
        padding: 0.8rem 0 0.5rem 0 !important;
    }
    
    .swal2-html-container {
        font-size: 0.95rem !important;
        padding: 0.5rem 0 1rem 0 !important;
    }
    
    .swal2-confirm, .swal2-cancel {
        font-size: 0.9rem !important;
        padding: 0.6rem 1.5rem !important;
        margin: 0 0.3rem !important;
    }
    
    .swal2-timer-progress-bar {
        height: 3px !important;
    }
    
    .swal2-icon {
        font-size: 0.7rem !important;
    }
    
    .swal2-close {
        font-size: 1.2rem !important;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        const content = document.getElementById(id + 'Content');

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        const content = document.getElementById(id + 'Content');

        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }, 300);
    }

    function editSeries(id, nomSerie, classeIds) {
        // Pour la compat UI: classeId = première classe (si série multi-classes)
        document.getElementById('editSeriesForm').action = `{{ url('/client/series') }}/${id}`;
        document.getElementById('editNomSerie').value = nomSerie;

        const select = document.getElementById('editSerieClasses');
        if (select) {
            const selectedIds = (classeIds ?? []).map(String);
            Array.from(select.options).forEach(opt => {
                opt.selected = selectedIds.includes(String(opt.value));
            });
        }

        openModal('editModal');
    }


    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.delete-series-btn').forEach((button) => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();

                Swal.fire({
                    title: `Supprimer la série « ${this.dataset.name} » ?`,
                    icon: 'warning',
                    showCancelButton: true,
                    cancelButtonText: 'Annuler',
                    confirmButtonText: 'Oui, supprimer',
                    confirmButtonColor: '#d33',
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.closest('form').submit();
                    }
                });
            }, true);
        });

        // Affichage success/error via SweetAlert si dispo
        @if(session('success'))
            Swal?.fire?.({
                icon: 'success',
                title: 'Succès',
                text: @json(session('success')),
                timer: 2500,
                showConfirmButton: false,
            });
        @endif

        @if(session('error'))
            Swal?.fire?.({
                icon: 'error',
                title: 'Erreur',
                text: @json(session('error')),
                timer: 3000,
                showConfirmButton: false,
            });
        @endif
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const addModal = document.getElementById('addModal');
            const editModal = document.getElementById('editModal');

            if (addModal && !addModal.classList.contains('hidden')) {
                closeModal('addModal');
            }
            if (editModal && !editModal.classList.contains('hidden')) {
                closeModal('editModal');
            }
        }
    });
</script>
@endsection
