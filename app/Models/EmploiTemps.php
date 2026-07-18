<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class EmploiTemps extends Model {
 protected $table = 'emploi_temps';
 protected $fillable = ['tenant_id','etablissement_id','annee_academique_id','classe_id','serie_id','matiere_id','enseignant_id','jour','heure_debut','heure_fin','slot_key','salle'];
 public function classe(): BelongsTo { return $this->belongsTo(Classe::class); }
 public function serie(): BelongsTo { return $this->belongsTo(Series::class, 'serie_id'); }
 public function matiere(): BelongsTo { return $this->belongsTo(Matiere::class); }
 public function enseignant(): BelongsTo { return $this->belongsTo(Enseignant::class); }
 public function anneeAcademique(): BelongsTo { return $this->belongsTo(AnneeAcademique::class, 'annee_academique_id'); }
 public function etablissement(): BelongsTo { return $this->belongsTo(Etablissement::class); }
}
