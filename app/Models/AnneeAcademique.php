<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class AnneeAcademique extends Model
{
    use HasFactory;

    protected $table = 'annee_academique';

    protected $fillable = [
        'tenant_id',
        'etablissement_id',
        'libelle',
        'date_debut',
        'date_fin',
        'statut',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'etablissement_id' => 'integer',
        'date_debut' => 'date',
        'date_fin' => 'date',
        'statut' => 'string',
    ];

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function isExpired(): bool
    {
        return $this->date_fin !== null
            && $this->date_fin->startOfDay()->lt(Carbon::today());
    }

    public function isEffectivelyActive(): bool
    {
        return $this->statut === 'active' && ! $this->isExpired();
    }
}

