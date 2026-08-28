<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('db_kitchens', function (Blueprint $table) {
            $table->id();
            $table->integer('org_id')->nullable();
            $table->integer('branchID')->nullable();
            $table->integer('userID')->nullable();
            $table->string('barcode', 255)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('db_kitchen_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kitchen_id')->constrained('db_kitchens')->onDelete('cascade');
            $table->string('locale')->index();
            $table->string('name');
            $table->unique(['kitchen_id', 'locale']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('db_kitchen_translations');
        Schema::dropIfExists('db_kitchens');
    }
};
