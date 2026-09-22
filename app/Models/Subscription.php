<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Plan;
use App\Models\Payment;
use App\Models\Etablissement;

class Subscription extends Model
{
    use HasFactory;

    protected $table = 'subscriptions';

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

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'amount' => 'integer',
        'price' => 'integer',
        'duration' => 'integer',
    ];

    // Constantes du flux de validation et de cycle de vie d'abonnement
    public const ABONNEMENT_EN_ATTENTE = 'en_attente';
    public const ABONNEMENT_PAYE = 'paye';
    public const ABONNEMENT_ACTIF = 'actif';
    public const ABONNEMENT_EXPIRE = 'expire';
    public const GRACE_DAYS = 7;

    protected static function booted(): void
    {
        static::creating(function (Subscription $subscription) {
            if ($subscription->plan_id && empty($subscription->name)) {
                $plan = Plan::find($subscription->plan_id);
                if ($plan) {
                    $subscription->name = $subscription->name ?: ($plan->nom ?? 'Abonnement');
                    $subscription->type = $subscription->type ?: ($plan->durationLabel() ?? 'Mensuel');
                    $subscription->price = $subscription->price ?: (int) $plan->prix;
                    $subscription->amount = $subscription->amount ?: (int) $plan->prix;
                    $subscription->duration = $subscription->duration ?: $plan->durationInMonths();
                }
            }
            if (empty($subscription->name)) {
                $subscription->name = 'Abonnement';
            }
            if (empty($subscription->type)) {
                $subscription->type = 'Mensuel';
            }
            if (empty($subscription->statut) || ! in_array($subscription->statut, ['active', 'expired', 'cancelled'], true)) {
                $subscription->statut = 'active';
            }
            if (empty($subscription->status) || ! in_array($subscription->status, ['active', 'inactive'], true)) {
                $subscription->status = 'active';
            }
        });
    }

    /**
     * Vérifie si l'abonnement est dans le statut validé/actif.
     */
    public function isAbonnementActif(): bool
    {
        return $this->abonnement_status === self::ABONNEMENT_ACTIF
            || ($this->abonnement_status === null && in_array(strtolower((string) $this->statut), ['active', 'actif'], true));
    }

    /**
     * Vérifie si l'abonnement a été payé par le client et attend validation par le SADMIN.
     */
    public function isAbonnementPaye(): bool
    {
        return $this->abonnement_status === self::ABONNEMENT_PAYE;
    }

    /**
     * Vérifie si l'abonnement est en attente de paiement.
     */
    public function isAbonnementEnAttente(): bool
    {
        return $this->abonnement_status === self::ABONNEMENT_EN_ATTENTE;
    }

    /**
     * Vérifie si l'abonnement est marqué ou calculé comme expiré.
     */
    public function isAbonnementExpire(): bool
    {
        return $this->abonnement_status === self::ABONNEMENT_EXPIRE || $this->isExpired();
    }

    /**
     * Détermine si l'abonnement est expiré selon sa date de fin (date_fin).
     *
     * Si la date_fin est strictement antérieure à aujourd'hui (00:00:00),
     * l'abonnement est considéré comme expiré.
     */
    public function isExpired(): bool
    {
        if (! $this->date_fin) {
            return $this->abonnement_status === self::ABONNEMENT_EXPIRE
                || in_array(strtolower((string) $this->statut), ['expired', 'expire', 'inactive', 'inactif'], true);
        }

        return $this->date_fin->startOfDay()->lt(Carbon::today()->startOfDay());
    }

    /**
     * Calcule la date de fin de la période de grâce de 7 jours (date_fin + 7 jours).
     */
    public function dateFinGrace(): ?Carbon
    {
        return $this->date_fin ? $this->date_fin->copy()->addDays(self::GRACE_DAYS) : null;
    }

    /**
     * Détermine si l'abonnement est actuellement dans la période de grâce de 7 jours.
     * (Abonnement expiré, mais date actuelle <= date_fin + 7 jours).
     */
    public function isWithinGracePeriod(): bool
    {
        if (! $this->date_fin || ! $this->isExpired()) {
            return false;
        }

        $finGrace = $this->dateFinGrace();

        return $finGrace !== null && Carbon::today()->startOfDay()->lte($finGrace->startOfDay());
    }

    /**
     * Détermine si la période de grâce de 7 jours est également expirée.
     * (Date actuelle > date_fin + 7 jours).
     */
    public function isGraceExpired(): bool
    {
        if (! $this->date_fin) {
            return false;
        }

        $finGrace = $this->dateFinGrace();

        return $finGrace !== null && Carbon::today()->startOfDay()->gt($finGrace->startOfDay());
    }

    /**
     * Retourne le nombre de jours restants dans la période de grâce.
     */
    public function remainingGraceDays(): ?int
    {
        if (! $this->date_fin || ! $this->isExpired()) {
            return null;
        }

        $finGrace = $this->dateFinGrace();
        if (! $finGrace) {
            return null;
        }

        $today = Carbon::today()->startOfDay();
        $endGrace = $finGrace->startOfDay();

        if ($today->gt($endGrace)) {
            return 0;
        }

        return (int) $today->diffInDays($endGrace, false);
    }

    /**
     * Détermine si l'accès est autorisé (soit actif et non expiré, soit dans la période de grâce).
     */
    public function isValid(): bool
    {
        if ($this->isAbonnementActif() && ! $this->isExpired()) {
            return true;
        }

        return $this->isWithinGracePeriod();
    }

    /*
    |--------------------------------------------------------------------------
    | Relations Eloquent
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
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

    /**
     * Résout l'utilisateur client associé à cet abonnement de manière robuste.
     */
    public function resolveClient(): ?User
    {
        if ($this->relationLoaded('user') && $this->user) {
            return $this->user;
        }
        if ($this->relationLoaded('client') && $this->client) {
            return $this->client;
        }
        if ($this->user_id) {
            $user = $this->user ?? User::find($this->user_id);
            if ($user) {
                return $user;
            }
        }
        if ($this->client_id) {
            $user = $this->client ?? User::find($this->client_id);
            if ($user) {
                return $user;
            }
        }
        if ($this->tenant_id) {
            $user = User::where('tenant_id', $this->tenant_id)->whereRaw('LOWER(role) = ?', ['client'])->first()
                ?? User::where('tenant_id', $this->tenant_id)->first();
            if ($user) {
                return $user;
            }
        }
        return null;
    }

    /**
     * Accesseur pour le nom du client avec fallbacks complets.
     */
    public function getClientNameAttribute(): string
    {
        $client = $this->resolveClient();
        if ($client) {
            $nomComplet = trim(($client->nom ?? '') . ' ' . ($client->prenom ?? ''));
            if (!empty($nomComplet)) {
                return $nomComplet;
            }
            if (!empty($client->name)) {
                return $client->name;
            }
            if (!empty($client->email)) {
                return $client->email;
            }
        }

        if ($this->relationLoaded('tenant') && $this->tenant) {
            $resp = trim(($this->tenant->nom_responsable ?? '') . ' ' . ($this->tenant->prenom_responsable ?? ''));
            if (!empty($resp)) {
                return $resp;
            }
            if (!empty($this->tenant->nom_entreprise)) {
                return $this->tenant->nom_entreprise;
            }
        }

        return '—';
    }

    /**
     * Résout l'établissement associé de manière robuste.
     */
    public function resolveEtablissement(): ?Etablissement
    {
        $client = $this->resolveClient();
        if ($client) {
            if ($client->relationLoaded('etablissement') && $client->etablissement) {
                return $client->etablissement;
            }
            if ($client->etablissement_id) {
                $etab = $client->etablissement ?? Etablissement::find($client->etablissement_id);
                if ($etab) {
                    return $etab;
                }
            }
            if ($client->relationLoaded('etablissements') && $client->etablissements && $client->etablissements->isNotEmpty()) {
                return $client->etablissements->first();
            }
        }

        if ($this->relationLoaded('tenant') && $this->tenant && $this->tenant->etablissements && $this->tenant->etablissements->isNotEmpty()) {
            return $this->tenant->etablissements->first();
        }

        $tenantId = $this->tenant_id ?? $client?->tenant_id;
        if ($tenantId) {
            return Etablissement::where('tenant_id', $tenantId)->first();
        }

        return null;
    }

    /**
     * Accesseur pour le nom de l'établissement avec fallbacks complets.
     */
    public function getEtablissementNameAttribute(): string
    {
        $etab = $this->resolveEtablissement();
        return $etab?->nom ?? '—';
    }
}
