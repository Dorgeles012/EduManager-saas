<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NoteHistorique extends Model
{
    protected $table = 'note_historiques';

    protected $fillable = [
        'tenant_id',
        'note_id',
        'action',
        'acteur_id',
        'avant',
        'apres',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'note_id' => 'integer',
        'acteur_id' => 'integer',
        'avant' => 'array',
        'apres' => 'array',
    ];

    public function note(): BelongsTo
    {
        return $this->belongsTo(Note::class, 'note_id');
    }

    public function acteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acteur_id');
    }
}
