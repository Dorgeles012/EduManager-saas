<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('plans')) {
            Schema::table('plans', function (Blueprint $table) {
                if (! Schema::hasColumn('plans', 'duration_type')) {
                    $table->string('duration_type', 20)->default('monthly')->after('prix');
                }

                if (! Schema::hasColumn('plans', 'duration_value')) {
                    $table->unsignedInteger('duration_value')->default(1)->after('duration_type');
                }

                if (! Schema::hasColumn('plans', 'max_schools')) {
                    $table->unsignedInteger('max_schools')->nullable()->after('duration_value');
                }

                if (! Schema::hasColumn('plans', 'is_unlimited')) {
                    $table->boolean('is_unlimited')->default(false)->after('max_schools');
                }
            });

            if (Schema::hasColumn('plans', 'max_ecoles')) {
                DB::table('plans')
                    ->whereNull('max_schools')
                    ->update(['max_schools' => DB::raw('max_ecoles')]);
            } else {
                DB::table('plans')
                    ->whereNull('max_schools')
                    ->update(['max_schools' => 1]);
            }

            if (Schema::hasColumn('plans', 'duree')) {
                DB::table('plans')->select(['id', 'duree'])->orderBy('id')->chunkById(100, function ($plans) {
                    foreach ($plans as $plan) {
                        $months = max(1, (int) $plan->duree);

                        DB::table('plans')
                            ->where('id', $plan->id)
                            ->update([
                                'duration_type' => $months >= 12 ? 'annual' : 'monthly',
                                'duration_value' => $months >= 12 ? 1 : $months,
                            ]);
                    }
                });
            }
        }

    }

    public function down(): void
    {

        if (Schema::hasTable('plans')) {
            Schema::table('plans', function (Blueprint $table) {
                foreach (['is_unlimited', 'max_schools', 'duration_value', 'duration_type'] as $column) {
                    if (Schema::hasColumn('plans', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
