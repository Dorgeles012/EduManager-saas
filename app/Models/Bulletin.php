<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bulletin extends Model
{
    use HasFactory;

    public const STATUT_BROUILLON = 'brouillon';
    public const STATUT_EN_ATTENTE = 'en_attente';
    public const STATUT_PUBLIE = 'publie';

    public const STATUTS = [
        self::STATUT_BROUILLON => 'Brouillon',
        self::STATUT_EN_ATTENTE => 'En attente de publication',
        self::STATUT_PUBLIE => 'Publié',
    ];

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
        'statut',
        'publie_le',
        'publie_par_id',
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
        'publie_le' => 'datetime',
        'publie_par_id' => 'integer',

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

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class, 'classe_id');
    }

    public function anneeAcademique(): BelongsTo
    {
        return $this->belongsTo(AnneeAcademique::class, 'annee_academique_id');
    }

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class, 'etablissement_id');
    }

    public function scopePublie(Builder $query): Builder
    {
        return $query->where('statut', self::STATUT_PUBLIE);
    }

    public function isPublie(): bool
    {
        return $this->statut === self::STATUT_PUBLIE;
    }
}
