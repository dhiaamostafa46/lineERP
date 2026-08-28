<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThemesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('themes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('panel_body_background')->default('#EEF0F8');
            $table->string('panel_aside_background')->default('#3F4254');
            $table->string('panel_aside_color')->default('#EEF0F8');
            $table->string('panel_btn_background')->default('#3699FF');
            $table->string('panel_header_color')->default('#a2a3b7');
            $table->string('panel_content_color')->default('#181C32');
            $table->string('panel_btn_color')->default('#ffffff');

            $table->string('mobile_body_background')->default('#EEF0F8');
            $table->string('mobile_aside_background')->default('#3F4254');
            $table->string('mobile_aside_color')->default('#EEF0F8');
            $table->string('mobile_btn_background')->default('#3699FF');
            $table->string('mobile_header_color')->default('#a2a3b7');
            $table->string('mobile_content_color')->default('#181C32');
            $table->string('mobile_btn_color')->default('#ffffff');
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
        Schema::drop('themes');
    }
}
