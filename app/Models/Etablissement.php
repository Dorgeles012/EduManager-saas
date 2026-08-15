<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id', 'tenant_id');
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    public function niveaux(): HasMany
    {
        return $this->hasMany(Niveau::class, 'etablissement_id');
    }

    public function classes(): HasMany
    {
        return $this->hasMany(Classe::class, 'etablissement_id');
    }

    public function eleves(): HasMany
    {
        return $this->hasMany(Eleve::class, 'etablissement_id');
    }

    public function getLogoUrlAttribute(): string
    {
        if (! empty($this->logo)) {
            return asset('storage/' . ltrim($this->logo, '/'));
        }

        return asset('images/default-school.png');
    }
}
