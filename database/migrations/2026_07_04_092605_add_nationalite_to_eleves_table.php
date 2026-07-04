<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('eleves') && ! Schema::hasColumn('eleves', 'nationalite')) {
            Schema::table('eleves', function (Blueprint $table) {
                $table->string('nationalite')->nullable()->after('lieu_naissance');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('eleves') && Schema::hasColumn('eleves', 'nationalite')) {
            Schema::table('eleves', function (Blueprint $table) {
                $table->dropColumn('nationalite');
            });
        }
    }
};
