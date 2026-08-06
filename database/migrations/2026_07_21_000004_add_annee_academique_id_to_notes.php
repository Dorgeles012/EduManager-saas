<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute le rattachement des notes à l'année académique pour le filtre
     * "Année académique → Période → Matières".
     */
    public function up(): void
    {
        if (Schema::hasTable('notes') && ! Schema::hasColumn('notes', 'annee_academique_id')) {
            Schema::table('notes', function (Blueprint $table) {
                $table->unsignedBigInteger('annee_academique_id')->nullable()->after('periode');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('notes') && Schema::hasColumn('notes', 'annee_academique_id')) {
            Schema::table('notes', function (Blueprint $table) {
                $table->dropColumn('annee_academique_id');
            });
        }
    }
};
