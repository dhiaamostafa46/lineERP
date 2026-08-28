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
        Schema::create('hr_custodies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained();
            $table->foreignId('asset_id')->constrained('hr_assets');
            $table->foreignId('received_id')->nullable()->constrained('hr_employees');
            $table->string('details')->nullable();
            $table->string('file');
            $table->date('received_at')->nullable();
            $table->date('return_at')->nullable();
            $table->date('Accept_at')->nullable();
            $table->text('text_accept')->nullable();
            $table->foreignId('accept_id')->nullable()->constrained('hr_employees');
            $table->unsignedTinyInteger('status')->default(1)->comment('1 = Pending, 2 = Received ,3=return ,4=Accept return');
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
        Schema::drop('hr_custodies');
    }
};
