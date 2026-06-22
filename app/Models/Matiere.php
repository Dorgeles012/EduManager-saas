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
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'coefficient' => 'integer',
    ];

    public function enseignants(): HasMany
    {
        return $this->hasMany(Enseignant::class, 'matiere_id');
    }
}

