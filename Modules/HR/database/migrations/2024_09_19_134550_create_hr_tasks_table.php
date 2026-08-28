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

        Schema::create('hr_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable();
            $table->longText('description')->nullable();
            $table->date('done')->nullable();
            $table->unsignedTinyInteger('status')->default(1);
            $table->integer('flage')->default(0);
            $table->integer('group')->nullable();
            $table->foreignId('employee_id')->nullable()->constrained('hr_employees');
            $table->foreignId('department_id')->nullable()->constrained('hr_departments');
            $table->foreignId('group_id')->nullable()->constrained('hr_groups');
            $table->string('file', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });


        Schema::create('hr_task_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hr_task_id')->constrained('hr_tasks')->onDelete('cascade');
            $table->longText('description')->nullable();
            $table->foreignId('employee_id')->nullable()->constrained('hr_employees')->onDelete('cascade');
            $table->integer('userID')->nullable();
            $table->string('file', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_task_details');
        Schema::dropIfExists('hr_tasks');
    }
};
