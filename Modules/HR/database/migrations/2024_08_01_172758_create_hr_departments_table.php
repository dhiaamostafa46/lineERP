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
        Schema::create('hr_departments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('parent_id')->nullable();
            $table->unsignedInteger('owner_id')->nullable();
            $table->unsignedTinyInteger('status');
            $table->string('code');
            $table->unsignedTinyInteger('type');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hr_department_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hr_department_id')->constrained();
            $table->string('name');
            $table->string('locale')->index();
            $table->unique(['hr_department_id', 'locale']);
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
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Schema::dropIfExists('hr_department_translations');
        Schema::dropIfExists('hr_departments');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
