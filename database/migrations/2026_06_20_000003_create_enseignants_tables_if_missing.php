<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('enseignants')) {
            Schema::create('enseignants', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id');
                $table->unsignedBigInteger('etablissement_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('nom');
                $table->string('prenoms')->nullable();
                $table->string('email')->nullable();
                $table->string('telephone', 50)->nullable();
                $table->string('password')->nullable();
                $table->unsignedBigInteger('matiere_id')->nullable();
                $table->string('specialite')->nullable();
                $table->enum('statut', ['active', 'inactive'])->default('active');
                $table->timestamps();
            });
        } elseif (!Schema::hasColumn('enseignants', 'matiere_id')) {
            Schema::table('enseignants', function (Blueprint $table) {
                $table->unsignedBigInteger('matiere_id')->nullable()->after('password');
            });
        }

        if (!Schema::hasTable('enseignant_matiere')) {
            Schema::create('enseignant_matiere', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('enseignant_id');
                $table->unsignedBigInteger('matiere_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('enseignant_matiere');
        Schema::dropIfExists('enseignants');
    }
};
