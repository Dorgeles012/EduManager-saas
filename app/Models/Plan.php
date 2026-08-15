<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $table = 'plans';

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }

    public function subscriptionType(): BelongsTo
    {
        return $this->belongsTo(SubscriptionType::class, 'subscription_type_id');
    }


    protected $fillable = [
        'nom',
        'description',
        'prix',
        'duration_type',
        'duration_value',
        'max_schools',
        'max_ecoles',
        'is_unlimited',
        'duree',
        'subscription_type_id',
        'statut',
    ];

    protected $casts = [
        'prix' => 'integer',
        'duration_value' => 'integer',
        'max_schools' => 'integer',
        'max_ecoles' => 'integer',
        'is_unlimited' => 'boolean',
        'duree' => 'integer',
    ];

    public function durationInMonths(): int
    {
        if ($this->duration_type === 'annual') {
            return 12;
        }

        if ($this->duration_type === 'monthly') {
            return 1;
        }

        return max(1, (int) ($this->duree ?: 1));
    }

    public function durationLabel(): string
    {
        return ($this->duration_type === 'annual' || (int) $this->duree >= 12) ? 'Annuel' : 'Mensuel';
    }

    public function schoolsLimitLabel(): string
    {
        if ($this->is_unlimited || ($this->max_schools === null && (int) ($this->max_ecoles ?? 1) >= 999)) {
            return 'Illimité';
        }

        $count = $this->max_schools ?? $this->max_ecoles ?? 1;
        $val = max(1, (int) $count);
        return $val === 1 ? '1 école' : "{$val} écoles";
    }

    public function allowsSchoolCount(int $schoolCount): bool
    {
        if ($this->is_unlimited || ($this->max_schools === null && (int) ($this->max_ecoles ?? 1) >= 999)) {
            return true;
        }

        $count = $this->max_schools ?? $this->max_ecoles ?? 1;
        return $schoolCount <= max(1, (int) $count);
    }
}

