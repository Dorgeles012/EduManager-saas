<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Services\CommunicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParentMessageController extends Controller
{
    public function __construct(
        protected CommunicationService $communicationService
    ) {}

    public function index(): View
    {
        $user = auth()->user();
        $conversations = $this->communicationService->getUserConversations($user);
        $authorizedContacts = $this->communicationService->getAuthorizedContacts($user);
        $unreadTotal = $this->communicationService->getUnreadCount($user);

        return view('parent.messages.index', [
            'conversations' => $conversations,
            'groups' => $authorizedContacts['groups'],
            'contacts' => $authorizedContacts['contacts'],
            'unreadTotal' => $unreadTotal,
        ]);
    }

    public function getConversations(): JsonResponse
    {
        $user = auth()->user();
        $conversations = $this->communicationService->getUserConversations($user);

        return response()->json([
            'conversations' => $conversations,
            'unread_total' => $this->communicationService->getUnreadCount($user),
        ]);
    }

    public function getMessages(int $conversationId, Request $request): JsonResponse
    {
        $user = auth()->user();
        $afterId = $request->integer('after_id') ?: null;
        $data = $this->communicationService->getConversationMessages($user, $conversationId, $afterId);

        return response()->json($data);
    }

    public function startConversation(Request $request): JsonResponse
    {
        $user = auth()->user();
        $request->validate([
            'recipient_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $conversation = $this->communicationService->getOrCreateDirectConversation(
            $user,
            $request->integer('recipient_id')
        );

        return response()->json([
            'success' => true,
            'conversation_id' => $conversation->id,
        ]);
    }

    public function sendMessage(int $conversationId, Request $request): JsonResponse
    {
        $user = auth()->user();

        $request->validate([
            'content' => ['nullable', 'string', 'max:5000'],
            'type' => ['nullable', 'string', 'in:text,image,audio,video,file'],
            'file' => ['nullable', 'file', 'max:51200'],
            'duration' => ['nullable', 'integer'],
        ]);

        $communication = $this->communicationService->sendMessage(
            $user,
            $conversationId,
            $request->only(['content', 'type', 'duration']),
            $request->file('file')
        );

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $communication->id,
                'sender_id' => $communication->sender_id,
                'is_me' => true,
                'sender_name' => $user->name,
                'sender_role' => ucfirst((string) $user->role),
                'type' => $communication->type,
                'content' => $communication->content,
                'file_url' => $communication->file_url,
                'file_name' => $communication->file_name,
                'mime_type' => $communication->mime_type,
                'duration' => $communication->duration,
                'formatted_duration' => $communication->formatted_duration,
                'created_at' => $communication->created_at->format('H:i'),
            ],
        ]);
    }
}
