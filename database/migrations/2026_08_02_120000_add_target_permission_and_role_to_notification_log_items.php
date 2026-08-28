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
                if (!Schema::hasColumn('notification_log_items', 'target_permission')) {
                    $table->string('target_permission')->nullable()->after('notification_type');
                }
                if (!Schema::hasColumn('notification_log_items', 'target_role')) {
                    $table->string('target_role')->nullable()->after('target_permission');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('notification_log_items')) {
            Schema::table('notification_log_items', function (Blueprint $table) {
                if (Schema::hasColumn('notification_log_items', 'target_permission')) {
                    $table->dropColumn('target_permission');
                }
                if (Schema::hasColumn('notification_log_items', 'target_role')) {
                    $table->dropColumn('target_role');
                }
            });
        }
    }
};
