<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('eleves') && !Schema::hasColumn('eleves', 'niveau_id')) {
            Schema::table('eleves', function (Blueprint $table) {
                $table->unsignedBigInteger('niveau_id')->nullable()->after('classe_id');
            });
        }

        if (Schema::hasTable('enseignants') && !Schema::hasColumn('enseignants', 'matiere_id')) {
            Schema::table('enseignants', function (Blueprint $table) {
                $table->unsignedBigInteger('matiere_id')->nullable()->after('password');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('enseignants') && Schema::hasColumn('enseignants', 'matiere_id')) {
            Schema::table('enseignants', function (Blueprint $table) {
                $table->dropColumn('matiere_id');
            });
        }

        if (Schema::hasTable('eleves') && Schema::hasColumn('eleves', 'niveau_id')) {
            Schema::table('eleves', function (Blueprint $table) {
                $table->dropColumn('niveau_id');
            });
        }
    }
};
