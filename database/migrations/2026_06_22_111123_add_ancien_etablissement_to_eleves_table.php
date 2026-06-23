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
        Schema::table('eleves', function (Blueprint $table) {
            if (! Schema::hasColumn('eleves', 'ancien_etablissement')) {
                $table->string('ancien_etablissement')->nullable()->after('lieu_naissance');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eleves', function (Blueprint $table) {
            if (Schema::hasColumn('eleves', 'ancien_etablissement')) {
                $table->dropColumn('ancien_etablissement');
            }
        });
    }
};
