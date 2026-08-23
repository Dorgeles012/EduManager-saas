<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Communication extends Model
{
    use HasFactory;

    protected $table = 'communications';

    protected $fillable = [
        'tenant_id',
        'conversation_id',
        'sender_id',
        'type',
        'content',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'duration',
        'is_read',
        'status',
        'delivered_at',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'conversation_id' => 'integer',
        'sender_id' => 'integer',
        'file_size' => 'integer',
        'duration' => 'integer',
        'is_read' => 'boolean',
        'delivered_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function getFileUrlAttribute(): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        if (str_starts_with($this->file_path, 'http://') || str_starts_with($this->file_path, 'https://')) {
            return $this->file_path;
        }

        return Storage::disk('public')->url($this->file_path);
    }

    public function getFormattedDurationAttribute(): ?string
    {
        if ($this->duration === null) {
            return null;
        }

        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;

        return sprintf('%02d:%02d', $minutes, $seconds);
    }
}
