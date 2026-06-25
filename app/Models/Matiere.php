<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    // Si `matieres.serie` contient l'id de `series.id` (cas récent), alors cette relation marche.
    // La filtration "par série" est appliquée côté contrôleur pour gérer aussi l'ancien schéma.
    public function serieModel()
    {
        return $this->belongsTo(Series::class, 'serie', 'id');
    }



    protected static function booted()
    {
        static::addGlobalScope('tenant', function ($builder) {
            if (auth()->check()) {
                $builder->where('tenant_id', auth()->user()->tenant_id);
            }
        });
    }

}


