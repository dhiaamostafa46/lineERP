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
        Schema::create('vehicle_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained('vehicle_brands')->restrictOnDelete(); // Foreign key to 'vehicle_brands'
            $table->string('file')->nullable();
            $table->unsignedTinyInteger('status')->default(2)->comment("1=inactive,2=active");
            $table->timestamps();
            $table->softDeletes();

        });

     
        Schema::create('vehicle_models_translations', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('vehicle_model_id')->constrained('vehicle_models')->onDelete('cascade');
            $table->string('locale')->index();
            $table->string('name');
            $table->text('description')->nullable();
         
            $table->unique(['vehicle_model_id', 'locale']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_models_translations');
        Schema::dropIfExists('vehicle_models');
    }
};
