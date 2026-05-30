<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (! Schema::hasColumn('clients', 'etablissement_id')) {
                $table->foreignId('etablissement_id')
                    ->nullable()
                    ->after('ville')
                    ->constrained('etablissements')
                    ->cascadeOnUpdate()
                    ->nullOnDelete();
            }
        });

        Schema::table('clients', function (Blueprint $table) {
            if (Schema::hasColumn('clients', 'entreprise')) {
                $table->dropColumn('entreprise');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (! Schema::hasColumn('clients', 'entreprise')) {
                $table->string('entreprise', 150)->nullable()->after('ville');
            }
        });

        Schema::table('clients', function (Blueprint $table) {
            if (Schema::hasColumn('clients', 'etablissement_id')) {
                $table->dropConstrainedForeignId('etablissement_id');
            }
        });
    }
};
