<?php

namespace App\Http\Controllers\Eleve;

use App\Models\NotificationRecipient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EleveNotificationController extends EleveController
{
    /**
     * Liste des notifications de l'élève connecté.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        $notifications = NotificationRecipient::query()
            ->where('user_id', $user->id)
            ->with('notification.sender')
            ->whereHas('notification')
            ->latest()
            ->paginate(15);

        $unreadCount = NotificationRecipient::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();

        return view('eleve.notifications', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    /**
     * Marque une notification comme lue.
     */
    public function markRead(Request $request, NotificationRecipient $notificationRecipient): RedirectResponse
    {
        abort_unless($notificationRecipient->user_id === $request->user()->id, 404);

        if (! $notificationRecipient->read_at) {
            $notificationRecipient->update(['read_at' => now()]);
        }

        return back()->with('success', 'Notification marquée comme lue.');
    }

    /**
     * Marque toutes les notifications comme lues.
     */
    public function markAllRead(Request $request): RedirectResponse
    {
        NotificationRecipient::query()
            ->where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }

    /**
     * Supprime une notification.
     */
    public function destroy(Request $request, NotificationRecipient $notificationRecipient): RedirectResponse
    {
        abort_unless($notificationRecipient->user_id === $request->user()->id, 404);

        $notificationRecipient->delete();

        return back()->with('success', 'Notification supprimée.');
    }
}
