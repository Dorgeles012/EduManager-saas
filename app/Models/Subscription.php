<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

use App\Models\User;
use App\Models\Plan;
use App\Models\Payment;


class Subscription extends Model
{
    use HasFactory;

    protected $table = 'subscriptions';

    // Structure cible (séparation Plans vs Subscriptions)
    protected $fillable = [
        'tenant_id',
        'client_id',
        'user_id',
        'plan_id',
        'amount',
        'status',
        'name',
        'type',
        'price',
        'duration',
'date_debut',
        'date_fin',
        'statut',
        'abonnement_status',
    ];

    // Constantes du flux de validation d'abonnement
    public const ABONNEMENT_EN_ATTENTE = 'en_attente';
    public const ABONNEMENT_PAYE = 'paye';
    public const ABONNEMENT_ACTIF = 'actif';
    public const ABONNEMENT_EXPIRE = 'expire';

    public function isAbonnementActif(): bool
    {
        return $this->abonnement_status === self::ABONNEMENT_ACTIF;
    }

    public function isAbonnementPaye(): bool
    {
        return $this->abonnement_status === self::ABONNEMENT_PAYE;
    }

    public function isAbonnementEnAttente(): bool
    {
        return $this->abonnement_status === self::ABONNEMENT_EN_ATTENTE;
    }


    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'subscription_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'subscription_id');
    }
}

