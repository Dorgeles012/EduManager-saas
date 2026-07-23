<?php

namespace App\Console\Commands;

use App\Models\Enseignant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class BackfillEnseignantUsers extends Command
{
    protected $signature = 'enseignants:backfill-users';
    protected $description = 'Crée un compte User pour chaque enseignant existant sans user_id';

    public function handle(): int
    {
        $enseignants = Enseignant::whereNull('user_id')->get();

        if ($enseignants->isEmpty()) {
            $this->info('Tous les enseignants ont déjà un user_id. Aucune action nécessaire.');
            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($enseignants->count());
        $bar->start();

        foreach ($enseignants as $enseignant) {
            // Éviter les doublons : vérifier si un User avec cet email existe déjà
            $existingUser = User::where('email', $enseignant->email)->first();
            if ($existingUser) {
                $enseignant->update(['user_id' => $existingUser->id]);
                $bar->advance();
                continue;
            }

            // Créer le User
            $user = User::create([
                'tenant_id' => $enseignant->tenant_id,
                'etablissement_id' => $enseignant->etablissement_id,
                'nom' => $enseignant->nom,
                'prenom' => $enseignant->prenoms,
                'email' => $enseignant->email,
                'telephone' => $enseignant->telephone,
                'password' => Hash::make('12345678'),
                'role' => 'enseignant',
                'statut' => 'actif',
            ]);

            $enseignant->update(['user_id' => $user->id]);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info($enseignants->count() . ' compte(s) User créé(s) avec succès pour les enseignants.');
        $this->warn('Mot de passe par défaut pour tous les enseignants : 12345678');

        return Command::SUCCESS;
    }
}

