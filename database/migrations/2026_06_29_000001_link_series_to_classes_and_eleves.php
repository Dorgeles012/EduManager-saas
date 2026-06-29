<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('series') && ! Schema::hasColumn('series', 'id_classe')) {
            Schema::table('series', function (Blueprint $table) {
                $table->foreignId('id_classe')
                    ->nullable()
                    ->after('tenant_id')
                    ->constrained('classes')
                    ->nullOnDelete();
            });
        }

        if (Schema::hasTable('eleves') && ! Schema::hasColumn('eleves', 'id_serie')) {
            Schema::table('eleves', function (Blueprint $table) {
                $table->foreignId('id_serie')
                    ->nullable()
                    ->after('classe_id')
                    ->constrained('series')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('eleves') && Schema::hasColumn('eleves', 'id_serie')) {
            Schema::table('eleves', function (Blueprint $table) {
                $table->dropConstrainedForeignId('id_serie');
            });
        }

        if (Schema::hasTable('series') && Schema::hasColumn('series', 'id_classe')) {
            Schema::table('series', function (Blueprint $table) {
                $table->dropConstrainedForeignId('id_classe');
            });
        }
    }
};
