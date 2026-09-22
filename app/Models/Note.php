<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Note extends Model
{
    use HasFactory;

    public const STATUT_BROUILLON = 'brouillon';
    public const STATUT_SOUMIS = 'soumis';
    public const STATUT_APPROUVE_PERSONNEL = 'approuve_personnel';
    public const STATUT_REJETE_PERSONNEL = 'rejete_personnel';
    public const STATUT_PUBLIE = 'publie';
    public const STATUT_REJETE_CLIENT = 'rejete_client';

    public const STATUTS = [
        self::STATUT_BROUILLON => 'Brouillon',
        self::STATUT_SOUMIS => 'En attente de validation',
        self::STATUT_APPROUVE_PERSONNEL => 'Vérifié (en attente client)',
        self::STATUT_REJETE_PERSONNEL => 'Rejeté par le personnel',
        self::STATUT_PUBLIE => 'Validé & Publié',
        self::STATUT_REJETE_CLIENT => 'Rejeté par le client',
    ];

    public const TYPE_INTERROGATION = 'interrogation';
    public const TYPE_DEVOIR = 'devoir';
    public const TYPE_COMPOSITION = 'composition';
    public const TYPE_AUTRE = 'autre';

    public const TYPES = [
        self::TYPE_INTERROGATION => 'Interrogation',
        self::TYPE_DEVOIR => 'Devoir sur table',
        self::TYPE_COMPOSITION => 'Composition',
        self::TYPE_AUTRE => 'Autre évaluation',
    ];

    protected $table = 'notes';

    protected $fillable = [
        'tenant_id',
        'etablissement_id',
        'eleve_id',
        'classe_id',
        'matiere_id',
        'enseignant_id',
        'titre_evaluation',
        'type_evaluation',
        'note',
        'periode',
        'annee_academique_id',
        'appreciation',
        'statut',
        'rejet_motif',
        'rejet_par',
        'soumis_le',
        'approuve_personnel_le',
        'approuve_personnel_id',
        'publie_le',
        'publie_par_id',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'etablissement_id' => 'integer',
        'eleve_id' => 'integer',
        'classe_id' => 'integer',
        'matiere_id' => 'integer',
        'enseignant_id' => 'integer',
        'annee_academique_id' => 'integer',
        'note' => 'float',
        'soumis_le' => 'datetime',
        'approuve_personnel_le' => 'datetime',
        'publie_le' => 'datetime',
    ];

    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class, 'eleve_id');
    }

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class, 'classe_id');
    }

    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class, 'matiere_id');
    }

    public function enseignant(): BelongsTo
    {
        return $this->belongsTo(Enseignant::class, 'enseignant_id');
    }

    public function anneeAcademique(): BelongsTo
    {
        return $this->belongsTo(AnneeAcademique::class, 'annee_academique_id');
    }

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class, 'etablissement_id');
    }

    public function historiques(): HasMany
    {
        return $this->hasMany(NoteHistorique::class, 'note_id');
    }

    public function isValidee(): bool
    {
        return in_array($this->statut, [self::STATUT_APPROUVE_PERSONNEL, self::STATUT_PUBLIE], true);
    }

    public function scopePublie(Builder $query): Builder
    {
        return $query->where('statut', self::STATUT_PUBLIE);
    }

    public function scopePourEleve(Builder $query, int $eleveId): Builder
    {
        return $query->where('eleve_id', $eleveId);
    }

    /**
     * Appréciation suggérée — source unique de vérité.
     */
    public function getAppreciationSuggereeAttribute(): string
    {
        return \App\Services\BulletinService::noteAppreciation($this->note);
    }

    public function getStatutLabelAttribute(): string
    {
        return self::STATUTS[$this->statut] ?? ucfirst($this->statut);
    }

    public function isPublie(): bool
    {
        return $this->statut === self::STATUT_PUBLIE;
    }

    public function isRejete(): bool
    {
        return in_array($this->statut, [self::STATUT_REJETE_PERSONNEL, self::STATUT_REJETE_CLIENT], true);
    }

    public function isModifiableParEnseignant(): bool
    {
        return true;
    }
}
