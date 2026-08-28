<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('db_service_points', function (Blueprint $table) {
            $table->id();
            $table->integer('org_id')->nullable();
            $table->integer('branchID')->nullable();
            $table->integer('userID')->nullable();
            $table->string('code', 255)->nullable();
            $table->tinyInteger('type')->default(1)->comment('1=Table, 2=Kitchen, 3=Drive');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
        Schema::create('db_service_point_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_point_id')->constrained('db_service_points')->onDelete('cascade');
            $table->string('locale')->index(); // ar, en, ...
            $table->string('name');
            $table->unique(['service_point_id', 'locale']);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('db_service_point_translations');
        Schema::dropIfExists('db_service_points');
    }
};
