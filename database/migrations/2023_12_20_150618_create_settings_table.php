<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('logo')->default('medlur-light-5.png');
            $table->string('org_id')->nullable();
            $table->integer(' Activity_type')->default(1);
            $table->string('fav_icon')->default('medlur-light-fav.png');
            $table->string('name')->default('Medlur');
            $table->boolean('coming_soon')->default(0);


            $table->integer('count_user')->default(1);
            $table->integer('actual_user')->default(1);
            $table->date('subscription_date')->default(date('Y-m-d'));
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('settings');
    }
}
