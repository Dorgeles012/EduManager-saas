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

        // Si la colonne de liaison n'existe pas déjà, on l'ajoute.
        // On choisit subscription_type_id pour référencer subscription_types.id.
        if (!Schema::hasColumn('plans', 'subscription_type_id')) {
            Schema::table('plans', function (Blueprint $table) {
                $table->unsignedBigInteger('subscription_type_id')->nullable()->after('prix');

                // En MySQL, la FK nécessite les types identiques.
                // On met la contrainte seulement si la table subscription_types existe.
                if (Schema::hasTable('subscription_types')) {
                    $table->foreign('subscription_type_id')
                        ->references('id')
                        ->on('subscription_types')
                        ->onDelete('set null');
                }
            });
        }

        // Si la colonne legacy `type` existe encore, on la supprime (sinon la migration échoue).
        // Mais seulement si elle existe réellement.
        if (Schema::hasColumn('plans', 'type')) {
            Schema::table('plans', function (Blueprint $table) {
                // Attention : si des contraintes/index existent sur `type`, le drop peut échouer.
                // Dans ce repo on vise un fix minimal.
                $table->dropColumn('type');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('plans')) {
            return;
        }

        // Recréer la colonne legacy si nécessaire.
        if (!Schema::hasColumn('plans', 'type')) {
            Schema::table('plans', function (Blueprint $table) {
                $table->string('type')->nullable()->after('prix');
            });
        }

        // Retirer la FK colonne.
        if (Schema::hasColumn('plans', 'subscription_type_id')) {
            Schema::table('plans', function (Blueprint $table) {
                // dropForeign peut échouer si non existante; on protège en tentant seulement.
                $table->dropForeign(['subscription_type_id']);
                $table->dropColumn('subscription_type_id');
            });
        }
    }
};

