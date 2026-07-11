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
        'mention',
        'total_coefficients',
        'total_points',
        'resultat_classe',
        'decision',
        'observation_conseil',
        'date',

        'signature_professeur_principal',
        'signature_directeur',
        'distinctions',
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
        'total_coefficients' => 'float',
        'total_points' => 'float',
        'date' => 'date',

        // Certains champs peuvent être stockés en JSON/array côté BD.
        // On les caste pour éviter "Array to string conversion" lors de la génération PDF.
        'distinctions' => 'array',
        'decision' => 'string',
        'observation_conseil' => 'string',
        'signature_directeur' => 'string',
    ];

    public function disciplines(): HasMany
    {
        return $this->hasMany(BulletinDiscipline::class, 'bulletin_id');
    }

    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class, 'eleve_id');
    }

    public function classe(): BelongsTo { return $this->belongsTo(Classe::class); }
    public function anneeAcademique(): BelongsTo { return $this->belongsTo(AnneeAcademique::class); }
    public function etablissement(): BelongsTo { return $this->belongsTo(Etablissement::class); }
}

