<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('matieres')) {
            return;
        }

        if (!Schema::hasColumn('matieres', 'serie')) {
            Schema::table('matieres', function (Blueprint $table) {
                $table->unsignedBigInteger('serie')->nullable()->after('coefficient');
            });
        }

        if (Schema::hasColumn('matieres', 'serie') && Schema::hasTable('series')) {
            try {
                Schema::table('matieres', function (Blueprint $table) {
                    $table->foreign('serie')->references('id')->on('series')->onDelete('restrict');
                });
            } catch (\Throwable $e) {
                // Ignore if the foreign key already exists or cannot be added.
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('matieres') || !Schema::hasColumn('matieres', 'serie')) {
            return;
        }

        Schema::table('matieres', function (Blueprint $table) {
            try {
                $table->dropForeign(['serie']);
            } catch (\Throwable $e) {
                // Ignore if foreign key does not exist.
            }

            $table->dropColumn('serie');
        });
    }
};
