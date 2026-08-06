<?php

namespace App\Http\Controllers\Parent;

use App\Models\Eleve;

/**
 * Trait partagé pour vérifier que l'élève appartient bien au parent connecté.
 * Toutes les requêtes des contrôleurs Parent doivent passer par childBelongsToParent().
 */
trait ParentAccessTrait
{
    /**
     * Vérifie que l'élève appartient au parent connecté (et au bon tenant).
     *
     * @return Eleve
     */
    protected function childBelongsToParent(Eleve $eleve): Eleve
    {
        $user = auth()->user();

        abort_unless(
            (int) $eleve->parent_id === (int) $user->id
            && (int) $eleve->tenant_id === (int) $user->tenant_id,
            403,
            'Accès interdit : cet enfant ne vous est pas affilié.'
        );

        return $eleve;
    }
}
