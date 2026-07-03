<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SerieMatiere extends Model
{
    protected $table = 'serie_matieres';

    protected $fillable = ['serie_id', 'matiere_id', 'coefficient'];

    protected $casts = [
        'serie_id' => 'integer',
        'matiere_id' => 'integer',
        'coefficient' => 'integer',
    ];

    public function serie(): BelongsTo
    {
        return $this->belongsTo(Series::class, 'serie_id');
    }

    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class);
    }
}
