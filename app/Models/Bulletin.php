<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bulletin extends Model
{
    use HasFactory;

    protected $table = 'bulletins';

    protected $fillable = [
        'tenant_id',
        'etablissement_id',
        'annee_academique_id',
        'eleve_id',
        'classe_id',
        'trimestre',

        'total_heures',
        'absences',
        'rang',
        'moyenne_generale',
        'resultat_classe',
        'decision',
        'observation_conseil',
        'date',

        'signature_professeur_principal',
        'signature_directeur',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'etablissement_id' => 'integer',
        'annee_academique_id' => 'integer',
        'eleve_id' => 'integer',
        'classe_id' => 'integer',
        'total_heures' => 'float',
        'absences' => 'integer',
        'rang' => 'integer',
        'moyenne_generale' => 'float',
        'date' => 'date',
    ];

    public function disciplines(): HasMany
    {
        return $this->hasMany(BulletinDiscipline::class, 'bulletin_id');
    }

    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class, 'eleve_id');
    }
}

