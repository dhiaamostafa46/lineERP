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
        Schema::create('hr_trackers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('hr_departments');
            $table->unsignedTinyInteger('type')->comment('1 => Holidays, 2 => Penalties, 3 => Advances, 4 => Rewards');
            $table->unsignedTinyInteger('status')->comment('1 => inactive, 2 => active');
            $table->string('name');
            $table->text('tracker_approvals');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['department_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('hr_trackers');
    }
};
