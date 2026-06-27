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
        'discipline',
        'moyenne',
        'coefficient',
        'moyenne_coefficient',
        'rang',
        'appréciation',
        'professeur',
        'signature',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'bulletin_id' => 'integer',
        'moyenne' => 'float',
        'coefficient' => 'float',
        'moyenne_coefficient' => 'float',
        'rang' => 'integer',
    ];

    public function bulletin(): BelongsTo
    {
        return $this->belongsTo(Bulletin::class, 'bulletin_id');
    }
}

