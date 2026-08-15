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

        if (Schema::hasTable('etablissements') && ! Schema::hasColumn('etablissements', 'client_id')) {
            Schema::table('etablissements', function (Blueprint $table) {
                $table->unsignedBigInteger('client_id')->nullable()->after('tenant_id');
                $table->index(['client_id', 'tenant_id'], 'etablissements_client_tenant_index');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('etablissements') && Schema::hasColumn('etablissements', 'client_id')) {
            Schema::table('etablissements', function (Blueprint $table) {
                $table->dropIndex('etablissements_client_tenant_index');
                $table->dropColumn('client_id');
            });
        }

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
