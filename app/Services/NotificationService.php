<?php

namespace App\Services;

use App\Models\SystemNotification;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/** Creates persisted, per-user in-app notifications. */
class NotificationService
{
    /** @param Collection<int, User>|array<int, User> $recipients */
    public function sendToUsers(?User $sender, $recipients, string $title, string $message, string $category = 'system', string $priority = 'normal', ?string $audience = 'users'): ?SystemNotification
    {
        $recipients = collect($recipients)->unique('id')->values();

        if ($recipients->isEmpty()) {
            return null;
        }

        return DB::transaction(function () use ($sender, $recipients, $title, $message, $category, $priority, $audience) {
            $notification = SystemNotification::create([
                'tenant_id' => $sender?->tenant_id ?? $recipients->first()?->tenant_id,
                'sender_id' => $sender?->id,
                'titre' => $title,
                'message' => $message,
                'audience' => $audience,
                'category' => $category,
                'priority' => $priority,
                'statut' => 'unread',
                'sent_at' => now(),
            ]);

            $notification->recipients()->createMany(
                $recipients->map(fn (User $user) => ['user_id' => $user->id])->all()
            );

            return $notification;
        });
    }

    /** @param array<int, int> $recipientIds */
    public function sendToAudience(User $sender, string $audience, string $title, string $message, string $category = 'system', string $priority = 'normal', array $recipientIds = []): ?SystemNotification
    {
        $query = User::query()->where('tenant_id', $sender->tenant_id);
        $recipients = match ($audience) {
            'clients' => $query->whereRaw('LOWER(role) = ?', ['client'])->get(),
            'parents' => $query->whereRaw('LOWER(role) = ?', ['parent'])->get(),
            'personnel' => $query->whereRaw('LOWER(role) = ?', ['personnel'])->get(),
            'enseignants' => $query->whereRaw('LOWER(role) = ?', ['enseignant'])->get(),
            'users' => $query->whereIn('id', $recipientIds)->get(),
            default => $query->get(),
        };

        return $this->sendToUsers($sender, $recipients, $title, $message, $category, $priority, $audience);
    }
}
