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
        Schema::table('pos_devices', function (Blueprint $table) {
            if (!Schema::hasColumn('pos_devices', 'allow_discount_modification')) {
                $table->boolean('allow_discount_modification')->default(true);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_devices', function (Blueprint $table) {
            if (Schema::hasColumn('pos_devices', 'allow_discount_modification')) {
                $table->dropColumn('allow_discount_modification');
            }
        });
    }
};
