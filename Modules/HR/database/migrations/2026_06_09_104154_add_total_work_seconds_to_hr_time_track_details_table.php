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
        Schema::table('hr_time_track_details', function (Blueprint $table) {
            $table->unsignedInteger('total_work_seconds')->default(0)->after('overtime');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hr_time_track_details', function (Blueprint $table) {
            $table->dropColumn('total_work_seconds');
        });
    }
};
