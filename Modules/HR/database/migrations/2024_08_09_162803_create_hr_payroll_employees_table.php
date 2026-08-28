<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_payroll_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->onDelete('cascade');
            $table->foreignId('payroll_id')->constrained('hr_payrolls')->onDelete('cascade');
            $table->string('username')->nullable();
            $table->char('currency', 3)->default('SAR');
            $table->string('job_name')->nullable();
            $table->string('department_name')->nullable();
            $table->decimal('basic_wage', 8, 2);
            $table->decimal('total_allowances', 8, 2);
            $table->decimal('total_deducts', 8, 2);
            $table->decimal('total_penalties', 8, 2);
            $table->decimal('total_advances', 8, 2);
            $table->decimal('total_rewards', 8, 2);
            $table->decimal('net_wage', 8, 2);
            $table->unsignedTinyInteger('status')->default(1)->comment('1 = pending, 2 = approved, 3 = rejected');
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
        Schema::drop('hr_payroll_employees');
    }
};
