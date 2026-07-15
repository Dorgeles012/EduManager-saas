@extends('sadmin.layouts.app')

@section('content')
<div class="max-w-max-width mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div class="lg:col-span-12">
            <h2 class="font-headline-lg text-headline-lg text-primary">Historique des notifications</h2>
            <p class="font-body-md text-body-md text-text-muted mt-1">Suivi des envois et de leur lecture.</p>
        </div>
        <a class="px-4 py-2 rounded-lg bg-primary text-white text-sm" href="{{ route('sadmin.notifications') }}">Nouvelle notification</a>
    </div>
    <div class="bg-surface-container-lowest rounded-xl card-shadow overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-surface-bright text-xs text-text-muted">
                <tr>
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3">Sujet</th>
                    <th class="px-4 py-3">Audience</th>
                    <th class="px-4 py-3">Destinataires</th>
                    <th class="px-4 py-3">Statut</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-container">
                @forelse($notifications as $notification)
                <tr>
                    <td class="px-4 py-3 text-sm">{{ $notification->sent_at?->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3">
                        <p class="font-medium">{{ $notification->titre }}</p>
                        <p class="text-xs text-text-muted">{{ $notification->category }}</p>
                    </td>
                    <td class="px-4 py-3 text-sm">{{ $notification->audience }}</td>
                    <td class="px-4 py-3 text-sm">{{ $notification->recipients_count }}</td>
                    <td class="px-4 py-3 text-sm">
                        <span class="text-success-green">Envoyée</span>
                        <br>
                        <span class="text-text-muted">{{ $notification->read_count }} lue(s) · {{ $notification->recipients_count - $notification->read_count }} non lue(s)</span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <button type="button" class="history-delete rounded-full p-2 text-alert-red hover:bg-alert-red/10" data-url="{{ route('sadmin.notifications.destroy', $notification) }}" title="Supprimer">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-10 text-center text-text-muted">Aucune notification envoyée.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $notifications->links() }}</div>
</div>
@endsection

@section('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('.history-delete').forEach(button => {
    button.addEventListener('click', function() {
        const deleteUrl = this.dataset.url;
        const row = this.closest('tr');
        
        Swal.fire({
            title: 'Supprimer la notification ?',
            text: 'Cette action est irréversible.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-xl',
                confirmButton: 'rounded-lg px-4 py-2 text-sm font-medium',
                cancelButton: 'rounded-lg px-4 py-2 text-sm font-medium'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.message || 'Erreur serveur');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    // Animation de suppression de la ligne
                    if (row) {
                        row.classList.add('removing');
                        setTimeout(() => {
                            row.remove();
                            
                            // Vérifier s'il reste des lignes
                            const tbody = document.querySelector('tbody');
                            const visibleRows = tbody.querySelectorAll('tr:not(.empty-message)');
                            
                            if (visibleRows.length === 0) {
                                const emptyRow = document.createElement('tr');
                                emptyRow.className = 'empty-message';
                                emptyRow.innerHTML = `
                                    <td colspan="6" class="px-4 py-10 text-center text-text-muted">
                                        Aucune notification envoyée.
                                    </td>
                                `;
                                tbody.appendChild(emptyRow);
                            }
                        }, 300);
                    }
                    
                    // Message de succès
                    Swal.fire({
                        title: 'Supprimée !',
                        text: data.message || 'La notification a été supprimée avec succès.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'rounded-xl'
                        }
                    });
                })
                .catch(error => {
                    console.error('Erreur de suppression:', error);
                    
                    Swal.fire({
                        title: 'Erreur !',
                        text: error.message || 'Impossible de supprimer cette notification.',
                        icon: 'error',
                        confirmButtonColor: '#dc2626',
                        customClass: {
                            popup: 'rounded-xl',
                            confirmButton: 'rounded-lg px-4 py-2 text-sm font-medium'
                        }
                    });
                });
            }
        });
    });
});
</script>
@endsection

<style>
.history-delete {
    transition: background-color 0.2s ease, transform 0.2s ease;
}

.history-delete:hover {
    transform: scale(1.05);
    background-color: rgba(220, 38, 38, 0.15) !important;
}

.history-delete:active {
    transform: scale(0.95);
}

@media (max-width: 640px) {
    .history-delete {
        padding: 0.6rem !important;
    }
    .history-delete i {
        font-size: 1rem;
    }
}

tbody tr {
    transition: opacity 0.3s ease, transform 0.3s ease, background-color 0.3s ease;
}

tbody tr.removing {
    opacity: 0;
    transform: scale(0.95);
    background-color: #fee2e2;
}

tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

.history-delete i {
    font-size: 1rem;
    transition: none !important;
}
</style>