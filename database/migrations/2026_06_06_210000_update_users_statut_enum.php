<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            // MySQL note:
            // "ALTER TABLE ... MODIFY statut ENUM('actif','bloqué')" échoue si la colonne contient
            // déjà des valeurs non compatibles et MySQL tronque en ''.
            // Ici, l'ENUM courant est censé être: ('active','inactive','blocked').
            // On assainit donc d'abord en ne gardant que ces valeurs (et en remplaçant le vide).

            DB::statement("UPDATE users SET statut = 'active' WHERE statut IS NULL OR statut = ''");
            DB::statement("UPDATE users SET statut = 'active' WHERE statut NOT IN ('active','inactive','blocked')");

            // Ensuite on modifie l'ENUM.
            Schema::table('users', function (Blueprint $table) {
                $table->enum('statut', ['actif', 'bloqué'])->default('actif')->change();
            });

            // Puis on convertit dans le nouvel ENUM.
            DB::table('users')->where('statut', 'active')->update(['statut' => 'actif']);
            DB::table('users')->where('statut', 'inactive')->update(['statut' => 'actif']);
            DB::table('users')->where('statut', 'blocked')->update(['statut' => 'bloqué']);

            return;
        }

        // SQLite (tests): pas de vrais ENUM.
        DB::statement("UPDATE users SET statut = TRIM(LOWER(statut))");
        DB::statement("UPDATE users SET statut = 'actif' WHERE statut IN ('active','actif','inactive','inactif','on','enabled','enable','1','true') OR statut IS NULL OR statut = ''");
        DB::statement("UPDATE users SET statut = 'bloqué' WHERE statut IN ('blocked','bloqué','bloque','block','disabled','disable','0','false')");
    }

    public function down(): void
    {
        // Rollback volontairement minimal (non déterministe selon l'état initial).
    }
};

