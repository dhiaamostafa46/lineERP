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
        Schema::create('hr_termination_type_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('termination_type_id')->constrained('hr_termination_types')->onDelete('cascade');
            $table->integer('percentage');
            $table->integer('worked_days');
            $table->integer('fixed_amount');
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
        Schema::drop('hr_termination_type_rewards');
    }
};
