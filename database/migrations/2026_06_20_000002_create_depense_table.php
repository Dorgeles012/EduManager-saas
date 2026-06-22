<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('depense')) {
            Schema::create('depense', function (Blueprint $table) {
                $table->id('id_depense');
                $table->unsignedBigInteger('tenant_id')->nullable()->index();
                $table->string('libel_depense');
                $table->integer('montant');
                $table->string('categorie')->nullable();
                $table->date('date_depense')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('depense');
    }
};
