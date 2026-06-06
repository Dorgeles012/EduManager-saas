<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'client_id')) {
                // NOTE: on évite la contrainte FK ici.
                $table->foreignId('client_id')
                    ->nullable()
                    ->after('tenant_id');
            }

            // Indexes (on évite les doublons en vérifiant l'existence via Doctrine)
            // Les environnements de tests peuvent être SQLite (donc sans indexes FK spécifiques).
            // Ne pas ajouter d'index ici pour éviter les doublons lors des migrations répétées
            // (les migrations existantes créent parfois déjà ces indexes).



        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Reverse minimal (suppression des colonnes si elles existent).
            // En pratique, un rollback complet dépend de l'état initial de la DB.
            // Rollback volontairement minimal : ne touche pas aux contraintes/indices existants.
            // Les colonnes peuvent avoir été ajoutées et/ou référencées par d'autres migrations.


        });
    }

};
