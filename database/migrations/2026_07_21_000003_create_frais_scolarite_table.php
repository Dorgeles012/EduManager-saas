<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table de configuration des frais de scolarité par niveau.
     */
    public function up(): void
    {
        if (! Schema::hasTable('frais_scolarite')) {
            Schema::create('frais_scolarite', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id');
                $table->unsignedBigInteger('etablissement_id')->nullable();
                $table->unsignedBigInteger('niveau_id');
                $table->unsignedBigInteger('annee_academique_id')->nullable();
                $table->integer('inscription')->default(0);
                $table->integer('scolarite')->default(0);
                $table->integer('autres_frais')->default(0);
                $table->timestamps();

                $table->unique(['tenant_id', 'niveau_id', 'annee_academique_id'], 'frais_niveau_annee_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('frais_scolarite');
    }
};
