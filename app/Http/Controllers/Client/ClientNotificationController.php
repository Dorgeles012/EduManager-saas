<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\NotificationRecipient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientNotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $recipients = NotificationRecipient::query()
            ->where('user_id', $request->user()->id)
            ->with('notification:id,titre,message,category,priority,sent_at')
            ->latest('id')
            ->limit(15)
            ->get();

        return response()->json([
            'unread_count' => $recipients->whereNull('read_at')->count(),
            'notifications' => $recipients->map(fn (NotificationRecipient $recipient) => [
                'id' => $recipient->id,
                'title' => $recipient->notification->titre,
                'message' => $recipient->notification->message,
                'category' => $recipient->notification->category,
                'priority' => $recipient->notification->priority,
                'date' => optional($recipient->notification->sent_at)->translatedFormat('d M Y, H:i'),
                'read' => $recipient->read_at !== null,
            ])->values(),
        ]);
    }

    public function markRead(Request $request, NotificationRecipient $notificationRecipient): JsonResponse
    {
        abort_unless($notificationRecipient->user_id === $request->user()->id, 404);

        $notificationRecipient->update(['read_at' => $notificationRecipient->read_at ?? now()]);

        return response()->json(['ok' => true]);
    }
}
