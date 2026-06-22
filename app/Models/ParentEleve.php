<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParentEleve extends Model
{
    use HasFactory;

    protected $table = 'parents';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'nom',
        'prenom',
        'email',
        'telephone',
        'adresse',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'user_id' => 'integer',
    ];

    public function eleves(): HasMany
    {
        return $this->hasMany(Eleve::class, 'parent_id');
    }
}
