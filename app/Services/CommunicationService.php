<?php

namespace App\Services;

use App\Models\{Classe, Communication, Conversation, ConversationParticipant, Eleve, Enseignant, User};
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CommunicationService
{
    /**
     * Récupère la liste des contacts autorisés pour un utilisateur donné
     * selon les règles de permission strictes du système.
     */
    public function getAuthorizedContacts(User $user): array
    {
        $role = strtolower(trim((string) $user->role));
        $tenantId = $user->tenant_id;

        $groups = [];
        $contacts = [];

        switch ($role) {
            case 'eleve':
                $eleve = Eleve::where('tenant_id', $tenantId)
                    ->where(function ($q) use ($user) {
                        $q->where('id', $user->eleve_id)
                            ->orWhereHas('user', fn ($u) => $u->where('id', $user->id));
                    })
                    ->first();

                if ($eleve && $eleve->classe_id) {
                    $classe = Classe::with(['niveau', 'enseignants.user_id' => function ($q) {
                        // Eager load
                    }])->where('tenant_id', $tenantId)->find($eleve->classe_id);

                    if ($classe) {
                        // Groupe de classe de l'élève
                        $classGroup = $this->getOrCreateClassGroup($classe->id, $tenantId);
                        $groups[] = [
                            'conversation_id' => $classGroup->id,
                            'class_id' => $classe->id,
                            'name' => 'Classe ' . $classe->nom,
                            'level' => $classe->niveau?->nom,
                            'type' => 'class_group',
                        ];

                        // Enseignants affiliés à la classe de l'élève
                        $teacherUserIds = $classe->enseignants()
                            ->whereNotNull('user_id')
                            ->pluck('user_id')
                            ->unique()
                            ->all();

                        if (!empty($teacherUserIds)) {
                            $teachers = User::where('tenant_id', $tenantId)
                                ->whereIn('id', $teacherUserIds)
                                ->where('id', '!=', $user->id)
                                ->get();

                            foreach ($teachers as $teacher) {
                                $contacts[] = [
                                    'user_id' => $teacher->id,
                                    'name' => $teacher->name ?? ($teacher->nom . ' ' . $teacher->prenom),
                                    'email' => $teacher->email,
                                    'role' => 'Enseignant',
                                    'avatar' => $teacher->image ? asset('storage/' . $teacher->image) : null,
                                ];
                            }
                        }
                    }
                }
                break;

            case 'enseignant':
                $enseignant = Enseignant::with('classes')
                    ->where('tenant_id', $tenantId)
                    ->where(function ($q) use ($user) {
                        $q->where('user_id', $user->id)->orWhere('email', $user->email);
                    })
                    ->first();

                if ($enseignant) {
                    $classeIds = $enseignant->classes->pluck('id')->all();

                    // Groupes des classes de l'enseignant
                    foreach ($enseignant->classes as $classe) {
                        $classGroup = $this->getOrCreateClassGroup($classe->id, $tenantId);
                        $groups[] = [
                            'conversation_id' => $classGroup->id,
                            'class_id' => $classe->id,
                            'name' => 'Classe ' . $classe->nom,
                            'type' => 'class_group',
                        ];
                    }

                    // Élèves de ses classes (comptes utilisateurs)
                    if (!empty($classeIds)) {
                        $studentUserIds = User::where('tenant_id', $tenantId)
                            ->whereRaw('LOWER(role) = ?', ['eleve'])
                            ->whereHas('eleve', fn ($q) => $q->whereIn('classe_id', $classeIds))
                            ->pluck('id')
                            ->all();

                        $students = User::with('eleve.classe')
                            ->whereIn('id', $studentUserIds)
                            ->orderBy('nom')
                            ->get();

                        foreach ($students as $student) {
                            $contacts[] = [
                                'user_id' => $student->id,
                                'name' => $student->name ?? ($student->nom . ' ' . $student->prenom),
                                'email' => $student->email,
                                'role' => 'Élève (' . ($student->eleve?->classe?->nom ?? 'N/A') . ')',
                                'avatar' => $student->image ? asset('storage/' . $student->image) : null,
                            ];
                        }
                    }
                }

                // Personnel de l'établissement
                $personnels = User::where('tenant_id', $tenantId)
                    ->whereRaw('LOWER(role) = ?', ['personnel'])
                    ->where('id', '!=', $user->id)
                    ->orderBy('nom')
                    ->get();

                foreach ($personnels as $personnel) {
                    $contacts[] = [
                        'user_id' => $personnel->id,
                        'name' => $personnel->name ?? ($personnel->nom . ' ' . $personnel->prenom),
                        'email' => $personnel->email,
                        'role' => 'Personnel',
                        'avatar' => $personnel->image ? asset('storage/' . $personnel->image) : null,
                    ];
                }
                break;

            case 'parent':
                $eleves = Eleve::where('tenant_id', $tenantId)
                    ->where('parent_id', $user->id)
                    ->get();

                $classeIds = $eleves->pluck('classe_id')->filter()->unique()->all();

                // Enseignants des classes de ses enfants
                if (!empty($classeIds)) {
                    $teacherUserIds = DB::table('classe_enseignant')
                        ->join('enseignants', 'classe_enseignant.enseignant_id', '=', 'enseignants.id')
                        ->whereIn('classe_enseignant.classe_id', $classeIds)
                        ->whereNotNull('enseignants.user_id')
                        ->pluck('enseignants.user_id')
                        ->unique()
                        ->all();

                    $teachers = User::where('tenant_id', $tenantId)
                        ->whereIn('id', $teacherUserIds)
                        ->orderBy('nom')
                        ->get();

                    foreach ($teachers as $teacher) {
                        $contacts[] = [
                            'user_id' => $teacher->id,
                            'name' => $teacher->name ?? ($teacher->nom . ' ' . $teacher->prenom),
                            'email' => $teacher->email,
                            'role' => 'Enseignant',
                            'avatar' => $teacher->image ? asset('storage/' . $teacher->image) : null,
                        ];
                    }
                }

                // Personnel
                $personnels = User::where('tenant_id', $tenantId)
                    ->whereRaw('LOWER(role) = ?', ['personnel'])
                    ->orderBy('nom')
                    ->get();

                foreach ($personnels as $personnel) {
                    $contacts[] = [
                        'user_id' => $personnel->id,
                        'name' => $personnel->name ?? ($personnel->nom . ' ' . $personnel->prenom),
                        'email' => $personnel->email,
                        'role' => 'Personnel',
                        'avatar' => $personnel->image ? asset('storage/' . $personnel->image) : null,
                    ];
                }
                break;

            case 'personnel':
                // Parents
                $parents = User::where('tenant_id', $tenantId)
                    ->whereRaw('LOWER(role) = ?', ['parent'])
                    ->orderBy('nom')
                    ->get();

                foreach ($parents as $parent) {
                    $contacts[] = [
                        'user_id' => $parent->id,
                        'name' => $parent->name ?? ($parent->nom . ' ' . $parent->prenom),
                        'email' => $parent->email,
                        'role' => 'Parent',
                        'avatar' => $parent->image ? asset('storage/' . $parent->image) : null,
                    ];
                }

                // Enseignants
                $teachers = User::where('tenant_id', $tenantId)
                    ->whereRaw('LOWER(role) = ?', ['enseignant'])
                    ->orderBy('nom')
                    ->get();

                foreach ($teachers as $teacher) {
                    $contacts[] = [
                        'user_id' => $teacher->id,
                        'name' => $teacher->name ?? ($teacher->nom . ' ' . $teacher->prenom),
                        'email' => $teacher->email,
                        'role' => 'Enseignant',
                        'avatar' => $teacher->image ? asset('storage/' . $teacher->image) : null,
                    ];
                }
                break;
        }

        return [
            'groups' => $groups,
            'contacts' => $contacts,
        ];
    }

    /**
     * Vérifie si un expéditeur a le droit d'écrire en privé à un destinataire donné.
     */
    public function canCommunicate(User $sender, User $recipient): bool
    {
        if ($sender->tenant_id !== $recipient->tenant_id) {
            return false;
        }

        if ($sender->id === $recipient->id) {
            return false;
        }

        $contactsData = $this->getAuthorizedContacts($sender);
        $allowedUserIds = array_column($contactsData['contacts'], 'user_id');

        return in_array($recipient->id, $allowedUserIds, true);
    }

    /**
     * Vérifie si un utilisateur a accès à un groupe de classe.
     */
    public function canAccessClassGroup(User $user, int $classId): bool
    {
        $role = strtolower(trim((string) $user->role));
        $tenantId = $user->tenant_id;

        if ($role === 'eleve') {
            $eleve = Eleve::where('tenant_id', $tenantId)
                ->where(function ($q) use ($user) {
                    $q->where('id', $user->eleve_id)
                        ->orWhereHas('user', fn ($u) => $u->where('id', $user->id));
                })
                ->first();

            return $eleve && (int) $eleve->classe_id === $classId;
        }

        if ($role === 'enseignant') {
            $enseignant = Enseignant::where('tenant_id', $tenantId)
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)->orWhere('email', $user->email);
                })
                ->first();

            return $enseignant && $enseignant->classes()->where('classes.id', $classId)->exists();
        }

        return false;
    }

    /**
     * Crée ou récupère une conversation directe entre deux utilisateurs autorisés.
     */
    public function getOrCreateDirectConversation(User $user, int $recipientId): Conversation
    {
        $recipient = User::findOrFail($recipientId);

        if (! $this->canCommunicate($user, $recipient)) {
            throw ValidationException::withMessages([
                'recipient' => 'Vous n\'êtes pas autorisé à communiquer avec cet utilisateur.',
            ]);
        }

        // Trouver si une conversation directe existe déjà entre ces 2 personnes
        $conversation = Conversation::where('tenant_id', $user->tenant_id)
            ->where('type', 'direct')
            ->whereHas('participants', fn ($q) => $q->where('user_id', $user->id))
            ->whereHas('participants', fn ($q) => $q->where('user_id', $recipient->id))
            ->first();

        if (! $conversation) {
            $conversation = DB::transaction(function () use ($user, $recipient) {
                $conv = Conversation::create([
                    'tenant_id' => $user->tenant_id,
                    'type' => 'direct',
                    'created_by' => $user->id,
                    'last_message_at' => now(),
                ]);

                $conv->participants()->attach([
                    $user->id => ['last_read_at' => now()],
                    $recipient->id => ['last_read_at' => null],
                ]);

                return $conv;
            });
        }

        return $conversation;
    }

    /**
     * Crée ou récupère le groupe de classe et synchronise ses participants.
     */
    public function getOrCreateClassGroup(int $classId, int $tenantId): Conversation
    {
        $classe = Classe::findOrFail($classId);

        $conversation = Conversation::firstOrCreate(
            [
                'tenant_id' => $tenantId,
                'type' => 'class_group',
                'class_id' => $classId,
            ],
            [
                'title' => 'Classe ' . $classe->nom,
                'last_message_at' => now(),
            ]
        );

        // Synchroniser les élèves de la classe
        $studentUserIds = User::where('tenant_id', $tenantId)
            ->whereRaw('LOWER(role) = ?', ['eleve'])
            ->whereHas('eleve', fn ($q) => $q->where('classe_id', $classId))
            ->pluck('id')
            ->all();

        // Synchroniser les enseignants affiliés à la classe
        $teacherUserIds = DB::table('classe_enseignant')
            ->join('enseignants', 'classe_enseignant.enseignant_id', '=', 'enseignants.id')
            ->where('classe_enseignant.classe_id', $classId)
            ->whereNotNull('enseignants.user_id')
            ->pluck('enseignants.user_id')
            ->all();

        $allMemberIds = array_unique(array_merge($studentUserIds, $teacherUserIds));

        $existingParticipantIds = $conversation->participants()->pluck('users.id')->all();
        $missingIds = array_diff($allMemberIds, $existingParticipantIds);

        if (!empty($missingIds)) {
            $conversation->participants()->attach($missingIds, ['last_read_at' => null]);
        }

        return $conversation;
    }

    /**
     * Récupère la liste de toutes les conversations actives d'un utilisateur
     */
    public function getUserConversations(User $user): Collection
    {
        // Assurer que les groupes de classe auxquels l'utilisateur a droit sont synchronisés
        $this->getAuthorizedContacts($user);

        $conversations = Conversation::with(['participants', 'classe.niveau', 'lastCommunication.sender'])
            ->where('tenant_id', $user->tenant_id)
            ->whereHas('participants', fn ($q) => $q->where('user_id', $user->id))
            ->orderByDesc('last_message_at')
            ->get();

        return $conversations->map(function ($conv) use ($user) {
            $participantPivot = $conv->participants->firstWhere('id', $user->id)?->pivot;
            $lastReadAt = $participantPivot?->last_read_at;

            $unreadCount = Communication::where('conversation_id', $conv->id)
                ->where('sender_id', '!=', $user->id)
                ->when($lastReadAt, fn ($q) => $q->where('created_at', '>', $lastReadAt))
                ->count();

            if ($conv->isClassGroup()) {
                $title = $conv->title ?: ('Classe ' . ($conv->classe?->nom ?? 'N/A'));
                $avatar = null;
                $subtitle = 'Groupe de classe • ' . ($conv->classe?->niveau?->nom ?? '');
            } else {
                $other = $conv->getOtherParticipant($user->id);
                $title = $other ? ($other->name ?? ($other->nom . ' ' . $other->prenom)) : 'Utilisateur';
                $avatar = $other?->image ? asset('storage/' . $other->image) : null;
                $subtitle = ucfirst((string) $other?->role);
            }

            $lastComm = $conv->lastCommunication;
            $lastMessageText = '';
            if ($lastComm) {
                $prefix = $lastComm->sender_id === $user->id ? 'Vous: ' : (($conv->isClassGroup() ? ($lastComm->sender?->prenom ?? 'Membre') . ': ' : ''));
                if ($lastComm->type === 'image') {
                    $lastMessageText = $prefix . '📷 Photo';
                } elseif ($lastComm->type === 'audio') {
                    $lastMessageText = $prefix . '🎤 Note vocale (' . ($lastComm->formatted_duration ?? 'Audio') . ')';
                } elseif ($lastComm->type === 'video') {
                    $lastMessageText = $prefix . '🎬 Vidéo';
                } elseif ($lastComm->type === 'file') {
                    $lastMessageText = $prefix . '📎 ' . ($lastComm->file_name ?? 'Fichier');
                } else {
                    $lastMessageText = $prefix . $lastComm->content;
                }
            }

            return [
                'id' => $conv->id,
                'type' => $conv->type,
                'title' => $title,
                'subtitle' => $subtitle,
                'avatar' => $avatar,
                'is_class_group' => $conv->isClassGroup(),
                'unread_count' => $unreadCount,
                'last_message' => $lastMessageText,
                'last_message_time' => $conv->last_message_at ? $conv->last_message_at->diffForHumans() : '',
            ];
        });
    }

    /**
     * Récupère les messages d'une conversation avec autorisation
     */
    public function getConversationMessages(User $user, int $conversationId, ?int $afterId = null): array
    {
        $conversation = Conversation::with(['participants', 'classe'])
            ->where('tenant_id', $user->tenant_id)
            ->findOrFail($conversationId);

        // Vérifier l'accès
        abort_unless($conversation->participants->contains('id', $user->id), 403, 'Accès non autorisé à cette conversation.');

        $query = Communication::with('sender')
            ->where('conversation_id', $conversation->id)
            ->when($afterId, fn ($q) => $q->where('id', '>', $afterId))
            ->orderBy('created_at', 'asc');

        $communications = $query->get();

        // Marquer comme lu
        $this->markAsRead($user, $conversationId);

        $messages = $communications->map(function ($msg) use ($user) {
            return [
                'id' => $msg->id,
                'sender_id' => $msg->sender_id,
                'is_me' => $msg->sender_id === $user->id,
                'sender_name' => $msg->sender?->name ?? ($msg->sender?->nom . ' ' . $msg->sender?->prenom),
                'sender_role' => ucfirst((string) $msg->sender?->role),
                'type' => $msg->type,
                'content' => $msg->content,
                'file_url' => $msg->file_url,
                'file_name' => $msg->file_name,
                'mime_type' => $msg->mime_type,
                'duration' => $msg->duration,
                'formatted_duration' => $msg->formatted_duration,
                'created_at' => $msg->created_at->format('H:i'),
                'created_date' => $msg->created_at->format('d/m/Y'),
            ];
        });

        return [
            'conversation' => [
                'id' => $conversation->id,
                'type' => $conversation->type,
                'title' => $conversation->isClassGroup() ? ($conversation->title ?: 'Classe ' . $conversation->classe?->nom) : ($conversation->getOtherParticipant($user->id)?->name ?? 'Conversation'),
                'subtitle' => $conversation->isClassGroup() ? 'Groupe de classe' : ucfirst((string) $conversation->getOtherParticipant($user->id)?->role),
                'is_class_group' => $conversation->isClassGroup(),
            ],
            'messages' => $messages,
        ];
    }

    /**
     * Envoie un message dans une conversation
     */
    public function sendMessage(User $sender, int $conversationId, array $data, ?UploadedFile $file = null): Communication
    {
        $conversation = Conversation::with('participants')
            ->where('tenant_id', $sender->tenant_id)
            ->findOrFail($conversationId);

        abort_unless($conversation->participants->contains('id', $sender->id), 403, 'Vous ne faites pas partie de cette conversation.');

        $type = $data['type'] ?? 'text';
        $content = $data['content'] ?? null;
        $duration = !empty($data['duration']) ? (int) $data['duration'] : null;

        $filePath = null;
        $fileName = null;
        $mimeType = null;
        $fileSize = null;

        if ($file && $file->isValid()) {
            $fileName = $file->getClientOriginalName();
            $mimeType = $file->getMimeType();
            $fileSize = $file->getSize();

            // Déterminer le type selon le MIME
            if (str_starts_with($mimeType, 'image/')) {
                $type = 'image';
            } elseif (str_starts_with($mimeType, 'audio/')) {
                $type = 'audio';
            } elseif (str_starts_with($mimeType, 'video/')) {
                $type = 'video';
            } else {
                $type = 'file';
            }

            $storagePath = "communications/{$sender->tenant_id}/{$conversationId}";
            $filePath = $file->store($storagePath, 'public');
        }

        if ($type === 'text' && empty(trim((string) $content))) {
            throw ValidationException::withMessages(['content' => 'Le contenu du message ne peut pas être vide.']);
        }

        return DB::transaction(function () use ($sender, $conversation, $type, $content, $filePath, $fileName, $mimeType, $fileSize, $duration) {
            $communication = Communication::create([
                'tenant_id' => $sender->tenant_id,
                'conversation_id' => $conversation->id,
                'sender_id' => $sender->id,
                'type' => $type,
                'content' => $content,
                'file_path' => $filePath,
                'file_name' => $fileName,
                'mime_type' => $mimeType,
                'file_size' => $fileSize,
                'duration' => $duration,
                'is_read' => false,
            ]);

            $conversation->update(['last_message_at' => now()]);

            // Mettre à jour last_read_at pour l'expéditeur
            ConversationParticipant::where('conversation_id', $conversation->id)
                ->where('user_id', $sender->id)
                ->update(['last_read_at' => now()]);

            return $communication->load('sender');
        });
    }

    /**
     * Marque une conversation comme lue pour un utilisateur
     */
    public function markAsRead(User $user, int $conversationId): void
    {
        ConversationParticipant::where('conversation_id', $conversationId)
            ->where('user_id', $user->id)
            ->update(['last_read_at' => now()]);

        // Pour les conversations directes, marquer is_read = true
        Communication::where('conversation_id', $conversationId)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    /**
     * Nombre total de messages non lus pour un utilisateur
     */
    public function getUnreadCount(User $user): int
    {
        $conversationIds = ConversationParticipant::where('user_id', $user->id)
            ->pluck('conversation_id')
            ->all();

        if (empty($conversationIds)) {
            return 0;
        }

        $participants = ConversationParticipant::where('user_id', $user->id)->get()->keyBy('conversation_id');

        $totalUnread = 0;
        foreach ($conversationIds as $cId) {
            $lastRead = $participants[$cId]->last_read_at ?? null;
            $unreadInConv = Communication::where('conversation_id', $cId)
                ->where('sender_id', '!=', $user->id)
                ->when($lastRead, fn ($q) => $q->where('created_at', '>', $lastRead))
                ->count();
            $totalUnread += $unreadInConv;
        }

        return $totalUnread;
    }
}
