<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParentEleve extends Model
{
    use HasFactory;

    // Model conservé pour compatibilité, mais la table `parents` n'est plus utilisée.
    protected $table = 'users';

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

    // S'assurer que l'utilisateur correspondant a bien le rôle parent.
    public function scopeParent($query)
    {
        return $query->whereRaw('LOWER(role) = ?', ['parent']);
    }
}
