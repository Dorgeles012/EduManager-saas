<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('note_historiques')) {
            return;
        }

        Schema::table('note_historiques', function (Blueprint $table) {
            $table->index(['tenant_id', 'acteur_id'], 'note_historiques_tenant_acteur_index');
            $table->foreign('tenant_id', 'note_historiques_tenant_fk')
                ->references('id')->on('tenants')->restrictOnDelete();
            $table->foreign('note_id', 'note_historiques_note_fk')
                ->references('id')->on('notes')->nullOnDelete();
            $table->foreign('acteur_id', 'note_historiques_acteur_fk')
                ->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('note_historiques')) {
            return;
        }

        Schema::table('note_historiques', function (Blueprint $table) {
            $table->dropForeign('note_historiques_tenant_fk');
            $table->dropForeign('note_historiques_note_fk');
            $table->dropForeign('note_historiques_acteur_fk');
            $table->dropIndex('note_historiques_tenant_acteur_index');
        });
    }
};
