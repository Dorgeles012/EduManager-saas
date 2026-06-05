<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('subscriptions')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                if (! Schema::hasColumn('subscriptions', 'client_id')) {
                    $table->unsignedBigInteger('client_id')->nullable();
                }

                if (! Schema::hasColumn('subscriptions', 'amount')) {
                    $table->integer('amount')->nullable();
                }
            });
        }

        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                if (! Schema::hasColumn('payments', 'amount')) {
                    $table->integer('amount')->nullable();
                }

                if (! Schema::hasColumn('payments', 'payment_method')) {
                    $table->string('payment_method', 100)->nullable();
                }

                if (! Schema::hasColumn('payments', 'status')) {
                    $table->enum('status', ['pending', 'paid', 'failed'])->default('paid');
                }

                if (! Schema::hasColumn('payments', 'statut')) {
                    $table->enum('statut', ['pending', 'paid', 'failed'])->default('paid');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('subscriptions')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                if (Schema::hasColumn('subscriptions', 'amount')) {
                    $table->dropColumn('amount');
                }

                if (Schema::hasColumn('subscriptions', 'client_id')) {
                    $table->dropColumn('client_id');
                }
            });
        }

        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                foreach (['statut', 'status', 'payment_method', 'amount'] as $column) {
                    if (Schema::hasColumn('payments', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
