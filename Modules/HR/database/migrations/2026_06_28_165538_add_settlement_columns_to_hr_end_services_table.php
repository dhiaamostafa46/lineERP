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
        Schema::table('hr_end_services', function (Blueprint $table) {
            $table->double('total_penalties')->default(0)->after('reward_amount')->comment('العقوبات المستحقة');
            $table->double('total_advances')->default(0)->after('total_penalties')->comment('السلف غير المسددة');
            $table->double('total_deducts')->default(0)->after('total_advances')->comment('الاستقطاعات غير المسددة');
            $table->double('net_reward')->default(0)->after('total_deducts')->comment('صافي مكافأة نهاية الخدمة');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hr_end_services', function (Blueprint $table) {
            $table->dropColumn(['total_penalties', 'total_advances', 'total_deducts', 'net_reward']);
        });
    }
};
