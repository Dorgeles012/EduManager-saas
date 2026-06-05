<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fix pour SQLSTATE[42S02] : Table 'edu_manager.payments' doesn't exist
        // Si la table n'existe pas, on la crée avec la structure attendue.
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id');
                $table->unsignedBigInteger('subscription_id');
                $table->integer('montant');
                $table->string('methode_paiement', 100)->nullable();
                $table->string('reference_paiement')->nullable();
                $table->date('date_paiement');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

