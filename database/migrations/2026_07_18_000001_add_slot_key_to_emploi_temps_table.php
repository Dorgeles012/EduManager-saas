<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emploi_temps_slots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('enseignant_id');
            $table->string('slot_key', 64);
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->timestamps();
            $table->unique(['tenant_id', 'enseignant_id', 'slot_key'], 'emploi_temps_slots_teacher_key_unique');
        });

        Schema::table('emploi_temps', function (Blueprint $table) {
            if (! Schema::hasColumn('emploi_temps', 'slot_key')) {
                $table->string('slot_key', 64)->nullable()->after('heure_fin');
                $table->index(['tenant_id', 'enseignant_id', 'slot_key'], 'emploi_temps_teacher_slot_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('emploi_temps', function (Blueprint $table) {
            if (Schema::hasColumn('emploi_temps', 'slot_key')) {
                $table->dropIndex('emploi_temps_teacher_slot_index');
                $table->dropColumn('slot_key');
            }
        });

        Schema::dropIfExists('emploi_temps_slots');
    }
};
