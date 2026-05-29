<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Migration NON-destructive : uniquement des index pour améliorer les recherches/validations.
        Schema::table('etablissements', function (Blueprint $table) {
            $table->index(['tenant_id', 'nom'], 'etablissements_tenant_nom_idx');
            $table->index(['tenant_id', 'email'], 'etablissements_tenant_email_idx');
            $table->index(['tenant_id', 'acronyme'], 'etablissements_tenant_acronyme_idx');
        });
    }

    public function down(): void
    {
        Schema::table('etablissements', function (Blueprint $table) {
            $table->dropIndex('etablissements_tenant_nom_idx');
            $table->dropIndex('etablissements_tenant_email_idx');
            $table->dropIndex('etablissements_tenant_acronyme_idx');
        });
    }
};

