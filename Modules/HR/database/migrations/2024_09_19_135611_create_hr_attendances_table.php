<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hr_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->onDelete('cascade');
            $table->foreignId('places_id')->nullable()->constrained('hr_places');
            $table->integer('day')->nullable();
            $table->string('name', 255)->nullable();
            $table->string('lat', 255)->nullable();
            $table->string('lon', 255)->nullable();
            $table->string('address', 255)->nullable();

            $table->date('date')->nullable();
            $table->time('check_time')->nullable();




            $table->unsignedInteger('delay')->default(0); // تحسين القيم الافتراضية
            $table->unsignedInteger('early_leave')->default(0);
            $table->unsignedInteger('overtime')->default(0);
            $table->unsignedInteger('early_arrival')->default(0);



            $table->time('shift_from')->nullable();
            $table->time('shift_to')->nullable();

            $table->integer('Active')->nullable();
            $table->integer('kind')->default(1);
            $table->unsignedTinyInteger('type')->default(2);
            $table->unsignedTinyInteger('status')->comment('1 => inactive, 2 => active')->default(2);
            $table->integer('distance')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_attendances');
    }
};





