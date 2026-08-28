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
        Schema::create('hr_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->onDelete('cascade');
            $table->foreignId('approver_id')->nullable()->constrained('users');
            $table->foreignId('payroll_id')->nullable()->constrained('hr_payrolls')->onDelete('cascade');
            $table->unsignedInteger('amount')->nullable();
            $table->integer('over_time')->nullable();
            $table->integer('days_off')->nullable();
            $table->date('start_at')->nullable();
            $table->date('end_at')->nullable();
            $table->text('note')->nullable();
            $table->unsignedTinyInteger('type');
            $table->unsignedTinyInteger('status')->default(1)->comment('1 = Pending, 2 = Approved, 3 = Rejected');
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
        Schema::drop('hr_rewards');
    }
};
