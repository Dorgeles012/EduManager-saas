<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { Schema::table('emploi_temps', function (Blueprint $table) { $table->unsignedBigInteger('etablissement_id')->nullable()->after('tenant_id'); $table->unsignedBigInteger('annee_academique_id')->nullable()->after('etablissement_id'); $table->index(['tenant_id','classe_id','jour']); }); }
 public function down(): void { Schema::table('emploi_temps', function (Blueprint $table) { $table->dropIndex(['tenant_id','classe_id','jour']); $table->dropColumn(['etablissement_id','annee_academique_id']); }); }
};
