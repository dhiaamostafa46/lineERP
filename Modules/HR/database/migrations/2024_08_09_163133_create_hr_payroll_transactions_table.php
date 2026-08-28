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
        Schema::create('hr_payroll_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_employee_id')->constrained('hr_payroll_employees');
            $table->morphs('forable');
            $table->decimal('amount', 8, 2);
            $table->char('currency', 3)->default('SAR');
            $table->boolean('is_deduct');
            $table->string('note')->nullable();
            $table->string('name')->nullable();
            $table->unsignedTinyInteger('type')->comment('1 = basic_wage, 2 = allowance, 3 = deduction, 4 = penalty, 5 = advance, 6 => reward');
            $table->unsignedTinyInteger('status')->default(1)->comment('1 => pending, 2 => approved, 3 => rejected');
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
        Schema::drop('hr_payroll_transactions');
    }
};
