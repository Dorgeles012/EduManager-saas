<?php

namespace App\Services;

class BulletinService
{
    /**
     * Returns the official mention and council observation for an average out of 20.
     */
    public function evaluation(?float $moyenne): array
    {
        if ($moyenne === null) {
            return ['mention' => null, 'observation' => null];
        }

        return match (true) {
            $moyenne >= 19 => ['mention' => 'Excellent', 'observation' => 'Travail exceptionnel. Résultats remarquables. Félicitations du jury. Continuez ainsi.'],
            $moyenne >= 18 => ['mention' => 'Excellent', 'observation' => 'Excellent travail. Très grande maîtrise des apprentissages. Toutes nos félicitations.'],
            $moyenne >= 17 => ['mention' => 'Très Bien', 'observation' => 'Très bon travail. Élève sérieux, appliqué et régulier. Félicitations.'],
            $moyenne >= 16 => ['mention' => 'Très Bien', 'observation' => 'Très bons résultats. Continuez vos efforts.'],
            $moyenne >= 15 => ['mention' => 'Bien', 'observation' => 'Bon travail. Ensemble satisfaisant. Encourageant pour la suite.'],
            $moyenne >= 14 => ['mention' => 'Assez Bien', 'observation' => 'Bons résultats. Quelques efforts supplémentaires permettront de progresser davantage.'],
            $moyenne >= 13 => ['mention' => 'Assez Bien', 'observation' => 'Travail satisfaisant. Continuez avec plus de régularité.'],
            $moyenne >= 12 => ['mention' => 'Passable', 'observation' => 'Résultats corrects. Des efforts sont encore attendus.'],
            $moyenne >= 11 => ['mention' => 'Passable', 'observation' => 'Ensemble acceptable mais irrégulier. Il faut travailler davantage.'],
            $moyenne >= 10 => ['mention' => 'Passable', 'observation' => 'Moyenne acquise de justesse. Les efforts doivent être poursuivis.'],
            $moyenne >= 9 => ['mention' => 'Insuffisant', 'observation' => 'Résultats insuffisants. Un travail plus sérieux est indispensable.'],
            $moyenne >= 8 => ['mention' => 'Faible', 'observation' => 'Travail faible. Il est nécessaire de fournir davantage d’efforts.'],
            $moyenne >= 7 => ['mention' => 'Très Faible', 'observation' => 'Résultats très insuffisants. Réaction rapide attendue.'],
            $moyenne >= 5 => ['mention' => 'Très Faible', 'observation' => 'Grandes difficultés. Travail insuffisant. Beaucoup plus d’investissement est nécessaire.'],
            default => ['mention' => 'Très Insuffisant', 'observation' => 'Résultats très préoccupants. Une remise au travail est indispensable.'],
        };
    }

    public function decision(?float $moyenne): ?string
    {
        return $moyenne === null ? null : ($moyenne >= 10 ? 'Admis(e)' : 'Refusé(e)');
    }
}
