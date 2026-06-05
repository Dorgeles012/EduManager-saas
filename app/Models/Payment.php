<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'tenant_id',
        'subscription_id',
        'amount',
        'payment_method',
        'status',
        'montant',
        'methode_paiement',
        'reference_paiement',
        'date_paiement',
        'statut',
    ];

    protected $casts = [
        'date_paiement' => 'date',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }
}
