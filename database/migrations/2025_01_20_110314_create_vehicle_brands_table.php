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
        Schema::create('vehicle_brands', function (Blueprint $table) {
            $table->id();
            $table->string('file')->nullable();
            $table->unsignedTinyInteger('status')->default(2)->comment("1=inactive,2=active");
            $table->timestamps();
            $table->softDeletes();

        });

     
        Schema::create('vehicle_brands_translations', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('brand_id')->constrained('vehicle_brands')->onDelete('cascade');
            $table->string('locale')->index();
            $table->string('name');
            $table->text('description')->nullable();
         
            $table->unique(['brand_id', 'locale']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_brands_translations');
        Schema::dropIfExists('vehicle_brands');
    }
};
