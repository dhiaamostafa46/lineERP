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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches');
            $table->string('org_id')->nullable();
            $table->string('tab')->nullable();
            $table->string('full_name');
            $table->string('username');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->date('dob')->nullable();
            $table->string('address')->nullable();
            $table->string('national_address')->nullable();
            $table->string('religion')->nullable();
            $table->integer('gender')->nullable()->comment('1 = male, 2 = female');
            $table->integer('marital_status')->nullable()->comment('1 = single, 2 = married, 3 = divorced, 4 = widowed, 5 = engaged');
            $table->integer('number_of_children')->nullable();
            $table->string('nationality')->nullable();
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
        Schema::drop('employees');
    }
};
