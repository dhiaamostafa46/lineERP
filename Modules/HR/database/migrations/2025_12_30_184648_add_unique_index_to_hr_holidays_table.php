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
        try {
            Schema::table('hr_holidays', function (Blueprint $table) {
                $table->unique(
                    ['employee_id', 'from_at', 'end_at'],
                    'hr_holidays_employee_dates_unique'
                );
            });
        } catch (\Exception $e) {
            // Skip if index exists or data violates unique constraint
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hr_holidays', function (Blueprint $table) {
            $table->dropUnique('hr_holidays_employee_dates_unique');
        });
    }
};
