<?php

namespace App\Http\Controllers\Sadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sadmin\StoreSystemNotificationRequest;
use App\Models\SystemNotification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SystemNotificationController extends Controller
{
    public function index(): View
    {
        $tenantId = auth()->user()->tenant_id;

        return view('sadmin.notifications', [
            'users' => User::query()->where('tenant_id', $tenantId)->orderBy('nom')->get(['id', 'nom', 'prenom', 'email', 'role']),
            'notifications' => $this->notifications($tenantId),
            'templates' => config('notification_templates'),
        ]);
    }

    public function store(StoreSystemNotificationRequest $request, NotificationService $notifications): RedirectResponse
    {
        $sender = $request->user();
        $data = $request->validated();
        $recipients = $this->recipients($sender->tenant_id, $data['audience'], $data['recipient_ids'] ?? []);

        if ($recipients->isEmpty()) {
            return back()->withInput()->withErrors(['audience' => 'Aucun destinataire actif ne correspond à cette audience.']);
        }

        $notifications->sendToUsers($sender, $recipients, $data['titre'], $data['message'], $data['category'] ?? 'system', $data['priority'], $data['audience']);

        return redirect()->route('sadmin.notifications')->with('success', 'Notification envoyée à '.$recipients->count().' destinataire(s).');
    }

    public function history(): View
    {
        return view('sadmin.historique', [
            'notifications' => $this->notifications(auth()->user()->tenant_id),
        ]);
    }

    public function destroy(SystemNotification $notification): \Illuminate\Http\JsonResponse
    {
        abort_unless($notification->tenant_id === auth()->user()->tenant_id, 404);
        $notification->delete();

        return response()->json(['ok' => true]);
    }

    private function notifications(int $tenantId)
    {
        return SystemNotification::query()
            ->where('tenant_id', $tenantId)
            ->withCount([
                'recipients as recipients_count',
                'recipients as read_count' => fn ($query) => $query->whereNotNull('read_at'),
            ])
            ->latest('sent_at')
            ->paginate(15);
    }

    private function recipients(int $tenantId, string $audience, array $recipientIds)
    {
        $query = User::query()->where('tenant_id', $tenantId);

        return match ($audience) {
            'clients' => $query->whereRaw('LOWER(role) = ?', ['client'])->get(),
            'parents' => $query->whereRaw('LOWER(role) = ?', ['parent'])->get(),
            'personnel' => $query->whereRaw('LOWER(role) = ?', ['personnel'])->get(),
            'enseignants' => $query->whereRaw('LOWER(role) = ?', ['enseignant'])->get(),
            'users' => $query->whereIn('id', $recipientIds)->get(),
            default => $query->get(),
        };
    }
}
