<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParentMessageController extends Controller
{
    /**
     * Liste des messages du parent connecté (reçus + envoyés).
     */
    public function index(): View
    {
        $parent = auth()->user();

        $messages = Message::query()
            ->where('tenant_id', $parent->tenant_id)
            ->where(function ($q) use ($parent) {
                $q->where('receiver_id', $parent->id)
                    ->orWhere('sender_id', $parent->id);
            })
            ->with('sender', 'receiver')
            ->latest()
            ->paginate(20);

        $unreadCount = Message::query()
            ->where('tenant_id', $parent->tenant_id)
            ->where('receiver_id', $parent->id)
            ->where('is_read', false)
            ->count();

        return view('parent.messages', [
            'messages' => $messages,
            'unreadCount' => $unreadCount,
        ]);
    }

    /**
     * Marque un message comme lu.
     */
    public function markRead(Request $request, int $message): RedirectResponse
    {
        $parent = $request->user();

        $msg = Message::query()
            ->where('tenant_id', $parent->tenant_id)
            ->where('id', $message)
            ->where('receiver_id', $parent->id)
            ->firstOrFail();

        if (! $msg->is_read) {
            $msg->update(['is_read' => true]);
        }

        return back()->with('success', 'Message marqué comme lu.');
    }

    /**
     * Nouveau message (affichage du formulaire).
     */
    public function create(): View
    {
        $parent = auth()->user();

        $destinataires = User::query()
            ->where('tenant_id', $parent->tenant_id)
            ->where('id', '!=', $parent->id)
            ->whereIn('role', ['client', 'personnel', 'enseignant'])
            ->orderBy('nom')
            ->get(['id', 'nom', 'prenom', 'role', 'email']);

        return view('parent.messages-compose', [
            'destinataires' => $destinataires,
        ]);
    }

    /**
     * Envoie un message.
     */
    public function store(Request $request): RedirectResponse
    {
        $parent = $request->user();

        $validated = $request->validate([
            'receiver_id' => ['required', 'integer', 'exists:users,id'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $receiver = User::findOrFail($validated['receiver_id']);

        abort_unless($receiver->tenant_id === $parent->tenant_id, 403);

        Message::create([
            'tenant_id' => $parent->tenant_id,
            'sender_id' => $parent->id,
            'receiver_id' => $validated['receiver_id'],
            'message' => $validated['message'],
            'is_read' => false,
        ]);

        return redirect()->route('parent.messages')->with('success', 'Message envoyé avec succès.');
    }
}

