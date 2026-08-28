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
            if (!Schema::hasColumn('assets', 'asset_category_id')) {
                $table->foreignId('asset_category_id')->nullable()->after('id')->constrained('asset_categories')->onDelete('restrict');
            }
            if (!Schema::hasColumn('assets', 'assetable_type')) {
                $table->nullableMorphs('assetable'); // assetable_type, assetable_id
            }

            // Make accounting fields nullable
            $table->unsignedBigInteger('asset_account_id')->nullable()->change();
            $table->unsignedBigInteger('depreciation_expense_account_id')->nullable()->change();
            $table->unsignedBigInteger('accumulated_depreciation_account_id')->nullable()->change();

            // Make purchase fields nullable
            $table->date('purchase_date')->nullable()->change();
            $table->decimal('purchase_value', 15, 2)->nullable()->change();
            $table->integer('useful_life')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign(['asset_category_id']);
            $table->dropColumn('asset_category_id');
            $table->dropMorphs('assetable');

            // Reverting change() isn't strictly necessary for down() in most cases, but good practice
            $table->unsignedBigInteger('asset_account_id')->nullable(false)->change();
            $table->unsignedBigInteger('depreciation_expense_account_id')->nullable(false)->change();
            $table->unsignedBigInteger('accumulated_depreciation_account_id')->nullable(false)->change();
            $table->date('purchase_date')->nullable(false)->change();
            $table->decimal('purchase_value', 15, 2)->nullable(false)->change();
            $table->integer('useful_life')->nullable(false)->change();
        });
    }
};
