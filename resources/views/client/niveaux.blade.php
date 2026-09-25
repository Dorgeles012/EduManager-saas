@extends('client.layouts.app')
@section('title', 'EduManager - Niveaux')
@section('content')

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Gestion des Niveaux</h2>
        <p class="text-sm text-gray-500 mt-1">Liste complète des niveaux d'enseignement de votre réseau.</p>
    </div>
    <button id="addLevelBtn" class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-xs font-semibold transition shadow-sm">
        <span class="material-symbols-outlined text-sm">add</span>
        Ajouter un niveau
    </button>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                <span class="material-symbols-outlined text-lg">layers</span>
            </div>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Total</span>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold text-gray-900" id="totalLevelsCount">{{ $totalLevels ?? 0 }}</h3>
        <p class="text-xs text-gray-500 mt-0.5">Niveaux d'enseignement</p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
            <span class="material-symbols-outlined text-base">format_list_bulleted</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-900">Liste des niveaux</h3>
            <p class="text-[11px] text-gray-500">{{ count($levels ?? []) }} niveau(x) enregistré(s)</p>
        </div>
    </div>

    <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">N°</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Niveau</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Établissement</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Date de création</th>
                    <th class="px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100" id="levelsTableBody">
                @forelse($levels ?? [] as $level)
                <tr class="hover:bg-gray-50/50 transition-colors" data-level-id="{{ $level['id'] ?? '' }}">
                    <td class="px-4 py-3 text-xs text-gray-500 font-semibold">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <span class="text-xs w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 text-[11px] font-bol font-semibold text-gray-900 level-name">{{ $level['name'] }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-100 text-[10px] font-bold text-gray-700 level-school">
                            {{ $level['school'] ?? 'Non assigné' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-600">{{ $level['date'] ?? 'N/A' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-1.5">
                            <button type="button"
                                    class="edit-btn w-8 h-8 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-600 flex items-center justify-center transition"
                                    data-id="{{ $level['id'] ?? '' }}"
                                    data-name="{{ $level['name'] ?? '' }}"
                                    data-school-id="{{ $level['school_id'] ?? '' }}"
                                    title="Modifier">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </button>
                            <button type="button"
                                    class="delete-btn w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition"
                                    data-id="{{ $level['id'] ?? 0 }}"
                                    data-name="{{ $level['name'] ?? '' }}"
                                    title="Supprimer">
                                <span class="material-symbols-outlined text-base">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="emptyRow">
                    <td colspan="5" class="py-16 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center mb-3">
                                <span class="material-symbols-outlined text-2xl text-gray-300">layers</span>
                            </div>
                            <p class="text-sm font-semibold text-gray-700">Aucun niveau trouvé</p>
                            <p class="text-xs text-gray-400 mt-1">Commencez par ajouter votre premier niveau.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="md:hidden divide-y divide-gray-100" id="levelsMobileBody">
        @forelse($levels ?? [] as $level)
        <div class="p-4 space-y-3 mobile-level-row" data-level-id="{{ $level['id'] ?? '' }}">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-sm font-bold flex-shrink-0">
                        {{ strtoupper(substr($level['name'], 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate level-name">{{ $level['name'] }}</p>
                        <p class="text-[11px] text-gray-500 truncate">{{ $level['date'] ?? 'N/A' }}</p>
                    </div>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-100 text-[10px] font-bold text-gray-700 level-school flex-shrink-0">
                    {{ $level['school'] ?? 'N/A' }}
                </span>
            </div>
            <div class="flex items-center gap-2 pt-1">
                <button type="button"
                        class="edit-btn flex-1 h-9 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold flex items-center justify-center gap-1.5 transition"
                        data-id="{{ $level['id'] ?? '' }}"
                        data-name="{{ $level['name'] ?? '' }}"
                        data-school-id="{{ $level['school_id'] ?? '' }}">
                    <span class="material-symbols-outlined text-sm">edit</span>
                    Modifier
                </button>
                <button type="button"
                        class="delete-btn w-9 h-9 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition"
                        data-id="{{ $level['id'] ?? 0 }}"
                        data-name="{{ $level['name'] ?? '' }}">
                    <span class="material-symbols-outlined text-base">delete</span>
                </button>
            </div>
        </div>
        @empty
        <div class="py-16 text-center">
            <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-2xl text-gray-300">layers</span>
            </div>
            <p class="text-sm font-semibold text-gray-700">Aucun niveau trouvé</p>
            <p class="text-xs text-gray-400 mt-1">Commencez par ajouter votre premier niveau.</p>
        </div>
        @endforelse
    </div>

    <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between">
        <span class="text-[11px] text-gray-500" id="paginationInfo">
            Affichage de 1 à {{ count($levels ?? []) }} sur {{ count($levels ?? []) }} niveau(x)
        </span>
        <div class="flex items-center gap-1.5 text-xs">
            <button class="px-3 py-1 bg-indigo-600 text-white rounded-lg text-[11px] font-semibold">1</button>
        </div>
    </div>
</div>

<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="add-level-modal">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm modal-backdrop"></div>
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0 relative z-10" id="add-level-modal-content">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <span class="material-symbols-outlined text-base">add_circle</span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Nouveau niveau</h3>
                    <p class="text-[11px] text-gray-500">Ajouter un niveau d'enseignement</p>
                </div>
            </div>
            <button type="button" class="close-modal-btn w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center transition" data-modal="add-level-modal">
                <span class="material-symbols-outlined text-gray-500">close</span>
            </button>
        </div>

        <form id="addLevelForm" class="p-5 space-y-4">
            @csrf
            <div>
                <label for="levelName" class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Nom du niveau</label>
                <input type="text" id="levelName" name="nom" required placeholder="Ex: CP, 6ème, CM2 B"
                       class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
            </div>
            <div>
                <label for="levelSchool" class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Établissement</label>
                <select id="levelSchool" name="etablissement_id" required
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                    <option value="">Sélectionner un établissement</option>
                    @foreach($schools ?? [] as $school)
                    <option value="{{ $school['id'] }}">{{ $school['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                <button type="button" class="close-modal-btn px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition" data-modal="add-level-modal">Annuler</button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-xs font-semibold transition shadow-sm">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<div class="fixed inset-0 z-[100] hidden items-center justify-center p-4" id="edit-level-modal">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm modal-backdrop"></div>
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0 relative z-10" id="edit-level-modal-content">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                    <span class="material-symbols-outlined text-base">edit</span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Modifier le niveau</h3>
                    <p class="text-[11px] text-gray-500">Mise à jour des informations</p>
                </div>
            </div>
            <button type="button" class="close-modal-btn w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center transition" data-modal="edit-level-modal">
                <span class="material-symbols-outlined text-gray-500">close</span>
            </button>
        </div>

        <form id="editLevelForm" class="p-5 space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" id="editLevelId">
            <div>
                <label for="editLevelName" class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Nom du niveau</label>
                <input type="text" id="editLevelName" name="nom" required
                       class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition">
            </div>
            <div>
                <label for="editLevelSchool" class="block text-[10px] font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Établissement</label>
                <select id="editLevelSchool" name="etablissement_id" required
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2.5 px-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none transition">
                    <option value="">Sélectionner un établissement</option>
                    @foreach($schools ?? [] as $school)
                    <option value="{{ $school['id'] }}">{{ $school['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                <button type="button" class="close-modal-btn px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition" data-modal="edit-level-modal">Annuler</button>
                <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-5 py-2 rounded-lg text-xs font-semibold transition shadow-sm">Mettre à jour</button>
            </div>
        </form>
    </div>
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

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    const content = document.getElementById(modalId + '-content');
    if (!modal || !content) return;
    modal.classList.remove('hidden'); modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
    void modal.offsetWidth;
    content.classList.remove('scale-95', 'opacity-0');
    content.classList.add('scale-100', 'opacity-100');
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    const content = document.getElementById(modalId + '-content');
    if (!modal || !content) return;
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        modal.classList.remove('flex'); modal.classList.add('hidden');
        document.body.style.overflow = '';
    }, 300);
}

document.getElementById('addLevelBtn')?.addEventListener('click', e => { e.preventDefault(); openModal('add-level-modal'); });

document.querySelectorAll('.close-modal-btn').forEach(btn => {
    btn.addEventListener('click', e => {
        e.preventDefault();
        const id = btn.getAttribute('data-modal');
        if (id) closeModal(id);
    });
});

document.querySelectorAll('.modal-backdrop').forEach(bd => {
    bd.addEventListener('click', function() {
        const modal = this.closest('.fixed');
        if (modal) closeModal(modal.id);
    });
});

document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('editLevelId').value = this.dataset.id || '';
        document.getElementById('editLevelName').value = this.dataset.name || '';
        const select = document.getElementById('editLevelSchool');
        if (select && this.dataset.schoolId) select.value = this.dataset.schoolId;
        openModal('edit-level-modal');
    });
});

function bindDelete(btn) {
    btn.addEventListener('click', function(e) {
        e.preventDefault(); e.stopPropagation();
        const id = this.dataset.id;
        const levelName = this.dataset.name || 'ce niveau';
        if (!id) return;
        const row = this.closest('tr, .mobile-level-row');

        Swal.fire({
            ...swalConfig,
            title: 'Supprimer ce niveau ?',
            html: `<strong class="text-rose-600">"${levelName}"</strong> sera définitivement supprimé.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler',
            iconColor: '#e11d48'
        }).then(result => {
            if (!result.isConfirmed) return;
            fetch(`/client/niveaux/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    row?.remove();
                    const total = document.getElementById('totalLevelsCount');
                    if (total) total.textContent = Math.max(0, (parseInt(total.textContent) || 0) - 1);
                    updateRowNumbers();
                    checkEmpty();
                    updatePaginationInfo();
                    Swal.fire({ ...swalConfig, icon: 'success', title: 'Supprimé !', text: data.message || 'Niveau supprimé.', timer: 2000, showConfirmButton: false });
                } else {
                    Swal.fire({ ...swalConfig, icon: 'error', title: 'Erreur', text: data.message || 'Erreur lors de la suppression.', confirmButtonText: 'OK' });
                }
            })
            .catch(() => Swal.fire({ ...swalConfig, icon: 'error', title: 'Erreur', text: 'Erreur réseau.', confirmButtonText: 'OK' }));
        });
    });
}
document.querySelectorAll('.delete-btn').forEach(bindDelete);

document.getElementById('addLevelForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const levelName = document.getElementById('levelName')?.value?.trim();
    const levelSchool = document.getElementById('levelSchool')?.value;
    if (!levelName || !levelSchool) {
        Swal.fire({ ...swalConfig, icon: 'error', title: 'Champs requis', text: 'Veuillez remplir tous les champs.', confirmButtonText: 'OK' });
        return;
    }
    fetch('{{ route("client.niveaux.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: new FormData(this)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closeModal('add-level-modal');
            this.reset();
            const tbody = document.getElementById('levelsTableBody');
            document.getElementById('emptyRow')?.remove();

            const newRow = document.createElement('tr');
            newRow.className = 'hover:bg-gray-50/50 transition-colors';
            newRow.setAttribute('data-level-id', data.level.id);
            newRow.innerHTML = `
                <td class="px-4 py-3 text-xs text-gray-500 font-semibold">${tbody.children.length + 1}</td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 text-[11px] font-bold flex-shrink-0">
                            ${(data.level.name || '').substring(0, 2).toUpperCase()}
                        </div>
                        <span class="text-xs font-semibold text-gray-900 level-name">${data.level.name}</span>
                    </div>
                </td>
                <td class="px-4 py-3">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-100 text-[10px] font-bold text-gray-700 level-school">${data.level.school || 'Non assigné'}</span>
                </td>
                <td class="px-4 py-3 text-xs text-gray-600">${data.level.date || 'N/A'}</td>
                <td class="px-4 py-3">
                    <div class="flex items-center justify-end gap-1.5">
                        <button type="button" class="edit-btn w-8 h-8 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-600 flex items-center justify-center transition"
                                data-id="${data.level.id}" data-name="${data.level.name}" data-school-id="${data.level.school_id || ''}" title="Modifier">
                            <span class="material-symbols-outlined text-base">edit</span>
                        </button>
                        <button type="button" class="delete-btn w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition"
                                data-id="${data.level.id}" data-name="${data.level.name}" title="Supprimer">
                            <span class="material-symbols-outlined text-base">delete</span>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(newRow);

            // Bind events sur la nouvelle ligne
            newRow.querySelector('.edit-btn')?.addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('editLevelId').value = this.dataset.id || '';
                document.getElementById('editLevelName').value = this.dataset.name || '';
                const select = document.getElementById('editLevelSchool');
                if (select && this.dataset.schoolId) select.value = this.dataset.schoolId;
                openModal('edit-level-modal');
            });
            bindDelete(newRow.querySelector('.delete-btn'));

            // Ajouter dans la vue mobile aussi
            const mobileBody = document.getElementById('levelsMobileBody');
            if (mobileBody && !mobileBody.querySelector('.py-16')) {
                const mRow = document.createElement('div');
                mRow.className = 'p-4 space-y-3 mobile-level-row';
                mRow.setAttribute('data-level-id', data.level.id);
                mRow.innerHTML = `
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-sm font-bold flex-shrink-0">
                                ${(data.level.name || '').substring(0, 2).toUpperCase()}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate level-name">${data.level.name}</p>
                                <p class="text-[11px] text-gray-500 truncate">${data.level.date || 'N/A'}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-100 text-[10px] font-bold text-gray-700 level-school flex-shrink-0">${data.level.school || 'N/A'}</span>
                    </div>
                `;
                mobileBody.appendChild(mRow);
            }

            const total = document.getElementById('totalLevelsCount');
            if (total) total.textContent = (parseInt(total.textContent) || 0) + 1;
            updatePaginationInfo();
            Swal.fire({ ...swalConfig, icon: 'success', title: 'Ajouté !', text: data.message || 'Niveau ajouté.', timer: 2000, showConfirmButton: false });
        } else {
            Swal.fire({ ...swalConfig, icon: 'error', title: 'Erreur', text: data.message || 'Erreur lors de l\'enregistrement.', confirmButtonText: 'OK' });
        }
    })
    .catch(() => Swal.fire({ ...swalConfig, icon: 'error', title: 'Erreur', text: 'Erreur réseau.', confirmButtonText: 'OK' }));
});

document.getElementById('editLevelForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('editLevelId')?.value;
    const levelName = document.getElementById('editLevelName')?.value?.trim();
    const levelSchool = document.getElementById('editLevelSchool')?.value;
    if (!id || !levelName || !levelSchool) {
        Swal.fire({ ...swalConfig, icon: 'error', title: 'Champs requis', text: 'Veuillez remplir tous les champs.', confirmButtonText: 'OK' });
        return;
    }
    fetch(`/client/niveaux/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: new FormData(this)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closeModal('edit-level-modal');
            document.querySelectorAll(`[data-level-id="${id}"]`).forEach(el => {
                const n = el.querySelector('.level-name');
                const s = el.querySelector('.level-school');
                if (n) n.textContent = data.level.name;
                if (s) s.textContent = data.level.school || 'Non assigné';
            });
            Swal.fire({ ...swalConfig, icon: 'success', title: 'Mis à jour !', text: data.message || 'Niveau modifié.', timer: 2000, showConfirmButton: false });
        } else {
            Swal.fire({ ...swalConfig, icon: 'error', title: 'Erreur', text: data.message || 'Erreur lors de la mise à jour.', confirmButtonText: 'OK' });
        }
    })
    .catch(() => Swal.fire({ ...swalConfig, icon: 'error', title: 'Erreur', text: 'Erreur réseau.', confirmButtonText: 'OK' }));
});

function updateRowNumbers() {
    document.querySelectorAll('#levelsTableBody tr').forEach((row, i) => {
        const cell = row.querySelector('td:first-child');
        if (cell) cell.textContent = i + 1;
    });
}

function updatePaginationInfo() {
    const tbody = document.getElementById('levelsTableBody');
    const info = document.getElementById('paginationInfo');
    if (info && tbody) {
        const count = tbody.children.length;
        info.textContent = count > 0 ? `Affichage de 1 à ${count} sur ${count} niveau(x)` : 'Affichage de 0 sur 0 niveau(x)';
    }
}

function checkEmpty() {
    const tbody = document.getElementById('levelsTableBody');
    if (tbody && tbody.children.length === 0) {
        tbody.innerHTML = `
            <tr id="emptyRow">
                <td colspan="5" class="py-16 text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center mb-3">
                            <span class="material-symbols-outlined text-2xl text-gray-300">layers</span>
                        </div>
                        <p class="text-sm font-semibold text-gray-700">Aucun niveau trouvé</p>
                        <p class="text-xs text-gray-400 mt-1">Commencez par ajouter votre premier niveau.</p>
                    </div>
                </td>
            </tr>
        `;
    }
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') ['add-level-modal', 'edit-level-modal'].forEach(id => {
        const m = document.getElementById(id);
        if (m && m.classList.contains('flex')) closeModal(id);
    });
});
</script>
@endpush