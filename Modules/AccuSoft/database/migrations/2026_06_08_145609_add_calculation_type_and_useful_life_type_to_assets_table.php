
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
            $table->string('calculation_type')->default('automatic')->after('depreciation_method')->comment('automatic, manual');
            $table->string('useful_life_type')->default('months')->after('useful_life')->comment('months, years');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn(['calculation_type', 'useful_life_type']);
        });
    }
};
