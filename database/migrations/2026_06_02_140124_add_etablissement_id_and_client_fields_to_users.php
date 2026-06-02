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
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'etablissement_id')) {
                $table->foreignId('etablissement_id')
                    ->nullable()
                    ->after('tenant_id')
                    ->constrained('etablissements')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('users', 'adresse')) {
                $table->string('adresse', 255)->nullable()->after('telephone');
            }

            if (! Schema::hasColumn('users', 'ville')) {
                $table->string('ville', 100)->nullable()->after('adresse');
            }

            // La vue et FormData utilisent l'ancien champ `photo`.
            // Dans `users` on stocke réellement dans `image`, mais on ajoute un alias `photo`
            // pour éviter de casser le CRUD.
            if (! Schema::hasColumn('users', 'photo')) {
                $table->string('photo', 255)->nullable()->after('image');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
