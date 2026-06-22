<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Scolarite extends Model
{
    use HasFactory;

    protected $table = 'scolarites';

    protected $fillable = [
        'tenant_id',
        'eleve_id',
        'montant_total',
        'montant_paye',
        'reste',
        'annee_scolaire',
        'statut',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'eleve_id' => 'integer',
        'montant_total' => 'integer',
        'montant_paye' => 'integer',
        'reste' => 'integer',
    ];

    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class, 'eleve_id');
    }

    public function versements(): HasMany
    {
        return $this->hasMany(Versement::class, 'scolarite_id');
    }
}
