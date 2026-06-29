<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Series extends Model
{
    use HasFactory;

    protected $table = 'series';

    protected $fillable = [
        'tenant_id',
        'id_classe',
        'nom_serie',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'id_classe' => 'integer',
    ];

    public function matieres(): HasMany
    {
        return $this->hasMany(Matiere::class, 'serie', 'id');
    }

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class, 'id_classe');
    }

    public function eleves(): HasMany
    {
        return $this->hasMany(Eleve::class, 'id_serie');
    }
}

