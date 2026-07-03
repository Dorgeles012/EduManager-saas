<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('serie_matieres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('serie_id')->constrained('series')->cascadeOnDelete();
            $table->foreignId('matiere_id')->constrained('matieres')->cascadeOnDelete();
            $table->unsignedInteger('coefficient')->default(1);
            $table->timestamps();

            $table->unique(['serie_id', 'matiere_id']);
        });

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

        Schema::table('bulletin_discipline', function (Blueprint $table) {
            $table->foreignId('matiere_id')->nullable()->after('bulletin_id')
                ->constrained('matieres')->nullOnDelete();
            $table->decimal('interrogation', 5, 2)->nullable()->after('discipline');
            $table->decimal('devoir', 5, 2)->nullable()->after('interrogation');
            $table->decimal('composition', 5, 2)->nullable()->after('devoir');
        });

        Schema::table('bulletins', function (Blueprint $table) {
            $table->decimal('total_coefficients', 8, 2)->default(0)->after('moyenne_generale');
            $table->decimal('total_points', 10, 2)->default(0)->after('total_coefficients');
        });
    }

    public function down(): void
    {
        Schema::table('bulletins', function (Blueprint $table) {
            $table->dropColumn(['total_coefficients', 'total_points']);
        });
        Schema::table('bulletin_discipline', function (Blueprint $table) {
            $table->dropConstrainedForeignId('matiere_id');
            $table->dropColumn(['interrogation', 'devoir', 'composition']);
        });
        Schema::dropIfExists('serie_matieres');
    }
};
