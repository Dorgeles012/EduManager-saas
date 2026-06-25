<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Series extends Model
{
    use HasFactory;

    protected $table = 'series';

    protected $fillable = [
        'tenant_id',
        'nom_serie',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
    ];

    public function matieres(): HasMany
    {
        return $this->hasMany(Matiere::class, 'serie', 'id');
    }
}

