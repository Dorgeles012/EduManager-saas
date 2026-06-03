<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
}

