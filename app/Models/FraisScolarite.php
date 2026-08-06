<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FraisScolarite extends Model
{
    use HasFactory;

    protected $table = 'frais_scolarite';

    protected $fillable = [
        'tenant_id',
        'etablissement_id',
        'niveau_id',
        'annee_academique_id',
        'inscription',
        'scolarite',
        'autres_frais',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'etablissement_id' => 'integer',
        'niveau_id' => 'integer',
        'annee_academique_id' => 'integer',
        'inscription' => 'integer',
        'scolarite' => 'integer',
        'autres_frais' => 'integer',
    ];

    public function niveau(): BelongsTo
    {
        return $this->belongsTo(Niveau::class, 'niveau_id');
    }

    public function anneeAcademique(): BelongsTo
    {
        return $this->belongsTo(AnneeAcademique::class, 'annee_academique_id');
    }

    /**
     * Montant total (inscription + scolarité + autres frais).
     */
    public function getMontantTotalAttribute(): int
    {
        return (int) $this->inscription + (int) $this->scolarite + (int) $this->autres_frais;
    }
}
