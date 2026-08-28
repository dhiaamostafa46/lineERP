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
        Schema::create('hr_payroll_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('payroll_id')->constrained('hr_payrolls');
            $table->unsignedTinyInteger('status')->default(1)->comment('1 = pending, 2 = approved, 3 = rejected');
            $table->text('note')->nullable();
            $table->unsignedTinyInteger('sort');
            $table->boolean('is_current')->default(0);
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
        Schema::drop('hr_payroll_approvals');
    }
};
