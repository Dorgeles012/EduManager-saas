<div class="relative">
    <button id="notification-button" type="button" class="relative rounded-lg p-2 text-slate-600 hover:bg-slate-100" aria-label="Notifications">
        <i class="fa-solid fa-bell"></i><span id="notification-badge" class="hidden absolute -right-1 -top-1 min-w-4 rounded-full bg-red-600 px-1 text-[10px] leading-4 text-white"></span>
    </button>
    <div id="notification-dropdown" class="hidden absolute right-0 top-11 z-50 w-96 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">
        <div class="flex justify-between border-b px-4 py-3"><span class="font-semibold">Notifications</span><span id="notification-count" class="text-xs text-slate-500"></span></div>
        <div id="notification-list" class="max-h-96 overflow-y-auto"></div>
    </div>
</div>

@once
    <script>
        (() => {
            const button = document.getElementById('notification-button'), dropdown = document.getElementById('notification-dropdown'), list = document.getElementById('notification-list'), badge = document.getElementById('notification-badge'), count = document.getElementById('notification-count');
            const escapeHtml = value => String(value ?? '').replace(/[&<>'"]/g, char => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' }[char]));
            const render = payload => { const unread = payload.unread_count || 0; badge.textContent = unread > 99 ? '99+' : unread; badge.classList.toggle('hidden', unread === 0); count.textContent = unread ? `${unread} non lue${unread > 1 ? 's' : ''}` : 'À jour'; list.innerHTML = payload.notifications.length ? payload.notifications.map(item => `<div class="flex border-b hover:bg-slate-50 ${item.read ? '' : 'bg-indigo-50 font-semibold'}"><a class="min-w-0 flex-1 px-4 py-3" href="${escapeHtml(item.url)}"><span class="block truncate text-sm"><i class="fa-solid fa-bell mr-2 text-indigo-600"></i>${escapeHtml(item.title)}</span><span class="mt-1 block truncate text-xs font-normal text-slate-500">${escapeHtml(item.preview)}</span><span class="mt-1 block text-[11px] font-normal text-slate-400">${escapeHtml(item.date)}${item.read ? '' : ' · Non lu'}</span></a><button type="button" class="notification-delete px-3 text-slate-400 hover:text-red-600" data-id="${item.id}" title="Supprimer"><i class="fa-solid fa-trash"></i></button></div>`).join('') : '<p class="px-4 py-6 text-center text-sm text-slate-500">Aucune notification.</p>'; };
            const load = () => fetch(@json(route('notifications.index')), { headers: { Accept: 'application/json' }, credentials: 'same-origin' }).then(r => r.ok ? r.json() : null).then(p => p && render(p)).catch(() => {});
            button?.addEventListener('click', () => { dropdown.classList.toggle('hidden'); load(); }); list?.addEventListener('click', event => { const target = event.target.closest('.notification-delete'); if (!target) return; Swal.fire({ title: 'Supprimer cette notification ?', text: 'Cette action est irréversible.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Oui, supprimer', cancelButtonText: 'Annuler', confirmButtonColor: '#dc2626' }).then(result => { if (!result.isConfirmed) return; fetch(`/notifications/${target.dataset.id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, Accept: 'application/json' }, credentials: 'same-origin' }).then(r => { if (!r.ok) throw new Error(); return r.json(); }).then(() => { Swal.fire({ icon: 'success', title: 'Notification supprimée avec succès.', timer: 1800, showConfirmButton: false }); load(); }).catch(() => Swal.fire({ icon: 'error', title: 'Une erreur est survenue lors de la suppression de la notification.' })); }); }); document.addEventListener('click', event => { if (!dropdown?.contains(event.target) && !button?.contains(event.target)) dropdown?.classList.add('hidden'); }); load(); window.setInterval(load, 20000);
        })();
    </script>
@endonce
