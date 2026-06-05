<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $table = 'plans';

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }


    protected $fillable = [
        'nom',
        'description',
        'prix',
        'subscription_type_id',
        'statut',
    ];


}

