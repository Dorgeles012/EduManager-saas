<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Détecter si la colonne `serie` existe encore en texte.
        // On va tenter de la convertir en FK (unsignedBigInteger).
        // NOTE: Si ta DB utilise déjà un type numérique, cette migration restera 
        // globalement safe car les ALTER vont matcher.

        if (!Schema::hasColumn('matieres', 'serie')) {
            // Rien à faire.
            return;
        }

        DB::transaction(function () {
            // 2) Créer les séries manquantes à partir des anciennes valeurs texte (ex: A1, B...)
            // On normalise le texte en le considérant comme `nom_serie`.
            $oldValues = DB::table('matieres')
                ->select('tenant_id', 'serie')
                ->whereNotNull('serie')
                ->whereRaw('TRIM(CAST(serie AS CHAR)) <> ""')
                ->get();

            foreach ($oldValues as $row) {
                $tenantId = (int) $row->tenant_id;
                $nomSerie = trim((string) $row->serie);

                if ($nomSerie === '') {
                    continue;
                }

                DB::table('series')->updateOrInsert(
                    ['tenant_id' => $tenantId, 'nom_serie' => $nomSerie],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }

            // 3) Convertir `matieres.serie` (texte) -> `matieres.serie` (FK vers series.id)
            // On remplace chaque valeur par l'id correspondant.
            // Pour éviter les soucis de type, on update par jointure implicite.
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // On modifie le type de colonne.
            // MySQL: MODIFY COLUMN. Pour compatibilité, on utilise alter table.
            Schema::table('matieres', function (Blueprint $table) {
                $table->unsignedBigInteger('serie')->nullable()->change();
            });

            // Fill serie (FK)
            $rows = DB::table('matieres')->select('id', 'tenant_id', 'serie')->get();
            foreach ($rows as $m) {
                $tenantId = (int) $m->tenant_id;
                $old = $m->serie;
                if ($old === null) {
                    continue;
                }
                $nomSerie = trim((string) $old);
                if ($nomSerie === '') {
                    continue;
                }

                $seriesId = DB::table('series')
                    ->where('tenant_id', $tenantId)
                    ->where('nom_serie', $nomSerie)
                    ->value('id');

                if ($seriesId) {
                    DB::table('matieres')->where('id', $m->id)->update(['serie' => (int) $seriesId]);
                }
            }

            // 4) Ajouter la contrainte FK (optionnel mais propre)
            Schema::table('matieres', function (Blueprint $table) {
                // Si une contrainte existe déjà, la commande peut échouer.
                // On privilégie donc la simple tentative via try/catch.
                try {
                    $table->foreign('serie')->references('id')->on('series')->onDelete('restrict');
                } catch (\Throwable $e) {
                    // ignore
                }
            });

            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        });
    }

    public function down(): void
    {
        // Retour arrière complexe (conversion inverse). On laisse down vide de manière intentionnelle.
    }
};

