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
        // Harmoniser l'enum sexe avec l'application (Masculin/Féminin)
        // Note: le enum original est 'M'/'F'. On le remplace.
        Schema::table('eleves', function (Blueprint $table) {
            if (Schema::hasColumn('eleves', 'sexe')) {
                $table->enum('sexe', ['Masculin', 'Féminin'])->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eleves', function (Blueprint $table) {
            if (Schema::hasColumn('eleves', 'sexe')) {
                $table->enum('sexe', ['M', 'F'])->nullable()->change();
            }
        });
    }
};
