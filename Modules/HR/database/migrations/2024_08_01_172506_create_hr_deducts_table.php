<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_deducts', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('status');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hr_deduct_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hr_deduct_id')->constrained();
            $table->string('name');
            $table->string('locale')->index();
            $table->unique(['hr_deduct_id', 'locale']);
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
       +
          DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Schema::dropIfExists('hr_deducts');
        Schema::dropIfExists('hr_deduct_translations');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
