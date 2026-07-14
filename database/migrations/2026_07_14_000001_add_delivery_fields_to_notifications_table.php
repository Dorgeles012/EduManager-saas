<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table): void {
            $table->foreignId('sender_id')->nullable()->after('tenant_id')->constrained('users')->nullOnDelete();
            $table->string('audience')->nullable()->after('message');
            $table->string('category')->nullable()->after('audience');
            $table->string('priority')->default('normal')->after('category');
            $table->timestamp('sent_at')->nullable()->after('priority');
        });

        Schema::create('notification_recipients', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('notification_id')->constrained('notifications')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->unique(['notification_id', 'user_id']);
            $table->index(['user_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_recipients');

        Schema::table('notifications', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('sender_id');
            $table->dropColumn(['audience', 'category', 'priority', 'sent_at']);
        });
    }
};
