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

                // Foreign keys (soft-fail if DB already has constraints differences)
                $table->foreign('serie_id')->references('id')->on('series')->cascadeOnDelete();
                $table->foreign('classe_id')->references('id')->on('classes')->cascadeOnDelete();
            });
        }

        if (Schema::hasColumn('series', 'id_classe')) {
            $now = now();
            DB::table('series')
                ->whereNotNull('id_classe')
                ->select('id', 'id_classe')
                ->orderBy('id')
                ->each(function ($serie) use ($now) {
                    DB::table('classe_serie')->insertOrIgnore([
                        'serie_id' => $serie->id,
                        'classe_id' => $serie->id_classe,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('classe_serie');
    }
};

