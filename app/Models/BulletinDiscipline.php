<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BulletinDiscipline extends Model
{
    use HasFactory;

    protected $table = 'bulletin_discipline';

    protected $fillable = [
        'tenant_id',
        'bulletin_id',
        'matiere_id',
        'discipline',
        'interrogation',
        'devoir',
        'composition',
        'moyenne',
        'coefficient',
        'moyenne_coefficient',
        'rang',
        'mention',
        'professeur',
        'signature',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'bulletin_id' => 'integer',
        'matiere_id' => 'integer',
        'moyenne' => 'float',
        'interrogation' => 'float',
        'devoir' => 'float',
        'composition' => 'float',
        'coefficient' => 'float',
        'moyenne_coefficient' => 'float',
        'rang' => 'integer',
    ];

    public function bulletin(): BelongsTo
    {
        return $this->belongsTo(Bulletin::class, 'bulletin_id');
    }

    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class);
    }
}

