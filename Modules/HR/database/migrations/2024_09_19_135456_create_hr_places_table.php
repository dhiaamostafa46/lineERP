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
        Schema::create('hr_places', function (Blueprint $table) {
            $table->id();

            $table->string('day')->nullable();
            $table->string('name', 255)->nullable();
            $table->string('lat', 255)->nullable();
            $table->string('lon', 255)->nullable();
            $table->string('address', 255)->nullable();
            $table->unsignedTinyInteger('status')->default(2 );
            $table->integer('distance')->default(1);

            $table->text('employee_id')->nullable();
            $table->text('department_id')->nullable();
            $table->text('branch_id')->nullable();

            $table->text('start_date')->nullable();
            $table->text('end_date')->nullable();
              $table->integer('enable_daterange')->default(0);

            // $table->foreignId('employee_id')->nullable()->constrained('hr_employees')->onDelete('cascade');
            // $table->foreignId('department_id')->nullable()->constrained('hr_departments');
            // $table->foreignId('branch_id')->nullable()->constrained('branches');
            $table->integer('flage')->default(0)->comment('3 => emp, 2 => deperment,1 => All');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_places');
    }
};
