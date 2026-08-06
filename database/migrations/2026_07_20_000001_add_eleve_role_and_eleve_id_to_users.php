<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute le rôle ELEVE et la colonne eleve_id sur users
     * pour permettre la connexion des élèves à leur espace.
     */
    public function up(): void
    {
        // 1. Colonne eleve_id (nullable) pour lier un compte élève à un élève
        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'eleve_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('eleve_id')->nullable()->after('client_id');
            });
        }

        // 2. Étendre l'enum role avec 'ELEVE'
        if (! Schema::hasTable('users')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            // Assainir les valeurs existantes avant de modifier l'ENUM
            DB::statement("UPDATE users SET role = 'PARENT' WHERE role IS NULL OR role = ''");
            DB::statement("UPDATE users SET role = UPPER(TRIM(role)) WHERE role NOT IN ('SADMIN','CLIENT','PERSONNEL','ENSEIGNANT','PARENT','ELEVE')");

            DB::statement("ALTER TABLE users MODIFY role ENUM('SADMIN','CLIENT','PERSONNEL','ENSEIGNANT','PARENT','ELEVE') NOT NULL DEFAULT 'PARENT'");
        } else {
            // SQLite / autres : pas de vrai ENUM, rien à faire
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
            DB::statement("ALTER TABLE users MODIFY role ENUM('SADMIN','CLIENT','PERSONNEL','ENSEIGNANT','PARENT') NOT NULL DEFAULT 'PARENT'");
        }
    }
};
