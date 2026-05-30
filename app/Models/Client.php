<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';

    protected $fillable = [
        'nom',
        'prenom',
        'telephone',
        'email',
        'adresse',
        'password',
        'status',
        'ville',
        'etablissement_id',
        'photo',
    ];

    protected $casts = [
        'etablissement_id' => 'integer',
        'telephone' => 'string',
        'email' => 'string',
        'status' => 'string',
    ];

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }
}

