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
        Schema::create('hr_termination_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('termination_id')->constrained('hr_terminations')->onDelete('cascade');
            $table->foreignId('contract_id')->constrained('hr_contracts')->onDelete('cascade');
            $table->bigInteger('worked_days');
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
        Schema::drop('hr_termination_contracts');
    }
};
