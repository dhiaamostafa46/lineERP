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
        Schema::create('hr_payrolls', function (Blueprint $table) {
            $table->id();
            $table->decimal('total', 10, 2);
            $table->char('currency', 3)->default('SAR');
            $table->date('payroll_date');
            $table->date('delivery_at');
            $table->timestamp('preparing_at')->default(now());
            $table->string('tab')->default('main');
            $table->unsignedTinyInteger('status')->default(2)->comment('1 = draft, 2 = preparing, 3 = accredited, 4 = delivered');
            $table->boolean('approvals_is_ready')->default(0);
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
        Schema::drop('hr_payrolls');
    }
};
