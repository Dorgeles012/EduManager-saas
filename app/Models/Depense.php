<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depense extends Model
{
    use HasFactory;

    protected $table = 'depense';

    protected $primaryKey = 'id_depense';

    protected $fillable = [
        'tenant_id',
        'libel_depense',
        'montant',
        'categorie',
        'date_depense',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'montant' => 'integer',
        'date_depense' => 'date',
    ];
}
