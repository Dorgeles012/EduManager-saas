<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('bulletin_discipline')) {
            return;
        }
        Schema::create('bulletin_discipline', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->unsignedBigInteger('bulletin_id');

            $table->string('discipline');
            $table->decimal('moyenne', 6, 2)->nullable();
            $table->decimal('coefficient', 6, 2)->default(1);
            $table->decimal('moyenne_coefficient', 8, 2)->nullable();
            $table->unsignedInteger('rang')->default(0);

            $table->string('appréciation')->nullable();
            $table->string('professeur')->nullable();
            $table->string('signature')->nullable();

            $table->timestamps();

            $table->foreign('bulletin_id')->references('id')->on('bulletins')->onDelete('cascade');
            $table->index(['bulletin_id', 'discipline']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bulletin_discipline');
    }
};

