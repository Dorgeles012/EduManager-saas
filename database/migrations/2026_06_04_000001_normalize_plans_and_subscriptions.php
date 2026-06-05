<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Normalisation TABLE plans -> structure cible
        if (Schema::hasTable('plans')) {
            Schema::table('plans', function (Blueprint $table) {
                // Ajouter colonnes manquantes (si besoin)
                if (!Schema::hasColumn('plans', 'duree')) {
                    $table->integer('duree')->default(12);
                }
                if (!Schema::hasColumn('plans', 'nombre_utilisateurs')) {
                    $table->integer('nombre_utilisateurs')->default(10);
                }
                if (!Schema::hasColumn('plans', 'nombre_enseignants')) {
                    $table->integer('nombre_enseignants')->default(10);
                }
                if (!Schema::hasColumn('plans', 'nombre_classes')) {
                    $table->integer('nombre_classes')->default(10);
                }
                if (!Schema::hasColumn('plans', 'statut')) {
                    $table->enum('statut', ['active', 'inactive'])->default('active');
                }

                // Garder compat pour anciennes colonnes si elles existent
                // (On ne les supprime pas ici pour éviter cassure sur historique)
                // $table->dropColumn('max_ecoles'); etc. -> laissé volontairement.
            });
        }

        // Normalisation TABLE subscriptions -> structure cible
        if (Schema::hasTable('subscriptions')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                // Ajouter colonnes manquantes
                if (!Schema::hasColumn('subscriptions', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable();
                }

                // S'assurer que plan_id existe (structure cible)
                if (!Schema::hasColumn('subscriptions', 'plan_id')) {
                    $table->unsignedBigInteger('plan_id')->nullable();
                }

                // date_debut/date_fin existent déjà dans ton schéma initial
                if (!Schema::hasColumn('subscriptions', 'date_debut')) {
                    $table->date('date_debut')->nullable();
                }
                if (!Schema::hasColumn('subscriptions', 'date_fin')) {
                    $table->date('date_fin')->nullable();
                }

                if (!Schema::hasColumn('subscriptions', 'statut')) {
                    $table->enum('statut', ['active', 'expired', 'cancelled'])->default('active');
                }

                // Colonnes ajoutées par l'ancien module subscription "plans-like".
                // IMPORTANT: sur certains schémas, des index/keys existent sans que la colonne existe.
                // Pour éviter les erreurs, on ne supprime rien de ces colonnes ici :
                // la séparation logique est assurée par le code (fillable + contrôleur + relations).
                $colsToDrop = [];



                // IMPORTANT: on peut avoir des index/keys uniques faits sur ces colonnes.
                // On tente donc de supprimer les colonnes seulement si elles existent
                // ET si aucun index n'empêche l'opération.
                foreach ($colsToDrop as $col) {
                    if (Schema::hasColumn('subscriptions', $col)) {
                        // On drop uniquement si la colonne existe réellement.
                        $table->dropColumn($col);
                    }
                }

                // Sécurité supplémentaire : certaines versions de schéma ont des clés/indices
                // liées à des colonnes déjà absentes (selon migrations précédentes). 
                // Dans ce cas, l'ALTER échoue. On ne tente donc pas d'agir plus ici.


            });


            // Si on a drop des colonnes, on peut avoir des index/constraints incohérents selon prod.
            // Ici on suppose un environnement dev.
        }
    }

    public function down(): void
    {
        // On ne tente pas de restaurer l'ancien schéma mélangé automatiquement.
        // Ceci évite des erreurs lors du rollback.
    }
};

