<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Enseignant extends Model
{
    use HasFactory;

    protected $table = 'enseignants';

    protected $fillable = [
        'tenant_id',
        'etablissement_id',
        'user_id',
        'nom',
        'prenoms',
        'email',
        'telephone',
        'matricule',
        'nombre_annees_enseignement',
        'sexe',
        'photo',
        'password',
        'matiere_id',
        'specialite',
        'statut',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'etablissement_id' => 'integer',
        'user_id' => 'integer',
        'matiere_id' => 'integer',
        'nombre_annees_enseignement' => 'integer',
    ];

    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class, 'matiere_id');
    }

    public function matieres(): BelongsToMany
    {
        return $this->belongsToMany(Matiere::class, 'enseignant_matiere', 'enseignant_id', 'matiere_id');
    }

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(Classe::class, 'classe_enseignant', 'enseignant_id', 'classe_id')->withTimestamps();
    }

    public function series(): BelongsToMany
    {
        return $this->belongsToMany(Series::class, 'enseignant_serie', 'enseignant_id', 'serie_id')->withTimestamps();
    }

    public function emploisDuTemps(): \Illuminate\Database\Eloquent\Relations\HasMany { return $this->hasMany(EmploiTemps::class, 'enseignant_id'); }
}
