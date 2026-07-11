<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('bulletin_discipline') && Schema::hasColumn('bulletin_discipline', 'appréciation')) {
            Schema::table('bulletin_discipline', fn (Blueprint $table) => $table->renameColumn('appréciation', 'mention'));
        }

        if (Schema::hasTable('bulletins')) {
            if (Schema::hasColumn('bulletins', 'moyenne') && ! Schema::hasColumn('bulletins', 'moyenne_generale')) {
                Schema::table('bulletins', fn (Blueprint $table) => $table->renameColumn('moyenne', 'moyenne_generale'));
            }

            $columns = [
                'etablissement_id' => fn (Blueprint $table) => $table->unsignedBigInteger('etablissement_id')->nullable(),
                'annee_academique_id' => fn (Blueprint $table) => $table->unsignedBigInteger('annee_academique_id')->nullable(),
                'total_heures' => fn (Blueprint $table) => $table->decimal('total_heures', 8, 2)->default(0),
                'absences' => fn (Blueprint $table) => $table->unsignedInteger('absences')->default(0),
                'total_coefficients' => fn (Blueprint $table) => $table->decimal('total_coefficients', 8, 2)->default(0),
                'total_points' => fn (Blueprint $table) => $table->decimal('total_points', 10, 2)->default(0),
                'resultat_classe' => fn (Blueprint $table) => $table->string('resultat_classe')->nullable(),
                'decision' => fn (Blueprint $table) => $table->string('decision')->nullable(),
                'observation_conseil' => fn (Blueprint $table) => $table->text('observation_conseil')->nullable(),
                'date' => fn (Blueprint $table) => $table->date('date')->nullable(),
                'signature_professeur_principal' => fn (Blueprint $table) => $table->string('signature_professeur_principal')->nullable(),
                'signature_directeur' => fn (Blueprint $table) => $table->string('signature_directeur')->nullable(),
                'distinctions' => fn (Blueprint $table) => $table->json('distinctions')->nullable(),
                'mention' => fn (Blueprint $table) => $table->string('mention')->nullable(),
            ];

            foreach ($columns as $column => $definition) {
                if (! Schema::hasColumn('bulletins', $column)) {
                    Schema::table('bulletins', $definition);
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('bulletins') && Schema::hasColumn('bulletins', 'mention')) {
            Schema::table('bulletins', fn (Blueprint $table) => $table->dropColumn('mention'));
        }

        if (Schema::hasTable('bulletin_discipline') && Schema::hasColumn('bulletin_discipline', 'mention')) {
            Schema::table('bulletin_discipline', fn (Blueprint $table) => $table->renameColumn('mention', 'appréciation'));
        }
    }
};
