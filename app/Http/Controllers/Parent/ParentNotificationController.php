<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\NotificationRecipient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParentNotificationController extends Controller
{
    /**
     * Liste des notifications du parent connecté.
     */
    public function index(Request $request): View
    {
        $parent = auth()->user();

        $notifications = NotificationRecipient::query()
            ->where('user_id', $parent->id)
            ->with('notification.sender')
            ->whereHas('notification')
            ->latest()
            ->paginate(15);

        $unreadCount = NotificationRecipient::query()
            ->where('user_id', $parent->id)
            ->whereNull('read_at')
            ->count();

        return view('parent.notifications', [
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
