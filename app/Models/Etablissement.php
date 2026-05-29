<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Etablissement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'etablissements';

    protected $fillable = [
        'tenant_id',
        'nom',
        'acronyme',
        'type_etablissement',
        'email',
        'telephone',
        'adresse',
        'logo',
        'statut',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
    ];
}

