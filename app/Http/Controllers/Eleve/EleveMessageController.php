<?php

namespace App\Http\Controllers\Eleve;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EleveMessageController extends EleveController
{
    /**
     * Liste des messages de l'élève connecté (reçus + envoyés).
     */
    public function index(): View
    {
        $user = auth()->user();

        $messages = Message::query()
            ->where('tenant_id', $user->tenant_id)
            ->where(function ($q) use ($user) {
                $q->where('receiver_id', $user->id)
                    ->orWhere('sender_id', $user->id);
            })
            ->with('sender', 'receiver')
            ->latest()
            ->paginate(20);

        $unreadCount = Message::query()
            ->where('tenant_id', $user->tenant_id)
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->count();

        return view('eleve.messages', [
            'messages' => $messages,
            'unreadCount' => $unreadCount,
        ]);
    }

    /**
     * Marque un message comme lu.
     */
    public function markRead(Request $request, int $message): RedirectResponse
    {
        $user = $request->user();

        $msg = Message::query()
            ->where('tenant_id', $user->tenant_id)
            ->where('id', $message)
            ->where('receiver_id', $user->id)
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
        $user = auth()->user();

        $destinataires = User::query()
            ->where('tenant_id', $user->tenant_id)
            ->where('id', '!=', $user->id)
            ->whereIn('role', ['client', 'personnel', 'enseignant', 'parent'])
            ->orderBy('nom')
            ->get(['id', 'nom', 'prenom', 'role', 'email']);

        return view('eleve.messages-compose', [
            'destinataires' => $destinataires,
        ]);
    }

    /**
     * Envoie un message.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'receiver_id' => ['required', 'integer', 'exists:users,id'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $receiver = User::findOrFail($validated['receiver_id']);

        abort_unless($receiver->tenant_id === $user->tenant_id, 403);

        Message::create([
            'tenant_id' => $user->tenant_id,
            'sender_id' => $user->id,
            'receiver_id' => $validated['receiver_id'],
            'message' => $validated['message'],
            'is_read' => false,
        ]);

        return redirect()->route('eleve.messages')->with('success', 'Message envoyé avec succès.');
    }
}
