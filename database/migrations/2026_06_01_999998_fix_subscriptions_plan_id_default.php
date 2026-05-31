<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('subscriptions', 'plan_id')) {
                // Pour éviter : Field 'plan_id' doesn't have a default value
                // et ne pas casser des inserts existants.
                $table->unsignedBigInteger('plan_id')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('subscriptions', 'plan_id')) {
                // Au rollback, on remet en NOT NULL sans spécifier de DEFAULT.
                // (si ton schéma original diffère, adapte ici.)
                $table->unsignedBigInteger('plan_id')->nullable(false)->change();
            }
        });
    }
};

