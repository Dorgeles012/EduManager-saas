<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // date_debut / date_fin ne doivent pas bloquer l'insert
            if (Schema::hasColumn('subscriptions', 'date_debut')) {
                $table->date('date_debut')->nullable()->change();
            }
            if (Schema::hasColumn('subscriptions', 'date_fin')) {
                $table->date('date_fin')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('subscriptions', 'date_debut')) {
                $table->date('date_debut')->nullable(false)->change();
            }
            if (Schema::hasColumn('subscriptions', 'date_fin')) {
                $table->date('date_fin')->nullable(false)->change();
            }
        });
    }
};

