<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('communications')) {
            Schema::table('communications', function (Blueprint $table) {
                if (!Schema::hasColumn('communications', 'status')) {
                    $table->enum('status', ['sent', 'delivered', 'read'])->default('sent')->after('is_read');
                }
                if (!Schema::hasColumn('communications', 'delivered_at')) {
                    $table->timestamp('delivered_at')->nullable()->after('status');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('communications')) {
            Schema::table('communications', function (Blueprint $table) {
                if (Schema::hasColumn('communications', 'status')) {
                    $table->dropColumn('status');
                }
                if (Schema::hasColumn('communications', 'delivered_at')) {
                    $table->dropColumn('delivered_at');
                }
            });
        }
    }
};
