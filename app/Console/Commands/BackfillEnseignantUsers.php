<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackfillEnseignantUsers extends Command
{
    protected $signature = 'enseignants:backfill-users';
    protected $description = 'Commande désactivée : les enseignants ne sont plus créés dans users';

    public function handle(): int
    {
        $this->error('Aucun compte utilisateur n’a été créé. Utilisez la commande d’audit des anciens comptes avant toute migration.');

        return Command::FAILURE;
    }
}
