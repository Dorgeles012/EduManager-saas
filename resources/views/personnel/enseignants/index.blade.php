@extends('personnel.layouts.app')
@section('title', 'EduManager - Enseignants')
@section('content')
<div class="flex justify-between items-end mb-6">
    <div>
        <h2 class="font-headline-md text-headline-md text-primary mb-0.5">Gestion des Enseignants</h2>
        <p class="text-body-sm text-text-muted text-sm">Gérez les enseignants de votre établissement, leurs affectations et leurs emplois du temps.</p>
    </div>
    <a href="{{ route('personnel.enseignants.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary text-white rounded-lg text-sm hover:opacity-90 active:scale-95 transition-all shadow-md">
        <span class="material-symbols-outlined text-base">person_add</span>
        Ajouter un enseignant
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="glass-card p-4 rounded-xl flex items-center gap-4 shadow-[4px_4px_12px_rgba(55,48,163,0.04)]">
        <div class="w-11 h-11 rounded-full bg-primary-fixed flex items-center justify-center text-primary">
            <span class="material-symbols-outlined text-2xl">school</span>
        </div>
        <div>
            <h3 class="text-xs text-text-muted">Enseignants</h3>
            <p class="text-headline-lg font-headline-lg text-on-surface">{{ $totalTeachers ?? 0 }}</p>
        </div>
    </div>
    <div class="glass-card p-4 rounded-xl flex items-center gap-4 shadow-[4px_4px_12px_rgba(55,48,163,0.04)]">
        <div class="w-11 h-11 rounded-full bg-secondary-container flex items-center justify-center text-secondary">
            <span class="material-symbols-outlined text-2xl">menu_book</span>
        </div>
        <div>
            <h3 class="text-xs text-text-muted">Matières actives</h3>
            <p class="text-headline-lg font-headline-lg text-on-surface">{{ $totalSubjects ?? 0 }}</p>
        </div>
    </div>
    <div class="glass-card p-4 rounded-xl flex items-center gap-4 shadow-[4px_4px_12px_rgba(55,48,163,0.04)]">
        <div class="w-11 h-11 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700">
            <span class="material-symbols-outlined text-2xl">event_note</span>
        </div>
        <div>
            <h3 class="text-xs text-text-muted">Emplois du temps</h3>
            <p class="text-headline-lg font-headline-lg text-on-surface">{{ $totalSchedules ?? 0 }}</p>
        </div>
    </div>
</div>

<div class="glass-card rounded-xl overflow-hidden shadow-[0_4px_12px_rgba(55,48,163,0.04)]">
    <div class="px-4 py-3 border-b border-surface-subtle bg-surface-container-low flex flex-col md:flex-row justify-between items-center gap-3">
        <h4 class="font-headline-sm text-headline-sm text-primary text-base flex items-center gap-2">
            <span class="material-symbols-outlined">badge</span>
            Liste des enseignants
        </h4>
        <div class="relative w-full md:w-80">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-text-muted text-lg">search</span>
            <input type="text" id="searchTeacher" placeholder="Rechercher par nom, email, matricule..." class="w-full pl-9 pr-3 py-1.5 text-sm border border-outline-variant rounded-lg focus:ring-primary focus:border-primary bg-white">
        </div>
    </div>
    @if(($teachers ?? collect())->isEmpty())
    <div class="min-h-[160px] flex flex-col items-center justify-center text-center p-6">
        <div class="w-16 h-16 bg-surface-container rounded-full flex items-center justify-center mb-3">
            <span class="material-symbols-outlined text-primary text-3xl">school</span>
        </div>
        <h3 class="font-headline-sm text-headline-sm text-on-surface mb-1 text-base">Aucun enseignant enregistré</h3>
        <p class="text-xs text-text-muted">Cliquez sur « Ajouter un enseignant » pour enregistrer le premier enseignant.</p>
    </div>
    @else
    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left text-sm border-separate border-spacing-y-1" id="teachersTable">
            <thead class="bg-surface-container-low text-xs uppercase tracking-wider text-text-muted">
                <tr>
                    <th class="px-3 py-2.5 font-semibold">#</th>
                    <th class="px-3 py-2.5 font-semibold min-w-[180px]">Nom &amp; Prénoms</th>
                    <th class="px-3 py-2.5 font-semibold">Matricule</th>
                    <th class="px-3 py-2.5 font-semibold">Email</th>
                    <th class="px-3 py-2.5 font-semibold">Téléphone</th>
                    <th class="px-3 py-2.5 font-semibold min-w-[140px]">Matière(s)</th>
                    <th class="px-3 py-2.5 font-semibold min-w-[140px]">Classe(s)</th>
                    <th class="px-3 py-2.5 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-subtle" id="teachersTableBody">
                @foreach($teachers as $teacher)
                <tr class="hover:bg-surface-container-low transition-colors rounded-lg shadow-sm bg-white teacher-row"
                    data-name="{{ strtolower($teacher['firstname'] . ' ' . $teacher['lastname']) }}"
                    data-email="{{ strtolower($teacher['email']) }}"
                    data-matricule="{{ strtolower($teacher['matricule'] ?? '') }}"
                    data-subject="{{ strtolower($teacher['subject']) }}">
                    <td class="px-3 py-2.5 align-middle">{{ $loop->iteration }}</td>
                    <td class="px-3 py-2.5 min-w-[180px] align-middle">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-bold overflow-hidden flex-shrink-0">
                                @if(!empty($teacher['photo']))
                                    <img src="{{ asset('storage/' . $teacher['photo']) }}" alt="" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($teacher['firstname'] ?? 'E', 0, 1) . substr($teacher['lastname'] ?? '', 0, 1)) }}
                                @endif
                            </div>
                            <span class="font-medium whitespace-nowrap text-sm text-on-surface">{{ ucfirst($teacher['firstname']) }} {{ ucfirst($teacher['lastname']) }}</span>
                        </div>
                    </td>
                    <td class="px-3 py-2.5 text-text-muted align-middle text-sm font-mono">{{ $teacher['matricule'] ?? 'N/A' }}</td>
                    <td class="px-3 py-2.5 text-text-muted align-middle text-sm">{{ $teacher['email'] }}</td>
                    <td class="px-3 py-2.5 text-text-muted align-middle text-sm">{{ $teacher['phone'] }}</td>
                    <td class="px-3 py-2.5 align-middle">
                        <span class="px-2 py-0.5 bg-primary/10 text-primary rounded-full text-xs font-medium">{{ $teacher['subject'] }}</span>
                    </td>
                    <td class="px-3 py-2.5 text-text-muted align-middle text-sm">
                        <span class="truncate block max-w-[160px]" title="{{ $teacher['classes'] }}">{{ $teacher['classes'] }}</span>
                    </td>
                    <td class="px-3 py-2.5 text-right align-middle">
                        <div class="flex justify-end items-center gap-1">
                            <button type="button" class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-primary hover:bg-primary-fixed transition-colors" title="Gérer l'emploi du temps" onclick="handleEmploiTempsPlus({{ $teacher['id'] }}, this, event)">
                                <span class="material-symbols-outlined text-base">event_note</span>
                            </button>
                            <a href="{{ route('personnel.enseignants.edit', $teacher['id']) }}" class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-warning-amber hover:bg-warning-amber/10 transition-colors" title="Modifier">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </a>
                            <form action="{{ route('personnel.enseignants.destroy', $teacher['id']) }}" method="POST" class="inline delete-teacher-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-alert-red hover:bg-error-container/20 transition-colors delete-teacher-btn" data-name="{{ $teacher['firstname'] }} {{ $teacher['lastname'] }}" title="Supprimer">
                                    <span class="material-symbols-outlined text-base">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-4 py-2.5 border-t border-surface-subtle bg-surface-container-low/30 flex items-center justify-between text-sm">
        <span class="text-text-muted text-xs">Affichage de {{ $teachers->firstItem() ?? 0 }} à {{ $teachers->lastItem() ?? 0 }} sur {{ $teachers->total() ?? 0 }} enseignants</span>
        <div class="flex gap-1 text-sm">{{ $teachers->links() ?? '' }}</div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .whatsapp-popup {
        position: fixed;
        z-index: 1000;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(226, 232, 240, 1);
        min-width: 180px;
        padding: 6px;
        opacity: 0;
        transform: scale(0.95);
        transition: opacity 0.15s ease, transform 0.15s ease;
        pointer-events: none;
    }
    .whatsapp-popup.visible {
        opacity: 1;
        transform: scale(1);
        pointer-events: auto;
    }
    .popup-header {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        color: #64748b;
        padding: 4px 8px 6px;
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 4px;
    }
    .popup-item {
        display: flex;
        align-items: center;
        gap: 8px;
        width: 100%;
        padding: 6px 8px;
        border: none;
        background: transparent;
        border-radius: 6px;
        font-size: 13px;
        color: #1e293b;
        cursor: pointer;
        transition: background-color 0.15s;
        text-align: left;
    }
    .popup-item:hover:not(.disabled) {
        background-color: #f1f5f9;
    }
    .popup-item.disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }
    .popup-item.text-danger {
        color: #e11d48;
    }
    .popup-item.text-danger:hover:not(.disabled) {
        background-color: #ffe4e6;
    }
    .popup-divider {
        height: 1px;
        background-color: #f1f5f9;
        margin: 4px 0;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let activePopup = null;
let activeButton = null;

function calculatePopupPosition(buttonEl, popupEl) {
    const rect = buttonEl.getBoundingClientRect();
    const popupWidth = 190;
    const popupHeight = 180;
    
    let top = rect.bottom + 6;
    let left = rect.right - popupWidth;
    
    if (left < 10) left = rect.left;
    if (top + popupHeight > window.innerHeight) {
        top = rect.top - popupHeight - 6;
    }
    
    return { top, left };
}

function closePopup() {
    if (activePopup) {
        activePopup.classList.remove('visible');
        setTimeout(() => {
            if (activePopup) activePopup.remove();
            activePopup = null;
        }, 150);
    }
    activeButton = null;
}

window.handleEmploiTempsPlus = function(enseignantId, btnEl, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    if (activeButton === btnEl && activePopup) {
        closePopup();
        return;
    }
    
    closePopup();
    btnEl.disabled = true;
    
    const baseUrl = '{{ url('/') }}';
    
    fetch(baseUrl + '/personnel/emploi-temps/teacher/' + enseignantId + '/exists', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        btnEl.disabled = false;
        const exists = !!data.exists;
        
        const popup = document.createElement('div');
        popup.className = 'whatsapp-popup';
        
        if (exists) {
            popup.innerHTML = `
                <div class="popup-header">Emploi du temps</div>
                <button class="popup-item disabled">
                    <span class="material-symbols-outlined text-base">add_circle</span>
                    <span>Créer</span>
                </button>
                <button class="popup-item" data-action="navigate" data-url="${baseUrl}/personnel/emploi-temps/teacher/${enseignantId}/edit">
                    <span class="material-symbols-outlined text-base text-amber-600">edit</span>
                    <span>Modifier</span>
                </button>
                <button class="popup-item" data-action="navigate" data-url="${baseUrl}/personnel/emploi-temps/teacher/${enseignantId}/show">
                    <span class="material-symbols-outlined text-base text-primary">visibility</span>
                    <span>Consulter</span>
                </button>
                <div class="popup-divider"></div>
                <button class="popup-item text-danger" data-action="delete-schedule" data-teacher-id="${enseignantId}">
                    <span class="material-symbols-outlined text-base">delete</span>
                    <span>Supprimer</span>
                </button>
            `;
        } else {
            popup.innerHTML = `
                <div class="popup-header">Emploi du temps</div>
                <button class="popup-item" data-action="navigate" data-url="${baseUrl}/personnel/emploi-temps/teacher/${enseignantId}/create">
                    <span class="material-symbols-outlined text-base text-emerald-600">add_circle</span>
                    <span>Créer</span>
                </button>
                <button class="popup-item disabled">
                    <span class="material-symbols-outlined text-base">edit</span>
                    <span>Modifier</span>
                </button>
                <button class="popup-item disabled">
                    <span class="material-symbols-outlined text-base">visibility</span>
                    <span>Consulter</span>
                </button>
            `;
        }
        
        popup.querySelectorAll('.popup-item:not(.disabled)').forEach(item => {
            item.addEventListener('click', function(e) {
                e.stopPropagation();
                const action = this.dataset.action;
                if (action === 'navigate') {
                    window.location.href = this.dataset.url;
                } else if (action === 'delete-schedule') {
                    const tid = this.dataset.teacherId;
                    closePopup();
                    Swal.fire({
                        title: 'Supprimer l\'emploi du temps ?',
                        text: 'Tous les créneaux de cet enseignant seront effacés.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ba1a1a',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Oui, supprimer',
                        cancelButtonText: 'Annuler'
                    }).then(res => {
                        if (res.isConfirmed) {
                            fetch(`${baseUrl}/personnel/emploi-temps/teacher/${tid}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(r => r.json())
                            .then(resData => {
                                Swal.fire({ icon: 'success', title: 'Supprimé', text: resData.message, timer: 1800, showConfirmButton: false });
                                setTimeout(() => window.location.reload(), 1800);
                            });
                        }
                    });
                }
            });
        });
        
        document.body.appendChild(popup);
        const pos = calculatePopupPosition(btnEl, popup);
        popup.style.top = pos.top + 'px';
        popup.style.left = pos.left + 'px';
        setTimeout(() => popup.classList.add('visible'), 10);
        
        activePopup = popup;
        activeButton = btnEl;
    })
    .catch(() => { btnEl.disabled = false; });
};

document.addEventListener('click', () => closePopup());
window.addEventListener('scroll', () => closePopup(), true);

document.addEventListener('DOMContentLoaded', function() {
    // Recherche dynamique
    const searchInput = document.getElementById('searchTeacher');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            document.querySelectorAll('.teacher-row').forEach(row => {
                const name = row.dataset.name || '';
                const email = row.dataset.email || '';
                const mat = row.dataset.matricule || '';
                const sub = row.dataset.subject || '';
                row.style.display = (!query || name.includes(query) || email.includes(query) || mat.includes(query) || sub.includes(query)) ? '' : 'none';
            });
        });
    }

    // Suppression enseignant
    document.querySelectorAll('.delete-teacher-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: `L'enseignant "${this.dataset.name}" ainsi que ses affectations seront définitivement supprimés.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ba1a1a',
                cancelButtonColor: '#64748B',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then(r => {
                if (r.isConfirmed) form.submit();
            });
        });
    });

    @if(session('success')) Swal.fire({icon:'success',title:'Succès',text:@json(session('success')),timer:2500,showConfirmButton:false}); @endif
    @if(session('error')) Swal.fire({icon:'error',title:'Erreur',text:@json(session('error')),timer:3000,showConfirmButton:false}); @endif
});
</script>
@endpush
