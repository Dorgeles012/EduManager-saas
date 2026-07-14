<?php

namespace App\Http\Controllers\Sadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sadmin\StoreSystemNotificationRequest;
use App\Models\SystemNotification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SystemNotificationController extends Controller
{
    public function index(): View
    {
        $tenantId = auth()->user()->tenant_id;

        return view('sadmin.notifications', [
            'users' => User::query()->where('tenant_id', $tenantId)->orderBy('nom')->get(['id', 'nom', 'prenom', 'email', 'role']),
            'notifications' => $this->notifications($tenantId),
        ]);
    }

    public function store(StoreSystemNotificationRequest $request): RedirectResponse
    {
        $sender = $request->user();
        $data = $request->validated();
        $recipients = $this->recipients($sender->tenant_id, $data['audience'], $data['recipient_ids'] ?? []);

        if ($recipients->isEmpty()) {
            return back()->withInput()->withErrors(['audience' => 'Aucun destinataire actif ne correspond à cette audience.']);
        }

        DB::transaction(function () use ($sender, $data, $recipients): void {
            $notification = SystemNotification::create([
                'tenant_id' => $sender->tenant_id,
                'sender_id' => $sender->id,
                'titre' => $data['titre'],
                'message' => $data['message'],
                'audience' => $data['audience'],
                'category' => $data['category'] ?? 'Système',
                'priority' => $data['priority'],
                'statut' => 'unread',
                'sent_at' => now(),
            ]);

            $notification->recipients()->createMany($recipients->map(fn (User $user) => ['user_id' => $user->id])->all());
        });

        return redirect()->route('sadmin.notifications')->with('success', 'Notification envoyée à '.$recipients->count().' destinataire(s).');
    }

    public function history(): View
    {
        return view('sadmin.historique', [
            'notifications' => $this->notifications(auth()->user()->tenant_id),
        ]);
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
