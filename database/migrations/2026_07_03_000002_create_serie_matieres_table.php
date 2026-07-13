<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Certaines installations ont déjà cette table, sans trace de cette
        // migration dans `migrations`. Ne jamais la recréer dans ce cas.
        if (! Schema::hasTable('serie_matieres')) {
            Schema::create('serie_matieres', function (Blueprint $table) {
                $table->id();
                $table->foreignId('serie_id')->constrained('series')->cascadeOnDelete();
                $table->foreignId('matiere_id')->constrained('matieres')->cascadeOnDelete();
                $table->unsignedInteger('coefficient')->default(1);
                $table->timestamps();

                $table->unique(['serie_id', 'matiere_id']);
            });
        }

        // Conserve les associations de l'ancien schéma matieres.serie.
        if (Schema::hasColumn('matieres', 'serie')) {
            DB::table('matieres')
                ->whereNotNull('serie')
                ->orderBy('id')
                ->each(function ($matiere): void {
                    $serieId = is_numeric($matiere->serie)
                        ? (int) $matiere->serie
                        : DB::table('series')
                            ->where('tenant_id', $matiere->tenant_id)
                            ->where('nom_serie', $matiere->serie)
                            ->value('id');

                    if ($serieId && DB::table('series')->where('id', $serieId)->where('tenant_id', $matiere->tenant_id)->exists()) {
                        DB::table('serie_matieres')->insertOrIgnore([
                            'serie_id' => $serieId,
                            'matiere_id' => $matiere->id,
                            'coefficient' => max(1, (int) ($matiere->coefficient ?? 1)),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                });
        }

        if (Schema::hasTable('bulletin_discipline')) {
            if (! Schema::hasColumn('bulletin_discipline', 'matiere_id')) {
                Schema::table('bulletin_discipline', function (Blueprint $table) {
                    $table->foreignId('matiere_id')->nullable()->after('bulletin_id')
                        ->constrained('matieres')->nullOnDelete();
                });
            }

            if (! Schema::hasColumn('bulletin_discipline', 'interrogation')) {
                Schema::table('bulletin_discipline', function (Blueprint $table) {
                    $table->decimal('interrogation', 5, 2)->nullable()->after('discipline');
                });
            }

            if (! Schema::hasColumn('bulletin_discipline', 'devoir')) {
                Schema::table('bulletin_discipline', function (Blueprint $table) {
                    $table->decimal('devoir', 5, 2)->nullable()->after('interrogation');
                });
            }

            if (! Schema::hasColumn('bulletin_discipline', 'composition')) {
                Schema::table('bulletin_discipline', function (Blueprint $table) {
                    $table->decimal('composition', 5, 2)->nullable()->after('devoir');
                });
            }
        }

        if (Schema::hasTable('bulletins') && ! Schema::hasColumn('bulletins', 'total_coefficients')) {
            Schema::table('bulletins', function (Blueprint $table) {
                $table->decimal('total_coefficients', 8, 2)->default(0)->after('moyenne_generale');
            });
        }

        if (Schema::hasTable('bulletins') && ! Schema::hasColumn('bulletins', 'total_points')) {
            Schema::table('bulletins', function (Blueprint $table) {
                $table->decimal('total_points', 10, 2)->default(0)->after('total_coefficients');
            });
        }
    }

    public function down(): void
    {
        // Migration volontairement irréversible : ces structures peuvent être
        // antérieures à cette migration et contiennent potentiellement des données.
    }
};
