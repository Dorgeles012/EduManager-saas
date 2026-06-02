<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;


    protected $fillable = [
        'tenant_id',
        'name',
        'nom',
        'prenom',
        'email',
        'telephone',
        'password',
        'image',
        'role',
        'statut',
        'adresse',
        'ville',
        'email_verified_at',
        'etablissement_id',
    ];


    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'etablissement_id' => 'integer',
        ];
    }

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class, 'etablissement_id');
    }


    public function getNameAttribute(): ?string
    {
        return $this->nom;
    }

    public function setNameAttribute(?string $value): void
    {
        $this->attributes['nom'] = $value;
    }

    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->tenant_id)) {
                $user->tenant_id = 1;
            }
        });
    }
}
