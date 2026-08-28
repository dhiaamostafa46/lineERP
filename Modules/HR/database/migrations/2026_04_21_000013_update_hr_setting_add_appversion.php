<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('hr_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('hr_settings', 'app_version')) {
                $table->string('app_version')->nullable();
            }

            if (!Schema::hasColumn('hr_settings', 'app_min_version')) {
                $table->string('app_min_version')->nullable();
            }

            if (!Schema::hasColumn('hr_settings', 'app_url')) {
                $table->string('app_url')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('hr_settings', function (Blueprint $table) {
            // حذف الحقول الجديدة
            $table->dropColumn(['app_version', 'app_min_version', 'app_url']);
        });
    }
};
