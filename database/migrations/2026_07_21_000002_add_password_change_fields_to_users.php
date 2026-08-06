<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute les champs de contrôle d'obligation de changement de mot de passe.
     */
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            if (! Schema::hasColumn('users', 'must_change_password')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->boolean('must_change_password')->default(false)->after('password');
                });
            }

            if (! Schema::hasColumn('users', 'password_changed_at')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->timestamp('password_changed_at')->nullable()->after('must_change_password');
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users')) {
            if (Schema::hasColumn('users', 'must_change_password')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropColumn('must_change_password');
                });
            }

            if (Schema::hasColumn('users', 'password_changed_at')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropColumn('password_changed_at');
                });
            }
        }
    }
};
