<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Note extends Model
{
    use HasFactory;

    protected $table = 'notes';

    protected $fillable = [
        'tenant_id',
        'eleve_id',
        'classe_id',
        'matiere_id',
'note',
        'periode',
        'annee_academique_id',
        'appreciation',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'eleve_id' => 'integer',
        'classe_id' => 'integer',
        'matiere_id' => 'integer',
        'note' => 'float',
    ];

    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class, 'eleve_id');
    }

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class, 'classe_id');
    }

public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class, 'matiere_id');
    }

    public function anneeAcademique(): BelongsTo
    {
        return $this->belongsTo(AnneeAcademique::class, 'annee_academique_id');
    }

    /**
     * Appréciation suggérée — source unique de vérité.
     */
    public function getAppreciationSuggereeAttribute(): string
    {
        return \App\Services\BulletinService::noteAppreciation($this->note);
    }
}
