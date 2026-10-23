<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('versements')) {
            return;
        }

        Schema::table('versements', function (Blueprint $table) {
            if (! Schema::hasColumn('versements', 'type')) {
                $table->string('type', 30)->nullable()->after('montant');
            }
            if (! Schema::hasColumn('versements', 'statut')) {
                // Les écritures historiques n'ont pas d'état d'encaissement vérifiable.
                // Elles restent nulles jusqu'à leur vérification manuelle.
                $table->string('statut', 30)->nullable()->after('reference');
            }
            if (! Schema::hasColumn('versements', 'enregistre_par')) {
                $table->unsignedBigInteger('enregistre_par')->nullable()->after('statut');
            }
            if (! Schema::hasColumn('versements', 'idempotency_key')) {
                $table->string('idempotency_key', 64)->nullable()->unique()->after('enregistre_par');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('versements')) {
            return;
        }

        Schema::table('versements', function (Blueprint $table) {
            if (Schema::hasColumn('versements', 'idempotency_key')) {
                $table->dropUnique(['idempotency_key']);
            }
            foreach (['idempotency_key', 'enregistre_par', 'statut', 'type'] as $column) {
                if (Schema::hasColumn('versements', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
