<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('versements') && ! Schema::hasColumn('versements', 'reference')) {
            Schema::table('versements', function (Blueprint $table) {
                $table->string('reference', 100)->nullable()->after('methode');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('versements') && Schema::hasColumn('versements', 'reference')) {
            Schema::table('versements', function (Blueprint $table) {
                $table->dropColumn('reference');
            });
        }
    }
};
