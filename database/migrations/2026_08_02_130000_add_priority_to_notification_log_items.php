<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('notification_log_items')) {
            Schema::table('notification_log_items', function (Blueprint $table) {
                if (!Schema::hasColumn('notification_log_items', 'priority')) {
                    $table->tinyInteger('priority')->default(2)->after('status')->comment('1=Low, 2=Normal, 3=High, 4=Urgent');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('notification_log_items')) {
            Schema::table('notification_log_items', function (Blueprint $table) {
                if (Schema::hasColumn('notification_log_items', 'priority')) {
                    $table->dropColumn('priority');
                }
            });
        }
    }
};
