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
        Schema::create('areas', function (Blueprint $table) {
            $table->id();

            $table->string('code')->unique(); // AREA-001

            $table->unsignedTinyInteger('status')->default(2)->comment("1=inactive,2=active");

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('code');
            $table->index('status');
        });

        Schema::create('area_translations', function (Blueprint $table) {
            $table->id();

            // Using areas_id like your structure
            $table->foreignId('area_id')
                ->constrained('areas')
                ->onDelete('cascade');

            $table->string('locale')->index();

            $table->string('name');

            $table->unique(['area_id', 'locale']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('area_translations');
        Schema::dropIfExists('areas');
    }
};
