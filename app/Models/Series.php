<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Series extends Model
{
    use HasFactory;

    protected $table = 'series';

    protected $fillable = [
        'tenant_id',
        // Backward-compat: l'ancien schéma utilisait `id_classe` (1 série → 1 classe)
        'id_classe',
        'nom_serie',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'id_classe' => 'integer',
    ];

    public function matieres(): BelongsToMany
    {
        return $this->belongsToMany(Matiere::class, 'serie_matieres', 'serie_id', 'matiere_id')
            ->withPivot('coefficient')
            ->withTimestamps();
    }

    public function serieMatieres(): HasMany
    {
        return $this->hasMany(SerieMatiere::class, 'serie_id');
    }

    /**
     * Nouvelle relation: une série peut être associée à plusieurs classes.
     */
    public function classes(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            Classe::class,
            'classe_serie',
            'serie_id',
            'classe_id'
        )->withTimestamps();
    }

    /**
     * Ancien lien 1→1 (laissé pour compatibilité).
     */
    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class, 'id_classe');
    }

    public function eleves(): HasMany
    {
        return $this->hasMany(Eleve::class, 'id_serie');
    }

    public function enseignants(): BelongsToMany
    {
        return $this->belongsToMany(Enseignant::class, 'enseignant_serie', 'serie_id', 'enseignant_id')->withTimestamps();
    }
}

