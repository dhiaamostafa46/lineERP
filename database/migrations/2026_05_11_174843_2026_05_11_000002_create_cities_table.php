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
        Schema::create('cities', function (Blueprint $table) {
            $table->id();

            $table->string('code')->unique(); // CTY-001

            $table->unsignedTinyInteger('status')->default(2)->comment("1=inactive,2=active");

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('code');
            $table->index('status');
        });

        Schema::create('city_translations', function (Blueprint $table) {
            $table->id();

            // Using cities_id like your structure
            $table->foreignId('city_id')
                ->constrained('cities')
                ->onDelete('cascade');

            $table->string('locale')->index();

            $table->string('name');
            $table->unique(['city_id', 'locale']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('city_translations');
        Schema::dropIfExists('cities');
    }
};
