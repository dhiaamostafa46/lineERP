<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hr_time_tracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->onDelete('cascade');
            $table->string('day')->nullable();
            $table->date('date'); // التاريخ
            $table->string('lat', 255)->nullable();
            $table->string('lon', 255)->nullable();
            $table->string('address', 255)->nullable();
            $table->unsignedTinyInteger('hour')->default(0);
            $table->unsignedTinyInteger('type')->default(1)->comment('1 => absent, 2 => present, 3 => vacation ,4 => holiday');
            $table->unsignedTinyInteger('status')->comment('1 => inactive, 2 => active')->default(2);
            $table->unsignedTinyInteger('process')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hr_time_track_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hr_time_track_id')->constrained('hr_time_tracks')->onDelete('cascade');
            $table->time('check_time')->nullable();
            $table->time('check_out')->nullable();
            $table->string('early_arrival')->nullable();
            $table->string('delay')->nullable();
            $table->string('early_leave')->nullable();
            $table->string('overtime')->nullable();
            $table->time('shift_from')->nullable();
            $table->time('shift_to')->nullable();
            $table->string('lat', 255)->nullable();
            $table->string('lon', 255)->nullable();
            $table->string('address', 255)->nullable();
            $table->unsignedTinyInteger('type')->default(1)->comment('1 => absent, 2 => present, 3 => vacation ,4 => holiday');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Schema::dropIfExists('hr_time_track_details');
        Schema::dropIfExists('hr_time_tracks');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
