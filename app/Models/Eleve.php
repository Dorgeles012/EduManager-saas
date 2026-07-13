<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Eleve extends Model
{
    use HasFactory;

    protected $table = 'eleves';

    protected $fillable = [
        'tenant_id',
        'etablissement_id',
        'classe_id',
        'id_serie',
        'niveau_id',
        'parent_id',
        'matricule',
        'nom',
        'prenom',
        'sexe',
        'date_naissance',
        'lieu_naissance',
        'ancien_etablissement',
        'nationalite',
        'interne',
        'affecte',
        'photo',
        'statut',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'etablissement_id' => 'integer',
        'classe_id' => 'integer',
        'id_serie' => 'integer',
        'niveau_id' => 'integer',
        'parent_id' => 'integer',
        'date_naissance' => 'date',
        'interne' => 'boolean',
        'affecte' => 'boolean',
    ];

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class, 'classe_id');
    }

    public function niveau(): BelongsTo
    {
        return $this->belongsTo(Niveau::class, 'niveau_id');
    }

    public function serie(): BelongsTo
    {
        return $this->belongsTo(Series::class, 'id_serie');
    }

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class, 'etablissement_id');
    }

    public function bulletins(): HasMany { return $this->hasMany(Bulletin::class); }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id')->whereRaw('LOWER(role) = ?', ['parent']);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo) {
            return null;
        }

        $path = $this->normalizePhotoPath($this->photo);

        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return Storage::disk('public')->exists($path)
            ? Storage::url($path)
            : null;
    }

    private function normalizePhotoPath(string $photo): string
    {
        $normalized = ltrim($photo, '/');

        if (str_starts_with($normalized, 'storage/')) {
            $normalized = substr($normalized, strlen('storage/'));
        }

        return $normalized;
    }
}
