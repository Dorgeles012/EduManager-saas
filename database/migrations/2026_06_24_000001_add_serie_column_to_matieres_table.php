<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('matieres')) {
            return;
        }

        if (!Schema::hasColumn('matieres', 'serie')) {
            Schema::table('matieres', function (Blueprint $table) {
                $table->unsignedBigInteger('serie')->nullable()->after('coefficient');
            });
        }

        if (Schema::hasColumn('matieres', 'serie') && Schema::hasTable('series')) {
            // Check if the foreign key constraint already exists before creating it
            $constraintExists = $this->foreignKeyExists('matieres', 'matieres_serie_foreign');
            
            if (!$constraintExists) {
                try {
                    Schema::table('matieres', function (Blueprint $table) {
                        $table->foreign('serie')->references('id')->on('series')->onDelete('restrict');
                    });
                } catch (\Throwable $e) {
                    // Ignore if the foreign key cannot be added (already exists or other issue)
                }
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('matieres') || !Schema::hasColumn('matieres', 'serie')) {
            return;
        }

        Schema::table('matieres', function (Blueprint $table) {
            try {
                $table->dropForeign(['serie']);
            } catch (\Throwable $e) {
                // Ignore if foreign key does not exist.
            }

            $table->dropColumn('serie');
        });
    }

    /**
     * Check if a foreign key constraint exists in a table
     *
     * @param string $table
     * @param string $constraint
     * @return bool
     */
    private function foreignKeyExists(string $table, string $constraint): bool
    {
        try {
            $database = DB::getDatabaseName();
            $result = DB::selectOne(
                "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE 
                 WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL",
                [$database, $table, $constraint]
            );
            return (bool) $result;
        } catch (\Throwable $e) {
            // If we can't query, assume it doesn't exist
            return false;
        }
    }
};

