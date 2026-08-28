<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            // Check if constraint exists before adding
            $indexes = \Illuminate\Support\Facades\DB::select("SHOW INDEX FROM assets WHERE Key_name = 'uq_assetable_mapping'");
            if (empty($indexes)) {
                $table->unique(['assetable_type', 'assetable_id'], 'uq_assetable_mapping');
            }
            if (!Schema::hasColumn('assets', 'cost_center_id')) {
                $table->foreignId('cost_center_id')
                      ->nullable()
                      ->after('asset_category_id')
                      ->constrained('cost_centers')
                      ->nullOnDelete();
            }
        });

        Schema::table('depreciations', function (Blueprint $table) {
            if (!Schema::hasColumn('depreciations', 'units_produced_in_period')) {
                $table->unsignedInteger('units_produced_in_period')
                      ->nullable()
                      ->after('depreciation_amount')
                      ->comment('وحدات الإنتاج في الفترة — للطريقة 4');
            }
            if (!Schema::hasColumn('depreciations', 'cost_center_id')) {
                $table->foreignId('cost_center_id')
                      ->nullable()
                      ->after('journal_entry_id')
                      ->constrained('cost_centers')
                      ->nullOnDelete();
            }
            if (!Schema::hasColumn('depreciations', 'depreciation_run_id')) {
                $table->foreignId('depreciation_run_id')
                      ->nullable()
                      ->after('cost_center_id')
                      ->constrained('depreciation_runs')
                      ->nullOnDelete();
            }
        });

        Schema::table('depreciation_runs', function (Blueprint $table) {
            if (!Schema::hasColumn('depreciation_runs', 'uses_individual_entries')) {
                $table->boolean('uses_individual_entries')
                      ->default(false)
                      ->after('journal_entry_id')
                      ->comment('true = قيد لكل أصل، false = قيد مجمَّع');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('depreciation_runs', function (Blueprint $table) {
            $table->dropColumn('uses_individual_entries');
        });

        Schema::table('depreciations', function (Blueprint $table) {
            $table->dropForeign(['depreciation_run_id']);
            $table->dropForeign(['cost_center_id']);
            $table->dropColumn(['units_produced_in_period', 'cost_center_id', 'depreciation_run_id']);
        });

        Schema::table('assets', function (Blueprint $table) {
            $indexes = \Illuminate\Support\Facades\DB::select("SHOW INDEX FROM assets WHERE Key_name = 'uq_assetable_mapping'");
            if (!empty($indexes)) {
                $table->dropUnique('uq_assetable_mapping');
            }
            $table->dropForeign(['cost_center_id']);
            $table->dropColumn('cost_center_id');
        });
    }
};
