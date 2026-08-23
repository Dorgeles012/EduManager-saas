<?php

namespace App\Http\Controllers\Eleve;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CommunicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EleveMessageController extends Controller
{
    public function __construct(
        protected CommunicationService $communicationService
    ) {}

    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $conversations = $this->communicationService->getUserConversations($user);
        $authorizedContacts = $this->communicationService->getAuthorizedContacts($user);
        $unreadTotal = $this->communicationService->getUnreadCount($user);

        return view('eleve.messages.index', [
            'conversations' => $conversations,
            'groups' => $authorizedContacts['groups'],
            'contacts' => $authorizedContacts['contacts'],
            'unreadTotal' => $unreadTotal,
        ]);
    }

    public function getConversations(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $conversations = $this->communicationService->getUserConversations($user);

        return response()->json([
            'conversations' => $conversations,
            'unread_total' => $this->communicationService->getUnreadCount($user),
        ]);
    }

    public function getMessages(int $conversationId, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $afterId = $request->integer('after_id') ?: null;
        $data = $this->communicationService->getConversationMessages($user, $conversationId, $afterId);

        return response()->json($data);
    }

    public function startConversation(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
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
        /** @var User $user */
        $user = Auth::user();

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
                'formatted_duration' => $communication->formatted_duration ?: ($communication->duration ? sprintf('%02d:%02d', floor($communication->duration / 60), $communication->duration % 60) : null),
                'status' => $communication->status ?? 'sent',
                'created_at' => $communication->created_at->format('H:i'),
            ],
        ]);
    }
}
