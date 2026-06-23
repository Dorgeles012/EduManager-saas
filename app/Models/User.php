<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;


    protected $fillable = [
        'tenant_id',
        'name',
        'nom',
        'prenom',
        'client_id',
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

    /**
     * Personnels appartenant à un client.
     */
    public function personnels(): HasMany
    {
        return $this->hasMany(User::class, 'client_id');
    }

    /**
     * Client propriétaire du personnel (self-relation).
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'user_id');
    }

    /**
     * Élèves rattachés à ce parent.
     *
     * La relation métier est : eleves.parent_id -> users.id
     */
    public function eleves(): HasMany
    {
        return $this->hasMany(Eleve::class, 'parent_id');
    }




    public function getNameAttribute(): ?string
    {
        return $this->nom;
    }

    public function setNameAttribute(?string $value): void
    {
        $this->attributes['nom'] = $value;
    }

    public const STATUT_ACTIF = 'actif';
    public const STATUT_BLOQUE = 'bloqué';

    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->tenant_id)) {
                $user->tenant_id = 1;
            }

            // Par défaut : toujours actif lors de la création (sauf si explicitement fourni)
            if (empty($user->statut)) {
                $user->statut = self::STATUT_ACTIF;
            }
        });
    }

    public function isBlocked(): bool
    {
        return $this->statut === self::STATUT_BLOQUE;
    }

    public function block(): self
    {
        $this->statut = self::STATUT_BLOQUE;
        $this->save();

        return $this;
    }

    public function unblock(): self
    {
        $this->statut = self::STATUT_ACTIF;
        $this->save();

        return $this;
    }
}

