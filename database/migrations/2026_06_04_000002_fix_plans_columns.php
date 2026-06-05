<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('plans')) {
            return;
        }

        Schema::table('plans', function (Blueprint $table) {
            // S'assurer que les colonnes attendues existent avec des types cohérents
            if (Schema::hasColumn('plans', 'prix')) {
                // Convertir si jamais nécessaire (on garde l'existant)
            }

            if (Schema::hasColumn('plans', 'max_ecoles') && !Schema::hasColumn('plans', 'nombre_classes')) {
                // Aucun mapping automatique; on évite toute modification destructrice ici.
            }


            if (!Schema::hasColumn('plans', 'statut')) {
                $table->enum('statut', ['active', 'inactive'])->default('active');
            }

                // Dans cette passe, la table plans ne contient plus:
                // - duree, nombre_utilisateurs, nombre_enseignants, nombre_classes
                // On ne crée donc pas ces colonnes ici.


            if (!Schema::hasColumn('plans', 'description')) {
                $table->text('description')->nullable();
            }
        });
    }

    public function down(): void
    {
        // pas de rollback automatique
    }
};

