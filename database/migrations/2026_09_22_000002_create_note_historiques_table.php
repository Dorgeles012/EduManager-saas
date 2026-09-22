<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('note_historiques')) {
            Schema::create('note_historiques', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id');
                $table->unsignedBigInteger('note_id')->nullable();
                $table->string('action', 30);
                $table->unsignedBigInteger('acteur_id')->nullable();
                $table->json('avant')->nullable();
                $table->json('apres')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'note_id']);
                $table->index(['tenant_id', 'action']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('note_historiques');
    }
};
