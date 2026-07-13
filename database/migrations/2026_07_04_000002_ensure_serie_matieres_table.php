<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // La création est centralisée dans la migration précédente. Celle-ci
        // conserve uniquement la synchronisation des associations historiques.
        if (! Schema::hasTable('serie_matieres')) {
            return;
        }

        if (Schema::hasColumn('matieres', 'serie')) {
            DB::table('matieres')
                ->whereNotNull('serie')
                ->orderBy('id')
                ->chunkById(200, function ($matieres): void {
                    foreach ($matieres as $matiere) {
                        $raw = $matiere->serie;

                        if ($raw === null) {
                            continue;
                        }

                        $serieId = is_numeric($raw)
                            ? (int) $raw
                            : DB::table('series')
                                ->where('tenant_id', (int) $matiere->tenant_id)
                                ->where('nom_serie', trim((string) $raw))
                                ->value('id');

                        if (! $serieId) {
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

        if (Schema::hasColumn('serie_matieres', 'coefficient')) {
            DB::table('serie_matieres')->whereNull('coefficient')->update(['coefficient' => 1]);
        }
    }

    public function down(): void
    {
        // Ne jamais supprimer les associations existantes.
    }
};
