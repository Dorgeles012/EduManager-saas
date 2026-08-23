<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversation extends Model
{
    use HasFactory;

    protected $table = 'conversations';

    protected $fillable = [
        'tenant_id',
        'type',
        'class_id',
        'title',
        'created_by',
        'last_message_at',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'class_id' => 'integer',
        'created_by' => 'integer',
        'last_message_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class, 'class_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_participants', 'conversation_id', 'user_id')
            ->withPivot('last_read_at')
            ->withTimestamps();
    }

    public function conversationParticipants(): HasMany
    {
        return $this->hasMany(ConversationParticipant::class, 'conversation_id');
    }

    public function communications(): HasMany
    {
        return $this->hasMany(Communication::class, 'conversation_id');
    }

    public function lastCommunication(): HasOne
    {
        return $this->hasOne(Communication::class, 'conversation_id')->latestOfMany();
    }

    public function isClassGroup(): bool
    {
        return $this->type === 'class_group';
    }

    public function isDirect(): bool
    {
        return $this->type === 'direct';
    }

    /**
     * Pour les conversations directes : trouver l'autre participant
     */
    public function getOtherParticipant(int $currentUserId): ?User
    {
        if ($this->isClassGroup()) {
            return null;
        }

        return $this->participants->firstWhere('id', '!=', $currentUserId);
    }
}
