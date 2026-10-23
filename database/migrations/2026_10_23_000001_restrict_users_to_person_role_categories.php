<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'role')) {
            return;
        }

        if (! in_array(Schema::getConnection()->getDriverName(), ['mysql', 'mariadb'], true)) {
            return;
        }

        $legacyCount = DB::table('users')
            ->whereRaw('UPPER(TRIM(role)) IN (?, ?)', ['ELEVE', 'ENSEIGNANT'])
            ->count();

        if ($legacyCount > 0) {
            throw new RuntimeException("{$legacyCount} ancien(s) compte(s) élève/enseignant existent encore dans users. Auditez-les avec users:audit-legacy-school-accounts, puis résolvez-les manuellement avant cette migration.");
        }

        DB::statement("ALTER TABLE users MODIFY role ENUM('SADMIN','CLIENT','PERSONNEL','PARENT') NOT NULL DEFAULT 'PARENT'");
    }

    public function down(): void
    {
        if (Schema::hasTable('users')
            && Schema::hasColumn('users', 'role')
            && in_array(Schema::getConnection()->getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE users MODIFY role ENUM('SADMIN','CLIENT','PERSONNEL','PARENT','ENSEIGNANT','ELEVE') NOT NULL DEFAULT 'PARENT'");
        }
    }
};
