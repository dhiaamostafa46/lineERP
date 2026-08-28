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
        Schema::table('zatca_settings', function (Blueprint $table) {
            $table->string('organization_name')->nullable();
            $table->string('organization_unit_name')->nullable();
            $table->string('building_number')->nullable();
            $table->string('street_name')->nullable();
            $table->string('district_name')->nullable();
            $table->string('city_name')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country_code')->default('SA');
            $table->string('vat_number')->nullable();
            $table->string('vat_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zatca_settings', function (Blueprint $table) {
            $table->dropColumn([
                'organization_name',
                'organization_unit_name',
                'building_number',
                'street_name',
                'district_name',
                'city_name',
                'postal_code',
                'country_code',
                'vat_number',
                'vat_name',
            ]);
        });
    }
};
