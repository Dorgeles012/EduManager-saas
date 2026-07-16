<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('enseignants', function (Blueprint $table) {
            if (!Schema::hasColumn('enseignants', 'matricule')) $table->string('matricule')->nullable()->unique()->after('telephone');
            if (!Schema::hasColumn('enseignants', 'nombre_annees_enseignement')) $table->unsignedInteger('nombre_annees_enseignement')->nullable()->after('matricule');
            if (!Schema::hasColumn('enseignants', 'sexe')) $table->enum('sexe', ['Masculin', 'Féminin'])->nullable()->after('nombre_annees_enseignement');
            if (!Schema::hasColumn('enseignants', 'photo')) $table->string('photo')->nullable()->after('sexe');
        });
        if (Schema::hasTable('enseignant_matiere') && !Schema::hasColumn('enseignant_matiere', 'created_at')) {
            Schema::table('enseignant_matiere', function (Blueprint $table) { $table->timestamps(); });
        }
        if (!Schema::hasTable('enseignant_serie')) Schema::create('enseignant_serie', function (Blueprint $table) { $table->id(); $table->foreignId('enseignant_id')->constrained('enseignants')->cascadeOnDelete(); $table->foreignId('serie_id')->constrained('series')->cascadeOnDelete(); $table->timestamps(); $table->unique(['enseignant_id','serie_id']); });
        Schema::table('emploi_temps', function (Blueprint $table) { if (!Schema::hasColumn('emploi_temps', 'serie_id')) $table->unsignedBigInteger('serie_id')->nullable()->after('classe_id'); });
    }
    public function down(): void
    {
        Schema::dropIfExists('enseignant_serie');
        Schema::table('emploi_temps', function (Blueprint $table) { if (Schema::hasColumn('emploi_temps','serie_id')) $table->dropColumn('serie_id'); });
        Schema::table('enseignants', function (Blueprint $table) { foreach (['photo','sexe','nombre_annees_enseignement','matricule'] as $column) if (Schema::hasColumn('enseignants',$column)) $table->dropColumn($column); });
    }
};
