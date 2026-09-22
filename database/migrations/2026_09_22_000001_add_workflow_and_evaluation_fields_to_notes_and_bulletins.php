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
        if (Schema::hasTable('notes')) {
            Schema::table('notes', function (Blueprint $table) {
                if (!Schema::hasColumn('notes', 'etablissement_id')) {
                    $table->unsignedBigInteger('etablissement_id')->nullable()->after('tenant_id');
                }
                if (!Schema::hasColumn('notes', 'enseignant_id')) {
                    $table->unsignedBigInteger('enseignant_id')->nullable()->after('matiere_id');
                }
                if (!Schema::hasColumn('notes', 'titre_evaluation')) {
                    $table->string('titre_evaluation', 150)->nullable()->after('matiere_id');
                }
                if (!Schema::hasColumn('notes', 'type_evaluation')) {
                    $table->string('type_evaluation', 50)->default('devoir')->after('titre_evaluation');
                }
                if (!Schema::hasColumn('notes', 'statut')) {
                    $table->string('statut', 50)->default('brouillon')->after('appreciation');
                }
                if (!Schema::hasColumn('notes', 'rejet_motif')) {
                    $table->text('rejet_motif')->nullable()->after('statut');
                }
                if (!Schema::hasColumn('notes', 'rejet_par')) {
                    $table->string('rejet_par', 50)->nullable()->after('rejet_motif');
                }
                if (!Schema::hasColumn('notes', 'soumis_le')) {
                    $table->timestamp('soumis_le')->nullable()->after('rejet_par');
                }
                if (!Schema::hasColumn('notes', 'approuve_personnel_le')) {
                    $table->timestamp('approuve_personnel_le')->nullable()->after('soumis_le');
                }
                if (!Schema::hasColumn('notes', 'approuve_personnel_id')) {
                    $table->unsignedBigInteger('approuve_personnel_id')->nullable()->after('approuve_personnel_le');
                }
                if (!Schema::hasColumn('notes', 'publie_le')) {
                    $table->timestamp('publie_le')->nullable()->after('approuve_personnel_id');
                }
                if (!Schema::hasColumn('notes', 'publie_par_id')) {
                    $table->unsignedBigInteger('publie_par_id')->nullable()->after('publie_le');
                }
            });
        }

        if (Schema::hasTable('bulletins')) {
            Schema::table('bulletins', function (Blueprint $table) {
                if (!Schema::hasColumn('bulletins', 'statut')) {
                    $table->string('statut', 50)->default('brouillon')->after('observation_conseil');
                }
                if (!Schema::hasColumn('bulletins', 'publie_le')) {
                    $table->timestamp('publie_le')->nullable()->after('statut');
                }
                if (!Schema::hasColumn('bulletins', 'publie_par_id')) {
                    $table->unsignedBigInteger('publie_par_id')->nullable()->after('publie_le');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('notes')) {
            Schema::table('notes', function (Blueprint $table) {
                $cols = [
                    'etablissement_id',
                    'enseignant_id',
                    'titre_evaluation',
                    'type_evaluation',
                    'statut',
                    'rejet_motif',
                    'rejet_par',
                    'soumis_le',
                    'approuve_personnel_le',
                    'approuve_personnel_id',
                    'publie_le',
                    'publie_par_id',
                ];
                foreach ($cols as $col) {
                    if (Schema::hasColumn('notes', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('bulletins')) {
            Schema::table('bulletins', function (Blueprint $table) {
                $cols = ['statut', 'publie_le', 'publie_par_id'];
                foreach ($cols as $col) {
                    if (Schema::hasColumn('bulletins', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
