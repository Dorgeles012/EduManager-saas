<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Ajoute une clé de compatibilité en lecture seule pour auditer les anciens comptes élèves. */
    public function up(): void
    {
        // 1. Colonne eleve_id (nullable) pour lier un compte élève à un élève
        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'eleve_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('eleve_id')->nullable()->after('client_id');
            });
        }

        if (! Schema::hasTable('users')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $legacyCount = DB::table('users')
                ->whereRaw('UPPER(TRIM(role)) IN (?, ?)', ['ELEVE', 'ENSEIGNANT'])
                ->count();

            if ($legacyCount > 0) {
                throw new RuntimeException("{$legacyCount} ancien(s) compte(s) élève/enseignant dans users. Exécutez users:audit-legacy-school-accounts et résolvez les comptes avant de réduire l'ENUM.");
            }

            DB::statement("ALTER TABLE users MODIFY role ENUM('SADMIN','CLIENT','PERSONNEL','PARENT') NOT NULL DEFAULT 'PARENT'");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'eleve_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('eleve_id');
            });
        }

        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE users MODIFY role ENUM('SADMIN','CLIENT','PERSONNEL','PARENT','ENSEIGNANT','ELEVE') NOT NULL DEFAULT 'PARENT'");
        }
    }
};
