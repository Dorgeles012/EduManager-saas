<?php

namespace App\Services;

use App\Models\Versement;
use Illuminate\Support\Str;

/**
 * Service centralisé pour les paiements de scolarité.
 *
 * Architecture extensible : chaque moyen de paiement (Orange Money, MTN Money,
 * Moov Money, Wave, Espèces) pourra être branché sur une API externe plus tard
 * sans modifier les contrôleurs.
 */
class PaymentProviderService
{
    /**
     * Moyens de paiement disponibles.
     */
    public const METHODS = [
        'orange_money' => 'Orange Money',
        'mtn_money' => 'MTN Money',
        'moov_money' => 'Moov Money',
        'wave' => 'Wave',
        'especes' => 'Espèces',
    ];

    /**
     * Enregistre un versement (transaction locale).
     *
     * @param  array  $data  [
     *                       'tenant_id', 'scolarite_id', 'montant', 'date', 'methode'
     *                       ]
     * @return Versement
     */
    public function charge(array $data): Versement
    {
        $methode = $data['methode'] ?? 'especes';

        // Ici, on pourra appeler les API de paiement mobile selon $methode
        // (Orange/MTN/Moov/Wave) avant de confirmer le versement.
        $this->executeProvider($methode, $data['montant']);

        return Versement::create([
            'tenant_id' => $data['tenant_id'],
            'scolarite_id' => $data['scolarite_id'],
            'montant' => $data['montant'],
            'date_versement' => $data['date'],
            'methode' => $this->label($methode),
            'reference' => $this->generateReference(),
        ]);
    }

    /**
     * Point d'extension pour brancher les API de paiement mobile.
     */
    protected function executeProvider(string $methode, int $montant): void
    {
        // TODO: intégration réelle des API (Orange Money, MTN MoMo, Moov, Wave)
        // selon la clé $methode.
    }

    /**
     * Génère une référence unique de transaction.
     */
    protected function generateReference(): string
    {
        return 'PAY-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6));
    }

    /**
     * Convertit une clé de méthode en libellé lisible.
     */
    public function label(string $key): string
    {
        return self::METHODS[$key] ?? Str::title(str_replace('_', ' ', $key));
    }
}
