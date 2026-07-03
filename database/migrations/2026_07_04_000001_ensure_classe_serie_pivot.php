<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('classe_serie')) {
            Schema::create('classe_serie', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('serie_id');
                $table->unsignedBigInteger('classe_id');
                $table->timestamps();

                $table->unique(['serie_id', 'classe_id']);

                $table->foreign('serie_id')->references('id')->on('series')->cascadeOnDelete();
                $table->foreign('classe_id')->references('id')->on('classes')->cascadeOnDelete();

                $table->index(['classe_id']);
                $table->index(['serie_id']);
            });
        }

        // Backward compat: si series.id_classe existe encore, on hydrate la pivot
        if (Schema::hasColumn('series', 'id_classe') && Schema::hasTable('classe_serie')) {
            $now = now();
            DB::table('series')
                ->whereNotNull('id_classe')
                ->whereNotNull('id')
                ->select('id', 'id_classe', 'tenant_id')
                ->chunkById(200, function ($series) use ($now) {
                    foreach ($series as $serie) {
                        DB::table('classe_serie')->insertOrIgnore([
                            'serie_id' => (int) $serie->id,
                            'classe_id' => (int) $serie->id_classe,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }
                });
        }
    }

    public function down(): void
    {
        // Ne jamais détruire par défaut en prod : garder les données.
        // Le down est volontairement vide.
    }
};

