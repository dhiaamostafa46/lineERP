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
        Schema::table('asset_categories', function (Blueprint $table) {
            $table->string('calculation_type')->default('automatic')->nullable()->after('default_depreciation_method');
            $table->string('useful_life_type')->default('yearly')->nullable()->after('calculation_type');
            $table->tinyInteger('status')->default(1)->after('useful_life_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_categories', function (Blueprint $table) {
            $table->dropColumn(['calculation_type', 'useful_life_type', 'status']);
        });
    }
};
