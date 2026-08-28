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
        Schema::create('hr_allowances', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('status');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hr_allowance_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hr_allowance_id')->constrained();
            $table->string('name');
            $table->string('locale')->index();
            $table->unique(['hr_allowance_id', 'locale']);
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
        Schema::dropIfExists('hr_allowance_translations');
        Schema::dropIfExists('hr_allowances');
    }
};
