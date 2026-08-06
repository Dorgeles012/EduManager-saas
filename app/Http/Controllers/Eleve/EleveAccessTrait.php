<?php

namespace App\Http\Controllers\Eleve;

use App\Models\Eleve;

trait EleveAccessTrait
{
    /**
     * Récupère l'élève lié au compte de l'utilisateur connecté (rôle élève).
     * Vérifie le tenant et le lien eleve_id.
     */
    protected function currentEleve(bool $abortIfMissing = true): ?Eleve
    {
        $user = auth()->user();

        $eleve = Eleve::with(['classe.niveau', 'niveau', 'serie', 'etablissement'])
            ->where('tenant_id', $user->tenant_id)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->where('id', $user->eleve_id)
            ->first();

        if (! $eleve && $abortIfMissing) {
            abort(403, 'Profil élève introuvable.');
        }

        return $eleve;
    }
}
