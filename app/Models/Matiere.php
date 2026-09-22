<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Schema;

class Matiere extends Model
{
    use HasFactory;

    protected $table = 'matieres';

    protected $fillable = [
        'tenant_id',
        'nom',
        'coefficient',
        'serie',
    ];


    protected $casts = [
        'tenant_id' => 'integer',
        'coefficient' => 'integer',
    ];

    public function enseignants(): HasMany
    {
        return $this->hasMany(Enseignant::class, 'matiere_id');
    }

    public function enseignantsPivot(): BelongsToMany
    {
        return $this->belongsToMany(Enseignant::class, 'enseignant_matiere', 'matiere_id', 'enseignant_id')->withTimestamps();
    }

    /**
     * Vérifie si un enseignant donné est responsable de cette matière.
     */
    public function isEnseignantResponsable(int|Enseignant $enseignant): bool
    {
        $enseignantId = is_int($enseignant) ? $enseignant : $enseignant->id;

        if ($this->enseignantsPivot()->where('enseignants.id', $enseignantId)->exists()) {
            return true;
        }

        return $this->enseignants()->where('id', $enseignantId)->exists();
    }

    /**
     * Retourne l'enseignant responsable de la matière.
     */
    public function getEnseignantResponsableAttribute(): ?Enseignant
    {
        return $this->enseignantsPivot()->first() ?? $this->enseignants()->first();
    }

    public function series(): BelongsToMany
    {
        return $this->belongsToMany(Series::class, 'serie_matieres', 'matiere_id', 'serie_id')
            ->withPivot('coefficient')
            ->withTimestamps();
    }

    // Si `matieres.serie` contient l'id de `series.id` (cas récent), alors cette relation marche.
    // La filtration "par série" est appliquée côté contrôleur pour gérer aussi l'ancien schéma.
    public function serieModel()
    {
        return $this->belongsTo(Series::class, 'serie', 'id');
    }



    protected static function booted()
    {
        static::addGlobalScope('tenant', function ($builder) {
            if (auth()->check() && Schema::hasColumn('matieres', 'tenant_id')) {
                $builder->where($builder->getModel()->qualifyColumn('tenant_id'), auth()->user()->tenant_id);
            }
        });
    }

}
