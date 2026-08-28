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
        if (!Schema::hasTable('hr_attendance_policy_logs')) {
            Schema::create('hr_attendance_policy_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('hr_time_track_id')->nullable()->index();
                $table->unsignedBigInteger('employee_id')->index();
                $table->unsignedBigInteger('hr_attendance_policy_id')->index();
                $table->date('date')->index();
                $table->integer('policy_type')->default(1);
                $table->decimal('calculated_amount', 10, 2)->default(0);
                $table->json('details')->nullable();
                $table->timestamp('applied_at')->nullable();
                $table->timestamps();

                $table->foreign('employee_id')->references('id')->on('hr_employees')->onDelete('cascade');
                $table->foreign('hr_attendance_policy_id')->references('id')->on('hr_attendance_policies')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_attendance_policy_logs');
    }
};
