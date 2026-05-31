<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('subscriptions', 'tenant_id')) {
                // Corrige le bug SQLSTATE[HY000]: tenant_id sans DEFAULT.
                $table->unsignedBigInteger('tenant_id')->default(1)->nullable(false)->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('subscriptions', 'tenant_id')) {
                // Retour au comportement sans DEFAULT (si tu avais un schéma différent, ajuste).
                $table->unsignedBigInteger('tenant_id')->default(null)->change();
            }
        });
    }
};

