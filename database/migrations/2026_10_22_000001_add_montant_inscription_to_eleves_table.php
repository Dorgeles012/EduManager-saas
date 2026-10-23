<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('eleves') && ! Schema::hasColumn('eleves', 'montant_inscription')) {
            Schema::table('eleves', function (Blueprint $table) {
                $table->unsignedInteger('montant_inscription')->default(0)->after('statut');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('eleves') && Schema::hasColumn('eleves', 'montant_inscription')) {
            Schema::table('eleves', function (Blueprint $table) {
                $table->dropColumn('montant_inscription');
            });
        }
    }
};
