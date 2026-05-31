<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table des types d'abonnement (mensuel, annuel, premium, etc.)
        Schema::create('subscription_types', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique(); // ex: mensuel, annuel, premium, entreprise
            $table->unsignedInteger('default_duration')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });


        // Adaptation stricte de la table `subscriptions` pour correspondre au schéma attendu.
        // NB: cette migration suppose que les colonnes n'existent pas encore.
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('name');
            $table->string('type');
            $table->integer('price');
            $table->integer('duration');
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->unique(['name', 'type'], 'subscriptions_name_type_unique');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_types');
        // On ne supprime pas les colonnes ajoutées pour éviter de casser une base déjà modifiée.
    }
};

