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
        Schema::create('hr_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->onDelete('cascade');
            $table->decimal('basic', 10, 2)->nullable();
            $table->decimal('day_amount', 10, 2)->nullable();
            $table->decimal('hour_amount', 10, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hr_salary_allowances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained('hr_employees')->onDelete('cascade');
            $table->foreignId('payroll_id')->nullable()->constrained('hr_payrolls')->onDelete('cascade');
            $table->foreignId('salary_id')->constrained('hr_salaries')->onDelete('cascade');
            $table->foreignId('allowance_id')->constrained('hr_allowances')->onDelete('cascade');
            $table->decimal('amount', 10, 2)->nullable();
        });

        Schema::create('hr_salary_deducts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained('hr_employees')->onDelete('cascade');
            $table->foreignId('payroll_id')->nullable()->constrained('hr_payrolls')->onDelete('cascade');
            $table->foreignId('salary_id')->constrained('hr_salaries')->onDelete('cascade');
            $table->foreignId('deduct_id')->constrained('hr_deducts')->onDelete('cascade');
            $table->decimal('amount', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('hr_salary_deducts');
        Schema::drop('hr_salary_allowances');
        Schema::drop('hr_salaries');
    }
};
