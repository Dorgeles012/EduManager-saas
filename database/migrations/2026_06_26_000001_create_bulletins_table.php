<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('bulletins')) {
            return;
        }
        Schema::create('bulletins', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->unsignedBigInteger('etablissement_id');
            $table->unsignedBigInteger('annee_academique_id');
            $table->unsignedBigInteger('eleve_id');
            $table->unsignedBigInteger('classe_id');

            $table->string('trimestre', 10);

            $table->decimal('total_heures', 8, 2)->default(0);
            $table->unsignedInteger('absences')->default(0);
            $table->unsignedInteger('rang')->default(0);
            $table->decimal('moyenne_generale', 6, 2)->nullable();

            $table->string('resultat_classe')->nullable();
            $table->string('decision')->nullable();
            $table->string('observation_conseil')->nullable();

            $table->date('date')->nullable();

            $table->string('signature_professeur_principal')->nullable();
            $table->string('signature_directeur')->nullable();

            // Distinctions stockées en tableau JSON (simple et rapide)
            $table->json('distinctions')->nullable();

            $table->timestamps();

            $table->index(['tenant_id', 'etablissement_id', 'annee_academique_id']);
            $table->index(['eleve_id', 'classe_id', 'trimestre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bulletins');
    }
};

