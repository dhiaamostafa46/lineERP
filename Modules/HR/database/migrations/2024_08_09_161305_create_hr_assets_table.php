<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('hr_departments');
            $table->foreignId('type_id')->constrained('hr_asset_types');
            $table->boolean('is_new')->default(0);
            $table->string('note')->nullable();
            $table->unsignedTinyInteger('status')->default(2)->comment('1 = inactive, 2 = active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hr_asset_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hr_asset_id')->constrained();
            $table->string('name');
            $table->string('locale')->index();
            $table->unique(['hr_asset_id', 'locale']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hr_asset_translations');
        Schema::dropIfExists('hr_assets');
    }
};
