<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classe extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'tenant_id',
        'etablissement_id',
        'niveau_id',
        'filiere_id',
        'nom',
        'capacite',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'etablissement_id' => 'integer',
        'niveau_id' => 'integer',
        'filiere_id' => 'integer',
        'capacite' => 'integer',
    ];

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class, 'etablissement_id');
    }

    public function niveau(): BelongsTo
    {
        return $this->belongsTo(Niveau::class, 'niveau_id');
    }

    public function eleves(): HasMany
    {
        return $this->hasMany(Eleve::class, 'classe_id');
    }

    public function bulletins(): HasMany { return $this->hasMany(Bulletin::class); }

    public function series(): HasMany
    {
        return $this->hasMany(Series::class, 'id_classe');
    }
}
