<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ajoute la colonne SoftDeletes (et ne re-crée pas la table).
        Schema::table('etablissements', function (Blueprint $table) {
            if (! Schema::hasColumn('etablissements', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('etablissements', function (Blueprint $table) {
            if (Schema::hasColumn('etablissements', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};

