<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $table = 'subscriptions';

    protected $fillable = [
        'tenant_id',
        'name',
        'type',
        'price',
        'duration',
        'status',
    ];

    protected $casts = [
        'price' => 'integer',
        'duration' => 'integer',
    ];
}

