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
        Schema::create('hr_tracker_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tracker_id')->constrained('hr_trackers')->onDelete('cascade');
            $table->foreignId('job_id')->nullable()->constrained('hr_jobs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('hr_tracker_jobs');
    }
};
