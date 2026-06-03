<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
}

