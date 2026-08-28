<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('job_id')->nullable()->constrained('hr_jobs')->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained('hr_departments')->onDelete('cascade');
            $table->foreignId('shift_id')->nullable()->constrained('hr_shift_types')->onDelete('cascade');
            $table->unsignedTinyInteger('max_off_days')->default(0);
            $table->decimal('max_advance', 10, 2)->default(0.0);
            $table->decimal('vacation_balance', 8, 2)->default(0);
            $table->boolean('fingerprint_exempt')->default(false);
            $table->tinyInteger('attendance_type')->default(1)->comment('نوع الحضور: 1=بصمة، 2=جغرافي');
            $table->string('username')->nullable();
            $table->string('job_level')->nullable();
            $table->string('specialty')->nullable();
            $table->date('start_at')->nullable();
            $table->date('license_expired_at')->nullable();
            $table->string('Direct_manager')->nullable();
            $table->string('job_number', 50)->nullable()->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Schema::dropIfExists('hr_employees');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
