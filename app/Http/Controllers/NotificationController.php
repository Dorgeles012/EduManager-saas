<?php

namespace App\Http\Controllers;

use App\Models\NotificationRecipient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $recipients = $this->recipients($request)->limit(15)->get();

        return response()->json([
            'unread_count' => NotificationRecipient::query()->where('user_id', $request->user()->id)->whereNull('read_at')->count(),
            'notifications' => $recipients->map(fn (NotificationRecipient $recipient) => $this->payload($recipient))->values(),
        ]);
    }

    public function show(Request $request, NotificationRecipient $notificationRecipient): View
    {
        $recipient = $this->owned($request, $notificationRecipient);
        $recipient->update(['read_at' => $recipient->read_at ?? now()]);
        $recipient->load('notification.sender');

        return view('notifications.show', compact('recipient'));
    }

    public function markRead(Request $request, NotificationRecipient $notificationRecipient): JsonResponse
    {
        $recipient = $this->owned($request, $notificationRecipient);
        $recipient->update(['read_at' => $recipient->read_at ?? now()]);

        return response()->json(['ok' => true, 'unread_count' => NotificationRecipient::query()->where('user_id', $request->user()->id)->whereNull('read_at')->count()]);
    }

    public function destroy(Request $request, NotificationRecipient $notificationRecipient): JsonResponse
    {
        $this->owned($request, $notificationRecipient)->delete();

        return response()->json([
            'ok' => true,
            'unread_count' => NotificationRecipient::query()->where('user_id', $request->user()->id)->whereNull('read_at')->count(),
        ]);
    }

    private function recipients(Request $request)
    {
        return NotificationRecipient::query()
            ->where('user_id', $request->user()->id)
            ->with('notification:id,sender_id,titre,message,category,priority,sent_at')
            ->whereHas('notification')
            ->join('notifications', 'notification_recipients.notification_id', '=', 'notifications.id')
            ->select('notification_recipients.*')
            ->orderByDesc('notifications.sent_at');
    }

    private function owned(Request $request, NotificationRecipient $recipient): NotificationRecipient
    {
        abort_unless($recipient->user_id === $request->user()->id, 404);
        return $recipient;
    }

    private function payload(NotificationRecipient $recipient): array
    {
        $template = collect(config('notification_templates'))->firstWhere('category', $recipient->notification->category);

        return [
            'id' => $recipient->id,
            'title' => $recipient->notification->titre,
            'preview' => (string) str($recipient->notification->message)->squish()->limit(60),
            'category' => $recipient->notification->category,
            'icon' => $template['icon'] ?? 'fa-bell',
            'priority' => $recipient->notification->priority,
            'date' => optional($recipient->notification->sent_at)->translatedFormat('d M Y, H:i'),
            'read' => $recipient->read_at !== null,
            'url' => route('notifications.show', $recipient),
        ];
    }
}
