<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Versement extends Model
{
    use HasFactory;

    protected $table = 'versements';

    protected $fillable = [
        'tenant_id',
        'scolarite_id',
        'montant',
        'date_versement',
        'methode',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'scolarite_id' => 'integer',
        'montant' => 'integer',
        'date_versement' => 'date',
    ];

    public function scolarite(): BelongsTo
    {
        return $this->belongsTo(Scolarite::class, 'scolarite_id');
    }
}
