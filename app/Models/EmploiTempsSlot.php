<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmploiTempsSlot extends Model
{
    protected $table = 'emploi_temps_slots';

    protected $fillable = [
        'tenant_id',
        'enseignant_id',
        'slot_key',
        'heure_debut',
        'heure_fin',
    ];

    protected function casts(): array
    {
        return [
            'heure_debut' => 'string',
            'heure_fin'   => 'string',
        ];
    }

    /* ------------------------------------------------------------------
     * Relations
     * ------------------------------------------------------------------ */

    public function enseignant(): BelongsTo
    {
        return $this->belongsTo(Enseignant::class);
    }

    /**
     * Chaque créneau appartient à un enseignant unique via slot_key.
     * On peut aussi le lier aux entrées d'emploi_temps par la même clé.
     */
    public function emploiTempsEntries()
    {
        return $this->hasMany(EmploiTemps::class, 'slot_key', 'slot_key')
            ->whereColumn('emploi_temps.enseignant_id', 'emploi_temps_slots.enseignant_id')
            ->whereColumn('emploi_temps.tenant_id', 'emploi_temps_slots.tenant_id');
    }
}

