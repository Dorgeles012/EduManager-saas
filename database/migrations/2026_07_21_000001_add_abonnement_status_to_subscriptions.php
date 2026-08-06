<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute le flux de validation d'abonnement (en_attente → payé → actif → expiré).
     */
    public function up(): void
    {
        if (Schema::hasTable('subscriptions') && ! Schema::hasColumn('subscriptions', 'abonnement_status')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->enum('abonnement_status', ['en_attente', 'paye', 'actif', 'expire'])
                    ->default('en_attente')
                    ->after('statut');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('subscriptions') && Schema::hasColumn('subscriptions', 'abonnement_status')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->dropColumn('abonnement_status');
            });
        }
    }
};
