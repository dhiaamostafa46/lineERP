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
        Schema::create('hr_contractitems', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')->constrained('hr_employees')->onDelete('cascade');
            $table->foreignId('contract_id')->constrained('hr_contracts')->onDelete('cascade');
            $table->string('Desc_ar', 255)->nullable();
            $table->string('Desc_En', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });



        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_contractitems');
    }
};
