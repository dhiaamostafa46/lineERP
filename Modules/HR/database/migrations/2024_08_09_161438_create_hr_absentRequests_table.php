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
        Schema::create('hr_absentRequests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained();
            $table->foreignId('approver_id')->nullable()->constrained('users');
            $table->string('from_at');
            $table->string('end_at');
            $table->string('details')->nullable();
            $table->date('request_date');
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
        Schema::drop('hr_absentRequests');
    }
};
