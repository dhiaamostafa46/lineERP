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
        if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
            Schema::table('st_settlement_items', function (Blueprint $table) {
                $table->dropForeign(['product_id']);
                $table->dropForeign(['unit_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('st_settlement_items', function (Blueprint $table) {
            //
        });
    }
};
