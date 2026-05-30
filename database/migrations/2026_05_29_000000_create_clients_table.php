<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            $table->string('nom');
            $table->string('prenom');
            $table->string('telephone', 50)->nullable();
            $table->string('email')->unique();
            $table->string('adresse', 255)->nullable();
            $table->string('password');
            $table->string('status')->default('actif');
            $table->string('ville', 100)->nullable();
            $table->foreignId('etablissement_id')
                ->constrained('etablissements')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
