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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete(); // Foreign key to 'branches'
            //$table->foreignId('branch_id')->constrained()->nullOnDelete()->nullable();
            $table->foreignId('vehicle_brand_id')->constrained('vehicle_brands')->restrictOnDelete();
            $table->foreignId('vehicle_model_id')->constrained('vehicle_models')->restrictOnDelete();
            $table->foreignId('vehicle_license_id')->nullable()->constrained('vc_licenses')->nullOnDelete();
            $table->string('color', 30)->nullable();
            $table->smallInteger('year')->nullable();
            $table->string('plate')->unique();
            $table->unsignedInteger('current_mileage')->default(0)->nullable();
            $table->text('notes')->nullable();
            $table->tinyInteger('status')->default(1)->comment("1=available,2=active,3=maintenance,4=out_of_service,5=sold");
            $table->json('attributes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // فهارس
            $table->index('plate');
            $table->index('status');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vheicles');
    }
};
