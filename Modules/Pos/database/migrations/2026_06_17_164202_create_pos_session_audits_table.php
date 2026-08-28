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
        Schema::create('pos_session_audits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pos_session_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('device_id')->nullable();
            $table->string('action'); // login, logout, locked_out_by_other_device
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('pos_session_id')->references('id')->on('pos_sessions')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('device_id')->references('id')->on('pos_devices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_session_audits');
    }
};
