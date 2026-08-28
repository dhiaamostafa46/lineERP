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
        Schema::create('hr_termination_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('status')->comment('1 => inactive, 2 => active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hr_termination_type_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type_id')->constrained('hr_termination_types');
            $table->string('name');
            $table->string('locale')->index();
            $table->unique(['type_id', 'locale']);
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
        Schema::dropIfExists('hr_termination_type_translations');
        Schema::dropIfExists('hr_termination_types');
    }
};
