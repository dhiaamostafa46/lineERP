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
        Schema::table('inventory_settings', function (Blueprint $table) {
            $table->unsignedTinyInteger('default_transfer_type')->default(1)->comment('1=direct, 2=indirect')->after('costing_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_settings', function (Blueprint $table) {
            $table->dropColumn('default_transfer_type');
        });
    }
};
