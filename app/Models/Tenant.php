<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = ['nom_entreprise', 'nom_responsable', 'prenom_responsable', 'email', 'telephone', 'adresse', 'statut'];

    public function etablissements(): HasMany { return $this->hasMany(Etablissement::class); }
    public function users(): HasMany { return $this->hasMany(User::class); }
}
