<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Certains setups ont déjà la table via une autre migration.
        // On ne la crée que si absente.
        if (!Schema::hasTable('serie_matieres')) {
            Schema::create('serie_matieres', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('serie_id');
                $table->unsignedBigInteger('matiere_id');
                $table->unsignedInteger('coefficient')->default(1);
                $table->timestamps();

                $table->unique(['serie_id', 'matiere_id']);

                // Contraintes FK (si supportées par le schéma existant)
                $table->foreign('serie_id')->references('id')->on('series')->cascadeOnDelete();
                $table->foreign('matiere_id')->references('id')->on('matieres')->cascadeOnDelete();

                $table->index(['matiere_id']);
                $table->index(['serie_id']);
            });
        }

        // Si table déjà existante, on ne modifie pas la structure (évite les conflits).
        // On hydrate juste les données manquantes si possible.


        // Backward compat: conserver anciennes associations matieres.serie si encore présentes
        if (Schema::hasColumn('matieres', 'serie') && Schema::hasTable('serie_matieres')) {
            DB::table('matieres')
                ->whereNotNull('serie')
                ->orderBy('id')
                ->chunkById(200, function ($matieres) {
                    foreach ($matieres as $matiere) {
                        $raw = $matiere->serie;
                        if ($raw === null) {
                            continue;
                        }

                        // Cas ancien: champ numérique (id série)
                        if (is_numeric($raw)) {
                            $serieId = (int) $raw;
                        } else {
                            $serieId = DB::table('series')
                                ->where('tenant_id', (int) $matiere->tenant_id)
                                ->where('nom_serie', trim((string) $raw))
                                ->value('id');
                        }

                        if (!$serieId) {
                            continue;
                        }

                        DB::table('serie_matieres')->insertOrIgnore([
                            'serie_id' => (int) $serieId,
                            'matiere_id' => (int) $matiere->id,
                            'coefficient' => max(1, (int) ($matiere->coefficient ?? 1)),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                });
        }

        // Assurer coefficient non nul
        if (Schema::hasTable('serie_matieres') && Schema::hasColumn('serie_matieres', 'coefficient')) {
            DB::table('serie_matieres')->whereNull('coefficient')->update(['coefficient' => 1]);
        }
    }

    public function down(): void
    {
        // down volontairement vide (ne pas détruire les données)
    }
};


