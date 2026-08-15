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
        'is_unlimited',
        'duree',
        'subscription_type_id',
        'statut',
    ];

    protected $casts = [
        'prix' => 'integer',
        'duration_value' => 'integer',
        'max_schools' => 'integer',
        'is_unlimited' => 'boolean',
        'duree' => 'integer',
    ];

    public function durationInMonths(): int
    {
        $value = max(1, (int) ($this->duration_value ?: 1));

        return $this->duration_type === 'annual' ? $value * 12 : $value;
    }

    public function durationLabel(): string
    {
        return $this->duration_type === 'annual' ? 'Annuel' : 'Mensuel';
    }

    public function schoolsLimitLabel(): string
    {
        if ($this->is_unlimited) {
            return 'Illimite';
        }

        return (string) max(1, (int) ($this->max_schools ?? 1));
    }

    public function allowsSchoolCount(int $schoolCount): bool
    {
        return $this->is_unlimited || $schoolCount <= max(1, (int) ($this->max_schools ?? 1));
    }

}

